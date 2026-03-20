<template>
  <div class="p-6">
    <h2 class="text-xl font-bold mb-4">Dashboard</h2>
    <p>Welcome, {{ user?.name }}</p>
    <button
      @click="logout"
      class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
    >
      Logout
    </button>
    <div class="mt-6">
      <h3 class="font-bold mb-2">Expenses</h3>
      <ul>
        <li v-for="exp in expenses" :key="exp.id">
          {{ exp.name }} - {{ exp.amount }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: null,
      expenses: [],
    };
  },
  async created() {
    this.user = JSON.parse(localStorage.getItem("user")) || null;
    if (this.user) {
      try {
        const res = await fetch(
          "http://localhost:8000/backend/expenses/list.php",
        );
        const data = await res.json();
        this.expenses = data.expenses || [];
      } catch (err) {
        console.error("Failed to fetch expenses", err);
      }
    } else {
      this.$router.push("/login");
    }
  },
  methods: {
    logout() {
      localStorage.removeItem("user");
      this.$router.push("/login");
    },
  },
};
</script>
