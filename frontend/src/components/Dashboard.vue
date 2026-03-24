<template>
  <MainLayout>
    <div class="w-full max-w-6xl mx-auto p-8">

      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">Welcome, {{ user?.name || 'User' }}!</span>
          <button @click="logout" class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
            <i class="bi bi-box-arrow-right"></i>
            Logout
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-gray-100 text-gray-600 text-xl">
            <i class="bi bi-wallet2"></i>
          </div>
          <div class="stat-content">
            <h3 class="text-sm text-gray-600 mb-1">Total Balance</h3>
            <p class="text-xl font-bold text-gray-900">₱{{ totalBalance.toLocaleString() }}</p>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-green-100 text-green-600 text-xl">
            <i class="bi bi-arrow-up-circle"></i>
          </div>
          <div class="stat-content">
            <h3 class="text-sm text-gray-600 mb-1">Income</h3>
            <p class="text-xl font-bold text-green-600">₱{{ totalIncome.toLocaleString() }}</p>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-red-100 text-red-600 text-xl">
            <i class="bi bi-arrow-down-circle"></i>
          </div>
          <div class="stat-content">
            <h3 class="text-sm text-gray-600 mb-1">Expenses</h3>
            <p class="text-xl font-bold text-red-600">₱{{ totalExpenses.toLocaleString() }}</p>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-blue-100 text-blue-600 text-xl">
            <i class="bi bi-piggy-bank"></i>
          </div>
          <div class="stat-content">
            <h3 class="text-sm text-gray-600 mb-1">Savings</h3>
            <p class="text-xl font-bold text-blue-600">₱{{ savings.toLocaleString() }}</p>
          </div>
        </div>
      </div>


      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <div class="bg-white p-6 rounded-xl shadow-sm">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Spending Overview</h2>
          <div class="h-80 relative">
            <canvas v-if="hasTransactionData" ref="spendingChart"></canvas>
            <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 text-center">
              <i class="bi bi-graph-up text-5xl mb-4 opacity-50"></i>
              <p class="font-semibold mb-2">No spending data available</p>
              <small class="text-xs opacity-80">Start adding transactions to see your spending overview</small>
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Budget Progress
            <span class="text-xs font-normal text-gray-400 ml-2">This month</span>
          </h2>
          <div class="h-80 overflow-y-auto relative">
            <template v-if="hasBudgetData && !selectedBudgetCategory">
              <div class="flex flex-col gap-4">
                <div
                  v-for="item in budgetItems"
                  :key="item.id"
                  @click="selectBudgetCategory(item.id)"
                  class="p-3 rounded-lg border border-gray-100 hover:border-gray-300 cursor-pointer transition-all hover:shadow-sm"
                >
                  <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                      <i :class="item.icon" class="text-base" :style="{ color: item.color }"></i>
                      <span class="text-sm font-medium text-gray-900">{{ item.name }}</span>
                    </div>
                    <span class="text-xs font-medium" :class="item.over ? 'text-red-600' : 'text-gray-500'">
                      ₱{{ item.spent.toLocaleString() }} / ₱{{ item.budget.toLocaleString() }}
                    </span>
                  </div>
                  <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div
                      class="h-full rounded-full transition-all duration-500"
                      :style="{ width: item.pct + '%', backgroundColor: item.over ? '#ef4444' : item.color }"
                    ></div>
                  </div>
                  <div class="flex justify-between mt-1">
                    <span class="text-xs" :class="item.over ? 'text-red-500 font-medium' : 'text-gray-400'">{{ item.pct }}% used</span>
                    <span class="text-xs text-gray-400">₱{{ item.remaining.toLocaleString() }} left</span>
                  </div>
                </div>
              </div>
            </template>

            <template v-else-if="selectedBudgetCategory">
              <div class="flex flex-col h-full">
                <button @click="closeBudgetDetail" class="flex items-center gap-1 text-sm text-purple-600 hover:text-purple-800 mb-3 font-medium">
                  <i class="bi bi-arrow-left"></i> Back
                </button>
                <div class="flex items-center gap-2 mb-2">
                  <i :class="selectedBudgetCategory.icon" class="text-lg" :style="{ color: selectedBudgetCategory.color }"></i>
                  <span class="font-semibold text-gray-900">{{ selectedBudgetCategory.name }}</span>
                </div>
                <div class="flex items-center gap-3 mb-3">
                  <span class="text-sm font-medium" :class="selectedBudgetCategory.over ? 'text-red-600' : 'text-gray-700'">
                    ₱{{ selectedBudgetCategory.spent.toLocaleString() }} / ₱{{ selectedBudgetCategory.budget.toLocaleString() }}
                  </span>
                  <span class="text-xs px-2 py-0.5 rounded-full" :class="selectedBudgetCategory.over ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700'">
                    {{ selectedBudgetCategory.over ? 'Over budget' : selectedBudgetCategory.pct + '% used' }}
                  </span>
                </div>
                <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden mb-4">
                  <div
                    class="h-full rounded-full transition-all duration-500"
                    :style="{ width: selectedBudgetCategory.pct + '%', backgroundColor: selectedBudgetCategory.over ? '#ef4444' : selectedBudgetCategory.color }"
                  ></div>
                </div>
                <div v-if="selectedBudgetCategory.transactions.length > 0" class="flex-1 overflow-y-auto flex flex-col gap-2">
                  <div v-for="tx in selectedBudgetCategory.transactions" :key="tx.id" class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-50">
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ tx.description }}</p>
                      <p class="text-xs text-gray-400">{{ tx.date }}</p>
                    </div>
                    <span class="text-sm font-semibold text-red-600">-₱{{ tx.amount.toLocaleString() }}</span>
                  </div>
                </div>
                <div v-else class="flex-1 flex items-center justify-center text-gray-400 text-sm">No expenses this month</div>
              </div>
            </template>

            <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 text-center">
              <i class="bi bi-pie-chart text-5xl mb-4 opacity-50"></i>
              <p class="font-semibold mb-2">No budget data available</p>
              <small class="text-xs opacity-80">Set budget limits on expense categories to track progress</small>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
          <button @click="openAddModal" class="flex items-center gap-2 px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
            <i class="bi bi-plus-circle"></i>
            Add Transaction
          </button>
        </div>
        <div class="flex flex-col gap-4">
          <template v-if="hasTransactionData">
            <div v-for="transaction in recentTransactions" :key="transaction.id" class="flex items-center gap-4 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="transaction.type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                <i :class="transaction.category_icon || (transaction.type === 'income' ? 'bi bi-arrow-down-circle' : 'bi bi-arrow-up-circle')"></i>
              </div>
              <div class="flex-1">
                <h4 class="font-semibold text-gray-900 mb-1">{{ transaction.description }}</h4>
                <p class="text-sm text-gray-600">{{ transaction.category }} • {{ transaction.date }}</p>
              </div>
              <div class="font-semibold text-base" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                {{ transaction.type === 'income' ? '+' : '-' }}₱{{ transaction.amount.toLocaleString() }}
              </div>
            </div>
          </template>
          <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 text-center">
            <i class="bi bi-receipt text-5xl mb-4 opacity-50"></i>
            <p class="font-semibold mb-2">No transactions yet</p>
            <small class="text-xs opacity-80">Your recent transactions will appear here</small>
          </div>
        </div>
      </div>
    </div>

    <AddTransactionModal />
  </MainLayout>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';
import { useDashboard } from '@/composables/useDashboard';
import MainLayout from './MainLayout.vue';
import AddTransactionModal from './AddTransactionModal.vue';
import { useAddTransactionModal } from '@/composables/useAddTransactionModal';

const { open: openAddModal } = useAddTransactionModal();

const {
  spendingChart, user, totalBalance, totalIncome, totalExpenses, savings,
  hasTransactionData, hasBudgetData, budgetItems, selectedBudgetCategory,
  selectBudgetCategory, closeBudgetDetail, recentTransactions,
  logout, getTransactionIcon, loadDashboardData
} = useDashboard();

const onTransactionAdded = () => {
  loadDashboardData();
};

onMounted(() => {
  console.log('Dashboard mounted, loading data...');
  loadDashboardData();
  window.addEventListener('transaction-added', onTransactionAdded);
});

onUnmounted(() => {
  window.removeEventListener('transaction-added', onTransactionAdded);
});
</script>
