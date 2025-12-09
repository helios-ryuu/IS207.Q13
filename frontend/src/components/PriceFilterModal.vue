<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      
      <header class="modal-header">
        <h3>Chọn khoảng giá</h3>
        <button class="close-btn" @click="$emit('close')">
          <font-awesome-icon icon="times" />
        </button>
      </header>
      
      <div class="modal-body">
        <div class="slider-wrapper">
          <Slider
            v-model="priceRange"
            :min="0"
            :max="100000000"
            :step="100000"
            :format="formatSliderLabel"
          />
        </div>
        
        <div class="input-group">
          <div class="input-wrapper">
            <input 
              type="text" 
              class="price-input" 
              placeholder="Giá tối thiểu"
              v-model="minPriceFormatted"
              @blur="updateRangeFromInput(0)"
            />
            <span class="currency-symbol">đ</span>
          </div>
          <span class="input-divider">-</span>
          <div class="input-wrapper">
            <input 
              type="text" 
              class="price-input" 
              placeholder="Giá tối đa"
              v-model="maxPriceFormatted"
              @blur="updateRangeFromInput(1)"
            />
            <span class="currency-symbol">đ</span>
          </div>
        </div>
      </div>

      <footer class="modal-footer">
        <button class="footer-btn clear-btn" @click="clearFilters">Xoá lọc</button>
        <button class="footer-btn apply-btn" @click="applyFilters">Áp dụng</button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Slider from '@vueform/slider'; // Import thư viện Slider

const emit = defineEmits(['close', 'apply']);

// Định dạng số (1000000 -> 1.000.000)
const formatter = new Intl.NumberFormat('vi-VN');

// Trạng thái cho Slider (luôn là mảng 2 giá trị [min, max])
const priceRange = ref([0, 100000000]); // 0 - 100 triệu

// Computed properties để định dạng ô input (thêm/bớt dấu chấm)
const minPriceFormatted = computed({
  get: () => formatter.format(priceRange.value[0]),
  set: (val) => {
    // Chuyển "1.000.000" về số 1000000
    priceRange.value[0] = Number(val.replace(/\./g, '')) || 0;
  }
});

const maxPriceFormatted = computed({
  get: () => formatter.format(priceRange.value[1]),
  set: (val) => {
    priceRange.value[1] = Number(val.replace(/\./g, '')) || 0;
  }
});

// Cập nhật slider khi người dùng blur khỏi ô input
const updateRangeFromInput = (index) => {
  // Đảm bảo min < max
  if (priceRange.value[0] > priceRange.value[1]) {
    if (index === 0) priceRange.value[0] = priceRange.value[1]; // Min = Max
    else priceRange.value[1] = priceRange.value[0]; // Max = Min
  }
};

// Định dạng nhãn cho slider (0 -> 0, 100000000 -> 100 triệu)
const formatSliderLabel = (value) => {
  if (value >= 100000000) return '100 triệu';
  if (value === 0) return '0';
  return `${formatter.format(value)} đ`;
};

// Xử lý nút bấm
const clearFilters = () => {
  priceRange.value = [0, 100000000];
  emit('apply', priceRange.value);
};

const applyFilters = () => {
  emit('apply', priceRange.value);
};
</script>

<style scoped>
/* CSS cho Modal (tương tự các modal khác) */
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
  padding: 2.5rem 1.5rem;
}

/* Slider */
.slider-wrapper {
  margin-bottom: 2rem;
  /* Tùy chỉnh màu sắc slider cho giống ảnh (vàng/cam) */
  --slider-connect-bg: #f5a623;
  --slider-tooltip-bg: #f5a623;
  --slider-handle-ring-color: #f5a623;
}

/* Nhóm Input */
.input-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.input-wrapper {
  position: relative;
  flex: 1;
}
.price-input {
  width: 100%;
  padding: 0.75rem 2rem 0.75rem 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
}
.price-input:focus {
  outline-color: #f5a623;
}
.currency-symbol {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #888;
  pointer-events: none;
}
.input-divider {
  font-size: 1.2rem;
  color: #888;
}

/* Footer (Nút Xóa lọc, Áp dụng) */
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
.apply-btn {
  color: white;
  background-color: #f5a623; /* Màu vàng cam */
  border: 1px solid #f5a623;
}
</style>