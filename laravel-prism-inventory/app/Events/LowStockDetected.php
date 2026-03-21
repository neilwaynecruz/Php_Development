<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $product_id;
    public string $name;
    public ?string $sku;
    public string $category;
    public int $quantity;
    public int $threshold;

    /**
     * Create a new event instance.
     */
    public function __construct(array $productData)
    {
        $this->product_id = $productData['product_id'];
        $this->name       = $productData['name'];
        $this->sku        = $productData['sku'] ?? null;
        $this->category   = $productData['category'];
        $this->quantity   = $productData['quantity'];
        $this->threshold  = $productData['low_stock_threshold'];
    }

    public function broadcastOn(): Channel
    {
        // public channel – all online admins/users listening get it
        return new Channel('products.low-stock');
    }

    public function broadcastAs(): string
    {
        return 'LowStockDetected';
    }
}