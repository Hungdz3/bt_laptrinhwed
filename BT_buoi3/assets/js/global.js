// assets/js/global.js

// ── Hệ thống thông báo Toast ────────────────────────────────────────────────
function showToast(message, type = 'info') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  
  let icon = 'ℹ️';
  if (type === 'success') icon = '✅';
  else if (type === 'error') icon = '❌';

  toast.innerHTML = `
    <span style="font-size: 16px;">${icon}</span>
    <div>${message}</div>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 4000);
}

// ── Hệ thống hộp thoại xác nhận Modal (Thay thế confirm()) ─────────────────────
function showConfirmModal(message, onConfirm) {
  const backdrop = document.createElement('div');
  backdrop.className = 'modal-backdrop';

  const modal = document.createElement('div');
  modal.className = 'custom-modal';

  modal.innerHTML = `
    <div class="modal-header">Xác nhận yêu cầu</div>
    <div class="modal-body">${message}</div>
    <div class="modal-footer">
      <button class="btn-modal-cancel">Hủy bỏ</button>
      <button class="btn-modal-confirm">Đồng ý</button>
    </div>
  `;

  backdrop.appendChild(modal);
  document.body.appendChild(backdrop);

  const cancelBtn = modal.querySelector('.btn-modal-cancel');
  const confirmBtn = modal.querySelector('.btn-modal-confirm');

  const closeModal = () => {
    backdrop.remove();
  };

  cancelBtn.onclick = closeModal;
  backdrop.onclick = (e) => {
    if (e.target === backdrop) closeModal();
  };

  confirmBtn.onclick = () => {
    closeModal();
    if (typeof onConfirm === 'function') {
      onConfirm();
    }
  };
}
