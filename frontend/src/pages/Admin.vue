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

// Modal state
const showModal = ref(false)
const selectedProduct = ref(null)
const processingAction = ref(false)

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
    const response = await api.get('/admin/products/pending')
    if (response.data.success) {
      pendingPosts.value = response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch pending products:', err)
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

// Open modal for review
function openReviewModal(product) {
  selectedProduct.value = product
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedProduct.value = null
}

async function approveProduct() {
  if (!selectedProduct.value || processingAction.value) return
  
  processingAction.value = true
  try {
    const response = await api.put(`/admin/products/${selectedProduct.value.id}/approve`)
    if (response.data.success) {
      showSuccess('Sản phẩm đã được phê duyệt thành công!')
      pendingPosts.value = pendingPosts.value.filter(p => p.id !== selectedProduct.value.id)
      closeModal()
      fetchStats()
    }
  } catch (err) {
    showError(err.response?.data?.message || 'Không thể phê duyệt sản phẩm')
  } finally {
    processingAction.value = false
  }
}

async function rejectProduct() {
  if (!selectedProduct.value || processingAction.value) return
  
  processingAction.value = true
  try {
    const response = await api.put(`/admin/products/${selectedProduct.value.id}/reject`)
    if (response.data.success) {
      showSuccess('Sản phẩm đã bị từ chối')
      pendingPosts.value = pendingPosts.value.filter(p => p.id !== selectedProduct.value.id)
      closeModal()
      fetchStats()
    }
  } catch (err) {
    showError(err.response?.data?.message || 'Không thể từ chối sản phẩm')
  } finally {
    processingAction.value = false
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
              <h2>Sản phẩm chờ duyệt <span class="badge">{{ pendingPosts.length }}</span></h2>
              <button class="btn-secondary" @click="refreshData" :disabled="loading">
                {{ loading ? 'Đang tải...' : 'Làm mới' }}
              </button>
            </div>
            
            <div v-if="pendingPosts.length" class="posts-list">
              <div v-for="p in pendingPosts" :key="p.id" class="post-item">
                <div class="post-image" v-if="p.image">
                  <img :src="p.image" :alt="p.title" @error="$event.target.style.display='none'" />
                </div>
                <div class="post-info">
                  <div class="post-title">{{ p.title }}</div>
                  <div class="post-meta">{{ p.author }} • {{ p.date }} • {{ p.price }}</div>
                  <div class="post-location">📍 {{ p.location }}</div>
                </div>
                <div class="post-actions">
                  <button class="btn-review" @click="openReviewModal(p)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Xét duyệt
                  </button>
                </div>
              </div>
            </div>
            <div v-else class="empty">Không có sản phẩm chờ duyệt</div>
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

    <!-- Review Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Xét duyệt sản phẩm</h3>
          <button class="modal-close" @click="closeModal">&times;</button>
        </div>
        
        <div class="modal-body" v-if="selectedProduct">
          <!-- Product Preview -->
          <div class="product-preview">
            <div class="preview-image" v-if="selectedProduct.image">
              <img :src="selectedProduct.image" :alt="selectedProduct.title" />
            </div>
            <div class="preview-image placeholder" v-else>
              <span>Không có ảnh</span>
            </div>
            
            <div class="preview-info">
              <h4>{{ selectedProduct.title }}</h4>
              <div class="preview-price">{{ selectedProduct.price }}</div>
              <div class="preview-meta">
                <div><strong>Người đăng:</strong> {{ selectedProduct.author }}</div>
                <div><strong>Ngày đăng:</strong> {{ selectedProduct.date }}</div>
                <div><strong>Danh mục:</strong> {{ selectedProduct.category }}</div>
                <div><strong>Khu vực:</strong> {{ selectedProduct.location }}</div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button class="btn-modal-reject" @click="rejectProduct" :disabled="processingAction">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            {{ processingAction ? 'Đang xử lý...' : 'Từ chối bài đăng' }}
          </button>
          <button class="btn-modal-approve" @click="approveProduct" :disabled="processingAction">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            {{ processingAction ? 'Đang xử lý...' : 'Xác nhận duyệt' }}
          </button>
        </div>
      </div>
    </div>
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
  align-items: center;
  padding: 12px;
  background: #f8fafc;
  border-radius: 8px;
  gap: 12px;
}

.post-item:hover { background: #f1f5f9; }

.post-image {
  width: 60px;
  height: 60px;
  border-radius: 6px;
  overflow: hidden;
  flex-shrink: 0;
  background: #e2e8f0;
}

.post-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.post-info {
  flex: 1;
}

.post-title {
  font-weight: 500;
  color: #1e293b;
  margin-bottom: 4px;
}

.post-meta {
  font-size: 12px;
  color: #64748b;
}

.post-location {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.post-actions {
  display: flex;
  gap: 8px;
}

.btn-review {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  font-weight: 500;
  background: #6366f1;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s;
}

.btn-review:hover {
  background: #4f46e5;
}

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

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.modal-close {
  width: 32px;
  height: 32px;
  border: none;
  background: #f1f5f9;
  border-radius: 50%;
  font-size: 20px;
  cursor: pointer;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  background: #e2e8f0;
  color: #1e293b;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
}

/* Product Preview */
.product-preview {
  display: flex;
  gap: 20px;
}

.preview-image {
  width: 200px;
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
  flex-shrink: 0;
  background: #f1f5f9;
}

.preview-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.preview-image.placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  font-size: 14px;
}

.preview-info {
  flex: 1;
}

.preview-info h4 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 8px 0;
  line-height: 1.4;
}

.preview-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: #dc2626;
  margin-bottom: 16px;
}

.preview-meta {
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-size: 14px;
  color: #64748b;
}

.preview-meta strong {
  color: #475569;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
}

.btn-modal-reject, .btn-modal-approve {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}

.btn-modal-reject {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.btn-modal-reject:hover {
  background: #fee2e2;
}

.btn-modal-approve {
  background: #10b981;
  color: #fff;
}

.btn-modal-approve:hover {
  background: #059669;
}

.btn-modal-reject:disabled, .btn-modal-approve:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 1024px) {
  .dashboard-grid { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .post-item { flex-direction: column; align-items: flex-start; }
  .post-actions { width: 100%; margin-top: 8px; }
  .btn-review { width: 100%; justify-content: center; }
  
  .product-preview { flex-direction: column; }
  .preview-image { width: 100%; height: 200px; }
  
  .modal-footer { flex-direction: column; }
  .btn-modal-reject, .btn-modal-approve { width: 100%; justify-content: center; }
}
</style>

