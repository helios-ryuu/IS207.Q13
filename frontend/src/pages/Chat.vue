<template>
  <HeaderOther />

  <div v-if="conversations.length === 0" class="chat-page-container empty-state">
    <div class="empty-content">
      <img src="/empty-chat-icon.svg" alt="Không có tin nhắn" class="empty-svg">
      <h2>Bạn chưa có cuộc trò chuyện nào!</h2>
      <p>Trải nghiệm chat để làm rõ thông tin về mặt hàng trước khi bắt đầu thực hiện mua bán</p>
      <button @click="goToHome" class="home-btn">Về trang chủ</button>
    </div>
  </div>

  <div v-else class="chat-page-container">
    <div class="chat-layout">

      <aside class="chat-sidebar">
        <header class="sidebar-header">
          <h3>Chat</h3>
          <div class="search-wrapper">
            <font-awesome-icon icon="search" class="search-icon" />
            <input type="text" placeholder="Nhập 3 ký tự để tìm kiếm..." class="chat-search" v-model="searchTerm">
          </div>

          <div class="tabs">
            <button class="tab-btn" :class="{ active: activeTab === 'all' }" @click="activeTab = 'all'">Tất cả</button>
            <button class="tab-btn" :class="{ active: activeTab === 'unread' }" @click="activeTab = 'unread'">Chưa đọc</button>
          </div>
        </header>

        <div class="conversation-list">
          <div
              v-for="convo in filteredConversations"
              :key="convo.id"
              class="convo-item"
              :class="{ active: activeConversationId == convo.id, unread: convo.unread }"
              @click="selectConversation(convo.id)"
          >
            <img :src="convo.avatar" alt="Avatar" class="convo-avatar">
            <div class="convo-details">
              <span class="convo-name">{{ convo.name }}</span>
              <span class="convo-preview">{{ convo.preview }}</span>
            </div>
            <span class="convo-time">{{ convo.time }}</span>
          </div>
        </div>
      </aside>

      <main class="chat-main" v-if="activeConversation">
        <header class="chat-header">
          <div class="header-left">
            <img :src="activeConversation.avatar" alt="Avatar" class="convo-avatar">
            <div>
              <h4>{{ activeConversation.name }}</h4>
              <span>Đang hoạt động</span>
            </div>
          </div>
          <button class="icon-btn"><font-awesome-icon icon="ellipsis-v" /></button>
        </header>

        <div class="message-area" ref="messageAreaRef">
          <div class="product-info-card" v-if="currentProductContext">
            <img src="https://via.placeholder.com/50" alt="SP" class="product-image">
            <div class="product-details">
              <span style="font-size: 0.8rem; color: #777;">Đang quan tâm:</span>
              <span style="font-weight: bold; color: #d70000; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">{{ currentProductContext }}</span>
            </div>
          </div>

          <div class="date-divider"><span>Hôm nay</span></div>

          <div
              v-for="message in activeMessages"
              :key="message.id"
              class="message"
              :class="message.type"
          >
            <span>{{ message.text }}</span>
          </div>
        </div>

        <footer class="message-input-area">
          <div class="quick-replies">
            <button @click="sendQuickReply('Sản phẩm này còn không ạ?')">Sản phẩm còn không?</button>
            <button @click="sendQuickReply('Có fix giá không bạn?')">Có fix giá không?</button>
          </div>
          <div class="input-row">
            <button class="icon-btn"><font-awesome-icon icon="camera" /></button>
            <input type="text" placeholder="Nhập tin nhắn..." v-model="newMessage" @keyup.enter="sendMessage()">
            <button class="send-btn" @click="sendMessage()">Gửi</button>
          </div>
        </footer>
      </main>

    </div>
  </div>

  <Footer />
</template>

<script setup>
import { ref, onMounted, watch, nextTick, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '../utils/useAuth';
import HeaderOther from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';

const router = useRouter();
const route = useRoute();
const { isLoggedIn } = useAuth();

const newMessage = ref('');
const messageAreaRef = ref(null);
const conversations = ref([]);
const activeConversationId = ref(null);
const activeTab = ref('all');
const searchTerm = ref('');
const allMessages = ref({});
const currentProductContext = ref(null);

watch(isLoggedIn, (isNowLoggedIn) => {
  if (isNowLoggedIn === false) router.push('/');
});

const activeConversation = computed(() => conversations.value.find(c => c.id == activeConversationId.value));

const activeMessages = computed(() => {
  if (!activeConversationId.value) return [];
  return allMessages.value[activeConversationId.value] || [];
});

const filteredConversations = computed(() => {
  return conversations.value.filter(convo => {
    const matchesTab = (activeTab.value === 'unread') ? convo.unread : true;
    const matchesSearch = convo.name.toLowerCase().includes(searchTerm.value.toLowerCase());
    return matchesTab && matchesSearch;
  });
});

onMounted(() => {
  // 1. Tải dữ liệu giả lập có sẵn
  conversations.value = [
    { id: 1, name: 'Cửa Hàng Giá Rẻ', preview: 'Sản phẩm này đã qua sử dụng chưa?', time: 'Vài giây trước', avatar: 'https://via.placeholder.com/40/007bff/fff?text=CH', unread: false },
    { id: 2, name: 'Điện Thoại Vui', preview: 'Bạn có ship không?', time: '5 phút trước', avatar: 'https://via.placeholder.com/40/ffc107/fff?text=DT', unread: false },
  ];
  allMessages.value = {
    1: [{ id: 101, text: 'Chào shop', type: 'sent' }, { id: 102, text: 'Chào bạn', type: 'received' }],
    2: [{ id: 201, text: 'Ship Cod ko?', type: 'sent' }]
  };

  // 2. XỬ LÝ KHI CHUYỂN TỪ TRANG CHI TIẾT SẢN PHẨM
  const { sellerId, sellerName, sellerAvatar, productName } = route.query;

  if (sellerId) {
    if (productName) currentProductContext.value = productName;

    // Tìm xem đã có cuộc hội thoại với người này chưa
    const existingConvo = conversations.value.find(c => c.id == sellerId);

    if (existingConvo) {
      // Nếu có, chọn nó
      selectConversation(sellerId);
    } else {
      // Nếu chưa, tạo mới
      const newConvo = {
        id: sellerId,
        name: sellerName || 'Người bán',
        avatar: sellerAvatar || 'https://via.placeholder.com/40/4caf50/fff?text=NB', // Avatar mặc định nếu thiếu
        preview: productName ? `Quan tâm: ${productName}` : 'Bắt đầu trò chuyện',
        time: 'Vừa xong',
        unread: false
      };

      conversations.value.unshift(newConvo);
      allMessages.value[sellerId] = []; // Tạo mảng tin nhắn trống
      selectConversation(sellerId);
    }
  } else if (conversations.value.length > 0) {
    // Nếu vào trực tiếp trang chat, chọn cái đầu tiên
    selectConversation(conversations.value[0].id);
  }
});

const selectConversation = (id) => {
  activeConversationId.value = id;
  const convo = conversations.value.find(c => c.id == id);
  if (convo) convo.unread = false;
  // Khi chuyển người, reset context sản phẩm nếu không phải người vừa bấm từ trang detail
  // (Logic đơn giản hóa cho demo)
  if (id != route.query.sellerId) {
    currentProductContext.value = null;
  } else if (route.query.productName) {
    currentProductContext.value = route.query.productName;
  }
  scrollToBottom();
};

const sendMessage = (textToSend = null) => {
  const text = textToSend || newMessage.value;
  if (!text || text.trim() === '') return;

  const newMsg = {
    id: Date.now(),
    text: text,
    type: 'sent',
  };

  if (!allMessages.value[activeConversationId.value]) {
    allMessages.value[activeConversationId.value] = [];
  }
  allMessages.value[activeConversationId.value].push(newMsg);

  // Cập nhật preview
  const convo = conversations.value.find(c => c.id == activeConversationId.value);
  if (convo) convo.preview = text;

  newMessage.value = '';
  scrollToBottom();
};

const sendQuickReply = (text) => sendMessage(text);

const scrollToBottom = () => {
  nextTick(() => {
    if (messageAreaRef.value) {
      messageAreaRef.value.scrollTop = messageAreaRef.value.scrollHeight;
    }
  });
};

const goToHome = () => router.push('/');
</script>

<style scoped>
.chat-page-container.empty-state { display: flex; justify-content: center; align-items: center; text-align: center; background-color: #f7f7f7; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
.empty-content { max-width: 450px; }
.empty-content h2 { font-size: 1.5rem; margin-top: 1.5rem; margin-bottom: 1rem; }
.empty-content p { color: #555; margin-bottom: 2rem; line-height: 1.5; }
.home-btn { padding: 0.75rem 2rem; font-size: 1rem; font-weight: 600; color: black; background-color: #f5a623; border: none; border-radius: 8px; cursor: pointer; }
.chat-page-container { max-width: 1200px; margin: 20px auto; padding: 0 15px; height: calc(100vh - 150px); }
.chat-layout { display: flex; height: 100%; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); overflow: hidden; }
.chat-sidebar { width: 300px; border-right: 1px solid #eee; display: flex; flex-direction: column; }
.sidebar-header { padding: 1rem; border-bottom: 1px solid #eee; }
.sidebar-header h3 { font-size: 1.5rem; margin-bottom: 1rem; }
.chat-search { width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border-radius: 8px; border: 1px solid #ddd; font-size: 0.9rem; background: #f5f5f5; }
.search-wrapper { position: relative; margin-bottom: 1rem; }
.search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #888; }
.tabs { display: flex; margin-top: 1rem; border-bottom: 1px solid #eee; }
.tab-btn { flex: 1; padding: 0.75rem 0.5rem; border: none; background: none; cursor: pointer; font-weight: 500; color: #777; border-bottom: 3px solid transparent; }
.tab-btn.active { color: #000; border-bottom-color: #f5a623; }
.conversation-list { overflow-y: auto; flex-grow: 1; }
.convo-item { display: flex; padding: 1rem; gap: 0.75rem; cursor: pointer; border-bottom: 1px solid #f5f5f5; position: relative; }
.convo-item:hover { background-color: #f9f9f9; }
.convo-item.active { background-color: #f0f8ff; }
.convo-item.unread .convo-name, .convo-item.unread .convo-preview { font-weight: bold; color: #000; }
.convo-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.convo-details { display: flex; flex-direction: column; flex-grow: 1; overflow: hidden; }
.convo-name { font-weight: 500; }
.convo-preview { font-size: 0.9rem; color: #777; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.convo-time { font-size: 0.75rem; color: #999; position: absolute; top: 1rem; right: 1rem; }
.chat-main { flex-grow: 1; display: flex; flex-direction: column; height: 100%; }
.chat-header { padding: 0.75rem 1.5rem; border-bottom: 1px solid #eee; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; }
.chat-header .header-left { display: flex; align-items: center; gap: 0.75rem; }
.chat-header h4 { margin: 0; }
.chat-header span { font-size: 0.85rem; color: #888; }
.icon-btn { background: none; border: none; font-size: 1.2rem; color: #555; cursor: pointer; }
.message-area { flex-grow: 1; overflow-y: auto; padding: 1rem; background-color: #f7f7f7; display: flex; flex-direction: column; gap: 0.75rem; }
.date-divider { text-align: center; font-size: 0.8rem; color: #888; margin: 0.5rem 0; }
.product-info-card { display: flex; gap: 0.75rem; padding: 0.75rem; background: white; border-radius: 8px; border: 1px solid #eee; max-width: 350px; align-self: center; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
.product-image { width: 50px; height: 50px; border-radius: 4px; object-fit: cover; }
.product-details { font-size: 0.9rem; font-weight: 500; display: flex; flex-direction: column; justify-content: center; }
.message { padding: 0.5rem 1rem; border-radius: 18px; max-width: 70%; width: fit-content; line-height: 1.4; }
.message.received { background-color: #e9e9e9; align-self: flex-start; border-bottom-left-radius: 4px; }
.message.sent { background-color: #fde8b1; color: #333; align-self: flex-end; border-bottom-right-radius: 4px; }
.message-input-area { padding: 1rem; border-top: 1px solid #eee; background: white; }
.quick-replies { display: flex; gap: 0.5rem; margin-bottom: 0.75rem; overflow-x: auto; }
.quick-replies button { padding: 0.5rem 0.75rem; background: #f5f5f5; border: 1px solid #ddd; border-radius: 20px; font-size: 0.85rem; cursor: pointer; white-space: nowrap; }
.input-row { display: flex; gap: 0.75rem; }
.input-row input { flex-grow: 1; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; outline: none; background: #f7f7f7; }
.input-row input:focus { border-color: #f5a623; background: white; }
.send-btn { padding: 0.75rem 1.25rem; border: none; background-color: #f5a623; color: black; border-radius: 8px; cursor: pointer; font-weight: 500; }
.empty-svg { max-width: 250px; margin-bottom: 1rem; color: #ccc; }
</style>