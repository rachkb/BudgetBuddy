<template>
  <MainLayout>
    <div class="p-8 max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <button class="flex items-center gap-2 px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
          <i class="bi bi-plus-circle"></i>
          Add Category
        </button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-lg flex items-center justify-center text-xl text-white bg-amber-500">
            <i class="bi bi-cup-hot"></i>
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Food</h3>
            <p class="text-sm text-gray-600 mb-2">₱350 / ₱500</p>
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-purple-500 transition-all duration-300" style="width: 70%"></div>
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-lg flex items-center justify-center text-xl text-white bg-blue-500">
            <i class="bi bi-car-front"></i>
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Transport</h3>
            <p class="text-sm text-gray-600 mb-2">₱200 / ₱300</p>
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-purple-500 transition-all duration-300" style="width: 66%"></div>
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-lg flex items-center justify-center text-xl text-white bg-blue-500">
            <i class="bi bi-bag"></i>
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Shopping</h3>
            <p class="text-sm text-gray-600 mb-2">₱150 / ₱400</p>
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-purple-500 transition-all duration-300" style="width: 37%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from './MainLayout.vue';
</script>
