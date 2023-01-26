import './plugins/bootstrap';
import Vuetify from './plugins/vuetify.js';
import { createRouter, createWebHistory } from 'vue-router'
import { createPinia} from 'pinia'
import { createApp } from 'vue';

import App from './App.vue';
import routes from '~pages'

console.log(routes);

import { useUserStore } from './store/user';

const pinia =  createPinia()
const app = createApp(App);

const router = createRouter({
  history: createWebHistory(),
  routes,
})

app.use(Vuetify)
app.use(router)
app.use(pinia)


const userStore = useUserStore()

router.beforeEach((to, from, next) => {
  if (to.name !== 'login' && to.meta.requiresAuth && !userStore.isAuthenticated) next({ name: 'login' })
  else next()
}) 


app.mount('#app');
