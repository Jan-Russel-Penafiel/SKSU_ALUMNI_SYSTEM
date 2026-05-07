// =============================================================
// Client-side helpers
// =============================================================
document.addEventListener('DOMContentLoaded', function () {
  const hasSwal = typeof window.Swal !== 'undefined';
  const alertDefaults = {
    confirmButtonColor: '#15803d',
    cancelButtonColor: '#64748b',
    buttonsStyling: true
  };

  function normalizeIcon(type) {
    if (type === 'error' || type === 'warning' || type === 'info' || type === 'question') {
      return type;
    }
    return type === 'success' ? 'success' : 'info';
  }

  function showFlashMessages() {
    if (!hasSwal || !Array.isArray(window.AppFlashes) || !window.AppFlashes.length) return;

    window.AppFlashes.reduce(function (chain, flash) {
      return chain.then(function () {
        const type = flash.type || 'info';
        return window.Swal.fire(Object.assign({}, alertDefaults, {
          icon: normalizeIcon(type),
          title: type === 'success' ? 'Success' : (type === 'error' ? 'Error' : 'Notice'),
          text: flash.message || '',
          timer: type === 'success' ? 2600 : undefined,
          timerProgressBar: type === 'success',
          confirmButtonText: 'OK'
        }));
      });
    }, Promise.resolve());
  }

  // Confirm dangerous actions
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      const message = el.getAttribute('data-confirm') || 'Are you sure?';
      if (!hasSwal) {
        e.preventDefault();
        return;
      }

      e.preventDefault();
      window.Swal.fire(Object.assign({}, alertDefaults, {
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: el.getAttribute('data-confirm-button') || 'Yes, continue',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true
      })).then(function (result) {
        if (!result.isConfirmed) return;

        if (el.tagName === 'A' && el.href) {
          window.location.href = el.href;
          return;
        }

        const form = el.closest('form');
        if (form) form.submit();
      });
    });
  });

  // Auto-dismiss flash messages after 5s
  document.querySelectorAll('.auto-dismiss').forEach(function (el) {
    setTimeout(() => el.remove(), 5000);
  });

  // ===== Modal system =====
  // Open: any element with [data-modal-open="modalId"]
  // Close: any element with [data-modal-close], the backdrop, or Escape
  // Optional: trigger may carry data-fill='{"name":"value",...}' to prefill modal inputs by name
  function openModal(id, fillJson) {
    const m = document.getElementById(id);
    if (!m) return;
    if (fillJson) {
      try {
        const data = typeof fillJson === 'string' ? JSON.parse(fillJson) : fillJson;
        Object.keys(data).forEach(function (k) {
          const field = m.querySelector('[name="' + k + '"]');
          if (!field) return;
          if (field.tagName === 'SELECT') {
            field.value = data[k] != null ? data[k] : '';
          } else if (field.type === 'checkbox') {
            field.checked = !!data[k];
          } else {
            field.value = data[k] != null ? data[k] : '';
          }
        });
      } catch (err) {
        console.warn('modal fill parse failed', err);
      }
    }
    // Update header title if modal-title-set provided
    const titleEl = m.querySelector('[data-modal-title]');
    if (titleEl && fillJson) {
      try {
        const d = typeof fillJson === 'string' ? JSON.parse(fillJson) : fillJson;
        if (d.__title) titleEl.textContent = d.__title;
      } catch (e) {}
    }
    m.classList.add('is-open');
    document.body.classList.add('modal-open');
    // focus first input
    setTimeout(function () {
      const first = m.querySelector('input:not([type=hidden]), select, textarea');
      if (first) first.focus();
    }, 80);
  }
  function closeModal(m) {
    if (!m) return;
    m.classList.remove('is-open');
    if (!document.querySelector('.modal-backdrop.is-open')) {
      document.body.classList.remove('modal-open');
    }
  }

  document.querySelectorAll('[data-modal-open]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      openModal(btn.getAttribute('data-modal-open'), btn.getAttribute('data-fill'));
    });
  });

  document.querySelectorAll('.modal-backdrop').forEach(function (m) {
    // click on backdrop (not panel) closes
    m.addEventListener('click', function (e) {
      if (e.target === m) closeModal(m);
    });
    m.querySelectorAll('[data-modal-close]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        closeModal(m);
      });
    });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.modal-backdrop.is-open').forEach(closeModal);
  });

  // expose for ad-hoc use
  window.AppModal = { open: openModal, close: function (id) { closeModal(document.getElementById(id)); } };

  showFlashMessages();
});
