<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequisitionService
{
    /**
     * Create a new requisition in draft status for the given username.
     */
    public function createDraft(string $username, ?string $notes = null): int
    {
        $id = DB::table('requisitions')->insertGetId([
            'username'   => $username,
            'status'     => 'draft',
            'notes'      => $notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->log(null, $username, 'create', "Created draft requisition #{$id}");

        return (int) $id;
    }

    /**
     * Add or update an item in a requisition (draft/submitted only).
     */
    public function addOrUpdateItem(int $reqId, int $productId, int $quantity, string $username): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $req = DB::table('requisitions')->where('id', $reqId)->first();
        if (!$req) {
            throw new \RuntimeException('Requisition not found.');
        }
        if (!in_array($req->status, ['draft','submitted'], true)) {
            throw new \RuntimeException('Items can only be modified in draft or submitted status.');
        }

        $existing = DB::table('requisition_items')
            ->where('requisition_id', $reqId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            DB::table('requisition_items')
                ->where('id', $existing->id)
                ->update([
                    'quantity'   => $quantity,
                    'updated_at' => now(),
                ]);
            $this->log($reqId, $username, 'update', "Updated item product_id={$productId} qty={$quantity}");
        } else {
            DB::table('requisition_items')->insert([
                'requisition_id' => $reqId,
                'product_id'     => $productId,
                'quantity'       => $quantity,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            $this->log($reqId, $username, 'create', "Added item product_id={$productId} qty={$quantity}");
        }
    }

    public function removeItem(int $itemId, string $username): void
    {
        $item = DB::table('requisition_items')->where('id', $itemId)->first();
        if (!$item) {
            return;
        }

        $req = DB::table('requisitions')->where('id', $item->requisition_id)->first();
        if (!$req) {
            return;
        }
        if (!in_array($req->status, ['draft','submitted'], true)) {
            throw new \RuntimeException('Items can only be removed in draft or submitted status.');
        }

        DB::table('requisition_items')->where('id', $itemId)->delete();
        $this->log($req->id, $username, 'delete', "Removed item #{$itemId}, product_id={$item->product_id}");
    }

    public function submit(int $reqId, string $username): void
    {
        $req = DB::table('requisitions')->where('id', $reqId)->first();
        if (!$req) {
            throw new \RuntimeException('Requisition not found.');
        }
        if ($req->username !== $username && $this->isUser($username)) {
            throw new \RuntimeException('You cannot submit someone else\'s requisition.');
        }
        if (!in_array($req->status, ['draft','rejected','cancelled'], true)) {
            throw new \RuntimeException('Only draft/rejected/cancelled requisitions can be submitted.');
        }

        DB::table('requisitions')->where('id', $reqId)->update([
            'status'     => 'submitted',
            'updated_at' => now(),
        ]);

        $this->log($reqId, $username, 'update', "Submitted requisition #{$reqId}");
    }

    public function cancel(int $reqId, string $username): void
    {
        $req = DB::table('requisitions')->where('id', $reqId)->first();
        if (!$req) {
            throw new \RuntimeException('Requisition not found.');
        }
        // user can only cancel own draft/submitted
        if ($this->isUser($username) && $req->username !== $username) {
            throw new \RuntimeException('You cannot cancel someone else\'s requisition.');
        }
        if (!in_array($req->status, ['draft','submitted'], true)) {
            throw new \RuntimeException('Only draft/submitted requisitions can be cancelled.');
        }

        DB::table('requisitions')->where('id', $reqId)->update([
            'status'     => 'cancelled',
            'updated_at' => now(),
        ]);

        $this->log($reqId, $username, 'update', "Cancelled requisition #{$reqId}");
    }

    // Admin actions

    public function approve(int $reqId, string $admin, ?string $notes = null): void
    {
        $req = DB::table('requisitions')->where('id', $reqId)->first();
        if (!$req) {
            throw new \RuntimeException('Requisition not found.');
        }
        if ($req->status !== 'submitted') {
            throw new \RuntimeException('Only submitted requisitions can be approved.');
        }

        DB::table('requisitions')->where('id', $reqId)->update([
            'status'     => 'approved',
            'notes'      => $notes !== null ? $notes : $req->notes, // keep old if none provided
            'updated_at' => now(),
        ]);

        $msg = "Approved requisition #{$reqId}";
        if ($notes) {
            $msg .= " (Notes: {$notes})";
        }
        $this->log($reqId, $admin, 'update', $msg);
    }

    public function reject(int $reqId, string $admin, ?string $reason = null, ?string $notes = null): void
    {
        $req = DB::table('requisitions')->where('id', $reqId)->first();
        if (!$req) {
            throw new \RuntimeException('Requisition not found.');
        }
        if (!in_array($req->status, ['submitted','approved'], true)) {
            throw new \RuntimeException('Only submitted/approved requisitions can be rejected.');
        }

        DB::table('requisitions')->where('id', $reqId)->update([
            'status'     => 'rejected',
            'notes'      => $notes !== null ? $notes : $req->notes,
            'updated_at' => now(),
        ]);

        $msg = "Rejected requisition #{$reqId}";
        if ($reason) {
            $msg .= " (Reason: {$reason})";
        }
        if ($notes) {
            $msg .= " (Notes: {$notes})";
        }
        $this->log($reqId, $admin, 'update', $msg);
    }

    /**
     * Fulfill: deduct stock and mark requisition as fulfilled.
     * Simple mode: all-or-nothing (no partials).
     */
    public function fulfill(int $reqId, string $admin): void
    {
        DB::transaction(function () use ($reqId, $admin) {
            $req = DB::table('requisitions')->where('id', $reqId)->lockForUpdate()->first();
            if (!$req) {
                throw new \RuntimeException('Requisition not found.');
            }
            if (!in_array($req->status, ['approved'], true)) {
                throw new \RuntimeException('Only approved requisitions can be fulfilled.');
            }

            $items = DB::table('requisition_items')
                ->where('requisition_id', $reqId)
                ->get();

            if ($items->isEmpty()) {
                throw new \RuntimeException('Requisition has no items.');
            }

            // Check stock first
            foreach ($items as $item) {
                $product = DB::table('products')
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();
                if (!$product) {
                    throw new \RuntimeException("Product ID {$item->product_id} not found.");
                }
                if ($product->is_archived ?? 0) {
                    throw new \RuntimeException("Product {$product->name} is archived.");
                }
                if ((int)$product->quantity < (int)$item->quantity) {
                    throw new \RuntimeException("Not enough stock for {$product->name}. Requested {$item->quantity}, available {$product->quantity}.");
                }
            }

            // Deduct stock
            foreach ($items as $item) {
                $product = DB::table('products')
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                $newQty = (int)$product->quantity - (int)$item->quantity;
                DB::table('products')
                    ->where('product_id', $item->product_id)
                    ->update([
                        'quantity'  => $newQty,
                        'updated_at'=> now(),
                    ]);

                // log per product (using activity_logs table convention)
                DB::insert(
                    "INSERT INTO activity_logs (username, action, product_id, details, created_at) VALUES (?, ?, ?, ?, ?)",
                    [
                        $admin,
                        'fulfill',
                        $item->product_id,
                        "Fulfilled requisition #{$reqId}: -{$item->quantity} from {$product->name}",
                        now(),
                    ]
                );
            }

            DB::table('requisitions')->where('id', $reqId)->update([
                'status'     => 'fulfilled',
                'updated_at' => now(),
            ]);

            $this->log($reqId, $admin, 'update', "Fulfilled requisition #{$reqId}");
        });
    }

    private function log(?int $reqId, string $username, string $action, string $details): void
    {
        // Store in activity_logs for simplicity (no product_id for main requisition logs)
        DB::insert(
            "INSERT INTO activity_logs (username, action, product_id, details, created_at) VALUES (?, ?, ?, ?, ?)",
            [
                $username,
                $action,
                null,
                "[REQ {$reqId}] ".$details,
                now(),
            ]
        );
    }

    private function isUser(string $username): bool
    {
        // In your app, role is in session; we can't read it here.
        // We'll trust controllers to block non-admin appropriately.
        return true;
    }

    public function updateNotes(int $reqId, string $username, ?string $notes, bool $byAdmin): void
    {
        $req = DB::table('requisitions')->where('id', $reqId)->first();
        if (!$req) {
            throw new \RuntimeException('Requisition not found.');
        }

        // users can edit notes only on their own draft/submitted
        if (!$byAdmin) {
            if ($req->username !== $username) {
                throw new \RuntimeException('You cannot edit notes on someone else\'s requisition.');
            }
            if (!in_array($req->status, ['draft','submitted','rejected','cancelled'], true)) {
                throw new \RuntimeException('You cannot edit notes in this status.');
            }
        }

        DB::table('requisitions')->where('id', $reqId)->update([
            'notes'      => $notes,
            'updated_at' => now(),
        ]);

        $who = $byAdmin ? 'admin' : 'user';
        $this->log($reqId, $username, 'update', "Updated {$who} notes");
    }

    public function delete(int $reqId, string $username, bool $byAdmin = false): void
    {
        $req = DB::table('requisitions')->where('id', $reqId)->first();
        if (!$req) {
            return;
        }

        if (!$byAdmin) {
            if ($req->username !== $username) {
                throw new \RuntimeException('You cannot delete someone else\'s requisition.');
            }

            if (!in_array($req->status, ['draft','cancelled','rejected'], true)) {
                throw new \RuntimeException('You can only delete draft, cancelled or rejected requests.');
            }
        }

        DB::table('requisition_items')->where('requisition_id', $reqId)->delete();
        DB::table('requisitions')->where('id', $reqId)->delete();

        $who = $byAdmin ? 'admin' : $username;
        $this->log($reqId, $who, 'delete', "Deleted requisition #{$reqId} (status was {$req->status})");
    }
}