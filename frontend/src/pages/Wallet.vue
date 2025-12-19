<template>
  <div class="wallet-page">
    <Header />
    
    <div class="wallet-container">
      <div class="page-header">
        <h1 class="page-title">Ví Của Tôi</h1>
        <p class="page-subtitle">Quản lý số dư và lịch sử giao dịch</p>
      </div>

      <div class="balance-card">
        <div class="balance-info">
          <span class="label">Số dư khả dụng</span>
          <h2 class="amount">{{ formatCurrency(balance) }}</h2>
        </div>
        
        <div class="balance-actions">
          <button class="btn-action btn-deposit" @click="showDepositModal = true">
            <span>+ Nạp tiền</span>
          </button>
          
          <button class="btn-action btn-withdraw" @click="showWithdrawModal = true">
            <span>Rút tiền</span>
          </button>
        </div>
      </div>

      <div class="history-section">
        <h3 class="section-title">Lịch sử giao dịch</h3>
        
        <div class="table-responsive">
          <div v-if="loading" class="loading-text">Đang tải dữ liệu...</div>
          
          <table v-else class="transaction-table">
            <thead>
              <tr>
                <th>Mã GD</th>
                <th>Nội dung</th>
                <th>Số tiền</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
              </tr>
            </thead>
            <tbody>
            <tr v-for="t in transactions" :key="t.id">
              <td class="code">#{{ t.transaction_code || t.id }}</td>
              
              <td class="description">
                <div v-if="t.type === 'deposit' || (!t.type && Number(t.amount) > 0 && !t.order_id)" class="deposit-type">
                  Nạp tiền vào ví
                </div>
                <div v-else-if="t.type === 'income' || (!t.type && Number(t.amount) > 0 && t.order_id)" class="income-type">
                  Doanh thu đơn hàng
                </div>
                <div v-else class="expense-type">
                  Rút tiền về ngân hàng
                </div>
              </td>

              <td class="amount-col" :class="Number(t.amount) > 0 ? 'text-green' : 'text-red'">
                {{ Number(t.amount) > 0 ? '+' : '' }}{{ formatCurrency(t.amount) }}
              </td>

              <td class="time">{{ formatDate(t.transaction_date || t.created_at) }}</td>
              
              <td>
                <span class="status-badge" :class="getStatusClass(t.status)">
                  {{ getStatusLabel(t.status) }}
                </span>
              </td>
            </tr>
            
            <tr v-if="transactions.length === 0">
              <td colspan="5" class="empty-state">
                <p>Chưa có giao dịch nào phát sinh</p>
              </td>
            </tr>
          </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="showDepositModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Nạp Tiền Vào Ví</h3>
          <button class="btn-close" @click="showDepositModal = false">&times;</button>
        </div>
        
        <div class="modal-body">
          <div class="form-group">
            <label>Nhập số tiền muốn nạp (VNĐ)</label>
            <input 
              v-model.number="depositAmount" 
              type="number" 
              placeholder="VD: 100000"
              class="form-input"
            >
          </div>
          <p class="note-text text-blue">
            ⓘ Đây là tính năng mô phỏng. Hệ thống sẽ cộng tiền ngay lập tức để bạn test.
          </p>
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" @click="showDepositModal = false">Hủy</button>
          <button class="btn-primary" @click="handleDeposit" :disabled="isSubmitting">
            {{ isSubmitting ? 'Đang xử lý...' : 'Xác nhận Nạp' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="showWithdrawModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Rút Tiền Về Ngân Hàng</h3>
          <button class="btn-close" @click="showWithdrawModal = false">&times;</button>
        </div>
        
        <div class="modal-body">
          <div class="balance-hint">
             Số dư khả dụng: <strong>{{ formatCurrency(balance) }}</strong>
          </div>

          <div class="form-group">
            <label>Số tiền rút</label>
            <input 
              v-model.number="withdrawForm.amount" 
              type="number" 
              placeholder="Tối thiểu 50.000 đ"
              class="form-input"
            >
          </div>

          <div class="form-group">
            <label>Ngân hàng</label>
            <select v-model="withdrawForm.bank_name" class="form-input">
              <option value="">-- Chọn ngân hàng --</option>
              <option value="Vietcombank">Vietcombank</option>
              <option value="MBBank">MB Bank</option>
              <option value="Techcombank">Techcombank</option>
              <option value="ACB">ACB</option>
            </select>
          </div>

          <div class="form-group">
            <label>Số tài khoản</label>
            <input v-model="withdrawForm.bank_account" type="text" class="form-input">
          </div>

          <div class="form-group">
            <label>Tên chủ tài khoản</label>
            <input v-model="withdrawForm.account_holder" type="text" class="form-input uppercase">
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" @click="showWithdrawModal = false">Hủy</button>
          <button class="btn-danger" @click="handleWithdraw" :disabled="isSubmitting">
            {{ isSubmitting ? 'Đang xử lý...' : 'Xác nhận Rút' }}
          </button>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../utils/api';
import { useToast } from '../utils/useToast';
import Header from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';

const { showSuccess, showError } = useToast();

const balance = ref(0);
const transactions = ref([]);
const loading = ref(false);
const isSubmitting = ref(false);

const showWithdrawModal = ref(false);
const showDepositModal = ref(false); // [MỚI]

const depositAmount = ref(''); // [MỚI]
const withdrawForm = ref({
    amount: '',
    bank_name: '',
    bank_account: '',
    account_holder: ''
});

// Load dữ liệu
const fetchWalletData = async () => {
    loading.value = true;
    try {
        const resWallet = await api.get('/wallet');
        if(resWallet.data.data) {
             balance.value = parseFloat(resWallet.data.data.balance);
        }
        const resHistory = await api.get('/wallet/transactions');
        transactions.value = resHistory.data.data || [];
    } catch (error) {
        console.error("Lỗi tải ví:", error);
    } finally {
        loading.value = false;
    }
};

// [MỚI] Xử lý nạp tiền
const handleDeposit = async () => {
    if (!depositAmount.value || depositAmount.value < 10000) {
        showError('Vui lòng nhập số tiền hợp lệ (tối thiểu 10.000đ)');
        return;
    }
    isSubmitting.value = true;
    try {
        await api.post('/wallet/deposit', { amount: depositAmount.value });
        showSuccess('Nạp tiền thành công!');
        showDepositModal.value = false;
        depositAmount.value = '';
        await fetchWalletData(); // Refresh lại ví
    } catch (error) {
        showError(error.response?.data?.message || 'Lỗi nạp tiền');
    } finally {
        isSubmitting.value = false;
    }
};

// Xử lý rút tiền
const handleWithdraw = async () => {
    if (!withdrawForm.value.amount || withdrawForm.value.amount > balance.value) {
        showError('Số tiền không hợp lệ hoặc vượt quá số dư!');
        return;
    }
    isSubmitting.value = true;
    try {
        await api.post('/wallet/withdraw', withdrawForm.value);
        showSuccess('Yêu cầu rút tiền thành công!');
        showWithdrawModal.value = false;
        withdrawForm.value = { amount: '', bank_name: '', bank_account: '', account_holder: '' };
        await fetchWalletData();
    } catch (error) {
        showError(error.response?.data?.message || 'Lỗi rút tiền');
    } finally {
        isSubmitting.value = false;
    }
};

// Helpers format
const formatCurrency = (val) => {
    const num = Number(val);
    if(isNaN(num)) return '0 ₫';
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num);
};

const formatDate = (dateString) => {
    if(!dateString) return '';
    return new Date(dateString).toLocaleString('vi-VN');
};

const getStatusClass = (s) => ({
    'completed': 'status-success',
    'pending': 'status-warning',
    'failed': 'status-danger'
}[s] || 'status-default');

const getStatusLabel = (s) => ({
    'completed': 'Thành công',
    'pending': 'Chờ xử lý',
    'failed': 'Thất bại'
}[s] || s);

onMounted(() => fetchWalletData());
</script>

<style scoped>
.wallet-page { background: #f5f7fa; min-height: 100vh; }
.wallet-container { max-width: 1000px; margin: 0 auto; padding: 30px 20px; }

.page-header { margin-bottom: 25px; }
.page-title { font-size: 24px; font-weight: bold; color: #333; }
.page-subtitle { color: #666; font-size: 14px; }

/* Balance Card */
.balance-card {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  color: white; border-radius: 12px; padding: 30px;
  display: flex; justify-content: space-between; align-items: center;
  box-shadow: 0 8px 20px rgba(0, 86, 179, 0.2);
  margin-bottom: 30px;
}
.balance-info .label { font-size: 13px; opacity: 0.9; text-transform: uppercase; }
.balance-info .amount { font-size: 38px; font-weight: 800; margin-top: 5px; }

/* Action Buttons Container */
.balance-actions { display: flex; gap: 15px; }

/* Nút Chung */
.btn-action {
  padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; font-size: 14px;
  transition: transform 0.2s; display: flex; align-items: center; gap: 8px;
}
.btn-action:hover { transform: translateY(-2px); }

/* [MỚI] Nút Nạp tiền - Màu Vàng để nổi bật */
.btn-deposit {
  background-color: #ffc107; color: #333; 
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

/* Nút Rút tiền - Trong suốt */
.btn-withdraw {
  background: rgba(255,255,255,0.2); color: white;
  border: 1px solid rgba(255,255,255,0.5);
}

/* Table Section */
.history-section { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.section-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
.transaction-table { width: 100%; border-collapse: collapse; }
.transaction-table th { text-align: left; padding: 12px; background: #f8f9fa; color: #666; font-size: 13px; }
.transaction-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }

.text-green { color: #28a745; font-weight: bold; }
.text-red { color: #dc3545; font-weight: bold; }
.status-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.status-success { background: #d4edda; color: #155724; }
.status-warning { background: #fff3cd; color: #856404; }
.status-danger { background: #f8d7da; color: #721c24; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 999; }
.modal-content { background: white; width: 450px; border-radius: 10px; overflow: hidden; animation: slideIn 0.3s; }
.modal-header { padding: 15px 20px; background: #f8f9fa; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; }
.modal-header h3 { margin: 0; font-size: 16px; font-weight: bold; }
.btn-close { border: none; background: none; font-size: 20px; cursor: pointer; }
.modal-body { padding: 20px; }
.modal-footer { padding: 15px 20px; background: #f8f9fa; display: flex; justify-content: flex-end; gap: 10px; }

.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; }
.form-input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
.balance-hint { background: #e9ecef; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; }
.note-text { font-size: 12px; color: #0056b3; margin-top: 10px; background: #e7f1ff; padding: 8px; border-radius: 4px; }

.btn-primary { background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
.btn-secondary { background: white; border: 1px solid #ddd; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
.btn-danger { background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }

@keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@media (max-width: 600px) {
  .balance-card { flex-direction: column; align-items: flex-start; }
  .balance-actions { width: 100%; margin-top: 15px; }
  .btn-action { flex: 1; justify-content: center; }
}
</style>