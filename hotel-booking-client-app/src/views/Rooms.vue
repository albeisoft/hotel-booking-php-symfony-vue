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
      <h3>Rooms</h3>
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
          <Button label="Export" icon="pi pi-download" severity="help" @click="exportCSV($event)" />
      </template>
    </Toolbar>

    <DataTable ref="dt" 
      v-if="loaded" 
      :loading="loading" 
      :value="records" 
      dataKey="id" 
      v-model:selection="selectedRecords" 
      v-model:filters="filters"             
      filterDisplay="menu" 
      :globalFilterFields="['name', 'category_id', 'category_id', 'is_view', 'floor', 'no_places']" 
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
      <Column field="name" header="Name" sortable style="min-width: 5rem">
        <template #body="{ data }">
            {{ data.name }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by name" />
        </template>
      </Column>
      <Column field="category_name" header="Category" sortable style="min-width: 5rem">
        <template #body="{ data }">
            {{ data.category_name }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by category name" />
        </template>
      </Column>
      <Column field="is_view" header="Is view" sortable style="min-width: 8rem">
        <template #body="{ data }">
            <!-- {{ data.is_view ? 'Yes' : 'No' }} -->
            <span v-if="data.is_view"><i class="pi pi-check" style="color: green; font-size: 1rem"></i></span>
            <span v-if="!data.is_view"><i class="pi pi-times" style="color: red; font-size: 1rem"></i></span>
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by is view" />
        </template>
      </Column>
       <Column field="floor" header="Floor" sortable style="min-width: 5rem">
        <template #body="{ data }">
            {{ data.floor }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by floor" />
        </template>
      </Column>
       <Column field="no_places" header="No places" sortable style="min-width: 10rem">
        <template #body="{ data }">
            {{ data.no_places }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by no places" />
        </template>
      </Column>

      <Column :exportable="false" header="Action" style="min-width: 8rem">
        <template #body="{ data }">
          <!-- <div class="justify-content-center"> -->
          <div class="flex justify-content-center flex-wrap gap-3">          
            <Button label="Edit" icon="pi pi-pencil" @click="openDialog(1, data.id)" />&nbsp;                
            <Button label="Delete" icon="pi pi-trash" severity="danger" @click="openConfirmDeleteRecordDialog(data.id, data.name)" />
          </div>
        </template>
      </Column>

      <template #footer> There {{(records.length > 1 || records.length == 0) ? 'are' : 'is' }} {{ records ? records.length : 0 }} {{(records.length > 1 || records.length == 0 )? 'records' : 'record' }}. </template>
    </DataTable>  
  </div>
  
  <Toast />
  <Dialog v-model:visible="addOrEditDialog" :style="{width: '450px'}" header="Room" :modal="true" class="p-fluid">
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
                    <div class="col">
                      <label>Name</label>                        
                      <Field v-model="form.name" name="name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }" />                       
                      &nbsp;<small class="p-error">{{errors.name}}</small>                      
                    </div>
                    <div class="col">
                      <label>Category</label>
                      <Field v-model="form.category_id" name="category_id" as="select" class="form-select" :class="{ 'is-invalid': errors.category_id }" 
                      aria-describedby="text-error">
                        <option value="">Select</option>
                        <!-- <option v-for="(category, categoryIndex) in categories" :key="category.id" :value="category.id">{{ category.name }}</option> -->
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                      </Field>
                      &nbsp;<small class="p-error">{{errors.category_id}}</small>
                    </div>                    
                  </div>
                  <div class="row">                    
                    <div class="col">
                      <label>Floor</label>
                      <Field v-model="form.floor" name="floor" type="text" class="form-control" :class="{ 'is-invalid': errors.floor }" 
                      aria-describedby="text-error" />
                      &nbsp;<small class="p-error">{{errors.floor}}</small>
                    </div>
                    <div class="col">
                      <label>No places</label>
                      <Field v-model="form.no_places" name="no_places" type="text" class="form-control" :class="{ 'is-invalid': errors.no_places }" 
                      aria-describedby="text-error" />
                      &nbsp;<small class="p-error">{{errors.no_places}}</small>
                    </div>
                    <div class="col-3">                     
                      <label>Is view</label>
                      <!-- 
                      <Field v-model="form.is_view" name="is_view" type="checkbox" :value="form.is_view" :unchecked-value="false" 
                      -->
                      <!-- Vee Validation Field of type checkbox bug - don't set value (false or true checked or unchecked) -->
                      <!-- <Field v-model="form.is_view" name="is_view" type="checkbox" :value="form.is_view" 
                      class="form-check-input" :class="{ 'is-invalid': errors.is_view }" aria-describedby="text-error" id="is_wiew" />
                      -->                      
                      
                      <input v-model="form.is_view" name="is_view" type="checkbox" :value="form.is_view"
                      class="form-check-input" :class="{ 'is-invalid': errors.is_view }" aria-describedby="text-error" />

                      <!-- &nbsp; <small class="p-error" v-if="form.is_view!=null">Is view is required.</small> -->
                      &nbsp;<small class="p-error">{{errors.is_view}}</small>
                    </div>
                  </div>
                   <div class="row">                    
                     <div class="col-15">                      
                      <label>Note</label>
                      <!-- 
                        <Field v-model="form.note" name="note" id="note" as="textarea" rows="2" cols="7" class="form-textarea" :class="{ 'is-invalid': errors.note }" 
                      aria-describedby="text-error" />
                      -->                      
                      <Field v-model="form.note" name="note" id="note" as="textarea" :value="form.note" rows="2" class="form-textarea" :class="{ 'is-invalid': errors.note }" 
                      aria-describedby="text-error" />
                      &nbsp;<small class="p-error">{{errors.note}}</small>
                    </div>
                </div>           
            </Form>
        </div>
    </div>
    
    <template #footer>
        <Button label="Close" icon="pi pi-times" text @click="hideDialog"/>        
        <Button label="Save" type="submit" form="myForm" icon="pi pi-check" text />
    </template>
  </Dialog>

  <Dialog v-model:visible="deleteRecordDialog" :style="{width: '450px'}" header="Confirm" :modal="true">
    <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span v-if="recordId">Are you sure you want to delete <b>{{recordName}}</b>?</span>
    </div>
    <template #footer>        
        <Button label="No" icon="pi pi-times" text @click="hideConfirmDeleteRecordDialog"/>        
        <Button label="Yes" icon="pi pi-check" text @click="deleteRecord(recordId)" />
    </template>
  </Dialog>

  <Dialog v-model:visible="deleteSelectedRecordsDialog" :style="{width: '450px'}" header="Confirm" :modal="true">
    <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />        
        Are you sure you want to delete the selected records?
    </div>
    <template #footer>
        <Button label="No" icon="pi pi-times" text @click="deleteSelectedRecordsDialog = false"/>
        <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedRecords" />
    </template>
  </Dialog>	

</template>

<script setup>
  import { ref, onMounted, computed } from "vue";  
  
  import useGeneralStore from '../store/general';
  import useUserStore from '../store/user';
  import useRoomStore from '../store/room';
  import useCategoryStore from '../store/category';
        
  import axios from 'axios';
  
  import DataTable from 'primevue/datatable';
  import Column from 'primevue/column';
  import ColumnGroup from 'primevue/columngroup';   // optional
  import Row from 'primevue/row';                   // optional 
 
  import { FilterMatchMode, FilterOperator } from 'primevue/api';
     
  //import { useDialog } from 'primevue/usedialog';
  import { useToast } from 'primevue/usetoast';
  
  // end primevue
  
  import { Form, Field } from 'vee-validate';
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
  const record = ref();  
  const selectedRecords = ref();
  const form = ref({});  
  const isEditCheckOk = ref();

  const store = useRoomStore();
  const userStore = useUserStore();
  const categoryStore = useCategoryStore();

  const loading = ref(true);
  const loaded = ref(false);

  import { storeToRefs } from 'pinia'  
  const { getRecordByIdFind } = storeToRefs(store);

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
      // fetch (get) rooms
      fetchRecords();
      // fetch (get) categories
      categoriesFetchRecords();      
  }); // mounted

  const fetchRecords = () => {    
    store.fetchRecords();

    //message.value = storeMessage;
    message.value = messageWithDelay(store.message, 1);    
    loading.value = false;
    loaded.value = true;    
  }  

  const categoriesFetchRecords = () => {    
    categoryStore.fetchRecords();
    //console.log('store rooms - fetch categories # ' + JSON.stringify(categoryStore.categories, null, 2));  
  }

  /*
  const getRecordById = (id) => {    
    store.getRecordById(id);
    record.value = store.record;
    console.log(' room record # ' + JSON.stringify(record.value, null, 2));
  }
  */

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
  // get categories
  const categories = computed(() => {    
    return categoryStore.categories;
  });

  // get records - made by action
  const records = computed(() => {
    return store.records;
  });

  // get records from store from getters by: getRecords
  const getRecords = computed(() => {
    return store.getRecords;
  });
  
  /*
  // get records from store from getters by: getRecord
  const getRecord = computed(() => {
    return store.getRecord;
  });
  */

  const filters = ref();

  const initFilters = () => {
    filters.value = {
      global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
      category_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
      is_view: { value: null, matchMode: FilterMatchMode.EQUALS },
      floor: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
      no_places: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
      note: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },      
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
    category_id: yup.number()
      .required('Category is required.')
      .positive()
      .integer(),    
    floor: yup.number()
      .required('Floor is required.')
      .positive()
      .integer(),
    no_places: yup.number()
      .required('No places is required.')
      .positive()
      .integer(),
    /* vue validate bug on checkbox Field
    is_view: yup.boolean()
      .required('Is view is required.')
      .default(false),
    */    
    note: yup.string()
      //.required('Note is required.')
      //.notRequired()
      //.min(2, 'Must be at least 2 characters.'),
    
  });
    
  // open (show) dialog modal window for add or edit record
  const openDialog = (isEditCheck, id) => {
    message.value = '';
    addOrEditDialog.value = true;

    // isEditCheckOk.value is need for other checking in other methods for add or edit procedures
    isEditCheckOk.value = isEditCheck;

    if (isEditCheckOk.value) {
      
      store.fetchRecordById(id);
      message.value = messageWithDelay(store.message, 1);
      //console.log('editRecord - form # ' + id + ' # ' + JSON.stringify(store.record, null, 2));      

      // set delay till record by id is loaded from store - Delay in miliseconds: 3000 = 3 seconds 
      setTimeout(() => {
        form.value = {};
        form.value = store.record;       

        //console.log('form #' + JSON.stringify(form.value, null, 2));        
      }, 2000);

      /*
      let recordTest = setTimeout(() => {
        //console.log('let After delay # ' + JSON.stringify(store.record, null, 2));
      }, 3000);      
      */

      //form.value = store.record;
      //form.value = {};
      /*
      form.value = {
        id: data.id,
        name: data.name,
        category_id: data.category_id,
        is_view: data.is_view,
        floor: data.floor,
        no_places: data.no_places,
        note: data.note,
      };
      */
    }
    else {
      form.value = {};
    }

    form.value = {};
  };
  // hide dialog modal window for add or edit record 
  const hideDialog = () => {     
    addOrEditDialog.value = false;    
  };
  
  const editData = () => {
    try {      
      // save data to database     

      //console.log('editData - form.value # ' + JSON.stringify(form.value, null, 2));      
    
      form.value = {
        id: form.value.id,
        name: form.value.name,
        category_id: form.value.category_id,
        is_view: (form.value.is_view == null) ? 0 : form.value.is_view,
        floor: form.value.floor,
        no_places: form.value.no_places,
        note: (form.value.note == null) ? '' : form.value.note,
      }      
      
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

</script>