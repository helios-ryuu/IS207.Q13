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
        <p>Đang tải dữ liệu đơn hàng...</p>
      </div>

      <div v-else class="order-list">
        <div v-for="order in visibleOrders" :key="order.id" class="order-card">
          <div class="card-header">
            <div class="shop-info">
              <span class="shop-name">{{ order.shopName }}</span>
              <button class="btn-view-shop" @click="visitShop(order)">
                <font-awesome-icon icon="store" /> Xem Shop
              </button>
            </div>
            <div class="order-status">
              <span class="delivery-status"><font-awesome-icon icon="truck" /> {{ order.deliveryStatus }}</span>
              <span class="status-label">{{ order.statusLabel }}</span>
            </div>
          </div>

          <!-- Order Meta -->
          <div class="order-meta-row">
            <span class="meta-item">Mã Đơn: <strong>{{ order.trackingCode || `ORD-${order.id}` }}</strong></span>
            <span class="meta-item">Ngày đặt: <strong>{{ order.orderDate }}</strong></span>
          </div>

          <!-- Seller Info -->
          <div class="seller-header">
            <div class="seller-info-row">
              <div v-if="!order.sellerAvatar" class="seller-avatar-letter">
                {{ order.sellerName ? order.sellerName.charAt(0).toUpperCase() : 'S' }}
              </div>
              <img v-else :src="order.sellerAvatar" class="seller-avatar" @error="handleAvatarError">
              <span class="seller-name">{{ order.sellerName }}</span>
              <button class="btn-view-shop-small" @click="visitShop(order)">Xem Shop</button>
            </div>
          </div>

          <!-- Items Table Header -->
          <div class="items-table-header">
            <span class="col-product">Sản phẩm</span>
            <span class="col-price">Đơn giá</span>
            <span class="col-qty">SL</span>
            <span class="col-subtotal">Thành tiền</span>
          </div>

          <!-- All Items in Order -->
          <div v-for="(item, index) in order.items" :key="index" class="product-row">
            <div class="col-product">
              <img :src="item.image || FALLBACK_IMAGE" alt="Sản phẩm" class="product-img" @error="handleImageError">
              <div class="product-info">
                <h3 class="product-name">{{ item.product_name }}</h3>
                <p class="product-variant">{{ item.variant }}</p>
              </div>
            </div>
            <div class="col-price">
              <span>{{ formatPrice(item.unit_price) }}</span>
            </div>
            <div class="col-qty">
              <span>{{ item.quantity }}</span>
            </div>
            <div class="col-subtotal">
              <span>{{ formatPrice(item.unit_price * item.quantity) }}</span>
            </div>
          </div>


          <div class="card-footer">
            <div class="price-breakdown">
              <div class="price-row"><span>Tạm tính ({{ order.itemCount }} sản phẩm):</span><span>{{ order.subtotalFormatted }}</span></div>
              <div class="price-row"><span>Phí vận chuyển:</span><span>{{ order.shippingFeeFormatted }}</span></div>
              <div class="price-row total"><span>Tổng cộng:</span><span class="total-price">{{ order.totalPrice }}</span></div>
            </div>
            
            <div class="action-buttons">
              
              <template v-if="order.statusId === 'pending'">
                <button class="btn btn-outline-danger" @click="openCancelModal(order)">Hủy Đơn</button>
              </template>

              <template v-else-if="order.statusId === 'shipping'">
                <button class="btn btn-primary" @click="openConfirmReceivedModal(order)">Xác nhận đã nhận hàng</button>
              </template>

              <template v-else-if="order.statusId === 'completed'">
                <button v-if="!order.isRated" class="btn btn-primary" @click="openRatingModal(order)">Đánh Giá</button>
                <button v-else class="btn btn-disabled" disabled>Đã đánh giá</button>
                
                <button class="btn btn-default" @click="buyAgain(order)">Mua Lại</button>
                </template>
              
              <template v-else-if="order.statusId === 'cancelled'">
                 <button class="btn btn-default" @click="openViewCancelReasonModal(order)">Xem lý do hủy</button>
                 <button class="btn btn-default" @click="buyAgain(order)">Mua Lại</button>
              </template>

              <template v-else-if="order.statusId === 'return'">
                <button class="btn btn-default">Đang xử lý trả hàng</button>
              </template>

              <template v-else>
                <button class="btn btn-default" @click="visitShop(order)">Xem Shop</button>
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
        <div class="product-preview" v-if="ratingData.order?.items?.length">
          <img :src="ratingData.order.items[0].image || FALLBACK_IMAGE" alt="Product" @error="handleImageError">
          <div class="preview-info"><p class="name">{{ ratingData.order.items[0].product_name }}</p><p class="variant">{{ ratingData.order.items[0].variant }}</p></div>
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

  <!-- Modal xác nhận đã nhận hàng -->
  <div v-if="showConfirmReceivedModal" class="modal-overlay" @click.self="closeConfirmReceivedModal">
    <div class="modal-content fade-in">
      <div class="modal-header">
        <h3>Xác nhận đã nhận hàng</h3>
        <button class="close-btn" @click="closeConfirmReceivedModal">✕</button>
      </div>
      <div class="modal-body">
        <div class="product-preview" v-if="confirmReceivedData.order?.items?.length">
          <img :src="confirmReceivedData.order.items[0].image || FALLBACK_IMAGE" alt="Product" @error="handleImageError">
          <div class="preview-info">
            <p class="name">{{ confirmReceivedData.order.items[0].product_name }}</p>
            <p class="variant">{{ confirmReceivedData.order.items[0].variant }}</p>
          </div>
        </div>
        <div class="confirm-message">
          <font-awesome-icon icon="box" class="confirm-icon" />
          <p>Bạn xác nhận đã nhận được hàng và hài lòng với sản phẩm?</p>
          <p class="confirm-note">Sau khi xác nhận, đơn hàng sẽ được chuyển sang trạng thái <strong>Hoàn thành</strong>.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" @click="closeConfirmReceivedModal">Hủy</button>
        <button class="btn btn-primary" @click="confirmReceivedOrder">Xác nhận đã nhận hàng</button>
      </div>
    </div>
  </div>

  <!-- MODAL HỦY ĐƠN - NHẬP LÝ DO -->
  <div v-if="showCancelModal" class="modal-overlay" @click.self="closeCancelModal">
    <div class="modal-content fade-in">
      <div class="modal-header">
        <h3>Hủy đơn hàng</h3>
        <button class="close-btn" @click="closeCancelModal">✕</button>
      </div>
      <div class="modal-body">
        <div class="product-preview" v-if="cancelData.order?.items?.length">
          <img :src="cancelData.order.items[0].image || FALLBACK_IMAGE" @error="handleImageError">
          <div class="preview-info">
            <p class="name">{{ cancelData.order.items[0].product_name }}</p>
            <p class="variant">Mã đơn: {{ cancelData.order.trackingCode }}</p>
          </div>
        </div>
        <div class="form-group">
          <label>Lý do hủy đơn <span class="required">*</span></label>
          <textarea v-model="cancelData.reason" rows="4" placeholder="Nhập lý do hủy đơn hàng..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" @click="closeCancelModal">Đóng</button>
        <button class="btn btn-outline-danger" @click="submitCancelOrder" :disabled="!cancelData.reason.trim()">Xác nhận hủy</button>
      </div>
    </div>
  </div>

  <!-- MODAL XEM LÝ DO HỦY -->
  <div v-if="showViewCancelReasonModal" class="modal-overlay" @click.self="closeViewCancelReasonModal">
    <div class="modal-content fade-in">
      <div class="modal-header">
        <h3>Lý do hủy đơn</h3>
        <button class="close-btn" @click="closeViewCancelReasonModal">✕</button>
      </div>
      <div class="modal-body info-view">
        <p><strong>Mã đơn hàng:</strong> {{ viewCancelReasonData.trackingCode }}</p>
        <p><strong>Lý do hủy:</strong></p>
        <div class="cancel-reason-box">{{ viewCancelReasonData.reason || 'Không có lý do' }}</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" @click="closeViewCancelReasonModal">Đóng</button>
      </div>
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
import { useToast } from '../utils/useToast';

const router = useRouter();
const goToHome = () => router.push('/');
const { showSuccess, showError } = useToast();

// --- DATA STATE ---
const activeTab = ref('all');
const isDropdownOpen = ref(false);
const selectedCategories = ref([]); 
const visibleCount = ref(5); 
const ratedOrderIds = ref([]);
const isLoading = ref(false);
const orders = ref([]); // Mảng chứa dữ liệu thật
const searchQuery = ref(''); // Tìm kiếm theo mã đơn

// --- RETURN MODAL STATE (Giữ UI từ Main) ---
const showReturnModal = ref(false);
const showReturnDetailsModal = ref(false);
const returnData = reactive({ orderId: null, reason: '', images: [], pickupDate: '' });
const currentReturnDetails = reactive({ reason: '', pickupDate: '' });

// MODAL ĐÁNH GIÁ
const showRatingModal = ref(false);
const ratingData = reactive({ order: null, score: 5, comment: '' });

// MODAL XÁC NHẬN ĐÃ NHẬN HÀNG
const showConfirmReceivedModal = ref(false);
const confirmReceivedData = reactive({ order: null });

// MODAL HỦY ĐƠN
const showCancelModal = ref(false);
const cancelData = reactive({ order: null, reason: '' });

// MODAL XEM LÝ DO HỦY
const showViewCancelReasonModal = ref(false);
const viewCancelReasonData = reactive({ trackingCode: '', reason: '' });

const tabs = [
  { id: 'all', name: 'Tất cả' },
  { id: 'pending', name: 'Chờ xác nhận' },
  { id: 'shipping', name: 'Đang vận chuyển' },
  { id: 'completed', name: 'Hoàn thành' },
  { id: 'cancelled', name: 'Đã hủy' }
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
      // Lấy tất cả items trong đơn hàng (giờ chỉ từ 1 seller)
      const items = order.items || [];
      const firstItem = items.length > 0 ? items[0] : {};
      
      // Tính tạm tính từ items
      const subtotal = items.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
      const shippingFee = order.shipping_fee || 0;
      const totalAmount = order.total_amount || (subtotal + shippingFee);
      
      // Đếm tổng số sản phẩm
      const itemCount = items.reduce((sum, item) => sum + item.quantity, 0);

      return {
        id: order.id,
        trackingCode: order.tracking_code,
        orderDate: order.order_date || '',
        shopName: firstItem.seller_name || 'Shop VietMarket', 
        shopId: firstItem.seller_id,
        productId: firstItem.product_id,
        
        items: items,
        itemCount: itemCount,
        category: firstItem.category || 'Khác',
        
        statusId: mapBackendStatus(order.status),
        statusLabel: getStatusLabel(order.status),
        deliveryStatus: getDeliveryStatusText(order.status),
        isRated: order.is_rated || false, // Từ API
        
        // Giá tiền chi tiết
        subtotal: subtotal,
        subtotalFormatted: formatPrice(subtotal),
        shippingFee: shippingFee,
        shippingFeeFormatted: shippingFee > 0 ? formatPrice(shippingFee) : 'Miễn phí',
        totalPrice: order.total_amount_formatted || formatPrice(totalAmount),
        
        sellerAvatar: firstItem.seller_avatar || null,
        sellerName: firstItem.seller_name || 'Người bán',
        notes: order.notes || '',
        cancelReason: order.notes || ''
      };
    });
  } catch (error) {
    console.error("Lỗi tải đơn hàng:", error);
  } finally {
    isLoading.value = false;
  }
};

// 2. Hủy đơn hàng - Mở modal nhập lý do
const openCancelModal = (order) => {
  cancelData.order = order;
  cancelData.reason = '';
  showCancelModal.value = true;
};
const closeCancelModal = () => {
  showCancelModal.value = false;
  cancelData.order = null;
  cancelData.reason = '';
};

const submitCancelOrder = async () => {
  if (!cancelData.order || !cancelData.reason.trim()) return;
  
  try {
    await api.post(`/orders/${cancelData.order.id}/cancel`, {
      reason: cancelData.reason.trim()
    });
    showSuccess('Đã hủy đơn hàng thành công');
    closeCancelModal();
    await fetchOrders();
  } catch (error) {
    console.error("Lỗi hủy đơn:", error);
    showError(error.response?.data?.message || 'Không thể hủy đơn hàng này');
  }
};

// Xem lý do hủy
const openViewCancelReasonModal = (order) => {
  viewCancelReasonData.trackingCode = order.trackingCode;
  viewCancelReasonData.reason = order.cancelReason || order.notes || 'Không có lý do';
  showViewCancelReasonModal.value = true;
};
const closeViewCancelReasonModal = () => {
  showViewCancelReasonModal.value = false;
};

// 3. Xác nhận đã nhận hàng (API Thật - Cần backend hỗ trợ PUT status)
// 3. Xác nhận đã nhận hàng (API Thật - Cần backend hỗ trợ PUT status)
const confirmReceived = async (orderId) => {
  if (!confirm('Bạn xác nhận đã nhận được hàng và hài lòng với sản phẩm?')) return;
  try {
    // Gọi API update status thành completed (Nếu backend cho phép User update)
    // Nếu backend chưa có, bạn cần báo backend mở quyền này cho user
    await api.put(`/orders/${orderId}/status`, { status: 'completed' });
    showSuccess('Cảm ơn bạn đã mua hàng!');
    await fetchOrders();
  } catch (error) {
    console.error("Lỗi xác nhận:", error);
    showError('Có lỗi xảy ra, vui lòng thử lại sau.');
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
    case 'processing': 
    case 'shipping': return 'shipping'; // Gộp vào tab Vận chuyển
    case 'shipped': 
    case 'delivered': 
    case 'suspended': return 'completed'; // Suspended vẫn coi như hoàn thành để cho đánh giá
    case 'cancelled': return 'cancelled';
    case 'refunded': return 'return';
    case 'completed': return 'completed';
    default: return 'all';
  }
};

const getStatusLabel = (status) => {
  const map = {
    pending: 'CHỜ XÁC NHẬN',
    confirmed: 'ĐÃ XÁC NHẬN',
    processing: 'ĐANG VẬN CHUYỂN',
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
    processing: 'Đơn hàng đang vận chuyển',
    shipping: 'Đơn hàng đang vận chuyển',
    shipped: 'Shipper đang giao hàng đến bạn',
    delivered: 'Giao hàng thành công',
    completed: 'Giao hàng thành công',
    cancelled: 'Đơn hàng đã bị hủy',
    refunded: 'Đã hoàn tiền thành công'
  };
  return map[status] || '';
};

// --- CÁC HÀM ĐIỀU HƯỚNG ---
const visitShop = (order) => {
  if (order.shopId) {
    router.push({ name: 'SellerProfile', params: { id: order.shopId } });
  } else {
    showError('Không tìm thấy thông tin người bán');
  }
};
const buyAgain = (order) => {
  if (order.productId) {
    router.push({ path: `/product/${order.productId}` });
  } else {
    showError('Không tìm thấy sản phẩm');
  }
};
const goToSeller = (sellerId) => {
  if (sellerId) {
    router.push({ name: 'SellerProfile', params: { id: sellerId } });
  }
};

// --- MODAL & ACTION LOGIC (UI từ Main) ---
// Fallback images - Data URI
const FALLBACK_IMAGE = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="150" fill="%23eee"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23999" font-size="12" text-anchor="middle" dy=".3em"%3ENo Image%3C/text%3E%3C/svg%3E';
const FALLBACK_AVATAR = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="%23ddd"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23888" font-size="10" text-anchor="middle" dy=".3em"%3EUser%3C/text%3E%3C/svg%3E';

const handleImageError = (e) => { if (!e.target.src.startsWith('data:')) e.target.src = FALLBACK_IMAGE; };
const handleAvatarError = (e) => { if (!e.target.src.startsWith('data:')) e.target.src = FALLBACK_AVATAR; };

// Logic Đánh giá (Frontend Mockup - chưa gọi API thật vì chưa có endpoint rating)
const checkIfRated = (orderId) => ratedOrderIds.value.includes(orderId);
const openRatingModal = (order) => { ratingData.order = order; ratingData.score = 5; ratingData.comment = ''; showRatingModal.value = true; };
const closeRatingModal = () => showRatingModal.value = false;
const getRatingText = (score) => ['Tệ', 'Không hài lòng', 'Bình thường', 'Hài lòng', 'Tuyệt vời'][score - 1] || '';

const submitRating = async () => {
  if (!ratingData.order || !ratingData.order.items?.length) return;
  
  const productId = ratingData.order.items[0].product_id;
  if (!productId) {
    showError('Không tìm thấy thông tin sản phẩm');
    return;
  }
  
  try {
    await api.post(`/products/${productId}/reviews`, {
      rating: ratingData.score,
      content: ratingData.comment
    });
    showSuccess('Gửi đánh giá thành công!');
    
    // Cập nhật trạng thái isRated trong orders array
    const orderIndex = orders.value.findIndex(o => o.id === ratingData.order.id);
    if (orderIndex !== -1) {
      orders.value[orderIndex].isRated = true;
    }
    
    closeRatingModal();
  } catch (error) {
    console.error("Lỗi gửi đánh giá:", error);
    const msg = error.response?.data?.message || 'Không thể gửi đánh giá, vui lòng thử lại.';
    
    // Nếu backend báo đã đánh giá (422), đánh dấu order là đã rated
    if (error.response?.status === 422 && msg.includes('đã đánh giá')) {
      const orderIndex = orders.value.findIndex(o => o.id === ratingData.order.id);
      if (orderIndex !== -1) {
        orders.value[orderIndex].isRated = true;
      }
      closeRatingModal();
    }
    showError(msg);
  }
};

const openConfirmReceivedModal = (order) => { 
  confirmReceivedData.order = order; 
  showConfirmReceivedModal.value = true; 
};
const closeConfirmReceivedModal = () => showConfirmReceivedModal.value = false;

const confirmReceivedOrder = async () => {
  if (!confirmReceivedData.order) return;
  
  try {
    await api.put(`/orders/${confirmReceivedData.order.id}/status`, { status: 'completed' });
    showSuccess('Đã xác nhận nhận hàng, đơn hàng hoàn thành!');
    await fetchOrders();
    closeConfirmReceivedModal();
  } catch (error) {
    console.error("Lỗi xác nhận:", error);
    const msg = error.response?.data?.message || 'Có lỗi xảy ra, vui lòng thử lại sau.';
    showError(msg);
  }
};

// --- FILTER ---
const allFilteredOrders = computed(() => {
  return orders.value.filter(order => {
    const matchTab = activeTab.value === 'all' || order.statusId === activeTab.value;
    const matchCategory = selectedCategories.value.length === 0 || selectedCategories.value.includes(order.category);
    // Lọc theo mã đơn hàng
    const searchLower = searchQuery.value.toLowerCase().trim();
    const matchSearch = !searchLower || 
      (order.trackingCode && order.trackingCode.toLowerCase().includes(searchLower)) ||
      (order.id && order.id.toString().includes(searchLower));
    return matchTab && matchCategory && matchSearch;
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
.btn-chat, .btn-view-shop { padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; font-weight: 500; }
.btn-chat { background: #0055aa; color: #fff; border: none; }
.btn-view-shop { background: #fff; border: 1px solid #ddd; color: #555; }
.order-status { display: flex; align-items: center; gap: 1rem; }
.delivery-status { color: #26aa99; font-size: 0.9rem; font-weight: 500; }
.status-label { color: #0055aa; text-transform: uppercase; padding-left: 1rem; border-left: 2px solid #eee; font-weight: 700; }

/* Product Info & Seller Groups */
.order-meta-row { padding: 0.8rem 1.5rem; background: #f9fbfd; border-bottom: 1px solid #eee; display: flex; gap: 2rem; font-size: 0.9rem; color: #666; flex-wrap: wrap; }
.order-meta-row .meta-item { display: flex; gap: 0.5rem; }
.order-meta-row strong { color: #333; }

/* Seller Header */
.seller-header { background: #f1f5f9; padding: 0.8rem 1.5rem; }
.seller-info-row { display: flex; align-items: center; gap: 0.6rem; }
.seller-info-row .seller-name { font-weight: 600; color: #1e293b; }
.btn-view-shop-small { padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: 4px; background: #fff; border: 1px solid #cbd5e1; color: #64748b; cursor: pointer; margin-left: 0.5rem; }
.btn-view-shop-small:hover { border-color: #0055aa; color: #0055aa; }

/* Items Table Header */
.items-table-header { display: flex; padding: 0.6rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e5e7eb; font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase; }
.items-table-header .col-product { flex: 1; }
.items-table-header .col-price { width: 100px; text-align: center; }
.items-table-header .col-qty { width: 50px; text-align: center; }
.items-table-header .col-subtotal { width: 120px; text-align: right; }

/* Product Row as Table */
.product-row { display: flex; padding: 0.8rem 1.5rem; border-bottom: 1px solid #f1f5f9; align-items: center; }
.product-row:last-of-type { border-bottom: none; }
.product-row .col-product { flex: 1; display: flex; gap: 0.8rem; align-items: center; min-width: 0; }
.product-row .col-price { width: 100px; text-align: center; color: #64748b; font-size: 0.9rem; }
.product-row .col-qty { width: 50px; text-align: center; color: #333; font-weight: 600; }
.product-row .col-subtotal { width: 120px; text-align: right; color: #0055aa; font-weight: 600; font-size: 0.95rem; }

.product-img { width: 60px; height: 60px; border: 1px solid #eee; object-fit: cover; border-radius: 6px; flex-shrink: 0; }
.product-info { flex: 1; min-width: 0; }
.product-name { font-size: 0.95rem; font-weight: 600; margin: 0 0 0.2rem; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.product-variant { font-size: 0.8rem; color: #94a3b8; margin: 0; }

.seller-avatar { width: 24px; height: 24px; border-radius: 50%; object-fit: cover; }
.seller-avatar-letter { width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; }

/* Footer & Price Breakdown */
.card-footer { background: #f8fbff; padding: 1.2rem 1.5rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 1.5rem; flex-wrap: wrap; }
.price-breakdown { display: flex; flex-direction: column; gap: 0.4rem; min-width: 250px; }
.price-row { display: flex; justify-content: space-between; font-size: 0.9rem; color: #666; }
.price-row.total { font-size: 1rem; font-weight: 600; color: #333; padding-top: 0.4rem; border-top: 1px dashed #ddd; margin-top: 0.3rem; }
.total-price { color: #0055aa; font-size: 1.4rem; font-weight: 700; }
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

.confirm-message { text-align: center; color: #555; padding: 1rem 0; }
.confirm-icon { font-size: 3rem; color: #0055aa; margin-bottom: 1rem; display: block; margin-left: auto; margin-right: auto; }
.confirm-note { font-size: 0.9rem; color: #777; margin-top: 0.8rem; background: #f5f9ff; padding: 0.8rem; border-radius: 6px; }

/* Cancel Reason Modal */
.cancel-reason-box { background: #fff5f5; border: 1px solid #ffcccc; padding: 1rem; border-radius: 8px; color: #555; margin-top: 0.5rem; white-space: pre-wrap; }
.required { color: #dc3545; }
.form-group textarea { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; font-size: 0.95rem; resize: vertical; }
.form-group textarea:focus { outline: none; border-color: #0055aa; box-shadow: 0 0 0 2px rgba(0,85,170,0.1); }

.fade-in { animation: fadeIn 0.2s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>