<template>
  <div class="product-detail-page">
    <Header />
    <main class="container">
      <nav class="breadcrumbs">
        <router-link to="/">Trang chủ</router-link> / 
        <router-link to="/products">Sản phẩm</router-link> / 
        <strong>{{ product?.name || 'Đang tải...' }}</strong>
      </nav>

      <div class="product-main-card">
        <section class="product-gallery">
          <div class="main-image-wrapper">
            <button class="gallery-nav prev" @click="prevImage">&lt;</button>
            <button class="gallery-nav next" @click="nextImage">&gt;</button>
            <div class="image-actions">
              <button class="icon-btn" title="Yêu thích">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
              </button>
            </div>
            <img :src="currentImage" :alt="product?.name" class="main-image" @error="handleImageError">
          </div>
          <div class="thumbnail-list-wrapper">
            <div class="thumbnail-list">
              <img v-for="(image, index) in product?.images" :key="index" :src="image" alt="Thumbnail" :class="{ active: index === currentImageIndex }" @click="selectImage(index)" @error="handleImageError">
            </div>
          </div>
        </section>

        <section class="product-info">
          <h1>{{ product?.name }}</h1>

          <div class="price-section">
            <span class="price">{{ product?.priceDisplay }}</span>
            <span class="tag" v-for="tag in product?.tags" :key="tag">{{ tag }}</span>
          </div>

          <div class="description-preview">
            {{ product?.description }}
          </div>

          <div class="meta-info-list">
            <div class="meta-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              <span>Khu vực: <strong>{{ product?.location }}</strong></span>
            </div>
            <div class="meta-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              <span>Đăng lúc: <strong>{{ product?.lastUpdated }}</strong></span>
            </div>
          </div>



          <!-- Status Banner for pending/rejected -->
          <div v-if="product?.status === 'pending'" class="status-banner status-pending">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>Sản phẩm đang chờ xét duyệt</span>
          </div>
          <div v-if="product?.status === 'rejected'" class="status-banner status-rejected">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <span>Sản phẩm đã bị từ chối</span>
          </div>

          <div class="action-buttons">
            <button 
              class="btn-chat" 
              @click="handleChat"
              :disabled="!isProductAvailable"
              :class="{ 'btn-disabled': !isProductAvailable }"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
              Chat ngay
            </button>
            
            <button 
              class="btn-add-cart" 
              @click="handleAddToCart"
              :disabled="!isProductAvailable"
              :class="{ 'btn-disabled': !isProductAvailable }"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
              Thêm vào giỏ
            </button>
            
            <button 
              class="btn-buy-now" 
              @click="handleBuyNow"
              :disabled="!isProductAvailable"
              :class="{ 'btn-disabled': !isProductAvailable }"
            >
              Mua ngay
            </button>
          </div>

          <div class="seller-info">
            <div v-if="!product?.seller.avatar || product?.seller.avatar.startsWith('data:')" class="seller-avatar-letter" @click="goToSellerProfile">
              {{ product?.seller.name ? product.seller.name.charAt(0).toUpperCase() : 'S' }}
            </div>
            <img v-else :src="product?.seller.avatar" class="seller-avatar" @click="goToSellerProfile" @error="handleUserAvatarError">
            <div class="seller-details">
              <strong @click="goToSellerProfile" style="cursor: pointer">{{ product?.seller.name }}</strong>
            </div>
            <button class="btn-view-profile" @click="goToSellerProfile">Xem trang</button>
          </div>
        </section>
      </div>

      <div class="product-info-layout">
        <div class="bottom-left-column">
          <section class="bottom-card" id="description">
            <h2>Mô tả chi tiết</h2>
            <p>{{ product?.detailedDescription }}</p>
          </section>
          
          <section class="bottom-card" id="specs">
            <h2>Thông tin chi tiết</h2>
            <ul class="specs-list">
              <li v-for="spec in product?.specs" :key="spec.label">
                <span class="spec-label">{{ spec.label }}</span>
                <span class="spec-value">{{ spec.value }}</span>
              </li>
            </ul>
          </section>
        </div>
        
        <div class="bottom-right-column">
          <CommentSection :reviews="product?.reviews" :stats="product?.reviewStats" />
        </div>
      </div>

      <section class="related-listings-card" v-if="visibleSellerListings.length > 0">
        <h2>Tin khác của {{ product?.seller.name }}</h2>
        <div class="horizontal-product-list">
          <ProductCard v-for="item in visibleSellerListings" :key="item.id" :product="item" />
        </div>
      </section>

      <section class="related-listings-card" v-if="visibleSimilarListings.length > 0">
        <h2>Tin đăng tương tự</h2>
        <div class="grid-product-list">
          <ProductCard v-for="item in visibleSimilarListings" :key="item.id" :product="item" />
        </div>
      </section>

    </main>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../utils/api';
import { getImageUrl } from '../utils/imageUrl';
import { useToast } from '../utils/useToast';
import Header from "../components/layout/SearchHeader.vue";
import Footer from "../components/layout/AppFooter.vue";
import CommentSection from '../components/CommentSection.vue';
import ProductCard from '../components/product/ProductCardSimple.vue';
import { useCart } from '../stores/cart';
import { useAuth } from '../utils/useAuth';

const route = useRoute();
const router = useRouter();
const product = ref(null);
const loading = ref(true);

const { addToCart } = useCart();
const { isLoggedIn } = useAuth();
const { showSuccess, showError } = useToast();

// State & Logic "Xem thêm"
const sellerListings = ref([]);
const similarListings = ref([]);
const visibleSellerListings = computed(() => sellerListings.value.slice(0, 4));
const visibleSimilarListings = computed(() => similarListings.value.slice(0, 8));

// Check if product is available for purchase (not pending/rejected)
const isProductAvailable = computed(() => {
  if (!product.value) return false;
  return product.value.status === 'active';
});

// Fallback images - Data URI để tránh lỗi network và infinite loop
const FALLBACK_IMAGE = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="600" height="400" fill="%23eee"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23999" font-size="20" text-anchor="middle" dy=".3em"%3ENo Image%3C/text%3E%3C/svg%3E';
const FALLBACK_AVATAR = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="%23ddd"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23888" font-size="12" text-anchor="middle" dy=".3em"%3EUser%3C/text%3E%3C/svg%3E';

// Logic Ảnh
const currentImageIndex = ref(0);
const currentImage = computed(() => {
  if (product.value?.images?.length > 0) return product.value.images[currentImageIndex.value];
  return FALLBACK_IMAGE;
});
const nextImage = () => { if (product.value) currentImageIndex.value = (currentImageIndex.value + 1) % product.value.images.length; };
const prevImage = () => { if (product.value) currentImageIndex.value = (currentImageIndex.value - 1 + product.value.images.length) % product.value.images.length; };
const selectImage = (index) => { currentImageIndex.value = index; };

// Helper Error Images - Prevent infinite loop by checking if already using fallback
const handleImageError = (e) => { 
  if (!e.target.src.startsWith('data:')) {
    e.target.src = FALLBACK_IMAGE;
  }
};
const handleUserAvatarError = (e) => { 
  if (!e.target.src.startsWith('data:')) {
    e.target.src = FALLBACK_AVATAR;
  }
};

// === HÀM XỬ LÝ (LOGIC QUAN TRỌNG) ===

// 1. Chat
const handleChat = () => {
  if (!product.value) return;
  if (!isProductAvailable.value) {
    showError('Sản phẩm chưa được duyệt, không thể chat');
    return;
  }
  if (!isLoggedIn.value) {
    if(confirm('Bạn cần đăng nhập để chat với người bán. Đăng nhập ngay?')) {
      router.push({ path: '/login', query: { redirect: route.fullPath } });
    }
    return;
  }
  router.push({
    path: '/chat',
    query: { 
      sellerId: product.value.seller.id, 
      sellerName: product.value.seller.name,
      sellerAvatar: product.value.seller.avatar,
      productName: product.value.name,
      productImage: product.value.image
    }
  });
};

// 2. Thêm vào giỏ hàng (ĐÃ SỬA: Đảm bảo lấy đúng variant_id)
const handleAddToCart = async () => {
  if (!product.value) return;
  
  if (!isProductAvailable.value) {
    showError('Sản phẩm chưa được duyệt, không thể thêm vào giỏ');
    return;
  }
  
  if (!isLoggedIn.value) {
    if(confirm('Bạn cần đăng nhập để thêm vào giỏ hàng. Đăng nhập ngay?')) {
      router.push({ path: '/login', query: { redirect: route.fullPath } });
    }
    return;
  }
  
  // Tạo object gửi sang Store
  // Store sẽ dùng 'variant_id' để gọi API.
  // Chúng ta đảm bảo product.value.variant_id là chính xác từ hàm mapProductFromApi
  const cartItem = {
    id: product.value.id,
    variant_id: product.value.variant_id, // Quan trọng: Đây phải là ID của Variant, không phải Product (nếu có variant)
    name: product.value.name,
    price: product.value.priceNumber,     // Gửi giá dạng số
    image: product.value.image,
    seller: product.value.seller,
    quantity: 1
  };

  console.log("Adding to cart:", cartItem); // Debug log để kiểm tra

  const result = await addToCart(cartItem);
  if (result.success) {
    showSuccess(result.message);
  } else {
    showError(result.message);
  }
};

// 3. Mua ngay
const handleBuyNow = async () => {
  if (!product.value) return;

  if (!isProductAvailable.value) {
    showError('Sản phẩm chưa được duyệt, không thể mua');
    return;
  }

  if (!isLoggedIn.value) {
    showError('Bạn cần đăng nhập để mua hàng');
    router.push({ path: '/login', query: { redirect: route.fullPath } });
    return;
  }

  const cartItem = {
    id: product.value.id,
    variant_id: product.value.variant_id,
    name: product.value.name,
    price: product.value.priceNumber,
    image: product.value.image,
    seller: product.value.seller,
    quantity: 1
  };

  const result = await addToCart(cartItem);
  if (result.success) {
    router.push('/cart');
  } else {
    showError(result.message);
  }
};

// 4. Chuyển trang người bán
const goToSellerProfile = () => {
  if (product.value?.seller) {
    router.push({ name: 'SellerProfile', params: { id: product.value.seller.id } });
  }
};

// === HELPER FUNCTIONS ===
const formatPrice = (price) => {
  if (!price) return '0';
  return new Intl.NumberFormat('vi-VN').format(price);
};

// === MAP DATA (QUAN TRỌNG: LẤY ĐÚNG ID) ===
const mapProductFromApi = (data) => {
  // Tìm variant đầu tiên
  let firstVariant = {};
  if (data.variants && data.variants.length > 0) {
    firstVariant = data.variants[0];
  }

  // Xử lý hình ảnh - API trả về data.images[] với property 'url'
  const images = [];
  
  // Ưu tiên 1: Lấy từ mảng images ở root (cấu trúc mới từ ProductResource)
  if (data.images && data.images.length > 0) {
    data.images.forEach(img => {
      const url = typeof img === 'string' ? img : (img.url || img.image_url);
      if (url) images.push(getImageUrl(url));
    });
  }
  
  // Ưu tiên 2: Lấy từ thumbnail
  if (images.length === 0 && data.thumbnail) {
    images.push(getImageUrl(data.thumbnail));
  }
  
  // Fallback: placeholder
  if (images.length === 0) images.push(FALLBACK_IMAGE);

  // Xử lý giá tiền
  const rawPrice = firstVariant.price || data.price_range?.min || data.price || 0;
  const displayPrice = formatPrice(rawPrice) + ' đ';

  // QUAN TRỌNG: Xác định variant_id
  // Nếu có variant, dùng ID variant. Nếu không (sản phẩm đơn), dùng ID sản phẩm.
  const finalVariantId = firstVariant.id || data.id;

  return {
    id: data.id,                  // Product ID
    variant_id: finalVariantId,   // Variant ID (Dùng để thêm giỏ hàng)
    name: data.name,
    description: data.description,
    status: data.status || 'active', // Trạng thái sản phẩm
    priceDisplay: displayPrice,   // Dùng hiển thị
    priceNumber: rawPrice,        // Dùng tính toán
    image: images[0],
    tags: data.categories?.map(c => c.name) || [],
    location: data.location || 'Toàn quốc',
    lastUpdated: new Date(data.updated_at || new Date()).toLocaleDateString('vi-VN'),
    images: images,
    seller: {
      id: data.seller?.id || 0,
      name: data.seller?.name || data.seller?.full_name || data.seller?.username || 'Người bán',
      avatar: getImageUrl(data.seller?.avatar || data.seller?.avatar_url, FALLBACK_AVATAR)
    },
    detailedDescription: data.description || 'Chưa có mô tả chi tiết.',
    specs: [
      { label: 'Tình trạng:', value: data.status === 'active' ? 'Còn hàng' : 'Hết hàng' },
      { label: 'Màu sắc:', value: firstVariant.color || 'N/A' },
      { label: 'Kích cỡ:', value: firstVariant.size || 'N/A' }
    ],
    comments: []
  };
};

const mapProductCard = (item) => ({
  id: item.id,
  title: item.name,
  price: formatPrice(item.price_range?.min || item.variants?.[0]?.price) + ' đ',
  location: item.location || 'Toàn quốc',
  imageUrl: getImageUrl(item.image || item.variants?.[0]?.images?.[0]?.image_url)
});

// Fetch Data
const fetchProductDetail = async () => {
  loading.value = true;
  product.value = null; 
  
  const productId = route.params.id;
  try {
    const response = await api.get(`/products/${productId}`);
    const data = response.data.data || response.data;
    
    // Map dữ liệu product chính
    product.value = mapProductFromApi(data);

    // Fetch Reviews & Stats
    try {
        const [reviewsRes, statsRes] = await Promise.all([
            api.get(`/products/${productId}/reviews`),
            api.get(`/products/${productId}/reviews/stats`)
        ]);
        
        if (reviewsRes.data.success) {
            product.value.reviews = reviewsRes.data.data;
        } else {
            product.value.reviews = [];
        }

        if (statsRes.data.success) {
            product.value.reviewStats = {
                average: statsRes.data.average,
                details: statsRes.data.details
            };
        }
    } catch (e) {
        console.warn("Reviews load failed", e);
        product.value.reviews = [];
        product.value.reviewStats = { average: 0, details: {} };
    }

    // Fetch Similar Products
    try {
      const similarRes = await api.get(`/products/${productId}/similar`);
      if (similarRes.data.success) {
          similarListings.value = similarRes.data.data.map(item => ({
              id: item.id,
              title: item.title,
              price: formatPrice(item.price) + ' đ',
              imageUrl: getImageUrl(item.image),
              location: item.location,
              sellerId: item.seller_id,
              userAvatar: getImageUrl(item.seller_avatar) // Quan trọng: Đã thêm API seller_avatar
          }));
      }
    } catch (e) {}
    
    // Fetch Seller's Other Listings
    if (data.seller?.id) {
       try {
         const sellerRes = await api.get(`/products/seller/${data.seller.id}`);
         // Filter bỏ sản phẩm hiện tại
         const sData = (sellerRes.data && sellerRes.data.data) ? sellerRes.data.data : [];
         sellerListings.value = sData
            .filter(p => p.id != productId)
            .map(item => ({
                 id: item.id,
                 title: item.name, // Resource trả về 'name'
                 price: formatPrice(item.price_range?.min || (item.variants && item.variants.length > 0 ? item.variants[0].price : 0)) + ' đ',
                 imageUrl: getImageUrl(item.thumbnail || (item.images && item.images.length > 0 ? item.images[0].url : null)), 
                 location: item.location || 'Toàn quốc',
                 sellerId: data.seller.id,
                 userAvatar: product.value.seller.avatar 
            }));
       } catch (e) {}
    }

  } catch (error) { console.error(error); }
  loading.value = false;
};

// Watch route thay đổi để load lại trang
watch(() => route.params.id, (newId) => { 
  if (newId) { 
    fetchProductDetail(); 
    window.scrollTo({top: 0}); 
  } 
});

onMounted(() => { fetchProductDetail(); });
</script>

<style scoped>
/* CSS GIỮ NGUYÊN */
.product-detail-page { background-color: #f8f9fa; padding-bottom: 40px; }
.container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }

/* Breadcrumbs */
.breadcrumbs { padding: 15px 0; font-size: 0.9rem; color: #666; }
.breadcrumbs a { color: #0d6efd; text-decoration: none; }
.breadcrumbs strong { color: #333; }

/* Main Card */
.product-main-card { display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
@media (max-width: 992px) { .product-main-card { grid-template-columns: 1fr; } }

/* Gallery */
.main-image-wrapper { position: relative; aspect-ratio: 4/3; border-radius: 8px; overflow: hidden; border: 1px solid #f0f0f0; }
.main-image { width: 100%; height: 100%; object-fit: contain; background: #fafafa; }
.image-actions { position: absolute; top: 10px; right: 10px; z-index: 2; }
.icon-btn { width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.9); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #555; transition: all 0.2s; }
.icon-btn:hover { background: #fff; color: #dc3545; }
.gallery-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; background: rgba(255,255,255,0.8); border: none; border-radius: 50%; cursor: pointer; font-size: 1.2rem; }
.gallery-nav.prev { left: 10px; } .gallery-nav.next { right: 10px; }
.thumbnail-list { display: flex; gap: 10px; margin-top: 15px; overflow-x: auto; padding-bottom: 5px; }
.thumbnail-list img { width: 70px; height: 70px; border-radius: 6px; border: 2px solid transparent; cursor: pointer; object-fit: cover; }
.thumbnail-list img.active { border-color: #0d6efd; }

/* Info Section */
.product-info h1 { font-size: 1.8rem; margin: 0 0 10px 0; color: #222; }
.price-section { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
.price { font-size: 1.8rem; color: #dc3545; font-weight: 700; }
.tag { background: #f0f2f5; color: #555; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; }

.meta-row { display: flex; align-items: center; gap: 10px; font-size: 0.9rem; color: #666; margin-bottom: 15px; }
.rating-box { display: flex; align-items: center; gap: 4px; color: #ffc107; font-weight: bold; }
.description-preview { color: #555; line-height: 1.5; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

.meta-info-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; font-size: 0.95rem; color: #444; }
.meta-item { display: flex; align-items: center; gap: 8px; }

/* Action Buttons Grid */
.action-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
.btn-chat, .btn-add-cart, .btn-buy-now { height: 44px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 1rem; transition: opacity 0.2s; }

/* Chat: Xanh dương */
.btn-chat { background: #0d6efd; color: white; grid-column: 1 / 2; }
/* Thêm giỏ hàng: Viền cam / Nền nhạt */
.btn-add-cart { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; grid-column: 2 / 3; }
.btn-add-cart:hover { background: #ffeeba; }
/* Mua ngay: Đỏ - Full dòng dưới */
.btn-buy-now { background: #dc3545; color: white; grid-column: 1 / 3; }

.btn-chat:hover, .btn-buy-now:hover { opacity: 0.9; }

/* Status Banner */
.status-banner {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-weight: 500;
  font-size: 0.95rem;
}
.status-banner.status-pending {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fcd34d;
}
.status-banner.status-rejected {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

/* Disabled Buttons */
.btn-disabled {
  opacity: 0.5 !important;
  cursor: not-allowed !important;
  pointer-events: none;
}
.btn-disabled:hover {
  opacity: 0.5 !important;
}

/* Seller Info */
.seller-info { display: flex; align-items: center; gap: 12px; padding: 16px; background: #f8f9fa; border-radius: 8px; }
.seller-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; cursor: pointer; }
.seller-avatar-letter { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 20px; cursor: pointer; }
.seller-details { flex: 1; }
.seller-details strong { display: block; color: #333; }
.active-status { font-size: 0.8rem; color: #28a745; }
.btn-view-profile { padding: 6px 12px; border: 1px solid #ced4da; background: white; border-radius: 20px; font-size: 0.85rem; cursor: pointer; }

/* Price Range Chart */
.price-range { background: #fff; border: 1px solid #eee; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
.range-bar-container { position: relative; margin-top: 30px; height: 30px; }
.range-bar-bg { height: 6px; background: #e9ecef; border-radius: 3px; position: relative; top: 10px; }
.range-bar-active { position: absolute; height: 100%; background: #28a745; border-radius: 3px; }
.range-tooltip { position: absolute; top: -25px; transform: translateX(-50%); background: #333; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; white-space: nowrap; }
.range-labels { display: flex; justify-content: space-between; margin-top: 15px; font-size: 0.8rem; color: #666; }

/* Bottom Layout */
.product-info-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-top: 24px; }
@media (max-width: 768px) { .product-info-layout { grid-template-columns: 1fr; } }
.bottom-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 24px; }
.bottom-card h2 { font-size: 1.25rem; margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 16px; }
.specs-list li { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f8f9fa; }
.spec-label { color: #666; } .spec-value { font-weight: 600; color: #333; }

/* Related Items */
.related-listings-card { background: white; padding: 20px; border-radius: 12px; margin-top: 24px; }
.related-listings-card h2 { font-size: 1.25rem; margin-bottom: 16px; }
.horizontal-product-list { display: flex; gap: 16px; overflow-x: auto; padding-bottom: 10px; }
.horizontal-product-list > * { flex: 0 0 200px; }
.grid-product-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
</style>