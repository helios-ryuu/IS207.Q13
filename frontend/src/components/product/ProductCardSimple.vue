<template>
  <router-link
      :to="{ name: 'ProductDetail', params: { id: product.id } }"
      class="product-card-link"
  >
    <div class="product-card">
      <button
          class="favorite-btn"
          :class="{ 'is-favorited': isFavorited, 'is-loading': isToggling }"
          :disabled="isToggling"
          @click.stop.prevent="toggleFavorite"
          :title="isFavorited ? 'Bỏ yêu thích' : 'Thêm yêu thích'"
      >
        <span v-if="isToggling" class="btn-spinner"></span>
        <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" :fill="isFavorited ? '#ef4444' : 'white'" :stroke="isFavorited ? '#ef4444' : '#888'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
      </button>

      <div class="product-image-wrapper">
        <img :src="getImageUrl(product.imageUrl || product.image)" :alt="product.title" class="product-image">
      </div>

      <div class="product-info">
        <h3 class="product-title">{{ product.title }}</h3>
        <div class="price-section">
          <span class="current-price">{{ product.price }}</span>
          <span v-if="product.originalPrice" class="original-price">{{ product.originalPrice }}</span>
        </div>

        <div class="seller-info" @click.prevent="goToSeller">
          <div class="avatar-placeholder">
            <img v-if="product.userAvatar" :src="product.userAvatar" class="avatar-img" />
          </div>
          <span class="seller-name">{{ product.seller || product.username }}</span>
        </div>

        <span class="location">{{ product.location }}</span>
      </div>
    </div>
  </router-link>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue' // Thêm watch
import { useRouter } from 'vue-router' // 1. Thêm import useRouter
import { useAuth } from '../../utils/useAuth'
import { useToast } from '../../utils/useToast'
import api from '../../utils/api'
import { getImageUrl } from '../../utils/imageUrl'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['favorite-changed'])
const router = useRouter() // 2. Khởi tạo router
const { isLoggedIn } = useAuth()
const { showSuccess, showError } = useToast()

const isFavorited = ref(!!props.product.is_favorited) 
const isToggling = ref(false)

// Theo dõi nếu prop thay đổi (ví dụ khi load more)
watch(() => props.product.is_favorited, (newVal) => {
  isFavorited.value = !!newVal;
});

// === BỔ SUNG: Hàm chuyển trang Seller ===
const goToSeller = () => {
  // Lấy ID người bán từ prop, nếu không có thì dùng ID giả định 123
  const sellerId = props.product.sellerId || props.product.userId || 123;
  router.push(`/seller/${sellerId}`);
}

/*
// Các logic cũ giữ nguyên
const checkFavoriteStatus = async () => {
  if (!isLoggedIn.value || !props.product?.id) return

  try {
    const response = await api.get(`/user/favorites/${props.product.id}/check`)
    if (response.data.success) {
      isFavorited.value = response.data.is_favorited
    }
  } catch (err) {
    console.debug('Could not check favorite status:', err.message)
  }
}
  */

const toggleFavorite = async () => {
  if (!isLoggedIn.value) {
    showError('Vui lòng đăng nhập để thêm vào yêu thích')
    return
  }

  if (!props.product?.id) return

  isToggling.value = true

  try {
    const response = await api.post(`/user/favorites/${props.product.id}/toggle`)

    if (response.data.success) {
      isFavorited.value = response.data.is_favorited
      emit('favorite-changed', {
        productId: props.product.id,
        isFavorited: isFavorited.value
      })
    }
  } catch (err) {
    console.error('Failed to toggle favorite:', err)
    showError(err.response?.data?.message || 'Không thể thực hiện thao tác')
  } finally {
    isToggling.value = false
  }
}

onMounted(() => {
  // Không gọi API check ở đây nữa!
  //checkFavoriteStatus()
})
</script>

<style scoped>
.product-card-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.product-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  position: relative;
  transition: transform 0.2s;
  cursor: pointer;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.product-card:hover .product-title {
  text-decoration: underline;
}

.favorite-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: white;
  border: none;
  padding: 8px;
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  z-index: 10;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
}

.favorite-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.favorite-btn.is-favorited {
  background: #fef2f2;
}

.favorite-btn.is-favorited:hover {
  background: #fee2e2;
}

.favorite-btn.is-loading {
  opacity: 0.7;
  cursor: not-allowed;
}

.favorite-btn:disabled {
  cursor: not-allowed;
}

.btn-spinner {
  width: 14px;
  height: 14px;
  border: 2px solid #e5e7eb;
  border-top-color: #ef4444;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.product-image-wrapper {
  overflow: hidden;
  height: 200px;
  width: 100%;
  background-color: #f9f9f9;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding: 10px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.product-title {
  font-size: 15px;
  font-weight: 500;
  color: #333;
  margin: 0 0 8px 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: text-decoration 0.2s;
}

.price-section {
  margin-bottom: 8px;
}

.current-price {
  font-size: 16px;
  font-weight: bold;
  color: #ff4500;
  margin-right: 8px;
}

.original-price {
  font-size: 13px;
  color: #999;
  text-decoration: line-through;
}

/* === SỬA STYLE SELLER-INFO === */
.seller-info {
  display: flex;
  align-items: center;
  margin-bottom: 4px;
  margin-top: auto;
  /* Thêm hiệu ứng chuột để biết là bấm được */
  cursor: pointer;
  padding: 4px 0;
  transition: opacity 0.2s;
}

.seller-info:hover {
  opacity: 0.8;
}

.avatar-placeholder {
  width: 20px;
  height: 20px;
  background-color: #e0e0e0;
  border-radius: 50%;
  margin-right: 6px;
  overflow: hidden; /* Để ảnh avatar không bị tràn */
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.seller-name {
  font-size: 12px;
  color: #777;
  text-decoration: none;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Khi hover vào dòng seller info thì tên người bán gạch chân */
.seller-info:hover .seller-name {
  text-decoration: underline;
  color: #007bff;
}

.location {
  font-size: 12px;
  color: #777;
  display: block;
  margin-top: 2px;
}
</style>