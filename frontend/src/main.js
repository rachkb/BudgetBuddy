import "./assets/tailwind.css";

import { createApp } from "vue";
import App from "./App.vue";
import { createRouter, createWebHistory } from "vue-router";

import LoginForm from "./components/LoginForm.vue";
import SignupForm from "./components/SignupForm.vue";
import Dashboard from "./components/Dashboard.vue";
import TransactionsPage from "./components/TransactionsPage.vue";
import CategoriesPage from "./components/CategoriesPage.vue";
import ReportsPage from "./components/ReportsPage.vue";
import ProfilePage from "./components/ProfilePage.vue";

const routes = [
  { path: "/login", component: LoginForm },
  { path: "/signup", component: SignupForm },
  { path: "/dashboard", component: Dashboard },
  { path: "/transactions", component: TransactionsPage },
  { path: "/categories", component: CategoriesPage },
  { path: "/reports", component: ReportsPage },
  { path: "/profile", component: ProfilePage },
  { path: "/", redirect: "/login" }, // default route
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

createApp(App).use(router).mount("#app");
