<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../utils/useAuth'

const router = useRouter()
const { user, logout } = useAuth()

const section = ref('overview')

function go(sectionName){ section.value = sectionName }

function doLogout(){
  logout()
  router.push('/login')
}

// Mock data for demonstration — replace with real API data when available
const orders = ref([
  { id: 'OD1001', date: '2025-11-01', total: '₫1,200,000', status: 'Đã giao' },
  { id: 'OD1002', date: '2025-10-21', total: '₫250,000', status: 'Đang vận chuyển' },
])

const addresses = ref([
  { id:1, label: 'Nhà riêng', line: '123 Đường A, Quận 1, TP.HCM' },
  { id:2, label: 'Cơ quan', line: 'Toà nhà B, Quận 3, TP.HCM' }
])

const paymentMethods = ref([
  { id:1, brand: 'Visa', last4: '4242', holder: 'Nguyen Van A' },
  { id:2, brand: 'Momo', last4: '****', holder: 'Nguyen Van A' }
])

</script>

<template>
  <div class="profile-page">
    <div class="top-return">
      <router-link to="/home">Trở về trang chủ</router-link>
    </div>
    <div class="profile-layout">
      <aside class="sidebar">
        <div class="userbox">
          <div class="avatar">{{ user?.name ? user.name.charAt(0) : 'U' }}</div>
          <div class="info">
            <div class="name">{{ user?.name || 'Người dùng' }}</div>
            <div class="email">{{ user?.email || '' }}</div>
          </div>
        </div>

        <nav class="menu">
          <button :class="{active: section==='overview'}" @click="go('overview')">Tổng quan</button>
          <button :class="{active: section==='orders'}" @click="go('orders')">Đơn hàng</button>
          <button :class="{active: section==='addresses'}" @click="go('addresses')">Địa chỉ</button>
          <button :class="{active: section==='payments'}" @click="go('payments')">Phương thức thanh toán</button>
          <button :class="{active: section==='security'}" @click="go('security')">Bảo mật</button>
        </nav>

        <div class="sidebar-foot">
          <button class="logout" @click="doLogout">Đăng xuất</button>
        </div>
      </aside>

      <main class="content">
        <section v-show="section==='overview'">
          <h2>Tổng quan</h2>
          <p>Xin chào, <strong>{{ user?.name || 'Người dùng' }}</strong>. Đây là trang quản lý tài khoản của bạn.</p>
          <div class="card">
            <h3>Thông tin</h3>
            <p><strong>Email:</strong> {{ user?.email || '-' }}</p>
            <p><strong>Tên:</strong> {{ user?.name || '-' }}</p>
          </div>
        </section>

        <section v-show="section==='orders'">
          <h2>Đơn hàng</h2>
          <div v-if="orders.length" class="orders">
            <div v-for="o in orders" :key="o.id" class="order">
              <div><strong>{{ o.id }}</strong></div>
              <div>{{ o.date }}</div>
              <div>{{ o.total }}</div>
              <div class="status">{{ o.status }}</div>
            </div>
          </div>
          <div v-else>Không có đơn hàng.</div>
        </section>

        <section v-show="section==='addresses'">
          <h2>Địa chỉ</h2>
          <div v-for="a in addresses" :key="a.id" class="addr">
            <div class="addr-label">{{ a.label }}</div>
            <div class="addr-line">{{ a.line }}</div>
          </div>
          <button class="btn small">Thêm địa chỉ</button>
        </section>

        <section v-show="section==='payments'">
          <h2>Phương thức thanh toán</h2>
          <div v-for="p in paymentMethods" :key="p.id" class="pay">
            <div>{{ p.brand }} •••• {{ p.last4 }}</div>
            <div>{{ p.holder }}</div>
          </div>
          <button class="btn small">Thêm phương thức</button>
        </section>

        <section v-show="section==='security'">
          <h2>Bảo mật</h2>
          <div class="card">
            <p>Đổi mật khẩu</p>
            <button class="btn small">Thay đổi mật khẩu</button>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<style scoped>
.profile-page{ min-height:100vh; background: #f6f9ff; padding:32px; box-sizing:border-box }
.profile-layout{ display:flex; gap:24px; max-width:1200px; margin:0 auto }
.top-return{ margin-bottom:12px; display:flex; align-items:center }
.top-return a{ color:#374151; text-decoration:none; font-size:14px; font-weight:500 }
.top-return a::before{ content:'<'; margin-right:8px; color:#374151; font-weight:700 }
.top-return a:hover{ color:#111 }
.sidebar{ width:260px; background:white; border-radius:12px; padding:18px; box-shadow:0 6px 18px rgba(37,44,97,0.06); display:flex; flex-direction:column; }
.userbox{ display:flex; gap:12px; align-items:center; margin-bottom:12px }
.avatar{ width:56px; height:56px; border-radius:10px; background:#6366f1; color:white; display:flex; align-items:center; justify-content:center; font-weight:700 }
.info .name{ font-weight:600 }
.info .email{ font-size:13px; color:#666 }
.menu{ display:flex; flex-direction:column; gap:8px; margin-top:8px }
.menu button{ text-align:left; padding:10px 12px; border-radius:8px; border:none; background:transparent; cursor:pointer }
.menu button.active{ background:#eef2ff; font-weight:700 }
.sidebar-foot{ margin-top:auto }
.logout{ width:100%; padding:10px; border-radius:8px; border:none; background:#ef4444; color:white; cursor:pointer }

.content{ flex:1 }
.content h2{ margin-bottom:12px }
.card{ background:white; padding:14px; border-radius:10px; box-shadow:0 4px 12px rgba(37,44,97,0.04); margin-bottom:12px }
.orders .order{ display:flex; gap:12px; align-items:center; padding:10px 12px; background:white; border-radius:8px; margin-bottom:8px }
.order .status{ margin-left:auto; font-weight:600 }
.addr, .pay{ background:white; padding:12px; border-radius:8px; margin-bottom:8px }
.btn.small{ margin-top:8px; padding:8px 12px; border-radius:8px; background:#6366f1; color:white; border:none; cursor:pointer }

@media (max-width:900px){ .profile-layout{ flex-direction:column } .sidebar{ width:100% } }
</style>
