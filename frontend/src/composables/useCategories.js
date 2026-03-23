import { ref } from 'vue';

export function useCategories() {
  const categories = ref([]);
  const isLoading = ref(false);
  const error = ref('');
  const showModal = ref(false);
  const editingCategory = ref(null);

  const categoryForm = ref({
    name: '',
    icon: 'bi-tag',
    color: 'primary',
    budget_limit: null
  });

  const iconOptions = [
    { value: 'bi-cup-hot', label: 'Food & Drinks' },
    { value: 'bi-car-front', label: 'Transport' },
    { value: 'bi-bag', label: 'Shopping' },
    { value: 'bi-house', label: 'Housing' },
    { value: 'bi-heart-pulse', label: 'Health' },
    { value: 'bi-controller', label: 'Entertainment' },
    { value: 'bi-book', label: 'Education' },
    { value: 'bi-piggy-bank', label: 'Savings' },
    { value: 'bi-gift', label: 'Gifts' },
    { value: 'bi-tag', label: 'Other' }
  ];

  const colorOptions = [
    { value: 'primary', label: 'Purple', class: 'bg-primary-500' },
    { value: 'food', label: 'Orange', class: 'bg-orange-500' },
    { value: 'transport', label: 'Blue', class: 'bg-blue-500' },
    { value: 'shopping', label: 'Pink', class: 'bg-pink-500' },
    { value: 'green', label: 'Green', class: 'bg-green-500' },
    { value: 'red', label: 'Red', class: 'bg-red-500' },
    { value: 'yellow', label: 'Yellow', class: 'bg-yellow-500' },
    { value: 'indigo', label: 'Indigo', class: 'bg-indigo-500' }
  ];

  const getUser = () => {
    try {
      return JSON.parse(localStorage.getItem('user'));
    } catch {
      return null;
    }
  };

  const loadCategories = async () => {
    isLoading.value = true;
    error.value = '';
    const user = getUser();
    if (!user) {
      error.value = 'Please log in first';
      isLoading.value = false;
      return;
    }
    
    try {
      const res = await fetch('http://localhost:8000/categories/list', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: user.id })
      });
      
      const data = await res.json();
      
      if (data.success) {
        categories.value = data.categories;
      } else {
        error.value = data.error || 'Failed to load categories';
      }
    } catch (err) {
      error.value = 'Connection failed. Check your server.';
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  };

  const addCategory = async () => {
    isLoading.value = true;
    error.value = '';
    
    try {
      const user = getUser();
      const res = await fetch('http://localhost:8000/categories/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...categoryForm.value, user_id: user.id })
      });
      
      const data = await res.json();
      
      if (data.success) {
        categories.value.unshift(data.category);
        closeModal();
      } else {
        error.value = data.error || 'Failed to add category';
      }
    } catch (err) {
      error.value = 'Connection failed. Check your server.';
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  };

  const updateCategory = async () => {
    isLoading.value = true;
    error.value = '';
    
    try {
      const user = getUser();
      const res = await fetch('http://localhost:8000/categories/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: editingCategory.value.id,
          ...categoryForm.value,
          user_id: user.id
        })
      });
      
      const data = await res.json();
      
      if (data.success) {
        const index = categories.value.findIndex(c => c.id === data.category.id);
        if (index !== -1) {
          categories.value[index] = data.category;
        }
        closeModal();
      } else {
        error.value = data.error || 'Failed to update category';
      }
    } catch (err) {
      error.value = 'Connection failed. Check your server.';
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  };

  const deleteCategory = async (categoryId) => {
    if (!confirm('Are you sure you want to delete this category?')) {
      return;
    }
    
    isLoading.value = true;
    error.value = '';
    
    try {
      const user = getUser();
      const res = await fetch('http://localhost:8000/categories/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: categoryId, user_id: user.id })
      });
      
      const data = await res.json();
      
      if (data.success) {
        categories.value = categories.value.filter(c => c.id !== categoryId);
      } else {
        error.value = data.error || 'Failed to delete category';
      }
    } catch (err) {
      error.value = 'Connection failed. Check your server.';
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  };

  const openAddModal = () => {
    editingCategory.value = null;
    categoryForm.value = {
      name: '',
      icon: 'bi-tag',
      color: 'primary',
      budget_limit: null
    };
    showModal.value = true;
  };

  const openEditModal = (category) => {
    editingCategory.value = category;
    categoryForm.value = {
      name: category.name,
      icon: category.icon,
      color: category.color,
      budget_limit: category.budget_limit
    };
    showModal.value = true;
  };

  const closeModal = () => {
    showModal.value = false;
    editingCategory.value = null;
    error.value = '';
    categoryForm.value = {
      name: '',
      icon: 'bi-tag',
      color: 'primary',
      budget_limit: null
    };
  };

  const handleSubmit = () => {
    if (editingCategory.value) {
      updateCategory();
    } else {
      addCategory();
    }
  };

  return {
    categories,
    isLoading,
    error,
    showModal,
    editingCategory,
    categoryForm,
    iconOptions,
    colorOptions,
    loadCategories,
    addCategory,
    updateCategory,
    deleteCategory,
    openAddModal,
    openEditModal,
    closeModal,
    handleSubmit
  };
}
