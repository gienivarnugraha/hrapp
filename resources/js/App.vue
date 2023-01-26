<template>
  <v-app :theme="theme">
    <v-app-bar app :elevation="2">
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

      <template v-slot:append class="mr-10">
        <v-tooltip text="Theme">
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" location="bottom" :icon="theme === 'light' ? 'mdi-weather-sunny' : 'mdi-weather-night'"
              @click="onClick"></v-btn>
          </template>
        </v-tooltip>

        <!-- v-if="userStore.isAuthenticated" -->
        <v-tooltip text="Logout">
          <template v-slot:activator="{ props }">
            <v-btn  v-bind="props" location="bottom" icon="mdi-logout" :loading="loading" @click="logout"></v-btn>
          </template>
        </v-tooltip>
      </template>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" temporary border>
      <v-list>
          <v-list-item
            prepend-avatar="https://randomuser.me/api/portraits/women/85.jpg"
            title="Sandra Adams"
            subtitle="sandra_a88@gmailcom"
          ></v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-list density="compact" nav>
          <v-list-item prepend-icon="mdi-folder" title="Dashboard" value="dashboard" link to="/dashboard"></v-list-item>
          <v-list-item prepend-icon="mdi-account-multiple" title="Users" value="users" link to="/users"></v-list-item>
          <v-list-item prepend-icon="mdi-star" title="Competencies" value="competencies" link to="/competencies"></v-list-item>
        </v-list>
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

.v-toolbar__append{
  margin-inline-end: 40px !important;
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