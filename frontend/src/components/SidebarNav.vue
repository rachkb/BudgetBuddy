<template>
  <div class="h-screen bg-gradient-to-b from-purple-500 to-purple-600 text-white fixed left-0 top-0 flex flex-col transition-all duration-300 shadow-lg z-50" :style="{ width: isCollapsed ? '5rem' : '18rem' }">
  
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
      <div class="flex items-center gap-3" v-if="!isCollapsed">
        <img src="../assets/favicon.png" alt="BudgetBuddy" class="w-10 h-10 object-contain" />
        <h1 class="text-xl font-bold m-0">Budget Buddy</h1>
      </div>
      <div class="flex justify-center w-full" v-else>
        <img src="../assets/favicon.png" alt="BudgetBuddy" class="w-8 h-8 object-contain" />
      </div>
      <button @click="toggleSidebar" class="bg-white/10 border-none text-white w-8 h-8 rounded-full flex items-center justify-center cursor-pointer transition-colors hover:bg-white/20">
        <i :class="isCollapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left'"></i>
      </button>
    </div>

    <nav class="flex-1 py-4 overflow-y-auto">
      <div class="mb-8">
        <h3 class="text-xs font-semibold uppercase text-white/60 px-6 mb-2 tracking-wider" v-if="!isCollapsed">Main</h3>
        <ul class="list-none m-0 p-0">
          <li class="mb-1">
            <router-link to="/dashboard" class="flex items-center gap-3 px-6 py-3 text-white/80 no-underline transition-all border-none bg-none w-full text-left cursor-pointer text-base" :class="{ 'bg-white/15 text-white border-l-3 border-white': isActive('/dashboard') }">
              <i class="bi bi-speedometer2 text-xl flex-shrink-0 w-5 text-center"></i>
              <span class="whitespace-nowrap overflow-hidden text-ellipsis" v-if="!isCollapsed">Dashboard</span>
            </router-link>
          </li>
          <li class="mb-1">
            <router-link to="/transactions" class="flex items-center gap-3 px-6 py-3 text-white/80 no-underline transition-all border-none bg-none w-full text-left cursor-pointer text-base" :class="{ 'bg-white/15 text-white border-l-3 border-white': isActive('/transactions') }">
              <i class="bi bi-arrow-left-right text-xl flex-shrink-0 w-5 text-center"></i>
              <span class="whitespace-nowrap overflow-hidden text-ellipsis" v-if="!isCollapsed">Transactions</span>
            </router-link>
          </li>
          <li class="mb-1">
            <router-link to="/categories" class="flex items-center gap-3 px-6 py-3 text-white/80 no-underline transition-all border-none bg-none w-full text-left cursor-pointer text-base" :class="{ 'bg-white/15 text-white border-l-3 border-white': isActive('/categories') }">
              <i class="bi bi-tags text-xl flex-shrink-0 w-5 text-center"></i>
              <span class="whitespace-nowrap overflow-hidden text-ellipsis" v-if="!isCollapsed">Categories</span>
            </router-link>
          </li>
          <li class="mb-1">
            <router-link to="/reports" class="flex items-center gap-3 px-6 py-3 text-white/80 no-underline transition-all border-none bg-none w-full text-left cursor-pointer text-base" :class="{ 'bg-white/15 text-white border-l-3 border-white': isActive('/reports') }">
              <i class="bi bi-graph-up text-xl flex-shrink-0 w-5 text-center"></i>
              <span class="whitespace-nowrap overflow-hidden text-ellipsis" v-if="!isCollapsed">Reports</span>
            </router-link>
          </li>
        </ul>
      </div>

      <div class="mb-0">
        <h3 class="text-xs font-semibold uppercase text-white/60 px-6 mb-2 tracking-wider" v-if="!isCollapsed">Account</h3>
        <ul class="list-none m-0 p-0">
          <li class="mb-1">
            <router-link to="/profile" class="flex items-center gap-3 px-6 py-3 text-white/80 no-underline transition-all border-none bg-none w-full text-left cursor-pointer text-base" :class="{ 'bg-white/15 text-white border-l-3 border-white': isActive('/profile') }">
              <i class="bi bi-person text-xl flex-shrink-0 w-5 text-center"></i>
              <span class="whitespace-nowrap overflow-hidden text-ellipsis" v-if="!isCollapsed">Profile</span>
            </router-link>
          </li>
          <li class="mb-1">
            <button @click="handleLogout" class="flex items-center gap-3 px-6 py-3 text-white/80 no-underline transition-all border-none bg-none w-full text-left cursor-pointer text-base hover:bg-red-500/20 hover:text-red-200">
              <i class="bi bi-box-arrow-right text-xl flex-shrink-0 w-5 text-center"></i>
              <span class="whitespace-nowrap overflow-hidden text-ellipsis" v-if="!isCollapsed">Logout</span> <!--put confirmation msg before logout-->
            </button>
          </li>
        </ul>
      </div>
    </nav>

    <div class="p-6 border-t border-white/10" v-if="!isCollapsed">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-xl">
          <i class="bi bi-person-circle"></i>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-sm m-0 whitespace-nowrap overflow-hidden text-ellipsis">{{ user?.name || 'Demo User' }}</p>
          <p class="text-xs text-white/70 m-0 whitespace-nowrap overflow-hidden text-ellipsis">{{ user?.email || 'demo@budgetbuddy.com' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

// load sidebar state from localStorage on mount
const savedState = localStorage.getItem('sidebar-collapsed');
const isCollapsed = ref(savedState === 'true');

// sample data
const user = ref({ name: 'Demo User', email: 'demo@budgetbuddy.com' });

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
  // save preference to localStorage
  localStorage.setItem('sidebar-collapsed', isCollapsed.value);
  // emit event for parent components
  window.dispatchEvent(new CustomEvent('sidebar-toggle', {
    detail: { isCollapsed: isCollapsed.value }
  }));
};

const isActive = (path) => {
  return route.path === path || route.path.startsWith(path + '/');
};

const handleLogout = () => {
  localStorage.removeItem('user');
  router.push('/login');
};
</script>
