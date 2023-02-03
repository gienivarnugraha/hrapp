import './plugins/bootstrap';
import '../sass/app.scss'
import Vuetify from './plugins/vuetify.js';
import { createRouter, createWebHistory } from 'vue-router'
import { createPinia } from 'pinia'
import { createApp } from 'vue';

import App from './App.vue';
import routes from '~pages'

import { useUserStore } from './store/user';
import { storeToRefs } from 'pinia';


const pinia = createPinia()
const app = createApp(App);

const router = createRouter({
  history: createWebHistory(),
  routes,
})

app.use(Vuetify)
app.use(router)
app.use(pinia)


const { isAuthenticated } = storeToRefs(useUserStore())
const { getUser } = useUserStore()

getUser()

router.beforeEach((to, from, next) => {
  document.title = `${to.meta.title} - HRAPP`

  if (to.name !== 'login' && to.meta.requiresAuth && !isAuthenticated) next({ name: 'login' })
  else next()
})

app.mount('#app');
