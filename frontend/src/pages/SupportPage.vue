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
        <div class="support-contact-info">
          <span><font-awesome-icon icon="phone" /> Hotline: 1900 1234</span>
          <span><font-awesome-icon icon="envelope" /> support@vietmarket.vn</span>
        </div>
      </div>
    </div>

    <div class="container main-layout">
      
      <aside class="sidebar">
        <h3 class="sidebar-title">Danh mục hỗ trợ</h3>
        <ul class="menu-list">
          <li 
            v-for="(menu, index) in currentRoleData" 
            :key="index" 
            class="menu-item"
            :class="{ 'is-active-category': selectedCategory?.id === menu.id, 'is-open': menu.isOpen }"
          >
            <div class="menu-label" @click="toggleSidebarMenu(index)">
              <span>{{ menu.title }}</span>
              <font-awesome-icon :icon="menu.isOpen ? 'chevron-down' : 'chevron-right'" class="arrow-icon" />
            </div>

            <ul class="submenu" v-show="menu.isOpen">
              <li 
                v-for="(article, subIndex) in menu.articles" 
                :key="subIndex"
                :class="{ active: isArticleActive(article) }"
                @click="goToDetailLevel(article, menu)"
              >
                {{ article.title }}
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
            {{ activeRole === 'buyer' ? 'Người Mua' : 'Người Bán' }}
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
             > <span class="current">{{ selectedArticle?.title }}</span>
          </template>
        </div>

        <!-- VIEW 1: DASHBOARD -->
        <div v-if="viewMode === 'root'" class="view-section fade-in">
          <h1 class="page-title">Xin chào, chúng tôi có thể giúp gì cho bạn?</h1>
          
          <div class="categories-grid">
            <div 
              v-for="(cat, index) in currentRoleData" 
              :key="index" 
              class="category-card simple-card"
              @click="goToListLevel(cat)"
            >
              <div class="cat-info">
                <h3>{{ cat.title }}</h3>
                <p>{{ cat.articles.length }} chủ đề</p>
              </div>
              <font-awesome-icon icon="arrow-right" class="go-icon" />
            </div>
          </div>

          <div class="quick-links">
             <h3>Câu hỏi thường gặp</h3>
             <ul>
               <li @click="quickAccess('policy_return')">Chính sách trả hàng & hoàn tiền?</li>
               <li @click="quickAccess('safe_tips')">Làm sao để mua hàng an toàn?</li>
               <li @click="quickAccess('forgot_pass')">Tôi quên mật khẩu?</li>
             </ul>
          </div>
        </div>

        <!-- VIEW 2: LIST ARTICLES -->
        <div v-else-if="viewMode === 'list'" class="view-section fade-in">
          <h2 class="category-heading">
            <font-awesome-icon icon="folder-open" /> {{ selectedCategory?.title }}
          </h2>
          
          <div class="article-list-container">
            <div 
              v-for="(article, index) in selectedCategory?.articles" 
              :key="index"
              class="article-item-card"
              @click="goToDetailLevel(article, selectedCategory)"
            >
              <font-awesome-icon icon="file-alt" class="file-icon" />
              <span>{{ article.title }}</span>
            </div>
          </div>
        </div>

        <!-- VIEW 3: DETAIL ARTICLE -->
        <div v-else class="view-section fade-in">
          <h1 class="article-title">{{ selectedArticle?.title }}</h1>
          <div class="article-meta">Cập nhật lần cuối: Hôm nay</div>
          
          <div class="article-body">
            <!-- Render HTML Content -->
            <div class="content-html" v-html="selectedArticle?.content"></div>

            <!-- Optional Steps Visualization -->
            <div v-if="selectedArticle?.steps" class="steps-container">
              <div class="step-box" v-for="(step, index) in selectedArticle.steps" :key="index" :class="{ 'blue-theme': index % 2 !== 0 }">
                <div class="step-number">{{ index + 1 }}</div>
                <div class="step-content">
                  <h3>{{ step.title }}</h3>
                  <p>{{ step.desc }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="feedback-section">
            <p>Bài viết này có hữu ích không?</p>
            <button class="btn-feedback">👍 Có</button>
            <button class="btn-feedback">👎 Không</button>
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
const selectedArticle = ref(null);      

import { onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const initFromQuery = () => {
  const { role, article } = route.query;
  
  if (role && (role === 'buyer' || role === 'seller')) {
    activeRole.value = role;
  }

  if (article) {
    // Tìm article trong database của role hiện tại
    const currentData = database[activeRole.value];
    if (currentData) {
      for (const cat of currentData) {
        const foundArticle = cat.articles.find(a => a.id === article);
        if (foundArticle) {
          goToDetailLevel(foundArticle, cat);
          return;
        }
      }
    }
  } else {
    // Nếu không có article, về root
    goToRootLevel();
  }
};

onMounted(() => {
  initFromQuery();
});

watch(() => route.query, () => {
  initFromQuery();
});      

// =================================================================
// 🟢 CƠ SỞ DỮ LIỆU HỖ TRỢ (Nội dung đầy đủ)
// =================================================================
const database = reactive({
  buyer: [
    {
      id: 'shopping_tips',
      title: 'Mẹo mua hàng an toàn',
      isOpen: true,
      articles: [
        {
          id: 'safe_tips',
          title: 'Hướng dẫn mua hàng an toàn',
          content: `
            <p>VietMarket là sàn thương mại điện tử C2C kết nối trực tiếp người mua và người bán. Để giao dịch an toàn, hãy tuân thủ <strong>"Quy tắc 3 KHÔNG"</strong>:</p>
            <ul>
              <li><strong>KHÔNG chuyển khoản trước:</strong> Tuyệt đối không cọc tiền, chuyển khoản trước khi nhận hàng. 99% các vụ lừa đảo bắt đầu bằng việc yêu cầu cọc.</li>
              <li><strong>KHÔNG giao dịch một mình nơi vắng vẻ:</strong> Hẹn gặp tại nơi công cộng, quán cafe, sảnh chung cư có camera hoặc bảo vệ. Đi cùng bạn bè, người thân nếu có thể.</li>
              <li><strong>KHÔNG ngại kiểm tra kỹ:</strong> 
                <ul>
                  <li>So sánh IMEI/Serial trên máy và vỏ hộp.</li>
                  <li>Kiểm tra các ốc vít xem có dấu hiệu tháo mở không.</li>
                  <li>Test kỹ các chức năng cơ bản (nghe gọi, wifi, camera, cảm ứng...).</li>
                </ul>
              </li>
            </ul>
          `
        },
        {
          id: 'scam_alert',
          title: 'Nhận biết dấu hiệu lừa đảo',
          content: `
            <p>Hãy cảnh giác cao độ nếu gặp các trường hợp sau:</p>
            <p>1. <strong>Giá rẻ bất thường:</strong> "IP 14 Pro Max giá 5 triệu" chắc chắn là lừa đảo (treo đầu dê bán thịt chó).</p>
            <p>2. <strong>Hối thúc đặt cọc:</strong> Kẻ gian thường viện cớ "đang có người khác hỏi mua", "cần tiền gấp" để giục bạn chuyển khoản giữ hàng.</p>
            <p>3. <strong>Dẫn dụ ra ngoài ứng dụng:</strong> Yêu cầu kết bạn Zalo/Facebook để gửi ảnh, nhưng thực chất là gửi đường link giả mạo chiếm đoạt tài khoản.</p>
            <p>4. <strong>Lý do gửi hàng xe khách:</strong> "Mình ở xa không giao trực tiếp được, bạn chuyển khoản mình gửi xe khách cho", đây là bẫy lừa đảo phổ biến.</p>
          `
        }
      ]
    },
    {
      id: 'account',
      title: 'Tài khoản & Hồ sơ',
      isOpen: false,
      articles: [
        {
          id: 'register',
          title: 'Đăng ký & Bảo mật',
          content: '<p>Để bảo vệ tài khoản, vui lòng sử dụng số điện thoại chính chủ. Không chia sẻ mã OTP cho bất kỳ ai, kể cả nhân viên VietMarket.</p>'
        },
        {
          id: 'forgot_pass',
          title: 'Quên mật khẩu / Bị khóa',
          content: '<p>Nếu quên mật khẩu, hãy dùng chức năng "Quên mật khẩu" tại màn hình đăng nhập. Nếu tài khoản bị khóa do vi phạm, vui lòng liên hệ hotro@vietmarket.vn.</p>'
        }
      ]
    },
    {
      id: 'payment',
      title: 'Thanh toán & Giao nhận',
      isOpen: false,
      articles: [
        {
          id: 'cod',
          title: 'Thanh toán trực tiếp',
          content: '<p>VietMarket khuyến khích hình thức <strong>"Tiền trao cháo múc"</strong>. Người mua và người bán gặp nhau trực tiếp, kiểm tra hàng hóa oke rồi mới thanh toán tiền mặt hoặc chuyển khoản tại chỗ.</p>'
        },
        {
          id: 'policy_return',
          title: 'Chính sách Đổi trả & Hoàn tiền',
          content: `
            <div style="background: #ffebee; padding: 15px; border-radius: 8px; border: 1px solid #ffcdd2; color: #c62828;">
              <strong>LƯU Ý QUAN TRỌNG:</strong> 
              <p style="margin: 5px 0 0;">VietMarket là nền tảng đăng tin rao vặt trung gian. Chúng tôi cung cấp công cụ để người mua và người bán kết nối với nhau.</p>
            </div>
            <p style="margin-top: 15px;">Do tính chất giao dịch trực tiếp giữa các cá nhân (C2C):</p>
            <ul>
                <li><strong>VietMarket KHÔNG hỗ trợ quy trình Trả hàng/Hoàn tiền trên hệ thống:</strong> Mọi yêu cầu đổi trả, bảo hành sau mua bán là thỏa thuận dân sự trực tiếp giữa Người Mua và Người Bán.</li>
                <li><strong>Trách nhiệm kiểm tra:</strong> Người mua có trách nhiệm kiểm tra kỹ lưỡng tình trạng sản phẩm trước khi thanh toán. Việc thanh toán đồng nghĩa với việc bạn đã chấp nhận tình trạng sản phẩm.</li>
                <li><strong>Giải quyết tranh chấp:</strong> Nếu phát sinh tranh chấp, hai bên tự thương lượng. VietMarket chỉ hỗ trợ cung cấp thông tin lịch sử chat/đăng tin nếu có yêu cầu từ cơ quan chức năng.</li>
            </ul>
          `,
          steps: [
             { title: 'Thỏa thuận trước', desc: 'Hỏi kỹ người bán về chính sách bao test ("bao test 7 ngày lỗi hoàn tiền" là thỏa thuận riêng của người bán).' },
             { title: 'Kiểm tra kỹ', desc: 'Không thanh toán khi chưa cầm sản phẩm trên tay và test mọi chức năng.' },
             { title: 'Giữ bằng chứng', desc: 'Lưu lại tin nhắn cam kết, số điện thoại của người bán.' }
          ]
        }
      ]
    },
    {
      id: 'general',
      title: 'Về VietMarket & Chính sách',
      isOpen: false,
      articles: [
        {
          id: 'intro',
          title: 'Giới thiệu về VietMarket',
          content: '<p>VietMarket là nền tảng thương mại điện tử C2C hàng đầu, nơi kết nối hàng triệu người mua và người bán. Sứ mệnh của chúng tôi là tạo ra một môi trường mua bán an toàn, minh bạch và thuận tiện cho cộng đồng.</p>'
        },
        {
          id: 'regulations',
          title: 'Quy chế hoạt động sàn',
          content: '<p>Quy chế này quy định các quyền và nghĩa vụ của người tham gia giao dịch trên sàn VietMarket. Tất cả thành viên phải tuân thủ nghiêm ngặt để đảm bảo quyền lợi chung.</p>'
        },
        {
          id: 'privacy',
          title: 'Chính sách bảo mật',
          content: '<p>Chúng tôi cam kết bảo mật tuyệt đối thông tin cá nhân của khách hàng. Dữ liệu chỉ được sử dụng cho mục đích cung cấp dịch vụ và không được chia sẻ cho bên thứ ba trái phép.</p>'
        },
        {
          id: 'dispute',
          title: 'Giải quyết tranh chấp',
          content: '<p>VietMarket khuyến khích thương lượng. Nếu không thành, chúng tôi sẽ đóng vai trò trung gian hòa giải dựa trên bằng chứng cung cấp.</p>'
        },
        {
          id: 'contact_support',
          title: 'Liên hệ hỗ trợ',
          content: '<p>Email: hotro@vietmarket.vn<br>Hotline: 1900 1234<br>Thời gian làm việc: 8h00 - 17h30 (Thứ 2 - Thứ 7)</p>'
        },
        {
            id: 'recruitment',
            title: 'Tuyển dụng',
            content: '<p>Hiện tại VietMarket chưa có đợt tuyển dụng mới. Vui lòng quay lại sau hoặc theo dõi fanpage của chúng tôi để cập nhật thông tin mới nhất.</p>'
        },
        {
            id: 'media',
            title: 'Truyền thông',
            content: '<p>Liên hệ hợp tác truyền thông: media@vietmarket.vn</p>'
        },
        {
             id: 'blog',
             title: 'Blog VietMarket',
             content: '<p>Khám phá các mẹo mua sắm, xu hướng thị trường và câu chuyện thành công tại Blog của chúng tôi.</p>'
        }
      ]
    }
  ],

  seller: [
    {
      id: 'selling_guide',
      title: 'Hướng dẫn bán hàng',
      isOpen: true,
      articles: [
        {
          id: 'post_ad',
          title: 'Quy định đăng tin',
          content: `
            <p>Tin đăng cần tuân thủ:</p>
            <ul>
              <li>Không bán hàng cấm, hàng giả, hàng nhái.</li>
              <li>Hình ảnh phải là ảnh thật của sản phẩm.</li>
              <li>Chọn đúng danh mục sản phẩm.</li>
            </ul>
          `
        },
        {
          id: 'promotions',
          title: 'Đẩy tin & Dịch vụ VIP',
          content: '<p>Sử dụng "Đẩy tin" để bài viết lên đầu trang tìm kiếm. Phí dịch vụ sẽ trừ vào ví Đồng Tốt của bạn.</p>'
        }
      ]
    },
    {
      id: 'order_process',
      title: 'Quy trình xử lý đơn',
      isOpen: false,
      articles: [
        {
          id: 'confirm_order',
          title: 'Xác nhận và Giao hàng',
          content: '<p>Khi có đơn mới, bạn cần xác nhận trong vòng 24h. Sau đó đóng gói và bàn giao cho đơn vị vận chuyển.</p>',
          steps: [
            { title: 'Bước 1', desc: 'Nhận thông báo đơn mới.' },
            { title: 'Bước 2', desc: 'Vào Quản lý đơn > Chấp nhận đơn.' },
            { title: 'Bước 3', desc: 'In phiếu gửi (hoặc ghi mã vận đơn) và đóng gói.' },
            { title: 'Bước 4', desc: 'Shipper đến lấy hàng.' }
          ]
        },
        {
          id: 'wallet',
          title: 'Rút tiền doanh thu',
          content: '<p>Tiền bán hàng sẽ được cộng vào Ví sau khi đơn hàng thành công (Khách xác nhận đã nhận hoặc sau 3 ngày không khiếu nại). Bạn có thể rút về ngân hàng bất cứ lúc nào.</p>'
        }
      ]
    }
  ]
});

const currentRoleData = computed(() => database[activeRole.value]);

// --- METHODS ---

const switchRole = (role) => {
  activeRole.value = role;
  goToRootLevel();
};

const toggleSidebarMenu = (index) => {
  const menus = currentRoleData.value;
  const isCurrentlyOpen = menus[index].isOpen;
  // Close others optional, maybe let multiple stay open
  menus.forEach(m => m.isOpen = false); 
  menus[index].isOpen = !isCurrentlyOpen; // Toggle
};

const goToRootLevel = () => {
  viewMode.value = 'root';
  selectedCategory.value = null;
  selectedArticle.value = null;
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const goToListLevel = (categoryObj) => {
  selectedCategory.value = categoryObj;
  
  // Mở menu tương ứng
  currentRoleData.value.forEach(m => {
    if (m.id === categoryObj.id) m.isOpen = true;
  });

  viewMode.value = 'list';
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const goToDetailLevel = (articleObj, categoryObj) => {
  if (categoryObj) selectedCategory.value = categoryObj;
  selectedArticle.value = articleObj;
  viewMode.value = 'detail';
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const quickAccess = (articleId) => {
  // Tìm article trong data
  for (const cat of currentRoleData.value) {
    const found = cat.articles.find(a => a.id === articleId);
    if (found) {
      goToDetailLevel(found, cat);
      return;
    }
  }
};

const isArticleActive = (article) => {
  return viewMode.value === 'detail' && selectedArticle.value?.id === article.id;
};
</script>

<style scoped>
:root {
  --primary-blue: #0055aa;
  --primary-yellow: #ffc107;
  --light-blue-bg: #e6f0fa;
  --light-yellow-bg: #fff9c4;
}

.support-page-wrapper { background-color: #f8f9fa; min-height: 100vh; padding-bottom: 3rem; }
.container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }

/* Breadcrumb */
.breadcrumb { 
  font-size: 0.95rem; color: #888; margin-bottom: 1.5rem; 
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
  margin-bottom: 2rem; 
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.top-bar-content { 
  display: flex; 
  justify-content: space-between; 
  align-items: center;
  height: 60px;
}
.user-role-switch { 
  display: flex; 
  gap: 2rem; 
  font-weight: 600; 
  color: #777; 
  height: 100%; 
}
.role-item { 
  cursor: pointer; 
  position: relative; 
  transition: color 0.2s; 
  display: flex;
  align-items: center; 
  height: 100%; 
  padding: 0 5px;
  font-size: 1rem;
}
.role-item:hover, .role-item.active { color: #0055aa; }
.role-item.active::after { 
  content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: #0055aa; 
}

.support-contact-info { display: flex; gap: 1.5rem; color: #555; font-size: 0.9rem; }
.support-contact-info span { display: flex; align-items: center; gap: 0.5rem; }

/* Layout */
.main-layout { display: flex; gap: 2rem; align-items: flex-start; }
.sidebar { width: 280px; flex-shrink: 0; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 1.5rem 0; }
.content-area { flex: 1; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 2rem; min-height: 500px; }

/* Menu Sidebar */
.sidebar-title { font-size: 0.9rem; color: #999; margin: 0 1.5rem 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.menu-list { list-style: none; padding: 0; margin: 0; }
.menu-label { 
  display: flex; justify-content: space-between; align-items: center;
  padding: 1rem 1.5rem; font-weight: 600; color: #333; cursor: pointer;
  border-left: 3px solid transparent; transition: all 0.2s;
}
.menu-label:hover { color: #0055aa; background-color: #f8fbff; }
.menu-item.is-active-category .menu-label { 
  color: #0055aa; 
  background-color: #f0f7ff;
  border-left-color: #0055aa; 
}
.arrow-icon { font-size: 0.8rem; color: #bbb; }

.submenu { list-style: none; padding: 0; background: #fcfcfc; border-bottom: 1px solid #eee; }
.submenu li { 
  padding: 0.8rem 1.5rem 0.8rem 2.5rem; color: #555; cursor: pointer; font-size: 0.95rem; transition: all 0.2s; 
}
.submenu li:hover { color: #0055aa; background-color: #edf5fe; }
.submenu li.active { color: #0055aa; font-weight: 600; background-color: #e3effd; }

/* --- VIEW 1: DASHBOARD --- */
.page-title { font-size: 1.6rem; color: #333; margin-bottom: 2rem; font-weight: 700; text-align: center; }
.categories-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 3rem; }

.category-card {
  border: 1px solid #eee;
  border-radius: 12px; padding: 1.5rem;
  display: flex; align-items: center; justify-content: space-between; 
  cursor: pointer; transition: all 0.2s; 
  background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.03);
}
.category-card:hover { 
  transform: translateY(-5px); 
  box-shadow: 0 10px 20px rgba(0,85,170, 0.1); 
  border-color: #0055aa; 
}
.cat-info h3 { margin: 0; font-size: 1.1rem; color: #333; font-weight: 700; }
.cat-info p { margin: 0.3rem 0 0; font-size: 0.9rem; color: #888; }
.go-icon { color: #ddd; transition: 0.2s; }
.category-card:hover .go-icon { color: #0055aa; }

.quick-links { background: #f8fbff; padding: 1.5rem; border-radius: 12px; border: 1px dashed #0055aa; }
.quick-links h3 { margin: 0 0 1rem; color: #0055aa; font-size: 1.1rem; }
.quick-links ul { list-style-type: none; padding: 0; display: flex; flex-direction: column; gap: 0.8rem; }
.quick-links li { cursor: pointer; color: #333; text-decoration: underline; font-weight: 500; transition: color 0.2s; }
.quick-links li:hover { color: #0055aa; }

/* --- VIEW 2: LIST --- */
.category-heading { font-size: 1.4rem; color: #0055aa; margin-bottom: 1.5rem; border-bottom: 2px solid #eee; padding-bottom: 1rem; }
.article-list-container { display: flex; flex-direction: column; gap: 1rem; }
.article-item-card {
  padding: 1.2rem; border: 1px solid #eee; border-radius: 8px; cursor: pointer;
  display: flex; align-items: center; gap: 1rem; font-size: 1rem; color: #444;
  transition: all 0.2s; background: #fff;
}
.article-item-card:hover { 
  border-color: #0055aa; 
  color: #0055aa; 
  background-color: #f8fbff;
  transform: translateX(5px);
}
.file-icon { color: #ccc; }
.article-item-card:hover .file-icon { color: #0055aa; }

/* --- VIEW 3: DETAIL --- */
.article-title { font-size: 1.8rem; color: #333; margin-bottom: 0.5rem; font-weight: 700; line-height: 1.3; }
.article-meta { color: #999; font-size: 0.9rem; margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 1rem; }
.article-body { color: #333; line-height: 1.6; font-size: 1rem; }
.content-html p { margin-bottom: 1rem; }
.content-html ul { padding-left: 1.5rem; margin-bottom: 1.5rem; }
.content-html li { margin-bottom: 0.5rem; }

.steps-container { margin-top: 2rem; }
.step-box { background: #f9f9f9; padding: 1.5rem; border-radius: 8px; display: flex; gap: 1.5rem; margin-bottom: 1rem; border-left: 4px solid #ddd; }
.step-number { width: 40px; height: 40px; background: #666; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
.step-content h3 { margin: 0 0 0.5rem; font-size: 1.1rem; color: #333; }
.step-content p { margin: 0; color: #666; }

.step-box.blue-theme { background: #f0f7ff; border-left-color: #0055aa; }
.step-box.blue-theme .step-number { background: #0055aa; }

.feedback-section { margin-top: 4rem; padding-top: 2rem; border-top: 1px solid #eee; text-align: center; }
.feedback-section p { color: #666; margin-bottom: 1rem; }
.btn-feedback { padding: 0.5rem 1.5rem; border: 1px solid #ddd; background: #fff; border-radius: 20px; cursor: pointer; margin: 0 0.5rem; transition: all 0.2s; }
.btn-feedback:hover { border-color: #0055aa; color: #0055aa; background: #f0f7ff; }

.fade-in { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>