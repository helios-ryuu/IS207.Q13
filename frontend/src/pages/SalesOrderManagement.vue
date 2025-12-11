<template>
  <HeaderOther />

  <div class="sales-order-page">
    <div class="container">
      
      <div class="breadcrumb">
        <span class="link" @click="goToHome">VietMarket</span> 
        <span class="divider">></span> 
        <span class="current">Đơn bán</span>
      </div>

      <div class="filter-card">
        
        <div class="filter-row top-row">
          <span class="label">Lọc theo:</span>
          
          <div class="dropdown-wrapper">
            <button class="btn-dropdown" @click="isDropdownOpen = !isDropdownOpen">
              <font-awesome-icon icon="filter" /> Danh mục
              <font-awesome-icon icon="chevron-down" />
            </button>

            <div class="dropdown-menu" v-if="isDropdownOpen">
              <div 
                v-for="cat in categories" 
                :key="cat" 
                class="dropdown-item"
                :class="{ 'active-item': selectedCategories.includes(cat) }"
                @click="toggleCategory(cat)"
              >
                <span>{{ cat }}</span>
                <font-awesome-icon v-if="selectedCategories.includes(cat)" icon="check" class="check-icon" />
              </div>
            </div>
          </div>

          <div class="tags-container">
            <div v-for="cat in selectedCategories" :key="cat" class="selected-tag">
              {{ cat }}
              <span class="remove-tag" @click="removeCategory(cat)">
                <font-awesome-icon icon="times" />
              </span>
            </div>
            <button 
              v-if="selectedCategories.length > 0" 
              class="clear-all-btn" 
              @click="clearAllCategories"
            >
              Xóa hết
            </button>
          </div>
        </div>

        <div class="filter-row date-row-container">
          <span class="label">Ngày đặt hàng:</span>
          
          <div class="date-controls-group">
            <div class="input-with-icon date-wrapper">
              <font-awesome-icon 
                icon="calendar-alt" 
                class="input-icon left-icon clickable-icon" 
                @click="openStartDate" 
              />
              <input 
                type="text" 
                v-model="displayStartDate" 
                @input="(e) => handleDateInput(e, 'start')"
                @keyup.enter="applyDateFilter"
                placeholder="dd/mm/yyyy" 
                class="form-input date-input-text" 
                maxlength="10"
              />
              <input type="date" ref="startDateRef" class="hidden-date-picker" :max="maxDateStr" @change="handleStartDateSelected" />
            </div>

            <span class="separator">-</span>

            <div class="input-with-icon date-wrapper">
              <font-awesome-icon 
                icon="calendar-alt" 
                class="input-icon left-icon clickable-icon" 
                @click="openEndDate" 
              />
              <input 
                type="text" 
                v-model="displayEndDate" 
                @input="(e) => handleDateInput(e, 'end')"
                @keyup.enter="applyDateFilter"
                placeholder="dd/mm/yyyy" 
                class="form-input date-input-text" 
                maxlength="10"
              />
              <input type="date" ref="endDateRef" class="hidden-date-picker" :max="maxDateStr" @change="handleEndDateSelected" />
            </div>

            <button class="btn-small-filter primary" @click="applyDateFilter">Lọc</button>
            <button class="btn-small-filter outline" @click="resetDateFilter">Đặt lại</button>
            
            <div class="sort-wrapper">
              <button class="btn-small-filter outline sort-btn" @click="isSortDropdownOpen = !isSortDropdownOpen">
                Sắp xếp theo <font-awesome-icon icon="sort" />
              </button>
              <div class="sort-dropdown" v-if="isSortDropdownOpen">
                <div class="sort-item" :class="{ active: sortDirection === 'asc' }" @click="selectSort('asc')">
                  <span>Tăng dần</span>
                  <font-awesome-icon v-if="sortDirection === 'asc'" icon="check" class="check-icon" />
                </div>
                <div class="sort-item" :class="{ active: sortDirection === 'desc' }" @click="selectSort('desc')">
                  <span>Giảm dần</span>
                  <font-awesome-icon v-if="sortDirection === 'desc'" icon="check" class="check-icon" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="filter-row search-row">
          <span class="label">Tìm kiếm:</span>
          <div class="search-wrapper-full">
            <div class="input-with-icon search-box bordered-input">
              <input type="text" placeholder="Nhập mã vận đơn cần tìm..." v-model="searchQuery" class="form-input" />
              <button class="icon-btn right-icon"><font-awesome-icon icon="search" /></button>
            </div>
            <button class="btn-primary search-btn">Tìm kiếm</button>
          </div>
        </div>

      </div>

      <div class="status-tabs-container">
        <div class="status-tabs">
          <button 
            v-for="tab in tabs" 
            :key="tab.id" 
            class="tab-item" 
            :class="{ active: activeTab === tab.id }"
            @click="changeTab(tab.id)"
          >
            {{ tab.name }}
          </button>
        </div>
      </div>

      <div class="order-list">
        
        <div v-for="order in visibleOrders" :key="order.id" class="order-card">
          <div class="card-header">
            <div class="buyer-info">
              <img :src="order.buyerAvatar" class="buyer-avatar" />
              <span class="buyer-name">{{ order.buyerName }}</span>
              <button class="btn-message"><font-awesome-icon icon="comments" /> Nhắn tin</button>
            </div>
            
            <div class="order-meta-group">
              <div class="meta-row">
                <span class="meta-label">Ngày đặt:</span>
                <span class="meta-value date-highlight">{{ formatDate(order.orderDate) }}</span>
              </div>
              <div class="meta-row">
                <span class="meta-label">Mã vận đơn:</span>
                <span class="meta-value tracking-code">{{ order.trackingCode }}</span>
              </div>
            </div>
          </div>

          <div class="product-row">
            <img :src="order.image" alt="Sản phẩm" class="product-img">
            <div class="col-info">
              <h3 class="product-name">{{ order.productName }}</h3>
              <p class="product-detail">Phân loại: {{ order.variant }}</p>
              <p class="product-detail">Số lượng: x{{ order.quantity }}</p>
            </div>
            <div class="col-price">
              <span class="price">{{ order.totalPrice }}</span>
              <span class="sub-text">Thanh toán khi nhận</span>
            </div>
            <div class="col-status">
              <span class="status-text">{{ order.statusText }}</span>
              <p class="deadline-text" v-if="order.deadline !== '-'">Giao trước: {{ order.deadline }}</p>
            </div>
            <div class="col-shipping">
              <span class="ship-method">Giao nhanh</span>
              <span class="ship-provider">{{ order.shippingProvider }}</span>
            </div>
            <div class="col-action">
              <button 
                class="btn-action" 
                :class="order.statusId === 'completed' ? 'btn-disabled' : 'btn-yellow'"
              >
                {{ order.statusId === 'completed' ? 'Đã giao' : 'Chuẩn bị hàng' }}
              </button>
            </div>
          </div>
        </div>

        <div v-if="hasMoreOrders" class="load-more-container">
          <button class="btn-load-more" @click="loadMore">
            Xem thêm đơn hàng <font-awesome-icon icon="chevron-down" />
          </button>
        </div>

        <div v-if="visibleOrders.length === 0" class="empty-result">
          <p>Không tìm thấy đơn hàng nào phù hợp.</p>
        </div>

      </div>
    </div>
  </div>

  <Footer />
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import HeaderOther from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';

const router = useRouter();
const goToHome = () => router.push('/');

// --- DATA ---
const activeTab = ref('all');
const isDropdownOpen = ref(false);
const selectedCategories = ref([]); 
const visibleCount = ref(5);
const searchQuery = ref('');

// Date & Sort Data
const displayStartDate = ref('');
const displayEndDate = ref('');
const appliedStartDate = ref('');
const appliedEndDate = ref('');
const startDateRef = ref(null);
const endDateRef = ref(null);
const maxDateStr = new Date().toISOString().split('T')[0];

const isSortDropdownOpen = ref(false);
const sortDirection = ref('desc');

const tabs = [
  { id: 'all', name: 'Tất cả' },
  { id: 'pending', name: 'Chờ xác nhận' },
  { id: 'pickup', name: 'Chờ lấy hàng' },
  { id: 'shipping', name: 'Đang giao hàng' },
  { id: 'completed', name: 'Đã giao hàng' },
  { id: 'cancelled', name: 'Đơn hủy' },
  { id: 'return', name: 'Trả hàng / Hoàn tiền' },
  { id: 'failed', name: 'Giao không thành công' }
];

const categories = [
  'Xe cộ',
  'Đồ điện tử',
  'Thú cưng',
  'Đồ ăn, Thực phẩm và các loại khác',
  'Tủ lạnh, Máy lạnh, Máy giặt',
  'Đồ gia dụng, Nội thất, Cây cảnh',
  'Thời trang, Đồ dùng cá nhân',
  'Giải trí, Thể thao, Sở thích',
  'Đồ dùng văn phòng, Công nông nghiệp'
];

const orders = ref([
  { id: 1, trackingCode: '2502239BEE14PX', buyerName: 'Con Bò', buyerAvatar: '/avatar.jpg', productName: 'Máy quạt mini giá tốt', variant: 'Đã sử dụng', quantity: 1, category: 'Đồ điện tử', totalPrice: '400.000 đ', statusId: 'pickup', statusText: 'Chờ lấy hàng', deadline: '10/10/2025', shippingProvider: 'SPX Express', image: '/avatar.jpg', orderDate: '2025-10-08' },
  { id: 2, trackingCode: 'SPEVN12345678', buyerName: 'Nguyễn Văn A', buyerAvatar: '/avatar.jpg', productName: 'Áo thun nam', variant: 'Size L', quantity: 2, category: 'Thời trang, Đồ dùng cá nhân', totalPrice: '300.000 đ', statusId: 'pickup', statusText: 'Chờ lấy hàng', deadline: '12/10/2025', shippingProvider: 'GHTK', image: '/avatar.jpg', orderDate: '2025-10-09' },
  { id: 3, trackingCode: 'GHN888999', buyerName: 'Le Thi C', buyerAvatar: '/avatar.jpg', productName: 'Thức ăn mèo', variant: 'Gói 1kg', quantity: 5, category: 'Thú cưng', totalPrice: '250.000 đ', statusId: 'shipping', statusText: 'Đang giao hàng', deadline: '-', shippingProvider: 'J&T Express', image: '/avatar.jpg', orderDate: '2025-10-05' },
  { id: 4, trackingCode: 'VNPOST555', buyerName: 'Tran Van D', buyerAvatar: '/avatar.jpg', productName: 'Tai nghe Bluetooth', variant: 'Trắng', quantity: 1, category: 'Đồ điện tử', totalPrice: '1.200.000 đ', statusId: 'completed', statusText: 'Giao thành công', deadline: '-', shippingProvider: 'Viettel Post', image: '/avatar.jpg', orderDate: '2025-09-30' },
  { id: 5, trackingCode: 'JNT112233', buyerName: 'Pham Van E', buyerAvatar: '/avatar.jpg', productName: 'Bàn phím cơ', variant: 'Red Switch', quantity: 1, category: 'Đồ điện tử', totalPrice: '850.000 đ', statusId: 'pending', statusText: 'Chờ xác nhận', deadline: '15/10/2025', shippingProvider: 'J&T Express', image: '/avatar.jpg', orderDate: '2025-10-12' },
  { id: 6, trackingCode: 'GRAB998877', buyerName: 'Hoang Thi F', buyerAvatar: '/avatar.jpg', productName: 'Nồi cơm điện', variant: '1.8L', quantity: 1, category: 'Đồ gia dụng, Nội thất, Cây cảnh', totalPrice: '1.500.000 đ', statusId: 'cancelled', statusText: 'Đã hủy', deadline: '-', shippingProvider: 'GrabExpress', image: '/avatar.jpg', orderDate: '2025-09-25' },
  { id: 7, trackingCode: 'BEST665544', buyerName: 'Vo Van G', buyerAvatar: '/avatar.jpg', productName: 'Giày Sneaker', variant: 'Size 42', quantity: 1, category: 'Thời trang, Đồ dùng cá nhân', totalPrice: '600.000 đ', statusId: 'return', statusText: 'Trả hàng/Hoàn tiền', deadline: '-', shippingProvider: 'Best Express', image: '/avatar.jpg', orderDate: '2025-10-01' },
  { id: 8, trackingCode: 'AHAMOVE333', buyerName: 'Dang Thi H', buyerAvatar: '/avatar.jpg', productName: 'Chậu cây cảnh', variant: 'Sứ trắng', quantity: 3, category: 'Đồ gia dụng, Nội thất, Cây cảnh', totalPrice: '150.000 đ', statusId: 'shipping', statusText: 'Đang giao hàng', deadline: '-', shippingProvider: 'Ahamove', image: '/avatar.jpg', orderDate: '2025-10-11' },
]);

// --- HELPER & LOGIC ---
const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const [year, month, day] = dateStr.split('-');
  return `${day}/${month}/${year}`;
};

const convertToISO = (dateStr) => {
  if (!dateStr || dateStr.length !== 10) return null;
  const parts = dateStr.split('/');
  if (parts.length === 3) return `${parts[2]}-${parts[1]}-${parts[0]}`;
  return null;
};

const isLeapYear = (year) => (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
const getMaxDays = (month, year) => {
  if ([1, 3, 5, 7, 8, 10, 12].includes(month)) return 31;
  if ([4, 6, 9, 11].includes(month)) return 30;
  if (month === 2) return year ? (isLeapYear(year) ? 29 : 28) : 29;
  return 31;
};

const allFilteredOrders = computed(() => {
  let result = orders.value.filter(order => {
    const matchTab = activeTab.value === 'all' || order.statusId === activeTab.value;
    const matchCategory = selectedCategories.value.length === 0 || selectedCategories.value.includes(order.category);
    const matchSearch = !searchQuery.value || order.trackingCode.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    let matchDate = true;
    if (appliedStartDate.value && appliedEndDate.value) {
      matchDate = order.orderDate >= appliedStartDate.value && order.orderDate <= appliedEndDate.value;
    }
    return matchTab && matchCategory && matchSearch && matchDate;
  });

  return result.sort((a, b) => {
    const dateA = new Date(a.orderDate);
    const dateB = new Date(b.orderDate);
    return sortDirection.value === 'asc' ? dateA - dateB : dateB - dateA;
  });
});

const visibleOrders = computed(() => allFilteredOrders.value.slice(0, visibleCount.value));
const hasMoreOrders = computed(() => visibleCount.value < allFilteredOrders.value.length);

// --- METHODS ---
const openStartDate = () => startDateRef.value?.showPicker();
const handleStartDateSelected = (e) => {
  const val = e.target.value;
  if (val) {
    const [y, m, d] = val.split('-');
    displayStartDate.value = `${d}/${m}/${y}`;
    applyDateFilter();
  }
};

const openEndDate = () => endDateRef.value?.showPicker();
const handleEndDateSelected = (e) => {
  const val = e.target.value;
  if (val) {
    const [y, m, d] = val.split('-');
    displayEndDate.value = `${d}/${m}/${y}`;
    applyDateFilter();
  }
};

const handleDateInput = (event, type) => {
  let v = event.target.value.replace(/\D/g, ''); 
  if (v.length >= 2) v = v.slice(0, 2) + '/' + v.slice(2);
  if (v.length >= 5) v = v.slice(0, 5) + '/' + v.slice(5);
  v = v.slice(0, 10);

  if (v.length >= 5) {
    let monthStr = v.slice(3, 5);
    let month = parseInt(monthStr);
    if (month > 12) { month = 12; v = v.slice(0, 3) + '12' + v.slice(5); }
    if (month === 0) { month = 1; v = v.slice(0, 3) + '01' + v.slice(5); }
  }
  if (v.length === 10) {
    const day = parseInt(v.slice(0, 2));
    const month = parseInt(v.slice(3, 5));
    const year = parseInt(v.slice(6, 10));
    const maxDay = getMaxDays(month, year);
    if (day > maxDay) v = maxDay.toString().padStart(2, '0') + v.slice(2);
    if (day === 0) v = '01' + v.slice(2);
  } else if (v.length >= 5) {
    const day = parseInt(v.slice(0, 2));
    const month = parseInt(v.slice(3, 5));
    const tempMax = (month === 2) ? 29 : getMaxDays(month, null);
    if (day > tempMax) v = tempMax.toString().padStart(2, '0') + v.slice(2);
  }

  if (type === 'start') displayStartDate.value = v; else displayEndDate.value = v;
};

const applyDateFilter = () => {
  let startISO = convertToISO(displayStartDate.value);
  let endISO = convertToISO(displayEndDate.value);
  const todayISO = new Date().toISOString().split('T')[0];

  if (startISO && startISO > todayISO) {
    alert("Ngày bắt đầu không được lớn hơn ngày hiện tại!");
    const [y, m, d] = todayISO.split('-');
    displayStartDate.value = `${d}/${m}/${y}`;
    startISO = todayISO;
  }
  if (endISO && endISO > todayISO) {
    alert("Ngày kết thúc không được lớn hơn ngày hiện tại!");
    const [y, m, d] = todayISO.split('-');
    displayEndDate.value = `${d}/${m}/${y}`;
    endISO = todayISO;
  }

  if (startISO && endISO && startISO > endISO) {
    const tempDisplay = displayStartDate.value;
    displayStartDate.value = displayEndDate.value;
    displayEndDate.value = tempDisplay;
    const tempISO = startISO;
    startISO = endISO;
    endISO = tempISO;
  }

  appliedStartDate.value = startISO || '';
  appliedEndDate.value = endISO || '';
  resetPagination();
};

const resetDateFilter = () => {
  displayStartDate.value = ''; displayEndDate.value = '';
  appliedStartDate.value = ''; appliedEndDate.value = '';
  if (startDateRef.value) startDateRef.value.value = '';
  if (endDateRef.value) endDateRef.value.value = '';
  resetPagination();
};

const selectSort = (dir) => { sortDirection.value = dir; isSortDropdownOpen.value = false; };
const loadMore = () => visibleCount.value += 5;
const resetPagination = () => visibleCount.value = 5;
const changeTab = (id) => { activeTab.value = id; resetPagination(); };
const toggleCategory = (cat) => {
  const index = selectedCategories.value.indexOf(cat);
  if (index === -1) selectedCategories.value.push(cat); else selectedCategories.value.splice(index, 1);
  resetPagination();
};
const removeCategory = (cat) => toggleCategory(cat);
const clearAllCategories = () => { selectedCategories.value = []; resetPagination(); };
</script>

<style scoped>
:root {
  --primary-blue: #0055aa;
  --primary-yellow: #ffc107;
  --text-dark: #333;
  --bg-light: #f5f5f5;
  --border-color: #d9d9d9;
}

.sales-order-page { background-color: #f5f5f5; min-height: 100vh; padding-bottom: 3rem; font-family: Arial, sans-serif; }
.container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }

/* Breadcrumb */
.breadcrumb { padding: 1.2rem 0; font-size: 0.95rem; color: #555; }
.breadcrumb .link { color: #0055aa; cursor: pointer; font-weight: 600; }
.breadcrumb .divider { margin: 0 0.5rem; color: #999; }

/* Filter Card */
.filter-card {
  background: #fff; border-radius: 12px 12px 0 0; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  padding: 1.5rem; margin-bottom: 2px;
}

.filter-row { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.2rem; }
.filter-row:last-child { margin-bottom: 0; }
.label { font-weight: 600; color: #444; font-size: 0.95rem; min-width: 90px; white-space: nowrap; }

/* Date Row */
.date-row-container { display: flex; align-items: center; gap: 1rem; flex-wrap: nowrap; margin-bottom: 1.2rem; }
.date-controls-group { display: flex; align-items: center; gap: 0.5rem; flex-wrap: nowrap; }
.separator { color: #888; font-weight: 500; }

.date-wrapper { position: relative; display: flex; align-items: center; }
.date-input-text { 
  padding: 0.6rem 0.8rem 0.6rem 2.5rem; border: 1px solid #bbb; border-radius: 6px; 
  font-size: 0.9rem; width: 130px; cursor: text; background: #fff;
}
.date-input-text:focus { border-color: #0055aa; }
.hidden-date-picker { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; pointer-events: none; }
.left-icon { position: absolute; left: 0.8rem; color: #888; z-index: 10; }
.clickable-icon { cursor: pointer; pointer-events: auto; }

/* Buttons */
.btn-small-filter {
  padding: 0.6rem 1rem; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.9rem; white-space: nowrap;
}
.btn-small-filter.primary { background-color: #0055aa; border: 1px solid #0055aa; color: #fff; }
.btn-small-filter.primary:hover { background-color: #004499; }
.btn-small-filter.outline { background-color: #fff; border: 1px solid #ddd; color: #666; }
.btn-small-filter.outline:hover { border-color: #999; color: #333; }

/* Sort */
.sort-wrapper { position: relative; }
.sort-btn { display: flex; align-items: center; gap: 0.5rem; min-width: 130px; justify-content: center; }
.sort-dropdown {
  position: absolute; top: 110%; right: 0; background: #fff; border: 1px solid #eee;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; min-width: 200px; z-index: 100; padding: 0.5rem 0;
}
.sort-item {
  padding: 0.7rem 1rem; cursor: pointer; font-size: 0.9rem; color: #333; display: flex; justify-content: space-between; align-items: center;
}
.sort-item:hover { background-color: #f5f5f5; color: #0055aa; }
.sort-item.active { color: #0055aa; font-weight: 600; background-color: #e6f0fa; }

/* Dropdown */
.dropdown-wrapper { position: relative; }
.btn-dropdown {
  background: #fff; border: 1px solid #bbb; padding: 0.6rem 1rem; border-radius: 6px;
  cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; color: #333; min-width: 140px;
}
.dropdown-menu {
  position: absolute; top: 110%; left: 0; background: #fff; border: 1px solid #eee;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; min-width: 250px; z-index: 100;
  padding: 0.5rem 0; max-height: 300px; overflow-y: auto;
}
.dropdown-item { padding: 0.7rem 1rem; cursor: pointer; display: flex; justify-content: space-between; }
.dropdown-item.active-item { color: #0055aa; font-weight: 600; background: #e6f0fa; }

.tags-container { display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center; }
.selected-tag {
  background-color: #fff; border: 1px solid #0055aa; color: #0055aa;
  padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500;
  display: flex; align-items: center; gap: 0.4rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.remove-tag:hover { cursor: pointer; color: #d0011b; }
.clear-all-btn { background: none; border: none; color: #555; text-decoration: underline; cursor: pointer; font-size: 0.85rem; margin-left: 0.5rem; }

.search-wrapper-full { display: flex; align-items: center; gap: 0.8rem; flex-grow: 1; }
.search-box { flex-grow: 1; max-width: 600px; position: relative; }
.search-box input { 
  padding: 0.6rem 0.8rem 0.6rem 0.8rem; padding-right: 2.5rem; 
  width: 100%; border: 1px solid #bbb; border-radius: 6px; font-size: 0.9rem; outline: none;
}
.search-box input:focus { border-color: #0055aa; }
.right-icon { position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #888; cursor: pointer; }
.btn-primary.search-btn {
  background: #ffc107; color: #222; border: none; padding: 0.6rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;
}
.btn-primary.search-btn:hover { background: #ffb300; }

.status-tabs-container { background: #fff; border-radius: 0 0 12px 12px; margin-bottom: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden; }
.status-tabs { display: flex; overflow-x: auto; border-top: 1px solid #eee; }
.tab-item { flex: 1; min-width: max-content; padding: 1rem 1.5rem; text-align: center; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-weight: 500; color: #666; font-size: 0.95rem; }
.tab-item.active { color: #0055aa; border-bottom-color: #0055aa; font-weight: 700; }

.order-card { background: #fff; margin-bottom: 1rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #f0f0f0; }
.card-header { padding: 1rem 1.5rem; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between; align-items: center; background: #fff; }
.buyer-info { display: flex; align-items: center; gap: 0.8rem; }
.buyer-avatar { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #eee; }
.buyer-name { font-weight: 700; color: #333; }

.btn-message {
  display: flex; align-items: center; gap: 0.4rem; background-color: #fff;
  border: 1px solid #0055aa; color: #0055aa; padding: 0.3rem 0.8rem;
  border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
  transition: all 0.2s; margin-left: 0.5rem; 
}
.btn-message:hover { background-color: #f0f7ff; }
.btn-chat { display: none; } 

.order-meta-group { display: flex; flex-direction: column; align-items: flex-end; gap: 0.2rem; }
.meta-row { font-size: 0.9rem; color: #666; display: flex; align-items: center; gap: 0.4rem; }
.meta-label { font-weight: 400; }
.meta-value { font-weight: 600; color: #333; }
.date-highlight { color: #555; }
.tracking-code { color: #0055aa; }

.product-row { display: grid; grid-template-columns: 80px 2fr 1fr 1.5fr 1fr 1.2fr; gap: 1.5rem; padding: 1.5rem; align-items: start; background: #fff; }
.product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
.product-name { font-size: 1rem; font-weight: 600; margin-bottom: 0.3rem; color: #333; }
.product-detail { font-size: 0.85rem; color: #777; margin-bottom: 0.2rem; }
.price { font-weight: 700; color: #0055aa; display: block; margin-bottom: 0.3rem; font-size: 1rem; }
.sub-text { font-size: 0.8rem; color: #999; }
.status-text { color: #26aa99; font-weight: 600; display: block; margin-bottom: 0.3rem; font-size: 0.95rem; }
.deadline-text { font-size: 0.8rem; color: #666; line-height: 1.4; }
.ship-method { font-weight: 600; color: #333; display: block; font-size: 0.95rem; }
.ship-provider { font-size: 0.85rem; color: #666; }

.btn-action { width: 100%; padding: 0.6rem 0; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.9rem; border: none; }
.btn-yellow { 
  background-color: #ffc107; 
  color: #222; 
  box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
}
.btn-yellow:hover { 
  background-color: #f57c00; 
}
.btn-disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; }

.load-more-container { text-align: center; margin-top: 1.5rem; }
.btn-load-more { background: #fff; border: 1px solid #ddd; padding: 0.8rem 2.5rem; border-radius: 25px; cursor: pointer; font-weight: 600; color: #555; transition: all 0.2s; }
.btn-load-more:hover { color: #0055aa; border-color: #0055aa; }
.empty-result { padding: 4rem; text-align: center; color: #777; background: #fff; border-radius: 12px; }
</style>