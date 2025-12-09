<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      
      <template v-if="currentView === 'main'">
        <header class="modal-header">
          <button class="close-btn" @click="$emit('close')">
            <font-awesome-icon icon="times" />
          </button>
        </header>
        
        <div class="modal-body">
          <div class="location-group">
            <div class="group-header">
              <font-awesome-icon icon="location-crosshairs" />
              <span>Tìm kiếm quanh bạn</span>
            </div>
            <button class="select-box-button">
              <span>Nhập vị trí và khoảng cách tìm kiếm</span>
              <font-awesome-icon icon="chevron-right" class="arrow" />
            </button>
          </div>
          <div class="location-group">
            <div class="group-header">
              <font-awesome-icon icon="building" />
              <span>Tìm theo khu vực</span>
            </div>
            <div class="quick-select-buttons">
              <button 
                :class="{ active: selectedCity === 'TP Hồ Chí Minh' }"
                @click="selectQuickCity('TP Hồ Chí Minh')">
                TP Hồ Chí Minh
              </button>
              <button 
                :class="{ active: selectedCity === 'Hà Nội' }"
                @click="selectQuickCity('Hà Nội')">
                Hà Nội
              </button>
              <button 
                :class="{ active: selectedCity === 'Đà Nẵng' }"
                @click="selectQuickCity('Đà Nẵng')">
                Đà Nẵng
              </button>
            </div>
            <div class="select-group">
              <button class="select-box-button" @click="showCityView">
                <span>{{ selectedCity || 'Chọn tỉnh thành' }}</span>
                <font-awesome-icon icon="chevron-right" class="arrow" />
              </button>
              <button class="select-box-button" :disabled="!selectedCity" @click="showDistrictView">
                <span>{{ selectedDistrict || 'Chọn quận huyện' }}</span>
                <font-awesome-icon icon="chevron-right" class="arrow" />
              </button>
              <button class="select-box-button" :disabled="!selectedDistrict" @click="showWardView">
                <span>{{ selectedWard || 'Chọn phường xã' }}</span>
                <font-awesome-icon icon="chevron-right" class="arrow" />
              </button>
            </div>
          </div>
        </div>
        <footer class="modal-footer">
          <button class="footer-btn clear-btn" @click="clearFilters">Xoá lọc</button>
          <button class="footer-btn apply-btn" @click="applyFilters">Áp dụng</button>
        </footer>
      </template>

      <LocationSubView
        v-else-if="currentView === 'city'"
        title="Chọn tỉnh thành"
        :options="cityOptions"
        :current-value="selectedCity"
        @select="handleCitySelect"
        @close="showMainView"
      />

      <LocationSubView
        v-else-if="currentView === 'district'"
        title="Chọn quận huyện"
        :options="districtOptions"
        :current-value="selectedDistrict"
        @select="handleDistrictSelect"
        @close="showMainView"
      />

      <LocationSubView
        v-else-if="currentView === 'ward'"
        title="Chọn phường xã"
        :options="wardOptions"
        :current-value="selectedWard"
        @select="handleWardSelect"
        @close="showMainView"
      />

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import LocationSubView from './LocationSubView.vue';

const emit = defineEmits(['close', 'applyLocation']);

// ===================================================================
// --- 📍 CHÚ THÍCH: NƠI THÊM DỮ LIỆU ĐỊA ĐIỂM ---
//
// Trong một dự án thực tế, bạn sẽ gọi API để lấy dữ liệu này.
// Hiện tại, chúng ta đang dùng dữ liệu giả lập (hard-code).
// ===================================================================

// --- BƯỚC 1: Thêm TỈNH THÀNH tại đây ---
const allCities = [
  { value: 'TP Hồ Chí Minh', label: 'TP Hồ Chí Minh' },
  { value: 'Hà Nội', label: 'Hà Nội' },
  { value: 'Đà Nẵng', label: 'Đà Nẵng' },
  { value: 'Bình Dương', label: 'Bình Dương' },
  { value: 'An Giang', label: 'An Giang' },
  { value: 'Bà Rịa - Vũng Tàu', label: 'Bà Rịa - Vũng Tàu' },
  { value: 'Bắc Giang', label: 'Bắc Giang' },
  // ... (Thêm 63 tỉnh thành khác vào đây)
];

// --- BƯỚC 2: Thêm QUẬN HUYỆN tại đây ---
// Key (khóa) phải khớp chính xác với `value` của Tỉnh thành ở trên.
const allDistricts = {
  'TP Hồ Chí Minh': [ 
    { value: 'Thành phố Thủ Đức', label: 'Thành phố Thủ Đức' }, 
    { value: 'Quận 1', label: 'Quận 1' }, 
    { value: 'Quận 3', label: 'Quận 3' }, 
    { value: 'Quận 4', label: 'Quận 4' },
    // ... (Thêm các quận huyện khác của TP.HCM)
  ],
  'Hà Nội': [ 
    { value: 'Quận Ba Đình', label: 'Quận Ba Đình' }, 
    { value: 'Quận Cầu Giấy', label: 'Quận Cầu Giấy' } 
  ],
  'Đà Nẵng': [ 
    { value: 'Quận Hải Châu', label: 'Quận Hải Châu' }, 
    { value: 'Quận Sơn Trà', label: 'Quận Sơn Trà' } 
  ],
  // ... (Thêm các cặp Tỉnh: [Quận/Huyện] khác)
};

// --- BƯỚC 3: Thêm PHƯỜNG XÃ tại đây ---
// Key (khóa) phải khớp chính xác với `value` của Quận huyện ở trên.
const allWards = {
  'Quận 1': [ 
    { value: 'Phường Bến Nghé', label: 'Phường Bến Nghé' }, 
    { value: 'Phường Cầu Ông Lãnh', label: 'Phường Cầu Ông Lãnh' } 
  ],
  'Thành phố Thủ Đức': [ 
    { value: 'Phường Linh Trung', label: 'Phường Linh Trung' }, 
    { value: 'Phường Linh Chiểu', label: 'Phường Linh Chiểu' } 
  ],
  // ... (Thêm các cặp Quận: [Phường/Xã] khác)
};
// ------------------------------------

// Trạng thái nội bộ
const currentView = ref('main'); // 'main', 'city', 'district', 'ward'
const selectedCity = ref(null);
const selectedDistrict = ref(null);
const selectedWard = ref(null);

// Danh sách động
const cityOptions = ref(allCities);
const districtOptions = ref([]);
const wardOptions = ref([]);

// --- Hàm chuyển đổi View ---
const showMainView = () => { currentView.value = 'main'; };
const showCityView = () => { currentView.value = 'city'; };
const showDistrictView = () => { currentView.value = 'district'; };
const showWardView = () => { currentView.value = 'ward'; };

// --- Hàm xử lý Data ---
const loadDistricts = (city) => {
  selectedDistrict.value = null;
  selectedWard.value = null;
  wardOptions.value = [];
  districtOptions.value = allDistricts[city] || [];
};
const loadWards = (district) => {
  selectedWard.value = null;
  wardOptions.value = allWards[district] || [];
};

// --- Hàm xử lý sự kiện ---
const selectQuickCity = (city) => {
  selectedCity.value = city;
  loadDistricts(city);
};

// Xử lý khi chọn xong ở SubView
const handleCitySelect = (city) => {
  if(city === 'Toàn quốc') {
    clearFilters(false); 
    return; 
  }
  selectedCity.value = city;
  if(city === null) { 
    selectedDistrict.value = null;
    selectedWard.value = null;
    districtOptions.value = [];
    wardOptions.value = [];
  } else {
    loadDistricts(city);
  }
  showMainView();
};
const handleDistrictSelect = (district) => {
  selectedDistrict.value = district;
  if(district === null) {
    selectedWard.value = null;
    wardOptions.value = [];
  } else {
    loadWards(district);
  }
  showMainView();
};
const handleWardSelect = (ward) => {
  selectedWard.value = ward;
  showMainView();
};

// Xử lý nút Footer
const clearFilters = (closeModal = true) => {
  selectedCity.value = null;
  selectedDistrict.value = null;
  selectedWard.value = null;
  districtOptions.value = [];
  wardOptions.value = [];
  emit('applyLocation', 'Toàn quốc');
  if(closeModal) emit('close');
};

const applyFilters = () => {
  let locationText = 'Toàn quốc';
  if (selectedWard.value) locationText = selectedWard.value;
  else if (selectedDistrict.value) locationText = selectedDistrict.value;
  else if (selectedCity.value) locationText = selectedCity.value;
  
  emit('applyLocation', locationText);
  emit('close');
};

</script>

<style scoped>
/* (Toàn bộ CSS giữ nguyên như file trước) */
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
  max-width: 480px;
  background: white;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  max-height: 80vh;
  height: 600px; 
  overflow: hidden;
}

.modal-header {
  padding: 0.5rem 1rem;
  display: flex;
  justify-content: flex-end;
  align-items: center;
}
.close-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #888;
}

/* Body */
.modal-body {
  overflow-y: auto;
  padding: 0 1.5rem 1rem 1.5rem;
  flex-grow: 1;
}
.location-group {
  margin-bottom: 1.5rem;
}
.group-header {
  font-size: 1.1rem;
  font-weight: bold;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.select-box-button {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding: 1rem;
  background-color: #f5f5f5;
  border-radius: 8px;
  cursor: pointer;
  color: #333;
  font-weight: 500;
  border: 1px solid #f5f5f5;
  margin-bottom: 0.75rem;
  font-size: 1rem;
  text-align: left;
}
.select-box-button:hover {
  border-color: #ccc;
}
.select-box-button:disabled {
  background-color: #f9f9f9;
  color: #ccc;
  cursor: not-allowed;
}
.select-box-button .arrow {
  color: #888;
}

/* CSS cho Radio Buttons (kiểu nút) */
.quick-select-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}
.quick-select-buttons button {
  padding: 0.5rem 1rem;
  border: 1px solid #ccc;
  border-radius: 20px;
  background: white;
  cursor: pointer;
}
.quick-select-buttons button.active {
  background-color: #f5a623;
  border-color: #f5a623;
  color: black;
  font-weight: 500;
}
.select-group {
  display: flex;
  flex-direction: column;
}


/* Footer */
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
  color: black;
  background-color: #f5a623;
  border: 1px solid #f5a623;
}
</style>