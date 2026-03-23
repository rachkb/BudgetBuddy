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
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Budget Progress</h2>
          <div class="h-80 relative">
            <canvas v-if="hasBudgetData" ref="budgetChart"></canvas>
            <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 text-center">
              <i class="bi bi-pie-chart text-5xl mb-4 opacity-50"></i>
              <p class="font-semibold mb-2">No budget data available</p>
              <small class="text-xs opacity-80">Set up budgets to track your progress</small>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
          <button class="flex items-center gap-2 px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
            <i class="bi bi-plus-circle"></i>
            Add Transaction
          </button>
        </div>
        <div class="flex flex-col gap-4">
          <div v-if="hasTransactionData" class="flex items-center gap-4 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors" v-for="transaction in recentTransactions" :key="transaction.id">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="transaction.type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
              <i :class="getTransactionIcon(transaction.category)"></i>
            </div>
            <div class="transaction-details flex-1">
              <h4 class="font-semibold text-gray-900 mb-1">{{ transaction.description }}</h4>
              <p class="text-sm text-gray-600">{{ transaction.category }} • {{ transaction.date }}</p>
            </div>
            <div class="font-semibold text-base" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
              {{ transaction.type === 'income' ? '+' : '-' }}₱{{ transaction.amount }}
            </div>
          </div>
          <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 text-center">
            <i class="bi bi-receipt text-5xl mb-4 opacity-50"></i>
            <p class="font-semibold mb-2">No transactions yet</p>
            <small class="text-xs opacity-80">Your recent transactions will appear here</small>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import { useDashboard } from '@/composables/useDashboard';
import MainLayout from './MainLayout.vue';

const {spendingChart, budgetChart, user, totalBalance, totalIncome, totalExpenses, savings, hasTransactionData, hasBudgetData, recentTransactions, logout, getTransactionIcon, loadDashboardData
} = useDashboard();

onMounted(() => {
  console.log('Dashboard mounted, loading data...');
  loadDashboardData();
});
</script>
