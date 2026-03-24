import { ref, computed, watch } from 'vue';
import { useCategories } from './useCategories';

const isOpen = ref(false);

export function useAddTransactionModal() {
  const { categories, loadCategories } = useCategories();

  const formData = ref({
    type: 'expense',
    amount: '',
    description: '',
    category_id: '',
    transaction_date: new Date().toISOString().split('T')[0],
    notes: ''
  });

  const isLoading = ref(false);
  const error = ref('');

  const filteredCategories = computed(() => {
    return categories.value.filter(cat => cat.type === formData.value.type);
  });

  watch(() => formData.value.type, () => {
    formData.value.category_id = '';
  });

  const showCategoryModal = ref(false);

  const openCategoryModal = () => {
    showCategoryModal.value = true;
  };

  const onCategorySaved = async () => {
    await loadCategories();
  };

  const open = () => {
    isOpen.value = true;
  };

  const close = () => {
    isOpen.value = false;
  };

  const resetForm = () => {
    formData.value = {
      type: 'expense',
      amount: '',
      description: '',
      category_id: '',
      transaction_date: new Date().toISOString().split('T')[0],
      notes: ''
    };
    error.value = '';
  };

  watch(isOpen, (newVal) => {
    if (newVal) {
      loadCategories();
      resetForm();
    }
  });

  const addTransaction = async (transactionData) => {
    try {
      const user = JSON.parse(localStorage.getItem('user'));

      if (!user) {
        return { success: false, error: 'Please log in first' };
      }

      const response = await fetch('/backend/transactions/create.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          user_id: user.id,
          ...transactionData
        })
      });

      const data = await response.json();

      if (data.success) {
        window.dispatchEvent(new CustomEvent('transaction-added'));
        return { success: true };
      } else {
        return { success: false, error: data.error || 'Failed to add transaction' };
      }
    } catch (error) {
      console.error('Error adding transaction:', error);
      return { success: false, error: 'Network error. Please try again.' };
    }
  };

  const handleSubmit = async () => {
    isLoading.value = true;
    error.value = '';

    try {
      const result = await addTransaction(formData.value);

      if (result.success) {
        close();
      } else {
        error.value = result.error || 'Failed to add transaction';
      }
    } catch (err) {
      error.value = 'An error occurred. Please try again.';
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  };

  return {
    isOpen,
    formData,
    isLoading,
    error,
    filteredCategories,
    showCategoryModal,
    openCategoryModal,
    onCategorySaved,
    open,
    close,
    handleSubmit
  };
}
