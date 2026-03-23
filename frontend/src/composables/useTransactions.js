import { ref, computed } from 'vue';

export function useTransactions() {
  const transactions = ref([]);
  const categories = ref([]);
  const isLoading = ref(false);
  const error = ref('');

  // Transaction form data
  const transactionForm = ref({
    type: 'expense',
    amount: '',
    description: '',
    category_id: '',
    date: new Date().toISOString().split('T')[0], // Today's date
    notes: '',
    is_recurring: false,
    recurring_frequency: 'monthly',
    recurring_interval: 1,
    recurring_end_date: ''
  });

  // Category creation form
  const categoryForm = ref({
    name: '',
    icon: 'bi-tag',
    color: 'primary',
    type: 'expense'
  });

  // Modal states
  const showTransactionModal = ref(false);
  const showCategoryModal = ref(false);

  // Computed properties
  const incomeTransactions = computed(() => 
    transactions.value.filter(t => t.type === 'income')
  );

  const expenseTransactions = computed(() => 
    transactions.value.filter(t => t.type === 'expense')
  );

  const totalIncome = computed(() => 
    incomeTransactions.value.reduce((sum, t) => sum + t.amount, 0)
  );

  const totalExpenses = computed(() => 
    expenseTransactions.value.reduce((sum, t) => sum + t.amount, 0)
  );

  const totalBalance = computed(() => totalIncome.value - totalExpenses.value);

  const recentTransactions = computed(() => 
    transactions.value
      .sort((a, b) => new Date(b.date) - new Date(a.date))
      .slice(0, 5)
  );

  // Methods
  const loadTransactions = async (filters = {}) => {
    isLoading.value = true;
    error.value = '';

    try {
      const queryParams = new URLSearchParams();
      Object.entries(filters).forEach(([key, value]) => {
        if (value) queryParams.append(key, value);
      });

      const response = await fetch(`http://localhost:8000/transactions/list?${queryParams}`);
      const data = await response.json();

      if (Array.isArray(data)) {
        transactions.value = data;
      } else {
        error.value = data.error || 'Failed to load transactions';
      }
    } catch (err) {
      error.value = 'Connection failed. Please check your server.';
      console.error('Load transactions error:', err);
    } finally {
      isLoading.value = false;
    }
  };

  const loadCategories = async (type = '') => {
    try {
      const url = type ? `http://localhost:8000/categories/list?type=${type}` : 'http://localhost:8000/categories/list';
      const response = await fetch(url);
      const data = await response.json();

      if (data.success) {
        categories.value = data.categories;
      }
    } catch (err) {
      console.error('Load categories error:', err);
    }
  };

  const addTransaction = async () => {
    // Validation
    if (!transactionForm.value.amount || parseFloat(transactionForm.value.amount) <= 0) {
      error.value = 'Amount must be greater than 0';
      return false;
    }

    if (!transactionForm.value.description.trim()) {
      error.value = 'Description is required';
      return false;
    }

    if (!transactionForm.value.date) {
      error.value = 'Date is required';
      return false;
    }

    isLoading.value = true;
    error.value = '';

    try {
      const response = await fetch('http://localhost:8000/transactions/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(transactionForm.value)
      });

      const data = await response.json();

      if (data.success) {
        // Reset form and close modal
        resetTransactionForm();
        showTransactionModal.value = false;
        
        // Reload transactions
        await loadTransactions();
        
        return true;
      } else {
        error.value = data.error || 'Failed to add transaction';
        return false;
      }
    } catch (err) {
      error.value = 'Connection failed. Please check your server.';
      console.error('Add transaction error:', err);
      return false;
    } finally {
      isLoading.value = false;
    }
  };

  const deleteTransaction = async (transactionId) => {
    if (!confirm('Are you sure you want to delete this transaction?')) {
      return false;
    }

    try {
      const response = await fetch(`http://localhost:8000/transactions/delete?id=${transactionId}`, {
        method: 'DELETE'
      });

      const data = await response.json();

      if (data.success) {
        // Remove from local state
        transactions.value = transactions.value.filter(t => t.id !== transactionId);
        return true;
      } else {
        error.value = data.error || 'Failed to delete transaction';
        return false;
      }
    } catch (err) {
      error.value = 'Connection failed. Please check your server.';
      console.error('Delete transaction error:', err);
      return false;
    }
  };

  const addCategory = async () => {
    if (!categoryForm.value.name.trim()) {
      error.value = 'Category name is required';
      return false;
    }

    try {
      const response = await fetch('http://localhost:8000/categories/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(categoryForm.value)
      });

      const data = await response.json();

      if (data.success) {
        // Add to local categories
        categories.value.push(data.category);
        
        // Set as selected category in transaction form
        transactionForm.value.category_id = data.category.id;
        
        // Reset and close category modal
        resetCategoryForm();
        showCategoryModal.value = false;
        
        return true;
      } else {
        error.value = data.error || 'Failed to add category';
        return false;
      }
    } catch (err) {
      error.value = 'Connection failed. Please check your server.';
      console.error('Add category error:', err);
      return false;
    }
  };

  const resetTransactionForm = () => {
    transactionForm.value = {
      type: 'expense',
      amount: '',
      description: '',
      category_id: '',
      date: new Date().toISOString().split('T')[0],
      notes: '',
      is_recurring: false,
      recurring_frequency: 'monthly',
      recurring_interval: 1,
      recurring_end_date: ''
    };
    error.value = '';
  };

  const resetCategoryForm = () => {
    categoryForm.value = {
      name: '',
      icon: 'bi-tag',
      color: 'primary',
      type: 'expense'
    };
  };

  const openTransactionModal = (type = 'expense') => {
    resetTransactionForm();
    transactionForm.value.type = type;
    showTransactionModal.value = true;
  };

  const openCategoryModal = () => {
    resetCategoryForm();
    // Set category type to match transaction type
    categoryForm.value.type = transactionForm.value.type;
    // Set appropriate default icon based on type
    if (transactionForm.value.type === 'income') {
      categoryForm.value.icon = 'bi-briefcase';
    } else {
      categoryForm.value.icon = 'bi-cart';
    }
    showCategoryModal.value = true;
  };

  const getTransactionIcon = (category) => {
    if (!category) return 'bi bi-circle';
    return category.icon || 'bi bi-circle';
  };

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
      style: 'currency',
      currency: 'PHP'
    }).format(amount);
  };

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  // Initialize
  const initialize = async () => {
    await Promise.all([
      loadTransactions(),
      loadCategories()
    ]);
  };

  return {
    // State
    transactions,
    categories,
    isLoading,
    error,
    transactionForm,
    categoryForm,
    showTransactionModal,
    showCategoryModal,

    // Computed
    incomeTransactions,
    expenseTransactions,
    totalIncome,
    totalExpenses,
    totalBalance,
    recentTransactions,

    // Methods
    loadTransactions,
    loadCategories,
    addTransaction,
    deleteTransaction,
    addCategory,
    resetTransactionForm,
    resetCategoryForm,
    openTransactionModal,
    openCategoryModal,
    getTransactionIcon,
    formatCurrency,
    formatDate,
    initialize
  };
}
