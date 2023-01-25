
export const useUserStore = defineStore('user',{
  state: () => ({
    user: {},
    isAuthenticated: false,
  }),
  actions:{
    login(user){
      this.user = user;
      this.isAuthenticated = true;
    },
    logout() {
      this.user = {};
      this.isAuthenticated = false;
    }
  }
})