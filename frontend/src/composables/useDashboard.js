import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Chart from 'chart.js/auto';

export function useDashboard() {
  const router = useRouter();
  const spendingChart = ref(null);
  const budgetChart = ref(null);

  // sample
  const user = ref({ name: 'Demo User' }); 
  const expenses = ref([]);
  const income = ref([]);
  const budgets = ref([]);

  // sample data only
  const sampleExpenses = [
    { id: 1, description: 'Grocery Shopping', category: 'Food', amount: 150, date: '2024-01-15', type: 'expense' },
    { id: 2, description: 'Gas Station', category: 'Transport', amount: 60, date: '2024-01-14', type: 'expense' },
    { id: 3, description: 'Netflix Subscription', category: 'Entertainment', amount: 15, date: '2024-01-13', type: 'expense' }
  ];

  const sampleIncome = [
    { id: 1, description: 'Monthly Salary', category: 'Salary', amount: 3000, date: '2024-01-01', type: 'income' },
    { id: 2, description: 'Freelance Project', category: 'Freelance', amount: 500, date: '2024-01-10', type: 'income' }
  ];

  const sampleBudgets = [
    { category: 'Food', amount: 500, spent: 350 },
    { category: 'Transport', amount: 300, spent: 200 },
    { category: 'Shopping', amount: 400, spent: 150 },
    { category: 'Entertainment', amount: 200, spent: 50 },
    { category: 'Bills', amount: 800, spent: 650 }
  ];

  // computed properties
  const totalBalance = computed(() => {
    const totalIncome = income.value.reduce((sum, item) => sum + item.amount, 0);
    const totalExpenses = expenses.value.reduce((sum, item) => sum + item.amount, 0);
    const balance = totalIncome - totalExpenses;
    console.log('totalBalance:', balance, 'income:', totalIncome, 'expenses:', totalExpenses);
    return balance;
  });

  const totalIncome = computed(() => {
    const incomeTotal = income.value.reduce((sum, item) => sum + item.amount, 0);
    console.log('totalIncome:', incomeTotal);
    return incomeTotal;
  });

  const totalExpenses = computed(() => {
    const expenseTotal = expenses.value.reduce((sum, item) => sum + item.amount, 0);
    console.log('totalExpenses:', expenseTotal);
    return expenseTotal;
  });

  const savings = computed(() => {
    const savingsAmount = totalIncome.value * 0.2; // 20% savings rate
    console.log('savings:', savingsAmount);
    return savingsAmount;
  });

  const hasTransactionData = computed(() => {
    const hasData = expenses.value.length > 0 || income.value.length > 0;
    console.log('hasTransactionData:', hasData, 'expenses:', expenses.value.length, 'income:', income.value.length);
    return hasData;
  });

  const hasBudgetData = computed(() => {
    const hasData = budgets.value.length > 0;
    console.log('hasBudgetData:', hasData, 'budgets:', budgets.value.length);
    return hasData;
  });

  const recentTransactions = computed(() => {
    const allTransactions = [
      ...income.value.map(item => ({ 
        ...item, 
        type: 'income',
        description: item.description,
        category: item.category,
        amount: item.amount,
        date: item.date
      })),
      ...expenses.value.map(item => ({ 
        ...item, 
        type: 'expense',
        description: item.description,
        category: item.category,
        amount: item.amount,
        date: item.date
      }))
    ];
    return allTransactions
      .sort((a, b) => new Date(b.date) - new Date(a.date))
      .slice(0, 5);
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
    // spending overview chart
    if (hasTransactionData.value && spendingChart.value) {
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
    if (hasBudgetData.value && budgetChart.value) {
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

  const loadDashboardData = () => {
    // load sample data
    // in production, this would come from your API
    console.log('Loading dashboard data...');
    expenses.value = [...sampleExpenses];
    income.value = [...sampleIncome];
    budgets.value = [...sampleBudgets];
    
    // initialize charts after data is loaded
    setTimeout(() => {
      initCharts();
    }, 100);
  };

  return {
    // refs
    spendingChart,
    budgetChart,
    user,
    expenses,
    income,
    budgets,
    
    // computed
    totalBalance,
    totalIncome,
    totalExpenses,
    savings,
    hasTransactionData,
    hasBudgetData,
    recentTransactions,
    
    // methods
    logout,
    getTransactionIcon,
    initCharts,
    loadDashboardData
  };
}
