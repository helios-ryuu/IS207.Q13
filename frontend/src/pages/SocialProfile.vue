<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '../utils/useAuth'
import Header from '../components/Header-HomePage.vue'
import Footer from '../components/Footer.vue'

const router = useRouter()
const route = useRoute()
const { user } = useAuth()

// tabs
const tab = ref('posts') // posts | about | photos
const isFollowing = ref(false)
function toggleFollow(){ isFollowing.value = !isFollowing.value }
function goTab(name){ tab.value = name }

// simple heuristic: if route has a param `id` or query `user` and it's different from current user, treat as viewing other
const isMine = computed(() => {
  if (!user || !user.value) return false
  const id = route.params.id || route.query.user || route.query.view
  if (!id) return true // no id -> own profile
  // compare against common user fields
  const u = user.value
  const matches = [u.id, u.email, u.name].filter(Boolean).map(String)
  return matches.includes(String(id))
})

function editProfile(){ router.push('/profile/edit') }

function uploadCover() {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  input.onchange = (e) => {
    const file = e.target.files[0]
    if (file) {
      console.log('Cover uploaded:', file)
      // TODO: Handle actual upload
    }
  }
  input.click()
}

const posts = ref([
  { id: 'S1', text: 'Mình bán 1 chiếc bàn gỗ, liên hệ nhé!', date: '2025-11-05', likes: 12, comments: 3, image: true },
  { id: 'S2', text: 'Ai biết chỗ sửa xe gần Quận 3 không?', date: '2025-10-28', likes: 3, comments: 1 },
  { id: 'S3', text: 'Cần tìm người share xe đi làm từ Quận 1 đến Quận 7, giá mềm nha!', date: '2025-10-20', likes: 8, comments: 5 },
])

const photos = ref([
  { id: 'PH1' }, { id: 'PH2' }, { id: 'PH3' }, { id: 'PH4' }, { id: 'PH5' }, { id: 'PH6' }
])

const badges = ref([
  { icon: '⭐', label: 'Top Seller', color: '#f59e0b' },
  { icon: '✓', label: 'Verified', color: '#10b981' },
  { icon: '🔥', label: 'Active', color: '#ef4444' }
])

const stats = computed(()=>({ 
  posts: posts.value.length, 
  followers: 2100, 
  following: 180,
  rating: 4.8,
  transactions: 156
}))
</script>

<template>
  <div>
    <Header />

    <div class="social-page container">
      <div class="top-return"><router-link to="/home">← Trở về trang chủ</router-link></div>

      <section class="profile-hero">
        <div class="cover">
          <button v-if="isMine" class="upload-cover-btn" @click="uploadCover">
            <span class="icon">📷</span>
            <span class="text">Thay đổi ảnh bìa</span>
          </button>
        </div>
        <div class="hero-inner">
          <div class="avatar-wrapper">
            <div class="avatar">{{ user?.name ? user.name.charAt(0) : 'U' }}</div>
            <div class="avatar-ring"></div>
          </div>
          <div class="meta">
            <div class="name-row">
              <div class="name">{{ user?.name || 'Người dùng' }}</div>
              <div class="badges">
                <div v-for="badge in badges" :key="badge.label" 
                     class="badge" 
                     :style="{ '--badge-color': badge.color }">
                  <span class="badge-icon">{{ badge.icon }}</span>
                  <span class="badge-label">{{ badge.label }}</span>
                </div>
              </div>
            </div>
            <div class="handle">@{{ (user?.name || 'user').toLowerCase().replace(/\s+/g,'') }}</div>
            <div class="bio">Chào! Đây là trang cá nhân của tôi. Tôi bán đồ cũ, trao đổi ở khu vực TP.HCM. 🏠</div>
            <div class="rating-row">
              <div class="rating">
                <span class="stars">⭐⭐⭐⭐⭐</span>
                <span class="rating-value">{{ stats.rating }}/5.0</span>
              </div>
              <div class="transactions">{{ stats.transactions }} giao dịch thành công</div>
            </div>
          </div>
          <div class="actions">
          <button v-if="isMine" class="btn primary edit" @click="editProfile">
            <span class="btn-icon">✏️</span>
            Chỉnh sửa
          </button>
          <template v-else>
            <button class="btn primary" @click="toggleFollow">
              <span class="btn-icon">{{ isFollowing ? '✓' : '+' }}</span>
              {{ isFollowing ? 'Đã theo dõi' : 'Theo dõi' }}
            </button>
            <button class="btn outline" @click.prevent="router.push('/messages')">
              <span class="btn-icon">💬</span>
              Nhắn tin
            </button>
          </template>
          </div>
        </div>

        <div class="stats-row">
          <div class="stat">
            <div class="stat-icon">📝</div>
            <div class="num">{{ stats.posts }}</div>
            <div class="label">Bài đăng</div>
          </div>
          <div class="stat">
            <div class="stat-icon">👥</div>
            <div class="num">{{ stats.followers.toLocaleString() }}</div>
            <div class="label">Người theo dõi</div>
          </div>
          <div class="stat">
            <div class="stat-icon">➕</div>
            <div class="num">{{ stats.following }}</div>
            <div class="label">Đang theo dõi</div>
          </div>
        </div>
      </section>

      <nav class="tabs">
        <button :class="{active: tab==='posts'}" @click="goTab('posts')">Bài viết</button>
        <button :class="{active: tab==='about'}" @click="goTab('about')">Giới thiệu</button>
        <button :class="{active: tab==='photos'}" @click="goTab('photos')">Ảnh</button>
      </nav>

      <main class="tab-content">
        <div v-show="tab==='posts'" class="posts">
          <div v-for="p in posts" :key="p.id" class="post card">
            <div class="post-head">
              <div class="avatar-sm">{{ user?.name ? user.name.charAt(0) : 'U' }}</div>
              <div class="post-meta">
                <div class="name">{{ user?.name || 'Người dùng' }}</div>
                <div class="date">{{ p.date }}</div>
              </div>
              <div class="post-menu">⋯</div>
            </div>
            <div class="post-body">{{ p.text }}</div>
            <div v-if="p.image" class="post-image"></div>
            <div class="post-footer">
              <div class="action-btn">
                <span class="icon">❤️</span>
                <span class="count">{{ p.likes }}</span>
              </div>
              <div class="action-btn">
                <span class="icon">💬</span>
                <span class="count">{{ p.comments }}</span>
              </div>
              <div class="action-btn">
                <span class="icon">🔗</span>
                <span class="count">Chia sẻ</span>
              </div>
            </div>
          </div>
        </div>

        <div v-show="tab==='about'" class="about card">
          <h3>Giới thiệu</h3>
          <p>Tôi là một người dùng hoạt động tại TP.HCM, chuyên bán và trao đổi đồ cũ. Liên hệ: 0123456789</p>
        </div>

        <div v-show="tab==='photos'" class="photos">
          <div class="grid">
            <div v-for="ph in photos" :key="ph.id" class="photo"></div>
          </div>
        </div>
      </main>
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
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.social-page {
  background: #f8fafc;
  min-height: 100vh;
}

.top-return {
  margin-bottom: 1.5rem;
}

.top-return a {
  color: #64748b;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  transition: color 0.2s;
}

.top-return a:hover {
  color: #3b82f6;
}

/* Profile Hero */
.profile-hero {
  background: white;
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
  margin-bottom: 2rem;
  overflow: hidden;
  animation: slideUp 0.6s ease;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.cover {
  height: 280px;
  background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
  position: relative;
  overflow: hidden;
}

.upload-cover-btn {
  position: absolute;
  bottom: 1rem;
  right: 1rem;
  background: rgba(255, 255, 255, 0.95);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #0f172a;
  transition: all 0.3s ease;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.upload-cover-btn:hover {
  background: white;
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
}

.upload-cover-btn .icon {
  font-size: 18px;
}

.upload-cover-btn .text {
  font-size: 14px;
}

.hero-inner {
  padding: 2rem 2.5rem;
  display: flex;
  gap: 2rem;
  align-items: flex-start;
  position: relative;
  margin-top: 0;
}

.avatar-wrapper {
  position: relative;
  animation: bounceIn 0.8s ease;
}

@keyframes bounceIn {
  0% { transform: scale(0.3); opacity: 0; }
  50% { transform: scale(1.05); }
  70% { transform: scale(0.9); }
  100% { transform: scale(1); opacity: 1; }
}

.avatar {
  width: 140px;
  height: 140px;
  border-radius: 24px;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 56px;
  font-weight: 800;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  border: 5px solid white;
  color: #3b82f6;
  position: relative;
  z-index: 2;
}

.avatar-ring {
  position: absolute;
  top: -8px;
  left: -8px;
  right: -8px;
  bottom: -8px;
  border-radius: 28px;
  border: 3px solid #3b82f6;
  opacity: 0;
  animation: ringPulse 2s ease-in-out infinite;
}

@keyframes ringPulse {
  0%, 100% { opacity: 0; transform: scale(1); }
  50% { opacity: 0.6; transform: scale(1.05); }
}

.meta {
  flex: 1;
  padding-top: 1rem;
}

.name-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 0.5rem;
}

.name {
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
}

.badges {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.badge {
  background: white;
  border: 2px solid var(--badge-color);
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 12px;
  font-weight: 700;
  color: var(--badge-color);
  animation: badgeBounce 0.5s ease;
  transition: all 0.3s ease;
}

.badge:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

@keyframes badgeBounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

.badge-icon {
  font-size: 14px;
}

.handle {
  color: #64748b;
  font-size: 16px;
  margin-bottom: 1rem;
  font-weight: 500;
}

.bio {
  color: #475569;
  line-height: 1.6;
  margin-bottom: 1rem;
  max-width: 600px;
}

.rating-row {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.stars {
  font-size: 16px;
  letter-spacing: 2px;
}

.rating-value {
  font-weight: 700;
  color: #f59e0b;
  font-size: 16px;
}

.transactions {
  color: #64748b;
  font-size: 14px;
  font-weight: 600;
}

.actions {
  padding-top: 1rem;
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn {
  padding: 0.875rem 1.75rem;
  border-radius: 16px;
  border: none;
  cursor: pointer;
  font-weight: 700;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.btn::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
  width: 300px;
  height: 300px;
}

.btn-icon {
  font-size: 16px;
  position: relative;
  z-index: 1;
}

.btn.primary {
  background: #3b82f6;
  color: white;
  box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
}

.btn.primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(59, 130, 246, 0.4);
}

.btn.primary.edit {
  background: #10b981;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
}

.btn.primary.edit:hover {
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.4);
}

.btn.outline {
  background: white;
  border: 2px solid #e2e8f0;
  color: #475569;
}

.btn.outline:hover {
  border-color: #3b82f6;
  color: #3b82f6;
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(59, 130, 246, 0.15);
}

/* Stats Row */
.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  padding: 0 2.5rem 2rem;
}

.stat {
  background: white;
  padding: 1.5rem;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.stat:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  border-color: #3b82f6;
}

.stat-icon {
  font-size: 32px;
  animation: iconFloat 3s ease-in-out infinite;
}

@keyframes iconFloat {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

.stat .num {
  font-weight: 800;
  font-size: 28px;
  color: #0f172a;
}

.stat .label {
  color: #64748b;
  font-size: 14px;
  font-weight: 600;
}

/* Tabs */
.tabs {
  display: flex;
  gap: 0.5rem;
  margin: 2rem 0 1.5rem;
  border-bottom: 2px solid #e2e8f0;
  padding: 0 0.5rem;
}

.tabs button {
  padding: 1rem 1.5rem;
  border: none;
  background: transparent;
  cursor: pointer;
  color: #64748b;
  font-weight: 700;
  font-size: 15px;
  position: relative;
  transition: all 0.3s ease;
  border-radius: 12px 12px 0 0;
}

.tabs button:hover {
  color: #3b82f6;
  background: rgba(59, 130, 246, 0.05);
}

.tabs button.active {
  color: #3b82f6;
  background: rgba(59, 130, 246, 0.08);
}

.tabs button.active::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 3px;
  background: #3b82f6;
  border-radius: 3px 3px 0 0;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    transform: scaleX(0);
  }
  to {
    transform: scaleX(1);
  }
}

/* Tab Content */
.tab-content {
  display: flex;
  gap: 2rem;
}

.posts {
  flex: 1;
}

.card {
  background: white;
  padding: 1.5rem;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
  margin-bottom: 1.5rem;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
  border-color: #3b82f6;
}

.post-head {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1rem;
}

.avatar-sm {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #3b82f6;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
  flex-shrink: 0;
}

.post-meta {
  flex: 1;
}

.post-meta .name {
  font-weight: 700;
  color: #0f172a;
  font-size: 15px;
}

.post-meta .date {
  color: #94a3b8;
  font-size: 13px;
  margin-top: 2px;
}

.post-menu {
  cursor: pointer;
  color: #94a3b8;
  font-size: 20px;
  padding: 0.5rem;
  transition: all 0.2s;
}

.post-menu:hover {
  color: #3b82f6;
  transform: scale(1.2);
}

.post-body {
  color: #334155;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.post-image {
  width: 100%;
  height: 300px;
  background: #e2e8f0;
  border-radius: 16px;
  margin-bottom: 1rem;
}

.post-footer {
  display: flex;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.action-btn:hover {
  background: #3b82f6;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
}

.action-btn .icon {
  font-size: 16px;
}

.action-btn .count {
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
}

.action-btn:hover .count {
  color: white;
}

/* About & Photos */
.about {
  background: white;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
}

.about h3 {
  margin-bottom: 1rem;
  color: #0f172a;
  font-size: 20px;
}

.about p {
  color: #475569;
  line-height: 1.6;
}

.photos .grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.photo {
  height: 200px;
  background: #e2e8f0;
  border-radius: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.photo::before {
  content: '🖼️';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 48px;
  opacity: 0.3;
  transition: all 0.3s ease;
}

.photo:hover {
  transform: scale(1.05) rotate(2deg);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
}

.photo:hover::before {
  opacity: 0.6;
  transform: translate(-50%, -50%) scale(1.2);
}

/* Responsive */
@media (max-width: 900px) {
  .hero-inner {
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 0 1.5rem 1.5rem;
  }

  .actions {
    justify-content: center;
  }

  .name-row {
    justify-content: center;
  }

  .stats-row {
    grid-template-columns: repeat(3, 1fr);
    padding: 0 1.5rem 1.5rem;
  }

  .tab-content {
    flex-direction: column;
  }

  .photos .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>

