<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '../utils/useAuth'
import { useToast } from '../utils/useToast'
import api from '../utils/api'
import Header from '../components/Header-HomePage.vue'
import Footer from '../components/Footer.vue'

const { user } = useAuth()
const { showSuccess, showError } = useToast()

// Stats data from API
const stats = ref({ 
  users: 0, 
  usersGrowth: '+0%',
  postsPending: 0, 
  postsGrowth: '+0%',
  sales: '₫0',
  salesGrowth: '+0%',
  activeUsers: 0,
  activeGrowth: '+0%'
})

const pendingPosts = ref([])
const recentActivities = ref([])
const loading = ref(false)

// Fetch dashboard statistics
async function fetchStats() {
  try {
    const response = await api.get('/admin/statistics/dashboard')
    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch stats:', err)
    console.error('Error response:', err.response?.data)
    const errorMsg = err.response?.data?.message || 'Không thể tải thống kê'
    showError(errorMsg)
  }
}

// Fetch pending posts
async function fetchPendingPosts() {
  try {
    const response = await api.get('/admin/posts?status=pending')
    if (response.data.success) {
      pendingPosts.value = response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch posts:', err)
    showError('Không thể tải danh sách bài đăng')
  }
}

// Fetch recent activities
async function fetchActivities() {
  try {
    const response = await api.get('/admin/activities/recent')
    if (response.data.success) {
      recentActivities.value = response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch activities:', err)
  }
}

// Approve post
async function approve(postId) {
  const post = pendingPosts.value.find(p => p.id === postId)
  if (!post) return

  try {
    const response = await api.put(`/admin/posts/${post.raw_id}/approve`)
    if (response.data.success) {
      post.status = 'approved'
      showSuccess('Bài đăng đã được phê duyệt')
      
      setTimeout(() => {
        pendingPosts.value = pendingPosts.value.filter(p => p.id !== postId)
        fetchStats() // Refresh stats
      }, 600)
    }
  } catch (err) {
    showError(err.response?.data?.message || 'Không thể phê duyệt bài đăng')
  }
}

// Reject post
async function reject(postId) {
  const post = pendingPosts.value.find(p => p.id === postId)
  if (!post) return

  try {
    const response = await api.put(`/admin/posts/${post.raw_id}/reject`)
    if (response.data.success) {
      post.status = 'rejected'
      showSuccess('Bài đăng đã bị từ chối')
      
      setTimeout(() => {
        pendingPosts.value = pendingPosts.value.filter(p => p.id !== postId)
        fetchStats() // Refresh stats
      }, 600)
    }
  } catch (err) {
    showError(err.response?.data?.message || 'Không thể từ chối bài đăng')
  }
}

// Refresh all data
async function refreshData() {
  loading.value = true
  await Promise.all([
    fetchStats(),
    fetchPendingPosts(),
    fetchActivities()
  ])
  loading.value = false
  showSuccess('Dữ liệu đã được làm mới')
}

// Load data on mount
onMounted(() => {
  fetchStats()
  fetchPendingPosts()
  fetchActivities()
})

const activeTab = ref('pending') // pending, approved, rejected

</script>

<template>
  <div class="page">
    <Header />
    <main class="content">
      <div class="container admin-page">
        <!-- Hero Header -->
        <div class="admin-hero">
          <div class="hero-content">
            <h1 class="hero-title">
              <span class="wave">👋</span> Chào mừng trở lại, 
              <span class="gradient-text">{{ user?.name || 'Admin' }}</span>
            </h1>
            <p class="hero-subtitle">Quản lý hệ thống VietMarket - Dashboard tổng quan</p>
          </div>
          <div class="hero-badge">
            <span class="badge-icon">🔒</span>
            <span>Admin Dashboard</span>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
          <div class="stat-card card-users">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
              <div class="stat-label">Tổng người dùng</div>
              <div class="stat-value">{{ stats.users.toLocaleString() }}</div>
              <div class="stat-growth positive">
                <span class="arrow">↗</span> {{ stats.usersGrowth }} so với tháng trước
              </div>
            </div>
            <div class="stat-sparkle">✨</div>
          </div>

          <div class="stat-card card-pending">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
              <div class="stat-label">Chờ phê duyệt</div>
              <div class="stat-value">{{ stats.postsPending }}</div>
              <div class="stat-growth negative">
                <span class="arrow">↘</span> {{ stats.postsGrowth }} so với tuần trước
              </div>
            </div>
            <div class="stat-sparkle">⚡</div>
          </div>

          <div class="stat-card card-sales">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
              <div class="stat-label">Doanh thu tháng này</div>
              <div class="stat-value">{{ stats.sales }}</div>
              <div class="stat-growth positive">
                <span class="arrow">↗</span> {{ stats.salesGrowth }} tăng trưởng
              </div>
            </div>
            <div class="stat-sparkle">🚀</div>
          </div>

          <div class="stat-card card-active">
            <div class="stat-icon">🎯</div>
            <div class="stat-content">
              <div class="stat-label">Người dùng hoạt động</div>
              <div class="stat-value">{{ stats.activeUsers }}</div>
              <div class="stat-growth positive">
                <span class="arrow">↗</span> {{ stats.activeGrowth }} hôm nay
              </div>
            </div>
            <div class="stat-sparkle">💫</div>
          </div>
        </div>

        <div class="dashboard-content">
          <!-- Pending Posts Section -->
          <section class="section pending-section">
            <div class="section-header">
              <div class="section-title">
                <h2>📋 Bài đăng chờ phê duyệt</h2>
                <span class="badge-count">{{ pendingPosts.length }}</span>
              </div>
              <button class="refresh-btn" @click="refreshData" :disabled="loading">
                <span class="refresh-icon">🔄</span> {{ loading ? 'Đang tải...' : 'Làm mới' }}
              </button>
            </div>
            
            <div v-if="pendingPosts.length" class="posts-list">
              <div 
                v-for="p in pendingPosts" 
                :key="p.id" 
                class="post-card"
                :class="{ 'approved': p.status === 'approved', 'rejected': p.status === 'rejected' }"
              >
                <div class="post-header">
                  <div class="post-id">ID: {{ p.id }}</div>
                  <div class="post-category">{{ p.category }}</div>
                </div>
                <div class="post-body">
                  <h3 class="post-title">{{ p.title }}</h3>
                  <div class="post-meta">
                    <span class="meta-item">
                      <span class="icon">👤</span> {{ p.author }}
                    </span>
                    <span class="meta-item">
                      <span class="icon">📅</span> {{ p.date }}
                    </span>
                    <span class="meta-item price">
                      <span class="icon">💵</span> {{ p.price }}
                    </span>
                  </div>
                </div>
                <div class="post-actions">
                  <button class="btn btn-approve" @click="approve(p.id)" :disabled="p.status !== 'pending'">
                    <span class="btn-icon">✓</span> Phê duyệt
                  </button>
                  <button class="btn btn-reject" @click="reject(p.id)" :disabled="p.status !== 'pending'">
                    <span class="btn-icon">✕</span> Từ chối
                  </button>
                  <button class="btn btn-view">
                    <span class="btn-icon">👁️</span> Xem chi tiết
                  </button>
                </div>
              </div>
            </div>
            <div v-else class="empty-state">
              <div class="empty-icon">🎉</div>
              <div class="empty-text">Tuyệt vời! Không có bài đăng nào chờ duyệt.</div>
            </div>
          </section>

          <!-- Recent Activities -->
          <aside class="section activity-section">
            <div class="section-header">
              <h2>⚡ Hoạt động gần đây</h2>
            </div>
            <div class="activity-list">
              <div v-for="(activity, idx) in recentActivities" :key="idx" class="activity-item">
                <div class="activity-dot"></div>
                <div class="activity-content">
                  <div class="activity-action">{{ activity.action }}</div>
                  <div class="activity-user">{{ activity.user }}</div>
                  <div class="activity-time">{{ activity.time }}</div>
                </div>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </main>
    <Footer />
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f8fafc;
}

.content {
  flex: 1;
  padding: 2rem 1rem 4rem;
  background: #f8fafc;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
}

/* Hero Header */
.admin-hero {
  background: white;
  padding: 3rem 2rem;
  border-radius: 24px;
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
  position: relative;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}



.hero-content {
  z-index: 1;
}

.hero-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.wave {
  display: inline-block;
  animation: wave 2s ease-in-out infinite;
}

@keyframes wave {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(20deg); }
  75% { transform: rotate(-10deg); }
}

.gradient-text {
  color: #667eea;
}

.hero-subtitle {
  color: #64748b;
  font-size: 1.1rem;
}

.hero-badge {
  background: #f1f5f9;
  padding: 1rem 2rem;
  border-radius: 50px;
  color: #475569;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border: 2px solid #e2e8f0;
  z-index: 1;
}

.badge-icon {
  font-size: 1.5rem;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
  position: relative;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

.stat-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: #667eea;
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.stat-card:hover::before {
  transform: scaleX(1);
}

.stat-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  display: inline-block;
  animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

.stat-label {
  color: #64748b;
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 2.5rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.stat-growth {
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.stat-growth.positive {
  color: #10b981;
}

.stat-growth.negative {
  color: #ef4444;
}

.arrow {
  font-size: 1.2rem;
}

.stat-sparkle {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 2rem;
  opacity: 0.3;
  animation: sparkle 3s ease-in-out infinite;
}

@keyframes sparkle {
  0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.3; }
  50% { transform: scale(1.2) rotate(180deg); opacity: 0.6; }
}

.card-users { border-top: 4px solid #3b82f6; }
.card-pending { border-top: 4px solid #f59e0b; }
.card-sales { border-top: 4px solid #10b981; }
.card-active { border-top: 4px solid #8b5cf6; }

/* Dashboard Content */
.dashboard-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

.section {
  background: white;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f1f5f9;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.section-title h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0f172a;
}

.badge-count {
  background: #667eea;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 700;
}

.refresh-btn {
  background: #f1f5f9;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #475569;
  transition: all 0.2s;
}

.refresh-btn:hover {
  background: #e2e8f0;
  transform: translateY(-2px);
}

.refresh-icon {
  display: inline-block;
  transition: transform 0.3s;
}

.refresh-btn:hover .refresh-icon {
  transform: rotate(180deg);
}

/* Posts List */
.posts-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.post-card {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  padding: 1.5rem;
  border-radius: 16px;
  border: 2px solid transparent;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.post-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: #667eea;
  transform: scaleY(0);
  transition: transform 0.3s ease;
}

.post-card:hover::before {
  transform: scaleY(1);
}

.post-card:hover {
  transform: translateX(8px);
  border-color: #667eea;
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.post-card.approved {
  background: linear-gradient(135deg, #d1fae5, #a7f3d0);
  animation: approve 0.6s ease;
}

.post-card.rejected {
  background: linear-gradient(135deg, #fee2e2, #fecaca);
  animation: reject 0.6s ease;
}

@keyframes approve {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); opacity: 0; }
}

@keyframes reject {
  0% { transform: scale(1); }
  50% { transform: scale(0.95) rotate(2deg); }
  100% { transform: scale(1) rotate(0); opacity: 0; }
}

.post-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.post-id {
  font-size: 0.85rem;
  color: #64748b;
  font-weight: 600;
}

.post-category {
  background: #667eea;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.post-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.75rem;
}

.post-meta {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.9rem;
  color: #64748b;
}

.meta-item.price {
  color: #10b981;
  font-weight: 700;
}

.icon {
  font-size: 1rem;
}

.post-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
}

.btn {
  padding: 0.75rem 1.25rem;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s;
  font-size: 0.9rem;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-approve {
  background: #10b981;
  color: white;
}

.btn-approve:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
}

.btn-reject {
  background: #ef4444;
  color: white;
}

.btn-reject:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
}

.btn-view {
  background: white;
  border: 2px solid #e2e8f0;
  color: #475569;
}

.btn-view:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.btn-icon {
  font-size: 1rem;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  animation: bounce 2s ease-in-out infinite;
}

.empty-text {
  font-size: 1.1rem;
  color: #64748b;
  font-weight: 600;
}

/* Activity Section */
.activity-section {
  max-height: 600px;
  overflow-y: auto;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.activity-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 12px;
  transition: all 0.2s;
  position: relative;
}

.activity-item:hover {
  background: #f1f5f9;
  transform: translateX(4px);
}

.activity-dot {
  width: 12px;
  height: 12px;
  background: #667eea;
  border-radius: 50%;
  margin-top: 0.25rem;
  flex-shrink: 0;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.2); opacity: 0.8; }
}

.activity-action {
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 0.25rem;
}

.activity-user {
  color: #64748b;
  font-size: 0.9rem;
}

.activity-time {
  color: #94a3b8;
  font-size: 0.85rem;
  margin-top: 0.25rem;
}

/* Responsive */
@media (max-width: 1024px) {
  .dashboard-content {
    grid-template-columns: 1fr;
  }
  
  .hero-title {
    font-size: 2rem;
  }
  
  .admin-hero {
    flex-direction: column;
    gap: 1.5rem;
    text-align: center;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .post-actions {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
