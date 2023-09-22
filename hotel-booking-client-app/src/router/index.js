import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Register from "../views/Register.vue";
import Login from "../views/Login.vue";
import useUserStore from "../store/user";

import Categories from "../views/Categories.vue";
import Rooms from "../views/Rooms.vue";
import Clients from "../views/Clients.vue";
import Reservations from "../views/Reservations.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: Home,
      //-- uncoment next comment if you wish to make Home page private, to be accessed after login
      /* 
      beforeEnter: (to, from, next) => {
        // Install the user store
        const userStore = useUserStore();
        // Redirect if user is not authenticated
        if (userStore.userIsAuth === false) {
          return next("/login");
        }
        // Allow route entry if user is authenticated
        return next();
      },
      */
    },
    /*
    {
      path: "/about",
      name: "about",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../views/About.vue"),
    },*/

    {
      path: "/categories",
      name: "categories",
      component: Categories,
      beforeEnter: (to, from, next) => {
        // Install the user store
        const userStore = useUserStore();
        // Redirect if user is not authenticated
        if (userStore.userIsAuth === false) {
          return next("/");
        }

        // Allow route entry if user is authenticated
        return next();
      },
    },
    {
      path: "/rooms",
      name: "rooms",
      component: Rooms,
      beforeEnter: (to, from, next) => {
        // Install the user store
        const userStore = useUserStore();
        // Redirect if user is not authenticated
        if (userStore.userIsAuth === false) {
          return next("/");
        }
        // Allow route entry if user is authenticated
        return next();
      },
    },
    {
      path: "/clients",
      name: "clients",
      component: Clients,
      beforeEnter: (to, from, next) => {
        // Install the user store
        const userStore = useUserStore();
        // Redirect if user is not authenticated
        if (userStore.userIsAuth === false) {
          return next("/");
        }
        // Allow route entry if user is authenticated
        return next();
      },
    },
    {
      path: "/reservations",
      name: "reservations",
      component: Reservations,
      beforeEnter: (to, from, next) => {
        // Install the user store
        const userStore = useUserStore();
        // Redirect if user is not authenticated
        if (userStore.userIsAuth === false) {
          return next("/");
        }
        // Allow route entry if user is authenticated
        return next();
      },
    },
    {
      path: "/register",
      name: "register",
      component: Register,
      beforeEnter: (to, from, next) => {
        // Install the user store
        const userStore = useUserStore();
        // Redirect if user is authenticated
        if (userStore.userIsAuth === true) {
          return next("/");
        }
        // Allow route entry if user is not authenticated
        return next();
      },
    },
    {
      path: "/login",
      name: "login",
      component: Login,
      /*
      beforeEnter: (to, from, next) => {
        // Install the user store
        const userStore = useUserStore();
        // Redirect if user is authenticated
        if (userStore.userIsAuth === true) {
          return next("/");
        }
        // Allow route entry if user is not authenticated
        return next();
      },
      */
    },
  ],
});

export default router;
