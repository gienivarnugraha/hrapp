import axios from 'axios';
import Cookies from 'universal-cookie';

const cookies = new Cookies();

export const useUserStore = defineStore('user', {
  state: () => ({
    user: cookies.get('user'),
  }),
  getters: {
    isAuthenticated: (state) => !_.isEmpty(state.user),
    currentUser: (state) => state.user
  },
  actions: {
    async getUser() {
      try {
        const { data: user } = await axios.get('/api/user')

        if (_.isEmpty(cookies.get('user'))) {
          cookies.set('user', user)
        }

        this.user = user

        return this.user
      } catch (error) {
        console.log('login error',error);
        if (error.response.status === 401) {
          cookies.remove('user');
        }
      }
    },

    async login({ email, password }) {
      try {
        await axios.get('sanctum/csrf-cookie')

        await axios.post('api/login', {
          email: email.value,
          password: password.value
        })

        await this.getUser();

        this.router.push('/dashboard');

      } catch (error) {
        if (error.response.status === 401) {
          cookies.remove('user');
        }
        throw error.response.data;
      }
    },
    logout() {
      axios.post('/api/logout').then(response => {
        this.user = {};
        cookies.remove('user');
        this.router.push('/login');

      }).catch(error => {
        console.error('logoout error: ', error);
      });

    }
  }
})