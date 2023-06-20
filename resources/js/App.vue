<template>
  <v-app :theme="theme">
    <v-app-bar app :elevation="2">
      <v-app-bar-nav-icon @click="drawer = !drawer" v-if="isAuthenticated"></v-app-bar-nav-icon>
      
      <img src="/storage/img/polban.png" alt="" width="40" class="d-none d-sm-block mx-4 mx-md-8" />
      
      <div class="w-100" id="page-header">
      </div>
      <template v-slot:append class="mr-10">
        <v-tooltip text="Theme" location="bottom">
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" :icon="theme === 'light' ? 'mdi-weather-sunny' : 'mdi-weather-night'"
              @click="onClick"></v-btn>
          </template>
        </v-tooltip>

        <v-menu v-if="isAuthenticated" v-model="account" :close-on-content-click="false" location="bottom">
          <template v-slot:activator="{ props }">
            <v-icon icon="mdi-account" color="primary" v-bind="props">
            </v-icon>
          </template>

          <v-card min-width="400">
            <v-list>
              <v-list-item prepend-avatar="/storage/img/polban.png">
                <v-list-item-title>
                  <v-text-field v-if="accountEdit" class="w-100" v-model="currentUser.name" density="compact" hide-details></v-text-field>
                  <span v-else> {{ currentUser.name }} </span>
                </v-list-item-title>
                <v-list-item-subtitle>
                  <span> {{ currentUser.nik }} </span>
                </v-list-item-subtitle>
                <template v-slot:append>
                  <v-btn v-if="accountEdit" variant="text" icon="mdi-check" @click="saveAccount"></v-btn>
                  <v-btn variant="text" :color="accountEdit ? 'error' : 'primary'" :icon="accountEdit? 'mdi-close' : 'mdi-pencil'"
                    @click="accountEdit = !accountEdit"></v-btn>
                </template>
              </v-list-item>
            </v-list>

            <v-divider></v-divider>

            <v-card-actions>
              <v-btn location="bottom" :rounded="false" block flat prepend-icon="mdi-logout" color="error" :loading="loading" @click="logout"> Logout</v-btn>
            </v-card-actions>
          </v-card>
        </v-menu>


      </template>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" temporary border v-if="isAuthenticated">
      <v-list>
        <v-list-item prepend-avatar="/storage/img/polban.png" :title="currentUser.name"
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
import axios from 'axios';
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
const account = ref(false)
const accountEdit = ref(false)
const loading = ref(false)

function onClick() {
  theme.value = theme.value === 'light' ? 'dark' : 'light'
}

const logout = async () => {
  loading.value = true
  try {
    await logoutService()
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false
  }
}

const saveAccount = async () => {
  loading.value = true
  try {
    await axios.put('/api/user', {name: currentUser.value.name})
    accountEdit.value = false
  } catch (error) {
    console.error(error);
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