<template>
  <MainLayout>
    <div class="p-8 max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Transactions</h1>
        <button @click="openAddModal" class="flex items-center gap-2 px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
          <i class="bi bi-plus-circle"></i>
          Add Transaction
        </button>
      </div>
      
      <div class="flex flex-col gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm">
          <div class="flex gap-4 items-center">
            <input v-model="searchQuery" type="text" placeholder="Search transactions..." class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
            <select v-model="filterCategory" class="px-4 py-2 border border-gray-300 rounded-md text-sm min-w-40 focus:outline-none focus:ring-2 focus:ring-purple-500">
              <option value="all">All Categories</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <select v-model="filterType" class="px-4 py-2 border border-gray-300 rounded-md text-sm min-w-40 focus:outline-none focus:ring-2 focus:ring-purple-500">
              <option value="all">All Types</option>
              <option value="income">Income</option>
              <option value="expense">Expense</option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoadingTx" class="bg-white rounded-xl shadow-sm p-16 flex items-center justify-center">
          <div class="text-center text-gray-400">
            <i class="bi bi-arrow-repeat text-3xl mb-2 block animate-spin"></i>
            <p class="text-sm">Loading transactions...</p>
          </div>
        </div>

        <!-- Transactions List -->
        <div v-else-if="filteredTransactions.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
          <div class="flex flex-col divide-y divide-gray-100">
            <div
              v-for="tx in filteredTransactions"
              :key="tx.id"
              class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors"
            >
              <div
                class="w-10 h-10 rounded-lg flex items-center justify-center text-lg"
                :class="tx.type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'"
              >
                <i :class="tx.category_icon || (tx.type === 'income' ? 'bi bi-arrow-down-circle' : 'bi bi-arrow-up-circle')"></i>
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-gray-900 truncate">{{ tx.description }}</h4>
                <p class="text-sm text-gray-500">{{ tx.category_name || 'Uncategorized' }} &bull; {{ tx.transaction_date }}</p>
              </div>
              <div class="font-semibold text-base whitespace-nowrap" :class="tx.type === 'income' ? 'text-green-600' : 'text-red-600'">
                {{ tx.type === 'income' ? '+' : '-' }}₱{{ Number(tx.amount).toLocaleString() }}
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl shadow-sm min-h-96">
          <div class="flex flex-col items-center justify-center h-full text-gray-400 text-center py-16">
            <i class="bi bi-arrow-left-right text-5xl mb-4 opacity-50"></i>
            <h3 class="font-semibold mb-2">No transactions yet</h3>
            <p class="text-sm opacity-80">Start adding transactions to see them here</p>
          </div>
        </div>
      </div>
    </div>

    <AddTransactionModal />
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import MainLayout from './MainLayout.vue';
import AddTransactionModal from './AddTransactionModal.vue';
import { useAddTransactionModal } from '@/composables/useAddTransactionModal';
import { useCategories } from '@/composables/useCategories';

const { open: openAddModal } = useAddTransactionModal();
const { categories, loadCategories } = useCategories();

const transactions = ref([]);
const isLoadingTx = ref(false);
const searchQuery = ref('');
const filterCategory = ref('all');
const filterType = ref('all');

const filteredTransactions = computed(() => {
  return transactions.value.filter(tx => {
    const matchesSearch = !searchQuery.value || 
      tx.description?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      tx.category_name?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesCategory = filterCategory.value === 'all' || String(tx.category_id) === String(filterCategory.value);
    const matchesType = filterType.value === 'all' || tx.type === filterType.value;
    return matchesSearch && matchesCategory && matchesType;
  });
});

const loadTransactions = async () => {
  isLoadingTx.value = true;
  try {
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user) return;

    const response = await fetch('/backend/transactions/list.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: user.id })
    });
    const text = await response.text();
    if (!text) return;
    try {
      const data = JSON.parse(text);
      if (data.success) {
        transactions.value = data.data;
      }
    } catch (e) {
      console.error('Invalid JSON from transactions:', text);
    }
  } catch (err) {
    console.error('Error loading transactions:', err);
  } finally {
    isLoadingTx.value = false;
  }
};

const onTransactionAdded = () => {
  loadTransactions();
};

onMounted(() => {
  loadTransactions();
  loadCategories();
  window.addEventListener('transaction-added', onTransactionAdded);
});

onUnmounted(() => {
  window.removeEventListener('transaction-added', onTransactionAdded);
});
</script>
