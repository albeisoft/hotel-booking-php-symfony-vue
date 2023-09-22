<style>
  .message {
    color: #FF0000;
  }
  
@media (min-width: 1024px) {
  .mobileCategories {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>

<template> 
  <!--
    <div>
      <button @click="categoriesAll">Get categories from categoriesAll from view</button> <br />
      <div v-for="cat in categories" :key="cat.id">
        <span>{{ cat.id }} - {{ cat.name }}</span>
      </div>
    </div>
  -->
  <div class="card">
    <div>
      <span class="message">{{message}}</span>
      <h3>Categories</h3>
    </div>
    <br />

    <Toast />
    <ConfirmDialog />
    <DynamicDialog />

    <Toolbar class="mb-4">
      <template #start>
        <Button label="Add" icon="pi pi-plus" severity="success" class="mr-2" @click="editRecord(data, 0)" size="normal" />&nbsp;
        <Button label="Delete selected records" icon="pi pi-trash" severity="danger" @click="deleteSelectedRecordsConfirm()" 
        :disabled="!selectedRecords || !selectedRecords.length" size="normal" />
      </template>

      <template #end>
          <!-- <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" chooseLabel="Import" class="mr-2 inline-block" />&nbsp; -->
          <!-- <Button label="Export" icon="pi pi-upload" severity="help" @click="exportCSV($event)" /> -->
          <Button label="Export" icon="pi pi-download" severity="help" @click="exportCSV($event)" />
      </template>
    </Toolbar>

    <DataTable ref="dt" 
      v-if="loaded" 
      :loading="loading" 
      :value="categories" 
      dataKey="id" 
      v-model:selection="selectedRecords" 
      v-model:filters="filters"             
      filterDisplay="menu" 
      :globalFilterFields="['name', 'price']" 
      paginator :rows="10" :rowsPerPageOptions="[10, 10, 20, 50]" 
      paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink" 
      currentPageReportTemplate="{first} to {last} of {totalRecords}" 
      tableStyle="min-width: 50rem">
      
      <template #header>
        <div class="flex flex-wrap gap-2 align-items-center justify-content-between">
        <!-- <div class="flex justify-content-between"> -->
            <Button type="button" icon="pi pi-filter-slash" label="Clear filter(s)" outlined @click="clearFilter()" size="small" />&nbsp;
            <span class="p-input-icon-left">
                <i class="pi pi-search" />
                <InputText v-model="filters['global'].value" placeholder="Search..." />
            </span>
        </div>
      </template>      
      <template #empty>No data found.</template>
      <template #loading>Loading data. Wait.</template>

      <!-- <Column field="id" header="Id"></Column>
      <Column field="name" header="Name"></Column> -->      
      <!-- Dynamic column generation
        <Column v-for="col of columns" sortable :key="col.field" :field="col.field" :header="col.header"></Column>
      -->     
      <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
      <Column field="name" header="Name" sortable  style="min-width: 5rem">
        <template #body="{ data }">
            {{ data.name }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by name" />
        </template>
      </Column>
      <Column field="price" header="Price" sortable  style="min-width: 5rem">
        <template #body="{ data }">
            {{ data.price }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by price" />
        </template>
      </Column>
      <Column :exportable="false" header="Action" style="min-width: 5rem">
        <template #body="{ data }">
            <!-- <div class="justify-content-center"> -->
            <div class="flex justify-content-center flex-wrap gap-3">
              <Button label="Edit" icon="pi pi-pencil" @click="editRecord(data, 1)" />&nbsp;              
              <!-- <Button label="Confirm" icon="pi pi-trash" @click="confirm1()" />&nbsp; -->
              <Button label="Delete" icon="pi pi-trash" severity="danger" @click="deleteRecordConfirm(data.id)" />
              
              <!-- -- >
              <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editRecord(data, 1)" />&nbsp;
              <Button icon="pi pi-trash" outlined rounded severity="danger" @click="deleteRecordConfirm(data.id)" />
              < !-- -->
              <!-- // size of buttons can be small, normal (default is normal), large
                <div class="card flex justify-content-center flex-wrap gap-3">
                    <Button label="Primary" />
                    <Button label="Secondary" severity="secondary" />
                    <Button label="Success" severity="success" />
                    <Button label="Info" severity="info" />
                    <Button label="Warning" severity="warning" />
                    <Button label="Help" severity="help" />
                    <Button label="Danger" severity="danger" />
                </div>

                // Raised buttons display a shadow to indicate elevation.
                <Button label="Primary" raised />
                <Button label="Secondary" severity="secondary" raised />
                <Button label="Success" severity="success" raised />
                <Button label="Info" severity="info" raised />
                <Button label="Warning" severity="warning" raised />
                <Button label="Help" severity="help" raised />
                <Button label="Danger" severity="danger" raised />

                // Rounded buttons have a circular border radius.
                <Button label="Primary" rounded />
                <Button label="Secondary" severity="secondary" rounded />
                <Button label="Success" severity="success" rounded />
                <Button label="Info" severity="info" rounded />
                <Button label="Warning" severity="warning" rounded />
                <Button label="Help" severity="help" rounded />
                <Button label="Danger" severity="danger" rounded />

                // So button shape can be also: text, text raised, outlined, link,... 
              -->              

          </div>
        </template>
      </Column>      
      <template #footer> There are {{ categories ? categories.length : 0 }} records. </template>    
    </DataTable>
  </div> 

  <br />
  <!--
  <div class="card">
    <span class="message">{{message}}</span>
    <h2>Categories</h2>  
    <DataTable :value="products" tableStyle="min-width: 50rem">        
        < !--         
        <Column field="id" header="Id"></Column>
        <Column field="name" header="Name"></Column>
         -- >
        <! -- dynamic column name generation -- >
        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header"></Column>
    </DataTable>
  </div>
  -->

</template>

<script>
    // in this file is used Options API
    import { mapActions, mapState } from 'pinia';
    import useGeneralStore from '../store/general';
    import useUserStore from '../store/user';
    import { useCategoryStore } from '../store/category';
        
    import axios from 'axios';
    import { markRaw, defineAsyncComponent } from 'vue';

    // primevue import(s)
    import DataTable from 'primevue/datatable';
    import Column from 'primevue/column';
    import ColumnGroup from 'primevue/columngroup';   // optional
    import Row from 'primevue/row';                   // optional
    
    import { FilterMatchMode, FilterOperator } from 'primevue/api';     
    // end primevue 

    //import { ProductService } from '@/service/ProductService';

    const EditCategory = defineAsyncComponent(() => import('../components/EditCategory.vue'));
    //const DialogFooter = defineAsyncComponent(() => import('../components/DialogFooter.vue'));

    export default {
        name: "CategoriesView",      
        data: () => {
            return {
                message: null,                
                categories: [],
                //products: [],
                filters: null,
                columns: null,                
                loading: true,
                loaded: false,
                selectedRecords: null,           
                visible: false,
                //isLogged: (userStore.userIsAuth === true) ? true : false,            
                //loggingOut: false,               
            }
        },
        created() {
          this.columns = [
            { field: 'id', header: 'Id' },
            { field: 'name', header: 'Name' }            
          ];          
          this.initFilters();
        },        
        computed: {
          ...mapState(useGeneralStore, [
              'API_URL',
            ]),
            // gives access to this.categories inside the component
            // same as reading from store.categories
            //...mapState(useCategoryStore, ['categories']),
            // same as above but registers it as this.categoriesFromStore
            ...mapState(useCategoryStore, {
                categoriesFromStore: 'categories',
                // a function that gets access to the store
                //myCategoriesFromStore: (store) => store.categories,
          }),
          ...mapState(useCategoryStore, [
              'getMessage'
          ]),                    
          ...mapState(useCategoryStore, [
              'getRecords'
          ])
        }, // computed
        watch: {
        }, // watch
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

            /* // vue router
            // object
            router.push({ path: 'home' })
            // named route
            router.push({ name: 'user', params: { userId: '123' } })
            // with query, resulting in /register?plan=private
            router.push({ path: 'register', query: { plan: 'private' } })
            // with hash, resulting in /about#team
            router.push({ path: '/about', hash: '#team' })
            */
           
          },          
          // get categories from Category store, in fetchRecords action method
          ...mapActions(useCategoryStore, [
              'fetchRecords'
          ]),

          // get categories from categoriesAll            
          categoriesAll() {
            const userStore = useUserStore();
            const token = userStore.token;

            this.loading = true;

            axios.get(`${this.API_URL}category`, {
              headers: {
                Authorization: `Bearer ${token}`,
              },
            })
            .then(response => {
                this.categories = response.data;
                //console.log('categoriesAll() this.categories # ' + JSON.stringify(this.categories));

                this.loaded = true;
                this.loading = false;
            }).catch(error => {
                //console.log(error);
                if (error.response != null)
                  this.message = error.response.data.message;
                  if (this.message == 'Expired JWT Token')
                  {
                    this.logUserOut();
                  }
            }).finally(() => {                   
                // if an error is catched or not this always will run
            });
          }, // categories all
          /*
          // async get categories from categoriesAll            
          async asyncCategoriesAll() {
            const userStore = useUserStore();
            const token = userStore.token;
        
            await axios.get(`${this.API_URL}category`,{
              headers: {
                Authorization: `Bearer ${token}`,
              },
            })
            .then(response => {
                this.categories = response.data;
                console.log('this.categories # ' + JSON.stringify(this.categories));

            }).catch(error => {
                //console.log(error);
                if (error.response != null)                    
                  this.message = error.response.data.message;
            }).then(() => {                   
                // after error catched 
            });
          },
          */
          /* async without catch error
          async asyncCategoriesAll() {
            const userStore = useUserStore();
            const token = userStore.token;
            
            const request = await axios.get(`${this.API_URL}category`, {
              headers: {
                Authorization: `Bearer ${token}`,
              },
            });

            this.categories = request.data;
          },
            */
          
          /*
          // get async all categories from asyncgetRecords
          async asyncgetRecords() {
            try {
              const response = await axios.get(`${this.API_URL}category`);
              this.categories = response.data;
            } catch (error) {
              console.log(error);
            }
          },
          */

          clearFilter() {
            this.initFilters();
          },
          initFilters() {
            this.filters = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                price: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                
                /* // other examples of filters
                'country.name': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                representative: { value: null, matchMode: FilterMatchMode.IN },
                date: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                balance: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                status: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                activity: { value: [0, 100], matchMode: FilterMatchMode.BETWEEN },
                verified: { value: null, matchMode: FilterMatchMode.EQUALS }
                */
            };
          },
          exportCSV() {
            this.$refs.dt.exportCSV();
          },
          editRecord(data, isEdit) {
            // Add or Edit on dynamic modal dialog           
            const isEditCheck = isEdit ? 'Edit' : 'Add';
            
            // edit record
            const dialogRef = this.$dialog.open(EditCategory, {
                props: {
                    header: isEditCheck,
                    style: {
                      width: '50vw'
                    },                    
                    breakpoints: {
                      '960px': '75vw',
                      '640px': '90vw'
                    },                    
                    modal: true
                },
                data: {
                  // data for dynamic modal dialog
                  dataId: data ? data.id : null,
                  dataName: data ? data.name : null,
                  dataPrice: data ? data.price : null,
                  dataAll: data ? data : null,
                  isEditCheck: isEdit,
                },
                templates: {                  
                  //footer: markRaw(DialogFooter)
                },
                onClose: (options) => {
                  const data = options.data;                  

                  if (data) {
                      const buttonType = data.buttonType;
                      
                      const summary_and_detail = buttonType ? { summary: 'Record was not saved.', detail: `Pressed '${buttonType}' button` } : { summary: 'Record saved', detail: `Record was ${isEdit? 'updated' : 'added'}.` };
                      //{ summary: 'Record saved', detail: `Record with name '${data.name}' was saved.` };
                      this.$toast.add({ severity: 'info', ...summary_and_detail, life: 3000 });
                      
                      /* // buttonType is defined when DialogFooter is enabled (loaded), else use isEdit for custom code
                      //if (buttonType=='Save') {

                      if (isEdit) {
                        console.log('from parent - buttonType # ' + buttonType);
                        console.log('from parent - id # ' + JSON.stringify(data));

                        if (data.id != null) {
                          console.log('update form #');
                        }
                      }
                      else 
                      {
                        console.log('from parent - buttonType # ' + buttonType);
                         console.log('add form #');
                      }
                      */
                     
                      this.categories = this.categoriesAll();
                  }
                },               
            });
          }, // add or edit record

          /*
          confirm1() {
            this.$confirm.require({
                message: 'Are you sure you want to proceed?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: () => {
                  this.$toast.add({ severity: 'info', summary: 'Confirmed', detail: 'You have accepted', life: 3000 });

                  //alert('confirm1 edit');
                },
                reject: () => {
                  this.$toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
                }
            });
          },
          */
          deleteRecord(id) {
            const userStore = useUserStore();
            const token = userStore.token;
            const deleteAPI = `${this.API_URL}category/${id}`;

            axios.delete(deleteAPI, {
              headers: {
                Authorization: `Bearer ${token}`,                        
              },
            })
            .then(response => {
                // data was deleted
                // response.data
                //console.log('record deleted with id # ' + id);

                // after delete a record refresh (reload) data table
                this.categories = this.categoriesAll();
            })
            .catch(error => {
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
                    console.log('Error # ' + error);
                    this.message = error.response;
                }

                if (error.request!= null) {                    
                    console.log('Error request # ' + error);
                    this.message = error;
                }
            })
            .finally(() => {                   
                // if an error is catched or not this always will run                  
            });
          }, // delete record
          deleteRecordConfirm(id) {
              this.$confirm.require({
                  message: 'Do you want to delete this record?',
                  header: 'Delete confirmation',
                  icon: 'pi pi-info-circle',
                  acceptClass: 'p-button-danger',
                  accept: () => {
                    // delete record by id
                    this.deleteRecord(id);                    
                    this.$toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Record deleted.', life: 3000 });
                  },
                  reject: () => {
                    this.$toast.add({ severity: 'error', summary: 'Canceled', detail: 'You have canceled deletion.', life: 3000 });
                  }
              });
          }, // delete record
          deleteSelectedRecords() {
            //console.log('selectedRecords # ' + JSON.stringify(this.selectedRecords, null, 2));
            if (this.selectedRecords!= null) {
              this.selectedRecords.forEach(element => {
                // delete record by id
                this.deleteRecord(element.id);
              });
              this.selectedRecords = null;
            }
          }, // deleteSelectedRecords
          deleteSelectedRecordsConfirm()
          {
            this.$confirm.require({
              message: 'Do you want to delete selected records?',
              header: 'Delete confirmation',
              icon: 'pi pi-info-circle',
              acceptClass: 'p-button-danger',
              accept: () => {
                this.deleteSelectedRecords();                
                this.$toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Records deleted.', life: 3000 });
              },
              reject: () => {
                this.$toast.add({ severity: 'error', summary: 'Canceled', detail: 'You have canceled deletion.', life: 3000 });
              }
            });
          }, // deleteSelectedRecordsConfirm

        },  // methods

        /*
        // on page load beforeMount
        beforeMount() {
          this.categories = this.fetchRecords();
          this.message = this.getMessage;
          console.log('beforeMount #');          
        },
        */
        
        mounted() {          
          // load categories from current script
          this.categories = this.categoriesAll();

          //this.categories = this.categoriesFromStore;
          //console.log(' this.categoriesFromStore' + this.categoriesFromStore);
          //this.loading = false;          
          //this.loaded = true;

          // load categories from category store
          //const store = useCategoryStore();          
          //store.fetchRecords();
          //this.categories = store.categories;
          
          //console.log(' categories from store # ' + JSON.stringify(this.getRecords));

          if (this.message !=null)  {
            this.message = this.getMessage; 
            //console.log(this.message);
          }
          /*
          if (store.message != null)
            this.message = store.message;
          else
            this.message = this.getMessage;
          */

          /*          
          ProductService.getProductsMini().then((data) => { 
            this.products = data;
            this.loading = false;
            this.loaded = true;
          });
          */
         // //ProductService.getProductsSmall().then(data => this.products = data.slice(0,5));

        }, // mounted
             
        /* Options: Lifecycle - https://vuejs.org/api/options-lifecycle.html        
        beforeCreate, created, 
        beforeMount, mounted, 
        beforeUpdate, updated, 
        beforeUnmount, unmounted, 
        errorCaptured, 
        renderTracked, renderTriggered, 
        activated, deactivated, 
        serverPrefetch
        */
    }
</script>