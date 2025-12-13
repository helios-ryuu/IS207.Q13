<template>
  <div class="search-filter-bar">

    <!-- 1. CATEGORY PICKER -->
    <div class="category-picker-wrapper">
      <button class="category-dropdown" @click="toggleCategoryModal">
        <span v-html="categoryButtonText"></span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
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
      <button class="location-picker" @click="toggleLocationModal">
        <span>{{ selectedLocationText }}</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
      </button>

      <div v-if="isLocationModalOpen" class="location-modal">
        <h3 class="modal-title">Khu vực</h3>
        <div class="form-group">
          <label for="city">Chọn tỉnh thành*</label>
          <select id="city" class="modal-select" v-model="selectedProvince" @change="handleProvinceChange">
            <option :value="null" disabled>Chọn tỉnh thành</option>
            <option v-for="province in provinces" :key="province.id" :value="province.id">
              {{ province.name }}
            </option>
          </select>
          <div v-if="provinces.length === 0" class="loading-text">Đang tải tỉnh thành...</div>
        </div>
        <div class="form-group">
          <label for="district">Chọn quận huyện*</label>
          <select id="district" class="modal-select" v-model="selectedDistrict" :disabled="districts.length === 0">
            <option :value="null" disabled>Chọn quận huyện</option>
            <option v-for="district in districts" :key="district.id" :value="district.id">
              {{ district.name }}
            </option>
          </select>
          <div v-if="districts.length === 0 && selectedProvince" class="loading-text">Đang tải quận huyện...</div>
        </div>
        <button class="modal-apply-btn" @click="applyLocation">Áp dụng</button>
      </div>
    </div>

    <!-- 4. SEARCH BUTTON (Đã thêm @click) -->
    <button class="search-button" @click="handleSearch">
      Tìm kiếm
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router'; // 1. Import Router

const router = useRouter(); // 2. Khởi tạo Router

// --- STATE TÌM KIẾM ---
const keyword = ref(''); // Biến lưu từ khóa

// --- XỬ LÝ TÌM KIẾM (CHUYỂN TRANG) ---
const handleSearch = () => {
  // Tạo object query params
  const query = {};

  // 1. Thêm từ khóa
  if (keyword.value.trim()) {
    query.q = keyword.value.trim();
  }

  // 2. Thêm danh mục (Lưu ý: phải xóa thẻ <br> trong tên danh mục nếu có)
  if (selectedCategory.value) {
    query.category = selectedCategory.value.name.replace(/<br>/g, ' ');
  }

  // 3. Thêm khu vực
  if (savedLocationText.value) {
    // Chỉ lấy tên Tỉnh/Thành phố (phần sau dấu phẩy) để tìm kiếm rộng hơn
    // Ví dụ: "Quận 1, Tp Hồ Chí Minh" -> lấy "Tp Hồ Chí Minh"
    // Hoặc lấy cả chuỗi tùy logic backend của bạn. Ở đây mình lấy cả chuỗi.
    query.location = savedLocationText.value.split(', ').pop(); // Lấy phần cuối (Tỉnh/TP)
  }

  console.log("Navigating to products with:", query);

  // 4. Chuyển hướng sang trang ProductCatalog (giả sử path là /products)
  // Nếu path trong router.js của bạn khác (vd: /catalog), hãy sửa lại dòng dưới
  router.push({ path: '/products', query: query });
};


// --- CÁC LOGIC CŨ GIỮ NGUYÊN BÊN DƯỚI ---

// --- QUẢN LÝ MODAL DANH MỤC ---
const isCategoryModalOpen = ref(false);
const selectedCategory = ref(null);

const toggleCategoryModal = () => {
  isCategoryModalOpen.value = !isCategoryModalOpen.value;
  isLocationModalOpen.value = false;
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

// --- QUẢN LÝ MODAL KHU VỰC ---
const isLocationModalOpen = ref(false);
const provinces = ref([]);
const districts = ref([]);
const selectedProvince = ref(null);
const selectedDistrict = ref(null);
const savedLocationText = ref(null);

const toggleLocationModal = () => {
  isLocationModalOpen.value = !isLocationModalOpen.value;
  isCategoryModalOpen.value = false;
};

const selectedLocationText = computed(() => {
  return savedLocationText.value || "Chọn khu vực";
});

const fetchProvinces = async () => {
  try {
    await new Promise(r => setTimeout(r, 500));
    provinces.value = [
      { id: 'hcm', name: 'Tp Hồ Chí Minh' },
      { id: 'hn', name: 'Hà Nội' },
      { id: 'dn', name: 'Đà Nẵng' },
    ];
    selectedProvince.value = null;
  } catch (error) { console.error("Lỗi:", error); }
};

const fetchDistricts = async (provinceId) => {
  if (!provinceId) return;
  districts.value = [];
  selectedDistrict.value = null;
  try {
    await new Promise(r => setTimeout(r, 500));
    if (provinceId === 'hcm') {
      districts.value = [{ id: 'q1', name: 'Quận 1' }, { id: 'q3', name: 'Quận 3' }];
    } else if (provinceId === 'hn') {
      districts.value = [{ id: 'hk', name: 'Quận Hoàn Kiếm' }, { id: 'bđ', name: 'Quận Ba Đình' }];
    } else {
      districts.value = [{ id: 'other', name: 'Quận/Huyện khác' }];
    }
    selectedDistrict.value = null;
  } catch (error) { console.error("Lỗi khi tải quận huyện:", error); }
};

const handleProvinceChange = () => {
  fetchDistricts(selectedProvince.value);
};

const applyLocation = () => {
  const provinceName = provinces.value.find(p => p.id === selectedProvince.value)?.name;
  const districtName = districts.value.find(d => d.id === selectedDistrict.value)?.name;
  if (provinceName && districtName) {
    savedLocationText.value = `${districtName}, ${provinceName}`;
  }
  isLocationModalOpen.value = false;
};

onMounted(() => {
  fetchProvinces();
});
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
  background: #ffffff; border: 2px solid #333; border-radius: 6px;
  cursor: pointer; font-size: 16px; font-weight: 500;
  gap: 8px; padding: 8px 12px; white-space: nowrap; width: 200px;
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
</style>