<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from '../utils/useToast'
import api from '../utils/api'

const router = useRouter()
const { showError, showSuccess } = useToast()
const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const password2 = ref('')
const error = ref('')
const loading = ref(false)

async function submit(e){
  e?.preventDefault()
  error.value = ''
  
  if(!name.value || !username.value || !email.value || !password.value || !password2.value){
    error.value = 'Vui lòng điền đầy đủ thông tin.'
    return
  }
  
  if(password.value !== password2.value){
    error.value = 'Mật khẩu xác nhận không khớp.'
    return
  }

  loading.value = true
  
  try {
    console.log('Sending registration request...', {
      name: name.value,
      username: username.value,
      email: email.value
    })
    
    const response = await api.post('/auth/register', {
      name: name.value,
      username: username.value,
      email: email.value,
      password: password.value,
      password_confirmation: password2.value
    })
    
    console.log('Registration response:', response.data)
    
    if (response.data.success) {
      showSuccess('Đăng ký thành công! Vui lòng đăng nhập.')
      router.push('/login')
    }
  } catch (err) {
    console.error('Registration error:', err)
    console.error('Error response:', err.response)
    
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat()[0]
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else if (err.message) {
      error.value = err.message
    } else {
      error.value = 'Đăng ký thất bại. Vui lòng kiểm tra kết nối.'
    }
    showError(error.value)
  } finally {
    loading.value = false
  }
}

function socialRegister(provider){
  // Replace with OAuth registration flow
  loading.value = true
  setTimeout(()=>{
    loading.value = false
    router.push('/login')
  }, 900)
}
</script>

<template>
  <div class="register-page">
    <div class="register-card">
      <div class="top-return">
        <router-link to="/home">Trở về trang chủ</router-link>
      </div>
      
      <div class="logo-container">
        <img src="/logo-2.jpg" alt="VietMarket Logo" class="logo" />
      </div>
      
      <div class="brand">
        <h1>Tạo tài khoản</h1>
        <p>Đăng ký để bắt đầu giao dịch</p>
      </div>

      <form class="form" @submit="submit">
        <label class="field">
          <span>Họ và tên</span>
          <input v-model="name" type="text" placeholder="Nguyễn Văn A" />
        </label>

        <label class="field">
          <span>Tên đăng nhập</span>
          <input v-model="username" type="text" placeholder="nguyenvana" />
        </label>

        <label class="field">
          <span>Email</span>
          <input v-model="email" type="email" placeholder="you@example.com" />
        </label>

        <label class="field">
          <span>Mật khẩu</span>
          <input v-model="password" type="password" placeholder="••••••••" />
        </label>

        <label class="field">
          <span>Xác nhận mật khẩu</span>
          <input v-model="password2" type="password" placeholder="••••••••" />
        </label>

        <div class="actions">
          <button class="btn primary" :disabled="loading">{{ loading ? 'Đang tạo...' : 'Đăng ký' }}</button>
        </div>

        <p class="note">Đã có tài khoản? <router-link to="/login">Đăng nhập</router-link></p>

        <p v-if="error" class="error">{{ error }}</p>
      </form>

      <div class="sep"><span>Hoặc đăng nhập bằng</span></div>
      <div class="socials bottom">
        <button class="social-btn google" @click.prevent="socialRegister('google')">🔴 Google</button>
        <button class="social-btn facebook" @click.prevent="socialRegister('facebook')">🔵 Facebook</button>
        <button class="social-btn apple" @click.prevent="socialRegister('apple')"> Apple</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page{
  min-height: calc(100vh - 0px);
  display:flex;
  align-items:center;
  justify-content:center;
  background: linear-gradient(180deg,#f6f9ff 0%, #ffffff 100%);
  padding: 32px;
}
.register-card{
  width: 100%;
  max-width:520px;
  background: #fff;
  border-radius:12px;
  box-shadow: 0 8px 20px rgba(37,44,97,0.08);
  padding:28px;
  box-sizing:border-box;
}
.brand h1{ font-size:22px; margin-bottom:6px; text-align: center }
.brand p{ color:#666; font-size:13px; margin-bottom:18px; text-align: center }
 .top-return{ margin-bottom:12px; display:flex; align-items:center }
 .top-return a{ color:#374151; text-decoration:none; font-size:14px; font-weight:500 }
 .top-return a::before{ content:'<'; margin-right:8px; color:#374151; font-weight:700 }
 .top-return a:hover{ color:#111 }

.logo-container {
  display: flex;
  justify-content: center;
  margin: 1.5rem 0 1rem;
}

.logo {
  width: 120px;
  height: 120px;
  object-fit: contain;
  animation: fadeInDown 0.6s ease;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form .field{ display:block; margin-bottom:12px }
.form .field span{ display:block; font-size:13px; margin-bottom:6px; color:#444 }
.form input{ width:100%; padding:10px 12px; border:1px solid #e6e9f2; border-radius:8px; font-size:14px; outline:none }
.form input:focus{ box-shadow: 0 0 0 3px rgba(99,102,241,0.06); border-color:#6366f1 }
.actions{ margin-top:12px }
.btn{ width:100%; padding:10px 14px; border-radius:8px; border:none; cursor:pointer }
.btn.primary{ background:#6366f1; color:#fff; font-weight:600 }
.btn:disabled{ opacity:0.7; cursor:default }
.note{ font-size:13px; color:#666; margin-top:12px }
.error{ color:#b91c1c; margin-top:10px; font-size:13px }

@media (max-width:520px){ .register-card{ padding:18px; border-radius:10px } }

.socials{ display:flex; gap:8px; margin-top:12px; flex-wrap:wrap }
.social-btn{ flex:1; padding:0 12px; height:44px; display:flex; align-items:center; justify-content:center; gap:8px; border-radius:10px; border:1px solid #e6e9f2; background:white; cursor:pointer; font-weight:600; font-size:14px }
.social-btn.google{ background:#fff; color:#c4302b; border-color:#e6e9f2 }
.social-btn.facebook{ background:#1877f2; color:white; border-color:rgba(0,0,0,0.05) }
.social-btn.apple{ background:#111; color:white; border-color:rgba(255,255,255,0.06) }

.social-btn:active{ transform:translateY(1px) }

.sep{ display:flex; align-items:center; gap:12px; margin-top:14px; margin-bottom:10px }
.sep::before, .sep::after{ content:''; flex:1; height:1px; background:#e6e9f2 }
.sep span{ padding:0 8px; color:#666; font-size:13px }
</style>
