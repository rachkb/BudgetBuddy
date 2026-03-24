import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import Chart from 'chart.js/auto';

export function useDashboard() {
  const router = useRouter();
  const spendingChart = ref(null);
  const budgetChart = ref(null);
  let spendingChartInstance = null;
  let budgetChartInstance = null;

  const storedUser = JSON.parse(localStorage.getItem('user') || '{}');
  const user = ref(storedUser);
  const transactions = ref([]);
  const categories = ref([]);

  const getUser = () => {
    try { return JSON.parse(localStorage.getItem('user')); }
    catch { return null; }
  };

  // computed properties
  const incomeTransactions = computed(() =>
    transactions.value.filter(t => t.type === 'income')
  );

  const expenseTransactions = computed(() =>
    transactions.value.filter(t => t.type === 'expense')
  );

  const totalIncome = computed(() =>
    incomeTransactions.value.reduce((sum, t) => sum + Number(t.amount), 0)
  );

  const totalExpenses = computed(() =>
    expenseTransactions.value.reduce((sum, t) => sum + Number(t.amount), 0)
  );

  const totalBalance = computed(() => totalIncome.value - totalExpenses.value);

  const savings = computed(() => totalBalance.value > 0 ? totalBalance.value : 0);

  const hasTransactionData = computed(() => transactions.value.length > 0);

  const hasBudgetData = computed(() =>
    categories.value.some(c => c.type === 'expense' && c.budget_limit && Number(c.budget_limit) > 0)
  );

  const selectedBudgetCategory = ref(null);

  const budgetItems = computed(() => {
    const colorMap = {
      primary: '#8169f1', green: '#10b981', red: '#ef4444',
      yellow: '#f59e0b', blue: '#3b82f6', indigo: '#6366f1',
      pink: '#ec4899', orange: '#f97316', teal: '#14b8a6', dark: '#1f2937'
    };
    const now = new Date();
    const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

    return categories.value
      .filter(c => c.type === 'expense' && c.budget_limit && Number(c.budget_limit) > 0)
      .map(cat => {
        const spent = transactions.value
          .filter(t => t.type === 'expense' && t.category_id == cat.id && t.transaction_date && t.transaction_date.startsWith(currentMonth))
          .reduce((sum, t) => sum + Number(t.amount), 0);
        const budget = Number(cat.budget_limit);
        const pct = budget > 0 ? Math.min((spent / budget) * 100, 100) : 0;
        return {
          id: cat.id,
          name: cat.name,
          icon: cat.icon || 'bi-tag',
          budget,
          spent,
          remaining: Math.max(budget - spent, 0),
          pct: Math.round(pct),
          over: spent > budget,
          color: colorMap[cat.color] || cat.color || '#8169f1'
        };
      });
  });

  const selectBudgetCategory = (catId) => {
    if (selectedBudgetCategory.value && selectedBudgetCategory.value.id === catId) {
      selectedBudgetCategory.value = null;
      return;
    }
    const item = budgetItems.value.find(b => b.id === catId);
    if (!item) return;
    const now = new Date();
    const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
    const txns = transactions.value
      .filter(t => t.type === 'expense' && t.category_id == catId && t.transaction_date && t.transaction_date.startsWith(currentMonth))
      .map(t => ({ id: t.id, description: t.description, amount: Number(t.amount), date: t.transaction_date }))
      .sort((a, b) => new Date(b.date) - new Date(a.date));
    selectedBudgetCategory.value = { ...item, transactions: txns };
  };

  const closeBudgetDetail = () => {
    selectedBudgetCategory.value = null;
  };

  const recentTransactions = computed(() =>
    [...transactions.value]
      .sort((a, b) => new Date(b.transaction_date) - new Date(a.transaction_date))
      .slice(0, 5)
      .map(t => ({
        id: t.id,
        type: t.type,
        description: t.description,
        category: t.category_name || 'Uncategorized',
        category_icon: t.category_icon,
        amount: Number(t.amount),
        date: t.transaction_date
      }))
  );

  // methods
  const logout = () => {
    localStorage.removeItem('user');
    router.push('/login');
  };

  const getTransactionIcon = (category, iconClass) => {
    if (iconClass) return iconClass;
    return 'bi bi-circle';
  };

  const getMonthlyData = () => {
    const now = new Date();
    const months = [];
    const incomeByMonth = {};
    const expenseByMonth = {};

    for (let i = 5; i >= 0; i--) {
      const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
      const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
      const label = d.toLocaleString('default', { month: 'short' });
      months.push({ key, label });
      incomeByMonth[key] = 0;
      expenseByMonth[key] = 0;
    }

    transactions.value.forEach(t => {
      const date = new Date(t.transaction_date);
      const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
      if (t.type === 'income' && incomeByMonth[key] !== undefined) {
        incomeByMonth[key] += Number(t.amount);
      } else if (t.type === 'expense' && expenseByMonth[key] !== undefined) {
        expenseByMonth[key] += Number(t.amount);
      }
    });

    return {
      labels: months.map(m => m.label),
      incomeData: months.map(m => incomeByMonth[m.key]),
      expenseData: months.map(m => expenseByMonth[m.key])
    };
  };

  const initCharts = () => {
    if (spendingChartInstance) { spendingChartInstance.destroy(); spendingChartInstance = null; }

    if (hasTransactionData.value && spendingChart.value) {
      const { labels, incomeData, expenseData } = getMonthlyData();
      const ctx = spendingChart.value.getContext('2d');
      spendingChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Income',
            data: incomeData,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4
          }, {
            label: 'Expenses',
            data: expenseData,
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            fill: true,
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom' } },
          scales: {
            y: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() } }
          }
        }
      });
    }
  };

  const loadDashboardData = async () => {
    const u = getUser();
    if (!u) return;

    try {
      const [txRes, catRes] = await Promise.all([
        fetch('/backend/transactions/list.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: u.id })
        }),
        fetch('/backend/categories/list.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: u.id })
        })
      ]);

      const txText = await txRes.text();
      const catText = await catRes.text();

      if (txText) {
        try {
          const txData = JSON.parse(txText);
          if (txData.success) transactions.value = txData.data || [];
        } catch (e) { console.error('Invalid JSON from transactions:', e); }
      }

      if (catText) {
        try {
          const catData = JSON.parse(catText);
          if (catData.success) categories.value = catData.data || [];
        } catch (e) { console.error('Invalid JSON from categories:', e); }
      }
    } catch (err) {
      console.error('Error loading dashboard data:', err);
    }

    setTimeout(() => { initCharts(); }, 100);
  };

  return {
    spendingChart,
    user,
    totalBalance,
    totalIncome,
    totalExpenses,
    savings,
    hasTransactionData,
    hasBudgetData,
    budgetItems,
    selectedBudgetCategory,
    selectBudgetCategory,
    closeBudgetDetail,
    recentTransactions,
    logout,
    getTransactionIcon,
    initCharts,
    loadDashboardData
  };
}
