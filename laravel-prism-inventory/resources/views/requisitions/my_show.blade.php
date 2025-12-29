@extends('layouts.app')

@section('title', "Request #{$req->id} | Product Inventory System")

@section('content')
@if (session('message')) {!! session('message') !!} @endif

<div class="card shadow-sm mb-3">
  <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h5 class="mb-1">Request #{{ $req->id }}</h5>
      <div class="text-muted small">
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
      <a href="{{ route('requisitions.my.index') }}" class="btn btn-outline-secondary btn-sm">
        ← Back to My Requests
      </a>
      <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
        Products
      </a>

      @if (in_array($req->status, ['draft','rejected','cancelled']))
        <a href="{{ route('requisitions.my.edit', $req->id) }}" class="btn btn-secondary btn-sm">
          Edit
        </a>
      @endif

      @if (in_array($req->status, ['draft','rejected','cancelled']))
        <form method="POST"
                action="{{ route('requisitions.my.delete', $req->id) }}"
                onsubmit="return confirm('PERMANENTLY delete this request? This cannot be undone.');">
            @csrf
            <button class="btn btn-outline-danger btn-sm">Delete</button>
        </form>
      @endif

      @if (in_array($req->status, ['draft','submitted']))
        <form method="POST" action="{{ route('requisitions.my.cancel', $req->id) }}" onsubmit="return confirm('Cancel this request?');">
          @csrf
          <button class="btn btn-outline-danger btn-sm">Cancel</button>
        </form>
      @endif
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="card-title mb-3">Details</h6>
        <dl class="row mb-0 small">
          <dt class="col-sm-4">ID</dt>
          <dd class="col-sm-8">#{{ $req->id }}</dd>

          <dt class="col-sm-4">Status</dt>
          <dd class="col-sm-8">
            <span class="badge {{ $badgeClass }}">{{ ucfirst($req->status) }}</span>
          </dd>

          <dt class="col-sm-4">Created</dt>
          <dd class="col-sm-8">{{ $req->created_at }}</dd>

          <dt class="col-sm-4">Updated</dt>
          <dd class="col-sm-8">{{ $req->updated_at }}</dd>

          <dt class="col-sm-4">Notes</dt>
          <dd class="col-sm-8">{{ $req->notes ?: '—' }}</dd>
        </dl>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card shadow-sm">
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
                  <td colspan="6" class="text-center text-muted py-4">
                    No items in this request.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection