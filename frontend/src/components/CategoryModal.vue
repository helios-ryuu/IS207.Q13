<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      
      <header class="modal-header">
        <h3>Danh mục</h3>
        <button class="close-btn" @click="$emit('close')">
          <font-awesome-icon icon="times" />
        </button>
      </header>
      
      <div class="modal-body">
        <ul>
          <li @click="select('Xe cộ')">
            <span>Xe cộ</span>
          </li>
          <li @click="select('Đồ điện tử')">
            <span>Đồ điện tử</span>
          </li>
          <li @click="select('Thú cưng')">
            <span>Thú cưng</span>
          </li>
          <li @click="select('Đồ ăn, Thực phẩm và các loại khác')">
            <span>Đồ ăn, Thực phẩm và các loại khác</span>
          </li>
          <li @click="select('Tủ lạnh, Máy lạnh, Máy giặt')">
            <span>Tủ lạnh, Máy lạnh, Máy giặt</span>
          </li>
          <li @click="select('Đồ gia dụng, Nội thất, Cây cảnh')">
            <span>Đồ gia dụng, Nội thất, Cây cảnh</span>
          </li>
          <li @click="select('Thời trang, Đồ dùng cá nhân')">
            <span>Thời trang, Đồ dùng cá nhân</span>
          </li>
          <li @click="select('Giải trí, Thể thao, Sở thích')">
            <span>Giải trí, Thể thao, Sở thích</span>
          </li>
          <li @click="select('Đồ dùng văn phòng, Công nông nghiệp')">
            <span>Đồ dùng văn phòng, Công nông nghiệp</span>
          </li>
        </ul>
      </div>

      <footer class="modal-footer">
        <button class="clear-filter-btn" @click="select('')">
          Xoá Lọc
        </button>
      </footer>
    </div>
  </div>
</template>

<script setup>
// Định nghĩa các event mà component này sẽ phát ra
const emit = defineEmits(['close', 'selectCategory']);

// Hàm xử lý khi người dùng chọn một mục
const select = (categoryName) => {
  emit('selectCategory', categoryName);
};
</script>

<style scoped>
/* Lớp phủ toàn màn hình */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9998;
}

/* Nội dung Modal */
.modal-content {
  width: 90%;
  max-width: 450px;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: 80vh; /* Giới hạn chiều cao */
}

/* Header */
.modal-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
}
.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  text-align: center;
  flex-grow: 1; /* Căn giữa tiêu đề */
}
.close-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #888;
  position: absolute; /* Đặt nút X ở góc */
  right: 1.5rem;
}

/* Thân Modal (danh sách) */
.modal-body {
  overflow-y: auto; /* Cho phép cuộn nếu danh sách quá dài */
}
.modal-body ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.modal-body li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f5f5f5;
  cursor: pointer;
  font-size: 1rem;
}
.modal-body li:hover {
  background-color: #f9f9f9;
}
.modal-body li:last-child {
  border-bottom: none;
}
/* Đã xóa CSS cho .arrow-right */

/* Footer (Nút Xóa lọc) */
.modal-footer {
  padding: 1rem 1.5rem;
  background-color: white;
  border-top: 1px solid #eee;
}
.clear-filter-btn {
  width: 100%;
  padding: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  color: #007bff;
  background-color: white;
  border: 1px solid #007bff;
  border-radius: 8px;
  cursor: pointer;
}
.clear-filter-btn:hover {
  background-color: #f0f8ff;
}
</style>