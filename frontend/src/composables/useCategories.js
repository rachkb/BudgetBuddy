import { ref } from 'vue';

const categories = ref([]);
const isLoading = ref(false);
const error = ref('');

export function useCategories() {
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

    try {
      const user = getUser();
      if (!user) {
        error.value = 'Please log in first';
        return;
      }

      const response = await fetch('/backend/categories/list.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: user.id })
      });

      const data = await response.json();

      if (data.success) {
        categories.value = data.data;
      } else {
        error.value = data.error || 'Failed to load categories';
      }
    } catch (err) {
      error.value = 'Network error. Please try again.';
      console.error('Error loading categories:', err);
    } finally {
      isLoading.value = false;
    }
  };

  const createCategory = async (categoryData) => {
    try {
      const user = getUser();
      if (!user) return { success: false, error: 'Please log in first' };

      const response = await fetch('/backend/categories/create.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: user.id, ...categoryData })
      });

      const data = await response.json();

      if (data.success) {
        await loadCategories();
        return { success: true };
      } else {
        return { success: false, error: data.error || 'Failed to create category' };
      }
    } catch (err) {
      console.error('Error creating category:', err);
      return { success: false, error: 'Network error. Please try again.' };
    }
  };

  const updateCategory = async (categoryData) => {
    try {
      const user = getUser();
      if (!user) return { success: false, error: 'Please log in first' };

      const response = await fetch('/backend/categories/update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: user.id, ...categoryData })
      });

      const data = await response.json();

      if (data.success) {
        await loadCategories();
        return { success: true };
      } else {
        return { success: false, error: data.error || 'Failed to update category' };
      }
    } catch (err) {
      console.error('Error updating category:', err);
      return { success: false, error: 'Network error. Please try again.' };
    }
  };

  const deleteCategory = async (categoryId) => {
    try {
      const user = getUser();
      if (!user) return { success: false, error: 'Please log in first' };

      const response = await fetch('/backend/categories/delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: user.id, id: categoryId })
      });

      const data = await response.json();

      if (data.success) {
        await loadCategories();
        return { success: true };
      } else {
        return { success: false, error: data.error || 'Failed to delete category' };
      }
    } catch (err) {
      console.error('Error deleting category:', err);
      return { success: false, error: 'Network error. Please try again.' };
    }
  };

  return {
    categories,
    isLoading,
    error,
    loadCategories,
    createCategory,
    updateCategory,
    deleteCategory
  };
}
