<template>
    <div id="wrapper" class="auth-main">
        <div class="container">
            <div class="row clearfix">
                <div class="col-lg-4">
                    <div class="card">
                        <div>
                            <span class="message">{{message}}</span>
                            <h2>Register</h2>
                            <p class="lead">Create an account</p>
                        </div>
                        <div class="body">
                            <form class="form-auth-small" @submit.prevent="submitting === false && registerUser()">                                
                                <div class="form-group">
                                    <label for="email" class="control-label sr-only">Email </label>
                                    <input v-model="email" type="email" class="form-control" id="email" placeholder="Email" />
                                </div>
                                <div class="form-group">
                                    <label for="password" class="control-label sr-only">Password </label>
                                    <input v-model="password" type="password" class="form-control" id="password" placeholder="Password" />
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
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
                email: '',
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
                this.$router.push('/');
            }
        },
        methods: {
            ...mapActions(useUserStore, [
                'storeLoggedInUser'
            ]),
            registerUser() {                
                this.submitting = true;
                console.log('check 1 # ' + this.email + ' # ' + this.password);
                if (this.email!='' &&  (this.password!='' && this.password.length>=6))
                {                    
                    axios.post(`${this.API_URL}register`, {                    
                        email: this.email,
                        password: this.password,
                    }).then(response => {     
                        this.message = response.data.message;
                        //alert(response.data.message);
                        this.email='';
                        this.password='';
                    }).catch(error => {
                        this.message = error.response.data.message;
                        //alert(error.response.data.message);
                        console.log('error # ' + error);
                        
                    }).finally(() => {
                        // if an error is catched or not this always will run
                        this.submitting = false;                        
                    });
                }
                else {
                    this.message = 'Email and Password fields are required. Password must have minim 6 characters.';
                }

            }
        }
    }
</script>
  
<style>
  
</style>