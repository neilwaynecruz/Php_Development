document.addEventListener('DOMContentLoaded', function(){
  const table = document.getElementById('productTable');
  if (!table) return;

  const thead = table.querySelector('thead');
  const tbody = table.querySelector('tbody');

  // Record original order (for Reset view)
  const originalRows = Array.from(tbody.querySelectorAll('tr'));
  originalRows.forEach((row, i) => { row.dataset.originIndex = String(i); });

  // Quick search (current page only)
  const qsInput = document.getElementById('quickSearch');
  const qsClear = document.getElementById('clearQuickSearch');
  const resetViewBtn = document.getElementById('resetViewBtn');

  function normalizeText(el) {
    return (el?.textContent || '').trim().toLowerCase();
  }

  function normalizeNumber(text) {
    const cleaned = (text || '').replace(/[^\d.\-]/g, '');
    const val = parseFloat(cleaned);
    return isNaN(val) ? 0 : val;
  }

  function getCell(row, key) {
    // Prefer data-col on td
    let cell = row.querySelector(`td[data-col="${key}"]`);
    if (cell) return cell;

    // Fallback: locate column index by header data-col, then use nth-child
    const headers = Array.from(thead.querySelectorAll('th'));
    const idx = headers.findIndex(h => h.getAttribute('data-col') === key);
    if (idx >= 0) {
      // idx is 0-based; nth-child is 1-based
      cell = row.querySelector(`td:nth-child(${idx + 1})`);
    }
    return cell;
  }

  function filterRows(q) {
    const query = (q || '').trim().toLowerCase();
    const rows = Array.from(tbody.querySelectorAll('tr'));
    rows.forEach(row => {
      const nameCell = getCell(row, 'name');
      const catCell  = getCell(row, 'category');
      const nameTxt = normalizeText(nameCell);
      const catTxt  = normalizeText(catCell);
      const match = !query || nameTxt.includes(query) || catTxt.includes(query);
      row.style.display = match ? '' : 'none';
    });
  }

  if (qsInput) {
    qsInput.addEventListener('input', () => filterRows(qsInput.value));
  }
  if (qsClear) {
    qsClear.addEventListener('click', () => {
      qsInput.value = '';
      filterRows('');
      qsInput.focus();
    });
  }

  // Column visibility toggles
  const toggles = document.querySelectorAll('[data-toggle-col]');

  function columnIndexForKey(key) {
    const headers = Array.from(thead.querySelectorAll('th'));
    return headers.findIndex(h => h.getAttribute('data-col') === key);
  }

  function toggleColumn(key, visible) {
    const idx = columnIndexForKey(key);

    // Hide header by key
    const header = thead.querySelector(`th[data-col="${key}"]`);
    if (header) header.classList.toggle('d-none', !visible);

    // Hide cells by data-col (preferred)
    const cells = tbody.querySelectorAll(`td[data-col="${key}"]`);
    cells.forEach(td => td.classList.toggle('d-none', !visible));

    // Fallback: hide by column index if any cells missing data-col
    if (idx >= 0) {
      const nth = idx + 1; // nth-child is 1-based
      tbody.querySelectorAll(`tr`).forEach(row => {
        const td = row.querySelector(`td:nth-child(${nth})`);
        if (td) td.classList.toggle('d-none', !visible);
      });
    }
  }

  // Keep dropdown open when clicking checkboxes so multiple toggles are easy
  const columnsMenu = document.querySelector('.columns-menu');
  if (columnsMenu) {
    columnsMenu.addEventListener('click', (e) => {
      const target = e.target;
      if (target.matches('input[type="checkbox"]')) {
        // Avoid Bootstrap closing the dropdown automatically
        e.stopPropagation();
      }
    });
  }

  toggles.forEach(cb => {
    cb.addEventListener('change', () => toggleColumn(cb.dataset.toggleCol, cb.checked));
  });

  // Sorting (click header). Keeps server pagination; sorts only current page rows.
  const sortHeaders = thead.querySelectorAll('th.sortable');
  function compareValues(aCell, bCell, key) {
    const aText = aCell ? aCell.textContent.trim() : '';
    const bText = bCell ? bCell.textContent.trim() : '';
    if (key === 'id' || key === 'qty' || key === 'price' || key === 'total') {
      const aNum = normalizeNumber(aText);
      const bNum = normalizeNumber(bText);
      return aNum - bNum;
    }
    return aText.localeCompare(bText, undefined, { sensitivity: 'base' });
  }

  function sortBy(key, direction) {
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => r.style.display !== 'none');
    rows.sort((r1, r2) => {
      const c1 = getCell(r1, key);
      const c2 = getCell(r2, key);
      const cmp = compareValues(c1, c2, key);
      return direction === 'asc' ? cmp : -cmp;
    });
    rows.forEach(r => tbody.appendChild(r));
  }

  sortHeaders.forEach(th => {
    th.addEventListener('click', () => {
      const key = th.getAttribute('data-col');
      const current = th.getAttribute('data-sort') || 'none';
      const next = current === 'asc' ? 'desc' : 'asc';
      // Reset sort indicators on all headers
      sortHeaders.forEach(h => h.removeAttribute('data-sort'));
      // Set this one
      th.setAttribute('data-sort', next);
      sortBy(key, next);
    });
    th.tabIndex = 0;
    th.setAttribute('role', 'button');
    th.setAttribute('aria-label', `Sort by ${th.textContent.trim()}`);
    th.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        th.click();
      }
    });
  });

  // Reset view: quick search clear, show all columns, clear sorting, restore original order
  function resetView() {
    // Clear quick search
    if (qsInput) qsInput.value = '';
    filterRows('');

    // Show all columns
    toggles.forEach(cb => {
      cb.checked = true;
      toggleColumn(cb.dataset.toggleCol, true);
    });

    // Clear sort indicators
    sortHeaders.forEach(h => h.removeAttribute('data-sort'));

    // Restore original order
    const rows = Array.from(tbody.querySelectorAll('tr'));
    rows.sort((a, b) => {
      const ai = parseInt(a.dataset.originIndex || '0', 10);
      const bi = parseInt(b.dataset.originIndex || '0', 10);
      return ai - bi;
    });
    rows.forEach(r => tbody.appendChild(r));
  }

  if (resetViewBtn) {
    resetViewBtn.addEventListener('click', resetView);
  }

  // Initialize: no filters, all columns visible
  filterRows('');
});