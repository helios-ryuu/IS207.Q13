<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../utils/useAuth'
import { useToast } from '../utils/useToast'
import api from '../utils/api'
import { getImageUrl } from '../utils/imageUrl'
import Header from '../components/layout/HomeHeader.vue'
import Footer from '../components/layout/AppFooter.vue'

const router = useRouter()
const { user, isLoggedIn } = useAuth()
const { showToast } = useToast()

// Filter & Sort
const selectedCategory = ref('all')
const sortBy = ref('recent') // recent, price-asc, price-desc
const searchQuery = ref('')

// Loading & Error states
const isLoading = ref(true)
const error = ref(null)
const isRemoving = ref(null) // ID của sản phẩm đang xóa

// Favorites data từ API
const favorites = ref([])

// Fetch favorites từ API
const fetchFavorites = async () => {
  isLoading.value = true
  error.value = null
  
  try {
    const response = await api.get('/user/favorites')
    
    if (response.data.success) {
      favorites.value = response.data.data.map(item => ({
        id: item.id,
        title: item.title,
        price: item.price,
        image: getImageUrl(item.image),
        location: item.location,
        category: item.category,
        condition: item.condition,
        postedDate: item.posted_date,
        savedDate: item.saved_date,
        seller: item.seller,
        isFeatured: false
      }))
    }
  } catch (err) {
    console.error('Failed to fetch favorites:', err)
    error.value = err.response?.data?.message || 'Không thể tải danh sách yêu thích'
    showToast(error.value, 'error')
  } finally {
    isLoading.value = false
  }
}

const categories = computed(() => {
  const cats = new Set(favorites.value.map(f => f.category))
  return ['all', ...Array.from(cats)]
})

const filteredFavorites = computed(() => {
  let result = [...favorites.value]
  
  // Filter by category
  if (selectedCategory.value !== 'all') {
    result = result.filter(f => f.category === selectedCategory.value)
  }
  
  // Filter by search
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(f => 
      f.title.toLowerCase().includes(query) ||
      f.location.toLowerCase().includes(query)
    )
  }
  
  // Sort
  if (sortBy.value === 'price-asc') {
    result.sort((a, b) => a.price - b.price)
  } else if (sortBy.value === 'price-desc') {
    result.sort((a, b) => b.price - a.price)
  } else {
    result.sort((a, b) => new Date(b.savedDate) - new Date(a.savedDate))
  }
  
  return result
})

const stats = computed(() => ({
  total: favorites.value.length,
  thisWeek: favorites.value.filter(f => {
    const saved = new Date(f.savedDate)
    const weekAgo = new Date()
    weekAgo.setDate(weekAgo.getDate() - 7)
    return saved >= weekAgo
  }).length
}))

const removeFavorite = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
    return
  }
  
  isRemoving.value = id
  
  try {
    const response = await api.delete(`/user/favorites/${id}`)
    
    if (response.data.success) {
      favorites.value = favorites.value.filter(f => f.id !== id)
      showToast('Đã xóa khỏi danh sách yêu thích', 'success')
    }
  } catch (err) {
    console.error('Failed to remove favorite:', err)
    showToast(err.response?.data?.message || 'Không thể xóa sản phẩm', 'error')
  } finally {
    isRemoving.value = null
  }
}

const viewProduct = (id) => {
  router.push(`/product/${id}`)
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price) + ' ₫'
}

const formatDate = (dateStr) => {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  const now = new Date()
  const diff = Math.floor((now - date) / (1000 * 60 * 60 * 24))
  
  if (diff === 0) return 'Hôm nay'
  if (diff === 1) return 'Hôm qua'
  if (diff < 7) return `${diff} ngày trước`
  return date.toLocaleDateString('vi-VN')
}

const clearAll = async () => {
  if (!confirm('Bạn có chắc muốn xóa tất cả sản phẩm yêu thích?')) {
    return
  }
  
  isLoading.value = true
  
  try {
    // Xóa từng sản phẩm (có thể tạo API clear-all sau)
    for (const fav of favorites.value) {
      await api.delete(`/user/favorites/${fav.id}`)
    }
    favorites.value = []
    showToast('Đã xóa tất cả sản phẩm yêu thích', 'success')
  } catch (err) {
    console.error('Failed to clear favorites:', err)
    showToast('Không thể xóa tất cả sản phẩm', 'error')
    // Refresh lại danh sách
    await fetchFavorites()
  } finally {
    isLoading.value = false
  }
}

const retryFetch = () => {
  fetchFavorites()
}

onMounted(() => {
  if (!isLoggedIn.value) {
    router.push('/login')
  } else {
    fetchFavorites()
  }
})
</script>

<template>
  <div>
    <Header />

    <div class="favorites-page container">
      <div class="page-header">
        <div class="header-top">
          <h1 class="page-title">
            <span class="icon">❤️</span>
            Sản phẩm yêu thích
          </h1>
          <div class="stats">
            <div class="stat-item">
              <span class="stat-value">{{ stats.total }}</span>
              <span class="stat-label">Tổng số</span>
            </div>
            <div class="stat-item">
              <span class="stat-value">{{ stats.thisWeek }}</span>
              <span class="stat-label">Tuần này</span>
            </div>
          </div>
        </div>
        <p class="subtitle">Quản lý các sản phẩm bạn quan tâm</p>
      </div>

      <div class="toolbar">
        <div class="search-box">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Tìm trong danh sách yêu thích..."
            class="search-input"
          />
          <span class="search-icon">🔍</span>
        </div>

        <div class="filters">
          <select v-model="selectedCategory" class="filter-select">
            <option value="all">Tất cả danh mục</option>
            <option v-for="cat in categories.filter(c => c !== 'all')" :key="cat" :value="cat">
              {{ cat }}
            </option>
          </select>

          <select v-model="sortBy" class="filter-select">
            <option value="recent">Mới nhất</option>
            <option value="price-asc">Giá tăng dần</option>
            <option value="price-desc">Giá giảm dần</option>
          </select>

          <button v-if="favorites.length > 0" class="clear-btn" @click="clearAll">
            <span class="icon">🗑️</span>
            Xóa tất cả
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-state">
        <div class="spinner"></div>
        <p>Đang tải danh sách yêu thích...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-state">
        <div class="error-icon">⚠️</div>
        <h2 class="error-title">Có lỗi xảy ra</h2>
        <p class="error-text">{{ error }}</p>
        <button class="retry-btn" @click="retryFetch">
          Thử lại
        </button>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredFavorites.length === 0" class="empty-state">
        <div class="empty-icon">💔</div>
        <h2 class="empty-title">
          {{ favorites.length === 0 ? 'Chưa có sản phẩm yêu thích' : 'Không tìm thấy kết quả' }}
        </h2>
        <p class="empty-text">
          {{ favorites.length === 0 
            ? 'Hãy khám phá và lưu những sản phẩm bạn thích để xem sau' 
            : 'Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm'
          }}
        </p>
        <button class="explore-btn" @click="router.push('/products')">
          Khám phá ngay
        </button>
      </div>

      <!-- Products Grid -->
      <div v-else class="products-grid">
        <div v-for="item in filteredFavorites" :key="item.id" class="product-card">
          <div class="card-badge" v-if="item.isFeatured">⭐ Nổi bật</div>
          <button 
            class="remove-btn" 
            :class="{ 'is-removing': isRemoving === item.id }"
            :disabled="isRemoving === item.id"
            @click.stop="removeFavorite(item.id)"
          >
            <span v-if="isRemoving === item.id" class="btn-spinner"></span>
            <span v-else>✕</span>
          </button>
          
          <div class="product-image" @click="viewProduct(item.id)">
            <img :src="getImageUrl(item.image)" :alt="item.title" />
          </div>

          <div class="product-info">
            <div class="category-tag">{{ item.category }}</div>
            <h3 class="product-title" @click="viewProduct(item.id)">{{ item.title }}</h3>
            
            <div class="product-meta">
              <div class="price">{{ formatPrice(item.price) }}</div>
              <div class="condition">{{ item.condition }}</div>
            </div>

            <div class="product-details">
              <div class="detail-item">
                <span class="detail-icon">📍</span>
                <span class="detail-text">{{ item.location }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-icon">👤</span>
                <span class="detail-text">{{ item.seller }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-icon">🕒</span>
                <span class="detail-text">{{ formatDate(item.postedDate) }}</span>
              </div>
            </div>

            <div class="saved-date">
              <span class="icon">❤️</span>
              Đã lưu: {{ formatDate(item.savedDate) }}
            </div>

            <div class="card-actions">
              <button class="action-btn primary" @click="viewProduct(item.id)">
                Xem chi tiết
              </button>
              <button class="action-btn secondary" @click="router.push('/chat')">
                Nhắn tin
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
  font-family: inherit;
}

.favorites-page {
  background: #f8fafc;
  min-height: 100vh;
}

/* Header */
.page-header {
  margin-bottom: 2rem;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.page-title .icon {
  font-size: 36px;
  animation: heartBeat 1.5s ease-in-out infinite;
}

@keyframes heartBeat {
  0%, 100% { transform: scale(1); }
  25% { transform: scale(1.1); }
  50% { transform: scale(1); }
}

.stats {
  display: flex;
  gap: 2rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem 1.5rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.stat-value {
  font-size: 28px;
  font-weight: 800;
  color: #ef4444;
}

.stat-label {
  font-size: 13px;
  color: #64748b;
  font-weight: 600;
}

.subtitle {
  color: #64748b;
  font-size: 15px;
}

/* Toolbar */
.toolbar {
  background: white;
  padding: 1.5rem;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
  margin-bottom: 2rem;
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  align-items: center;
}

.search-box {
  flex: 1;
  min-width: 250px;
  position: relative;
}

.search-input {
  width: 100%;
  padding: 0.875rem 3rem 0.875rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 15px;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #ef4444;
  box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.search-icon {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
}

.filters {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.filter-select {
  padding: 0.875rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  color: #475569;
  background: white;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #ef4444;
}

.clear-btn {
  padding: 0.875rem 1.5rem;
  background: #fee2e2;
  color: #ef4444;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.clear-btn:hover {
  background: #ef4444;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.empty-icon {
  font-size: 80px;
  margin-bottom: 1.5rem;
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.loading-state p {
  color: #64748b;
  margin-top: 1rem;
  font-size: 15px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #fee2e2;
  border-top-color: #ef4444;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Error State */
.error-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.error-icon {
  font-size: 64px;
  margin-bottom: 1rem;
}

.error-title {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.75rem;
}

.error-text {
  color: #64748b;
  margin-bottom: 2rem;
  font-size: 15px;
}

.retry-btn {
  padding: 1rem 2rem;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.retry-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(239, 68, 68, 0.4);
}

/* Button Spinner */
.btn-spinner {
  width: 12px;
  height: 12px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  display: inline-block;
}

.remove-btn.is-removing {
  opacity: 0.7;
  cursor: not-allowed;
}

.empty-title {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.75rem;
}

.empty-text {
  color: #64748b;
  margin-bottom: 2rem;
  font-size: 15px;
}

.explore-btn {
  padding: 1rem 2rem;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.explore-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(239, 68, 68, 0.4);
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.product-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  position: relative;
  border: 2px solid transparent;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
  border-color: #ef4444;
}

.card-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  z-index: 2;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.remove-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 36px;
  height: 36px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  font-size: 18px;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.remove-btn:hover {
  background: #ef4444;
  transform: scale(1.1);
}

.product-image {
  width: 100%;
  height: 220px;
  background: #f1f5f9;
  cursor: pointer;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.1);
}

.product-info {
  padding: 1.5rem;
}

.category-tag {
  display: inline-block;
  background: #ede9fe;
  color: #7c3aed;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  margin-bottom: 0.75rem;
}

.product-title {
  font-size: 17px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 1rem;
  cursor: pointer;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
}

.product-title:hover {
  color: #ef4444;
}

.product-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f1f5f9;
}

.price {
  font-size: 22px;
  font-weight: 800;
  color: #ef4444;
}

.condition {
  background: #dcfce7;
  color: #16a34a;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.product-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 13px;
  color: #64748b;
}

.detail-icon {
  font-size: 14px;
}

.saved-date {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  background: #fef2f2;
  border-radius: 12px;
  margin-bottom: 1rem;
  font-size: 13px;
  font-weight: 600;
  color: #ef4444;
}

.card-actions {
  display: flex;
  gap: 0.75rem;
}

.action-btn {
  flex: 1;
  padding: 0.875rem;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn.primary {
  background: #ef4444;
  color: white;
}

.action-btn.primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
}

.action-btn.secondary {
  background: white;
  border: 2px solid #e2e8f0;
  color: #475569;
}

.action-btn.secondary:hover {
  border-color: #ef4444;
  color: #ef4444;
}

/* Responsive */
@media (max-width: 768px) {
  .header-top {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .toolbar {
    flex-direction: column;
    align-items: stretch;
  }

  .filters {
    flex-direction: column;
  }

  .filter-select,
  .clear-btn {
    width: 100%;
  }

  .products-grid {
    grid-template-columns: 1fr;
  }
}
</style>
