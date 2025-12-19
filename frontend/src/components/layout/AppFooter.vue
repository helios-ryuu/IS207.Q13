<template>
  <footer class="site-footer">
    <div class="footer-container">

      <div class="footer-column">
        <h3 class="footer-heading">Hỗ trợ khách hàng</h3>
        <ul class="footer-links">
          <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'contact_support' }}">Trung tâm trợ giúp</router-link></li>
          <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'safe_tips' }}">An toàn mua bán</router-link></li>
          <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'contact_support' }}">Liên hệ hỗ trợ</router-link></li>
        </ul>
      </div>

      <div class="footer-column middle-column">
        <h3 class="footer-heading">Về VietMarket</h3>
        <div class="sub-columns">
          <ul class="footer-links">
            <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'intro' }}">Giới thiệu</router-link></li>
            <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'regulations' }}">Quy chế hoạt động sàn</router-link></li>
            <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'privacy' }}">Chính sách bảo mật</router-link></li>
            <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'dispute' }}">Giải quyết tranh chấp</router-link></li>
          </ul>
          <ul class="footer-links">
            <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'recruitment' }}">Tuyển dụng</router-link></li>
            <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'media' }}">Truyền thông</router-link></li>
            <li><router-link :to="{ path: '/support', query: { role: 'buyer', article: 'blog' }}">Blog</router-link></li>
          </ul>
        </div>
      </div>

      <div class="footer-column">
        <h3 class="footer-heading">Liên kết</h3>

        <div class="social-icons">
          <a href="#" class="social-icon fb">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          </a>
          <a href="#" class="social-icon yt">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          </a>
          <a href="#" class="social-icon insta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
          </a>
        </div>

        <div class="contact-info">
          <p>Email: vietmarket@gmail.com</p>
          <p>CSKH: 0123 456 789</p>
          <p>Địa chỉ: 123 Street</p>
        </div>
      </div>

    </div>
  </footer>
</template>

<style scoped>
.site-footer {
  background-color: #ffffff;
  border-top: 1px solid #e0e0e0; /* Đường kẻ mờ phía trên */
  padding: 40px 0;
  margin-top: 40px; /* Khoảng cách với nội dung phía trên */
  width: 100%;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap; /* Để responsive tốt hơn */
  gap: 30px;
}

/* Cột chung */
.footer-column {
  flex: 1;
  min-width: 200px;
}

/* Cột giữa rộng hơn một chút để chứa 2 cột con */
.middle-column {
  flex: 1.5;
}

/* Tiêu đề cột */
.footer-heading {
  font-size: 16px;
  font-weight: 800; /* In đậm như hình */
  color: #000;
  margin-bottom: 20px;
  text-transform: uppercase; /* Nếu muốn chữ hoa như hình thì uncomment dòng này, nhưng trong hình là chữ thường in đậm */
}

/* Danh sách link */
.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 12px;
}

.footer-links a {
  text-decoration: none;
  color: #333;
  font-size: 14px;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #007bff; /* Màu xanh khi hover */
}

/* Cấu trúc cột giữa (chia đôi) */
.sub-columns {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

/* Social Icons */
.social-icons {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
}

.social-icon {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: transform 0.2s;
}

.social-icon svg {
  width: 100%;
  height: 100%;
}

.social-icon:hover {
  transform: scale(1.1);
}

/* Màu sắc icon thương hiệu */
.social-icon.fb { color: #1877F2; }
.social-icon.yt { color: #FF0000; }
.social-icon.insta { color: #E1306C; }

/* Thông tin liên hệ */
.contact-info p {
  margin: 8px 0;
  font-size: 14px;
  color: #333;
  line-height: 1.5;
}

/* Responsive cho mobile */
@media (max-width: 768px) {
  .footer-container {
    flex-direction: column;
  }

  .sub-columns {
    grid-template-columns: 1fr; /* Trên mobile cột giữa xếp dọc */
  }
}
</style>