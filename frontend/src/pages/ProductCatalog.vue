<template>
  <HeaderOther />

  <div class="product-catalog">
    <div class="container">
      
      <div class="filter-bar">
        
        <div class="top-filter-bar filter-group">
          <button class="filter-btn main-filter-btn" @click="isFilterModalOpen = true">
            <font-awesome-icon icon="filter" /> Lọc
          </button>
          
          <div class="filter-dropdown-wrapper">
            <button class="filter-select" @click="isCategoryModalOpen = true">
              <span>{{ selectedCategory === '' ? 'Danh mục' : selectedCategory }}</span>
              <font-awesome-icon icon="chevron-down" class="dropdown-arrow-static" />
            </button>
          </div>

          <div class="filter-dropdown-wrapper">
            <button class="filter-select" @click="isPriceModalOpen = true">
              <span>{{ selectedPriceDisplay }}</span>
              <font-awesome-icon icon="chevron-down" class="dropdown-arrow-static" />
            </button>
          </div>

          <div class="filter-dropdown-wrapper">
            <button class="filter-select" @click="isConditionModalOpen = true">
              <span>{{ selectedConditionDisplay }}</span>
              <font-awesome-icon icon="chevron-down" class="dropdown-arrow-static" />
            </button>
          </div>
        </div>
        
        <div class="filter-group location-filter">
          <span class="filter-label">Khu vực:</span>
          
          <button
            v-for="region in regions"
            :key="region"
            class="filter-btn"
            :class="{ active: selectedRegion === region }"
            @click="selectRegion(region)"
          >
            {{ region }}
          </button>
          
          <button 
            class="filter-btn"
            :class="{ active: selectedRegion === 'Gần tôi' }"
            @click="selectRegion('Gần tôi')"
          >
            Gần tôi
            <font-awesome-icon icon="map-marker-alt" />
          </button>

          <button class="clear-btn" @click="clearAllFilters">
            <font-awesome-icon icon="trash" /> Xóa lọc
          </button>
        </div>

        <div 
          class="category-bar" 
          v-if="selectedCategory === 'Đồ gia dụng, Nội thất, Cây cảnh'"
        >
          <div class="category-item" :class="{ active: selectedSubCategory === 'Bàn ghế' }" @click="selectSubCategory('Bàn ghế')" title="Bàn ghế"><div class="icon-wrapper"><font-awesome-icon icon="chair" /></div><span>Bàn ghế</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Tủ, kệ, gia đình' }" @click="selectSubCategory('Tủ, kệ, gia đình')" title="Tủ, kệ, gia đình"><div class="icon-wrapper"><span class="material-symbols-outlined">dresser</span></div><span>Tủ, kệ, gia đình</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Giường, chăn ga gối nệm' }" @click="selectSubCategory('Giường, chăn ga gối nệm')" title="Giường, chăn ga gối nệm"><div class="icon-wrapper"><font-awesome-icon icon="bed" /></div><span>Giường, chăn ga gối nệm</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Bếp, lò, đồ điện nhà bếp' }" @click="selectSubCategory('Bếp, lò, đồ điện nhà bếp')" title="Bếp, lò, đồ điện nhà bếp"><div class="icon-wrapper"><font-awesome-icon icon="blender" /></div><span>Bếp, lò, đồ điện nhà bếp</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Dụng cụ nhà bếp' }" @click="selectSubCategory('Dụng cụ nhà bếp')" title="Dụng cụ nhà bếp"><div class="icon-wrapper"><font-awesome-icon icon="kitchen-set" /></div><span>Dụng cụ nhà bếp</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Quạt' }" @click="selectSubCategory('Quạt')" title="Quạt"><div class="icon-wrapper"><font-awesome-icon icon="fan" /></div><span>Quạt</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Đèn' }" @click="selectSubCategory('Đèn')" title="Đèn"><div class="icon-wrapper"><font-awesome-icon icon="lightbulb" /></div><span>Đèn</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Cây cảnh, đồ trang trí' }" @click="selectSubCategory('Cây cảnh, đồ trang trí')" title="Cây cảnh, đồ trang trí"><div class="icon-wrapper"><font-awesome-icon icon="tree" /></div><span>Cây cảnh, đồ trang trí</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Thiết bị vệ sinh, nhà tắm' }" @click="selectSubCategory('Thiết bị vệ sinh, nhà tắm')" title="Thiết bị vệ sinh, nhà tắm"><div class="icon-wrapper"><font-awesome-icon icon="toilet" /></div><span>Thiết bị vệ sinh, nhà tắm</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Nội thất, đồ gia dụng khác' }" @click="selectSubCategory('Nội thất, đồ gia dụng khác')" title="Nội thất, đồ gia dụng khác"><div class="icon-wrapper"><font-awesome-icon icon="dungeon" /></div><span>Nội thất, đồ gia dụng khác</span></div>
        </div>

        <div 
          class="category-bar" 
          v-else-if="selectedCategory === 'Đồ điện tử'"
        >
          <div class="category-item" :class="{ active: selectedSubCategory === 'Điện thoại' }" @click="selectSubCategory('Điện thoại')" title="Điện thoại"><div class="icon-wrapper"><font-awesome-icon icon="mobile-alt" /></div><span>Điện thoại</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Máy tính bảng' }" @click="selectSubCategory('Máy tính bảng')" title="Máy tính bảng"><div class="icon-wrapper"><font-awesome-icon icon="tablet-alt" /></div><span>Máy tính bảng</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Laptop' }" @click="selectSubCategory('Laptop')" title="Laptop"><div class="icon-wrapper"><font-awesome-icon icon="laptop" /></div><span>Laptop</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Máy tính để bàn' }" @click="selectSubCategory('Máy tính để bàn')" title="Máy tính để bàn"><div class="icon-wrapper"><font-awesome-icon icon="desktop" /></div><span>Máy tính để bàn</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Máy ảnh, Máy quay' }" @click="selectSubCategory('Máy ảnh, Máy quay')" title="Máy ảnh, Máy quay"><div class="icon-wrapper"><font-awesome-icon icon="camera" /></div><span>Máy ảnh, Máy quay</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Tivi, Âm thanh' }" @click="selectSubCategory('Tivi, Âm thanh')" title="Tivi, Âm thanh"><div class="icon-wrapper"><font-awesome-icon icon="tv" /></div><span>Tivi, Âm thanh</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Thiết bị đeo thông minh' }" @click="selectSubCategory('Thiết bị đeo thông minh')" title="Thiết bị đeo thông minh"><div class="icon-wrapper"><font-awesome-icon icon="stopwatch" /></div><span>Thiết bị đeo thông minh</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Phụ kiện (Màn hình,..)' }" @click="selectSubCategory('Phụ kiện (Màn hình,..)')" title="Phụ kiện (Màn hình,..)"><div class="icon-wrapper"><font-awesome-icon icon="plug" /></div><span>Phụ kiện (Màn hình,..)</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Linh kiện (RAM,...)' }" @click="selectSubCategory('Linh kiện (RAM,...)')" title="Linh kiện (RAM,...)"><div class="icon-wrapper"><font-awesome-icon icon="microchip" /></div><span>Linh kiện (RAM,...)</span></div>
        </div>

        <div 
          class="category-bar" 
          v-else-if="selectedCategory === 'Xe cộ'"
        >
          <div class="category-item" :class="{ active: selectedSubCategory === 'Ô tô' }" @click="selectSubCategory('Ô tô')" title="Ô tô"><div class="icon-wrapper"><font-awesome-icon icon="car" /></div><span>Ô tô</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Xe máy' }" @click="selectSubCategory('Xe máy')" title="Xe máy"><div class="icon-wrapper"><font-awesome-icon icon="motorcycle" /></div><span>Xe máy</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Xe tải, xe ben' }" @click="selectSubCategory('Xe tải, xe ben')" title="Xe tải, xe ben"><div class="icon-wrapper"><font-awesome-icon icon="truck" /></div><span>Xe tải, xe ben</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Xe điện' }" @click="selectSubCategory('Xe điện')" title="Xe điện"><div class="icon-wrapper"><font-awesome-icon icon="bolt" /></div><span>Xe điện</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Xe đạp' }" @click="selectSubCategory('Xe đạp')" title="Xe đạp"><div class="icon-wrapper"><font-awesome-icon icon="bicycle" /></div><span>Xe đạp</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Phương tiện khác' }" @click="selectSubCategory('Phương tiện khác')" title="Phương tiện khác"><div class="icon-wrapper"><font-awesome-icon icon="tractor" /></div><span>Phương tiện khác</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Phụ tùng xe' }" @click="selectSubCategory('Phụ tùng xe')" title="Phụ tùng xe"><div class="icon-wrapper"><font-awesome-icon icon="cog" /></div><span>Phụ tùng xe</span></div>
        </div>
        
        <div 
          class="category-bar" 
          v-else-if="selectedCategory === 'Tủ lạnh, Máy lạnh, Máy giặt'"
        >
          <div class="category-item" :class="{ active: selectedSubCategory === 'Tủ lạnh' }" @click="selectSubCategory('Tủ lạnh')" title="Tủ lạnh"><div class="icon-wrapper"><span class="material-symbols-outlined">kitchen</span></div><span>Tủ lạnh</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Máy lạnh, điều hoà' }" @click="selectSubCategory('Máy lạnh, điều hoà')" title="Máy lạnh, điều hoà"><div class="icon-wrapper"><font-awesome-icon icon="snowflake" /></div><span>Máy lạnh, điều hoà</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Máy giặt' }" @click="selectSubCategory('Máy giặt')" title="Máy giặt"><div class="icon-wrapper"><font-awesome-icon icon="soap" /></div><span>Máy giặt</span></div>
        </div>

        <div 
          class="category-bar" 
          v-else-if="selectedCategory === 'Thú cưng'"
        >
          <div class="category-item" :class="{ active: selectedSubCategory === 'Chó' }" @click="selectSubCategory('Chó')" title="Chó"><div class="icon-wrapper"><font-awesome-icon icon="dog" /></div><span>Chó</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Mèo' }" @click="selectSubCategory('Mèo')" title="Mèo"><div class="icon-wrapper"><font-awesome-icon icon="cat" /></div><span>Mèo</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Chim' }" @click="selectSubCategory('Chim')" title="Chim"><div class="icon-wrapper"><font-awesome-icon icon="dove" /></div><span>Chim</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Thú cưng khác' }" @click="selectSubCategory('Thú cưng khác')" title="Thú cưng khác"><div class="icon-wrapper"><font-awesome-icon icon="fish" /></div><span>Thú cưng khác</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Phụ kiện, Thức ăn, Dịch vụ' }" @click="selectSubCategory('Phụ kiện, Thức ăn, Dịch vụ')" title="Phụ kiện, Thức ăn, Dịch vụ"><div class="icon-wrapper"><font-awesome-icon icon="bone" /></div><span>Phụ kiện, Thức ăn,...</span></div>
        </div>
        
        <div 
          class="category-bar" 
          v-else-if="selectedCategory === 'Thời trang, Đồ dùng cá nhân'"
        >
          <div class="category-item" :class="{ active: selectedSubCategory === 'Quần áo' }" @click="selectSubCategory('Quần áo')" title="Quần áo"><div class="icon-wrapper"><font-awesome-icon icon="shirt" /></div><span>Quần áo</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Đồng hồ' }" @click="selectSubCategory('Đồng hồ')" title="Đồng hồ"><div class="icon-wrapper"><font-awesome-icon icon="clock" /></div><span>Đồng hồ</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Giày dép' }" @click="selectSubCategory('Giày dép')" title="Giày dép"><div class="icon-wrapper"><font-awesome-icon icon="shoe-prints" /></div><span>Giày dép</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Túi xách' }" @click="selectSubCategory('Túi xách')" title="Túi xách"><div class="icon-wrapper"><font-awesome-icon icon="shopping-bag" /></div><span>Túi xách</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Nước hoa' }" @click="selectSubCategory('Nước hoa')" title="Nước hoa"><div class="icon-wrapper"><font-awesome-icon icon="spray-can" /></div><span>Nước hoa</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Phụ kiện thời trang' }" @click="selectSubCategory('Phụ kiện thời trang')" title="Phụ kiện thời trang"><div class="icon-wrapper"><font-awesome-icon icon="gem" /></div><span>Phụ kiện thời trang</span></div>
        </div>
        
        <div 
          class="category-bar" 
          v-else-if="selectedCategory === 'Giải trí, Thể thao, Sở thích'"
        >
          <div class="category-item" :class="{ active: selectedSubCategory === 'Nhạc cụ' }" @click="selectSubCategory('Nhạc cụ')" title="Nhạc cụ"><div class="icon-wrapper"><font-awesome-icon icon="guitar" /></div><span>Nhạc cụ</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Sách' }" @click="selectSubCategory('Sách')" title="Sách"><div class="icon-wrapper"><font-awesome-icon icon="book" /></div><span>Sách</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Đồ thể thao, Dã ngoại' }" @click="selectSubCategory('Đồ thể thao, Dã ngoại')" title="Đồ thể thao, Dã ngoại"><div class="icon-wrapper"><font-awesome-icon icon="futbol" /></div><span>Đồ thể thao, Dã...</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Đồ sưu tầm, đồ cổ' }" @click="selectSubCategory('Đồ sưu tầm, đồ cổ')" title="Đồ sưu tầm, đồ cổ"><div class="icon-wrapper"><font-awesome-icon icon="image" /></div><span>Đồ sưu tầm, đồ cổ</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Thiết bị chơi game' }" @click="selectSubCategory('Thiết bị chơi game')" title="Thiết bị chơi game"><div class="icon-wrapper"><font-awesome-icon icon="gamepad" /></div><span>Thiết bị chơi game</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Sở thích khác' }" @click="selectSubCategory('Sở thích khác')" title="Sở thích khác"><div class="icon-wrapper"><font-awesome-icon icon="compact-disc" /></div><span>Sở thích khác</span></div>
        </div>

        <div 
          class="category-bar" 
          v-else-if="selectedCategory === 'Đồ dùng văn phòng, Công nông nghiệp'"
        >
          <div class="category-item" :class="{ active: selectedSubCategory === 'Đồ dùng văn phòng' }" @click="selectSubCategory('Đồ dùng văn phòng')" title="Đồ dùng văn phòng"><div class="icon-wrapper"><font-awesome-icon icon="print" /></div><span>Đồ dùng văn phòng</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Đồ chuyên dụng, Giống nuôi trồng' }" @click="selectSubCategory('Đồ chuyên dụng, Giống nuôi trồng')" title="Đồ chuyên dụng, Giống nuôi trồng"><div class="icon-wrapper"><font-awesome-icon icon="screwdriver-wrench" /></div><span>Đồ chuyên dụng,...</span></div>
        </div>

        <div 
          class="category-bar" 
          v-else-if="selectedCategory === 'Đồ ăn, Thực phẩm và các loại khác'"
        >
          <div class="category-item" :class="{ active: selectedSubCategory === 'Thịt bò' }" @click="selectSubCategory('Thịt bò')" title="Thịt bò"><div class="icon-wrapper"><span class="material-symbols-outlined">kebab_dining</span></div><span>Thịt bò</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Thịt heo' }" @click="selectSubCategory('Thịt heo')" title="Thịt heo"><div class="icon-wrapper"><font-awesome-icon icon="bacon" /></div><span>Thịt heo</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Thịt gà' }" @click="selectSubCategory('Thịt gà')" title="Thịt gà"><div class="icon-wrapper"><font-awesome-icon icon="drumstick-bite" /></div><span>Thịt gà</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Hải sản' }" @click="selectSubCategory('Hải sản')" title="Hải sản"><div class="icon-wrapper"><span class="material-symbols-outlined">set_meal</span></div><span>Hải sản</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Trứng' }" @click="selectSubCategory('Trứng')" title="Trứng"><div class="icon-wrapper"><font-awesome-icon icon="egg" /></div><span>Trứng</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Sữa' }" @click="selectSubCategory('Sữa')" title="Sữa"><div class="icon-wrapper"><font-awesome-icon icon="mug-hot" /></div><span>Sữa</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Bánh' }" @click="selectSubCategory('Bánh')" title="Bánh"><div class="icon-wrapper"><font-awesome-icon icon="birthday-cake" /></div><span>Bánh</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Kẹo' }" @click="selectSubCategory('Kẹo')" title="Kẹo"><div class="icon-wrapper"><font-awesome-icon icon="candy-cane" /></div><span>Kẹo</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Nước ngọt' }" @click="selectSubCategory('Nước ngọt')" title="Nước ngọt"><div class="icon-wrapper"><font-awesome-icon icon="cocktail" /></div><span>Nước ngọt</span></div>
          <div class="category-item" :class="{ active: selectedSubCategory === 'Khác' }" @click="selectSubCategory('Khác')" title="Khác"><div class="icon-wrapper"><font-awesome-icon icon="ellipsis-h" /></div><span>Khác</span></div>
        </div>

      </div>

      <div class="product-content">
        <div class="sort-options">
          <div class="tabs">
            <button
              v-for="tab in tabs"
              :key="tab"
              class="tab-btn"
              :class="{ active: selectedTab === tab }"
              @click="selectTab(tab)"
            >
              {{ tab }}
            </button>
          </div>
          <div class="sort-controls">
            <select class="sort-select" @change="handleSortChange">
              <option value="newest">Tin mới nhất</option>
              <option value="oldest">Tin cũ nhất</option>
              <option value="price_desc">Giá giảm dần</option>
              <option value="price_asc">Giá tăng dần</option>
            </select>
            <button class="view-toggle" @click="toggleViewMode">
               <font-awesome-icon v-if="viewMode === 'grid'" icon="th-large" /> 
               <font-awesome-icon v-else icon="list" /> 
            </button>
          </div>
        </div>

        <!-- 1. TRẠNG THÁI LOADING -->
        <div v-if="loading" class="state-container">
          <div class="spinner"></div>
          <p>Đang tải sản phẩm...</p>
        </div>

        <!-- 2. TRẠNG THÁI KHÔNG CÓ DỮ LIỆU -->
        <div v-else-if="products.length === 0" class="state-container">
          <span style="font-size: 60px;">📦</span>
          <p>Không tìm thấy sản phẩm nào phù hợp.</p>
          <button class="btn-reset" @click="clearAllFilters">Xóa bộ lọc</button>
        </div>

        <div class="product-grid" :class="{ 'list-view': viewMode === 'list' }">
          <ProductCard v-for="product in products" :key="product.id" :product="product" />
        </div>
      </div>
    </div>
  </div>

  <CategoryModal 
  v-if="isCategoryModalOpen" 
  @close="isCategoryModalOpen = false"
  @select-category="handleCategorySelect"
  />
<!-- Lưu ý: Phải dùng @select-category (khớp với defineEmits ở bước 1) -->

  <AdvancedFilterModal
    v-if="isFilterModalOpen"
    :current-filters="advancedFilters"
    @close="isFilterModalOpen = false"
    @apply="handleApplyFilters"
  />

  <PriceFilterModal
    v-if="isPriceModalOpen"
    @close="isPriceModalOpen = false"
    @apply="handlePriceApply"
  />

  <ConditionModal
    v-if="isConditionModalOpen"
    :current-value="selectedCondition"
    @close="isConditionModalOpen = false"
    @apply="handleConditionApply"
  />
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../utils/api';
import ProductCard from '../components/product/ProductCardSimple.vue';
import CategoryModal from '../components/modals/CategoryModal.vue';
import AdvancedFilterModal from '../components/modals/AdvancedFilterModal.vue';
import PriceFilterModal from '../components/modals/PriceFilterModal.vue';
import ConditionModal from '../components/modals/ConditionModal.vue';
import HeaderOther from '../components/layout/SearchHeader.vue';

const route = useRoute();
const router = useRouter();

// --- STATE ---
const products = ref([]);
const categories = ref([]);
const loading = ref(false);

// Filter State
const selectedCategory = ref('');
const selectedSubCategory = ref('');
const selectedRegion = ref('');
const selectedPriceValue = ref([0, 100000000]);
const selectedCondition = ref('');
const viewMode = ref('grid');
const sortOption = ref('newest');
const keyword = ref('');

// Modals State
const isCategoryModalOpen = ref(false);
const isFilterModalOpen = ref(false);
const isPriceModalOpen = ref(false);
const isConditionModalOpen = ref(false);
const advancedFilters = ref({ video: false, seller: '' });

// Constants
const tabs = ref(['Tất cả', 'Cá nhân', 'Bán chuyên']);
const selectedTab = ref('Tất cả');
const regions = ref(['TP. Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ', 'Bình Dương', 'Hải Phòng', 'Đồng Nai']);

const formatter = new Intl.NumberFormat('vi-VN');

// Helper URL ảnh
const getImageUrl = (url) => {
  if (!url) return 'https://via.placeholder.com/200/eeeeee/cccccc?text=No+Image';
  if (url.startsWith('http')) return url;
  return `http://localhost:8000${url}`;
};

// --- HÀM MAP DỮ LIỆU (QUAN TRỌNG) ---
const mapProduct = (item) => {
  // 1. Xử lý ảnh
  let rawUrl = '';
  if (item.images && item.images.length > 0) rawUrl = item.images[0].url;
  else if (item.thumbnail) rawUrl = item.thumbnail;
  else if (item.variants?.[0]?.images?.[0]) {
     const img = item.variants[0].images[0];
     rawUrl = typeof img === 'string' ? img : img.url;
  }
  
  // 2. Xử lý giá
  const priceVal = item.price_range?.min || item.variants?.[0]?.price || 0;
  
  // 3. Xử lý địa chỉ (Lấy từ Description)
  let locationDisplay = 'Toàn quốc';
  if (item.description) {
    // Tìm dòng có chữ "Khu vực:"
    const match = item.description.match(/Khu vực:\s*(.*?)(\n|$)/);
    if (match && match[1]) {
      locationDisplay = match[1].trim();
    }
  }

  return {
    id: item.id,
    title: item.name,
    price: formatter.format(priceVal) + ' đ',
    seller: item.seller?.name || 'Shop',
    location: locationDisplay, // Hiển thị địa chỉ thật
    imageUrl: getImageUrl(rawUrl),
    is_favorited: item.is_favorited, 
  };
};

// --- COMPUTED ---
const selectedPriceDisplay = computed(() => {
  const [min, max] = selectedPriceValue.value;
  if (min === 0 && max === 100000000) return 'Giá';
  if (min === 0 && max < 100000000) return `Dưới ${formatter.format(max)} đ`;
  if (min > 0 && max === 100000000) return `Trên ${formatter.format(min)} đ`;
  return `${formatter.format(min)} - ${formatter.format(max)} đ`;
});

const selectedConditionDisplay = computed(() => {
  if (selectedCondition.value === 'new') return 'Mới';
  if (selectedCondition.value === 'used') return 'Đã sử dụng';
  return 'Tình trạng';
});

// --- CORE FUNCTIONS ---
const updateUrl = (newParams) => {
  const query = { ...route.query, ...newParams };
  Object.keys(query).forEach(key => {
    if (query[key] === undefined || query[key] === null || query[key] === '') {
      delete query[key];
    }
  });
  console.log("🔄 Updating URL:", query);
  router.push({ query });
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const params = {
      page: 1,
      per_page: 20,
      keyword: keyword.value,
      category: selectedCategory.value,
      subcategory: selectedSubCategory.value, // Backend cần nhận cái này
      location: selectedRegion.value,         // Backend cần nhận cái này
      price_min: selectedPriceValue.value[0] > 0 ? selectedPriceValue.value[0] : undefined,
      price_max: selectedPriceValue.value[1] < 100000000 ? selectedPriceValue.value[1] : undefined,
      sort: sortOption.value,
      condition: selectedCondition.value
    };

    console.log("📡 Calling API with:", params);
    const res = await api.get('/products', { params });
    
    const apiData = res.data.data || [];
    products.value = apiData.map(mapProduct);

  } catch (e) {
    console.error("Fetch error:", e);
  } finally {
    loading.value = false;
  }
};

// --- EVENT HANDLERS ---
const handleCategorySelect = (catName) => {
  isCategoryModalOpen.value = false;
  updateUrl({ category: catName, subcategory: undefined });
};

const selectSubCategory = (sub) => {
  const newVal = selectedSubCategory.value === sub ? undefined : sub;
  updateUrl({ subcategory: newVal });
};

const selectRegion = (region) => {
  const newVal = selectedRegion.value === region ? undefined : region;
  updateUrl({ location: newVal });
};

const handlePriceApply = (range) => {
  isPriceModalOpen.value = false;
  const isDefault = range[0] === 0 && range[1] === 100000000;
  updateUrl({ 
    price_min: isDefault ? undefined : range[0],
    price_max: isDefault ? undefined : range[1]
  });
};

const handleConditionApply = (val) => {
  isConditionModalOpen.value = false;
  updateUrl({ condition: val });
};

const handleSortChange = (e) => {
  updateUrl({ sort: e.target.value });
};

const clearAllFilters = () => {
  router.push({ query: {} });
};

const toggleViewMode = () => {
  viewMode.value = viewMode.value === 'grid' ? 'list' : 'grid';
};

const selectTab = (tab) => {
  selectedTab.value = tab;
};

// --- WATCHER ---
watch(() => route.query, (newQuery) => {
  console.log("⚡ URL Changed detected:", newQuery);
  selectedCategory.value = newQuery.category || '';
  selectedSubCategory.value = newQuery.subcategory || '';
  selectedRegion.value = newQuery.location || '';
  keyword.value = newQuery.q || '';
  sortOption.value = newQuery.sort || 'newest';
  selectedCondition.value = newQuery.condition || '';
  
  const pMin = parseInt(newQuery.price_min) || 0;
  const pMax = parseInt(newQuery.price_max) || 100000000;
  selectedPriceValue.value = [pMin, pMax];

  fetchProducts();
}, { immediate: true, deep: true });

onMounted(async () => {
  try {
    const res = await api.get('/categories');
    categories.value = res.data.data || [];
  } catch (e) {}
});

// Mock Apply Advanced Filter
const handleApplyFilters = () => isFilterModalOpen.value = false;
</script>

<style scoped>
/* TOÀN BỘ CSS CŨ GIỮ NGUYÊN, NGOẠI TRỪ 2 KHỐI DƯỚI ĐÂY */

/* ... (CSS từ .product-catalog đến .clear-btn giữ nguyên) ... */
.product-catalog {
  padding-top: 20px;
  background-color: #f0f2f5;
}
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}
.filter-bar {
  background-color: white;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 0; 
}
.filter-group, .category-bar, .top-filter-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
  padding: 15px; 
}
.filter-group {
  border-bottom: 1px solid #eee;
}
.filter-label {
  font-size: 14px;
  font-weight: 500;
  color: #555;
  margin-right: 8px;
}
.main-filter-btn {
  padding: 6px 12px;
  border-radius: 20px;
  border: 1px solid #ccc;
  background-color: white;
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}
.main-filter-btn:hover {
  border-color: #007bff;
}
.filter-dropdown-wrapper {
  position: relative;
  display: inline-block;
}
.filter-dropdown-wrapper .filter-select {
  padding: 6px 12px;
  border-radius: 20px;
  border: 1px solid #ccc;
  background-color: white;
  cursor: pointer;
  font-size: 14px;
  color: #333;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 0.5rem;
  white-space: nowrap;
}
.filter-select:hover {
  border-color: #007bff;
}
.filter-select:invalid {
  color: #333;
}
.filter-dropdown-wrapper .dropdown-arrow-static {
  color: #888;
}
.filter-dropdown-wrapper select.filter-select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
.filter-dropdown-wrapper .dropdown-arrow {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #888;
  pointer-events: none;
}
.filter-btn, .clear-btn {
  padding: 6px 12px;
  border-radius: 20px;
  border: 1px solid #ccc;
  background-color: white;
  cursor: pointer;
  font-size: 14px;
  display: flex; 
  align-items: center;
  gap: 0.4rem;
}
.filter-btn.active {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}
.clear-btn {
  color: #007bff;
  border-color: #007bff;
  margin-left: auto;
}

/* --- SỬA ĐỔI CSS TẠI ĐÂY --- */

/* 1. Sửa .category-bar (Thêm cuộn ngang) */
.category-bar {
  border: none;
  overflow-x: auto; /* Bật lại cuộn ngang */
  white-space: nowrap; /* Ngăn các item xuống dòng */
  padding-top: 5px; 
  padding-bottom: 15px;
  justify-content: flex-start; /* Căn trái */
  align-items: flex-start;
}

/* 2. Sửa .category-item (Dùng min-width) */
.category-item {
  display: inline-flex; /* SỬA: Dùng inline-flex */
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 5px 10px; /* Giảm padding 2 bên một chút */
  cursor: pointer;
  font-size: 13px;
  width: 90px; /* SỬA: Giữ chiều rộng cố định để chữ xuống dòng */
  vertical-align: top; /* BỔ SUNG: Căn các item thẳng hàng */
  min-height: 75px; /* BỔ SUNG: Đảm bảo các item có chiều cao tối thiểu bằng nhau */
}

/* 3. BỔ SUNG: CSS cho <span> bên trong .category-item (để cắt chữ) */
.category-item span {
  display: block;
  width: 100%;
  white-space: normal; /* CHO PHÉP chữ xuống dòng */
  line-height: 1.3;
  
  /* Kỹ thuật cắt chữ 2 dòng (multi-line ellipsis) */
  max-height: 2.6em; /* 1.3em (line-height) * 2 (số dòng) */
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2; /* Giới hạn 2 dòng */
  line-clamp: 2;
  -webkit-box-orient: vertical;
}

/* --- KẾT THÚC SỬA ĐỔI --- */

.category-item:hover, .category-item.active {
  color: #007bff;
}
.icon-wrapper {
  width: 40px;
  height: 40px;
  background-color: #e9e9e9;
  border-radius: 50%;
  margin-bottom: 5px;
  display: flex; 
  align-items: center;
  justify-content: center;
  color: #555;
}
.category-item.active .icon-wrapper {
  background-color: #007bff;
  color: white;
}
.product-content {
  background-color: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
.sort-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 15px;
  border-bottom: 1px solid #eee;
  margin-bottom: 15px;
}
.tabs .tab-btn {
  padding: 8px 15px;
  border: none;
  background: none;
  cursor: pointer;
  font-weight: 500;
  color: #666;
  border-radius: 4px;
}
.tabs .tab-btn.active {
  color: #007bff;
  border-bottom: 2px solid #007bff;
}
.sort-controls {
  display: flex;
  gap: 10px;
}
.sort-select {
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.view-toggle {
  padding: 8px;
  border: 1px solid #ccc;
  background-color: white;
  border-radius: 4px;
  cursor: pointer;
  color: #555;
  font-size: 1.1rem; 
}
.view-toggle:hover {
  color: #007bff;
  border-color: #007bff;
}
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
}
.product-grid.list-view {
  grid-template-columns: 1fr;
}
.icon-wrapper svg,
.icon-wrapper .material-symbols-outlined {
    font-size: 1.5rem !important;
    line-height: 1;
}
.icon-wrapper .material-symbols-outlined {
  font-family: 'Material Symbols Outlined', sans-serif !important; 
  font-variation-settings: 'wght' 600; 
}

/* CSS cho trạng thái Loading và Empty */
.state-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #666;
  text-align: center;
  width: 100%;
  min-height: 300px; /* Đảm bảo chiều cao để không bị giật layout */
}

/* Hiệu ứng xoay tròn (Spinner) */
.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #007bff; /* Màu xanh chủ đạo */
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 15px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Empty State Styles */
.empty-icon {
  font-size: 3rem;
  color: #ccc;
  margin-bottom: 15px;
}

.btn-reset {
  margin-top: 15px;
  padding: 8px 20px;
  background-color: #fff;
  border: 1px solid #007bff;
  color: #007bff;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-reset:hover {
  background-color: #007bff;
  color: white;
}
</style>