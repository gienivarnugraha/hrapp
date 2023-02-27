<template>
  <v-app :theme="theme">
    <v-app-bar app :elevation="2">
      <v-app-bar-nav-icon @click="drawer = !drawer" v-if="isAuthenticated"></v-app-bar-nav-icon>

      <template v-slot:append class="mr-10">
        <v-tooltip text="Theme">
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" location="bottom" :icon="theme === 'light' ? 'mdi-weather-sunny' : 'mdi-weather-night'"
              @click="onClick"></v-btn>
          </template>
        </v-tooltip>

        <!-- v-if="userStore.isAuthenticated" -->
        <v-tooltip text="Logout" v-if="isAuthenticated">
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" location="bottom" icon="mdi-logout" :loading="loading" @click="logout"></v-btn>
          </template>
        </v-tooltip>
      </template>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" temporary border v-if="isAuthenticated">
      <v-list>
        <v-list-item prepend-avatar="https://randomuser.me/api/portraits/women/85.jpg" :title="currentUser.name"
          :subtitle="currentUser.nik"></v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-item v-for="menu in menus" :key="menu.id" :prepend-icon="menu.icon" :title="menu.title"
          :value="menu.value" link :to="menu.to"></v-list-item>
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
const { isAuthenticated, currentUser } = storeToRefs(useUserStore())
const { logout: logoutService, getUser } = useUserStore()

const menus = [
  { id: 1, icon: 'mdi-folder', title: 'Dashboard', value: 'dashboard', to: '/dashboard' },
  { id: 2, icon: 'mdi-account-multiple', title: 'Peoples', value: 'peoples', to: '/peoples' },
  { id: 3, icon: 'mdi-star', title: 'Competencies', value: 'competencies', to: '/competencies' },
  { id: 4, icon: 'mdi-briefcase', title: 'Job Title', value: 'job_titles', to: '/job' },
]

const theme = ref('light')
const drawer = ref(false)
const loading = ref(false)

function onClick() {
  theme.value = theme.value === 'light' ? 'dark' : 'light'
}

const logout = async () => {
  loading.value = true
  try {
    await logoutService()

    router.push({ name: 'login' })

  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  getUser()
})
</script>


<style lang="css">
.bg-color {
  background-color: #1565C0;
}

.v-toolbar__append {
  margin-inline-end: 32px !important;
}

.bg-color-dark {
  background-color: #110420;
}
</style>

<route lang="yaml">
  name: login
  meta:
    requiresAuth: false
</route>