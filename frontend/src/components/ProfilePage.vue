<template>
  <MainLayout>
    <div class="page-container">
      <div class="page-header">
        <h1 class="page-title">Profile</h1>
        <button class="primary-btn">
          <i class="bi bi-check-circle"></i>
          Save Changes
        </button>
      </div>
      
      <div class="profile-content">
        <div class="profile-section">
          <div class="profile-avatar">
            <div class="avatar-large">
              <i class="bi bi-person-circle"></i>
            </div>
            <button class="change-avatar-btn">
              <i class="bi bi-camera"></i>
              Change Photo
            </button>
          </div>
          
          <div class="profile-form">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input type="text" id="name" v-model="user.name" class="form-input" />
            </div>
            
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" v-model="user.email" class="form-input" />
            </div>
            
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" v-model="user.phone" class="form-input" />
            </div>
            
            <div class="form-group">
              <label for="currency">Default Currency</label>
              <select id="currency" v-model="user.currency" class="form-select">
                <option value="PHP">PHP - Philippine Peso</option>
                <option value="USD">USD - US Dollar</option>
                <option value="EUR">EUR - Euro</option>
                <option value="JPY">JPY - Japanese Yen</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import MainLayout from './MainLayout.vue';

const user = ref({ //just sample data
  name: 'Demo User',
  email: 'demo@budgetbuddy.com',
  phone: '+1 (555) 123-4567',
  currency: 'PHP'
});

</script>
