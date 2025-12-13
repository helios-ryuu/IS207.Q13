<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../utils/useAuth'
import api from '../utils/api'
import { useToast } from '../utils/useToast'
import HeaderOther from '../components/layout/SearchHeader.vue'
import Footer from '../components/layout/AppFooter.vue'
import CascadingCategoryModal from '../components/modals/CascadingCategoryModal.vue'

const router = useRouter()
const { user, isLoggedIn } = useAuth()
const { showSuccess, showError } = useToast()

// Form state
const category = ref('')
const isCategoryModalOpen = ref(false)
const condition = ref('')
const price = ref('')
const title = ref('')
const description = ref('')
const sellerType = ref('personal')
const brand = ref('')
const color = ref('')
const size = ref('')

// Address - cascading
const city = ref('')
const district = ref('')

// Validation & Loading
const formErrors = ref({})
const isSubmitting = ref(false)
const categories = ref([])

// File upload
const photos = ref([])
const photoURLs = ref([]) // Cached URLs
const video = ref(null)
const videoURL = ref('') // Cached URL
const photoInput = ref(null)
const videoInput = ref(null)

// Address data (Giữ nguyên logic cũ của bạn vì Backend chưa có API địa chỉ)
const cities = [
  { value: 'hcm', label: 'TP. Hồ Chí Minh' },
  { value: 'hanoi', label: 'Hà Nội' },
  { value: 'danang', label: 'Đà Nẵng' },
  { value: 'cantho', label: 'Cần Thơ' },
  { value: 'haiphong', label: 'Hải Phòng' },
  { value: 'binhduong', label: 'Bình Dương' },
  { value: 'dongnai', label: 'Đồng Nai' },
]

const districtsByCity = {
  hcm: [
    { value: 'q1', label: 'Quận 1' },
    { value: 'q3', label: 'Quận 3' },
    { value: 'q7', label: 'Quận 7' },
    { value: 'bthanh', label: 'Bình Thạnh' },
    { value: 'govap', label: 'Gò Vấp' },
    { value: 'thuduc', label: 'TP. Thủ Đức' },
  ],
  hanoi: [
    { value: 'hoankiem', label: 'Hoàn Kiếm' },
    { value: 'badinh', label: 'Ba Đình' },
    { value: 'caugiay', label: 'Cầu Giấy' },
  ],
  danang: [{ value: 'haichau', label: 'Hải Châu' }],
  cantho: [{ value: 'ninhkieu', label: 'Ninh Kiều' }],
  haiphong: [{ value: 'lechan', label: 'Lê Chân' }],
  binhduong: [{ value: 'thudaumot', label: 'Thủ Dầu Một' }],
  dongnai: [{ value: 'bienhoa', label: 'Biên Hòa' }],
}

const getDistricts = () => districtsByCity[city.value] || []

watch(city, () => { district.value = '' })

// 1. Fetch Categories từ API
onMounted(async () => {
  if (!isLoggedIn.value) {
    showError('Vui lòng đăng nhập để đăng tin');
    router.push('/login');
    return;
  }

  try {
    const res = await api.get('/categories');
    // API Laravel Resource thường trả về { data: [...] }
    categories.value = res.data.data || res.data || [];
  } catch (e) {
    console.error("Lỗi lấy danh mục:", e);
  }
})

// Hàm convert tên danh mục (string) sang ID (int) để gửi Backend
const getCategoryId = (name) => {
  if (!name || categories.value.length === 0) return 1; // Default fallback
  
  // Logic tìm kiếm gần đúng
  // Ví dụ: chọn "Xe cộ", tìm category có name chứa "Xe cộ"
  const found = categories.value.find(c => 
    c.name.toLowerCase().includes(name.toLowerCase()) || 
    name.toLowerCase().includes(c.name.toLowerCase())
  );
  
  return found ? found.id : categories.value[0].id;
}

// Handlers
const MAX_IMAGE_SIZE = 5 * 1024 * 1024 // 5MB (Sửa lại cho khớp thông báo)
const MAX_VIDEO_SIZE = 50 * 1024 * 1024 // 50MB

const formatPrice = (e) => { 
  // Chỉ giữ lại số
  const val = e.target.value.replace(/[^0-9]/g, '');
  if (val) {
    price.value = new Intl.NumberFormat('vi-VN').format(parseInt(val));
  } else {
    price.value = '';
  }
}

const handlePhotoUpload = (e) => {
  const files = Array.from(e.target.files)
  const validFiles = []
  
  for (const file of files) {
    if (file.size > MAX_IMAGE_SIZE) {
      showError(`Ảnh "${file.name}" vượt quá 5MB`)
      continue
    }
    validFiles.push(file)
  }
  
  const remaining = 6 - photos.value.length
  const toAdd = validFiles.slice(0, remaining)
  
  for (const file of toAdd) {
    photos.value.push(file)
    photoURLs.value.push(URL.createObjectURL(file))
  }
  
  if (validFiles.length > remaining) {
    showError('Tối đa 6 hình ảnh')
  }
  
  // Reset input để chọn lại cùng file được
  if (photoInput.value) photoInput.value.value = '';
}

const removePhoto = (i) => {
  if (photoURLs.value[i]) URL.revokeObjectURL(photoURLs.value[i])
  photos.value.splice(i, 1)
  photoURLs.value.splice(i, 1)
}

const handleVideoUpload = (e) => {
  const file = e.target.files[0]
  if (!file) return
  
  if (file.size > MAX_VIDEO_SIZE) {
    showError('Video vượt quá 50MB')
    return
  }
  if (videoURL.value) URL.revokeObjectURL(videoURL.value)
  video.value = file
  videoURL.value = URL.createObjectURL(file)
}

const removeVideo = () => {
  if (videoURL.value) URL.revokeObjectURL(videoURL.value)
  video.value = null
  videoURL.value = ''
  if (videoInput.value) videoInput.value.value = ''
}

const handleCategorySelect = (sel) => { category.value = sel; isCategoryModalOpen.value = false }

const validateForm = () => {
  formErrors.value = {}
  if (!category.value) { showError('Vui lòng chọn danh mục'); return false }
  if (!condition.value) { formErrors.value.condition = true; showError('Vui lòng chọn tình trạng'); return false }
  if (!price.value) { formErrors.value.price = true; showError('Vui lòng nhập giá'); return false }
  if (!title.value) { formErrors.value.title = true; showError('Vui lòng nhập tiêu đề'); return false }
  if (!description.value) { formErrors.value.description = true; showError('Vui lòng nhập mô tả'); return false }
  if (!city.value) { formErrors.value.city = true; showError('Vui lòng chọn thành phố'); return false }
  // District là optional trong logic cũ của bạn, có thể bỏ qua check
  return true
}

// --- HÀM SUBMIT CHÍNH ---
const handleSubmit = async () => {
  if (!validateForm()) return
  isSubmitting.value = true
  
  try {
    // 1. Chuẩn bị dữ liệu Product
    // Chuyển giá từ string "5.000.000" về số int 5000000
    const rawPrice = parseInt(price.value.replace(/\./g, '').replace(/,/g, ''));
    
    // Gộp địa chỉ thành string (vì Backend chưa có bảng Address riêng cho Product)
    const locationString = `${city.value === 'hcm' ? 'TP.HCM' : city.value}, ${district.value}`;

    const payload = {
      name: title.value,
      description: description.value + `\n\n--- \nKhu vực: ${locationString} \nTình trạng: ${condition.value}`,
      category_ids: [getCategoryId(category.value)], // Backend nhận mảng ID
      status: 'active', // Mặc định hiển thị luôn
      
      // Tạo sẵn 1 variant mặc định
      variants: [{ 
        price: rawPrice, 
        quantity: 1, 
        color: color.value || 'Tiêu chuẩn', 
        size: size.value || 'Tiêu chuẩn' 
      }]
    }
    
    console.log("Submitting product:", payload);
    
    // 2. Gọi API tạo Product
    const res = await api.post('/products', payload)
    
    // Lấy ID sản phẩm vừa tạo (Backend trả về Resource hoặc Model)
    const newProduct = res.data.data || res.data;
    const productId = newProduct.id;
    
    console.log('Product created with ID:', productId);
    
    // 3. Upload từng ảnh (Gọi API: POST /products/{id}/images)
    if (photos.value.length > 0) {
      // Dùng Promise.all để upload song song cho nhanh
      const uploadPromises = photos.value.map((photoFile) => {
        const fd = new FormData();
        fd.append('image', photoFile); // Key phải là 'image' theo ProductImageController
        return api.post(`/products/${productId}/images`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
      });
      
      await Promise.all(uploadPromises);
      console.log('All images uploaded');
    }
    
    showSuccess('Đăng tin thành công!')
    
    // Chuyển hướng về trang quản lý tin
    router.push('/manage-posts') // Đảm bảo route này tồn tại trong router/index.js
    
  } catch (e) {
    console.error("Submit Error:", e);
    const msg = e.response?.data?.message || 'Có lỗi xảy ra khi đăng tin';
    showError(msg);
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="page">
    <HeaderOther />
    
    <div class="container">
      <div class="card">
        <!-- Left: Upload -->
        <div class="upload-col">
          <h2>Hình ảnh và Video</h2>
          
          <div v-if="photos.length < 6" class="upload-box" @click="$refs.photoInput.click()">
            <input ref="photoInput" type="file" accept="image/*" multiple hidden @change="handlePhotoUpload" />
            <span>Thêm ảnh (tối đa 6, mỗi ảnh ≤5MB)</span>
          </div>
          
          <div v-if="photos.length" class="photo-grid">
            <div v-for="(p, i) in photos" :key="i" class="photo-item">
              <img :src="photoURLs[i]" />
              <button class="remove-btn" @click.stop="removePhoto(i)">×</button>
            </div>
          </div>
          
          <div v-if="!video" class="upload-box" @click="$refs.videoInput.click()">
            <input ref="videoInput" type="file" accept="video/*" hidden @change="handleVideoUpload" />
            <span>Thêm video (tùy chọn, tối đa 50MB)</span>
          </div>
          
          <div v-if="video" class="video-preview">
            <video :src="videoURL" controls></video>
            <button class="remove-btn" @click="removeVideo">Xóa video</button>
          </div>
        </div>
        
        <!-- Right: Form -->
        <div class="form-col">
          <!-- Category -->
          <div class="field-box" @click="isCategoryModalOpen = true">
            <label>Danh mục <span class="req">*</span></label>
            <div class="field-value">{{ category || 'Chọn danh mục...' }} <span>▼</span></div>
          </div>
          
          <template v-if="category">
            <!-- Thông tin chung -->
            <section>
              <h3>Thông tin chung</h3>
              <select v-model="condition" :class="{ error: formErrors.condition }">
                <option value="" disabled>Tình trạng *</option>
                <option value="new">Mới 100%</option>
                <option value="likenew">Như mới</option>
                <option value="used">Đã sử dụng</option>
              </select>
              <input v-model="price" type="text" inputmode="numeric" placeholder="VD: 5000000" :class="{ error: formErrors.price }" @input="formatPrice" />
              <label class="field-label">Giá bán (VNĐ) <span class="req">*</span></label>
            </section>
            
            <!-- Thông tin chi tiết -->
            <section>
              <h3>Thông tin chi tiết - {{ category }}</h3>
              <input v-model="brand" type="text" placeholder="VD: Apple, Samsung, Honda..." />
              <label class="field-label">Thương hiệu</label>
              
              <input v-model="color" type="text" placeholder="VD: Đen, Trắng, Xám..." />
              <label class="field-label">Màu sắc</label>
              
              <input v-model="size" type="text" placeholder="VD: S, M, L, XL hoặc 256GB..." />
              <label class="field-label">Kích thước / Dung lượng</label>
            </section>
            
            <!-- Tiêu đề và Mô tả -->
            <section>
              <h3>Tiêu đề và Mô tả</h3>
              <input v-model="title" type="text" placeholder="VD: iPhone 14 Pro Max 256GB" :class="{ error: formErrors.title }" maxlength="100" />
              <label class="field-label">Tiêu đề tin đăng <span class="req">*</span></label>
              <span class="char-count">{{ title.length }}/100</span>
              
              <textarea v-model="description" placeholder="Mô tả tình trạng, phụ kiện, lý do bán..." :class="{ error: formErrors.description }" rows="5" maxlength="3000"></textarea>
              <label class="field-label">Mô tả chi tiết <span class="req">*</span></label>
              <span class="char-count">{{ description.length }}/3000</span>
            </section>
            
            <!-- Thông tin người bán -->
            <section>
              <h3>Thông tin người bán</h3>
              <div class="seller-btns">
                <button :class="{ active: sellerType === 'personal' }" @click="sellerType = 'personal'" type="button">Cá nhân</button>
                <button :class="{ active: sellerType === 'business' }" @click="sellerType = 'business'" type="button">Bán chuyên</button>
              </div>
              
              <select v-model="city" :class="{ error: formErrors.city }">
                <option value="" disabled>Thành phố *</option>
                <option v-for="c in cities" :key="c.value" :value="c.value">{{ c.label }}</option>
              </select>
              
              <select v-if="city" v-model="district" :class="{ error: formErrors.district }">
                <option value="" disabled>Quận/Huyện *</option>
                <option v-for="d in getDistricts()" :key="d.value" :value="d.value">{{ d.label }}</option>
              </select>
            </section>
          </template>
        </div>
      </div>
    </div>
    
    <!-- Fixed Actions -->
    <div class="actions-bar">
      <button class="btn-secondary" :disabled="isSubmitting">Xem trước</button>
      <button class="btn-primary" :disabled="isSubmitting" @click="handleSubmit">
        {{ isSubmitting ? 'Đang đăng...' : 'Đăng tin' }}
      </button>
    </div>
    
    <Footer />
    
    <CascadingCategoryModal v-if="isCategoryModalOpen" @close="isCategoryModalOpen = false" @select="handleCategorySelect" />
  </div>
</template>

<style scoped>
.page { min-height: 100vh; background: #f5f5f5; padding-bottom: 80px; }
.container { max-width: 1000px; margin: 0 auto; padding: 20px; }
.card { background: white; padding: 32px; display: flex; gap: 40px; border-radius: 8px; }

/* Upload Column */
.upload-col { width: 300px; flex-shrink: 0; }
.upload-col h2 { font-size: 18px; margin-bottom: 16px; }
.upload-box { height: 120px; border: 2px dashed #ccc; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; margin-bottom: 12px; color: #666; }
.upload-box:hover { border-color: #448aff; background: rgba(68,138,255,0.05); }
.photo-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 12px; }
.photo-item { position: relative; aspect-ratio: 1; border-radius: 4px; overflow: hidden; }
.photo-item img { width: 100%; height: 100%; object-fit: cover; }
.remove-btn { position: absolute; top: 2px; right: 2px; width: 20px; height: 20px; background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 50%; cursor: pointer; font-size: 14px; }
.video-preview video { width: 100%; border-radius: 4px; }
.video-preview .remove-btn { position: static; width: 100%; border-radius: 4px; margin-top: 8px; height: auto; padding: 8px; }

/* Form Column */
.form-col { flex: 1; display: flex; flex-direction: column; gap: 24px; }
.form-col section { display: flex; flex-direction: column; gap: 8px; }
.form-col h3 { font-size: 16px; font-weight: 600; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 8px; }

.field-box { border: 1px solid #ddd; border-radius: 6px; padding: 12px; cursor: pointer; }
.field-box:hover { border-color: #448aff; }
.field-box label { font-size: 12px; color: #666; }
.field-value { display: flex; justify-content: space-between; font-weight: 500; margin-top: 4px; }

input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
input:focus, select:focus, textarea:focus { outline: none; border-color: #448aff; }
input.error, select.error, textarea.error { border-color: #f44336; }

.field-label { font-size: 12px; color: #666; margin-top: -4px; }
.req { color: #f44336; }
.char-count { font-size: 11px; color: #999; align-self: flex-end; }

.seller-btns { display: flex; gap: 8px; }
.seller-btns button { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 6px; background: white; cursor: pointer; }
.seller-btns button.active { border-color: #448aff; color: #448aff; background: rgba(68,138,255,0.1); }

/* Actions Bar */
.actions-bar { position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 12px; display: flex; justify-content: center; gap: 16px; box-shadow: 0 -2px 8px rgba(0,0,0,0.1); z-index: 100; }
.btn-secondary, .btn-primary { padding: 12px 32px; border-radius: 6px; font-weight: 600; cursor: pointer; }
.btn-secondary { background: white; border: 1px solid #ddd; }
.btn-secondary:hover { background: #f5f5f5; }
.btn-primary { background: #d47b15; border: none; color: white; }
.btn-primary:hover { background: #b86a12; }

@media (max-width: 768px) {
  .card { flex-direction: column; }
  .upload-col { width: 100%; }
}
</style>
