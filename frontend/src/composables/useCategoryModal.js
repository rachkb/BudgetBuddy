import { ref, computed, watch } from 'vue';
import { useCategories } from './useCategories';

export function useCategoryModal(props, emit) {
  const { createCategory, updateCategory, deleteCategory } = useCategories();

  const isEditing = computed(() => !!props.editCategory);

  const formData = ref({
    type: 'expense',
    name: '',
    icon: 'bi-cart',
    color: 'primary',
    budget_limit: ''
  });

  const isLoading = ref(false);
  const error = ref('');

  const expenseIcons = [
    'bi-cart', 'bi-cup-hot', 'bi-car-front', 'bi-bag', 'bi-house',
    'bi-lightning', 'bi-phone', 'bi-droplet', 'bi-tools', 'bi-heart',
    'bi-basket', 'bi-hospital', 'bi-fuel-pump', 'bi-receipt', 'bi-film', 'bi-tag'
  ];

  const incomeIcons = [
    'bi-cash-stack', 'bi-briefcase', 'bi-laptop', 'bi-bank', 'bi-graph-up-arrow',
    'bi-currency-dollar', 'bi-building', 'bi-mortarboard', 'bi-shop',
    'bi-coin', 'bi-credit-card', 'bi-piggy-bank', 'bi-people', 'bi-trophy',
    'bi-code-slash', 'bi-tag'
  ];

  const iconOptions = computed(() => {
    return formData.value.type === 'income' ? incomeIcons : expenseIcons;
  });

  const namePlaceholder = computed(() => {
    return formData.value.type === 'income'
      ? 'e.g. Salary, Freelance, Investments, Rental'
      : 'e.g. Groceries, Transport, Rent, Utilities';
  });

  const colorOptions = [
    { value: 'primary', hex: '#8169f1' },
    { value: 'green', hex: '#10b981' },
    { value: 'red', hex: '#ef4444' },
    { value: 'yellow', hex: '#f59e0b' },
    { value: 'blue', hex: '#3b82f6' },
    { value: 'indigo', hex: '#6366f1' },
    { value: 'pink', hex: '#ec4899' },
    { value: 'orange', hex: '#f97316' },
    { value: 'teal', hex: '#14b8a6' },
    { value: 'dark', hex: '#1f2937' }
  ];

  const showBudgetLimit = computed(() => formData.value.type === 'expense');

  watch(() => formData.value.type, (newType) => {
    const icons = newType === 'income' ? incomeIcons : expenseIcons;
    if (!icons.includes(formData.value.icon)) {
      formData.value.icon = icons[0];
    }
    if (newType === 'income') {
      formData.value.budget_limit = '';
    }
  });

  watch(() => props.isOpen, (newVal) => {
    if (newVal) {
      error.value = '';
      if (props.editCategory) {
        formData.value = {
          type: props.editCategory.type || 'expense',
          name: props.editCategory.name || '',
          icon: props.editCategory.icon || 'bi-tag',
          color: props.editCategory.color || 'primary',
          budget_limit: props.editCategory.budget_limit || ''
        };
      } else {
        formData.value = { type: 'expense', name: '', icon: 'bi-cart', color: 'primary', budget_limit: '' };
      }
    }
  });

  const closeModal = () => {
    emit('close');
  };

  const handleSubmit = async () => {
    isLoading.value = true;
    error.value = '';

    try {
      let result;
      if (isEditing.value) {
        result = await updateCategory({ id: props.editCategory.id, ...formData.value });
      } else {
        result = await createCategory(formData.value);
      }

      if (result.success) {
        emit('saved');
        closeModal();
      } else {
        error.value = result.error || 'Failed to save category';
      }
    } catch (err) {
      error.value = 'An error occurred. Please try again.';
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  };

  const handleDelete = async () => {
    if (!confirm('Are you sure you want to delete this category?')) return;

    isLoading.value = true;
    error.value = '';

    try {
      const result = await deleteCategory(props.editCategory.id);
      if (result.success) {
        emit('saved');
        closeModal();
      } else {
        error.value = result.error || 'Failed to delete category';
      }
    } catch (err) {
      error.value = 'An error occurred. Please try again.';
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  };

  return {
    isEditing,
    formData,
    isLoading,
    error,
    iconOptions,
    namePlaceholder,
    showBudgetLimit,
    colorOptions,
    closeModal,
    handleSubmit,
    handleDelete
  };
}
