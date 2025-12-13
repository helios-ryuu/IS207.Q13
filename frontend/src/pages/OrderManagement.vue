<template>
  <HeaderOther />

  <div class="order-management-page">
    <div class="container">
      
      <div class="breadcrumb">
        <span class="link" @click="goToHome">VietMarket</span> 
        <span class="divider">></span> 
        <span class="current">Đơn mua</span>
      </div>

      <div class="filter-section">
        <div class="filter-row">
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
        <p>Đang tải dữ liệu đơn hàng...</p>
      </div>

      <div v-else class="order-list">
        <div v-for="order in visibleOrders" :key="order.id" class="order-card">
          <div class="card-header">
            <div class="shop-info">
              <span class="shop-name">{{ order.shopName }}</span>
              <button class="btn-chat" @click="contactSeller(order)">
                <font-awesome-icon icon="comments" /> Chat
              </button>
              
              <button class="btn-view-shop" @click="visitShop(order)">
                <font-awesome-icon icon="store" /> Xem Shop
              </button>
            </div>
            <div class="order-status">
              <span class="delivery-status"><font-awesome-icon icon="truck" /> {{ order.deliveryStatus }}</span>
              <span class="status-label">{{ order.statusLabel }}</span>
            </div>
          </div>

          <div class="product-row">
            <img :src="order.image" alt="Sản phẩm" class="product-img" @error="handleImageError">
            <div class="product-info">
              <h3 class="product-name">{{ order.productName }}</h3>
              <p class="product-variant">Phân loại: {{ order.variant }}</p>
              <p class="product-category-label">Danh mục: {{ order.category }}</p>
              <div class="order-meta">
                <span class="meta-item">Mã Đơn: <strong>{{ order.trackingCode || `ORD-${order.id}` }}</strong></span>
                <span class="meta-item">Giá: <strong>{{ order.price }}</strong></span>
              </div>
              <div class="seller-wrapper">
                <img :src="order.sellerAvatar" class="seller-avatar" @error="handleAvatarError">
                <span>{{ order.sellerName }}</span>
              </div>
            </div>
            <div class="product-price">
              <span class="current-price">{{ order.totalPrice }}</span>
            </div>
          </div>

          <div class="card-footer">
            <div class="total-section">
              Thành tiền: <span class="total-price">{{ order.totalPrice }}</span>
            </div>
            
            <div class="action-buttons">
              
              <template v-if="order.statusId === 'pending'">
                <button class="btn btn-default" @click="contactSeller(order)">Liên hệ</button>
                <button class="btn btn-outline-danger" @click="cancelOrder(order.id)">Hủy Đơn</button>
              </template>

              <template v-else-if="order.statusId === 'shipping'">
                <button class="btn btn-default" @click="contactSeller(order)">Liên hệ</button>
                <button class="btn btn-disabled" disabled>Đang vận chuyển</button>
              </template>

              <template v-else-if="order.statusId === 'delivering'">
                <button class="btn btn-default" @click="contactSeller(order)">Liên hệ</button>
                <button class="btn btn-primary" @click="confirmReceived(order.id)">Đã Nhận Hàng</button>
              </template>

              <template v-else-if="order.statusId === 'completed'">
                <button v-if="!checkIfRated(order.id)" class="btn btn-primary" @click="openRatingModal(order)">Đánh Giá</button>
                <button v-else class="btn btn-disabled" disabled>Đã đánh giá</button>
                
                <button class="btn btn-default" @click="buyAgain(order)">Mua Lại</button>
                </template>
              
              <template v-else-if="order.statusId === 'cancelled'">
                 <span class="status-note">Đơn đã hủy</span>
                 <button class="btn btn-default" @click="buyAgain(order)">Mua Lại</button>
              </template>

              <template v-else-if="order.statusId === 'return'">
                <button class="btn btn-default">Đang xử lý trả hàng</button>
              </template>

              <template v-else>
                <button class="btn btn-default" @click="contactSeller(order)">Liên hệ người bán</button>
              </template>

            </div>
          </div>
        </div>

        <div v-if="hasMoreOrders" class="load-more-container">
          <button class="btn-load-more" @click="loadMore">Xem thêm đơn hàng <font-awesome-icon icon="chevron-down" /></button>
        </div>
        
        <div v-if="visibleOrders.length === 0 && !isLoading" class="empty-result">
          <p>Không tìm thấy đơn hàng nào phù hợp.</p>
        </div>
      </div>
    </div>
  </div>

  <div v-if="showRatingModal" class="modal-overlay" @click.self="closeRatingModal">
    <div class="modal-content fade-in">
      <div class="modal-header"><h3>Đánh giá sản phẩm</h3><button class="close-btn" @click="closeRatingModal">✕</button></div>
      <div class="modal-body">
        <div class="product-preview" v-if="ratingData.order">
          <img :src="ratingData.order.image" alt="Product" @error="handleImageError">
          <div class="preview-info"><p class="name">{{ ratingData.order.productName }}</p><p class="variant">{{ ratingData.order.variant }}</p></div>
        </div>
        <div class="rating-stars-section">
          <div class="stars-wrapper">
            <span v-for="star in 5" :key="star" class="star-btn" @click="ratingData.score = star">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" :fill="star <= ratingData.score ? '#ffc107' : '#e4e5e9'" width="32px" height="32px"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            </span>
          </div>
          <span class="rating-text">{{ getRatingText(ratingData.score) }}</span>
        </div>
        <div class="rating-comment"><textarea v-model="ratingData.comment" rows="4" placeholder="Chia sẻ cảm nhận..."></textarea></div>
      </div>
      <div class="modal-footer"><button class="btn btn-default" @click="closeRatingModal">Trở lại</button><button class="btn btn-primary" @click="submitRating">Hoàn thành</button></div>
    </div>
  </div>

  <Footer />
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import HeaderOther from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';
import api from '../utils/api'; // Sử dụng API thật
import { getImageUrl } from '../utils/imageUrl';

const router = useRouter();
const goToHome = () => router.push('/');

// --- DATA STATE ---
const activeTab = ref('all');
const isDropdownOpen = ref(false);
const selectedCategories = ref([]); 
const visibleCount = ref(5); 
const ratedOrderIds = ref([]);
const isLoading = ref(false);
const orders = ref([]); // Mảng chứa dữ liệu thật

// --- RETURN MODAL STATE (Giữ UI từ Main) ---
const showReturnModal = ref(false);
const showReturnDetailsModal = ref(false);
const returnData = reactive({ orderId: null, reason: '', images: [], pickupDate: '' });
const currentReturnDetails = reactive({ reason: '', pickupDate: '' });

// MODAL ĐÁNH GIÁ
const showRatingModal = ref(false);
const ratingData = reactive({ order: null, score: 5, comment: '' });

const tabs = [
  { id: 'all', name: 'Tất cả' },
  { id: 'pending', name: 'Chờ xác nhận' },
  { id: 'shipping', name: 'Vận chuyển' }, // Gộp confirm/processing vào đây
  { id: 'delivering', name: 'Chờ giao hàng' }, 
  { id: 'completed', name: 'Hoàn thành' },
  { id: 'cancelled', name: 'Đã hủy' },
  { id: 'return', name: 'Trả hàng / Hoàn tiền' }
];

const categories = [
  'Xe cộ', 'Đồ điện tử', 'Thú cưng', 'Thời trang', 'Đồ gia dụng'
];

// --- API CALLS (LOGIC THẬT TỪ NHÁNH HEAD) ---

// 1. Lấy danh sách đơn hàng
const fetchOrders = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/orders');
    console.log("Dữ liệu API:", response.data);

    // Xử lý "Búp bê Nga" (lấy mảng dữ liệu thực)
    const ordersData = Array.isArray(response.data.data) 
      ? response.data.data 
      : (response.data.data?.data || []); 

    // Map dữ liệu từ Backend sang cấu trúc UI của Frontend
    orders.value = ordersData.map(order => {
      // Backend trả về key 'items' (dựa trên ảnh bạn cung cấp trước đó)
      const firstItem = (order.items && order.items.length > 0) ? order.items[0] : {}; 
      
      // Xử lý giá tiền
      let displayPrice = order.total_amount;
      if (typeof displayPrice === 'number') {
          displayPrice = formatPrice(displayPrice);
      }

      // Tên sản phẩm
      const displayName = firstItem.product_name || firstItem.variant || 'Sản phẩm đơn hàng';

      return {
        id: order.id,
        trackingCode: order.tracking_code,
        shopName: 'VietMarket Shop', 
        productName: displayName,
        variant: firstItem.variant || '',
        category: 'Tổng hợp',
        
        statusId: mapBackendStatus(order.status),
        statusLabel: getStatusLabel(order.status),
        deliveryStatus: getDeliveryStatusText(order.status),
        
        price: displayPrice,      
        totalPrice: displayPrice, 
        
        image: getImageUrl(firstItem.image || 'default.jpg'),
        sellerAvatar: '/avatar.jpg',
        sellerName: 'Shop'
      };
    });
  } catch (error) {
    console.error("Lỗi tải đơn hàng:", error);
  } finally {
    isLoading.value = false;
  }
};

// 2. Hủy đơn hàng (API Thật)
const cancelOrder = async (orderId) => {
  if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) return;
  
  try {
    await api.post(`/orders/${orderId}/cancel`);
    alert('Đã hủy đơn hàng thành công');
    await fetchOrders(); // Load lại danh sách
  } catch (error) {
    console.error("Lỗi hủy đơn:", error);
    alert(error.response?.data?.message || 'Không thể hủy đơn hàng này');
  }
};

// 3. Xác nhận đã nhận hàng (API Thật - Cần backend hỗ trợ PUT status)
const confirmReceived = async (orderId) => {
  if (!confirm('Bạn xác nhận đã nhận được hàng và hài lòng với sản phẩm?')) return;
  try {
    // Gọi API update status thành completed (Nếu backend cho phép User update)
    // Nếu backend chưa có, bạn cần báo backend mở quyền này cho user
    await api.put(`/orders/${orderId}/status`, { status: 'completed' });
    alert('Cảm ơn bạn đã mua hàng!');
    await fetchOrders();
  } catch (error) {
    console.error("Lỗi xác nhận:", error);
    alert('Có lỗi xảy ra, vui lòng thử lại sau.');
  }
};

// --- HELPER FUNCTIONS ---
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const mapBackendStatus = (status) => {
  switch(status) {
    case 'pending': return 'pending';
    case 'confirmed': 
    case 'processing': return 'shipping'; // Gộp vào vận chuyển
    case 'shipped': return 'delivering'; // Shipped -> Chờ giao hàng
    case 'delivered': return 'completed';
    case 'cancelled': return 'cancelled';
    case 'refunded': return 'return';
    default: return 'all';
  }
};

const getStatusLabel = (status) => {
  const map = {
    pending: 'CHỜ XÁC NHẬN',
    confirmed: 'ĐÃ XÁC NHẬN',
    processing: 'ĐANG XỬ LÝ',
    shipped: 'ĐANG GIAO',
    delivered: 'HOÀN THÀNH',
    cancelled: 'ĐÃ HỦY',
    refunded: 'ĐÃ HOÀN TIỀN'
  };
  return map[status] || status;
};

const getDeliveryStatusText = (status) => {
  const map = {
    pending: 'Đang chờ người bán xác nhận',
    confirmed: 'Đơn hàng đang được đóng gói',
    processing: 'Đơn hàng đang được vận chuyển kho',
    shipped: 'Shipper đang giao hàng đến bạn',
    delivered: 'Giao hàng thành công',
    cancelled: 'Đơn hàng đã bị hủy',
    refunded: 'Đã hoàn tiền thành công'
  };
  return map[status] || '';
};

// --- CÁC HÀM ĐIỀU HƯỚNG ---
const visitShop = (order) => alert("Tính năng đang phát triển");
const buyAgain = (order) => alert("Tính năng đang phát triển");
const contactSeller = (order) => alert("Tính năng Chat đang phát triển");

// --- MODAL & ACTION LOGIC (UI từ Main) ---
const handleImageError = (e) => { e.target.src = "https://via.placeholder.com/150?text=No+Image"; };
const handleAvatarError = (e) => { e.target.src = "https://via.placeholder.com/50?text=User"; };

// Logic Đánh giá (Frontend Mockup - chưa gọi API thật vì chưa có endpoint rating)
const checkIfRated = (orderId) => ratedOrderIds.value.includes(orderId);
const openRatingModal = (order) => { ratingData.order = order; ratingData.score = 5; ratingData.comment = ''; showRatingModal.value = true; };
const closeRatingModal = () => showRatingModal.value = false;
const getRatingText = (score) => ['Tệ', 'Không hài lòng', 'Bình thường', 'Hài lòng', 'Tuyệt vời'][score - 1] || '';

const submitRating = async () => {
  if (!ratingData.order) return;
  // TODO: Gọi API POST /reviews khi backend sẵn sàng
  alert('Gửi đánh giá thành công! (Demo)');
  ratedOrderIds.value.push(ratingData.order.id);
  closeRatingModal();
};

// --- FILTER ---
const allFilteredOrders = computed(() => {
  return orders.value.filter(order => {
    const matchTab = activeTab.value === 'all' || order.statusId === activeTab.value;
    const matchCategory = selectedCategories.value.length === 0 || selectedCategories.value.includes(order.category);
    return matchTab && matchCategory;
  });
});

const visibleOrders = computed(() => allFilteredOrders.value.slice(0, visibleCount.value));
const hasMoreOrders = computed(() => visibleCount.value < allFilteredOrders.value.length);
const loadMore = () => visibleCount.value += 5;
const changeTab = (tabId) => { activeTab.value = tabId; visibleCount.value = 10; };
const toggleCategory = (cat) => {
  const index = selectedCategories.value.indexOf(cat);
  if (index === -1) selectedCategories.value.push(cat); else selectedCategories.value.splice(index, 1);
};
const removeCategory = (cat) => toggleCategory(cat);
const clearAllCategories = () => { selectedCategories.value = []; };

onMounted(() => {
  fetchOrders();
  // Load rated IDs from local storage if needed
  const storedRatedIds = localStorage.getItem('rated_order_ids');
  if (storedRatedIds) ratedOrderIds.value = JSON.parse(storedRatedIds);
});
</script>

<style scoped>
/* Loading State */
.loading-state { text-align: center; padding: 3rem; color: #666; font-style: italic; font-size: 1.1rem; }

/* THEME COLORS */
:root { --primary-blue: #0055aa; --primary-yellow: #ffc107; --text-dark: #333; }

.order-management-page { background-color: #f5f5f5; min-height: 100vh; padding-bottom: 3rem; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }

/* Breadcrumb */
.breadcrumb { padding: 1.2rem 0; font-size: 0.95rem; color: #555; }
.breadcrumb .link { color: #0055aa; cursor: pointer; font-weight: 600; }
.breadcrumb .divider { margin: 0 0.5rem; color: #999; }

/* Filter Section */
.filter-section { background: #fff; padding: 1.2rem; border-bottom: 1px solid #f2f2f2; border-radius: 8px 8px 0 0; }
.filter-row { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.label { font-weight: 600; color: #555; }

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
.btn-chat, .btn-view-shop { padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; font-weight: 500; }
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
.product-category-label { font-size: 0.85rem; color: #999; font-style: italic; }
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