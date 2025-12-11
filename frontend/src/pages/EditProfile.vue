<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../utils/useAuth'
import { useToast } from '../utils/useToast'
import api from '../utils/api'
import Header from '../components/layout/HomeHeader.vue'
import Footer from '../components/layout/AppFooter.vue'

const router = useRouter()
const { user, updateUser } = useAuth()
const { showToast } = useToast()

// Form fields
const name = ref('')
const bio = ref('')
const phone = ref('')
const email = ref('')
const location = ref('')
const gender = ref('')
const website = ref('')
const facebook = ref('')
const instagram = ref('')
const twitter = ref('')

// Images
const avatarPreview = ref(null)
const coverPreview = ref(null)
const avatarFile = ref(null)
const coverFile = ref(null)

// State
const isSaving = ref(false)
const errors = ref({})

onMounted(() => {
  if (user?.value) {
    name.value = user.value.full_name || user.value.name || ''
    email.value = user.value.email || ''
    phone.value = user.value.phone_number || ''
    location.value = user.value.address || ''
    bio.value = user.value.bio || ''
    gender.value = user.value.gender || ''
    website.value = user.value.website || ''
    facebook.value = user.value.facebook || ''
    instagram.value = user.value.instagram || ''
    twitter.value = user.value.twitter || ''
    avatarPreview.value = user.value.avatar_url || null
    coverPreview.value = user.value.cover_url || null
  }
})

function handleAvatarChange(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 5 * 1024 * 1024) {
    errors.value.avatar = 'Tối đa 5MB'
    return
  }
  avatarFile.value = file
  avatarPreview.value = URL.createObjectURL(file)
  errors.value.avatar = null
}

function handleCoverChange(e) {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 10 * 1024 * 1024) {
    errors.value.cover = 'Tối đa 10MB'
    return
  }
  coverFile.value = file
  coverPreview.value = URL.createObjectURL(file)
  errors.value.cover = null
}

function removeAvatar() {
  avatarPreview.value = null
  avatarFile.value = null
}

function removeCover() {
  coverPreview.value = null
  coverFile.value = null
}

function validate() {
  errors.value = {}
  if (!name.value.trim()) errors.value.name = 'Tên không được để trống'
  if (!email.value.trim()) errors.value.email = 'Email không được để trống'
  if (phone.value && !/^[0-9]{10,11}$/.test(phone.value)) errors.value.phone = 'Số điện thoại không hợp lệ'
  return Object.keys(errors.value).length === 0
}

async function handleSave() {
  if (!validate()) return
  
  isSaving.value = true
  
  try {
    // 1. Update profile info
    await api.put('/user/profile', {
      full_name: name.value,
      phone_number: phone.value,
      address: location.value,
      bio: bio.value,
      gender: gender.value || null,
      website: website.value,
      facebook: facebook.value,
      instagram: instagram.value,
      twitter: twitter.value,
    })
    
    // 2. Upload avatar if changed
    let newAvatarUrl = null
    if (avatarFile.value) {
      const formData = new FormData()
      formData.append('avatar', avatarFile.value)
      const res = await api.post('/user/change-avatar', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      newAvatarUrl = res.data?.avatar_url
    }
    
    // 3. Upload cover if changed
    let newCoverUrl = null
    if (coverFile.value) {
      const formData = new FormData()
      formData.append('cover', coverFile.value)
      const res = await api.post('/user/change-cover', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      newCoverUrl = res.data?.cover_url
    }
    
    // Update local user data
    const updatedData = {
      ...user.value,
      full_name: name.value,
      phone_number: phone.value,
      address: location.value,
      bio: bio.value,
      gender: gender.value,
      website: website.value,
      facebook: facebook.value,
      instagram: instagram.value,
      twitter: twitter.value,
    }
    if (newAvatarUrl) updatedData.avatar_url = newAvatarUrl
    if (newCoverUrl) updatedData.cover_url = newCoverUrl
    updateUser(updatedData)
    
    showToast('Cập nhật thành công!', 'success')
    router.push('/profile')
  } catch (err) {
    console.error('Failed to update:', err)
    showToast(err.response?.data?.message || 'Có lỗi xảy ra', 'error')
  } finally {
    isSaving.value = false
  }
}

function handleCancel() {
  router.push('/profile')
}
</script>

<template>
  <div class="page">
    <Header />
    <main class="content">
      <div class="container">
        <div class="page-header">
          <button class="back-btn" @click="handleCancel">← Quay lại</button>
          <h1>Chỉnh sửa hồ sơ</h1>
        </div>

        <div class="grid">
          <!-- Left: Form -->
          <div class="form-section">
            <div class="card">
              <h2>Thông tin cá nhân</h2>
              
              <div class="field">
                <label>Tên hiển thị *</label>
                <input v-model="name" type="text" :class="{ error: errors.name }" placeholder="Nhập tên" />
                <span v-if="errors.name" class="error-msg">{{ errors.name }}</span>
              </div>

              <div class="field">
                <label>Email *</label>
                <input v-model="email" type="email" :class="{ error: errors.email }" placeholder="email@example.com" disabled />
                <span v-if="errors.email" class="error-msg">{{ errors.email }}</span>
              </div>

              <div class="field">
                <label>Số điện thoại</label>
                <input v-model="phone" type="tel" :class="{ error: errors.phone }" placeholder="0123456789" />
                <span v-if="errors.phone" class="error-msg">{{ errors.phone }}</span>
              </div>

              <div class="field">
                <label>Địa chỉ</label>
                <input v-model="location" type="text" placeholder="TP.HCM, Việt Nam" />
              </div>

              <div class="field">
                <label>Giới tính</label>
                <select v-model="gender">
                  <option value="">-- Chọn giới tính --</option>
                  <option value="male">Nam</option>
                  <option value="female">Nữ</option>
                  <option value="other">Khác</option>
                </select>
              </div>

              <div class="field">
                <label>Giới thiệu</label>
                <textarea v-model="bio" rows="3" placeholder="Viết vài dòng về bạn..."></textarea>
              </div>
            </div>

            <!-- Social Links -->
            <div class="card">
              <h2>Liên kết mạng xã hội</h2>
              
              <div class="field">
                <label>Website</label>
                <input v-model="website" type="url" placeholder="https://yourwebsite.com" />
              </div>

              <div class="field">
                <label>Facebook</label>
                <input v-model="facebook" type="text" placeholder="facebook.com/username" />
              </div>

              <div class="field">
                <label>Instagram</label>
                <input v-model="instagram" type="text" placeholder="@username" />
              </div>

              <div class="field">
                <label>Twitter</label>
                <input v-model="twitter" type="text" placeholder="@username" />
              </div>
            </div>

            <div class="actions">
              <button class="btn secondary" @click="handleCancel" :disabled="isSaving">Hủy</button>
              <button class="btn primary" @click="handleSave" :disabled="isSaving">
                {{ isSaving ? 'Đang lưu...' : 'Lưu thay đổi' }}
              </button>
            </div>
          </div>

          <!-- Right: Images -->
          <div class="image-section">
            <!-- Avatar -->
            <div class="card">
              <h2>Ảnh đại diện</h2>
              <div class="upload-area avatar-area">
                <input type="file" accept="image/*" @change="handleAvatarChange" ref="avatarInput" hidden />
                <div v-if="!avatarPreview" class="placeholder round" @click="$refs.avatarInput.click()">
                  <span class="icon">+</span>
                  <span>Tải ảnh (tối đa 5MB)</span>
                </div>
                <div v-else class="preview round">
                  <img :src="avatarPreview" alt="Avatar" />
                  <button class="remove" @click="removeAvatar">×</button>
                </div>
              </div>
              <span v-if="errors.avatar" class="error-msg">{{ errors.avatar }}</span>
            </div>

            <!-- Cover -->
            <div class="card">
              <h2>Ảnh bìa</h2>
              <div class="upload-area">
                <input type="file" accept="image/*" @change="handleCoverChange" ref="coverInput" hidden />
                <div v-if="!coverPreview" class="placeholder" @click="$refs.coverInput.click()">
                  <span class="icon">+</span>
                  <span>Tải ảnh bìa (tối đa 10MB)</span>
                </div>
                <div v-else class="preview">
                  <img :src="coverPreview" alt="Cover" />
                  <button class="remove" @click="removeCover">×</button>
                </div>
              </div>
              <span v-if="errors.cover" class="error-msg">{{ errors.cover }}</span>
            </div>
          </div>
        </div>
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
  max-width: 1100px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

.page-header h1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.back-btn {
  padding: 8px 16px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  color: #475569;
}

.back-btn:hover {
  border-color: #3b82f6;
  color: #3b82f6;
}

.grid {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 24px;
}

.card {
  background: #fff;
  padding: 24px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  margin-bottom: 16px;
}

.card h2 {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 16px;
}

.field {
  margin-bottom: 16px;
}

.field label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 6px;
}

.field input,
.field textarea,
.field select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  background: #f8fafc;
}

.field input:focus,
.field textarea:focus {
  outline: none;
  border-color: #3b82f6;
  background: #fff;
}

.field input.error,
.field textarea.error {
  border-color: #ef4444;
}

.field input:disabled {
  background: #f1f5f9;
  color: #64748b;
}

.error-msg {
  display: block;
  color: #ef4444;
  font-size: 12px;
  margin-top: 4px;
}

/* Upload Area */
.upload-area {
  margin-bottom: 8px;
}

.placeholder {
  border: 2px dashed #cbd5e1;
  border-radius: 12px;
  padding: 32px;
  text-align: center;
  cursor: pointer;
  background: #f8fafc;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #64748b;
}

.placeholder:hover {
  border-color: #3b82f6;
  background: #eff6ff;
}

.placeholder.round {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  padding: 0;
  margin: 0 auto;
  justify-content: center;
}

.placeholder .icon {
  font-size: 32px;
  font-weight: 300;
}

.preview {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
}

.preview.round {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  margin: 0 auto;
}

.preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.preview:not(.round) img {
  height: 180px;
}

.remove {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 28px;
  height: 28px;
  background: rgba(0,0,0,0.6);
  color: #fff;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.remove:hover {
  background: #ef4444;
}

/* Actions */
.actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

.btn {
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn.primary {
  background: #3b82f6;
  color: #fff;
}

.btn.primary:hover:not(:disabled) {
  background: #2563eb;
}

.btn.secondary {
  background: #fff;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.btn.secondary:hover:not(:disabled) {
  border-color: #3b82f6;
  color: #3b82f6;
}

@media (max-width: 900px) {
  .grid {
    grid-template-columns: 1fr;
  }
  .image-section {
    order: -1;
  }
}
</style>
