import { ref, computed, watch, nextTick } from 'vue';
import Chart from 'chart.js/auto';

export function useReports() {
  const transactions = ref([]);
  const categories = ref([]);
  const isLoading = ref(false);
  const period = ref('this_month');
  const chartRef = ref(null);
  const spendingChartRef = ref(null);
  let chartInstance = null;
  let spendingChartInstance = null;

  // drill-down state: 'overview' or 'expense_breakdown'
  const view = ref('overview');

  const getUser = () => {
    try { return JSON.parse(localStorage.getItem('user')); }
    catch { return null; }
  };

  const getDateRange = () => {
    const now = new Date();
    let start, end;
    switch (period.value) {
      case 'this_month':
        start = new Date(now.getFullYear(), now.getMonth(), 1);
        end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        break;
      case 'last_month':
        start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        end = new Date(now.getFullYear(), now.getMonth(), 0);
        break;
      case 'last_3_months':
        start = new Date(now.getFullYear(), now.getMonth() - 2, 1);
        end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        break;
      case 'this_year':
        start = new Date(now.getFullYear(), 0, 1);
        end = new Date(now.getFullYear(), 11, 31);
        break;
      default:
        start = new Date(now.getFullYear(), now.getMonth(), 1);
        end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    }
    return {
      start: start.toISOString().split('T')[0],
      end: end.toISOString().split('T')[0]
    };
  };

  const filteredTransactions = computed(() => {
    const { start, end } = getDateRange();
    return transactions.value.filter(t => t.transaction_date >= start && t.transaction_date <= end);
  });

  const totalIncome = computed(() =>
    filteredTransactions.value.filter(t => t.type === 'income').reduce((s, t) => s + Number(t.amount), 0)
  );

  const totalExpenses = computed(() =>
    filteredTransactions.value.filter(t => t.type === 'expense').reduce((s, t) => s + Number(t.amount), 0)
  );

  const netSavings = computed(() => totalIncome.value - totalExpenses.value);

  const colorMap = {
    primary: '#8169f1', green: '#10b981', red: '#ef4444',
    yellow: '#f59e0b', blue: '#3b82f6', indigo: '#6366f1',
    pink: '#ec4899', orange: '#f97316', teal: '#14b8a6', dark: '#1f2937'
  };

  const expenseCategoryBreakdown = computed(() => {
    const expenseTxns = filteredTransactions.value.filter(t => t.type === 'expense');
    const map = {};
    expenseTxns.forEach(t => {
      const catId = t.category_id || 'uncategorized';
      if (!map[catId]) {
        const cat = categories.value.find(c => c.id == catId);
        map[catId] = {
          id: catId,
          name: cat ? cat.name : 'Uncategorized',
          icon: cat ? cat.icon : 'bi-tag',
          color: cat ? (colorMap[cat.color] || cat.color || '#8169f1') : '#9ca3af',
          total: 0
        };
      }
      map[catId].total += Number(t.amount);
    });
    return Object.values(map).sort((a, b) => b.total - a.total);
  });

  const destroyChart = () => {
    if (chartInstance) { chartInstance.destroy(); chartInstance = null; }
  };

  const destroySpendingChart = () => {
    if (spendingChartInstance) { spendingChartInstance.destroy(); spendingChartInstance = null; }
  };

  const getMonthlyData = () => {
    const { start, end } = getDateRange();
    const startDate = new Date(start);
    const endDate = new Date(end);
    const months = [];
    const incomeByMonth = {};
    const expenseByMonth = {};

    const d = new Date(startDate.getFullYear(), startDate.getMonth(), 1);
    while (d <= endDate) {
      const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
      const label = d.toLocaleString('default', { month: 'short', year: '2-digit' });
      months.push({ key, label });
      incomeByMonth[key] = 0;
      expenseByMonth[key] = 0;
      d.setMonth(d.getMonth() + 1);
    }

    filteredTransactions.value.forEach(t => {
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

  const buildSpendingChart = () => {
    destroySpendingChart();
    if (!spendingChartRef.value) return;
    if (filteredTransactions.value.length === 0) return;

    const { labels, incomeData, expenseData } = getMonthlyData();
    const ctx = spendingChartRef.value.getContext('2d');
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
          tension: 0.4,
          pointBackgroundColor: '#10b981',
          pointRadius: 4,
          pointHoverRadius: 6
        }, {
          label: 'Expenses',
          data: expenseData,
          borderColor: '#ef4444',
          backgroundColor: 'rgba(239, 68, 68, 0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#ef4444',
          pointRadius: 4,
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyleWidth: 12 } } },
        scales: {
          y: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() } },
          x: { grid: { display: false } }
        }
      }
    });
  };

  const buildOverviewChart = () => {
    destroyChart();
    if (!chartRef.value) return;
    const inc = totalIncome.value;
    const exp = totalExpenses.value;
    if (inc === 0 && exp === 0) return;

    const ctx = chartRef.value.getContext('2d');
    chartInstance = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Income', 'Expenses'],
        datasets: [{
          data: [inc, exp],
          backgroundColor: ['#10b981', '#ef4444'],
          borderWidth: 3,
          borderColor: '#ffffff',
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyleWidth: 12 } },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const val = ctx.raw;
                const total = inc + exp;
                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                return `${ctx.label}: ₱${val.toLocaleString()} (${pct}%)`;
              }
            }
          }
        },
        onClick: (_event, elements) => {
          if (elements.length > 0) {
            const idx = elements[0].index;
            if (idx === 1) { // clicked Expenses
              view.value = 'expense_breakdown';
              nextTick(() => buildBreakdownChart());
            }
          }
        }
      }
    });
  };

  const buildBreakdownChart = () => {
    destroyChart();
    if (!chartRef.value) return;
    const data = expenseCategoryBreakdown.value;
    if (data.length === 0) return;

    const ctx = chartRef.value.getContext('2d');
    chartInstance = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: data.map(d => d.name),
        datasets: [{
          data: data.map(d => d.total),
          backgroundColor: data.map(d => d.color),
          borderWidth: 3,
          borderColor: '#ffffff',
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyleWidth: 12 } },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const val = ctx.raw;
                const total = data.reduce((s, d) => s + d.total, 0);
                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                return `${ctx.label}: ₱${val.toLocaleString()} (${pct}%)`;
              }
            }
          }
        }
      }
    });
  };

  const goBackToOverview = () => {
    view.value = 'overview';
    nextTick(() => buildOverviewChart());
  };

  const buildChart = () => {
    if (view.value === 'expense_breakdown') {
      buildBreakdownChart();
    } else {
      buildOverviewChart();
    }
    buildSpendingChart();
  };

  const loadReportData = async () => {
    const u = getUser();
    if (!u) return;
    isLoading.value = true;

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
      console.error('Error loading report data:', err);
    } finally {
      isLoading.value = false;
    }

    view.value = 'overview';
    nextTick(() => buildChart());
  };

  watch(period, () => {
    view.value = 'overview';
    nextTick(() => {
      buildChart();
    });
  });

  return {
    isLoading,
    period,
    chartRef,
    spendingChartRef,
    view,
    totalIncome,
    totalExpenses,
    netSavings,
    expenseCategoryBreakdown,
    goBackToOverview,
    loadReportData
  };
}
