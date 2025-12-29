@extends('layouts.app')

@section('title', 'My Requests | Product Inventory System')

@section('content')
@if (session('message')) {!! session('message') !!} @endif

<div class="card shadow-sm mb-3 requisition-card">
  <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h5 class="mb-1">My Requests</h5>
      <div class="text-muted small">
        Create requisitions to request stock from the admin.
      </div>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
        ← Back to Products
      </a>
      <form method="POST" action="{{ route('requisitions.my.create') }}">
        @csrf
        <button class="btn btn-primary btn-sm">New Request</button>
      </form>
    </div>
  </div>
</div>

<div class="card shadow-sm requisition-card">
  <div class="card-body">
    <form method="GET" class="row g-2 mb-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Status</label>
        @php $statuses = ['all','draft','submitted','approved','rejected','fulfilled','cancelled']; @endphp
        <select name="status" class="form-select">
          @foreach ($statuses as $st)
            <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-secondary">Filter</button>
      </div>
      <div class="col-md-6 text-md-end text-muted small mt-2 mt-md-0">
        Drafts can be edited. Submitted requests wait for admin approval.
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Status</th>
            <th>Notes</th>
            <th>Created</th>
            <th>Updated</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($requisitions as $r)
            <tr>
              <td>#{{ $r->id }}</td>
              <td>
                @php
                  $badgeClass = match ($r->status) {
                    'submitted' => 'bg-info text-dark',
                    'approved'  => 'bg-success',
                    'rejected'  => 'bg-danger',
                    'fulfilled' => 'bg-primary',
                    'cancelled' => 'bg-secondary',
                    'draft'     => 'bg-warning text-dark',
                    default     => 'bg-light text-dark',
                  };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ ucfirst($r->status) }}</span>
              </td>
              <td>{{ \Illuminate\Support\Str::limit($r->notes, 50) }}</td>
              <td class="small">{{ $r->created_at }}</td>
              <td class="small">{{ $r->updated_at }}</td>
              <td class="text-end">
                @if (in_array($r->status, ['draft','rejected','cancelled']))
                    <div class="d-inline-flex gap-1">
                    <a href="{{ route('requisitions.my.edit', $r->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form method="POST"
                            action="{{ route('requisitions.my.delete', $r->id) }}"
                            onsubmit="return confirm('PERMANENTLY delete this request? This cannot be undone.');">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                    </div>
                @else
                    <a href="{{ route('requisitions.my.show', $r->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                @endif
                </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                <div class="mb-1">You don’t have any requests yet.</div>
                <div class="small">
                  Click <strong>New Request</strong> above to create your first requisition.
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $requisitions->withQueryString()->links() }}
  </div>
</div>
@endsection