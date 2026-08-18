// assets/js/dang_ky.js

let daDangKy = [];   // Lưu trữ danh sách học phần đã chọn của sinh viên

// ── Tải danh sách tất cả học phần từ API ────────────────────────────────────
async function taiHocPhan(keyword = '') {
  try {
    const res  = await fetch(`api/get_hoc_phan.php?msv=${MSV}&q=${encodeURIComponent(keyword)}`);
    const data = await res.json();
    renderBang(data);
  } catch (error) {
    console.error('Lỗi khi tải danh sách học phần:', error);
  }
}

// ── Tải danh sách môn học đã đăng ký thành công của SV ───────────────────────
async function taiDanhSachDaDangKy() {
  try {
    const res  = await fetch(`api/get_da_chon.php?msv=${MSV}`);
    const data = await res.json();
    daDangKy = data; // dữ liệu trả về gồm: ma_lhp, ten_mon, so_tin_chi
    renderSidebar();
  } catch (error) {
    console.error('Lỗi khi tải danh sách đã đăng ký:', error);
  }
}

// ── Render bảng danh sách học phần ──────────────────────────────────────────
function renderBang(ds) {
  const tbody = document.getElementById('danh-sach-hp');
  if (ds.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: #888;">Không tìm thấy học phần nào phù hợp.</td></tr>`;
    return;
  }

  tbody.innerHTML = ds.map(hp => {
    const day = hp.si_so_hien >= hp.si_so_max;
    const daDk = hp.da_dang_ky;
    
    return `
      <tr>
        <td class="ma-hp">${escapeHtml(hp.ma_lhp)}</td>
        <td><strong>${escapeHtml(hp.ten_mon)}</strong></td>
        <td style="text-align: center;">${hp.so_tin_chi}</td>
        <td>${escapeHtml(hp.giang_vien)}</td>
        <td>${escapeHtml(hp.lich_hoc)}</td>
        <td>${hp.si_so_hien}/${hp.si_so_max}</td>
        <td>
          ${day && !daDk
            ? `<span class="badge-day">Đã đầy</span>`
            : daDk
              ? `<span class="badge-dk">Đã đăng ký</span>`
              : `<button class="btn-dangky" onclick="dangKy('${hp.ma_lhp}', ${hp.so_tin_chi}, '${hp.ten_mon}')">Đăng ký</button>`
          }
        </td>
      </tr>`;
  }).join('');
}

// ── Gửi yêu cầu đăng ký 1 học phần lên Server ───────────────────────────────
async function dangKy(ma_hp, so_tc, ten_mon) {
  try {
    const res  = await fetch('api/dang_ky.php', {
      method : 'POST',
      headers: { 'Content-Type': 'application/json' },
      body   : JSON.stringify({ msv: MSV, ma_hp }),
    });
    const data = await res.json();

    if (data.success) {
      // Đăng ký thành công -> cập nhật danh sách và reload dữ liệu
      await taiDanhSachDaDangKy();
      await taiHocPhan(document.getElementById('search-input').value);
    } else {
      alert(data.message);
    }
  } catch (error) {
    alert('Không thể kết nối đến máy chủ.');
  }
}

// ── Gửi yêu cầu huỷ/xoá đăng ký học phần ────────────────────────────────────
async function huyChon(ma_hp) {
  if (!confirm(`Bạn có chắc chắn muốn hủy đăng ký lớp học phần ${ma_hp}?`)) {
    return;
  }
  try {
    const res  = await fetch('api/huy_dang_ky.php', {
      method : 'POST',
      headers: { 'Content-Type': 'application/json' },
      body   : JSON.stringify({ msv: MSV, ma_hp }),
    });
    const data = await res.json();

    if (data.success) {
      await taiDanhSachDaDangKy();
      await taiHocPhan(document.getElementById('search-input').value);
    } else {
      alert(data.message);
    }
  } catch (error) {
    alert('Không thể kết nối đến máy chủ.');
  }
}

// ── Cập nhật giao diện Sidebar đã chọn ───────────────────────────────────────
function renderSidebar() {
  const container = document.getElementById('ds-da-chon');
  const tongTC    = daDangKy.reduce((s, h) => s + Number(h.so_tin_chi), 0);

  if (daDangKy.length === 0) {
    container.innerHTML = `<p style="text-align: center; color: #888; padding: 20px 0;">Chưa đăng ký học phần nào.</p>`;
    document.getElementById('tong-mon').textContent    = '0';
    document.getElementById('tong-tc-chon').textContent = '0 TC';
    document.getElementById('so-tc').textContent = '0/24 tín chỉ';
    return;
  }

  container.innerHTML = daDangKy.map(h => `
    <div class="sidebar-item">
      <div class="sidebar-item-info">
        <strong class="ma-hp">${escapeHtml(h.ma_lhp)}</strong>
        <span style="font-size: 13px; font-weight: 500;">${escapeHtml(h.ten_mon)}</span>
        <span class="so-tc">${h.so_tin_chi} tín chỉ</span>
      </div>
      <button class="btn-xoa" onclick="huyChon('${h.ma_lhp}')" title="Huỷ học phần này">🗑</button>
    </div>
  `).join('');

  document.getElementById('tong-mon').textContent    = daDangKy.length;
  document.getElementById('tong-tc-chon').textContent = tongTC + ' TC';
  document.getElementById('so-tc').textContent = tongTC + '/24 tín chỉ';
}

// ── Hàm tìm kiếm ────────────────────────────────────────────────────────────
function timKiem() {
  const q = document.getElementById('search-input').value;
  taiHocPhan(q);
}

// ── Sự kiện nút "Xác nhận đăng ký" ──────────────────────────────────────────
function xacNhanDangKy() {
  if (daDangKy.length === 0) {
    alert('Vui lòng chọn ít nhất một lớp học phần để đăng ký!');
    return;
  }
  alert('Hệ thống đã lưu nhận danh sách đăng ký học phần của bạn thành công!');
}

// ── Hàm tiện ích bảo mật tránh lỗi XSS khi hiển thị text từ DB ───────────────
function escapeHtml(string) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return String(string).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// ── Khởi chạy khi tải xong trang ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  taiHocPhan();
  taiDanhSachDaDangKy();
});
