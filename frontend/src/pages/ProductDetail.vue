<template>
  <div class="product-detail-page">
    <Header />
    <main class="container">

      <!-- Breadcrumbs -->
      <nav class="breadcrumbs">
        <router-link to="/">Trang chủ</router-link> / 
        <span v-if="product?.categories?.[0]">{{ product.categories[0].name }} /</span>
        <strong>{{ product?.name || 'Đang tải...' }}</strong>
      </nav>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div> Đang tải thông tin sản phẩm...
      </div>

      <!-- Main Content -->
      <div v-else-if="product" class="product-main-card">
        
        <!-- Left: Gallery -->
        <section class="product-gallery">
          <div class="main-image-wrapper">
            <button class="gallery-nav prev" @click="prevImage" v-if="product.images.length > 1">&lt;</button>
            <button class="gallery-nav next" @click="nextImage" v-if="product.images.length > 1">&gt;</button>
            
            <img :src="currentImage" :alt="product.name" class="main-image">
          </div>
          
          <div class="thumbnail-list-wrapper" v-if="product.images.length > 1">
            <div class="thumbnail-list">
              <img 
                v-for="(img, index) in product.images" 
                :key="index" 
                :src="img" 
                :class="{ active: index === currentImageIndex }" 
                @click="selectImage(index)"
              >
            </div>
          </div>
        </section>

        <!-- Right: Info -->
        <section class="product-info">
          <h1>{{ product?.name }}</h1>
          
          <div class="rating-summary-mini" v-if="reviews.length > 0">
            <span class="score">{{ averageRating }}</span>
            <div class="stars">
              <span v-for="n in 5" :key="n" class="star">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" :fill="n <= Math.round(averageRating) ? '#ffc107' : '#ddd'" stroke="none"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
              </span>
            </div>
            <span class="count">({{ reviews.length }} đánh giá)</span>
            <span class="separator">|</span>
            <span class="sold">Đã bán 1.2k</span>
          </div>

          <p class="description">{{ product?.description }}</p>
          <div class="price-section">
            <span class="price">{{ product.price }}</span>
            <span class="tag" v-for="cat in product.categories" :key="cat.id">{{ cat.name }}</span>
          </div>

          <div class="meta-info">
            <div class="meta-item">
              <font-awesome-icon icon="map-marker-alt" />
              <span>Khu vực: <strong>Toàn quốc</strong></span>
            </div>
            <div class="meta-item">
              <font-awesome-icon icon="clock" />
              <span>Ngày đăng: <strong>{{ formatDate(product.created_at) }}</strong></span>
            </div>
          </div>

          <!-- Market Price (Giả lập hiển thị khoảng giá) -->
          <div class="price-range">
            <div class="price-range-header">
              <strong>Khoảng giá tham khảo</strong>
            </div>
            <span>Dựa trên tin đăng tương tự</span>
            <div class="range-bar-container">
              <div class="range-bar-bg">
                <div class="range-bar-active" style="left: 20%; right: 20%"></div>
              </div>
              <span class="range-label-min">{{ product.priceMin }}</span>
              <span class="range-label-max">{{ product.priceMax }}</span>
            </div>
          </div>

          <!-- Buttons -->
          <div class="action-buttons">
            <button class="btn-chat" @click="handleChat">
              <font-awesome-icon icon="comment-dots" /> Chat ngay
            </button>
            <button class="btn-add-cart">Yêu thích</button> <!-- Đổi tạm thành yêu thích -->
            <button class="btn-buy-now">Mua ngay</button>
          </div>

          <!-- Seller Info -->
          <div class="seller-info" v-if="product.seller">
            <img :src="product.seller.avatar" class="seller-avatar">
            <div class="seller-details">
              <strong @click="goToSellerProfile" class="pointer">{{ product.seller.name }}</strong>
              <div class="sub-text">Hoạt động 5 phút trước</div>
            </div>
            <div class="seller-stats">
              <div class="stat-item"><strong>{{ product.seller.listingCount || 5 }}</strong> <span>Tin bán</span></div>
            </div>
            <button class="btn-view-profile" @click="goToSellerProfile">Xem trang</button>
          </div>

        </section>
      </div>

      <div v-else class="not-found">
        <h3>Không tìm thấy sản phẩm này</h3>
        <button @click="router.push('/products')">Về danh sách</button>
      </div>

      <!-- Details & Comments -->
      <div class="product-info-layout" v-if="product">
        <div class="bottom-left-column">
          <section class="bottom-card" id="specs">
            <h2>Thông tin chi tiết</h2>
            <ul class="specs-list">
              <li v-for="(val, key) in product.specs" :key="key">
                <span class="spec-label">{{ key }}</span>
                <span class="spec-value">{{ val }}</span>
              </li>
              <li>
                <span class="spec-label">Tình trạng</span>
                <span class="spec-value">Đã sử dụng</span>
              </li>
            </ul>
          </section>
          
          <section class="bottom-card">
            <h2>Mô tả đầy đủ</h2>
            <p style="white-space: pre-line;">{{ product.detailedDescription }}</p>
          </section>
        </div>
        
        <div class="bottom-right-column">
          <section class="bottom-card review-section">
            <h2>Đánh giá sản phẩm</h2>
            
            <div class="rating-overview">
              <div class="rating-score-box">
                <span class="big-score">{{ averageRating }}</span>
                <span class="total-stars">/ 5</span>
              </div>
              <div class="stars-large">
                <span v-for="n in 5" :key="n" class="star">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" :fill="n <= Math.round(averageRating) ? '#ffc107' : '#ddd'" stroke="none"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                </span>
              </div>
              <div class="total-count">{{ reviews.length }} nhận xét</div>
            </div>

            <div class="review-list">
              <div v-if="reviews.length === 0" class="empty-review">
                Chưa có đánh giá nào cho sản phẩm này.
              </div>

              <div v-for="review in reviews" :key="review.id" class="review-item">
                <img :src="review.userAvatar" class="user-avatar" />
                <div class="review-content">
                  <div class="review-header">
                    <strong class="user-name">{{ review.userName }}</strong>
                    <span class="review-date">{{ review.date }}</span>
                  </div>
                  
                  <div class="user-rating-stars">
                    <span v-for="n in 5" :key="n" class="star-small">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" :fill="n <= review.rating ? '#ffc107' : '#ddd'" stroke="none"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    </span>
                  </div>
                  
                  <div class="variant-info" v-if="review.variant">Phân loại: {{ review.variant }}</div>
                  <p class="comment-text">{{ review.comment }}</p>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>

      <!-- Related Products -->
      <section class="related-listings-card" v-if="similarListings.length > 0">
        <h2>Tin đăng tương tự</h2>
        <div class="grid-product-list">
          <ProductCard
              v-for="item in similarListings"
              :key="item.id"
              :product="item"
          />
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
import Header from "../components/layout/SearchHeader.vue";
import Footer from "../components/layout/AppFooter.vue";
import ProductCard from '../components/product/ProductCardSimple.vue';
import { useCart } from '../stores/cart';

const route = useRoute();
const router = useRouter();

const product = ref(null);
const loading = ref(true);
const { addToCart } = useCart();

// [MỚI] STATE REVIEW & RATING (Thêm vào đây)
const reviews = ref([]);

const averageRating = computed(() => {
  if (reviews.value.length === 0) return 0;
  const total = reviews.value.reduce((sum, r) => sum + r.rating, 0);
  const avg = total / reviews.value.length;
  return avg % 1 === 0 ? avg : avg.toFixed(1);
});

// Các state cũ (Giữ nguyên)
const sellerListings = ref([]);
const similarListings = ref([]);
const currentImageIndex = ref(0);

// --- 1. XỬ LÝ ẢNH ---
const currentImage = computed(() => {
  if (product.value?.images?.length) {
    return product.value.images[currentImageIndex.value];
  }
  return 'https://via.placeholder.com/600x400?text=No+Image';
});

const nextImage = () => {
  if (product.value) currentImageIndex.value = (currentImageIndex.value + 1) % product.value.images.length;
};
const prevImage = () => {
  if (product.value) currentImageIndex.value = (currentImageIndex.value - 1 + product.value.images.length) % product.value.images.length;
// [SỬA] Hàm Chat để chuyển đúng dữ liệu cho trang Chat
const handleChat = () => {
  if (!product.value) return;
  router.push({
    path: '/chat',
    query: {
      sellerId: product.value.seller.id,
      sellerName: product.value.seller.name,
      sellerAvatar: product.value.seller.avatar,
      productName: product.value.name
    }
  });
};

const goToSellerProfile = () => {
  if (product.value && product.value.seller) {
    router.push({
      name: 'SellerProfile', // Tên route phải khớp với router
      params: { id: product.value.seller.id }
    });
  }
};
const selectImage = (i) => currentImageIndex.value = i;

// --- 2. FORMATTERS ---
const formatPrice = (price) => new Intl.NumberFormat('vi-VN').format(price) + ' đ';
const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('vi-VN');
};

const getImageUrl = (url) => {
  if (!url) return 'https://via.placeholder.com/400';
  if (url.startsWith('http')) return url;
  return `http://localhost:8000${url}`; // Fix link ảnh local
};

// --- 3. MAPPING DATA ---
const mapProductDetail = (apiData) => {
  // 1. Logic lấy danh sách ảnh (SỬA LẠI ĐOẠN NÀY)
  let allImages = [];

  // TRƯỜNG HỢP 1: Backend trả về mảng 'images' ở root (Cấu trúc mới ta vừa làm)
  // apiData.images = [{id: 1, url: '...'}, {id: 2, url: '...'}]
  if (apiData.images && Array.isArray(apiData.images) && apiData.images.length > 0) {
    allImages = apiData.images.map(imgObj => getImageUrl(imgObj.url));
  } 
  
  // TRƯỜNG HỢP 2: Fallback (Dự phòng nếu Backend cũ)
  else {
    if (apiData.thumbnail) allImages.push(getImageUrl(apiData.thumbnail));
    if (apiData.variants) {
      apiData.variants.forEach(v => {
        if (v.images && Array.isArray(v.images)) {
          v.images.forEach(img => {
             // Xử lý cả trường hợp img là string hoặc object
             const url = typeof img === 'string' ? img : img.url || img.image_url;
             if(url) allImages.push(getImageUrl(url));
          });
        }
      });
    }
  }

  // Lọc trùng (Unique) và xử lý ảnh lỗi
  allImages = [...new Set(allImages)]; // Xóa link trùng
  if (allImages.length === 0) allImages.push('https://via.placeholder.com/600x400?text=No+Image');

  // ... (Phần lấy giá và specs giữ nguyên) ...
  const minPrice = apiData.price_range?.min || apiData.variants?.[0]?.price || 0;
  const maxPrice = apiData.price_range?.max || minPrice;

  const specs = {};
  if (apiData.variants?.[0]) {
    if (apiData.variants[0].color) specs['Màu sắc'] = apiData.variants[0].color;
    if (apiData.variants[0].size) specs['Kích thước'] = apiData.variants[0].size;
  }

  return {
    id: apiData.id,
    name: apiData.name,
    description: apiData.description,
    detailedDescription: apiData.description,
    price: formatPrice(minPrice),
    priceMin: formatPrice(minPrice),
    priceMax: formatPrice(maxPrice * 1.2),
    
    images: allImages, // <--- Mảng này giờ sẽ chứa đủ 2 ảnh
    
    categories: apiData.categories || [],
    created_at: apiData.created_at,
    specs: specs,
    seller: {
      id: apiData.seller?.id,
      name: apiData.seller?.name || 'Người bán',
      avatar: getImageUrl(apiData.seller?.avatar),
      listingCount: 10
    }
  };
};



const mapProductCard = (item) => ({
  id: item.id,
  title: item.name,
  price: formatPrice(item.price_range?.min || 0),
  location: 'Toàn quốc',
  imageUrl: getImageUrl(item.thumbnail || item.variants?.[0]?.images?.[0])
});

// --- 4. FETCH API ---
const fetchProductDetail = async () => {
  loading.value = true;
  visibleSellerCount.value = 4;
  visibleSimilarCount.value = 8;

  const productId = route.params.id;

  // [MỚI] 1. LẤY ĐÁNH GIÁ TỪ LOCALSTORAGE
  const storageKey = `product_reviews_${productId}`;
  const storedReviews = localStorage.getItem(storageKey);
  if (storedReviews) {
    reviews.value = JSON.parse(storedReviews);
  } else {
    reviews.value = [];
  }

  // [MỚI] 2. GỌI API & XỬ LÝ FALLBACK
  try {
    const id = route.params.id;
    // Gọi API chi tiết
    const res = await api.get(`/products/${id}`);
    product.value = mapProductDetail(res.data.data);

    // Gọi API tương tự (nếu có)
    try {
      const similarResponse = await api.get(`/products/${productId}/similar`);
      if (similarResponse.data.success) {
        similarListings.value = similarResponse.data.data.map(item => ({
          id: item.id,
          title: item.title,
          price: formatPrice(item.price) + ' đ',
          location: item.location || 'TP. HCM',
          imageUrl: getImageUrl(item.image),
          seller: item.seller,
        }));
      }
    } catch (err) {
      console.error('Error fetching similar products:', err);
      similarListings.value = [];
    }

    if (data.seller?.id) {
      try {
        const sellerResponse = await api.get(`/products/seller/${data.seller.id}`);
        const sellerData = sellerResponse.data.data || sellerResponse.data;
        const sellerProducts = Array.isArray(sellerData) ? sellerData : sellerData.data || [];
        sellerListings.value = sellerProducts
            .filter(p => p.id !== parseInt(productId))
            .map(mapProductCard);
      } catch (err) {
        console.error('Error fetching seller products:', err);
        sellerListings.value = [];
      }
    } catch (e) { console.log('Không tải được SP tương tự'); }

  } catch (error) {
    console.error("Lỗi tải sản phẩm:", error);
    product.value = null;
  } finally {
    loading.value = false;
  }
};

// --- 5. ACTIONS ---
const handleChat = () => {
  if (!product.value?.seller?.id) return;
  router.push({
    path: '/chat',
    query: { partnerId: product.value.seller.id }
  });
};

const goToSellerProfile = () => {
  if (product.value?.seller?.id) {
    router.push(`/seller/${product.value.seller.id}`);
  }
};

// Watch ID đổi thì load lại (khi bấm vào SP tương tự)
watch(() => route.params.id, (newId) => {
  if (newId) {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    fetchProductDetail();
  }
});

onMounted(() => {
  fetchProductDetail();
});
</script>

<style scoped>
/* GIỮ NGUYÊN CSS CŨ CỦA BẠN, CHỈ THÊM MỘT CHÚT CHO LOADING */
.loading-state {
  padding: 50px; text-align: center; color: #666; font-size: 1.2rem;
  display: flex; align-items: center; justify-content: center; gap: 10px;
}
.spinner {
  width: 24px; height: 24px; border: 3px solid #eee; border-top-color: #ffd60a;
  border-radius: 50%; animation: spin 1s infinite linear;
}
@keyframes spin { 100% { transform: rotate(360deg); } }

/* CSS CŨ */
.product-detail-page { background-color: #f4f4f4; padding-bottom: 30px; }
.container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px; }
.breadcrumbs { font-size: 0.9rem; color: #555; padding: 20px 0; }
.breadcrumbs a { text-decoration: none; color: #555; }
.product-main-card { display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
@media (max-width: 992px) { .product-main-card { grid-template-columns: 1fr; } }
.product-gallery { display: flex; flex-direction: column; gap: 15px; }
.main-image-wrapper { position: relative; aspect-ratio: 4 / 3; border-radius: 8px; overflow: hidden; background: #f9f9f9; }
.main-image { width: 100%; height: 100%; object-fit: contain; }
.gallery-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(255,255,255,0.8); cursor: pointer; font-size: 1.5rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.gallery-nav.prev { left: 10px; } .gallery-nav.next { right: 10px; }
.thumbnail-list-wrapper { display: flex; gap: 10px; align-items: center; }
.thumbnail-list { display: flex; gap: 10px; overflow-x: auto; flex-grow: 1; padding-bottom: 5px; }
.thumbnail-list img { width: 70px; height: 50px; object-fit: cover; border-radius: 4px; border: 2px solid transparent; cursor: pointer; }
.thumbnail-list img.active { border-color: #007bff; }
.product-info { display: flex; flex-direction: column; gap: 15px; }
.product-info h1 { font-size: 1.5rem; margin: 0; line-height: 1.3; }
.description { color: #555; margin: 0; font-size: 0.95rem; }
.price-section { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.price { font-size: 1.8rem; font-weight: bold; color: #d70000; }
.tag { background: #f0f0f0; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; color: #555; }
.meta-info { display: flex; flex-direction: column; gap: 8px; padding: 15px 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee; }
.meta-item { display: flex; gap: 10px; color: #555; align-items: center; font-size: 0.95rem; }
.price-range { background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
.price-range-header { font-weight: bold; margin-bottom: 5px; }
.range-bar-container { position: relative; margin-top: 25px; height: 20px; }
.range-bar-bg { height: 6px; background: #e9ecef; border-radius: 3px; position: relative; top: 7px; }
.range-bar-active { position: absolute; height: 100%; background: #28a745; border-radius: 3px; opacity: 0.6; }
.range-label-min { position: absolute; bottom: -20px; left: 0; font-size: 0.8rem; color: #777; }
.range-label-max { position: absolute; bottom: -20px; right: 0; font-size: 0.8rem; color: #777; }
.action-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }
.btn-chat { background: #007bff; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-buy-now { background: #d70000; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer; grid-column: span 2; }
.btn-add-cart { background: #fff; border: 1px solid #ddd; color: #333; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer; }
.seller-info { display: flex; gap: 12px; align-items: center; padding: 15px; border: 1px solid #eee; border-radius: 8px; margin-top: 10px; }
.seller-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
.seller-details { flex-grow: 1; overflow: hidden; }
.seller-details strong { display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sub-text { font-size: 0.8rem; color: #888; }
.seller-stats { display: flex; gap: 15px; font-size: 0.85rem; margin-right: 10px; }
.btn-view-profile { background: white; border: 1px solid #007bff; color: #007bff; padding: 6px 12px; border-radius: 15px; cursor: pointer; font-size: 0.85rem; white-space: nowrap; }
.product-info-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px; }
@media (max-width: 992px) { .product-info-layout { grid-template-columns: 1fr; } }
.bottom-card { background-color: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); margin-bottom: 20px; }
.specs-list { list-style: none; padding: 0; margin: 0; }
.specs-list li { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
.specs-list li:last-child { border-bottom: none; }
.spec-label { color: #666; } .spec-value { font-weight: 500; color: #333; }
.related-listings-card { margin-top: 30px; }
.related-listings-card h2 { font-size: 1.4rem; font-weight: bold; margin-bottom: 20px; }
.grid-product-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
.pointer { cursor: pointer; }
.not-found { text-align: center; padding: 50px; }
.not-found button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
.spec-label { color: #555; } .spec-value { font-weight: 500; }

.related-listings-card { background-color: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); overflow: hidden; margin-top: 30px; }
.related-listings-card h2 { font-size: 1.5rem; font-weight: bold; margin-top: 0; margin-bottom: 20px; }

/* "Tin khác" */
.horizontal-product-list { display: flex; overflow-x: auto; gap: 15px; padding-bottom: 15px; margin: 0 -24px; padding-left: 24px; padding-right: 24px; }
.horizontal-product-list :deep(.product-card) { width: 220px; flex-shrink: 0; }
.horizontal-product-list :deep(a) { flex-shrink: 0; }

/* "Tin tương tự" */
.grid-product-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px; }
.grid-product-list :deep(.product-card) { width: auto; flex-shrink: 1; }

.see-more-btn { display: block; width: 100%; padding: 12px; margin-top: 20px; border: 1px solid #007bff; color: #007bff; background: #fff; border-radius: 6px; font-weight: bold; cursor: pointer; }
.see-more-btn:hover { background-color: #f0f8ff; }

/* --- [MỚI] STYLES CHO PHẦN RATING MỚI (ADDITIONAL CSS) --- */
.rating-summary-mini { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; font-size: 0.95rem; color: #555; }
.rating-summary-mini .score { color: #ffc107; font-weight: 700; border-bottom: 1px solid #ffc107; font-size: 1.1rem; }
.rating-summary-mini .separator { color: #ddd; }

.review-section { padding: 24px; }
.rating-overview { background: #fffbf8; border: 1px solid #f9ede5; padding: 20px; display: flex; align-items: center; gap: 30px; margin-bottom: 25px; border-radius: 6px; }
.rating-score-box { text-align: center; color: #ffc107; }
.big-score { font-size: 2.5rem; font-weight: 700; }
.total-stars { font-size: 1.1rem; color: #ffc107; }
.total-count { color: #555; margin-top: 5px; font-size: 0.9rem; }

.rating-filters { display: flex; gap: 10px; flex-wrap: wrap; }
.rating-filters button { background: #fff; border: 1px solid #ddd; padding: 6px 15px; cursor: pointer; border-radius: 2px; }
.rating-filters button.active { border-color: #0055aa; color: #0055aa; font-weight: 600; }

.review-list { display: flex; flex-direction: column; gap: 20px; }
.review-item { display: flex; gap: 15px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
.user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #eee; }
.review-content { flex: 1; }
.review-header { display: flex; justify-content: space-between; margin-bottom: 5px; }
.user-name { font-size: 0.95rem; color: #333; font-weight: 600; }
.review-date { font-size: 0.8rem; color: #999; }
.user-rating-stars { margin-bottom: 5px; display: flex; gap: 2px; }
.variant-info { font-size: 0.85rem; color: #888; margin-bottom: 8px; }
.comment-text { font-size: 0.95rem; line-height: 1.5; color: #444; }
.empty-review { text-align: center; color: #999; font-style: italic; padding: 20px; }
</style>