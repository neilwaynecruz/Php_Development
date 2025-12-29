(function () {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  function showAlert(html) {
    const container = document.querySelector('.container') || document.body;
    const wrap = document.createElement('div');
    wrap.innerHTML = html.trim();
    const alertEl = wrap.firstElementChild;
    if (!alertEl) return;
    alertEl.classList.add('alert-animate', 'alert-dim');
    container.prepend(alertEl);
    setTimeout(() => alertEl.classList.add('dimmed'), 3500);
  }

  async function submitAjaxForm(form) {
    const url = form.getAttribute('action');
    const method = (form.getAttribute('method') || 'POST').toUpperCase();
    const fd = new FormData(form);

    const res = await fetch(url, {
      method,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf
      },
      body: fd
    });

    let data;
    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      data = await res.json();
    } else {
      const text = await res.text();
      if (res.ok && text.includes('<html')) {
        window.location.reload();
        return;
      }
      data = { ok: res.ok, message: `<div class="alert alert-${res.ok ? 'success':'danger'}">Request completed.</div>` };
    }

    if (res.ok && (data?.ok !== false)) {
      if (data?.message) showAlert(data.message);
      setTimeout(() => window.location.reload(), 400);
    } else {
      const msg = data?.message || '<div class="alert alert-danger">Action failed.</div>';
      showAlert(msg);
    }
  }

  // Intercept forms with .js-ajax-form
  document.addEventListener('submit', (e) => {
    const form = e.target.closest('form.js-ajax-form');
    if (!form) return;

    // Handle Archive confirm in JS so Cancel actually cancels
    if (form.classList.contains('js-confirm-archive')) {
      const ok = window.confirm('Archive this product?');
      if (!ok) {
        // User clicked Cancel: do NOT submit
        e.preventDefault();
        return;
      }
    }

    // (You can add other confirm types similarly, e.g. js-confirm-delete)
    if (form.classList.contains('js-confirm-delete')) {
      if (!window.confirm('PERMANENTLY DELETE? This cannot be undone.')) {
        e.preventDefault();
        return;
      }
    }

    e.preventDefault();
    submitAjaxForm(form).catch(() => {
      showAlert('<div class="alert alert-danger">Network error. Please try again.</div>');
    });
  });

  // Edit Product modal populate (includes read-only ID display)
    const modalEl = document.getElementById('editProductModal');
    if (modalEl) {
      modalEl.addEventListener('show.bs.modal', (ev) => {
        const btn = ev.relatedTarget;
        if (!btn) return;
        const pid   = btn.getAttribute('data-id');
        const name  = btn.getAttribute('data-name') || '';
        const cat   = btn.getAttribute('data-category') || '';
        const qty   = btn.getAttribute('data-qty') || '0';
        const price = btn.getAttribute('data-price') || '0.00';
        const imgUrl = btn.getAttribute('data-image-url') || '';

        const pidHiddenEl   = document.getElementById('edit_pid');
        const pidDisplayEl  = document.getElementById('edit_pid_display');
        const nameEl        = document.getElementById('edit_name');
        const catEl         = document.getElementById('edit_category');
        const qtyEl         = document.getElementById('edit_quantity');
        const priceEl       = document.getElementById('edit_price');
        const imgWrapperEl  = document.getElementById('edit_current_image_wrapper');
        const imgPreviewEl  = document.getElementById('edit_current_image');
        const removeChkEl   = document.getElementById('remove_image');

        if (pidHiddenEl)  pidHiddenEl.value = pid;
        if (pidDisplayEl) pidDisplayEl.value = pid;
        if (nameEl)       nameEl.value = name;
        if (catEl)        catEl.value = cat;
        if (qtyEl)        qtyEl.value = qty;
        if (priceEl)      priceEl.value = price;

        if (removeChkEl) {
          removeChkEl.checked = false;
        }

        if (imgWrapperEl && imgPreviewEl) {
          if (imgUrl) {
            imgPreviewEl.src = imgUrl;
            imgWrapperEl.style.display = '';
          } else {
            imgPreviewEl.src = '';
            imgWrapperEl.style.display = 'none';
          }
        }
      });
    }
})();