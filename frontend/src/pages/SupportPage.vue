<template>
  <SearchHeader />

  <div class="support-page-wrapper">
    <div class="support-top-bar">
      <div class="container top-bar-content">
        <div class="user-role-switch">
          <span 
            class="role-item" 
            :class="{ active: activeRole === 'seller' }" 
            @click="switchRole('seller')"
          >
            Tôi là người bán
          </span>
          <span 
            class="role-item" 
            :class="{ active: activeRole === 'buyer' }" 
            @click="switchRole('buyer')"
          >
            Tôi là người mua
          </span>
        </div>
        <div class="support-search">
          <font-awesome-icon icon="search" class="search-icon" />
          <input type="text" placeholder="Nhập từ khóa tìm kiếm..." />
        </div>
      </div>
    </div>

    <div class="container main-layout">
      
      <aside class="sidebar">
        <h3 class="sidebar-title">Danh mục hỗ trợ</h3>
        <ul class="menu-list">
          <li 
            v-for="(menu, index) in currentRoleData.sidebarMenu" 
            :key="index" 
            class="menu-item"
            :class="{ 'is-active-category': selectedCategory?.title === menu.title, 'is-open': menu.isOpen }"
          >
            <div class="menu-label" @click="toggleSidebarMenu(index)">
              <span>{{ menu.title }}</span>
              <font-awesome-icon :icon="menu.isOpen ? 'chevron-down' : 'chevron-right'" class="arrow-icon" />
            </div>

            <ul class="submenu" v-show="menu.isOpen">
              <li 
                v-for="(subItem, subIndex) in menu.items" 
                :key="subIndex"
                :class="{ active: isArticleActive(subItem) }"
                @click="goToDetailLevel(subItem, menu)"
              >
                {{ subItem }}
              </li>
            </ul>
          </li>
        </ul>
      </aside>

      <main class="content-area">
        
        <div class="breadcrumb">
          <span class="crumb-link" @click="goToRootLevel">Trung tâm trợ giúp</span> > 
          
          <span 
            class="crumb-link" 
            :class="{ current: viewMode === 'root' }"
            @click="goToRootLevel"
          >
            {{ activeRole === 'buyer' ? 'Tôi là người mua' : 'Tôi là người bán' }}
          </span>
          
          <template v-if="viewMode !== 'root'">
             > 
             <span 
                class="crumb-link"
                :class="{ current: viewMode === 'list' }" 
                @click="goToListLevel(selectedCategory)"
             >
               {{ selectedCategory?.title }}
             </span>
          </template>

          <template v-if="viewMode === 'detail'">
             > <span class="current">{{ selectedArticle }}</span>
          </template>
        </div>

        <div v-if="viewMode === 'root'" class="view-section fade-in">
          <h1 class="page-title">Xin chào, bạn cần hỗ trợ về vấn đề gì?</h1>
          
          <div class="categories-grid">
            <div 
              v-for="(cat, index) in currentRoleData.sidebarMenu" 
              :key="index" 
              class="category-card simple-card"
              @click="goToListLevel(cat)"
            >
              <div class="cat-info">
                <h3>{{ cat.title }}</h3>
                <p>{{ cat.items.length }} bài viết</p>
              </div>
              <font-awesome-icon icon="arrow-right" class="go-icon" />
            </div>
          </div>
        </div>

        <div v-else-if="viewMode === 'list'" class="view-section fade-in">
          <h2 class="category-heading">
            <font-awesome-icon icon="folder-open" /> {{ selectedCategory?.title }}
          </h2>
          
          <div class="article-list-container">
            <div 
              v-for="(item, index) in selectedCategory?.items" 
              :key="index"
              class="article-item-card"
              @click="goToDetailLevel(item, selectedCategory)"
            >
              <font-awesome-icon icon="file-alt" class="file-icon" />
              <span>{{ item }}</span>
            </div>
          </div>
        </div>

        <div v-else class="view-section fade-in">
          <h1 class="article-title">{{ selectedArticle }}</h1>
          
          <div class="article-body">
            <p><strong>VietMarket</strong> hướng dẫn chi tiết về: <em>{{ selectedArticle }}</em>.</p>
            <p>{{ currentRoleData.introSafety }}</p>

            <div class="banner-box">
              <div class="banner-text">
                <span class="small-text">HƯỚNG DẪN</span>
                <span class="big-text">{{ selectedCategory?.title.toUpperCase() }}</span>
              </div>
              <div class="banner-decor"></div>
            </div>

            <div class="step-box" v-for="(step, index) in currentRoleData.steps" :key="index" :class="{ 'blue-theme': index % 2 !== 0 }">
              <div class="step-number">0{{ index + 1 }}</div>
              <div class="step-content">
                <h3>{{ step.title }}</h3>
                <ul>
                  <li v-for="(point, pIndex) in step.points" :key="pIndex">{{ point }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

      </main>

    </div>
  </div>

  <AppFooter />
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import SearchHeader from '../components/layout/SearchHeader.vue';
import AppFooter from '../components/layout/AppFooter.vue';

// --- STATE ---
const activeRole = ref('buyer');      
const viewMode = ref('root');         
const selectedCategory = ref(null);   
const selectedArticle = ref('');      

// =================================================================
// 🟢 KHU VỰC CHỈNH SỬA NỘI DUNG (DATABASE)
// =================================================================
const database = reactive({
  
  // 1. DỮ LIỆU CHO NGƯỜI MUA
  buyer: {
    // --- [EDIT 1] Nội dung giới thiệu chung (hiện ở đầu bài viết) ---
    introSafety: 'Dưới đây là các thông tin chi tiết giúp bạn mua sắm an toàn và hiệu quả trên VietMarket.',
    
    // --- [EDIT 2] Danh sách Menu bên trái ---
    sidebarMenu: [
      {
        title: 'Mẹo mua hàng', // <-- Tên danh mục lớn 1
        isOpen: true,
        items: [ // <-- Danh sách bài viết con của mục này
          'Mẹo mua hàng an toàn', 
          'Mẹo khi mua đồ điện tử', 
          'Các trường hợp lừa đảo cần tránh'
        ]
      },
      {
        title: 'Tài khoản & Hồ sơ', // <-- Tên danh mục lớn 2
        isOpen: false,
        items: [ // <-- Danh sách bài viết con
          'Cách đăng ký tài khoản', 
          'Quên mật khẩu', 
          'Thay đổi số điện thoại', 
          'Xóa tài khoản'
        ]
      },
      {
        title: 'Thanh toán & Nạp Đồng', // <-- Tên danh mục lớn 3
        isOpen: false,
        items: [
          'Phương thức thanh toán', 
          'Hướng dẫn nạp Đồng Tốt', 
          'Lịch sử giao dịch', 
          'Yêu cầu hoàn tiền'
        ]
      },
      {
        title: 'Khiếu nại & Báo cáo', // <-- Tên danh mục lớn 4
        isOpen: false,
        items: [
          'Báo cáo tin đăng vi phạm', 
          'Tố cáo người bán lừa đảo', 
          'Quy trình giải quyết tranh chấp'
        ]
      }
    ],

    // --- [EDIT 3] Nội dung chi tiết các bước (Hiện tại đang dùng chung cho mọi bài của Người Mua) ---
    steps: [
      { 
        title: 'BƯỚC 1: KIỂM TRA THÔNG TIN', // Tiêu đề bước 1
        points: [ // Các gạch đầu dòng của bước 1
          'Xem kỹ đánh giá.', 
          'So sánh giá cả.'
        ] 
      },
      { 
        title: 'BƯỚC 2: GIAO DỊCH AN TOÀN', // Tiêu đề bước 2
        points: [ // Các gạch đầu dòng của bước 2
          'Không cọc trước.', 
          'Giao dịch nơi đông người.'
        ] 
      }
    ]
  },

  // 2. DỮ LIỆU CHO NGƯỜI BÁN
  seller: {
    // --- [EDIT 4] Nội dung giới thiệu chung cho người bán ---
    introSafety: 'Các hướng dẫn giúp bạn bán hàng nhanh chóng và quản lý tin đăng hiệu quả.',

    // --- [EDIT 5] Danh sách Menu bên trái cho người bán ---
    sidebarMenu: [
      {
        title: 'Mẹo bán hàng', // <-- Tên danh mục lớn
        isOpen: true,
        items: ['Quy định đăng tin', 'Mẹo bán nhanh', 'Chụp ảnh sản phẩm đẹp']
      },
      {
        title: 'Quản lý tin đăng', // <-- Tên danh mục lớn
        isOpen: false,
        items: ['Sửa tin đăng', 'Đẩy tin lên đầu', 'Ẩn/Hiện tin', 'Gia hạn tin']
      },
      {
        title: 'Tài khoản doanh nghiệp', // <-- Tên danh mục lớn
        isOpen: false,
        items: ['Đăng ký Shop Uy Tín', 'Quyền lợi Đối tác', 'Bảng giá dịch vụ']
      }
    ],

    // --- [EDIT 6] Nội dung chi tiết các bước (Dùng chung cho mọi bài của Người Bán) ---
    steps: [
      { 
        title: 'CHUẨN BỊ NỘI DUNG', 
        points: ['Hình ảnh rõ nét.', 'Mô tả trung thực.'] 
      },
      { 
        title: 'CHĂM SÓC KHÁCH', 
        points: ['Trả lời nhanh.', 'Thái độ lịch sự.'] 
      }
    ]
  }
});
// =================================================================

const currentRoleData = computed(() => database[activeRole.value]);

// --- METHODS ---

const switchRole = (role) => {
  activeRole.value = role;
  goToRootLevel();
};

const toggleSidebarMenu = (index) => {
  const menus = currentRoleData.value.sidebarMenu;
  const isCurrentlyOpen = menus[index].isOpen;
  menus.forEach(m => m.isOpen = false);
  if (!isCurrentlyOpen) menus[index].isOpen = true;
};

const goToRootLevel = () => {
  viewMode.value = 'root';
  selectedCategory.value = null;
  selectedArticle.value = '';
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const goToListLevel = (categoryObj) => {
  selectedCategory.value = categoryObj;
  
  // Đồng bộ mở menu bên trái cho khớp
  currentRoleData.value.sidebarMenu.forEach(m => {
    if (m.title === categoryObj.title) m.isOpen = true;
    else m.isOpen = false;
  });

  viewMode.value = 'list';
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const goToDetailLevel = (articleTitle, categoryObj) => {
  selectedCategory.value = categoryObj;
  selectedArticle.value = articleTitle;
  viewMode.value = 'detail';
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const isArticleActive = (title) => {
  return viewMode.value === 'detail' && selectedArticle.value === title;
};
</script>

<style scoped>
:root {
  --primary-blue: #0055aa;
  --primary-yellow: #ffc107;
  --light-blue-bg: #e6f0fa;
  --light-yellow-bg: #fff9c4;
}

.support-page-wrapper { background-color: #fff; min-height: 100vh; }
.container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }

/* Breadcrumb */
.breadcrumb { 
  font-size: 0.95rem; color: #888; margin-bottom: 2rem; 
  display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;
}
.breadcrumb .crumb-link { 
  color: #555; cursor: pointer; transition: color 0.2s; 
}
.breadcrumb .crumb-link:hover { color: #0055aa; text-decoration: underline; }
.breadcrumb .current { color: #0055aa; font-weight: 700; }

/* Top Bar */
.support-top-bar { 
  background: #fff; 
  border-bottom: 1px solid #eee; 
  padding: 0; /* Xóa padding để kiểm soát height */
  margin-bottom: 2rem; 
}
.top-bar-content { 
  display: flex; 
  justify-content: space-between; 
  align-items: center;
  height: 60px; /* Đặt chiều cao cố định */
}
.user-role-switch { 
  display: flex; 
  gap: 2rem; 
  font-weight: 600; 
  color: #777; 
  height: 100%; /* Chiếm full chiều cao container */
}
.role-item { 
  cursor: pointer; 
  position: relative; 
  transition: color 0.2s; 
  display: flex;
  align-items: center; /* Căn giữa chữ */
  height: 100%; /* Full height để border nằm đúng đáy */
  padding: 0 5px; /* Thêm chút padding ngang */
}
.role-item:hover, .role-item.active { color: #0055aa; }

/* Thanh vàng nằm sát đáy tuyệt đối */
.role-item.active::after { 
  content: ''; 
  position: absolute; 
  bottom: 0; /* Sát đáy 0 */
  left: 0; 
  width: 100%; 
  height: 4px; 
  background: #ffc107; 
}

.support-search { position: relative; width: 400px; }
.support-search input { width: 100%; padding: 0.6rem 1rem 0.6rem 2.5rem; border: 1px solid #ddd; border-radius: 20px; outline: none; transition: border-color 0.2s; }
.support-search input:focus { border-color: #0055aa; box-shadow: 0 0 0 2px rgba(0,85,170,0.1); }
.search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #999; }

/* Layout */
.main-layout { display: flex; gap: 3rem; align-items: flex-start; margin-bottom: 4rem; }
.sidebar { width: 25%; }
.content-area { width: 75%; }

/* Menu Sidebar */
.sidebar-title { font-size: 0.85rem; color: #999; margin-bottom: 1rem; font-weight: 700; text-transform: uppercase; }
.menu-list { list-style: none; padding: 0; }
.menu-label { 
  display: flex; justify-content: space-between; align-items: center;
  padding: 0.8rem 0.5rem; font-weight: 600; color: #333; cursor: pointer;
  border-bottom: 1px solid #f0f0f0; transition: all 0.2s; border-radius: 4px;
}
.menu-label:hover { color: #0055aa; background-color: #f9f9f9; }
.menu-item.is-active-category .menu-label { 
  color: #0055aa; 
  background-color: var(--light-blue-bg);
  border-right: 4px solid #ffc107; 
}
.menu-item.is-open .menu-label { border-bottom-color: transparent; }

.arrow-icon { font-size: 0.8rem; color: #888; transition: transform 0.2s; }

.submenu { list-style: none; padding-left: 0.5rem; margin-bottom: 1rem; margin-top: 0.2rem; }
.submenu li { 
  padding: 0.6rem 0.5rem; color: #555; cursor: pointer; font-size: 0.95rem; transition: all 0.2s; border-radius: 4px;
}
.submenu li:hover { color: #0055aa; background-color: var(--light-yellow-bg); }
.submenu li.active { color: #0055aa; font-weight: 700; background-color: #fff59d; }

/* --- VIEW 1: ROOT --- */
.page-title { font-size: 1.8rem; color: #333; margin-bottom: 1.5rem; font-weight: 700; }
.categories-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }

.category-card {
  border: 1px solid #dbeafe;
  border-radius: 12px; padding: 1.8rem;
  display: flex; align-items: center; justify-content: space-between; 
  cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
  background: #fff;
}
.category-card:hover { 
  transform: translateY(-3px); 
  box-shadow: 0 5px 15px rgba(0,85,170, 0.12); 
  border-color: #0055aa; 
}
.cat-info h3 { margin: 0; font-size: 1.2rem; color: #333; font-weight: 600; transition: color 0.2s; }
.category-card:hover .cat-info h3 { color: #0055aa; }
.cat-info p { margin: 0.3rem 0 0; font-size: 0.95rem; color: #888; }
.go-icon { font-size: 1rem; color: #ccc; transition: color 0.2s; }
.category-card:hover .go-icon { color: #ffc107; }

/* --- VIEW 2: LIST --- */
.category-heading { font-size: 1.5rem; color: #0055aa; margin-bottom: 1.5rem; border-bottom: 2px solid #e6f0fa; padding-bottom: 0.5rem; }
.article-list-container { display: flex; flex-direction: column; gap: 0.8rem; }
.article-item-card {
  padding: 1rem; border: 1px solid #eee; border-radius: 8px; cursor: pointer;
  display: flex; align-items: center; gap: 0.8rem; font-size: 1rem; color: #444;
  transition: all 0.2s; background: #fff;
}
.article-item-card:hover { 
  background-color: var(--light-blue-bg); 
  border-color: #0055aa; 
  color: #0055aa; 
  padding-left: 1.2rem; 
}
.file-icon { color: #999; transition: color 0.2s; }
.article-item-card:hover .file-icon { color: #ffc107; }

/* --- VIEW 3: DETAIL --- */
.article-title { font-size: 2rem; color: #333; margin-bottom: 1.5rem; font-weight: 700; line-height: 1.3; }
.article-body p { line-height: 1.6; margin-bottom: 1rem; color: #444; }
.banner-box { background: linear-gradient(135deg, #ffc107 0%, #ffdb6e 100%); border-radius: 12px; padding: 2rem; margin: 2rem 0; display: flex; align-items: center; justify-content: center; min-height: 180px; box-shadow: 0 4px 10px rgba(255, 193, 7, 0.2); }
.banner-text { text-align: center; color: #fff; display: flex; flex-direction: column; text-shadow: 1px 1px 4px rgba(0,0,0,0.1); }
.small-text { font-size: 1.2rem; font-weight: 800; letter-spacing: 2px; color: #0055aa; background: #fff; padding: 0.2rem 0.5rem; border-radius: 4px; display: inline-block; margin-bottom: 0.5rem; transform: rotate(-5deg); align-self: center; }
.big-text { font-size: 2.5rem; font-weight: 900; line-height: 1.1; -webkit-text-stroke: 1px #e6a800; }
.step-box { background-color: #fff9c4; border-radius: 50px 12px 12px 50px; display: flex; align-items: flex-start; margin-bottom: 1.5rem; padding: 1.5rem 1.5rem 1.5rem 0; }
.step-number { background-color: #ffc107; color: #fff; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; flex-shrink: 0; margin-right: 1.5rem; border: 4px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.step-content h3 { margin: 0 0 0.5rem 0; font-size: 1.1rem; color: #333; font-weight: 800; text-transform: uppercase; }
.step-content ul { padding-left: 1.2rem; margin: 0; color: #555; }
.step-content li { margin-bottom: 0.3rem; }
.step-box.blue-theme { background-color: #e6f0fa; }
.step-box.blue-theme .step-number { background-color: #0055aa; }

.fade-in { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>