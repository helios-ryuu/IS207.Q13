<template>
  <div class="search-filter-bar">

    <!-- 1. CATEGORY PICKER -->
    <div class="category-picker-wrapper">
      <button class="category-dropdown" :class="{ active: selectedCategory }" @click="toggleCategoryModal">
        <font-awesome-icon icon="th-large" class="mobile-icon" />
        <span class="btn-text" v-html="categoryButtonText"></span>
        <svg class="chevron-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
      </button>

      <div v-if="isCategoryModalOpen" class="category-modal">
        <ul class="category-list">
          <li
              v-for="category in categories"
              :key="category.id"
              @click="selectCategory(category)"
              :class="{ selected: selectedCategory?.id === category.id }"
          >
            <div class="category-content-left">
              <img
                  v-if="category.image"
                  :src="getImageUrl(category.image)"
                  alt="icon"
                  class="cat-icon"
              />
              <span class="cat-name" v-html="category.name"></span>
            </div>
            <div class="radio-button"></div>
          </li>
        </ul>
      </div>
    </div>

    <!-- 2. SEARCH INPUT (Đã thêm v-model và @keyup.enter) -->
    <div class="search-input-wrapper">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      
      <!-- SỬA Ở ĐÂY: Thêm v-model và sự kiện Enter -->
      <input 
        type="text" 
        v-model="keyword" 
        placeholder="Tìm sản phẩm..." 
        @keyup.enter="handleSearch"
      />
    </div>

    <!-- 3. LOCATION PICKER -->
    <div class="location-picker-wrapper">
      <button class="location-picker" :class="{ active: savedLocationText && savedLocationText !== 'Toàn quốc' }" @click="toggleLocationModal">
        <font-awesome-icon icon="map-marker-alt" class="mobile-icon" />
        <span class="btn-text">{{ selectedLocationText }}</span>
        <svg class="chevron-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
      </button>

      <!-- Sử dụng Component Modal Mới -->
      <LocationPickerModal
        v-if="isLocationPickerOpen"
        @close="isLocationPickerOpen = false"
        @applyLocation="handleLocationApply"
      />
    </div>

    <!-- 4. SEARCH BUTTON (Đã thêm @click) -->
    <button class="search-button" @click="handleSearch">
      Tìm kiếm
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router'; 
import LocationPickerModal from './modals/LocationPickerModal.vue';

const router = useRouter();
const route = useRoute(); // Để sync URL

// --- STATE TÌM KIẾM ---
const keyword = ref(''); 
// --- QUẢN LÝ MODAL KHU VỰC (SỬ DỤNG SHARED COMPONENT) ---
const isLocationPickerOpen = ref(false);
const savedLocationText = ref('Toàn quốc');

// Đồng bộ URL -> Input & Location
watch(() => route.query, (newQuery) => {
  keyword.value = newQuery.q || '';
  if(newQuery.location) savedLocationText.value = newQuery.location;
}, { immediate: true });

// --- XỬ LÝ TÌM KIẾM (CHUYỂN TRANG) ---
const handleSearch = () => {
  const query = {};

  // 1. Keyword
  if (keyword.value.trim()) {
    query.q = keyword.value.trim();
  }

  // 2. Danh mục
  if (selectedCategory.value) {
    query.category = selectedCategory.value.name.replace(/<br>/g, ' ');
  }

  // 3. Khu vực (Nếu có)
  if (savedLocationText.value && savedLocationText.value !== 'Toàn quốc') {
    query.location = savedLocationText.value;
  }

  console.log("Navigating to products with:", query);
  router.push({ path: '/products', query: query });
};


// --- CÁC LOGIC CŨ GIỮ NGUYÊN BÊN DƯỚI ---

// --- QUẢN LÝ MODAL DANH MỤC ---
const isCategoryModalOpen = ref(false);
const selectedCategory = ref(null);

const toggleCategoryModal = () => {
  isCategoryModalOpen.value = !isCategoryModalOpen.value;
  // isLocationModalOpen.value = false; // LocationPick giờ là Modal riêng
};

const selectCategory = (category) => {
  selectedCategory.value = category;
  isCategoryModalOpen.value = false;
};

const getImageUrl = (imageName) => {
  return `/DanhMuc/${imageName}`;
};

const categoryButtonText = computed(() => {
  if (selectedCategory.value) {
    return selectedCategory.value.name.replace(/<br>/g, ' ');
  }
  return 'Danh mục';
});

const categories = ref([
  { id: 2, name: 'Xe cộ', image: 'XeCo.png' },
  { id: 3, name: 'Thú cưng', image: 'ThuCung.webp' },
  { id: 4, name: 'Đồ gia dụng, nội<br>thất, cây cảnh', image: 'DoGiaDung.webp' },
  { id: 5, name: 'Giải trí, Thể thao, Sở<br>thích', image: 'GiaiTri.webp' },
  { id: 6, name: 'Mẹ và bé', image: 'MeBe.webp' },
  { id: 7, name: 'Dịch vụ, Du lịch', image: 'DuLich.webp' },
  { id: 8, name: 'Cho tặng miễn phí', image: 'QuaTang.webp' },
  { id: 9, name: 'Việc làm', image: '' },
  { id: 10, name: 'Đồ điện tử', image: 'DoDienTu.webp' },
  { id: 11, name: 'Tủ lạnh, máy lạnh,<br>máy giặt', image: 'TuLanh.webp' },
  { id: 12, name: 'Đồ dùng văn phòng,<br>công nông nghiệp', image: 'MayIn.webp' },
  { id: 13, name: 'Thời trang, Đồ dùng<br>cá nhân', image: 'Thoitrang.webp' },
  { id: 14, name: 'Đồ ăn, thực phẩm<br>và các loại khác', image: 'DoAn.webp' },
  { id: 15, name: 'Dịch vụ chăm sóc<br>nhà cửa', image: 'NoiThat.webp' },
  { id: 16, name: 'Tất cả danh mục', image: 'tat-ca-danh-muc.webp' },
]);

// --- QUẢN LÝ MODAL KHU VỰC (SỬ DỤNG SHARED COMPONENT) ---
// const isLocationPickerOpen = ref(false); // MOVED TO TOP
// const savedLocationText = ref('Toàn quốc'); // MOVED TO TOP

const toggleLocationModal = () => {
  isLocationPickerOpen.value = !isLocationPickerOpen.value;
  isCategoryModalOpen.value = false;
};

const selectedLocationText = computed(() => {
  return savedLocationText.value || "Chọn khu vực";
});

const handleLocationApply = (location) => {
  savedLocationText.value = location;
  isLocationPickerOpen.value = false;
};
</script>

<style scoped>
/* GIỮ NGUYÊN CSS CŨ CỦA BẠN */
.search-filter-bar {
  display: flex;
  align-items: center;
  background-color: #ffffff;
  border-radius: 8px;
  padding: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  margin-bottom: 20px;
  gap: 12px;
  position: relative;
}
.category-picker-wrapper { position: relative; }
.category-dropdown {
  display: flex; align-items: center; justify-content: space-between;
  background: #f5f5f5; border: none; border-radius: 6px;
  cursor: pointer; font-size: 16px; font-weight: 500;
  gap: 8px; padding: 10px 14px; white-space: nowrap; min-width: 120px;
}
.category-dropdown.active,
.location-picker.active {
  background-color: #ffd60a;
  color: #333;
}
.category-dropdown span { overflow: hidden; text-overflow: ellipsis; }
.category-modal {
  position: absolute; top: calc(100% + 10px); left: 0; width: 320px;
  background-color: #ffffff; border-radius: 8px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
  z-index: 1000; border: 1px solid #e0e0e0; max-height: 400px; overflow-y: auto;
}
.category-list { list-style: none; padding: 0; margin: 0; }
.category-list li {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f5f5f5;
  transition: background-color 0.2s;
}
.category-list li:last-child { border-bottom: none; }
.category-list li:hover { background-color: #f9f9f9; }
.category-content-left { display: flex; align-items: center; gap: 12px; }
.cat-icon { width: 32px; height: 32px; object-fit: contain; }
.cat-name { font-size: 15px; color: #333; line-height: 1.4; }
.radio-button { width: 18px; height: 18px; border-radius: 50%; border: 2px solid #ccc; flex-shrink: 0; }
.category-list li.selected .radio-button {
  border-color: #ffd60a; background-color: #ffd60a; box-shadow: inset 0 0 0 3px white;
}
.search-input-wrapper {
  display: flex; align-items: center; flex-grow: 1;
  padding: 0 10px; gap: 8px; color: #777;
}
.search-input-wrapper input {
  border: none; outline: none; width: 100%;
  font-size: 16px; background: transparent;
}
.search-input-wrapper input::placeholder { color: #aaa; }
.location-picker-wrapper { position: relative; }
.location-picker {
  font-weight: bold; display: flex; align-items: center;
  background-color: #f5f5f5; border: none; border-radius: 6px;
  padding: 10px 14px; cursor: pointer; font-size: 16px; gap: 8px; white-space: nowrap;
}
.search-button {
  background-color: #ffd60a; border: none; border-radius: 6px;
  padding: 12px 24px; font-size: 16px; font-weight: bold; cursor: pointer;
}
.location-modal {
  position: absolute; top: calc(100% + 10px); right: 0; width: 350px;
  background-color: #ffffff; border-radius: 8px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
  padding: 20px; z-index: 1000; border: 1px solid #e0e0e0;
}
.modal-title { font-size: 1.2rem; font-weight: bold; margin-top: 0; margin-bottom: 20px; text-align: center; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-size: 14px; color: #555; }
.modal-select { width: 100%; padding: 12px; font-size: 16px; border: 1px solid #ccc; border-radius: 6px; background-color: #f9f9f9; }
.loading-text { font-size: 12px; color: #888; margin-top: 4px; }
.modal-apply-btn {
  width: 100%; padding: 12px; font-size: 16px; font-weight: bold;
  color: #333; background-color: #ffd60a; border: none; border-radius: 6px;
  cursor: pointer; margin-top: 10px;
}

/* ===== RESPONSIVE MOBILE ===== */
@media (max-width: 768px) {
  .search-filter-bar {
    flex-wrap: wrap;
    gap: 10px;
    padding: 10px;
  }

  /* Row 1: Category + Location + Search Input */
  .category-picker-wrapper {
    flex: 0 0 auto;
    min-width: auto;
  }

  .category-dropdown {
    padding: 8px 10px;
    font-size: 12px;
    min-width: auto;
  }

  .category-dropdown span {
    max-width: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .location-picker-wrapper {
    flex: 0 0 auto;
  }

  .location-picker {
    padding: 8px 10px;
    font-size: 12px;
    min-width: auto;
  }

  .location-picker span {
    max-width: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .search-input-wrapper {
    flex: 1;
    min-width: 100px;
  }

  .search-input-wrapper > svg {
    display: none;
  }

  .search-input-wrapper input {
    font-size: 14px;
    padding: 8px 10px;
  }

  /* Row 2: Search Button */
  .search-button {
    flex: 0 0 100%;
    order: 5;
    margin-top: 0;
    padding: 12px;
    font-size: 14px;
  }

  /* Hide dropdown arrows on mobile */
  .category-dropdown svg,
  .location-picker svg {
    width: 14px;
    height: 14px;
  }

  /* Modal adjustments */
  .category-modal {
    width: 280px;
    max-height: 60vh;
  }

  .location-modal {
    width: 280px;
  }
}

/* Hide icon on desktop, show on mobile */
.mobile-icon {
  display: none;
  font-size: 16px;
}

@media (max-width: 480px) {
  /* Show icon, hide text and chevron */
  .mobile-icon {
    display: inline-block;
  }

  .category-dropdown .btn-text,
  .location-picker .btn-text {
    display: none;
  }

  .category-dropdown .chevron-icon,
  .location-picker .chevron-icon {
    display: none;
  }

  .category-dropdown,
  .location-picker {
    padding: 10px;
    min-width: 40px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
  }
}
</style>