<script setup>
import { ref, onMounted } from 'vue'
import { useAuth } from '../utils/useAuth'
import { useToast } from '../utils/useToast'
import api from '../utils/api'
import AdminHeader from '../components/layout/AdminHeader.vue'
import AdminFooter from '../components/layout/AdminFooter.vue'

const { user } = useAuth()
const { showSuccess, showError } = useToast()

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

async function fetchStats() {
  try {
    const response = await api.get('/admin/statistics/dashboard')
    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch stats:', err)
  }
}

async function fetchPendingPosts() {
  try {
    const response = await api.get('/admin/posts?status=pending')
    if (response.data.success) {
      pendingPosts.value = response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch posts:', err)
  }
}

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

async function approve(postId) {
  const post = pendingPosts.value.find(p => p.id === postId)
  if (!post) return
  try {
    const response = await api.put(`/admin/posts/${post.raw_id}/approve`)
    if (response.data.success) {
      showSuccess('Bài đăng đã được phê duyệt')
      pendingPosts.value = pendingPosts.value.filter(p => p.id !== postId)
      fetchStats()
    }
  } catch (err) {
    showError(err.response?.data?.message || 'Không thể phê duyệt')
  }
}

async function reject(postId) {
  const post = pendingPosts.value.find(p => p.id === postId)
  if (!post) return
  try {
    const response = await api.put(`/admin/posts/${post.raw_id}/reject`)
    if (response.data.success) {
      showSuccess('Bài đăng đã bị từ chối')
      pendingPosts.value = pendingPosts.value.filter(p => p.id !== postId)
      fetchStats()
    }
  } catch (err) {
    showError(err.response?.data?.message || 'Không thể từ chối')
  }
}

async function refreshData() {
  loading.value = true
  await Promise.all([fetchStats(), fetchPendingPosts(), fetchActivities()])
  loading.value = false
  showSuccess('Đã làm mới dữ liệu')
}

onMounted(() => {
  fetchStats()
  fetchPendingPosts()
  fetchActivities()
})
</script>

<template>
  <div class="page">
    <AdminHeader />
    <main class="content">
      <div class="container">
        <!-- Header -->
        <div class="page-header">
          <h1>Chào mừng, {{ user?.name || 'Admin' }}</h1>
          <p>Quản lý hệ thống VietMarket</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-label">Tổng người dùng</div>
            <div class="stat-value">{{ stats.users.toLocaleString() }}</div>
            <div class="stat-growth positive">{{ stats.usersGrowth }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Chờ phê duyệt</div>
            <div class="stat-value">{{ stats.postsPending }}</div>
            <div class="stat-growth">{{ stats.postsGrowth }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Doanh thu tháng</div>
            <div class="stat-value">{{ stats.sales }}</div>
            <div class="stat-growth positive">{{ stats.salesGrowth }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Người dùng hoạt động</div>
            <div class="stat-value">{{ stats.activeUsers }}</div>
            <div class="stat-growth positive">{{ stats.activeGrowth }}</div>
          </div>
        </div>

        <!-- Main content -->
        <div class="dashboard-grid">
          <!-- Pending posts -->
          <section class="card">
            <div class="card-header">
              <h2>Bài đăng chờ duyệt <span class="badge">{{ pendingPosts.length }}</span></h2>
              <button class="btn-secondary" @click="refreshData" :disabled="loading">
                {{ loading ? 'Đang tải...' : 'Làm mới' }}
              </button>
            </div>
            
            <div v-if="pendingPosts.length" class="posts-list">
              <div v-for="p in pendingPosts" :key="p.id" class="post-item">
                <div class="post-info">
                  <div class="post-title">{{ p.title }}</div>
                  <div class="post-meta">{{ p.author }} • {{ p.date }} • {{ p.price }}</div>
                </div>
                <div class="post-actions">
                  <button class="btn-approve" @click="approve(p.id)">Duyệt</button>
                  <button class="btn-reject" @click="reject(p.id)">Từ chối</button>
                </div>
              </div>
            </div>
            <div v-else class="empty">Không có bài đăng chờ duyệt</div>
          </section>

          <!-- Activities -->
          <aside class="card">
            <div class="card-header">
              <h2>Hoạt động gần đây</h2>
            </div>
            <div class="activity-list">
              <div v-for="(a, i) in recentActivities" :key="i" class="activity-item">
                <div class="activity-action">{{ a.action }}</div>
                <div class="activity-meta">{{ a.user }} • {{ a.time }}</div>
              </div>
              <div v-if="!recentActivities.length" class="empty">Chưa có hoạt động</div>
            </div>
          </aside>
        </div>
      </div>
    </main>
    <AdminFooter />
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
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 24px;
}

.page-header h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.page-header p {
  color: #64748b;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.stat-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.stat-label {
  font-size: 13px;
  color: #64748b;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
}

.stat-growth {
  font-size: 12px;
  color: #64748b;
  margin-top: 4px;
}

.stat-growth.positive { color: #10b981; }

/* Dashboard */
.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
}

.card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #f1f5f9;
}

.card-header h2 {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
}

.badge {
  background: #6366f1;
  color: #fff;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  margin-left: 8px;
}

/* Buttons */
.btn-secondary {
  background: #f1f5f9;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  color: #475569;
}

.btn-secondary:hover { background: #e2e8f0; }
.btn-secondary:disabled { opacity: 0.6; cursor: not-allowed; }

/* Posts */
.posts-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.post-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: #f8fafc;
  border-radius: 8px;
}

.post-item:hover { background: #f1f5f9; }

.post-title {
  font-weight: 500;
  color: #1e293b;
  margin-bottom: 4px;
}

.post-meta {
  font-size: 12px;
  color: #64748b;
}

.post-actions {
  display: flex;
  gap: 8px;
}

.btn-approve, .btn-reject {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  font-weight: 500;
}

.btn-approve { background: #10b981; color: #fff; }
.btn-approve:hover { background: #059669; }

.btn-reject { background: #ef4444; color: #fff; }
.btn-reject:hover { background: #dc2626; }

/* Activity */
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.activity-item {
  padding: 10px;
  background: #f8fafc;
  border-radius: 6px;
}

.activity-action {
  font-weight: 500;
  color: #1e293b;
  margin-bottom: 2px;
}

.activity-meta {
  font-size: 12px;
  color: #94a3b8;
}

.empty {
  text-align: center;
  padding: 24px;
  color: #94a3b8;
}

/* Responsive */
@media (max-width: 1024px) {
  .dashboard-grid { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .post-item { flex-direction: column; align-items: flex-start; gap: 12px; }
  .post-actions { width: 100%; }
  .btn-approve, .btn-reject { flex: 1; }
}
</style>
