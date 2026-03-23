<template>
  <MainLayout>
    <div class="p-8 max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Transactions</h1>
        <button class="flex items-center gap-2 px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
          <i class="bi bi-plus-circle"></i>
          Add Transaction
        </button>
      </div>
      
      <div class="flex flex-col gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm mb-8">
          <div class="flex gap-4 items-center">
            <input type="text" placeholder="Search transactions..." class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm" />
            <select class="px-4 py-2 border border-gray-300 rounded-md text-sm min-w-40">
              <option>All Categories</option>
              <option>Food</option>
              <option>Transport</option>
              <option>Shopping</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-md text-sm min-w-40">
              <option>All Time</option>
              <option>This Month</option>
              <option>Last Month</option>
            </select>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm min-h-96">
          <div class="flex flex-col items-center justify-center h-full text-gray-400 text-center py-16">
            <i class="bi bi-arrow-left-right text-5xl mb-4 opacity-50"></i>
            <h3 class="font-semibold mb-2">No transactions yet</h3>
            <p class="text-sm opacity-80">Start adding transactions to see them here</p>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from './MainLayout.vue';
</script>
