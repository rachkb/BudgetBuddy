import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Chart from 'chart.js/auto';

export function useDashboard() {
  const router = useRouter();
  const spendingChart = ref(null);
  const budgetChart = ref(null);

  // Get user from localStorage
  const user = ref(JSON.parse(localStorage.getItem('user')) || { name: 'Demo User' });

  // Sample budgets for now (can be enhanced later)
  const budgets = ref([
    { category: 'Food', amount: 500, spent: 350 },
    { category: 'Transport', amount: 300, spent: 200 },
    { category: 'Shopping', amount: 400, spent: 150 },
    { category: 'Entertainment', amount: 200, spent: 50 },
    { category: 'Bills', amount: 800, spent: 650 }
  ]);

  const hasBudgetData = computed(() => {
    const hasData = budgets.value.length > 0;
    console.log('hasBudgetData:', hasData, 'budgets:', budgets.value.length);
    return hasData;
  });

  // methods
  const logout = () => {
    localStorage.removeItem('user');
    router.push('/login');
  };

  const getTransactionIcon = (category) => {
    const icons = {
      'Food': 'bi bi-cup-hot',
      'Transport': 'bi bi-car-front',
      'Shopping': 'bi bi-bag',
      'Entertainment': 'bi bi-controller',
      'Bills': 'bi bi-file-text',
      'Salary': 'bi bi-briefcase',
      'Freelance': 'bi bi-laptop',
      'Investment': 'bi bi-graph-up-arrow'
    };
    return icons[category] || 'bi bi-circle';
  };

  const initCharts = () => {
    // spending overview chart - simplified since we don't have real-time chart data
    if (spendingChart.value) {
      const ctx = spendingChart.value.getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Income',
            data: [2000, 2500, 2200, 3000, 2800, 3200],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4
          }, {
            label: 'Expenses',
            data: [1500, 1800, 1600, 2000, 1900, 2100],
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    }

    // budget progress chart
    if (budgetChart.value) {
      const ctx = budgetChart.value.getContext('2d');
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Food', 'Transport', 'Shopping', 'Entertainment', 'Bills'],
          datasets: [{
            data: [500, 300, 400, 200, 800],
            backgroundColor: [
              '#f59e0b',
              '#3b82f6',
              '#8b5cf6',
              '#ec4899',
              '#06b6d4'
            ]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    }
  };

  return {
    // refs
    spendingChart,
    budgetChart,
    user,
    budgets,
    
    // computed
    hasBudgetData,
    
    // methods
    logout,
    getTransactionIcon,
    initCharts
  };
}
