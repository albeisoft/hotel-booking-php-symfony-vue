import { defineStore } from "pinia";

const useUserStore = defineStore("user", {
  state: () => ({
    token: localStorage.getItem("token") || null,
    storedUser: localStorage.getItem("user") || null,
  }),
  getters: {
    user: (state) => {
      if (!!state.storedUser) {
        return JSON.parse(state.storedUser);
      }
      return state.storedUser;
    },
    userIsAuth: (state) => !!state.token,
  },
  actions: {
    storeLoggedInUser(token, user) {
      // Save the token to localStorage
      localStorage.setItem("token", token);

      // Save the user to localStorage
      const stringifiedUser = JSON.stringify(user);
      localStorage.setItem("user", stringifiedUser);

      // Save the token and user to the store ktate
      this.token = token;
      this.storedUser = stringifiedUser;
    },

    logoutUser() {
      // Delete the token from localStorage
      localStorage.removeItem("token");

      // Delete the user from localStorage
      localStorage.removeItem("user");

      // Delete the token and user from the state
      this.token = null;
      this.storedUser = null;
    },
  },
});

export default useUserStore;
