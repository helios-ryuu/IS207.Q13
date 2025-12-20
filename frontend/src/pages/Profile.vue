<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '../utils/useAuth'
import { useToast } from '../utils/useToast'
import api from '../utils/api'
import { getImageUrl } from '../utils/imageUrl'
import Header from '../components/layout/HomeHeader.vue'
import Footer from '../components/layout/AppFooter.vue'

const router = useRouter()
const route = useRoute()
const { user, logout } = useAuth()
const { showError } = useToast()

const tab = ref('posts')
const isFollowing = ref(false)
const loading = ref(true)

// Profile data from API
const profile = ref(null)
const posts = ref([])
const stats = ref({
  posts: 0,
  followers: 0,
  following: 0,
  rating: 0,
  transactions: 0
})

const isMine = computed(() => {
  if (!user || !user.value) return false
  const id = route.params.id || route.query.user
  if (!id) return true
  return [user.value.id, user.value.email, user.value.name].filter(Boolean).map(String).includes(String(id))
})

function toggleFollow() { isFollowing.value = !isFollowing.value }
function goTab(name) { tab.value = name }
function editProfile() { router.push('/profile/edit') }
function doLogout() { logout(); router.push('/home') }

// Helper functions
const getStatusLabel = (status) => {
  const labels = {
    'pending': 'Đang xét duyệt',
    'rejected': 'Đã bị từ chối',
    'active': 'Đang hiển thị',
    'hidden': 'Đã ẩn',
  }
  return labels[status] || status
}

const formatPrice = (price) => {
  if (!price) return '0 đ'
  return new Intl.NumberFormat('vi-VN').format(price) + ' đ'
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN')
}

async function fetchProfile() {
  try {
    const response = await api.get('/user/profile')
    if (response.data.success) {
      profile.value = response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch profile:', err)
    showError('Không thể tải thông tin hồ sơ')
  }
}

async function fetchPosts() {
  try {
    const response = await api.get('/user/listings')
    if (response.data.success) {
      posts.value = response.data.data || []
      stats.value.posts = posts.value.length
    }
  } catch (err) {
    console.error('Failed to fetch posts:', err)
  }
}

async function fetchStats() {
  try {
    // Fetch favorites count
    const favRes = await api.get('/user/favorites/count')
    if (favRes.data.success) {
      stats.value.following = favRes.data.data?.count || 0
    }
    
    // Fetch orders count for transactions
    const ordersRes = await api.get('/orders')
    if (ordersRes.data.success) {
      const deliveredOrders = (ordersRes.data.data || []).filter(o => o.status === 'delivered')
      stats.value.transactions = deliveredOrders.length
    }
  } catch (err) {
    console.error('Failed to fetch stats:', err)
  }
}

onMounted(async () => {
  loading.value = true
  await Promise.all([fetchProfile(), fetchPosts(), fetchStats()])
  loading.value = false
})
</script>

<template>
  <div class="page">
    <Header />
    <main class="content">
      <div class="container">
        <div class="top-return">
          <router-link to="/home">← Trở về trang chủ</router-link>
        </div>

        <div v-if="loading" class="loading">Đang tải...</div>

        <template v-else>
          <!-- Profile Card -->
          <section class="profile-card">
            <div class="cover" :style="profile?.cover_url ? { backgroundImage: `url(${getImageUrl(profile.cover_url)})` } : {}"></div>
            <div class="profile-info">
              <div class="avatar-wrapper">
                <img v-if="profile?.avatar_url" :src="profile.avatar_url" alt="Avatar" class="avatar" />
                <div v-else class="avatar">{{ profile?.full_name ? profile.full_name.charAt(0) : user?.full_name?.charAt(0) || 'U' }}</div>
              </div>
              <div class="info">
                <h1>{{ profile?.full_name || user?.full_name || 'Người dùng' }}</h1>
                <p class="handle">@{{ (profile?.full_name || user?.full_name || 'user').toLowerCase().replace(/\s+/g,'') }}</p>
                <p class="bio">{{ profile?.bio || 'Chào! Đây là trang cá nhân của tôi.' }}</p>
                <div class="rating">
                  <span>{{ profile?.role || user?.role }}</span>
                  <span class="sep">•</span>
                  <span>{{ stats.transactions }} giao dịch</span>
                </div>
              </div>
              <div class="actions">
                <button v-if="isMine" class="btn primary" @click="editProfile">Chỉnh sửa</button>
                <template v-else>
                  <button class="btn primary" @click="toggleFollow">{{ isFollowing ? 'Đã theo dõi' : 'Theo dõi' }}</button>
                  <button class="btn secondary" @click="router.push('/chat')">Nhắn tin</button>
                </template>
              </div>
            </div>
            <div class="stats">
              <div class="stat"><div class="num">{{ stats.posts }}</div><div class="label">Bài đăng</div></div>
              <div class="stat"><div class="num">{{ stats.following }}</div><div class="label">Yêu thích</div></div>
              <div class="stat"><div class="num">{{ stats.transactions }}</div><div class="label">Giao dịch</div></div>
            </div>
          </section>

          <!-- Tabs -->
          <nav class="tabs">
            <button :class="{ active: tab === 'posts' }" @click="goTab('posts')">Bài đăng</button>
            <button :class="{ active: tab === 'about' }" @click="goTab('about')">Giới thiệu</button>
          </nav>

          <!-- Tab Content -->
          <div class="tab-content">
            <div v-show="tab === 'posts'" class="posts-grid">
              <div v-if="posts.length === 0" class="empty">
                <div class="empty-icon">📝</div>
                <p>Chưa có bài đăng nào</p>
                <button v-if="isMine" class="btn primary" @click="router.push('/post')">Đăng tin ngay</button>
              </div>
              
              <div v-for="p in posts" :key="p.id" class="post-card" @click="router.push(`/product/${p.id}`)">
                <div class="post-image">
                  <img v-if="p.image" :src="getImageUrl(p.image)" alt="" @error="$event.target.style.display='none'" />
                  <div v-else class="post-image-placeholder">
                    <span>📷</span>
                  </div>
                  <span class="post-status" :class="'status-' + p.status">{{ getStatusLabel(p.status) }}</span>
                </div>
                <div class="post-body">
                  <h4 class="post-title">{{ p.title }}</h4>
                  <p class="post-category">{{ p.category }}</p>
                  <div class="post-footer">
                    <span class="post-price">{{ formatPrice(p.price) }}</span>
                    <span class="post-date">{{ formatDate(p.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div v-show="tab === 'about'" class="about-card">
              <h3>Giới thiệu</h3>
              <p>{{ profile?.bio || 'Chưa có thông tin giới thiệu.' }}</p>
              <div v-if="profile?.phone_number" class="info-row">
                <strong>Điện thoại:</strong> {{ profile.phone_number }}
              </div>
              <div v-if="profile?.address" class="info-row">
                <strong>Địa chỉ:</strong> {{ profile.address }}
              </div>
            </div>
          </div>
        </template>
      </div>
    </main>
    <Footer />
  </div>
</template>

<style scoped>
.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f8fafc;
}

.content {
  flex: 1;
  padding: 24px;
}

.container {
  max-width: 900px;
  margin: 0 auto;
}

.top-return {
  margin-bottom: 16px;
}

.top-return a {
  color: #64748b;
  text-decoration: none;
  font-size: 14px;
}

.top-return a:hover {
  color: #3b82f6;
}

.loading {
  text-align: center;
  padding: 48px;
  color: #64748b;
}

.empty {
  text-align: center;
  padding: 32px;
  color: #94a3b8;
}

/* Profile Card */
.profile-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  margin-bottom: 24px;
}

.cover {
  height: 160px;
  background: linear-gradient(135deg, #3b82f6, #06b6d4);
  background-size: cover;
  background-position: center;
}

.profile-info {
  display: flex;
  gap: 20px;
  padding: 20px;
  align-items: flex-start;
}

.avatar-wrapper {
  margin-top: -50px;
}

.avatar {
  width: 100px;
  height: 100px;
  border-radius: 16px;
  background: #fff;
  border: 4px solid #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  font-weight: 700;
  color: #3b82f6;
  object-fit: cover;
}

.info {
  flex: 1;
}

.info h1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.handle {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 8px;
}

.bio {
  color: #475569;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 8px;
}

.rating {
  font-size: 13px;
  color: #64748b;
}

.rating .sep {
  margin: 0 8px;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn {
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
}

.btn.primary {
  background: #3b82f6;
  color: #fff;
}

.btn.primary:hover {
  background: #2563eb;
}

.btn.secondary {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.btn.secondary:hover {
  background: #e2e8f0;
}

.stats {
  display: flex;
  border-top: 1px solid #f1f5f9;
}

.stat {
  flex: 1;
  text-align: center;
  padding: 16px;
}

.stat:not(:last-child) {
  border-right: 1px solid #f1f5f9;
}

.stat .num {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e293b;
}

.stat .label {
  font-size: 13px;
  color: #64748b;
}

/* Tabs */
.tabs {
  display: flex;
  gap: 4px;
  margin-bottom: 16px;
  border-bottom: 1px solid #e2e8f0;
}

.tabs button {
  padding: 12px 20px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  color: #64748b;
  border-bottom: 2px solid transparent;
  margin-bottom: -1px;
}

.tabs button:hover {
  color: #3b82f6;
}

.tabs button.active {
  color: #3b82f6;
  border-bottom-color: #3b82f6;
}

/* Posts Grid */
.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.empty {
  grid-column: 1 / -1;
  text-align: center;
  padding: 48px 24px;
  color: #94a3b8;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 12px;
}

.empty p {
  margin-bottom: 16px;
}

.post-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
}

.post-card:hover {
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  transform: translateY(-2px);
}

.post-image {
  position: relative;
  width: 100%;
  height: 180px;
  background: #f1f5f9;
}

.post-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.post-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  color: #cbd5e1;
}

.post-status {
  position: absolute;
  top: 10px;
  left: 10px;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.post-status.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.post-status.status-active {
  background: #d1fae5;
  color: #047857;
}

.post-status.status-rejected {
  background: #fee2e2;
  color: #dc2626;
}

.post-status.status-hidden {
  background: #f1f5f9;
  color: #64748b;
}

.post-body {
  padding: 16px;
}

.post-title {
  font-size: 15px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 6px 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.post-category {
  font-size: 12px;
  color: #64748b;
  margin: 0 0 12px 0;
}

.post-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.post-price {
  font-weight: 700;
  color: #dc2626;
  font-size: 16px;
}

.post-date {
  font-size: 12px;
  color: #94a3b8;
}

/* About */
.about-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 20px;
}

.about-card h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 12px;
}

.about-card p {
  color: #475569;
  line-height: 1.6;
  margin-bottom: 12px;
}

.info-row {
  font-size: 14px;
  color: #475569;
  margin-bottom: 8px;
}

@media (max-width: 640px) {
  .profile-info {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .avatar-wrapper {
    margin-top: -50px;
  }
  
  .actions {
    width: 100%;
    justify-content: center;
  }
  
  .posts-grid {
    grid-template-columns: 1fr;
  }
}
</style>
