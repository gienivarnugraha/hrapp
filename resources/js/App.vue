<template>
  <v-app :theme="theme" >
    <v-app-bar app>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

      <v-spacer></v-spacer>

      <v-btn :icon="theme === 'light' ? 'mdi-weather-sunny' : 'mdi-weather-night'" @click="onClick"></v-btn>

      <v-btn v-if="userStore.isAuthenticated" icon="mdi-logout" :loading="loading" @click="logout"></v-btn>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" fixed temporary>
    </v-navigation-drawer>

    <v-main :class="theme === 'light' ? 'bg-color' : 'bg-color-dark'">
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useUserStore } from './store/user';

const router = useRouter()
const userStore = useUserStore()

const theme = ref('light')
const drawer = ref(false)
const loading = ref(false)

function onClick() {
  theme.value = theme.value === 'light' ? 'dark' : 'light'
}

const logout = async () => {
  loading.value = true
  try {
    await axios.post('/logout')

    router.push({ name: 'login' })

  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false
  }
}
</script>


<style lang="css">
.bg-color {
  background-color: #6a64e5;
}
.bg-color-dark {
  background-color: #1e181e;
}
</style>

<route lang="yaml">
  name: login
  meta:
    requiresAuth: false
</route>