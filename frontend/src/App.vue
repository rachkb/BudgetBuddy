<template>
  <div id="app">
    <!-- Router will render the current page here -->
    <router-view :key="$route.fullPath" />
  </div>
</template>

<script>
export default {
  name: "App",
};
</script>

<style>
body {
  font-family: "Inter", sans-serif;
}
</style>
