@extends('layouts.app')

@section('title', "Requisition #{$req->id} | Product Inventory System")

@section('content')
@if (session('message')) {!! session('message') !!} @endif

<div class="card shadow-sm mb-3 requisition-card">
  <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h5 class="mb-1">Requisition #{{ $req->id }}</h5>
      <div class="text-muted small">
        User: <strong>{{ $req->username }}</strong> •
        Status:
        @php
          $badgeClass = match ($req->status) {
            'submitted' => 'bg-info text-dark',
            'approved'  => 'bg-success',
            'rejected'  => 'bg-danger',
            'fulfilled' => 'bg-primary',
            'cancelled' => 'bg-secondary',
            'draft'     => 'bg-warning text-dark',
            default     => 'bg-light text-dark',
          };
        @endphp
        <span class="badge {{ $badgeClass }}">{{ ucfirst($req->status) }}</span>
        • Created: {{ $req->created_at }}
      </div>
    </div>

    <div class="d-flex gap-2">
  <button type="button" class="btn btn-outline-secondary btn-sm" onclick="history.back()">← Back</button>
  <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
    Products
  </a>

  <form method="POST"
        action="{{ route('requisitions.admin.delete', $req->id) }}"
        onsubmit="return confirm('PERMANENTLY delete this requisition and its items? This cannot be undone.');">
    @csrf
    <button class="btn btn-outline-danger btn-sm">Delete</button>
  </form>
</div>

<div class="card shadow-sm mb-3 requisition-card">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-1">Requisition #{{ $req->id }}</h5>
      <div class="text-muted small">
        User: {{ $req->username }} |
        Status: {{ ucfirst($req->status) }} |
        Created: {{ $req->created_at }}
      </div>
    </div>

    <div class="d-flex flex-column flex-md-row gap-2 w-100">
    @if (in_array($req->status, ['submitted','approved'], true))
        <form method="POST" action="{{ route('requisitions.admin.reject', $req->id) }}" class="flex-grow-1">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-8">
            <label class="form-label small mb-1 text-muted">Reject reason / notes (optional)</label>
            <input type="text"
                    name="reason"
                    class="form-control form-control-sm mb-1"
                    placeholder="Reason for rejection (optional)">
            <textarea name="notes"
                        class="form-control form-control-sm"
                        rows="2"
                        placeholder="Additional notes visible to user (optional)"></textarea>
            </div>
            <div class="col-md-4 d-grid">
            <button class="btn btn-outline-danger btn-sm mt-3 mt-md-0"
                    onclick="return confirm('Reject this request?');">
                Reject
            </button>
            </div>
        </div>
        </form>
    @endif

    @if ($req->status === 'submitted')
        <form method="POST" action="{{ route('requisitions.admin.approve', $req->id) }}" class="flex-grow-1">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-8">
            <label class="form-label small mb-1 text-muted">Approval notes (optional)</label>
            <textarea name="notes"
                        class="form-control form-control-sm"
                        rows="2"
                        placeholder="Optional note about approval (visible to user)"></textarea>
            </div>
            <div class="col-md-4 d-grid">
            <button class="btn btn-success btn-sm mt-3 mt-md-0">Approve</button>
            </div>
        </div>
        </form>
        @endif

        @if ($req->status === 'approved')
            <form method="POST" action="{{ route('requisitions.admin.fulfill', $req->id) }}"
                onsubmit="return confirm('Fulfill this requisition and deduct stock from all items?');">
            @csrf
            <button class="btn btn-primary btn-sm">Fulfill &amp; Deduct Stock</button>
            </form>
        @endif
    </div>
  </div>
</div>

<div class="card shadow-sm requisition-card">
  <div class="card-body">
    <h6 class="card-title mb-3">Items</h6>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Product</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Requested</th>
            <th>Current Stock</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($items as $it)
            <tr>
              <td>{{ $it->id }}</td>
              <td>{{ $it->name }}</td>
              <td>{{ $it->sku ?? '—' }}</td>
              <td>{{ $it->category }}</td>
              <td>{{ $it->quantity }}</td>
              <td>{{ $it->current_qty }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No items.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <h6 class="mt-3">Notes</h6>
    <p>{{ $req->notes ?: '—' }}</p>
  </div>
</div>
@endsection