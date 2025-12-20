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

      <!-- BANK ACCOUNTS SECTION -->
      <div class="bank-accounts-section">
        <div class="section-header">
          <h3 class="section-title">Tài khoản ngân hàng liên kết</h3>
          <button class="btn-add-bank" @click="showAddBankModal = true">
            + Thêm tài khoản
          </button>
        </div>

        <div v-if="loadingBanks" class="loading-text">Đang tải...</div>
        
        <div v-else-if="bankAccounts.length === 0" class="empty-banks">
          <p>Chưa có tài khoản ngân hàng nào được liên kết.</p>
          <p class="hint">Thêm tài khoản để có thể rút tiền về ngân hàng.</p>
        </div>

        <div v-else class="bank-list">
          <div v-for="bank in bankAccounts" :key="bank.id" class="bank-card">
            <div class="bank-info">
              <span class="bank-name">{{ bank.bank_name }}</span>
            </div>
            <div class="bank-details">
              <div class="account-number">{{ bank.account_number_masked }}</div>
              <div class="account-holder">{{ bank.account_holder_name }}</div>
              <div v-if="bank.branch" class="branch">{{ bank.branch }}</div>
            </div>
            <div class="bank-actions">
              <button class="btn-delete-bank" @click="deleteBank(bank.id)">
                Xóa
              </button>
            </div>
          </div>
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
                <!-- [MỚI] Expense Type -->
                <div v-else-if="t.type === 'expense' || (!t.type && Number(t.amount) < 0 && t.order_id)" class="expense-type">
                   Thanh toán đơn hàng
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
              min="10000"
              max="500000000"
              placeholder="Tối thiểu 10.000đ"
              class="form-input"
              @keypress="onlyNumber"
            >
            <small class="limit-hint">Giới hạn: 10.000đ - 500.000.000đ</small>
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
              min="50000"
              max="500000000"
              placeholder="Tối thiểu 50.000đ"
              class="form-input"
              @keypress="onlyNumber"
            >
            <small class="limit-hint">Giới hạn: 50.000đ - 500.000.000đ</small>
          </div>

          <div class="form-group">
            <label>Chọn tài khoản nhận tiền</label>
            <div v-if="bankAccounts.length === 0" class="no-bank-warning">
              <p>⚠️ Bạn chưa liên kết tài khoản ngân hàng nào.</p>
              <button type="button" class="btn-link" @click="showWithdrawModal = false; showAddBankModal = true">
                + Thêm tài khoản ngay
              </button>
            </div>
            <select v-else v-model="withdrawForm.bank_account_id" class="form-input">
              <option value="">-- Chọn tài khoản --</option>
              <option v-for="bank in bankAccounts" :key="bank.id" :value="bank.id">
                {{ bank.bank_name }} - {{ bank.account_number_masked }} ({{ bank.account_holder_name }})
              </option>
            </select>
          </div>

          <div v-if="selectedBankForWithdraw" class="selected-bank-info">
            <div class="info-row"><span>Ngân hàng:</span> <strong>{{ selectedBankForWithdraw.bank_name }}</strong></div>
            <div class="info-row"><span>Số TK:</span> <strong>{{ selectedBankForWithdraw.account_number_masked }}</strong></div>
            <div class="info-row"><span>Chủ TK:</span> <strong>{{ selectedBankForWithdraw.account_holder_name }}</strong></div>
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

    <!-- Modal Thêm Tài Khoản Ngân Hàng -->
    <div v-if="showAddBankModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Thêm Tài Khoản Ngân Hàng</h3>
          <button class="btn-close" @click="showAddBankModal = false">&times;</button>
        </div>
        
        <div class="modal-body">
          <div class="form-group">
            <label>Ngân hàng *</label>
            <select v-model="newBankForm.bank_name" class="form-input">
              <option value="">-- Chọn ngân hàng --</option>
              <option value="Vietcombank">Vietcombank</option>
              <option value="Techcombank">Techcombank</option>
              <option value="BIDV">BIDV</option>
              <option value="VietinBank">VietinBank</option>
              <option value="MB Bank">MB Bank</option>
              <option value="ACB">ACB</option>
              <option value="Sacombank">Sacombank</option>
              <option value="TPBank">TPBank</option>
              <option value="VPBank">VPBank</option>
              <option value="Agribank">Agribank</option>
            </select>
          </div>

          <div class="form-group">
            <label>Số tài khoản *</label>
            <input v-model="newBankForm.account_number" type="text" class="form-input" placeholder="VD: 1234567890">
          </div>

          <div class="form-group">
            <label>Tên chủ tài khoản *</label>
            <input v-model="newBankForm.account_holder_name" type="text" class="form-input uppercase" placeholder="VD: NGUYEN VAN A">
          </div>

          <div class="form-group">
            <label>Chi nhánh (tùy chọn)</label>
            <input v-model="newBankForm.branch" type="text" class="form-input" placeholder="VD: Chi nhánh Quận 1">
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" @click="showAddBankModal = false">Hủy</button>
          <button class="btn-primary" @click="handleAddBank" :disabled="isSubmitting">
            {{ isSubmitting ? 'Đang xử lý...' : 'Thêm tài khoản' }}
          </button>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
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
const showDepositModal = ref(false);

// Bank Accounts
const showAddBankModal = ref(false);
const bankAccounts = ref([]);
const loadingBanks = ref(false);
const newBankForm = ref({
    bank_name: '',
    account_number: '',
    account_holder_name: '',
    branch: ''
});

const depositAmount = ref('');
const withdrawForm = ref({
    amount: '',
    bank_account_id: '' // ID của tài khoản ngân hàng đã liên kết
});

// Computed: Lấy thông tin bank đang chọn để hiển thị preview
const selectedBankForWithdraw = computed(() => {
    if (!withdrawForm.value.bank_account_id) return null;
    return bankAccounts.value.find(b => b.id === withdrawForm.value.bank_account_id);
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

// Load danh sách tài khoản ngân hàng
const fetchBankAccounts = async () => {
    loadingBanks.value = true;
    try {
        const res = await api.get('/user/bank-accounts');
        bankAccounts.value = res.data.data || [];
    } catch (error) {
        console.error("Lỗi tải bank accounts:", error);
    } finally {
        loadingBanks.value = false;
    }
};

// Thêm tài khoản mới
const handleAddBank = async () => {
    if (!newBankForm.value.bank_name || !newBankForm.value.account_number || !newBankForm.value.account_holder_name) {
        showError('Vui lòng nhập đầy đủ thông tin bắt buộc!');
        return;
    }
    isSubmitting.value = true;
    try {
        await api.post('/user/bank-accounts', newBankForm.value);
        showSuccess('Thêm tài khoản ngân hàng thành công!');
        showAddBankModal.value = false;
        newBankForm.value = { bank_name: '', account_number: '', account_holder_name: '', branch: '' };
        await fetchBankAccounts();
    } catch (error) {
        showError(error.response?.data?.message || 'Lỗi thêm tài khoản');
    } finally {
        isSubmitting.value = false;
    }
};

// Đặt làm mặc định
const setDefaultBank = async (id) => {
    try {
        await api.put(`/user/bank-accounts/${id}/set-default`);
        showSuccess('Đã đặt làm tài khoản mặc định');
        await fetchBankAccounts();
    } catch (error) {
        showError('Lỗi đặt mặc định');
    }
};

// Xóa tài khoản
const deleteBank = async (id) => {
    if (!confirm('Bạn có chắc muốn xóa tài khoản ngân hàng này?')) return;
    try {
        await api.delete(`/user/bank-accounts/${id}`);
        showSuccess('Đã xóa tài khoản ngân hàng');
        await fetchBankAccounts();
    } catch (error) {
        showError('Lỗi xóa tài khoản');
    }
};

// Xử lý nạp tiền
const handleDeposit = async () => {
    if (!depositAmount.value || depositAmount.value < 10000) {
        showError('Vui lòng nhập số tiền hợp lệ (tối thiểu 10.000đ)');
        return;
    }
    if (depositAmount.value > 500000000) {
        showError('Số tiền nạp tối đa là 500.000.000đ');
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
    if (!withdrawForm.value.amount || withdrawForm.value.amount < 50000) {
        showError('Số tiền rút tối thiểu là 50.000đ');
        return;
    }
    if (withdrawForm.value.amount > 500000000) {
        showError('Số tiền rút tối đa là 500.000.000đ');
        return;
    }
    if (withdrawForm.value.amount > balance.value) {
        showError('Số tiền vượt quá số dư khả dụng!');
        return;
    }
    if (!withdrawForm.value.bank_account_id) {
        showError('Vui lòng chọn tài khoản ngân hàng nhận tiền!');
        return;
    }
    isSubmitting.value = true;
    try {
        await api.post('/wallet/withdraw', {
            amount: withdrawForm.value.amount,
            bank_account_id: withdrawForm.value.bank_account_id
        });
        showSuccess('Rút tiền thành công! Số dư đã được cập nhật.');
        showWithdrawModal.value = false;
        withdrawForm.value = { amount: '', bank_account_id: '' };
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

// Chỉ cho nhập số
const onlyNumber = (e) => {
    if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
    }
};

onMounted(() => {
    fetchWalletData();
    fetchBankAccounts();
});
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
.form-input::-webkit-inner-spin-button,
.form-input::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
.form-input[type=number] { -moz-appearance: textfield; appearance: textfield; }
.limit-hint { display: block; font-size: 11px; color: #888; margin-top: 4px; }
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

/* Bank Accounts Section */
.bank-accounts-section {
  background: white; border-radius: 12px; padding: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;
}
.section-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;
}
.btn-add-bank {
  background: #28a745; color: white; border: none; padding: 8px 16px;
  border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;
}
.btn-add-bank:hover { background: #218838; }

.empty-banks { text-align: center; padding: 30px; color: #666; }
.empty-banks .hint { font-size: 13px; color: #999; margin-top: 5px; }

.bank-list { display: flex; flex-direction: column; gap: 12px; }
.bank-card {
  display: flex; justify-content: space-between; align-items: center;
  padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px;
  background: #fafafa; transition: all 0.2s;
}
.bank-card:hover { border-color: #007bff; }
.bank-card.is-default { background: #e7f1ff; border-color: #a7d1ff; }

.bank-info { display: flex; align-items: center; gap: 10px; }
.bank-name { font-weight: 700; color: #333; font-size: 14px; }
.default-badge {
  background: #007bff; color: white; font-size: 10px; padding: 2px 8px;
  border-radius: 10px; font-weight: 600;
}

.bank-details { flex: 1; padding-left: 20px; }
.account-number { font-family: monospace; font-size: 15px; color: #333; font-weight: 600; }
.account-holder { font-size: 13px; color: #666; margin-top: 2px; }
.branch { font-size: 12px; color: #999; margin-top: 2px; }

.bank-actions { display: flex; gap: 8px; }
.btn-set-default {
  background: white; border: 1px solid #007bff; color: #007bff;
  padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;
}
.btn-set-default:hover { background: #007bff; color: white; }
.btn-delete-bank {
  background: white; border: 1px solid #ff9aa5; color: #dc3545;
  padding: 6px 14px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;
}
.btn-delete-bank:hover { background: #dc3545; color: white; border: none; }

.uppercase { text-transform: uppercase; }

/* Withdraw Modal - Bank Selection */
.no-bank-warning {
  background: #fff3cd; padding: 15px; border-radius: 6px;
  text-align: center; color: #856404;
}
.no-bank-warning p { margin: 0 0 10px 0; }
.btn-link {
  background: none; border: none; color: #007bff;
  cursor: pointer; font-weight: 600; text-decoration: underline;
}
.btn-link:hover { color: #0056b3; }

.selected-bank-info {
  background: #e7f1ff; border: 1px solid #b8daff; border-radius: 6px;
  padding: 12px; margin-top: 15px;
}
.selected-bank-info .info-row {
  display: flex; justify-content: space-between;
  font-size: 13px; padding: 3px 0;
}
.selected-bank-info .info-row span { color: #666; }
.selected-bank-info .info-row strong { color: #333; }
</style>