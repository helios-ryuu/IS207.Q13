<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../../utils/useAuth'

const router = useRouter()
const { user, isLoggedIn, logout } = useAuth()
const isMenuOpen = ref(false)

function handleLogout() {
  logout()
  router.push('/login')
  isMenuOpen.value = false
}

function toggleMenu() {
  isMenuOpen.value = !isMenuOpen.value
}

function handleClickOutside(e) {
  if (isMenuOpen.value && !e.target.closest('.user-menu')) {
    isMenuOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<template>
  <header class="admin-header">
    <div class="header-content">
      <router-link to="/home" class="logo">
        <img src="/logo-2.jpg" alt="VietMarket" />
        <span>VietMarket</span>
      </router-link>
      
      <div class="user-menu">
        <button class="avatar-btn" @click.stop="toggleMenu">
          <img 
            :src="user?.avatar_url || '/avatar.jpg'" 
            alt="Avatar" 
            class="avatar"
          />
          <span class="user-name">{{ user?.full_name || user?.name || 'Admin' }}</span>
        </button>
        
        <div v-if="isMenuOpen" class="dropdown">
          <router-link to="/profile" class="dropdown-item">Hồ sơ</router-link>
          <button @click="handleLogout" class="dropdown-item logout">Đăng xuất</button>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
.admin-header {
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
  padding: 0 24px;
  height: 64px;
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  height: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  color: #1e293b;
  font-weight: 700;
  font-size: 1.25rem;
}

.logo img {
  width: 40px;
  height: 40px;
  border-radius: 8px;
}

.user-menu {
  position: relative;
}

.avatar-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px 12px;
  border-radius: 8px;
}

.avatar-btn:hover {
  background: #f1f5f9;
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
}

.user-name {
  font-weight: 500;
  color: #334155;
}

.dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 8px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  min-width: 160px;
  z-index: 100;
}

.dropdown-item {
  display: block;
  width: 100%;
  padding: 10px 16px;
  text-align: left;
  background: none;
  border: none;
  cursor: pointer;
  color: #334155;
  text-decoration: none;
  font-size: 14px;
}

.dropdown-item:hover {
  background: #f1f5f9;
}

.dropdown-item.logout {
  color: #dc2626;
  border-top: 1px solid #e2e8f0;
}
</style>
