<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      
      <header class="modal-header">
        <h3>Tình trạng</h3>
        <button class="close-btn" @click="$emit('close')">
          <font-awesome-icon icon="times" />
        </button>
      </header>
      
      <div class="modal-body">
        <div class="radio-group">
          <label>
            <input type="radio" name="condition" value="used" v-model="localCondition" />
            <span class="radio-label">Đã sử dụng</span>
            <span class="radio-custom"></span>
          </label>
          <label>
            <input type="radio" name="condition" value="new" v-model="localCondition" />
            <span class="radio-label">Mới</span>
            <span class="radio-custom"></span>
          </label>
        </div>
      </div>

      <footer class="modal-footer">
        <button class="footer-btn clear-btn" @click="clearFilter">Xoá Lọc</button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

// Nhận giá trị hiện tại từ component cha (ProductCatalog)
const props = defineProps({
  currentValue: String
});

const emit = defineEmits(['close', 'apply']);

// Biến trạng thái nội bộ, được đồng bộ với giá trị cha
const localCondition = ref(props.currentValue);

// Tự động áp dụng và đóng modal khi người dùng chọn
watch(localCondition, (newValue) => {
  emit('apply', newValue);
  emit('close'); // Tự động đóng khi chọn xong
});

// Xử lý nút Xóa lọc
const clearFilter = () => {
  localCondition.value = ''; // Xóa trạng thái nội bộ
  emit('apply', ''); // Gửi giá trị rỗng
  emit('close'); // Đóng modal
};
</script>

<style scoped>
/* CSS Modal (Tương tự các modal khác) */
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
.modal-content {
  width: 90%;
  max-width: 450px;
  background: white;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  max-height: 80vh;
}
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
  flex-grow: 1;
}
.close-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #888;
  position: absolute;
  right: 1.5rem;
}

/* Body */
.modal-body {
  padding: 1rem 1.5rem;
}

/* CSS cho Radio Buttons (Sao chép từ AdvancedFilterModal) */
.radio-group label {
  display: flex;
  align-items: center;
  padding: 0.75rem 0;
  cursor: pointer;
  position: relative;
  font-size: 1rem;
}
.radio-group input[type="radio"] {
  opacity: 0;
  position: absolute;
}
.radio-label {
  flex-grow: 1;
}
.radio-custom {
  width: 22px;
  height: 22px;
  border: 2px solid #ccc;
  border-radius: 50%;
  display: inline-block;
  position: relative;
}
.radio-custom::after {
  content: '';
  width: 12px;
  height: 12px;
  background: white;
  border-radius: 50%;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) scale(0);
  transition: transform 0.2s ease-in-out;
}
.radio-group input[type="radio"]:checked + .radio-label {
  color: #007bff;
}
.radio-group input[type="radio"]:checked ~ .radio-custom {
  background-color: #007bff;
  border-color: #007bff;
}
.radio-group input[type="radio"]:checked ~ .radio-custom::after {
  transform: translate(-50%, -50%) scale(1);
}

/* Footer (Chỉ có nút Xóa) */
.modal-footer {
  padding: 1rem 1.5rem;
  background-color: #f7f7f7;
  border-top: 1px solid #eee;
  display: flex;
  gap: 1rem;
  border-bottom-left-radius: 12px;
  border-bottom-right-radius: 12px;
}
.footer-btn {
  flex: 1;
  padding: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
}
.clear-btn {
  color: #007bff;
  background-color: white;
  border: 1px solid #007bff;
}
.clear-btn:hover {
  background-color: #f0f8ff;
}
</style>