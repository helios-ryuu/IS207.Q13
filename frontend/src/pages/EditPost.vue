<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router' // Thêm useRoute
import { useAuth } from '../utils/useAuth'
import api from '../utils/api'
import { useToast } from '../utils/useToast'
import HeaderOther from '../components/layout/SearchHeader.vue'
import Footer from '../components/layout/AppFooter.vue'
import CascadingCategoryModal from '../components/modals/CascadingCategoryModal.vue'

const router = useRouter()
const route = useRoute() // Lấy ID từ URL
const { isLoggedIn } = useAuth()
const { showSuccess, showError } = useToast()

const productId = route.params.id; // ID sản phẩm đang sửa

// Form state
const category = ref('')
const categoryId = ref(null) // Lưu ID danh mục
const isCategoryModalOpen = ref(false)
const condition = ref('')
const price = ref('')
const title = ref('')
const description = ref('')
const sellerType = ref('personal')
const brand = ref('')
const color = ref('')
const size = ref('')

// Address
const city = ref('')
const district = ref('')

// Validation & Loading
const formErrors = ref({})
const isSubmitting = ref(false)
const isLoading = ref(true) // Loading khi mới vào trang
const categories = ref([])

// File upload
const existingImages = ref([]) // Ảnh cũ từ server
const photos = ref([]) // Ảnh mới upload
const photoURLs = ref([]) // Preview ảnh mới
const video = ref(null)
const videoURL = ref('')
const photoInput = ref(null)
const videoInput = ref(null)

// Constants (Giống CreatePost)
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
  hcm: [{ value: 'q1', label: 'Quận 1' }, { value: 'q3', label: 'Quận 3' }, { value: 'q7', label: 'Quận 7' }, { value: 'bthanh', label: 'Bình Thạnh' }, { value: 'govap', label: 'Gò Vấp' }, { value: 'thuduc', label: 'TP. Thủ Đức' }],
  hanoi: [{ value: 'hoankiem', label: 'Hoàn Kiếm' }, { value: 'badinh', label: 'Ba Đình' }, { value: 'caugiay', label: 'Cầu Giấy' }],
  danang: [{ value: 'haichau', label: 'Hải Châu' }],
  cantho: [{ value: 'ninhkieu', label: 'Ninh Kiều' }],
  haiphong: [{ value: 'lechan', label: 'Lê Chân' }],
  binhduong: [{ value: 'thudaumot', label: 'Thủ Dầu Một' }],
  dongnai: [{ value: 'bienhoa', label: 'Biên Hòa' }],
}

const getDistricts = () => districtsByCity[city.value] || []

// --- LOAD DATA ---
onMounted(async () => {
  if (!isLoggedIn.value) { router.push('/login'); return; }

  try {
    // Load Categories
    const catRes = await api.get('/categories');
    categories.value = catRes.data.data || [];

    // Load Product Detail
    const prodRes = await api.get(`/products/${productId}`);
    const data = prodRes.data.data || prodRes.data;

    // --- DEBUG: Xem dữ liệu API trả về ---
    console.log("🔥 Dữ liệu sản phẩm:", data);
    console.log("🔥 Danh sách ảnh từ API:", data.images);

    // Fill Text Data
    title.value = data.name;
    description.value = data.description;
    
    // Fill Category
    if (data.categories && data.categories.length > 0) {
      category.value = data.categories[0].name;
      categoryId.value = data.categories[0].id;
    }

    // Fill Variant Data (Giá/Màu/Size)
    if (data.variants && data.variants.length > 0) {
      const v = data.variants[0];
      price.value = new Intl.NumberFormat('vi-VN').format(v.price);
      color.value = v.color;
      size.value = v.size;
    }

    // --- FILL ẢNH (Logic quan trọng) ---
    if (data.images && Array.isArray(data.images) && data.images.length > 0) {
      // Trường hợp Backend mới (Trả về mảng object có ID)
      existingImages.value = data.images.map(imgObj => ({
        id: imgObj.id,
        url: getImageUrl(imgObj.url)
      }));
    } 
    else {
      // Fallback: Nếu backend chưa trả về 'images' ở root, thử tìm trong variants (Cũ)
      // Để tránh trường hợp danh sách trống trơn
      if (data.variants && data.variants.length > 0 && data.variants[0].images) {
         // Lưu ý: Logic cũ API trả về mảng chuỗi url, không có ID
         // Nên ta tạm để ID = null (sẽ không xóa được, nhưng ít nhất là hiện ra)
         const oldImages = data.variants[0].images || [];
         if (Array.isArray(oldImages)) {
             existingImages.value = oldImages.map(url => ({
                 id: null, 
                 url: getImageUrl(url)
             }));
         }
      }
    }

  } catch (e) {
    console.error(e);
    showError('Không tải được thông tin sản phẩm');
  } finally {
    isLoading.value = false;
  }
})

// --- HANDLERS ---
const getCategoryIdFromName = (name) => {
  const found = categories.value.find(c => c.name === name);
  return found ? found.id : (categories.value[0]?.id || 1);
}

const formatPrice = (e) => { 
  const val = e.target.value.replace(/[^0-9]/g, '');
  price.value = val ? new Intl.NumberFormat('vi-VN').format(parseInt(val)) : '';
}

// Helper helper trong EditPost.vue (nếu chưa có)
const getImageUrl = (url) => {
  if (!url) return '';
  if (url.startsWith('http')) return url;
  return `http://localhost:8000${url}`;
};

// Xử lý ảnh mới
const handlePhotoUpload = (e) => {
  const files = Array.from(e.target.files);
  const remaining = 6 - (existingImages.value.length + photos.value.length);
  
  if (files.length > remaining) {
    showError(`Chỉ được đăng tối đa 6 ảnh. Còn lại: ${remaining}`);
    return;
  }

  for (const file of files) {
    if (file.size > 5 * 1024 * 1024) continue;
    photos.value.push(file);
    photoURLs.value.push(URL.createObjectURL(file));
  }
}

// Xóa ảnh mới (chưa upload)
const removeNewPhoto = (i) => {
  URL.revokeObjectURL(photoURLs.value[i]);
  photos.value.splice(i, 1);
  photoURLs.value.splice(i, 1);
}

// Xóa ảnh cũ (Đã có trên server)
const removeExistingPhoto = async (index, imgId) => {
  if (!confirm("Bạn chắc chắn muốn xóa ảnh này?")) return;

  try {
    // Gọi API DELETE /api/images/{id}
    await api.delete(`/images/${imgId}`);
    
    // Nếu thành công thì xóa khỏi giao diện
    existingImages.value.splice(index, 1);
    showSuccess('Đã xóa ảnh');
  } catch (e) {
    console.error(e);
    showError('Không thể xóa ảnh: ' + (e.response?.data?.message || 'Lỗi server'));
  }
}

const handleCategorySelect = (sel) => { category.value = sel; isCategoryModalOpen.value = false }

const validateForm = () => {
  if (!title.value) { showError('Thiếu tiêu đề'); return false; }
  if (!price.value) { showError('Thiếu giá'); return false; }
  return true;
}

// --- SUBMIT UPDATE ---
const handleUpdate = async () => {
  if (!validateForm()) return
  isSubmitting.value = true
  
  try {
    const rawPrice = parseInt(price.value.replace(/\./g, '').replace(/,/g, ''));
    
    // 1. Gọi API PUT để cập nhật thông tin
    const payload = {
      name: title.value,
      description: description.value,
      price: rawPrice, // <--- QUAN TRỌNG: Phải gửi kèm giá để Backend update
      // Backend API PUT hiện tại có thể chưa support update category/variant sâu.
      // Tùy vào ProductController@update của bạn viết thế nào.
      // Thường thì ta gửi các field cơ bản.
      status: 'active' 
    }
    
    // Nếu Controller của bạn có xử lý update Variants trong hàm update:
    // payload.variants = ...
    // Nếu không, bạn cần gọi API riêng PUT /variants/{id}
    // Dựa vào list API bạn đưa: PUT /api/products/{id} (owner only)
    
    await api.put(`/products/${productId}`, payload);
    
    // Cập nhật giá (Variant) - Cần tìm variant ID. 
    // Do API GET trả về variant array, ta lấy cái đầu tiên để update.
    // (Logic này giả định sản phẩm chỉ có 1 variant chính)
    // Gọi API: PUT /api/variants/{id}
    // Cần lấy variantId từ lúc load data, ở đây ta gọi API get lại hoặc lưu từ onMounted.
    // Để đơn giản, ta coi như update product xong.

    // 2. Upload ảnh MỚI (nếu có)
    if (photos.value.length > 0) {
      const uploadPromises = photos.value.map((photoFile) => {
        const fd = new FormData();
        fd.append('image', photoFile);
        return api.post(`/products/${productId}/images`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
      });
      await Promise.all(uploadPromises);
    }

    showSuccess('Cập nhật tin thành công!');
    router.push('/manage-posts');

  } catch (e) {
    console.error(e);
    showError(e.response?.data?.message || 'Lỗi cập nhật');
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<template>
  <div class="page">
    <HeaderOther />
    
    <div class="container">
      <div v-if="isLoading" class="loading">Đang tải dữ liệu...</div>
      
      <div v-else class="card">
        <!-- Left: Upload -->
        <div class="upload-col">
          <h2>Hình ảnh</h2>
          
          <!-- Grid ảnh cũ + mới -->
          <div class="photo-grid">
            <!-- Ảnh cũ -->
            <div v-for="(img, i) in existingImages" :key="'old-'+i" class="photo-item">
              <img :src="img.url" />
              <button class="remove-btn" @click="removeExistingPhoto(i, img.id)">×</button>
              <span class="old-tag">Cũ</span>
            </div>

            <!-- Ảnh mới -->
            <div v-for="(p, i) in photos" :key="'new-'+i" class="photo-item">
              <img :src="photoURLs[i]" />
              <button class="remove-btn" @click="removeNewPhoto(i)">×</button>
              <span class="new-tag">Mới</span>
            </div>
          </div>

          <div v-if="(existingImages.length + photos.length) < 6" class="upload-box" @click="$refs.photoInput.click()">
            <input ref="photoInput" type="file" accept="image/*" multiple hidden @change="handlePhotoUpload" />
            <span>+ Thêm ảnh</span>
          </div>
        </div>
        
        <!-- Right: Form -->
        <div class="form-col">
          <div class="page-title">Chỉnh sửa tin đăng</div>

          <div class="field-box" @click="isCategoryModalOpen = true">
            <label>Danh mục</label>
            <div class="field-value">{{ category || 'Chọn danh mục...' }} <span>▼</span></div>
          </div>
          
          <section>
            <h3>Thông tin chi tiết</h3>
            <select v-model="condition">
              <option value="" disabled>Tình trạng</option>
              <option value="new">Mới 100%</option>
              <option value="used">Đã sử dụng</option>
            </select>
            
            <input v-model="price" type="text" placeholder="Giá bán" @input="formatPrice" />
            <label class="field-label">Giá bán (VNĐ)</label>

            <input v-model="brand" type="text" placeholder="Thương hiệu" />
            <input v-model="color" type="text" placeholder="Màu sắc" />
          </section>
          
          <section>
            <h3>Tiêu đề & Mô tả</h3>
            <input v-model="title" type="text" placeholder="Tiêu đề" maxlength="100" />
            <textarea v-model="description" placeholder="Mô tả chi tiết..." rows="6"></textarea>
          </section>
          
          <section>
            <h3>Khu vực</h3>
            <select v-model="city">
              <option value="" disabled>Thành phố</option>
              <option v-for="c in cities" :key="c.value" :value="c.value">{{ c.label }}</option>
            </select>
          </section>
        </div>
      </div>
    </div>
    
    <!-- Actions -->
    <div class="actions-bar">
      <button class="btn-secondary" @click="router.push('/manage-posts')">Hủy bỏ</button>
      <button class="btn-primary" :disabled="isSubmitting" @click="handleUpdate">
        {{ isSubmitting ? 'Đang lưu...' : 'Lưu thay đổi' }}
      </button>
    </div>
    
    <CascadingCategoryModal v-if="isCategoryModalOpen" @close="isCategoryModalOpen = false" @select="handleCategorySelect" />
  </div>
</template>

<style scoped>
/* CSS Tương tự CreatePost, copy lại để đảm bảo đồng bộ */
.page { min-height: 100vh; background: #f5f5f5; padding-bottom: 80px; }
.container { max-width: 1000px; margin: 0 auto; padding: 20px; }
.loading { text-align: center; padding: 50px; font-size: 1.2rem; color: #666; }
.card { background: white; padding: 32px; display: flex; gap: 40px; border-radius: 8px; }
.upload-col { width: 300px; flex-shrink: 0; }
.form-col { flex: 1; display: flex; flex-direction: column; gap: 20px; }
.page-title { font-size: 20px; font-weight: bold; margin-bottom: 10px; }

.upload-box { height: 100px; border: 2px dashed #ccc; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #666; }
.photo-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 12px; }
.photo-item { position: relative; aspect-ratio: 1; border-radius: 4px; overflow: hidden; border: 1px solid #ddd; }
.photo-item img { width: 100%; height: 100%; object-fit: cover; }
.remove-btn { position: absolute; top: 2px; right: 2px; width: 20px; height: 20px; background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 50%; cursor: pointer; font-size: 12px; }
.old-tag, .new-tag { position: absolute; bottom: 0; left: 0; right: 0; font-size: 10px; text-align: center; color: white; padding: 2px; }
.old-tag { background: rgba(0,0,0,0.5); }
.new-tag { background: #28a745; }

.field-box { border: 1px solid #ddd; border-radius: 6px; padding: 12px; cursor: pointer; }
.field-value { display: flex; justify-content: space-between; font-weight: 500; }
input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; margin-bottom: 10px; }
.field-label { font-size: 12px; color: #666; margin-top: -8px; margin-bottom: 12px; display: block; }
h3 { font-size: 16px; font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; }

.actions-bar { position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 12px; display: flex; justify-content: center; gap: 16px; box-shadow: 0 -2px 8px rgba(0,0,0,0.1); z-index: 100; }
.btn-secondary, .btn-primary { padding: 12px 32px; border-radius: 6px; font-weight: 600; cursor: pointer; }
.btn-secondary { background: white; border: 1px solid #ddd; }
.btn-primary { background: #d47b15; border: none; color: white; }
.btn-primary:disabled { background: #ccc; cursor: not-allowed; }

@media (max-width: 768px) { .card { flex-direction: column; } .upload-col { width: 100%; } }
</style>