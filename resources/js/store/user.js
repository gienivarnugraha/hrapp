import axios from 'axios';
import _ from 'lodash';
import Cookies from 'universal-cookie';

const cookies = new Cookies();

export const useUserStore = defineStore('user', {
  state: () => ({
    user: {},
  }),
  getters: {
    isAuthenticated: (state) => !_.isEmpty(state.user),
    isMGR: (state) => state.user.role === 'MGR' || state.user.role === 'ADMIN',
    isHRBP: (state) => state.user.role === 'HRBP' || state.user.role === 'ADMIN',
    isSME: (state) => state.user.role === 'SME' || state.user.role === 'ADMIN',
    currentUser: (state) => state.user
  },
  actions: {
    getUser(){
      axios.get('/api/user').then(({data:user})=> {
        this.user = user;
        return user
      }).catch((err) => {
        console.log(err);
      })
    },
    async login({email, password}) {
      try {
        await axios.get('sanctum/csrf-cookie')
  
        const login = await axios.post('api/login', {
          email: email.value,
          password: password.value
        })

        this.getUser();
        
  
      } catch (error) {
        console.log('loginerror', error);
      }
    },
    logout() {
      axios.post('/api/logout').then(response => {
        this.user = {};
      }).catch(error => {
        console.log('logoout error: ',error);
      });

    }
  }
})