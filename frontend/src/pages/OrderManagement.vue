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
        <p>Đang khởi tạo dữ liệu mẫu...</p>
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
                <span class="meta-item">Mã Đơn hàng: <strong>{{ order.trackingCode }}</strong></span>
                <span class="meta-item">Ngày Đặt: <strong>{{ order.orderDateDisplay }}</strong></span>
                <span class="meta-item update-time">Cập nhật: <strong>{{ order.lastUpdateDisplay }}</strong></span>
              </div>
              <div class="seller-wrapper">
                <img :src="order.sellerAvatar" class="seller-avatar" @error="handleAvatarError">
                <span>{{ order.sellerName }}</span>
              </div>
            </div>
            <div class="product-price">
              <span class="current-price">{{ order.price }}</span>
            </div>
          </div>

          <div class="card-footer">
            <div class="total-section">
              Thành tiền: <span class="total-price">{{ order.totalPrice }}</span>
            </div>
            <div class="action-buttons">
              
              <template v-if="order.statusId === 'pending'">
                <button class="btn btn-default" @click="contactSeller(order)">Liên hệ người bán</button>
                <button class="btn btn-outline-danger" @click="cancelOrder(order)">Hủy Đơn</button>
              </template>

              <template v-else-if="order.statusId === 'shipping'">
                <button class="btn btn-default" @click="contactSeller(order)">Liên hệ người bán</button>
              </template>

              <template v-else-if="order.statusId === 'delivering'">
                <button class="btn btn-default" @click="contactSeller(order)">Liên hệ người bán</button>
                <button class="btn btn-primary" @click="confirmReceived(order)">Đã Nhận Hàng</button>
              </template>

              <template v-else-if="order.statusId === 'completed'">
                <button v-if="!checkIfRated(order.id)" class="btn btn-primary" @click="openRatingModal(order)">Đánh Giá</button>
                <button v-else class="btn btn-disabled" disabled>Đã đánh giá</button>
                
                <button class="btn btn-default" @click="buyAgain(order)">Mua Lại</button>
                <button class="btn btn-outline-danger" @click="openReturnModal(order)">Trả Hàng / Hoàn Tiền</button>
              </template>
              
              <template v-else-if="order.statusId === 'cancelled'">
                 <span class="status-note">Đơn đã hủy</span>
                 <button class="btn btn-default" @click="buyAgain(order)">Mua Lại</button>
              </template>

              <template v-else-if="order.statusId === 'return'">
                <button class="btn btn-default" @click="viewReturnDetails(order)">Xem chi tiết lý do</button>
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
        
        <div v-if="visibleOrders.length === 0" class="empty-result">
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

  <div v-if="showReturnModal" class="modal-overlay" @click.self="closeReturnModal">
    <div class="modal-content fade-in">
      <div class="modal-header"><h3>Yêu cầu Trả hàng</h3><button class="close-btn" @click="closeReturnModal">✕</button></div>
      <div class="modal-body">
        <div class="form-group"><label>Lý do:</label><textarea v-model="returnData.reason" rows="3"></textarea></div>
        <div class="form-group"><label>Ảnh minh chứng:</label><input type="file" multiple @change="handleFileChange"></div>
        <div class="form-group"><label>Ngày lấy hàng:</label><input type="date" v-model="returnData.pickupDate"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-default" @click="closeReturnModal">Hủy</button><button class="btn btn-primary" @click="submitReturn">Gửi yêu cầu</button></div>
    </div>
  </div>

  <div v-if="showReturnDetailsModal" class="modal-overlay" @click.self="closeReturnDetailsModal">
    <div class="modal-content fade-in">
      <div class="modal-header"><h3>Chi tiết Trả hàng</h3><button class="close-btn" @click="closeReturnDetailsModal">✕</button></div>
      <div class="modal-body info-view">
        <p><strong>Lý do:</strong> {{ currentReturnDetails.reason }}</p>
        <p><strong>Ngày hẹn:</strong> {{ currentReturnDetails.pickupDate }}</p>
        <div class="alert-box">Yêu cầu đang được xử lý.</div>
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
// Không cần import api vì đang dùng Demo Data
// import api from '../utils/api'; 

const router = useRouter();
const goToHome = () => router.push('/');

// --- DATA STATE ---
const activeTab = ref('all');
const isDropdownOpen = ref(false);
const selectedCategories = ref([]); 
const visibleCount = ref(10); 
const ratedOrderIds = ref([]);
const isLoading = ref(false);
const orders = ref([]); 

// --- RETURN MODAL STATE ---
const showReturnModal = ref(false);
const showReturnDetailsModal = ref(false);
const returnData = reactive({ orderId: null, reason: '', images: [], pickupDate: '' });
const currentReturnDetails = reactive({ reason: '', pickupDate: '' });

// Các tab trạng thái (GIỮ NGUYÊN)
const tabs = [
  { id: 'all', name: 'Tất cả' },
  { id: 'pending', name: 'Chờ xác nhận' },
  { id: 'shipping', name: 'Vận chuyển' }, 
  { id: 'delivering', name: 'Chờ giao hàng' }, 
  { id: 'completed', name: 'Hoàn thành' },
  { id: 'cancelled', name: 'Đã hủy' },
  { id: 'return', name: 'Trả hàng / Hoàn tiền' }
];

// Danh mục (GIỮ NGUYÊN)
const categories = [
  'Xe cộ', 'Đồ điện tử', 'Thú cưng', 'Đồ ăn, Thực phẩm và các loại khác',
  'Tủ lạnh, Máy lạnh, Máy giặt', 'Đồ gia dụng, Nội thất, Cây cảnh',
  'Thời trang, Đồ dùng cá nhân', 'Giải trí, Thể thao, Sở thích',
  'Đồ dùng văn phòng, Công nông nghiệp'
];

// --- HÀM MÔ PHỎNG CẬP NHẬT TRẠNG THÁI (DATABASE UPDATE DEMO) ---
const updateOrderStatus = (order, newStatus) => {
    // 1. In log ra Console (Mô phỏng gọi API PUT/PATCH /orders/{id}/status)
    console.log(`[DB DEMO] Cập nhật đơn hàng #${order.id} (${order.trackingCode}) từ '${order.statusId}' sang '${newStatus}'`);

    // 2. Cập nhật lại đối tượng Order trong Frontend
    order.statusId = newStatus;
    
    // 3. [QUAN TRỌNG] Cập nhật thời gian cuối cùng
    order.lastUpdate = Date.now();
    order.lastUpdateDisplay = formatDateTime(order.lastUpdate);
    
    // 4. Cập nhật lại Label và Text hiển thị
    const statusMap = {
        'pending': { label: 'CHỜ XÁC NHẬN', note: 'Đang chờ người bán xác nhận' },
        'shipping': { label: 'VẬN CHUYỂN', note: 'Đơn hàng đang được vận chuyển kho' },
        'delivering': { label: 'CHỜ GIAO HÀNG', note: 'Shipper đang giao hàng đến bạn' },
        'completed': { label: 'HOÀN THÀNH', note: 'Giao hàng thành công' },
        'cancelled': { label: 'ĐÃ HỦY', note: 'Đơn hàng đã bị hủy' },
        'return': { label: 'TRẢ HÀNG / HOÀN TIỀN', note: 'Đang xử lý yêu cầu trả hàng' }
    };
    if (statusMap[newStatus]) {
        order.statusLabel = statusMap[newStatus].label;
        order.deliveryStatus = statusMap[newStatus].note;
    }
};


// --- [QUAN TRỌNG] HÀM SINH DỮ LIỆU DEMO (5 ĐƠN MỖI TRẠNG THÁI) ---
const generateDemoData = () => {
  const demoList = [];
  const statusList = [
    { id: 'pending', label: 'CHỜ XÁC NHẬN', note: 'Đang chờ người bán xác nhận' },
    { id: 'shipping', label: 'VẬN CHUYỂN', note: 'Đơn hàng đang được vận chuyển' },
    { id: 'delivering', label: 'CHỜ GIAO HÀNG', note: 'Shipper đang giao hàng đến bạn' },
    { id: 'completed', label: 'HOÀN THÀNH', note: 'Giao hàng thành công' },
    { id: 'cancelled', label: 'ĐÃ HỦY', note: 'Đơn hàng đã bị hủy' },
    { id: 'return', label: 'TRẢ HÀNG / HOÀN TIỀN', note: 'Đang xử lý trả hàng' }
  ];

  // Sản phẩm mẫu (Dùng ID 1-5 để test chuyển trang)
  const products = [
    { id: 1, name: 'Đắc Nhân Tâm - Dale Carnegie', price: 85000, img: 'https://salt.tikicdn.com/cache/w1200/ts/product/2e/26/89/3b9c73950f555b76e10086d4e5f4124e.jpg', cat: 'Sách' },
    { id: 2, name: 'Điện thoại iPhone 15 Pro Max', price: 34990000, img: 'https://cdn.tgddvn/Products/Images/42/305658/iphone-15-pro-max-blue-titan-1-750x500.jpg', cat: 'Đồ điện tử' },
    { id: 3, name: 'Áo Thun Nam Cotton', price: 150000, img: 'https://product.hstatic.net/1000306633/product/ao_thun_nam_basic_a79f5_753c83058928422797e8893967527636_master.jpg', cat: 'Thời trang' },
    { id: 4, name: 'Tai nghe Bluetooth AirPods', price: 2500000, img: 'https://cdn.tgdd.vn/Products/Images/54/236016/bluetooth-airpods-2-apple-mv7n2-imei-ava-600x600.jpg', cat: 'Đồ điện tử' },
    { id: 5, name: 'Nồi chiên không dầu', price: 1200000, img: 'https://cdn.tgdd.vn/Products/Images/1944/249870/noi-chien-khong-dau-ava-af40lht-4-lit-1-600x600.jpg', cat: 'Đồ gia dụng' }
  ];

  let idCounter = 100;
  let prodIndex = 0;
  let now = new Date();
  
  // Vòng lặp tạo 5 đơn cho mỗi trạng thái
  statusList.forEach(status => {
    for (let i = 1; i <= 5; i++) {
      const randomProd = products[prodIndex % products.length];
      idCounter++;
      prodIndex++;
      
      // Giảm dần ngày đặt để đơn có vẻ là đơn cũ
      let orderDate = new Date(now.getTime());
      orderDate.setDate(orderDate.getDate() - (idCounter % 10)); 
      
      // [QUAN TRỌNG] Gán lastUpdate = orderDate khi khởi tạo (trạng thái pending)
      let lastUpdate = orderDate.getTime();
      
      // Nếu không phải pending, giả định nó đã được cập nhật gần đây hơn
      if (status.id !== 'pending') {
          lastUpdate = now.getTime() - (idCounter * 10000); // Gần thời điểm hiện tại hơn
      }
      
      demoList.push({
        id: idCounter, 
        statusId: status.id, 
        statusLabel: status.label,
        deliveryStatus: status.note,
        
        shopName: `Shop Bán Lẻ ${randomProd.cat}`,
        sellerId: 7, 
        sellerName: 'VietMarket',
        sellerAvatar: 'https://via.placeholder.com/50?text=Shop',
        
        productId: randomProd.id, 
        productName: randomProd.name,
        variant: 'Tiêu chuẩn',
        category: randomProd.cat,
        image: randomProd.img,
        
        trackingCode: `ORD-${idCounter}-${Math.random().toString(36).substring(2, 5).toUpperCase()}`,
        orderDate: orderDate.getTime(), 
        orderDateDisplay: formatDate(orderDate), 
        
        lastUpdate: lastUpdate, // Dữ liệu sắp xếp
        lastUpdateDisplay: formatDateTime(lastUpdate), // Hiển thị chi tiết
        
        price: formatCurrency(randomProd.price),
        totalPrice: formatCurrency(randomProd.price + 30000)
      });
    }
  });
  
  return demoList;
};

// --- HÀM LOAD DỮ LIỆU ---
const fetchOrders = async () => {
  isLoading.value = true;
  setTimeout(() => {
    orders.value = generateDemoData();
    isLoading.value = false;
  }, 500);
};

// --- CÁC HÀM XỬ LÝ HÀNH ĐỘNG (ACTION) ---
const cancelOrder = (order) => {
  if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')) {
    updateOrderStatus(order, 'cancelled');
    alert('Đã hủy đơn hàng thành công!');
  }
};

const confirmReceived = (order) => {
  if (confirm('Bạn xác nhận đã nhận được hàng?')) {
    updateOrderStatus(order, 'completed');
    alert('Cảm ơn bạn đã mua hàng!');
  }
};

const openReturnModal = (order) => { returnData.orderId = order.id; returnData.reason = ''; returnData.pickupDate = ''; showReturnModal.value = true; };
const closeReturnModal = () => showReturnModal.value = false;
const handleFileChange = (e) => { returnData.images = Array.from(e.target.files); };

const submitReturn = () => {
  if (!returnData.reason || !returnData.pickupDate) { alert('Vui lòng điền đủ thông tin'); return; }
  const found = orders.value.find(o => o.id === returnData.orderId);
  if (found) {
    updateOrderStatus(found, 'return');
    localStorage.setItem(`return_${found.id}`, JSON.stringify({ reason: returnData.reason, date: returnData.pickupDate }));
  }
  alert('Gửi yêu cầu thành công!'); closeReturnModal();
};

const viewReturnDetails = (order) => {
  const data = JSON.parse(localStorage.getItem(`return_${order.id}`) || '{}');
  currentReturnDetails.reason = data.reason || 'Sản phẩm lỗi';
  currentReturnDetails.pickupDate = data.date || 'Chưa rõ';
  showReturnDetailsModal.value = true;
};
const closeReturnDetailsModal = () => showReturnDetailsModal.value = false;


// --- CÁC HÀM ĐIỀU HƯỚNG ---
const visitShop = (order) => router.push(`/seller/${order.sellerId}`);
const buyAgain = (order) => router.push(`/product/${order.productId}`);
const contactSeller = (order) => router.push({ path: '/chat', query: { sellerId: order.sellerId, productName: order.productName } });

// --- HELPER FUNCTIONS ---
function formatCurrency(val) { return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val); }
function formatDate(date) { 
  return new Date(date).toLocaleDateString('vi-VN'); 
}
function formatDateTime(timestamp) {
    const date = new Date(timestamp);
    const time = date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    const day = date.toLocaleDateString('vi-VN');
    return `${time} ${day}`;
}
const getFullImageUrl = (path) => {
  if (!path) return 'https://via.placeholder.com/150';
  if (path.startsWith('http')) return path;
  return `http://localhost:8000/storage/${path}`;
};
const handleImageError = (e) => { e.target.src = "https://via.placeholder.com/150?text=No+Image"; };
const handleAvatarError = (e) => { e.target.src = "https://via.placeholder.com/50?text=User"; };

// --- LOGIC VÒNG ĐỜI ---
onMounted(() => {
  fetchOrders();
  const storedRatedIds = localStorage.getItem('rated_order_ids');
  if (storedRatedIds) ratedOrderIds.value = JSON.parse(storedRatedIds);
});
const checkIfRated = (orderId) => ratedOrderIds.value.includes(orderId);

// --- MODAL ĐÁNH GIÁ ---
const showRatingModal = ref(false);
const ratingData = reactive({ order: null, score: 5, comment: '' });
const openRatingModal = (order) => { ratingData.order = order; ratingData.score = 5; ratingData.comment = ''; showRatingModal.value = true; };
const closeRatingModal = () => showRatingModal.value = false;

const submitRating = async () => {
  if (!ratingData.order) return;
  
  // 1. Ghi nhận đơn hàng đã được đánh giá
  ratedOrderIds.value.push(ratingData.order.id);
  localStorage.setItem('rated_order_ids', JSON.stringify(ratedOrderIds.value));
  
  // 2. Lưu chi tiết đánh giá vào Local Storage
  const productId = ratingData.order.productId; 
  const storageKey = `product_reviews_${productId}`;
  
  const newReview = {
      id: Date.now(),
      userName: 'Bạn (Demo)', 
      userAvatar: '/avatar.jpg',
      rating: ratingData.score,
      comment: ratingData.comment || 'Tuyệt vời!',
      date: new Date().toLocaleDateString('vi-VN'),
      productName: ratingData.order.productName,
      variant: ratingData.order.variant
  };
  
  const existingReviews = JSON.parse(localStorage.getItem(storageKey) || '[]');
  existingReviews.unshift(newReview);
  localStorage.setItem(storageKey, JSON.stringify(existingReviews));

  alert('Đánh giá thành công! Đang chuyển đến trang chi tiết sản phẩm...'); 
  closeRatingModal();
  
  // 3. Chuyển hướng về trang sản phẩm
  router.push(`/product/${productId}`);
};

const getRatingText = (score) => ['Tệ', 'Không hài lòng', 'Bình thường', 'Hài lòng', 'Tuyệt vời'][score - 1] || '';

// --- FILTER ---
const allFilteredOrders = computed(() => {
  return orders.value.filter(order => {
    // Lọc theo tab và danh mục
    const matchTab = activeTab.value === 'all' || order.statusId === activeTab.value;
    const matchCategory = selectedCategories.value.length === 0 || selectedCategories.value.includes(order.category);
    return matchTab && matchCategory;
  }).sort((a, b) => b.lastUpdate - a.lastUpdate); // [QUAN TRỌNG] Sắp xếp theo lastUpdate (Mới nhất lên đầu)
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