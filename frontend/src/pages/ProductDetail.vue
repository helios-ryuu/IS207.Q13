<template>
  <div class="product-detail-page">
    <Header />
    <main class="container">

      <nav class="breadcrumbs">
        Chợ tốt / Điện thoại / Điện thoại TPHCM /
        <strong>{{ product?.name || 'Đang tải...' }}</strong>
      </nav>

      <div class="product-main-card">
        <section class="product-gallery">
          <div class="main-image-wrapper">
            <button class="gallery-nav prev" @click="prevImage">&lt;</button>
            <button class="gallery-nav next" @click="nextImage">&gt;</button>
            <div class="image-actions">
              <button class="icon-btn"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg></button>
              <button class="icon-btn"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg></button>
            </div>
            <img :src="currentImage" :alt="product?.name" class="main-image">
          </div>
          <div class="thumbnail-list-wrapper">
            <div class="thumbnail-list">
              <img v-for="(image, index) in product?.images" :key="index" :src="image" alt="Thumbnail" :class="{ active: index === currentImageIndex }" @click="selectImage(index)">
            </div>
            <button class="thumb-nav-next">&gt;</button>
          </div>
        </section>

        <section class="product-info">
          <h1>{{ product?.name }}</h1>
          <p class="description">{{ product?.description }}</p>
          <div class="price-section">
            <span class="price">{{ product?.price }} đ</span>
            <span class="tag" v-for="tag in product?.tags" :key="tag">{{ tag }}</span>
          </div>
          <div class="meta-info">
            <div class="meta-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              <span>Vị trí: <strong>{{ product?.location }}</strong></span>
            </div>
            <div class="meta-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              <span>Thời gian cập nhật: <strong>{{ product?.lastUpdated }}</strong></span>
            </div>
          </div>
          <div class="price-range" v-if="product?.marketPrice">
            <div class="price-range-header">
              <strong>Khoảng giá thị trường</strong>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            </div>
            <span>Theo dữ liệu trong 3 tháng gần nhất</span>
            <div class="range-bar-container">
              <div class="range-tooltip" :style="{ left: product.marketPrice.position }">
                {{ product.marketPrice.current }}
                <div class="range-tooltip-arrow"></div>
              </div>
              <div class="range-bar-bg">
                <div class="range-bar-active" :style="{ left: product.marketPrice.minPosition, right: product.marketPrice.maxPosition }"></div>
              </div>
              <span class="range-label-min">{{ product.marketPrice.min }}</span>
              <span class="range-label-max">{{ product.marketPrice.max }}</span>
            </div>
          </div>

          <div class="action-buttons">
            <button class="btn-chat" @click="handleChat">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
              Chat
            </button>
            <button class="btn-add-cart" @click="handleAddToCart">Thêm vào giỏ hàng</button>
            <button class="btn-buy-now">Đặt hàng</button>
          </div>

          <div class="seller-info">
            <img
                :src="product?.seller.avatar"
                :alt="product?.seller.name"
                class="seller-avatar"
                @click="goToSellerProfile"
                style="cursor: pointer"
            >
            <div class="seller-details">
              <strong
                  @click="goToSellerProfile"
                  style="cursor: pointer"
              >{{ product?.seller.name }}</strong> <br>
              <a href="#" @click.prevent="goToSellerProfile">Lịch sử hoạt động</a>
            </div>
            <div class="seller-stats">
              <div class="stat-item"><strong>{{ product?.seller.listings }}</strong> <span>SĐ bán</span></div>
              <div class="stat-item"><strong>{{ product?.seller.rating }} ⭐</strong> <br> <span>{{ product?.seller.reviews }} đánh giá</span></div>
            </div>
            <button class="btn-view-profile" @click="goToSellerProfile">Xem trang</button>
          </div>
          <div class="quick-chat">
            <span>Chat nhanh:</span>
            <button class="btn-quick-message">Bạn có ship hàng không? </button>
            <button class="btn-quick-message">Sản phẩm này còn bảo hành không?</button>
            <button class="btn-quick-message-nav">&gt;</button>
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
          <CommentSection :comments="product?.comments" />
        </div>
      </div>

      <section class="related-listings-card" id="seller-listings">
        <h2>Tin khác của {{ product?.seller.name }}</h2>
        <div class="horizontal-product-list">
          <ProductCard
              v-for="item in visibleSellerListings"
              :key="item.id"
              :product="item"
          />
        </div>
        <button v-if="hasMoreSeller" class="see-more-btn" @click="loadMoreSeller">
          Xem thêm
        </button>
      </section>

      <section class="related-listings-card" id="similar-listings">
        <h2>Tin đăng tương tự</h2>
        <div class="grid-product-list">
          <ProductCard
              v-for="item in visibleSimilarListings"
              :key="item.id"
              :product="item"
          />
        </div>
        <button v-if="hasMoreSimilar" class="see-more-btn" @click="loadMoreSimilar">
          Xem thêm
        </button>
      </section>

    </main>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../utils/api';
import Header from "../components/layout/SearchHeader.vue";
import Footer from "../components/layout/AppFooter.vue";
import CommentSection from '../components/CommentSection.vue';
import ProductCard from '../components/product/ProductCardSimple.vue';
import { useCart } from '../stores/cart';

const route = useRoute();
const router = useRouter();
const product = ref(null);
const loading = ref(true);
const { addToCart } = useCart();

// State "Xem thêm"
const sellerListings = ref([]);
const similarListings = ref([]);
const visibleSellerCount = ref(4);
const visibleSimilarCount = ref(8);
const visibleSellerListings = computed(() => sellerListings.value.slice(0, visibleSellerCount.value));
const visibleSimilarListings = computed(() => similarListings.value.slice(0, visibleSimilarCount.value));
const hasMoreSeller = computed(() => visibleSellerCount.value < sellerListings.value.length);
const hasMoreSimilar = computed(() => visibleSimilarCount.value < similarListings.value.length);
const loadMoreSeller = () => { visibleSellerCount.value += 4; };
const loadMoreSimilar = () => { visibleSimilarCount.value += 8; };

const currentImageIndex = ref(0);
const currentImage = computed(() => {
  if (product.value && product.value.images && product.value.images.length > 0) {
    return product.value.images[currentImageIndex.value];
  }
  return 'https://via.placeholder.com/600x400/eeeeee/cccccc?text=Loading...';
});
const nextImage = () => { if (product.value) currentImageIndex.value = (currentImageIndex.value + 1) % product.value.images.length; };
const prevImage = () => { if (product.value) currentImageIndex.value = (currentImageIndex.value - 1 + product.value.images.length) % product.value.images.length; };
const selectImage = (index) => { currentImageIndex.value = index; };

watch(() => route.params.id, (newId) => {
  if (newId) {
    fetchProductDetail();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

// === HÀM XỬ LÝ CHAT ===
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

// === HÀM CHUYỂN HƯỚNG TỚI TRANG NGƯỜI BÁN ===
const goToSellerProfile = () => {
  if (product.value && product.value.seller) {
    router.push({
      name: 'SellerProfile', // Đảm bảo tên route này khớp với trong router/index.js
      params: { id: product.value.seller.id }
    });
  }
};

// === HÀM THÊM VÀO GIỎ HÀNG ===
const handleAddToCart = () => {
  if (!product.value) return;
  
  const success = addToCart(product.value);
  if (success) {
    alert('Đã thêm sản phẩm vào giỏ hàng!');
  }
};

// Helper Format Price
const formatPrice = (price) => {
  if (!price) return '0';
  return new Intl.NumberFormat('vi-VN').format(price);
};

// Map API Data
// Helper to resolve image URL (same as ManageListings)
const getImageUrl = (url) => {
  if (!url) return 'https://via.placeholder.com/600x400/eeeeee/cccccc?text=No+Image';
  
  // If already full URL (not localhost without port), return as is
  if (url.startsWith('http') && !url.includes('localhost/')) return url;
  
  // Fix absolute localhost URLs (missing port 8000)
  if (url.startsWith('http://localhost/')) {
    return url.replace('http://localhost/', 'http://localhost:8000/');
  }
  
  // If relative URL starting with /storage, prepend backend URL
  if (url.startsWith('/storage')) {
    return 'http://localhost:8000' + url;
  }
  
  return url;
};

// Map API product to component format
const mapProductFromApi = (data) => {
  const variant = data.variants?.[0] || {};
  const images = [];
  if (data.variants) {
    data.variants.forEach(v => {
      if (v.images) {
        v.images.forEach(img => {
          // Handle both string URLs and object with image_url property
          const url = typeof img === 'string' ? img : img.image_url;
          if (url) images.push(getImageUrl(url));
        });
      }
    });
  }
  if (images.length === 0) {
    images.push('https://via.placeholder.com/600x400/eeeeee/cccccc?text=No+Image');
  }

  return {
    id: data.id,
    name: data.name,
    description: data.description || 'Mô tả sản phẩm',
    price: formatPrice(variant.price || data.price_range?.min),
    tags: data.categories?.map(c => c.name) || [],
    location: 'TP. HCM',
    lastUpdated: 'Hôm nay',
    images: images,
    seller: {
      id: data.seller?.id || 0,
      name: data.seller?.full_name || data.seller?.username || 'Shop VietMarket',
      avatar: getImageUrl(data.seller?.avatar) || 'https://via.placeholder.com/50/FFD60A/333333?text=S',
      listings: 10,
      rating: 5.0,
      reviews: 100
    },
    marketPrice: {
      min: formatPrice(data.price_range?.min) + ' đ',
      max: formatPrice(data.price_range?.max) + ' đ',
      current: formatPrice(variant.price) + ' đ',
      position: '50%',
      minPosition: '20%',
      maxPosition: '20%'
    },
    detailedDescription: data.description || 'Mô tả chi tiết sản phẩm',
    specs: [
      { label: 'Màu sắc:', value: variant.color || 'Không xác định' },
      { label: 'Kích thước:', value: variant.size || 'Không xác định' },
      { label: 'Tình trạng:', value: data.status === 'active' ? 'Còn hàng' : 'Hết hàng' }
    ],
    comments: []
  };
};

const mapProductCard = (item) => ({
  id: item.id,
  title: item.name,
  price: formatPrice(item.price_range?.min || item.variants?.[0]?.price) + ' đ',
  location: 'TP. HCM',
  imageUrl: getImageUrl(item.image || item.variants?.[0]?.images?.[0]?.image_url)
});

const fetchProductDetail = async () => {
  loading.value = true;
  visibleSellerCount.value = 4;
  visibleSimilarCount.value = 8;

  const productId = route.params.id;
  try {
    const response = await api.get(`/products/${productId}`);
    const data = response.data.data || response.data;
    product.value = mapProductFromApi(data);

    try {
      const similarResponse = await api.get(`/products/${productId}/similar`);
      if (similarResponse.data.success) {
        similarListings.value = similarResponse.data.data.map(item => ({
          id: item.id,
          title: item.title,
          price: formatPrice(item.price) + ' đ',
          location: item.location || 'TP. HCM',
          imageUrl: item.image || 'https://via.placeholder.com/200/eeeeee/cccccc?text=No+Image',
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
    }

  } catch (error) {
    console.error('Error fetching product:', error);
    product.value = null;
  }
  loading.value = false;
};

onMounted(() => { fetchProductDetail(); });
</script>

<style scoped>
.product-detail-page { background-color: #f4f4f4; padding-bottom: 30px; }
.container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px; }
.breadcrumbs { font-size: 0.9rem; color: #555; padding: 20px 0; }
.product-main-card { display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
@media (max-width: 992px) { .product-main-card { grid-template-columns: 1fr; } }
.product-gallery { display: flex; flex-direction: column; gap: 15px; }
.main-image-wrapper { position: relative; aspect-ratio: 4 / 3; border-radius: 8px; overflow: hidden; }
.main-image { width: 100%; height: 100%; object-fit: cover; background: #eee; }
.gallery-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(255,255,255,0.7); cursor: pointer; font-size: 1.5rem; }
.gallery-nav.prev { left: 10px; } .gallery-nav.next { right: 10px; }
.image-actions { position: absolute; top: 10px; right: 10px; display: flex; gap: 10px; }
.icon-btn { width: 36px; height: 36px; border-radius: 50%; border: 1px solid #ddd; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; }
.thumbnail-list-wrapper { display: flex; gap: 10px; align-items: center; }
.thumbnail-list { display: flex; gap: 10px; overflow-x: auto; flex-grow: 1; }
.thumbnail-list img { width: 80px; height: 60px; object-fit: cover; border-radius: 4px; border: 2px solid transparent; cursor: pointer; }
.thumbnail-list img.active { border-color: #007bff; }
.thumb-nav-next { width: 30px; height: 30px; border-radius: 50%; border: none; background: #f0f0f0; cursor: pointer; flex-shrink: 0; }
.product-info { display: flex; flex-direction: column; gap: 15px; }
.product-info h1 { font-size: 1.8rem; margin: 0; }
.description { color: #555; margin: 0; }
.price-section { display: flex; align-items: center; gap: 10px; }
.price { font-size: 2rem; font-weight: bold; color: #d70000; }
.tag { background: #e0e0e0; padding: 4px 8px; border-radius: 4px; font-size: 0.9rem; }
.meta-info { display: flex; flex-direction: column; gap: 10px; padding: 15px 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee; }
.meta-item { display: flex; gap: 8px; color: #555; align-items: center; }
.price-range { background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
.price-range-header { display: flex; gap: 5px; align-items: center; font-weight: bold; }
.range-bar-container { position: relative; margin-top: 35px; padding-bottom: 15px; }
.range-tooltip { position: absolute; top: -25px; transform: translateX(-50%); background: #007bff; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; white-space: nowrap; }
.range-tooltip-arrow { position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #007bff; }
.range-bar-bg { height: 6px; background: #e0e0e0; border-radius: 3px; position: relative; }
.range-bar-active { position: absolute; height: 100%; background: #007bff; border-radius: 3px; }
.range-label-min { position: absolute; bottom: 0; left: 0; font-size: 0.8rem; color: #777; }
.range-label-max { position: absolute; bottom: 0; right: 0; font-size: 0.8rem; color: #777; }
.action-buttons { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.action-buttons button { padding: 12px; border: none; border-radius: 6px; font-weight: bold; color: #fff; cursor: pointer; }
.btn-chat { background: #007bff; display: flex; align-items: center; justify-content: center; gap: 5px; }
.btn-add-cart { background: #8a2be2; } .btn-buy-now { background: #ff6600; }
.seller-info { display: flex; gap: 15px; align-items: center; padding: 15px; border: 1px solid #eee; border-radius: 8px; }
.seller-avatar { width: 50px; height: 50px; border-radius: 50%; background: #eee; }
.seller-details { flex-grow: 1; } .seller-details a { color: #007bff; text-decoration: none; font-size: 0.9rem; }
.seller-stats { display: flex; gap: 15px; padding-left: 15px; border-left: 1px solid #eee; }
.btn-view-profile { background: #f0f0f0; border: 1px solid #ddd; padding: 8px 12px; border-radius: 4px; cursor: pointer; }
.quick-chat { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.btn-quick-message { background: #f5f5f5; border: 1px solid #ddd; padding: 6px 12px; border-radius: 20px; font-size: 0.9rem; cursor: pointer; }
.btn-quick-message-nav { width: 30px; height: 30px; border-radius: 50%; border: none; background: #eee; cursor: pointer; }

.product-info-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px; }
@media (max-width: 992px) { .product-info-layout { grid-template-columns: 1fr; } }
.bottom-left-column { display: flex; flex-direction: column; gap: 30px; }
.bottom-right-column { display: flex; flex-direction: column; }
.bottom-card { background-color: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
.bottom-card h2 { font-size: 1.5rem; font-weight: bold; margin-top: 0; margin-bottom: 20px; }
.specs-list { list-style: none; padding: 0; margin: 0; }
.specs-list li { display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #f0f0f0; }
.specs-list li:last-child { border-bottom: none; }
.spec-label { color: #555; } .spec-value { font-weight: 500; }

.related-listings-card { background-color: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); overflow: hidden; margin-top: 30px; }
.related-listings-card h2 { font-size: 1.5rem; font-weight: bold; margin-top: 0; margin-bottom: 20px; }

/* "Tin khác" (Cuộn ngang) */
.horizontal-product-list { display: flex; overflow-x: auto; gap: 15px; padding-bottom: 15px; margin: 0 -24px; padding-left: 24px; padding-right: 24px; }
.horizontal-product-list :deep(.product-card) { width: 220px; flex-shrink: 0; }
.horizontal-product-list :deep(a) { flex-shrink: 0; }

/* "Tin tương tự" (Lưới) */
.grid-product-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px; }
.grid-product-list :deep(.product-card) { width: auto; flex-shrink: 1; }

.see-more-btn { display: block; width: 100%; padding: 12px; margin-top: 20px; border: 1px solid #007bff; color: #007bff; background: #fff; border-radius: 6px; font-weight: bold; cursor: pointer; }
.see-more-btn:hover { background-color: #f0f8ff; }
</style>