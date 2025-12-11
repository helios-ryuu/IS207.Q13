<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      
      <header class="modal-header">
        <h3>Lọc Nâng Cao</h3>
        <button class="close-btn" @click="$emit('close')">
          <font-awesome-icon icon="times" />
        </button>
      </header>
      
      <div class="modal-body">
        
        <details class="accordion" open>
          <summary class="accordion-header">
            Tin có video
            <font-awesome-icon icon="chevron-up" class="accordion-arrow" />
          </summary>
          <div class="accordion-content switch-group">
            <label class="switch-label">Có video</label>
            <label class="switch">
              <input type="checkbox" v-model="hasVideo">
              <span class="slider"></span>
            </label>
          </div>
        </details>
        
        <details class="accordion" open>
          <summary class="accordion-header">
            Đăng bởi
            <font-awesome-icon icon="chevron-up" class="accordion-arrow" />
          </summary>
          <div class="accordion-content radio-group">
            <label>
              <input type="radio" name="seller" value="personal" v-model="postedBy" />
              <span class="radio-label">Cá nhân</span>
              <span class="radio-custom"></span>
            </label>
            <label>
              <input type="radio" name="seller" value="professional" v-model="postedBy" />
              <span class="radio-label">Bán chuyên</span>
              <span class="radio-custom"></span>
            </label>
            <label>
              <input type="radio" name="seller" value="partner" v-model="postedBy" />
              <span class="radio-label">Đối Tác Chợ Tốt</span>
              <span class="radio-custom"></span>
            </label>
          </div>
        </details>

      </div>

      <footer class="modal-footer">
        <button class="footer-btn clear-btn" @click="clearFilters">Xoá lọc</button>
        <button class="footer-btn apply-btn" @click="applyFilters">Áp dụng</button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// SỬA ĐỔI: Nhận prop từ component cha (để lưu trạng thái)
const props = defineProps({
  currentFilters: Object 
});

const emit = defineEmits(['close', 'apply']);

// Trạng thái nội bộ của modal
// === ĐÃ XÓA selectedCondition ===
// SỬA ĐỔI: Khởi tạo trạng thái từ prop
const hasVideo = ref(props.currentFilters.video || false);
const postedBy = ref(props.currentFilters.seller || '');

const clearFilters = () => {
  // === ĐÃ XÓA selectedCondition.value = '' ===
  hasVideo.value = false;
  postedBy.value = '';
  // Gửi sự kiện áp dụng với giá trị rỗng
  emit('apply', {
    // === ĐÃ XÓA condition: selectedCondition.value ===
    video: hasVideo.value,
    seller: postedBy.value
  });
};

const applyFilters = () => {
  emit('apply', {
    // === ĐÃ XÓA condition: selectedCondition.value ===
    video: hasVideo.value,
    seller: postedBy.value
  });
};
</script>

<style scoped>
/* Lớp phủ và nội dung Modal */
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

/* Body (Nền xám, không padding) */
.modal-body {
  overflow-y: auto;
  padding: 0;
  background-color: #f7f7f7;
}

/* Accordion (Nền trắng, có padding) */
.accordion {
  background: white;
  border-bottom: 1px solid #eee;
  /* Thêm padding ở đây để tạo khoảng hở */
  padding: 0 1.5rem; 
}
.accordion:first-child {
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}
.accordion:last-child {
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
  border-bottom: none;
}

.accordion summary {
  padding: 1rem 0; /* Xóa padding 2 bên */
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  list-style: none;
}
.accordion summary::-webkit-details-marker {
  display: none;
}
.accordion-arrow {
  color: #555;
  transition: transform 0.2s;
}
.accordion:not([open]) .accordion-arrow {
  transform: rotate(180deg);
}

.accordion-content {
  padding: 0 0 1rem 0; /* Xóa padding 2 bên */
}

/* CSS cho Radio Buttons */
.radio-group label {
  display: flex;
  align-items: center;
  padding: 0.75rem 0;
  cursor: pointer;
  position: relative;
}
.radio-group input[type="radio"] {
  opacity: 0;
  position: absolute;
}
.radio-label {
  flex-grow: 1;
  font-size: 1rem;
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

/* CSS cho Toggle Switch */
.switch-group {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
}
.switch-label {
  font-size: 1rem;
}
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 28px;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #007bff;
}
input:checked + .slider:before {
  transform: translateX(22px);
}

/* Footer (Nút Xóa lọc, Áp dụng) */
.modal-footer {
  padding: 1rem 1.5rem;
  background-color: white;
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
.apply-btn {
  color: white;
  background-color: #f5a623;
  border: 1px solid #f5a623;
}
.clear-btn:hover {
  background-color: #f0f8ff;
}
.apply-btn:hover {
  opacity: 0.9;
}
</style>