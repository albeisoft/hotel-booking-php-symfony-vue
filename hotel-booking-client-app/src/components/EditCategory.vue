<template>
    <div class="card m-1">
        <div>
            <span class="message">{{message}}</span>
        </div>
        <!-- <h5 class="card-header">Category</h5> -->
        <!-- Cat id: {{id}} <br /><br />
        Name: {{name}} <br />
        Price: {{price}} <br /> -->
        <div class="card-body">
            <Toast />

            <!-- <Form @submit="updateData" :validation-schema="schema" v-slot="{ errors }" :initial-values="formVlues"> -->                   
            <Form @submit="updateData" :validation-schema="schema" v-slot="{ errors }">
                <div class="form-row">
                    <div class="form-group col-5">                      
                       <!-- <Field :value="id" @input="event => id = event.target.value" 
                            name="id" type="hidden" class="form-control" />
                            The v-model directive helps us simplify the above to see input (yup field) with v-model -->

                        <Field v-model="form.id" name="id" type="hidden" class="form-control" />
                    </div>
                    <div class="form-group col-5">
                        <label>Name</label>
                        <Field v-model="form.name" name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }" />
                        <!-- <div class="p-error">{{errors.name}}</div> -->
                        &nbsp;<small class="p-error">{{errors.name}}</small>
                    </div>
                    <div class="form-group col-5">                        
                        <label>Price</label>
                        <Field v-model="form.price" name="price" type="text" class="form-control" :class="{ 'is-invalid': errors.price }" 
                        aria-describedby="text-error" />                        
                        <!-- <div class="p-error">{{errors.price}}</div> -->                        
                        &nbsp;<small class="p-error">{{errors.price}}</small>
                    </div>
                </div>
                <!-- <div class="form-group"> -->
                <div class="w-full flex justify-between">
                    <Button type="button" label="Close" @click="closeDialog({ buttonType: 'Close' })" />&nbsp;
                    <!-- <Button type="reset" label="Reset" />&nbsp; -->                    
                    <!-- <Button type="submit" label="Save" :disabled="!isComplete" /> -->
                    <Button type="submit" label="Save" />
                </div>
            </Form>
        </div>
    </div>

    <!-- // form example with Form and Field components
        <Form @submit="onSubmit" :validation-schema="schema" v-slot="{ errors }">
            <div class="form-row">
                <div class="form-group col">
                    <label>Categories</label>
                    <Field name="categories" as="select" class="form-control" :class="{ 'is-invalid': errors.title }">
                        <option value=""></option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>                        
                    </Field>
                    <div class="invalid-feedback">{{errors.title}}</div>
                </div>
                <div class="form-group col-5">
                    <label>First Name</label>
                    <Field name="firstName" type="text" class="form-control" :class="{ 'is-invalid': errors.firstName }" />
                    <div class="invalid-feedback">{{errors.firstName}}</div>
                </div>
                <div class="form-group col-5">
                    <label>Last Name</label>
                    <Field name="lastName" type="text" class="form-control" :class="{ 'is-invalid': errors.lastName }" />
                    <div class="invalid-feedback">{{errors.lastName}}</div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <label>Date of Birth</label>
                    <Field name="dob" type="date" class="form-control" :class="{ 'is-invalid': errors.dob }" />
                    <div class="invalid-feedback">{{errors.dob}}</div>
                </div>
                <div class="form-group col">
                    <label>Email</label>
                    <Field name="email" type="text" class="form-control" :class="{ 'is-invalid': errors.email }" />
                    <div class="invalid-feedback">{{errors.email}}</div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <label>Password</label>
                    <Field name="password" type="password" class="form-control" :class="{ 'is-invalid': errors.password }" />
                    <div class="invalid-feedback">{{errors.password}}</div>
                </div>
                <div class="form-group col">
                    <label>Confirm Password</label>
                    <Field name="confirmPassword" type="password" class="form-control" :class="{ 'is-invalid': errors.confirmPassword }" />
                    <div class="invalid-feedback">{{errors.confirmPassword}}</div>
                </div>
            </div>
            <div class="form-group form-check">
                <Field name="acceptTerms" type="checkbox" id="acceptTerms" value="true" class="form-check-input" :class="{ 'is-invalid': errors.acceptTerms }" />
                <label for="acceptTerms" class="form-check-label">Accept Terms & Conditions</label>
                <div class="invalid-feedback">{{errors.acceptTerms}}</div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary mr-1">Register</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </Form>
    -->


    <!--
        <div class="card flex flex-column align-items-center gap-3">
        <form action="post">                         
            < !-- InputText component (primevue component) renders a native input element that implicitly includes any passed prop. 
            Value to describe the component can either be provided via label tag combined with id prop or using aria-labelledby, aria-label props. 
            -- >
            
                <label for="name">Name</label>
                <InputText id="name" />
                or    
                <span id="name">Name</span>
                <InputText aria-labelledby="name" />
                or
                <InputText aria-label="Name"/>        
        </form>            
    </div>
    -->

</template>

<script>
import { mapActions, mapState } from 'pinia';
import useGeneralStore from '../store/general';
import useUserStore from '../store/user';
import axios from 'axios';

import { useToast } from 'primevue/usetoast';
//import { Form, Field, ErrorMessage } from 'vee-validate';
import { Form, Field } from 'vee-validate';

// vee-validate’s entire core size is very small, but the same can’t be said about the validators you import. 
// next line of code import everything yup has to offer
import * as yup from 'yup';
// only import what you need from yup
//import { object, string } from 'yup';

export default {
    name: "EditCategory",
    inject: ['dialogRef'],
    components: {
        Form,
        Field,
    },
    data() {   
        // validating form data on Vue 3 with vee-validate and yup libraries
        // form validation schema
        const schema = yup.object().shape({
            name: yup.string()
                .required('Name is required.')
                .min(2, 'Must be at least 2 characters.'),
            price: yup.number()
                .required('Price is required.')
                .positive()
                .integer()
                .min(10, 'Must be minimum of 10.'),                
        });
        
        /* // Validate forms with Vue 3 and VeeValidate library: https://vee-validate.logaretm.com/v4 | https://www.npmjs.com/package/vee-validate
        // Also good to use Yup library (on VeeValidate): https://www.npmjs.com/package/yup
- 
        const schema = Yup.object().shape({
            title: Yup.string()
                .required('Title is required'),
            firstName: Yup.string()
                .required('First Name is required'),
            lastName: Yup.string()
                .required('Last name is required'),
            dob: Yup.string()
                .required('Date of Birth is required')
                .matches(/^\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/, 'Date of Birth must be a valid date in the format YYYY-MM-DD'),
            email: Yup.string()
                .required('Email is required')
                .email('Email is invalid'),
            password: Yup.string()
                .min(6, 'Password must be at least 6 characters')
                .required('Password is required'),
            confirmPassword: Yup.string()
                .oneOf([Yup.ref('password'), null], 'Passwords must match')
                .required('Confirm Password is required'),
            acceptTerms: Yup.string()
                .required('Accept Ts & Cs is required')
        });
        */
        return {
           message: null,
           schema,
           form: {
                id: null,
                name: null,
                price: null,
            },
            isEditCheck: null,
        }
    }, // data

    setup() {             
        const toast = useToast();       
        
    }, // setup
    mounted() {        
        // get category data        
        this.form.id = this.dialogRef.data.dataId;
        this.form.name = this.dialogRef.data.dataName;
        this.form.price = this.dialogRef.data.dataPrice;

        console.log('this.dialogRef.data #  ' + JSON.stringify(this.dialogRef.data, null, 2));
    },
    computed: {
        ...mapState(useGeneralStore, [
            'API_URL',
        ]),           
        isComplete() {
            return this.form.name && this.form.price;
        }
     }, // computed
    methods: {
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
        updateData() {
            // form values if form validation is ok
            console.log('Form\'s data was saved. \n' + JSON.stringify(this.form, null, 2));            
            //console.log('this.API_URL # ' + this.API_URL + 'category/' + this.form.id);
            //console.log(`put on API_URL # ${this.API_URL}category/${this.form.id}`);
            // save data to database            
            /* */
            const userStore = useUserStore();
            const token = userStore.token;
            //const id = this.form.id;
            const id = this.dialogRef.data.dataId;
            const addAPI = `${this.API_URL}category`;
            const editAPI = `${this.API_URL}category/${id}`;

            this.isEditCheck = this.dialogRef.data.isEditCheck;

            /*
            axios.interceptors.response.use(undefined, (err) => {
            const { config, message } = err;
            if (!config || !config.retry) {
                return Promise.reject(err);
            }
            // retry while Network timeout or Network Error
            if (!(message.includes("timeout") || message.includes("Network Error"))) {
                return Promise.reject(err);
            }
            config.retry -= 1;
            const delayRetryRequest = new Promise((resolve) => {
                setTimeout(() => {
                console.log("retry the request", config.url);
                resolve();
                }, config.retryDelay || 1000);
            });
            return delayRetryRequest.then(() => axios(config));
            });

            axios.put(`${this.API_URL}category/${id}`, this.form, {
              headers: {
                Authorization: `Bearer ${token}`,
                retry: 3, retryDelay: 3000
              },
            })...
            */

            axios({
                method: this.isEditCheck ? 'put' : 'post',
                url: this.isEditCheck ? editAPI : addAPI,
                data: this.form,
                headers: {
                    Authorization: `Bearer ${token}`,
                    //'Content-Type': 'application/json',               
                },
            })

            /* // axios used without dynamic use of api verbs and url's
            axios.put(apiUrlOk, this.form, {
              headers: {
                Authorization: `Bearer ${token}`,                
              },
            })
            */    

            .then(response => {
                // data was saved
                // response.data                

                this.id = response.data.id
                //refresh data table with edited (updated) data from response.data
                //console.log('response.data # ' + JSON.stringify(response.data));
                //console.log('response.data.id #' + response.data.id);

                //this.dialogRef.close({id: 12});             
                //this.dialogRef.close();
                this.dialogRef.close(response.data);
            }).catch(error => {

                //console.log('error # ' + error);
                if (error.response != null) {
                    this.message = error.response.data.message;                  
                    if (this.message == 'Expired JWT Token')
                    {
                        this.logUserOut();
                    }
                }         
                else 
                {
                    console.log('error # ' + error);
                    this.message = error.response;                    
                }

                if (error.request!= null) {                    
                    console.log('error request # ' + error);
                    this.message = error;
                }
            }).finally(() => {
                // if an error is catched or not this always will run
            });
            /* */
        },

        /*
        selectCategory(data) {
            this.dialogRef.close(data);
        },
        showInfo() {
            this.$dialog.open(InfoDemo, {
                props: {
                    header: 'Information',
                    modal: true,
                    //dismissableMask: true
                },
                data: {
                    //totalCategories: this.data ? this.data.length : 0
                }
            });
        }
        */
             
        closeDialog() {
            try {
                this.params.display = false
                //this.dialogRef.close({id: 2});
            } catch (error) {
                // log errors 
                return null;
            }
        },
        closeDialog(e) {
            this.dialogRef.close(e);
        }

    } // methods
}
</script>