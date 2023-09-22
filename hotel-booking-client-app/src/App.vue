<style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
  margin-top: 2rem;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }

  nav {
    text-align: left;
    margin-left: -1rem;
    font-size: 1rem;

    padding: 1rem 0;
    margin-top: 1rem;
  }
}
</style>

<template>
  <header>    
    <div class="wrapper">
      <!-- isLogged: {{isLogged}} -->
      <nav>
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/reservations" v-if="isLogged">Reservations</RouterLink>
        <RouterLink to="/clients" v-if="isLogged">Clients</RouterLink>
        <RouterLink to="/rooms" v-if="isLogged">Rooms</RouterLink>
        <RouterLink to="/categories" v-if="isLogged">Categories</RouterLink> 
        <RouterLink to="/login" v-if="!isLogged">Login</RouterLink>
         <span v-if="!isLogged"> - <RouterLink to="/register">Register</RouterLink></span>
         <!-- <span v-if="isLogged"> - <RouterLink to="/">Logout</RouterLink></span> -->
         <a @click.prevent="loggingOut === false && logUserOut()" href="#" v-if="isLogged">Logout</a>
      </nav>
    </div>
  </header>
  <br />
  
  <RouterView />  
</template>

<script>
  //import { RouterLink, RouterView } from 'vue-router'
  import useGeneralStore from './store/general';
  import useUserStore from './store/user';
  import { mapActions, mapState } from 'pinia';  

  export default {

      data: () => {
        const userStore = useUserStore();
        return {              
            isLogged: (userStore.userIsAuth === true) ? true : false,            
            loggingOut: false,
        }
      },
      
      computed: {
          ...mapState(useGeneralStore, [
              'API_URL',
          ]),
          ...mapState(useUserStore, [
              'userIsAuth'
          ])
      },
      watch: {
          userIsAuth() {
              /* 
                  By default the userIsAuth value is false.
                  On successful registration, after the storeLoggedInUser
                  method is called, the userIsAuth value is 
                  set to true and this watch property is invoked.
                  The code below will then run.
              */

              this.$router.push('/');
          }
      },
      methods: {
          ...mapActions(useUserStore, [
              'storeLoggedInUser'
          ]),
          ...mapActions(useUserStore, [
                'logoutUser'
          ]),
          logUserOut() {              
              this.logoutUser();              
              this.loggingOut = true;
              
              // refresh current page
              //windows.history.go();
              //location.reload(); - in some browsers can't reload
              //window.location.reload();
              // vue router reload current page - if you use TypeScript
              //this.$router.go(0);
              this.$router.go();
          },          
      }
  }
</script>