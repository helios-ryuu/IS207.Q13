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
              <!-- Sử dụng CODE chuẩn (HCM, HN, DN) -->
              <button 
                :class="{ active: isCityActive('HCM') }"
                @click="selectQuickCity('HCM')">
                TP. Hồ Chí Minh
              </button>
              <button 
                :class="{ active: isCityActive('HN') }"
                @click="selectQuickCity('HN')">
                Hà Nội
              </button>
              <button 
                :class="{ active: isCityActive('DN') }"
                @click="selectQuickCity('DN')">
                Đà Nẵng
              </button>
            </div>
            <div class="select-group">
              <button class="select-box-button" @click="showCityView">
                <span>{{ selectedCityLabel || 'Chọn tỉnh thành' }}</span>
                <font-awesome-icon icon="chevron-right" class="arrow" />
              </button>
              <button class="select-box-button" :disabled="!selectedCityCode" @click="showDistrictView">
                <span>{{ selectedDistrictLabel || 'Chọn quận huyện' }}</span>
                <font-awesome-icon icon="chevron-right" class="arrow" />
              </button>
              <button class="select-box-button" :disabled="!selectedDistrictCode" @click="showWardView">
                <span>{{ selectedWardLabel || 'Chọn phường xã' }}</span>
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
        :current-value="selectedCityCode"
        @select="handleCitySelect"
        @close="showMainView"
      />

      <LocationSubView
        v-else-if="currentView === 'district'"
        title="Chọn quận huyện"
        :options="districtOptions"
        :current-value="selectedDistrictCode"
        @select="handleDistrictSelect"
        @close="showMainView"
      />

      <LocationSubView
        v-else-if="currentView === 'ward'"
        title="Chọn phường xã"
        :options="wardOptions"
        :current-value="selectedWardName"
        @select="handleWardSelect"
        @close="showMainView"
      />

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import LocationSubView from '../LocationSubView.vue';
import { provinces, getDistrictsByProvince, getWardsByDistrict } from '../../data/vietnamLocations';

const emit = defineEmits(['close', 'applyLocation']);

// --- DỮ LIỆU ---
// City Options: [ { value: 'HN', label: 'Hà Nội' }, ... ]
const cityOptions = computed(() => provinces.map(p => ({ value: p.code, label: p.name })));

const districtOptions = ref([]); // [ { value: 'HN_BD', label: 'Quận Ba Đình' } ]
const wardOptions = ref([]);     // [ { value: 'Phường Phúc Xá', label: 'Phường Phúc Xá' } ]

// --- STATE (SCALAR VALUES) ---
const currentView = ref('main'); // 'main', 'city', 'district', 'ward'

const selectedCityCode = ref(null);      // 'HN'
const selectedDistrictCode = ref(null);  // 'HN_BD'
const selectedWardName = ref(null);      // 'Phường Phúc Xá'

// --- COMPUTED DISPLAY LABELS ---
const selectedCityLabel = computed(() => {
  const found = cityOptions.value.find(o => o.value === selectedCityCode.value);
  return found ? found.label : null;
});

const selectedDistrictLabel = computed(() => {
  const found = districtOptions.value.find(o => o.value === selectedDistrictCode.value);
  return found ? found.label : null;
});

const selectedWardLabel = computed(() => selectedWardName.value);


// --- HELPERS LOAD DATA ---
const loadDistricts = (cityCode) => {
  const dists = getDistrictsByProvince(cityCode);
  districtOptions.value = dists.map(d => ({ value: d.code, label: d.name }));
};

const loadWards = (districtCode) => {
  const wds = getWardsByDistrict(districtCode);
  // Ward không có code, dùng name làm value
  wardOptions.value = wds.map(w => ({ value: w.name, label: w.name }));
};

// --- HANDLERS ---

// 1. QUICK SELECT (Nút bấm nhanh)
const selectQuickCity = (code) => {
  if (selectedCityCode.value !== code) {
    selectedCityCode.value = code;
    // Reset cấp dưới
    selectedDistrictCode.value = null;
    selectedWardName.value = null;
    wardOptions.value = [];
    loadDistricts(code);
  }
};

const isCityActive = (code) => selectedCityCode.value === code;

// 2. VIEW SWITCHERS
const showMainView = () => { currentView.value = 'main'; };
const showCityView = () => { currentView.value = 'city'; };
const showDistrictView = () => { currentView.value = 'district'; };
const showWardView = () => { currentView.value = 'ward'; };


// 3. SELECTION HANDLERS (Từ SubView remit value)

// Chọn Tỉnh
const handleCitySelect = (val) => {
  // val là value (code) ví dụ 'HN' hoặc 'Toàn quốc'
  if (!val || val === 'Toàn quốc') {
    clearFilters(false);
    return;
  }
  
  selectedCityCode.value = val;
  // Reset cấp dưới
  selectedDistrictCode.value = null;
  selectedWardName.value = null;
  wardOptions.value = [];
  
  loadDistricts(val);
  showMainView();
};

// Chọn Quận
const handleDistrictSelect = (val) => {
  selectedDistrictCode.value = val;
  
  // Reset cấp dưới
  selectedWardName.value = null;
  
  if (val) {
    loadWards(val);
  } else {
    wardOptions.value = [];
  }
  showMainView();
};

// Chọn Phường
const handleWardSelect = (val) => {
  selectedWardName.value = val;
  showMainView();
};


// 4. FOOTER ACTIONS
const clearFilters = (closeModal = true) => {
  selectedCityCode.value = null;
  selectedDistrictCode.value = null;
  selectedWardName.value = null;
  districtOptions.value = [];
  wardOptions.value = [];
  
  emit('applyLocation', 'Toàn quốc');
  if(closeModal) emit('close');
};

const applyFilters = () => {
  // Lấy text hiển thị chi tiết nhất
  let result = 'Toàn quốc';
  
  if (selectedWardName.value) {
    result = selectedWardName.value;
  } else if (selectedDistrictLabel.value) {
    result = selectedDistrictLabel.value;
  } else if (selectedCityLabel.value) {
    result = selectedCityLabel.value;
  }
  
  emit('applyLocation', result);
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