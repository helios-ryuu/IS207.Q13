<template>
  <section class="category-grid-section">
    <div class="category-grid">
      <a
          v-for="category in categories"
          :key="category.id"
          href="#"
          class="category-item"
          @click.prevent="handleCategoryClick(category)"
      >
        <span v-if="category.isNew" class="new-badge">Mới</span>

        <div class="icon-wrapper">
          <img
              v-if="category.image"
              :src="getImageUrl(category.image)"
              :alt="category.name"
              class="category-icon"
          />
          <div v-else class="placeholder-icon"></div>
        </div>

        <span class="category-name" v-html="category.name"></span>
      </a>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router'; // 1. Import Router

const router = useRouter(); // 2. Khởi tạo router

// Hàm helper để lấy đường dẫn ảnh
const getImageUrl = (imageName) => {
  return `/DanhMuc/${imageName}`;
};

// 3. Hàm xử lý khi click vào danh mục
const handleCategoryClick = (category) => {
  // Trường hợp đặc biệt: ID 16 là "Tất cả danh mục" -> Chuyển sang trang sản phẩm mà không lọc gì cả
  if (category.id === 16) {
    router.push({ path: '/products' });
    return;
  }

  // Xử lý tên: Trong data bạn có thẻ <br> (ví dụ: "Đồ gia dụng, nội<br>thất..."), 
  // cần xóa <br> đi để gửi lên URL cho đúng với Database
  const cleanName = category.name.replace(/<br\s*\/?>/gi, ' ').trim();

  // Chuyển hướng sang trang ProductCatalog kèm query param
  router.push({ 
    path: '/products', 
    query: { category: cleanName } 
  });
};

// Dữ liệu danh mục (Giữ nguyên icon và text đẹp của bạn)
const categories = ref([
  // Hàng 1
  { id: 2, name: 'Xe cộ', image: 'XeCo.png', isNew: false },
  { id: 3, name: 'Thú cưng', image: 'ThuCung.webp', isNew: false },
  { id: 4, name: 'Đồ gia dụng, nội<br>thất, cây cảnh', image: 'DoGiaDung.webp', isNew: false },
  { id: 5, name: 'Giải trí, Thể thao, Sở<br>thích', image: 'GiaiTri.webp', isNew: false },
  { id: 6, name: 'Mẹ và bé', image: 'MeBe.webp', isNew: false },
  { id: 7, name: 'Dịch vụ, Du lịch', image: 'DuLich.webp', isNew: false },
  { id: 8, name: 'Cho tặng miễn phí', image: 'QuaTang.webp', isNew: false },

  // Hàng 2
  { id: 10, name: 'Đồ điện tử', image: 'DoDienTu.webp', isNew: false },
  { id: 11, name: 'Tủ lạnh, máy lạnh,<br>máy giặt', image: 'TuLanh.webp', isNew: false },
  { id: 12, name: 'Đồ dùng văn phòng,<br>công nông nghiệp', image: 'MayIn.webp', isNew: false },
  { id: 13, name: 'Thời trang, Đồ dùng<br>cá nhân', image: 'Thoitrang.webp', isNew: false },
  { id: 14, name: 'Đồ ăn, thực phẩm<br>và các loại khác', image: 'DoAn.webp', isNew: false },
  { id: 15, name: 'Dịch vụ chăm sóc<br>nhà cửa', image: 'NoiThat.webp', isNew: true },
  { id: 16, name: 'Tất cả danh mục', image: 'tat-ca-danh-muc.webp', isNew: false },
]);
</script>

<style scoped>
/* GIỮ NGUYÊN CSS CỦA BẠN */
.category-grid-section {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.category-grid {
  display: grid;
  grid-template-columns: repeat(8, 1fr);
  gap: 20px 10px;
}
.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  text-decoration: none;
  color: #333;
  font-size: 13px;
  font-weight: 500;
  line-height: 1.4;
  position: relative;
  transition: transform 0.2s ease;
  cursor: pointer;
}
.category-item:hover {
  transform: translateY(-3px);
  color: #ffd60a;
}
.icon-wrapper {
  width: 60px;
  height: 60px;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.category-icon {
  width: 100%;
  height: 100%;
  object-fit: contain;
}
.placeholder-icon {
  width: 100%;
  height: 100%;
  background-color: #f5f5f5;
  border-radius: 50%;
}
.category-name {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 36px;
}
.new-badge {
  position: absolute;
  top: -8px;
  right: 0;
  background-color: #ff424e;
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 9px;
  font-weight: bold;
  text-transform: uppercase;
  z-index: 2;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
@media (max-width: 1200px) { .category-grid { grid-template-columns: repeat(6, 1fr); } }
@media (max-width: 992px) { .category-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 576px) {
  .category-grid { grid-template-columns: repeat(4, 1fr); gap: 15px 5px; }
  .category-grid-section { padding: 15px 10px; }
  .icon-wrapper { width: 45px; height: 45px; }
  .category-name { font-size: 11px; }
}
</style>