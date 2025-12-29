@extends('layouts.app')

@section('title', "Edit Request #{$req->id} | Product Inventory System")

@section('content')
@if (session('message')) {!! session('message') !!} @endif

<div class="card shadow-sm mb-3">
  <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h5 class="mb-1">Edit Request #{{ $req->id }}</h5>
      <div class="text-muted small">
        Add items and submit when you’re ready. Admin will review your request.
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
        <form method="POST" action="{{ route('requisitions.my.submit', $req->id) }}" onsubmit="return confirm('Submit this request?');">
          @csrf
          <button class="btn btn-primary btn-sm">Submit</button>
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
    <div class="card shadow-sm mb-3 requisition-card">
      <div class="card-body">
        <h6 class="card-title mb-3">Details</h6>
        <dl class="row mb-0 small">
          <dt class="col-sm-4">ID</dt>
          <dd class="col-sm-8">#{{ $req->id }}</dd>

          <dt class="col-sm-4">Status</dt>
          <dd class="col-sm-8">{{ ucfirst($req->status) }}</dd>

          <dt class="col-sm-4">Created</dt>
          <dd class="col-sm-8">{{ $req->created_at }}</dd>

          <dt class="col-sm-12 mb-1 requisition-card">Notes (optional)</dt>
            <dd class="col-sm-12 requisition-card">
            <form method="POST" action="{{ route('requisitions.my.notes.save', $req->id) }}">
                @csrf
                <textarea name="notes"
                        class="form-control form-control-sm mb-2"
                        rows="3"
                        placeholder="Describe what this request is for (optional)...">{{ old('notes', $req->notes) }}</textarea>
                <button class="btn btn-outline-secondary btn-sm save-notes-btn">Save Notes</button>
            </form>
            </dd>
        </dl>
      </div>
    </div>

    <div class="card shadow-sm requisition-card">
      <div class="card-body">
        <h6 class="card-title mb-3">Add / Update Item</h6>
        <form method="POST" action="{{ route('requisitions.my.items.add', $req->id) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
              <option value="">Select product...</option>
              @foreach ($allProducts as $p)
                <option value="{{ $p->product_id }}">
                  [{{ $p->product_id }}] {{ $p->name }}
                  @if($p->sku) (SKU: {{ $p->sku }}) @endif
                  — In stock: {{ $p->quantity }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" min="1" required>
          </div>
          <button class="btn btn-primary w-100">Save Item</button>
        </form>
        <div class="small text-muted mt-2">
          If you add the same product again, its quantity will be updated instead of duplicated.
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card shadow-sm  requisition-card">
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
                  <td class="text-end">
                    @if (in_array($req->status, ['draft','submitted']))
                      <form method="POST" action="{{ route('requisitions.my.items.remove', [$req->id, $it->id]) }}" onsubmit="return confirm('Remove this item?');">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    No items yet. Use the form on the left to add products to this request.
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