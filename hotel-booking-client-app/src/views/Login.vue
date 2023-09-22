<template>
  <div id="wrapper" class="auth-main">
    <div class="container">
        <div class="row clearfix">
            
            <div class="col-lg-8">
                <div class="auth_detail">
                    
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div>
                        <span class="message">{{message}}</span>
                        <h2>Login</h2>
                    </div>
                    <br />
                    <div class="body">
                        <form class="form-auth-small" @submit.prevent="submitting === false && loginUser()">
                            <div class="form-group">
                                <label for="username" class="control-label sr-only">Username </label>
                                <input v-model="username" type="email" class="form-control" id="username" placeholder="Email" />
                            </div>
                            <div class="form-group">
                                <label for="password" class="control-label sr-only">Password </label>
                                <input v-model="password" type="password" class="form-control" id="password" placeholder="Password" />
                            </div>
                            <br />
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                            <div class="bottom">
                                <br />
                                <span>Don't have an account? <router-link to="/register">Register</router-link></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
    import axios from 'axios';
    import useGeneralStore from '../store/general';
    import useUserStore from '../store/user';
    import { mapActions, mapState } from 'pinia';
    export default {
        data: () => {
            return {
                message: null,
                username: '',
                password: '',
                submitting: false
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
                
                //this.$router.push('/');
                
                this.$router
                    .push({ path: '/' })
                    .then(() => { this.$router.go() })
                //It first redirects to / and then reloads the page. Helps with getting updated navbar after login.
                
            }
        },
        methods: {
            ...mapActions(useUserStore, [
                'storeLoggedInUser'
            ]),
            loginUser() {                
                this.submitting = true;
                axios.post(`${this.API_URL}login_check`, {
                    username: this.username,
                    password: this.password,
                }).then(response => {
                    //const token = response.data.authorisation.token;                                       
                    //console.log('token # ' + response.data.token);

                    const token = response.data.token;
                    const user = this.username;
                    this.storeLoggedInUser(token, user);
                    
                }).catch(error => {
                    //console.log(error);
                    if (error.response != null)
                       this.message = error.response.data.message;

                    //alert(error.response.data.message);
                    console.log('error # ' + error);
                }).finally(() => {
                    // if an error is catched or not this always will run
                    this.submitting = false;
                });
            }
        }
    }
</script>

<style>

</style>