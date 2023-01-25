
export const useUserStore = defineStore('user',{
  state: () => ({
    user: {},
    authenticated: false,
  }),
  getters:{
    isAuthenticated: (state) => state.authenticated || Boolean( localStorage.getItem('authenticated')),
  },
  actions:{
    login(user){
      this.user = user;
      this.authenticated = true;
      localStorage.setItem('user', JSON.stringify(user))
      localStorage.setItem('authenticated', true)
    },
    logout() {
      this.user = {};
      this.authenticated = false;
      localStorage.removeItem('user')
      localStorage.removeItem('authenticated')
    }
  }
})