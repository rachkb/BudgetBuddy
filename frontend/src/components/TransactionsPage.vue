<template>
  <MainLayout>
    <div class="p-8 max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Transactions</h1>
        <button @click="openTransactionModal" class="flex items-center gap-2 px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
          <i class="bi bi-plus-circle"></i>
          Add Transaction
        </button>
      </div>
      
      <div class="flex flex-col gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm mb-8">
          <div class="flex gap-4 items-center">
            <input 
              type="text" 
              placeholder="Search transactions..." 
              class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm"
              v-model="searchQuery"
            />
            <select class="px-4 py-2 border border-gray-300 rounded-md text-sm min-w-40" v-model="selectedCategory">
              <option value="">All Categories</option>
              <option 
                v-for="category in categories" 
                :key="category.id" 
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-md text-sm min-w-40" v-model="selectedType">
              <option value="">All Types</option>
              <option value="expense">Expenses</option>
              <option value="income">Income</option>
            </select>
          </div>
        </div>

<div class="bg-white rounded-xl shadow-sm min-h-96">
          <div v-if="isLoading" class="flex flex-col items-center justify-center h-full text-gray-400 text-center py-16">
            <i class="bi bi-arrow-repeat text-5xl mb-4 animate-spin"></i>
            <p class="font-semibold">Loading transactions...</p>
          </div>
          
          <div v-else-if="filteredTransactions.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400 text-center py-16">
            <i class="bi bi-arrow-left-right text-5xl mb-4 opacity-50"></i>
            <h3 class="font-semibold mb-2">No transactions found</h3>
            <p class="text-sm opacity-80">{{ searchQuery || selectedCategory || selectedType ? 'Try adjusting your filters' : 'Start adding transactions to see them here' }}</p>
          </div>
          
          <div v-else class="divide-y divide-gray-200">
            <div 
              v-for="transaction in filteredTransactions" 
              :key="transaction.id"
              class="p-4 hover:bg-gray-50 transition-colors flex items-center gap-4"
            >
              <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="transaction.type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                <i :class="getTransactionIcon(transaction.category_name || transaction.category)"></i>
              </div>
              <div class="flex-1">
                <h4 class="font-semibold text-gray-900">{{ transaction.description }}</h4>
                <p class="text-sm text-gray-600">{{ transaction.category_name || 'Uncategorized' }} • {{ formatDate(transaction.date) }}</p>
                <p v-if="transaction.notes" class="text-sm text-gray-500 mt-1">{{ transaction.notes }}</p>
              </div>
              <div class="font-semibold" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                {{ transaction.type === 'income' ? '+' : '-' }}₱{{ transaction.amount.toLocaleString() }}
              </div>
              <button @click="deleteTransaction(transaction.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-md transition-colors">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Transaction Modal -->
    <AddTransactionModal
      :show="showTransactionModal"
      :transaction-form="transactionForm"
      :category-form="categoryForm"
      :categories="categories"
      :is-loading="isLoading"
      :error="error"
      :show-category-modal="showCategoryModal"
      @close="closeTransactionModal"
      @submit="handleAddTransaction"
      @open-category-modal="openCategoryModal"
      @close-category-modal="closeCategoryModal"
      @create-category="handleCreateCategory"
      @load-categories="loadCategories"
    />
  </MainLayout>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import MainLayout from './MainLayout.vue';
import AddTransactionModal from './AddTransactionModal.vue';
import { useTransactions } from '@/composables/useTransactions';

const {
  transactions,
  categories,
  isLoading,
  error,
  transactionForm,
  categoryForm,
  showTransactionModal,
  showCategoryModal,
  loadTransactions,
  loadCategories,
  addTransaction,
  deleteTransaction: deleteTransactionApi,
  addCategory,
  openTransactionModal: openModal,
  openCategoryModal: openCategory,
  getTransactionIcon,
  formatDate
} = useTransactions();

// Local state for filters
const searchQuery = ref('');
const selectedCategory = ref('');
const selectedType = ref('');

// Computed property for filtered transactions
const filteredTransactions = computed(() => {
  let filtered = transactions.value;

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(transaction => 
      transaction.description.toLowerCase().includes(query) ||
      (transaction.category_name && transaction.category_name.toLowerCase().includes(query)) ||
      (transaction.notes && transaction.notes.toLowerCase().includes(query))
    );
  }

  // Filter by category
  if (selectedCategory.value) {
    filtered = filtered.filter(transaction => 
      transaction.category_id == selectedCategory.value
    );
  }

  // Filter by type
  if (selectedType.value) {
    filtered = filtered.filter(transaction => 
      transaction.type === selectedType.value
    );
  }

  return filtered;
});

// Methods
const openTransactionModal = () => {
  openModal();
};

const closeTransactionModal = () => {
  showTransactionModal.value = false;
  error.value = '';
};

const openCategoryModal = () => {
  openCategory();
};

const closeCategoryModal = () => {
  showCategoryModal.value = false;
  error.value = '';
};

const handleAddTransaction = async () => {
  const success = await addTransaction();
  if (success) {
    closeTransactionModal();
  }
};

const handleCreateCategory = async () => {
  const success = await addCategory();
  if (success) {
    closeCategoryModal();
  }
};

const deleteTransaction = async (transactionId) => {
  const success = await deleteTransactionApi(transactionId);
  if (!success) {
    // Error handling is done in the composable
  }
};

// Load data on mount
onMounted(() => {
  loadTransactions();
  loadCategories();
});
</script>

<style scoped>
.page-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.primary-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s;
}

.primary-btn:hover {
  background: #2563eb;
}

.transactions-content {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.filters-section {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.filter-group {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.search-input,
.filter-select {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.search-input:focus,
.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-input {
  flex: 1;
  min-width: 200px;
}

.filter-select {
  min-width: 150px;
}

.transactions-table {
  min-height: 400px;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6b7280;
}

.loading-state i {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6b7280;
  text-align: center;
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-state h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
}

.empty-state p {
  margin: 0;
  font-size: 0.875rem;
}

.transactions-list {
  padding: 1rem;
}

.transaction-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
  transition: background-color 0.2s;
  position: relative;
}

.transaction-item:hover {
  background: #f9fafb;
}

.transaction-item:last-child {
  border-bottom: none;
}

.transaction-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  font-size: 1.25rem;
}

.transaction-icon.income {
  background: #ecfdf5;
  color: #10b981;
}

.transaction-icon.expense {
  background: #fef2f2;
  color: #ef4444;
}

.transaction-details {
  flex: 1;
}

.transaction-details h4 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
}

.transaction-details p {
  margin: 0 0 0.25rem 0;
  font-size: 0.875rem;
  color: #6b7280;
}

.transaction-notes {
  font-style: italic;
  font-size: 0.75rem !important;
  color: #9ca3af !important;
}

.transaction-amount {
  font-weight: 600;
  font-size: 1.125rem;
  margin-right: 1rem;
}

.transaction-amount.income {
  color: #10b981;
}

.transaction-amount.expense {
  color: #ef4444;
}

.delete-btn {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 0.375rem;
  transition: all 0.2s;
  opacity: 0;
}

.transaction-item:hover .delete-btn {
  opacity: 1;
}

.delete-btn:hover {
  background: #fef2f2;
  color: #ef4444;
}

@media (max-width: 768px) {
  .page-container {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .filter-group {
    flex-direction: column;
  }
  
  .search-input,
  .filter-select {
    width: 100%;
  }
  
  .transaction-item {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  
  .transaction-icon {
    margin-right: 0;
  }
  
  .transaction-details {
    order: 2;
    flex-basis: 100%;
  }
  
  .transaction-amount {
    order: 1;
    margin-right: auto;
  }
  
  .delete-btn {
    order: 3;
    opacity: 1;
  }
}
</style>
