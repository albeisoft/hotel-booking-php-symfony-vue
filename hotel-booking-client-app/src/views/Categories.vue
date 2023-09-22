<style>
  .message {
    color: #FF0000;
  }
  
@media (min-width: 1024px) {
  .mobileClients {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>

<template>
  <div class="card">
    <div>
      <span class="message">{{message}}</span>
      <h3>Categories</h3>
    </div>
    <br />    
    <Toast />

    <Toolbar class="mb-4">
      <template #start>
        <Button label="Add" icon="pi pi-plus" severity="success" class="mr-2" @click="openDialog(0)" />&nbsp;
        <Button label="Delete selected records" icon="pi pi-trash" severity="danger" @click="openConfirmDeleteSelectedRecordsDialog()" 
        :disabled="!selectedRecords || !selectedRecords.length" />
      </template>

      <template #end>
          <!-- <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" chooseLabel="Import" class="mr-2 inline-block" />&nbsp; -->
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

      <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
      <Column field="name" header="Name" sortable style="min-width: 12rem">
        <template #body="{ data }">
            {{ data.name }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by name" />
        </template>
      </Column>
      <Column field="price" header="Price" sortable style="min-width: 12rem">
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
            <Button label="Edit" icon="pi pi-pencil" @click="openDialog(1, data)" />&nbsp;                
            <Button label="Delete" icon="pi pi-trash" severity="danger" @click="openConfirmDeleteRecordDialog(data.id, data.name)" />
          </div>
        </template>
      </Column>

      <template #footer> There {{(categories.length > 1 || categories.length == 0) ? 'are' : 'is' }} {{ categories ? categories.length : 0 }} {{(categories.length > 1 || categories.length == 0 )? 'records' : 'record' }}. </template>
    </DataTable>  
  </div>
  
  <Toast />
  <Dialog v-model:visible="addOrEditDialog" :style="{width: '450px'}" header="Category" :modal="true" class="p-fluid">
    <div class="card m-1">
        <div>
            <span class="message">{{message}}</span>
        </div>
        <div class="card-body">            
            <Form id="myForm" @submit="editData()" :validation-schema="schema" v-slot="{ errors }">
                <div class="row">
                    <div>
                        <Field v-model="form.id" name="id" type="hidden" class="form-control" />
                    </div>
                    <!--
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="name" placeholder="Name">
                      <label for="name">Name</label>
                    </div>
                    -->
                    <div class="col-5">
                        <label>Name</label>
                        <!-- <Field v-model="form.name.trim" autofocus name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }" /> -->
                        <Field v-model="form.name" name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }" />
                        
                        <!-- <div class="p-error">{{errors.name}}</div> -->                        
                        <!-- <small class="p-error" v-if="!form.name">Name is required.</small> -->
                        &nbsp;<small class="p-error">{{errors.name}}</small>
                    </div>
                    <div class="col-5">                        
                        <label>Price</label>
                        <Field v-model="form.price" name="price" type="text" class="form-control" :class="{ 'is-invalid': errors.price }" 
                        aria-describedby="text-error" />

                        <!-- <div class="p-error">{{errors.price}}</div> -->
                        <!-- <small class="p-error" v-if="!form.price">Price is required.</small> -->
                        &nbsp;<small class="p-error">{{errors.price}}</small>
                    </div>
                </div>
                <!--
                  < !-- <div class="form-group"> -- >
                  <div class="w-full flex justify-between">
                    <Button type="button" label="Close" @click="hideDialog" />&nbsp;
                    < !-- <Button type="submit" label="Save" :disabled="!(form.name && (form.price && form.price>=10))" /> -- >
                    <Button type="submit" label="Save" />
                  </div>
                -->
            </Form>
        </div>
    </div>
    
    <template #footer>
        <Button label="Close" icon="pi pi-times" text @click="hideDialog"/>
        <!-- <Button type="submit" form="myForm" label="Save" icon="pi pi-check" /> -->
        <Button label="Save" type="submit" form="myForm" icon="pi pi-check" text />
    </template>
  </Dialog>

  <Dialog v-model:visible="deleteRecordDialog" :style="{width: '450px'}" header="Confirm" :modal="true">
    <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span v-if="recordId">Are you sure you want to delete <b>{{recordName}}</b>?</span>
    </div>
    <template #footer>
        <!-- <Button label="No" icon="pi pi-times" text @click="deleteRecordDialog = false"/> -->
        <Button label="No" icon="pi pi-times" text @click="hideConfirmDeleteRecordDialog"/>        
        <Button label="Yes" icon="pi pi-check" text @click="deleteRecord(recordId)" />
    </template>
  </Dialog>

  <Dialog v-model:visible="deleteSelectedRecordsDialog" :style="{width: '450px'}" header="Confirm" :modal="true">
    <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <!-- <span v-if="selectedRecords">Are you sure you want to delete the selected records?</span> -->
        Are you sure you want to delete the selected records?
    </div>
    <template #footer>
        <Button label="No" icon="pi pi-times" text @click="deleteSelectedRecordsDialog = false"/>
        <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedRecords" />
    </template>
  </Dialog>	

</template>

<script setup>
  // for this project is used Composition API

  import { ref, onMounted, computed } from "vue";
  import { markRaw, defineAsyncComponent } from 'vue';
  
  import useGeneralStore from '../store/general';
  import useUserStore from '../store/user';
  import useCategoryStore from '../store/category';
        
  import axios from 'axios';
      
  // primevue import(s)
  //import { ProductService } from '@/service/ProductService';
  //import { CustomerService } from '@/service/CustomerService';

  import DataTable from 'primevue/datatable';
  import Column from 'primevue/column';
  import ColumnGroup from 'primevue/columngroup';   // optional
  import Row from 'primevue/row';                   // optional 
 
  import { FilterMatchMode, FilterOperator } from 'primevue/api';
     
  //import { useDialog } from 'primevue/usedialog';
  import { useToast } from 'primevue/usetoast';
  
  // end primevue 
  
  //import { Form, Field, ErrorMessage } from 'vee-validate';

  import { Form, Field } from 'vee-validate';
  // vee-validate’s entire core size is very small, but the same can’t be said about the validators you import. 
  // next line of code import everything yup has to offer
  import * as yup from 'yup';
  // only import what you need from yup
  //import { object, string } from 'yup';  
  
  // var declaration  
    
  //const dialog = useDialog();
  const toast = useToast();

  const message = ref();
  const dt = ref();
  const addOrEditDialog = ref(false);  
  const deleteRecordDialog = ref(false);
  const deleteSelectedRecordsDialog = ref(false);  
  const recordId = ref();
  const recordName = ref();
  const selectedRecords = ref();
  const form = ref({});  
  const isEditCheckOk = ref();

  const store = useCategoryStore();
  const userStore = useUserStore();

  const loading = ref(true);
  const loaded = ref(false);

  const messageWithDelay = (messageDelay, isStoreMessage) => {
    // set delay till message is loaded from store - Delay in miliseconds: 3000 = 3 seconds 
    setTimeout(() => {
      if (isStoreMessage)
        message.value = store.message;
      else
        message.value = messageDelay;
    }, 3000);
  };

  // get (load) data from API in pinia store
  onMounted(() => {       
      fetchRecords();     
  }); // mounted

  const fetchRecords = () => {    
    store.fetchRecords();
    message.value = messageWithDelay(store.message, 1);

    loading.value = false;
    loaded.value = true;    
  }

  // compute, map to pinia store getters, actions

  const API_URL = computed(() => {
    return store.state.API_URL;
  });

  /*
  //const message = ref("Some message");
  const message = computed(() => {
    return store.getMessage;
  });
  */

  // get records from store from getters by: getRecords
  const getRecords = computed(() => {
    return store.getRecords;
  });

  // get categories - made by action
  const categories = computed(() => {
    return store.categories;
  });

  /*
  const loggingOut = ref(false); 
  const logUserOut = () => {              
    userStore.logoutUser();              
    loggingOut = true;
    
    // refresh current page
    //windows.history.go();
    //location.reload(); - in some browsers can't reload
    //window.location.reload();
    // vue router reload current page - if you use TypeScript
    //this.$router.go(0);
    this.$router.go();
  }
  */

  /*
  const filters = ref({
      'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
  });
  */
  
  const filters = ref();
  /*
  const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    price: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    / *
    'country.name': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    representative: { value: null, matchMode: FilterMatchMode.IN },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
    verified: { value: null, matchMode: FilterMatchMode.EQUALS }
    * /
  });
  */

  const initFilters = () => {
    filters.value = {
      global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
      price: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
      /*
      'country.name': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        representative: { value: null, matchMode: FilterMatchMode.IN },
        date: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
        balance: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        status: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        activity: { value: [0, 100], matchMode: FilterMatchMode.BETWEEN },
        verified: { value: null, matchMode: FilterMatchMode.EQUALS }
      */
    };
  };
  
  initFilters();

  // clear filter function  
  const clearFilter = () => {
      initFilters();
  };

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

    //rating: yup.number().min(1).max(10).required(),
    //date: yup.date().default(() => new Date()),
    //isCheck: yup.boolean().default(false),
  });

  // open (show) dialog modal window for add or edit record
  const openDialog = (isEditCheck, data) => {

    addOrEditDialog.value = true;

    // isEditCheckOk.value is need for other checking in other methods for add or edit procedures
    isEditCheckOk.value = isEditCheck;

    if (isEditCheckOk.value) {      
      form.value = {
        id: data.id,
        name: data.name,
        price: data.price,        
      };
    }
    else {
      form.value = {};
    }
  };
  // hide dialog modal window for add or edit record 
  const hideDialog = () => {    
    addOrEditDialog.value = false;    
  };

  /*
  const validate  = (field) => {  
    schema
    .validateAt(field, values)
    .catch(err => {
      console.log(err);
      /*
      {
        errors: ["Name is a required field"],
        inner: [],
        message: "Name is a required field",
        name: "ValidationError",
        params: {path: "name", value: "", originalValue: "", label: undefined},
        path: "name",
        type: "required",
        value: "",
        // ..
      }
      * /
    });
  }
  */

  const editData = () => {
    try {      
      // save data to database      

      store.editRecord(isEditCheckOk.value, form.value);
      message.value = messageWithDelay(store.message, 1);
      hideDialog();      

      if (isEditCheckOk.value)
      {
        // toast severity can have values: success, info, warn, error
        toast.add({severity:'success', summary: 'Record status', detail: 'Record updated.', life: 3000});
      }
      else {
        toast.add({severity:'success', summary: 'Record status', detail: 'Record added.', life: 3000});
      }
    }
    catch (error) {      
        const userStore = useUserStore();
        if (error.response != null) {
          if (error.response.statusText == "Unauthorized" || error.response.data.message == 'Expired JWT Token') 
          {
            userStore.logoutUser();

            loggingOut = true;
            window.location.reload();
          }

          message =
            "Error: " +
            error.response.statusText +
            " status " +
            error.response.status;

          console.log(
            "Error: " +
              error.response.statusText +
              " status " +
              error.response.status
          );
        } 
        /* 
        else if (error.message != null) {
          message = "Error: " + error.message;
          console.log("Error message: " + error.message);
        } */
        else {
          message = "Error: " + error;
          console.log("Error: " + error);
        }
      } // catch    
  }; // editData

  const exportCSV = () => {
    dt.value.exportCSV();
  };

  // show dialog for delete record
  const openConfirmDeleteRecordDialog = (id, name) => {
    recordId.value = id;
    recordName.value = name;
    deleteRecordDialog.value = true;
  };

  // hide dialog
  const hideConfirmDeleteRecordDialog = () => {
    deleteRecordDialog.value = false;
  };

  const deleteRecord = (id) => {
    store.deleteRecord(id);
    message.value = messageWithDelay(store.message, 1);  
    deleteRecordDialog.value = false;
     
    toast.add({severity:'success', summary: 'Record status', detail: 'Record deleted.', life: 3000});
  }

  // show dialog for delete selected records
  const openConfirmDeleteSelectedRecordsDialog = () => {
    deleteSelectedRecordsDialog.value = true;
  };

  // hide dialog
  const hideConfirmDeleteSelectedRecordsDialog = () => {
    deleteSelectedRecordsDialog.value = false;
  };

  // delete seleted records
  const deleteSelectedRecords = () => {
    store.deleteSelectedRecords(selectedRecords.value);
    message.value = messageWithDelay(store.message, 1);
    selectedRecords.value = null;
    deleteSelectedRecordsDialog.value = false;        
    
    toast.add({severity:'success', summary: 'Record status', detail: 'Selected records deleted.', life: 3000});
  }
  
  /*
  const getCustomers = (data) => {
    return [...(data || [])].map((d) => {
        d.date = new Date(d.date);

        return d;
    });
  };
  */

</script>