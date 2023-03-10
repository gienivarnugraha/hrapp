import axios from 'axios';
import Cookies from 'universal-cookie';

const cookies = new Cookies();

export const useUserStore = defineStore('user', {
  state: () => ({
    user: {},
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

        await this.router.push('/dashboard');

      } catch (error) {
        console.log(error);
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

      }).catch(error => {
        console.log('logoout error: ', error);
      });

    }
  }
})