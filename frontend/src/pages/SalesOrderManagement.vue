<template>
  <HeaderOther />

  <div class="order-management-page">
    <div class="container">

      <div class="breadcrumb">
        <span class="link" @click="goToHome">Trang chủ</span>
        <span class="divider">></span>
        <span class="current">Đơn bán</span>
      </div>

      <div class="filter-section">
        <div class="filter-row">
          <div class="search-box">
            <font-awesome-icon icon="search" class="search-icon" />
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Tìm theo mã đơn hàng..." 
              class="search-input"
            />
          </div>
          <span class="label">Lọc theo:</span>
          <div class="dropdown-wrapper">
            <button class="btn-dropdown" @click="isDropdownOpen = !isDropdownOpen">
              <font-awesome-icon icon="filter" /> Danh mục
              <font-awesome-icon icon="chevron-down" />
            </button>
            <div class="dropdown-menu" v-if="isDropdownOpen">
              <div v-for="cat in categories" :key="cat" class="dropdown-item" :class="{ 'active-item': selectedCategories.includes(cat) }" @click="toggleCategory(cat)">
                <span>{{ cat }}</span>
                <font-awesome-icon v-if="selectedCategories.includes(cat)" icon="check" class="check-icon" />
              </div>
            </div>
          </div>
          <div class="tags-container">
            <div v-for="cat in selectedCategories" :key="cat" class="selected-tag">
              {{ cat }} <span class="remove-tag" @click="removeCategory(cat)"><font-awesome-icon icon="times" /></span>
            </div>
            <button v-if="selectedCategories.length > 0" class="clear-all-btn" @click="clearAllCategories">Xóa hết</button>
          </div>
        </div>
      </div>

      <div class="status-tabs">
        <button v-for="tab in tabs" :key="tab.id" class="tab-item" :class="{ active: activeTab === tab.id }" @click="changeTab(tab.id)">
          {{ tab.name }}
        </button>
      </div>

      <div v-if="isLoading" class="loading-state">
        <p>Đang tải dữ liệu...</p>
      </div>

      <div v-else class="order-list">
        <div v-for="order in visibleOrders" :key="order.id" class="order-card">
          <div class="card-header">
            <div class="shop-info">
              <span class="shop-name">Khách hàng: {{ order.customerName }}</span>
            </div>
            <div class="order-status">
              <span class="delivery-status">Cập nhật lúc: <strong>{{ order.lastUpdateDisplay }}</strong></span>
              <span class="status-label">{{ order.statusLabel }}</span>
            </div>
          </div>

          <div class="product-row">
            <img :src="order.image || FALLBACK_IMAGE" alt="Sản phẩm" class="product-img" @error="handleImageError">
            <div class="product-info">
              <h3 class="product-name">{{ order.productName }}</h3>
              <p class="product-variant">Phân loại: {{ order.variant }}</p>
              <p class="product-category-label">Danh mục: {{ order.category || 'Khác' }}</p>
              <p class="product-address">Địa chỉ: {{ order.address }}</p>
              <div class="order-meta">
                <span class="meta-item">Mã Đơn hàng: <strong>{{ order.trackingCode }}</strong></span>
                <span class="meta-item">Ngày Đặt: <strong>{{ order.orderDateDisplay }}</strong></span>
              </div>
            </div>
            <div class="product-price">
              <span class="current-price">{{ order.price }}</span>
            </div>
          </div>

          <div class="card-footer">
            <div class="total-section">
              Tổng tiền: <span class="total-price">{{ order.totalPrice }}</span>
            </div>
            <div class="action-buttons">

              <template v-if="order.statusId === 'pending'">
                <button class="btn btn-outline-danger" @click="cancelOrder(order)">Hủy Đơn</button>
                <button class="btn btn-primary" @click="acceptOrder(order)">Chấp nhận Đơn</button>
              </template>

              <template v-else-if="order.statusId === 'processing'">
                <button class="btn btn-primary" @click="shipOrder(order)">Đóng gói xong, giao vận</button>
              </template>

              <template v-else-if="order.statusId === 'shipping'">
                <button class="btn btn-primary" @click="completeOrder(order)">Hoàn tất vận chuyển</button>
                <button class="btn btn-default" @click="viewOrderDetails(order)">Xem chi tiết</button>
              </template>

              <template v-else-if="order.statusId === 'completed'">
                <button class="btn btn-default" @click="viewOrderDetails(order)">Xem chi tiết</button>
              </template>

              <template v-else-if="order.statusId === 'cancelled'">
                <button class="btn btn-default" @click="viewOrderDetails(order)">Xem chi tiết</button>
              </template>

              <template v-else-if="order.statusId === 'return'">
                <button class="btn btn-primary" @click="refundOrder(order)">Xác nhận nhận hàng hoàn</button>
              </template>

              <template v-else-if="order.statusId === 'refunded'">
                <button class="btn btn-disabled" disabled>Đã Hoàn Tiền</button>
              </template>

              <template v-else>
                <button class="btn btn-default" @click="viewOrderDetails(order)">Xem chi tiết</button>
              </template>

            </div>
          </div>
        </div>

        <div v-if="hasMoreOrders" class="load-more-container">
          <button class="btn-load-more" @click="loadMore">Xem thêm đơn hàng <font-awesome-icon icon="chevron-down" /></button>
        </div>

        <div v-if="visibleOrders.length === 0" class="empty-result">
          <p>Không tìm thấy đơn hàng nào phù hợp.</p>
        </div>
      </div>
    </div>
  </div>

  <div v-if="showReturnDetailsModal" class="modal-overlay" @click.self="closeReturnDetailsModal">
    <div class="modal-content fade-in">
      <div class="modal-header"><h3>Chi tiết Yêu cầu Trả hàng</h3><button class="close-btn" @click="closeReturnDetailsModal">✕</button></div>
      <div class="modal-body info-view">
        <p><strong>Đơn hàng:</strong> {{ currentReturnDetails.trackingCode }}</p>
        <p><strong>Khách hàng:</strong> {{ currentReturnDetails.customerName }}</p>
        <p><strong>Lý do:</strong> {{ currentReturnDetails.reason }}</p>
        <p><strong>Ngày hẹn lấy hàng:</strong> {{ currentReturnDetails.pickupDate }}</p>
        <div class="alert-box">
          Vui lòng kiểm tra chất lượng hàng hóa khi Shipper mang về và tiến hành hoàn tiền nếu đủ điều kiện.
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary" @click="closeReturnDetailsModal">Đóng</button></div>
    </div>
  </div>

  <Footer />
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import HeaderOther from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';
import * as sellerOrderService from '../services/sellerOrderService';
import { useToast } from '../utils/useToast';

const router = useRouter();
const goToHome = () => router.push('/');
const { showSuccess, showError } = useToast();

// --- DATA STATE ---
const activeTab = ref('all');
const isDropdownOpen = ref(false);
const selectedCategories = ref([]);
const visibleCount = ref(10);
const isLoading = ref(false);
const orders = ref([]);
const searchQuery = ref(''); // Tìm kiếm theo mã đơn

// --- MODAL STATE ---
const showReturnDetailsModal = ref(false);
const currentReturnDetails = reactive({ trackingCode: '', customerName: '', reason: '', pickupDate: '' });

// Các tab trạng thái (ĐƠN BÁN)
const tabs = [
  { id: 'all', name: 'Tất cả' },
  { id: 'pending', name: 'Chờ xác nhận' },
  { id: 'processing', name: 'Đang xử lý' },
  { id: 'shipping', name: 'Đang vận chuyển' },
  { id: 'completed', name: 'Hoàn thành' },
  { id: 'cancelled', name: 'Đã hủy' },
  { id: 'return', name: 'Yêu cầu trả hàng' },
  { id: 'refunded', name: 'Đã hoàn tiền' }
];

// Danh mục - khớp với backend CategorySeeder
const categories = [
  'Đồ điện tử',
  'Xe cộ',
  'Đồ gia dụng, Nội thất, Cây cảnh',
  'Tủ lạnh, Máy lạnh, Máy giặt',
  'Thời trang, Đồ dùng cá nhân',
  'Giải trí, Thể thao, Sở thích',
  'Thú cưng',
  'Đồ dùng văn phòng, Công nông nghiệp',
  'Đồ ăn, Thực phẩm và các loại khác',
  'Khác'
];

// --- TRANSFORM API DATA ---
const transformOrder = (apiOrder) => {
  const firstItem = apiOrder.items?.[0] || {};
  return {
    id: apiOrder.id,
    statusId: apiOrder.status,
    statusLabel: apiOrder.status_label,
    trackingCode: apiOrder.tracking_code,
    customerName: apiOrder.customer?.name || 'Khách hàng',
    customerAvatar: apiOrder.customer?.avatar || null,
    address: apiOrder.shipping_address?.full_address || 'N/A',
    productId: firstItem.product_id,
    productName: firstItem.product_name || 'Sản phẩm',
    variant: firstItem.variant || 'Tiêu chuẩn',
    category: firstItem.category || 'Khác',
    image: firstItem.image || null,
    price: firstItem.unit_price_formatted || '0 VNĐ',
    totalPrice: apiOrder.total_amount_formatted || '0 VNĐ',
    orderDateDisplay: apiOrder.order_date,
    lastUpdateDisplay: apiOrder.updated_at,
    items: apiOrder.items || []
  };
};

// --- HÀM LOAD DỮ LIỆU TỪ API ---
const fetchOrders = async () => {
  isLoading.value = true;
  try {
    const response = await sellerOrderService.getSellerOrders(activeTab.value);
    if (response.data?.status === 'success') {
      orders.value = response.data.data.map(transformOrder);
    } else {
      orders.value = [];
    }
  } catch (error) {
    console.error('Error fetching orders:', error);
    showError('Không thể tải danh sách đơn hàng');
    orders.value = [];
  } finally {
    isLoading.value = false;
  }
};

// --- CÁC HÀM XỬ LÝ HÀNH ĐỘNG ---
const acceptOrder = async (order) => {
  if (!window.confirm(`Xác nhận chấp nhận đơn hàng #${order.trackingCode}?`)) return;
  
  try {
    await sellerOrderService.acceptOrder(order.id);
    showSuccess(`Đơn hàng #${order.trackingCode} đã được chấp nhận.`);
    await fetchOrders();
  } catch (error) {
    console.error('Accept order error:', error);
    const errorMsg = error.response?.data?.message || 'Có lỗi xảy ra';
    showError(errorMsg);
  }
};

const cancelOrder = async (order) => {
  if (!window.confirm(`Xác nhận HỦY đơn hàng #${order.trackingCode}?`)) return;
  
  try {
    await sellerOrderService.cancelOrder(order.id);
    showSuccess(`Đã hủy đơn hàng #${order.trackingCode}.`);
    await fetchOrders();
  } catch (error) {
    console.error('Cancel order error:', error);
    const errorMsg = error.response?.data?.message || 'Có lỗi xảy ra';
    showError(errorMsg);
  }
};

const shipOrder = async (order) => {
  if (!window.confirm(`Xác nhận đơn hàng #${order.trackingCode} đã đóng gói xong và sẵn sàng giao vận?`)) return;
  
  try {
    await sellerOrderService.shipOrder(order.id);
    showSuccess(`Đã chuyển đơn hàng #${order.trackingCode} sang trạng thái Vận chuyển.`);
    await fetchOrders();
  } catch (error) {
    console.error('Ship order error:', error);
    const errorMsg = error.response?.data?.message || 'Có lỗi xảy ra';
    showError(errorMsg);
  }
};

const completeOrder = async (order) => {
  if (!window.confirm(`Xác nhận đơn hàng #${order.trackingCode} đã giao thành công?`)) return;
  
  try {
    await sellerOrderService.completeOrder(order.id);
    showSuccess(`Đơn hàng #${order.trackingCode} đã hoàn thành.`);
    await fetchOrders();
  } catch (error) {
    console.error('Complete order error:', error);
    const errorMsg = error.response?.data?.message || 'Có lỗi xảy ra';
    showError(errorMsg);
  }
};

const refundOrder = async (order) => {
  if (!window.confirm(`Xác nhận đã nhận hàng hoàn và hoàn tiền cho đơn #${order.trackingCode}?`)) return;
  
  try {
    await sellerOrderService.confirmReturn(order.id);
    showSuccess(`Đã hoàn tất thủ tục hoàn tiền cho đơn #${order.trackingCode}.`);
    await fetchOrders();
  } catch (error) {
    console.error('Refund order error:', error);
    const errorMsg = error.response?.data?.message || 'Có lỗi xảy ra';
    showError(errorMsg);
  }
};

const viewOrderDetails = (order) => {
  router.push(`/orders/${order.id}`);
};

const closeReturnDetailsModal = () => showReturnDetailsModal.value = false;

// --- HELPER FUNCTIONS ---
const FALLBACK_IMAGE = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="150" fill="%23eee"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23999" font-size="12" text-anchor="middle" dy=".3em"%3ENo Image%3C/text%3E%3C/svg%3E';
const FALLBACK_AVATAR = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="%23ddd"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23888" font-size="10" text-anchor="middle" dy=".3em"%3EUser%3C/text%3E%3C/svg%3E';
const handleImageError = (e) => { if (!e.target.src.startsWith('data:')) e.target.src = FALLBACK_IMAGE; };
const handleAvatarError = (e) => { if (!e.target.src.startsWith('data:')) e.target.src = FALLBACK_AVATAR; };

onMounted(() => {
  fetchOrders();
});

// Khi đổi tab, fetch lại với filter
const changeTab = (tabId) => {
  activeTab.value = tabId;
  visibleCount.value = 10;
  fetchOrders();
};

const allFilteredOrders = computed(() => {
  return orders.value.filter(order => {
    const matchCategory = selectedCategories.value.length === 0 || selectedCategories.value.some(cat => 
      order.items?.some(item => item.category === cat)
    );
    // Lọc theo mã đơn hàng
    const searchLower = searchQuery.value.toLowerCase().trim();
    const matchSearch = !searchLower || 
      (order.trackingCode && order.trackingCode.toLowerCase().includes(searchLower)) ||
      (order.id && order.id.toString().includes(searchLower));
    return matchCategory && matchSearch;
  });
});

const visibleOrders = computed(() => allFilteredOrders.value.slice(0, visibleCount.value));
const hasMoreOrders = computed(() => visibleCount.value < allFilteredOrders.value.length);
const loadMore = () => visibleCount.value += 5;
const toggleCategory = (cat) => {
  const index = selectedCategories.value.indexOf(cat);
  if (index === -1) selectedCategories.value.push(cat); else selectedCategories.value.splice(index, 1);
};
const removeCategory = (cat) => toggleCategory(cat);
const clearAllCategories = () => { selectedCategories.value = []; };

</script>

<style scoped>
/* Loading State */
.loading-state { text-align: center; padding: 3rem; color: #666; font-style: italic; font-size: 1.1rem; }

/* THEME COLORS */
:root { --primary-blue: #0055aa; --primary-yellow: #ffc107; --text-dark: #333; }

.order-management-page { background-color: #f5f5f5; min-height: 100vh; padding-bottom: 3rem; }
.container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }

/* Breadcrumb */
.breadcrumb { padding: 1.2rem 0; font-size: 0.95rem; color: #555; }
.breadcrumb .link { color: #0055aa; cursor: pointer; font-weight: 600; }
.breadcrumb .divider { margin: 0 0.5rem; color: #999; }

/* Filter Section */
.filter-section { background: #fff; padding: 1.2rem; border-bottom: 1px solid #f2f2f2; border-radius: 8px 8px 0 0; }
.filter-row { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.label { font-weight: 600; color: #555; }

/* Search Box */
.search-box { position: relative; display: flex; align-items: center; }
.search-icon { position: absolute; left: 12px; color: #999; font-size: 0.9rem; }
.search-input { padding: 0.6rem 1rem 0.6rem 2.2rem; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95rem; width: 220px; transition: all 0.2s; }
.search-input:focus { outline: none; border-color: #0055aa; box-shadow: 0 0 0 2px rgba(0,85,170,0.1); }
.search-input::placeholder { color: #aaa; }

.dropdown-wrapper { position: relative; }
.btn-dropdown { background: #fff; border: 1px solid #ddd; padding: 0.6rem 1.2rem; border-radius: 8px; cursor: pointer; font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem; color: #333; transition: all 0.2s; }
.btn-dropdown:hover { background-color: #f0f7ff; border-color: #0055aa; color: #0055aa; }

.dropdown-menu { position: absolute; top: 115%; left: 0; background: white; border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px; min-width: 280px; z-index: 100; padding: 0.5rem 0; max-height: 300px; overflow-y: auto; }
.dropdown-item { padding: 0.8rem 1.2rem; cursor: pointer; font-size: 0.9rem; color: #333; display: flex; justify-content: space-between; }
.dropdown-item:hover { background-color: #f5f5f5; color: #0055aa; }
.dropdown-item.active-item { color: #0055aa; font-weight: 600; background-color: #e6f0fa; }

/* Tags */
.tags-container { display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center; }
.selected-tag { display: flex; align-items: center; gap: 0.5rem; background-color: #fff; border: 1px solid #0055aa; color: #0055aa; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.remove-tag:hover { opacity: 1; color: #d0011b; cursor: pointer; }
.clear-all-btn { background: none; border: none; color: #555; font-size: 0.9rem; cursor: pointer; text-decoration: underline; margin-left: 0.5rem; }
.clear-all-btn:hover { color: #0055aa; }

/* Tabs */
.status-tabs { background: #fff; display: flex; border-bottom: 1px solid #eee; margin-bottom: 1.5rem; overflow-x: auto; white-space: nowrap; border-radius: 0 0 8px 8px; }
.tab-item { flex: 1; min-width: fit-content; text-align: center; padding: 1.2rem 1.5rem; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-size: 1rem; color: #666; font-weight: 500; transition: all 0.2s; }
.tab-item:hover { color: #0055aa; }
.tab-item.active { color: #0055aa; border-bottom-color: #0055aa; font-weight: 700; }

/* Order Card */
.order-card { background: #fff; margin-bottom: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #eee; }
.card-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background-color: #fff; }
.shop-info { display: flex; align-items: center; gap: 1rem; }
.shop-name { font-weight: 700; color: #333; font-size: 1.05rem; }
/* [ĐÃ SỬA] Thêm pointer-events cho nút Chat và Xem KH */
.btn-chat, .btn-view-shop { padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; font-weight: 500; pointer-events: auto !important; }
.btn-chat { background: #0055aa; color: #fff; border: none; }
.btn-view-shop { background: #fff; border: 1px solid #ddd; color: #555; }
.order-status { display: flex; align-items: center; gap: 1rem; }
.delivery-status { color: #26aa99; font-size: 0.9rem; font-weight: 500; }
.status-label { color: #0055aa; text-transform: uppercase; padding-left: 1rem; border-left: 2px solid #eee; font-weight: 700; }

/* Product Info */
.product-row { padding: 1.5rem; display: flex; gap: 1.2rem; border-bottom: 1px solid #f9f9f9; }
.product-img { width: 90px; height: 90px; border: 1px solid #eee; object-fit: cover; border-radius: 6px; }
.product-info { flex: 1; }
.product-name { font-size: 1.1rem; font-weight: 600; margin: 0 0 0.4rem; color: #333; }
.product-variant { font-size: 0.9rem; color: #777; margin-bottom: 0.4rem; }
.product-category-label { font-size: 0.85rem; color: #0055aa; margin-bottom: 0.3rem; }
.product-address { font-size: 0.85rem; color: #999; font-style: italic; margin-bottom: 0.3rem; }
/* Meta Info: Tracking/Date/Update */
.order-meta { display: flex; gap: 1.5rem; margin-bottom: 0.5rem; font-size: 0.85rem; color: #777; flex-wrap: wrap; }
.order-meta strong { font-weight: 600; color: #333; }
.update-time { color: #0055aa; font-weight: 500; }

.seller-wrapper { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.6rem; font-size: 0.9rem; color: #555; }
.seller-avatar { width: 24px; height: 24px; border-radius: 50%; }
.product-price { font-size: 1.1rem; color: #0055aa; font-weight: 700; }

/* Footer & Buttons */
.card-footer { background: #f8fbff; padding: 1.5rem; display: flex; flex-direction: column; align-items: flex-end; gap: 1.2rem; }
.total-price { color: #0055aa; font-size: 1.6rem; font-weight: 700; margin-left: 0.5rem; }
.action-buttons { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: flex-end; }
.btn { min-width: 140px; padding: 0.6rem 0; border-radius: 6px; cursor: pointer; font-size: 0.95rem; font-weight: 600; transition: all 0.2s; border: 1px solid transparent; }
.btn-primary { background: #ffc107; color: #222; border-color: #ffc107; box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3); }
.btn-primary:hover { background: #ffb300; transform: translateY(-1px); }
.btn-default { background: #fff; border-color: #ddd; color: #555; }
.btn-default:hover { background: #fff; border-color: #999; color: #333; }
.btn-disabled { background: #f0f0f0; color: #999; border-color: #ddd; cursor: not-allowed; }
.btn-outline-danger { background: #fff; border-color: #dc3545; color: #dc3545; }
.btn-outline-danger:hover { background: #dc3545; color: #fff; }
.status-note { font-style: italic; color: #777; margin-right: 10px; align-self: center; }

/* LOAD MORE */
.load-more-container { text-align: center; margin-top: 1.5rem; padding-bottom: 1rem; }
.btn-load-more { background: #fff; border: 1px solid #ddd; padding: 0.8rem 2.5rem; border-radius: 25px; color: #555; cursor: pointer; font-size: 0.95rem; font-weight: 600; transition: all 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
.btn-load-more:hover { background: #f9f9f9; border-color: #0055aa; color: #0055aa; }
.empty-result { padding: 4rem; text-align: center; color: #777; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

/* === MODAL CSS === */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; justify-content: center; align-items: center; }
.modal-content { background: #fff; width: 600px; max-width: 90%; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.modal-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { margin: 0; color: #333; font-size: 1.2rem; }
.close-btn { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #888; display: flex; }

.modal-body { padding: 1.5rem; }
.form-group { margin-bottom: 1.2rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #555; }
.form-group textarea, .form-group input { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; }
.preview-images { display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; }
.img-name { font-size: 0.85rem; color: #666; background: #f0f0f0; padding: 2px 8px; border-radius: 4px; }
.alert-box { background: #e7f3fe; color: #0055aa; padding: 1rem; border-radius: 6px; margin-top: 1rem; font-size: 0.9rem; }
.return-img-placeholder img { width: 100px; height: 100px; object-fit: cover; border-radius: 6px; border: 1px dashed #ccc; margin-top: 0.5rem; }

.product-preview { display: flex; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px dashed #eee; }
.product-preview img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
.preview-info .name { font-weight: 600; color: #333; margin: 0 0 0.3rem 0; }
.preview-info .variant { font-size: 0.9rem; color: #777; margin: 0; }
.rating-stars-section { margin-bottom: 1.5rem; text-align: center; }
.stars-wrapper { display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 0.5rem; }
.star-btn { cursor: pointer; transition: transform 0.2s; }
.star-btn:hover { transform: scale(1.1); }
.rating-text { display: block; margin-top: 0.5rem; color: #ffc107; font-weight: 700; font-size: 1.1rem; }
.rating-comment textarea { width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; resize: vertical; outline: none; font-family: inherit; font-size: 0.95rem; }
.rating-comment textarea:focus { border-color: #0055aa; }
.modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 1rem; background: #f9f9f9; }
.fade-in { animation: fadeIn 0.2s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>
