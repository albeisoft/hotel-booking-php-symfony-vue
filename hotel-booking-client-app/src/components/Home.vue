<script setup>  
  import useUserStore from '../store/user';
  const userStore = useUserStore();
  const isLogged = userStore.userIsAuth;  
</script>

<template>
  <h2>Home</h2>  
  <span v-if="!isLogged">Login to have access to Hotel Booking.</span>
  <span v-if="isLogged">You are logged to Hotel Booking.</span>
</template>