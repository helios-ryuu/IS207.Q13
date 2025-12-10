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
        <img :src="product.imageUrl || product.image || 'https://via.placeholder.com/200'" :alt="product.title" class="product-image">
      </div>

      <div class="product-info">
        <h3 class="product-title">{{ product.title }}</h3>
        <div class="price-section">
          <span class="current-price">{{ product.price }}</span>
          <span v-if="product.originalPrice" class="original-price">{{ product.originalPrice }}</span>
        </div>
        <div class="seller-info">
          <div class="avatar-placeholder"></div>
          <span class="seller-name">{{ product.seller || product.username }}</span>
        </div>
        <span class="location">{{ product.location }}</span>
      </div>
    </div>
  </router-link>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuth } from '../../utils/useAuth'
import api from '../../utils/api'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['favorite-changed'])

const { isLoggedIn } = useAuth()

const isFavorited = ref(false)
const isToggling = ref(false)

// Kiểm tra trạng thái yêu thích khi mount
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

// Toggle yêu thích
const toggleFavorite = async () => {
  if (!isLoggedIn.value) {
    alert('Vui lòng đăng nhập để thêm vào yêu thích')
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
    alert(err.response?.data?.message || 'Không thể thực hiện thao tác')
  } finally {
    isToggling.value = false
  }
}

onMounted(() => {
  checkFavoriteStatus()
})
</script>

<style scoped>
.product-card-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.product-title {
  font-size: 16px;
  font-weight: 500;
  color: #333;
  margin-bottom: 5px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-decoration: none;
  transition: text-decoration 0.2s;
}

.product-card:hover .product-title {
  text-decoration: underline;
}

.product-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  position: relative;
  transition: transform 0.2s;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
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
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding: 10px;
}

.price-section {
  margin-bottom: 5px;
}

.current-price {
  font-size: 18px;
  font-weight: bold;
  color: #ff4500;
  margin-right: 8px;
}

.original-price {
  font-size: 14px;
  color: #888;
  text-decoration: line-through;
}

.seller-info {
  display: flex;
  align-items: center;
  margin-bottom: 5px;
}

.avatar-placeholder {
  width: 24px;
  height: 24px;
  background-color: #e9e9e9;
  border-radius: 50%;
  margin-right: 8px;
}

.seller-name, .location {
  font-size: 13px;
  color: #666;
  text-decoration: none;
}

.seller-name:hover, .location:hover {
  text-decoration: underline;
}
</style>