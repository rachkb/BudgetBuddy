<template>
  <div class="sidebar" :class="{ 'sidebar-collapsed': isCollapsed }">
  
    <div class="sidebar-header">
      <div class="logo-section" v-if="!isCollapsed">
        <img src="../assets/favicon.png" alt="BudgetBuddy" class="logo" />
        <h1 class="logo-text">Budget Buddy</h1>
      </div>
      <div class="logo-section-collapsed" v-else>
        <img src="../assets/favicon.png" alt="BudgetBuddy" class="logo-collapsed" />
      </div>
      <button @click="toggleSidebar" class="toggle-btn">
        <i :class="isCollapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left'"></i>
      </button>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section">
        <h3 class="nav-section-title" v-if="!isCollapsed">Main</h3>
        <ul class="nav-list">
          <li class="nav-item">
            <router-link to="/dashboard" class="nav-link" :class="{ 'active': isActive('/dashboard') }">
              <i class="bi bi-speedometer2 nav-icon"></i>
              <span class="nav-text" v-if="!isCollapsed">Dashboard</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/transactions" class="nav-link" :class="{ 'active': isActive('/transactions') }">
              <i class="bi bi-arrow-left-right nav-icon"></i>
              <span class="nav-text" v-if="!isCollapsed">Transactions</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/categories" class="nav-link" :class="{ 'active': isActive('/categories') }">
              <i class="bi bi-tags nav-icon"></i>
              <span class="nav-text" v-if="!isCollapsed">Categories</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/reports" class="nav-link" :class="{ 'active': isActive('/reports') }">
              <i class="bi bi-graph-up nav-icon"></i>
              <span class="nav-text" v-if="!isCollapsed">Reports</span>
            </router-link>
          </li>
        </ul>
      </div>

      <div class="nav-section">
        <h3 class="nav-section-title" v-if="!isCollapsed">Account</h3>
        <ul class="nav-list">
          <li class="nav-item">
            <router-link to="/profile" class="nav-link" :class="{ 'active': isActive('/profile') }">
              <i class="bi bi-person nav-icon"></i>
              <span class="nav-text" v-if="!isCollapsed">Profile</span>
            </router-link>
          </li>
          <li class="nav-item">
            <button @click="handleLogout" class="nav-link logout-link">
              <i class="bi bi-box-arrow-right nav-icon"></i>
              <span class="nav-text" v-if="!isCollapsed">Logout</span> <!--put confirmation msg before logout-->
            </button>
          </li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer" v-if="!isCollapsed">
      <div class="user-info">
        <div class="user-avatar">
          <i class="bi bi-person-circle"></i>
        </div>
        <div class="user-details">
          <p class="user-name">{{ user?.name || 'Demo User' }}</p>
          <p class="user-email">{{ user?.email || 'demo@budgetbuddy.com' }}</p>
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
const isCollapsed = ref(false);

// sample data
const user = ref({ name: 'Demo User', email: 'demo@budgetbuddy.com' });

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
  // Save preference to localStorage
  localStorage.setItem('sidebar-collapsed', isCollapsed.value);
  // Emit event for parent components
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

// Load sidebar state from localStorage on mount
const savedState = localStorage.getItem('sidebar-collapsed');
if (savedState !== null) {
  isCollapsed.value = savedState === 'true';
}
</script>
