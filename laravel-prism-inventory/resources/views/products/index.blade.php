@extends('layouts.app')

@section('title', 'Products | Product Inventory System')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('products.index') }}">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo me-2" alt="Prism Logo">
      <span class="brand-text">
        {{ app(\App\Services\SettingsService::class)->get('brand_text', 'PRISM') }}
      </span>
    </a>

    <div class="ms-auto d-flex align-items-center gap-2">
      <span class="navbar-text">
        Hello, {{ e(session('user')) }}
        <span class="badge bg-warning text-dark ms-1">{{ e(session('role', 'user')) }}</span>
      </span>

      {{-- ADMIN-ONLY: Low Stock button with real-time badge --}}
      @if ($isAdmin)
        @php
            $lowStockCount = app(\App\Services\LowStockService::class)->count();
        @endphp
        <a href="{{ route('products.index', ['view' => 'active']) }}"
           class="btn btn-outline-light btn-sm position-relative">
          Low Stock
          <span data-role="low-stock-count"
                class="badge bg-danger position-absolute top-0 start-100 translate-middle {{ $lowStockCount ? '' : 'd-none' }}">
            {{ $lowStockCount ?: 0 }}
          </span>
        </a>
      @endif

      {{-- User + Admin: My Requests --}}
      <a href="{{ route('requisitions.my.index') }}" class="btn btn-outline-light btn-sm">My Requests</a>

      {{-- Admin-only links --}}
      @if ($isAdmin)
        <a href="{{ route('requisitions.admin.index') }}" class="btn btn-outline-light btn-sm">Requisitions</a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm">Users</a>
        <a href="{{ route('logs.index') }}" class="btn btn-outline-light btn-sm">Logs</a>
        <a href="{{ route('settings.index') }}" class="btn btn-outline-light btn-sm">Settings</a>
      @endif

      <a href="{{ route('account.form') }}" class="btn btn-outline-light btn-sm">Account</a>
      <a href="{{ route('app.logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
@endsection

@section('content')

<!-- SUMMARY CARDS -->
<div class="row g-4 mb-4 align-items-start">
  <div class="col-md-4">
    <div class="card shadow-sm stat-card">
      <div class="card-body">
        <h6 class="card-title">Total {{ $is_archived_view ? 'Archived' : 'Active' }} Products</h6>
        <div class="display-6">{{ number_format($totalProducts) }}</div>
      </div>
    </div>
  </div>

  @if (($isAdmin || $showTotalToUser) && !$is_archived_view)
    <div class="col-md-4">
      <div class="card shadow-sm stat-card">
        <div class="card-body">
          <h6 class="card-title">Total Stock Value</h6>
          <div class="display-6">₱{{ number_format($totalValue, 2) }}</div>
        </div>
      </div>
    </div>
  @endif

  @if (!$is_archived_view)
    <div class="col-md-4">
      <div class="card shadow-sm stat-card">
        <div class="card-body">
          <h6 class="card-title">Low Stock (≤ {{ (int) $lowThreshold }})</h6>
          <div class="display-6">{{ number_format($lowCount) }}</div>
        </div>
      </div>
    </div>
  @endif
</div>

<!-- FILTER -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('products.index') }}" class="row g-3 align-items-end">
      @if ($is_archived_view)
        <input type="hidden" name="view" value="archived">
      @endif

      <div class="col-md-5">
        <label class="form-label">Search</label>
        <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="Name, category, or SKU">
      </div>

      <div class="col-md-5">
        <label class="form-label">Category</label>
        <input type="text" name="cat" class="form-control" value="{{ $cat }}" placeholder="School Supplies, Electronics, etc.">
      </div>

      <div class="col-md-2 d-grid">
        <button class="btn btn-secondary">Filter</button>
      </div>
    </form>

    <div class="mt-2 d-flex gap-2">
      <a href="{{ route('products.index', ['clear' => 1]) }}" class="btn btn-link p-0">Reset Filters</a>
    </div>
  </div>
</div>

<div class="row g-4 align-items-start">

<!-- LEFT COLUMN -->
<div class="col-md-5">
@if ($isAdmin)
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h5 class="card-title mb-3">Add Product</h5>

    <form method="POST" action="{{ route('products.create') }}" class="js-ajax-form" enctype="multipart/form-data">
      @csrf
      <div class="mb-2">
        <label class="form-label">Name</label>
        <input type="text" name="pName" class="form-control" placeholder="Ballpen/Marker" required>
      </div>

      <div class="mb-2">
        <label class="form-label">Category</label>
        <input type="text" name="pCategory" class="form-control" placeholder="School Supplies, Electronics, etc." required>
      </div>

      <div class="mb-2">
        <label class="form-label">SKU (optional)</label>
        <input type="text" name="pSku" class="form-control" maxlength="64" placeholder="e.g. KB-001">
      </div>

      <div class="mb-2">
        <label class="form-label">Barcode (optional)</label>
        <input type="text" name="pBarcode" class="form-control" maxlength="128" placeholder="e.g. 1234567890123">
      </div>

      <div class="mb-2">
        <label class="form-label">Quantity</label>
        <input type="number" name="pQty" class="form-control" min="0" placeholder="e.g. 300" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" name="pPrice" class="form-control" min="0" step="0.01" placeholder="e.g. 9.99" required>
      </div>

      <!-- If you have image upload for Add, keep its field here -->

      <button class="btn btn-primary w-100">Add Product</button>
    </form>
  </div>
</div>
@endif
</div>

<!-- RIGHT COLUMN -->
<div class="col-md-7">
<div class="card shadow-sm">
<div class="card-body">

@php
  $toggleViewUrl = request()->fullUrlWithQuery([
    'view' => $is_archived_view ? 'active' : 'archived'
  ]);

  $productsExportUrl = route('products.exportCsv') . '?' . http_build_query([
    'q'    => $q,
    'cat'  => $cat,
    'view' => $view,
  ]);
@endphp

<!-- TABLE TOOLBAR -->
<div class="table-toolbar mb-3">
  <div class="row g-2 align-items-center">

    {{-- Left: Primary actions --}}
    <div class="col-lg-6 d-flex flex-wrap align-items-center gap-2">

      @if ($isAdmin)
        <form method="POST"
              action="{{ route('products.reset') }}"
              class="js-ajax-form"
              onsubmit="return confirm('This will delete all products. Continue?');">
          @csrf
          <button class="btn btn-outline-danger btn-sm">
            Reset All
          </button>
        </form>
      @endif

      @if ($is_archived_view)
        <a href="{{ $toggleViewUrl }}" class="btn btn-outline-secondary btn-sm">
          View Active
        </a>
      @else
        <a href="{{ $toggleViewUrl }}" class="btn btn-outline-secondary btn-sm">
          View Archived
        </a>
      @endif

      <button id="resetViewBtn"
              class="btn btn-outline-secondary btn-sm"
              type="button">
        Reset View
      </button>
    </div>

    {{-- Right: Secondary actions (Export / Columns) --}}
    <div class="col-lg-6 d-flex flex-wrap justify-content-lg-end align-items-center gap-2 mt-2 mt-lg-0">

      <a href="{{ $productsExportUrl }}"
         class="btn btn-outline-secondary btn-sm"
         title="Export filtered products">
        Export CSV
      </a>

      <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                data-bs-toggle="dropdown"
                type="button">
          Columns
        </button>
        <div class="dropdown-menu dropdown-menu-end p-2 columns-menu bg-white text-dark border"
             style="min-width: 240px;">
          <label class="form-check d-flex align-items-center text-dark">
            <input class="form-check-input me-2" type="checkbox" checked data-toggle-col="id">
            <span class="form-check-label">ID</span>
          </label>
          <label class="form-check d-flex align-items-center text-dark">
            <input class="form-check-input me-2" type="checkbox" checked data-toggle-col="sku">
            <span class="form-check-label">SKU</span>
          </label>
          <label class="form-check d-flex align-items-center text-dark">
            <input class="form-check-input me-2" type="checkbox" checked data-toggle-col="barcode">
            <span class="form-check-label">Barcode</span>
          </label>
          <label class="form-check d-flex align-items-center text-dark">
            <input class="form-check-input me-2" type="checkbox" checked data-toggle-col="name">
            <span class="form-check-label">Name</span>
          </label>
          <label class="form-check d-flex align-items-center text-dark">
            <input class="form-check-input me-2" type="checkbox" checked data-toggle-col="category">
            <span class="form-check-label">Category</span>
          </label>
          <label class="form-check d-flex align-items-center text-dark">
            <input class="form-check-input me-2" type="checkbox" checked data-toggle-col="qty">
            <span class="form-check-label">Qty</span>
          </label>
          <label class="form-check d-flex align-items-center text-dark">
            <input class="form-check-input me-2" type="checkbox" checked data-toggle-col="price">
            <span class="form-check-label">Price</span>
          </label>
          <label class="form-check d-flex align-items-center text-dark">
            <input class="form-check-input me-2" type="checkbox" checked data-toggle-col="total">
            <span class="form-check-label">Total</span>
          </label>
          @if ($isAdmin)
            <label class="form-check d-flex align-items-center text-dark">
              <input class="form-check-input me-2" type="checkbox" checked data-toggle-col="action">
              <span class="form-check-label">Action</span>
            </label>
          @endif
          <div class="mt-2 text-muted small">Tip: Click table headers to sort.</div>
        </div>
      </div>
    </div>

    {{-- Quick search --}}
    <div class="col-12 mt-3">
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="quickSearch" class="form-control" placeholder="Quick search (name/category)">
        <button class="btn btn-outline-secondary" id="clearQuickSearch" type="button">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>

  </div>
</div>

<!-- TABLE -->
<div class="table-responsive">
<table class="table table-hover align-middle product-table" id="productTable">
  <thead>
  <tr>
    <th class="sortable" data-col="id">ID</th>
    <th style="width:8%;">Img</th>
    <th class="sortable" data-col="sku">SKU</th>
    <th class="sortable" data-col="barcode">Barcode</th>
    <th class="sortable" data-col="name">Name</th>
    <th class="sortable" data-col="category">Category</th>
    <th class="sortable" data-col="qty">Qty</th>
    <th class="sortable" data-col="price">Price</th>
    <th class="sortable" data-col="total">Total</th>
    @if ($isAdmin)
      <th data-col="action" class="text-end">Action</th>
    @endif
  </tr>
  </thead>
  <tbody>
    @foreach ($rows as $row)
      @php
        $qtyVal   = (int) $row->quantity;
        $isLow    = !$is_archived_view && ($qtyVal <= (int) $lowThreshold);
        $rowClass = $isLow ? 'row-low-stock' : '';
        $imgPath  = $row->image_path ?? null;
        $imgUrl   = $imgPath ? asset('storage/'.$imgPath) : '';
      @endphp
      <tr class="{{ $rowClass }}">
        <td data-col="id">{{ (int) $row->product_id }}</td>

        <td>
          @if ($imgPath)
            <img src="{{ $imgUrl }}"
                 alt="Image of {{ e($row->name) }}"
                 class="product-thumb-img"
                 style="width:40px; height:40px; object-fit:cover; border-radius:4px; cursor:pointer;"
                 data-full-url="{{ $imgUrl }}"
                 title="Click to view">
          @else
            <span class="text-muted small">–</span>
          @endif
        </td>

        <td data-col="sku">{{ $row->sku ?? '—' }}</td>
        <td data-col="barcode">{{ $row->barcode ?? '—' }}</td>

        <td data-col="name">{{ e($row->name) }}</td>
        <td data-col="category">{{ e($row->category) }}</td>

        <td data-col="qty">
          @if ($isLow)
            <span class="qty-alert-badge" title="Below threshold">{{ $qtyVal }}</span>
          @else
            {{ $qtyVal }}
          @endif
        </td>

        <td data-col="price">₱{{ number_format((float) $row->price, 2) }}</td>
        <td data-col="total">₱{{ number_format($qtyVal * (float) $row->price, 2) }}</td>

        @if ($isAdmin)
          <td data-col="action" class="text-end">
            @if ($is_archived_view)
              <div class="d-flex justify-content-end action-btn-group">
                <form action="{{ route('products.restore', ['view' => 'archived']) }}" method="POST" class="d-inline js-ajax-form">
                  @csrf
                  <input type="hidden" name="restore_id" value="{{ (int) $row->product_id }}">
                  <button type="submit" class="btn btn-sm btn-success">Restore</button>
                </form>
                <form action="{{ route('products.deletePermanent', ['view' => 'archived']) }}" method="POST" class="d-inline js-ajax-form js-confirm-delete">
                  @csrf
                  <input type="hidden" name="delete_id" value="{{ (int) $row->product_id }}">
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
              </div>
            @else
              <div class="d-flex justify-content-end action-btn-group">
                <button class="btn btn-sm btn-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#editProductModal"
                        data-id="{{ (int) $row->product_id }}"
                        data-name="{{ e($row->name) }}"
                        data-category="{{ e($row->category) }}"
                        data-sku="{{ e($row->sku ?? '') }}"
                        data-barcode="{{ e($row->barcode ?? '') }}"
                        data-qty="{{ $qtyVal }}"
                        data-price="{{ number_format((float) $row->price, 2, '.', '') }}"
                        data-image-url="{{ $imgUrl }}">
                  Edit
                </button>
                <form action="{{ route('products.archive') }}" method="POST" class="d-inline js-ajax-form js-confirm-archive">
                  @csrf
                  <input type="hidden" name="archive_id" value="{{ (int) $row->product_id }}">
                  <button type="submit" class="btn btn-sm btn-archive">Archive</button>
                </form>
              </div>
            @endif
          </td>
        @endif
      </tr>
    @endforeach
  </tbody>
</table>
</div>

</div>
</div>
</div>

</div>

<!-- Edit Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content card">
      <div class="modal-header">
        <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
        <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editProductForm" action="{{ route('products.update') }}" method="POST" class="js-ajax-form" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="pid" id="edit_pid">
          <div class="mb-3">
            <label class="form-label">Product ID</label>
            <input type="number" id="edit_pid_display" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" id="edit_category" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">SKU (optional)</label>
            <input type="text" name="sku" id="edit_sku" class="form-control" maxlength="64">
          </div>
          <div class="mb-3">
            <label class="form-label">Barcode (optional)</label>
            <input type="text" name="barcode" id="edit_barcode" class="form-control" maxlength="128">
          </div>
          <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" id="edit_quantity" class="form-control" min="0" step="1" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" id="edit_price" class="form-control" min="0" step="0.01" required>
          </div>

          <div class="mb-3">
            <label class="form-label d-flex justify-content-between align-items-center">
              <span>Image (optional)</span>
            </label>

            <div class="mb-2" id="edit_current_image_wrapper" style="display:none;">
              <div class="d-flex align-items-center gap-2">
                <img id="edit_current_image"
                    src=""
                    alt="Current product image"
                    style="width:60px; height:60px; object-fit:cover; border-radius:4px; border:1px solid rgba(255,255,255,0.2);">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="remove_image" name="remove_image" value="1">
                  <label for="remove_image" class="form-check-label">Remove image</label>
                </div>
              </div>
              <div class="form-text">If you check “Remove image”, the current image will be deleted.</div>
            </div>

            <input type="file" name="image" class="form-control" accept="image/*">
            <div class="form-text">Upload to replace existing image (max 2MB).</div>
          </div>

          <button type="submit" class="btn btn-success w-100">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Image Viewer Modal -->
<div class="modal fade" id="productImageModal" tabindex="-1" aria-labelledby="productImageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="productImageModalLabel">Product Image</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex justify-content-center align-items-center">
        <img id="productImageModalImg"
             src=""
             alt="Product image"
             style="max-width:100%; max-height:80vh; object-fit:contain; border-radius:8px;">
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('productImageModal');
  const imgEl   = document.getElementById('productImageModalImg');

  if (modalEl && imgEl) {
    const bsModal = new bootstrap.Modal(modalEl);

    document.addEventListener('click', function (e) {
      const thumb = e.target.closest('.product-thumb-img');
      if (!thumb) return;
      const fullUrl = thumb.getAttribute('data-full-url');
      if (!fullUrl) return;
      imgEl.src = fullUrl;
      bsModal.show();
    });
  }
});
</script>

<script src="{{ asset('assets/js/products-ajax.js') }}?v=3"></script>
<script src="{{ asset('assets/js/products-table.js') }}?v=3"></script>
@endpush