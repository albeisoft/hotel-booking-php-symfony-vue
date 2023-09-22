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
      <h3>Reservations</h3>
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
      :globalFilterFields="['room_id', 'client_id', 'date', 'no_days', 'no_persons']" 
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
      <Column field="room_name" header="Room" sortable style="min-width: 5rem">
        <template #body="{ data }">
            {{ data.room_name }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by room name" />
        </template>
      </Column>
      <Column field="client_name" header="Client" sortable style="min-width: 5rem">
        <template #body="{ data }">
            {{ data.client_name }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by client name" />
        </template>
      </Column>
      <Column field="date" header="From date" sortable style="min-width: 5rem">
        <template #body="{ data }">          
            {{ moment(data.date).format('DD-MM-YYYY') }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by date (from date)" />
        </template>
      </Column>
       <Column field="no_days" header="No days (To date)" sortable style="min-width: 5rem">
        <template #body="{ data }">
            {{ data.no_days == 1 ? data.no_days + ' day ' : data.no_days + ' days ' }} 
            ({{ moment(data.date).add(data.no_days, 'days').format('DD-MM-YYYY') }})
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by no days (to date)" />
        </template>
      </Column>
       <Column field="no_persons" header="No persons" sortable style="min-width: 10rem">
        <template #body="{ data }">
            {{ data.no_persons }}
        </template>
        <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Search by no persons" />
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
  <Dialog v-model:visible="addOrEditDialog" :style="{width: '570px'}" header="Reservation" :modal="true" class="p-fluid">
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
                      <label>Room</label>
                      <Field v-model="form.room_id" name="room_id" as="select" class="form-select" :class="{ 'is-invalid': errors.room_id }" 
                      aria-describedby="text-error">
                        <option value="">Select</option>
                        <!-- <option v-for="(room, roomIndex) in rooms" :key="room.id" :value="room.id">{{ room.name }}</option> -->
                        <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                      </Field>
                      &nbsp;<small class="p-error">{{errors.room_id}}</small>
                    </div>
                    <div class="col">
                      <label>Client</label>
                      <Field v-model="form.client_id" name="client_id" as="select" class="form-select" :class="{ 'is-invalid': errors.client_id }" 
                      aria-describedby="text-error">
                        <option value="">Select</option>                        
                        <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                      </Field>
                      &nbsp;<small class="p-error">{{errors.client_id}}</small>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <label>Date</label>                     
                      <Field v-model="form.date" name="date" type="date" class="form-control" :class="{ 'is-invalid': errors.date }" 
                      aria-describedby="text-error" />
                      &nbsp;<small class="p-error">{{errors.date}}</small>
                    </div>               
                    <div class="col">
                      <label>No days</label>
                      <Field v-model="form.no_days" name="no_days" type="text" class="form-control" :class="{ 'is-invalid': errors.no_days }" 
                      aria-describedby="text-error" />
                      &nbsp;<small class="p-error">{{errors.no_days}}</small>
                    </div>
                     <div class="col">
                      <label>No persons</label>
                      <Field v-model="form.no_persons" name="no_persons" type="text" class="form-control" :class="{ 'is-invalid': errors.no_persons }" 
                      aria-describedby="text-error" />
                      &nbsp;<small class="p-error">{{errors.no_persons}}</small>
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
  import useReservationStore from '../store/reservation';
  import useRoomStore from '../store/room';
  import useClientStore from '../store/client';
        
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
  //import { object, string, number, date, InferType } from 'yup';  

  import moment from 'moment';

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

  const store = useReservationStore();  
  const userStore = useUserStore();  
  const roomStore = useRoomStore();
  const clientStore = useClientStore();

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
      // fetch (get) reservations
      fetchRecords();

      // fetch (get) rooms
      roomsFetchRecords(); 
      
      // fetch (get) clients
      clientsFetchRecords(); 
      
  }); // mounted

  const fetchRecords = () => {    
    store.fetchRecords();    

    //message.value = storeMessage;
    message.value = messageWithDelay(store.message, 1);    
    loading.value = false;
    loaded.value = true;    
  }  

  const roomsFetchRecords = () => {    
    roomStore.fetchRecords();
  }

  const clientsFetchRecords = () => {    
    clientStore.fetchRecords();
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
  
  // get records - made by action
  const records = computed(() => {
    return store.records;
  });

   // get rooms for form dropdown list (ddl) menu
  const rooms = computed(() => {    
    return roomStore.records;    
  });

  // get clients for form dropdown list (ddl) menu
  const clients = computed(() => {    
    return clientStore.records;
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
      room_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
      client_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
      //date: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
      date: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
      no_days: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
      no_persons: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
    };
  };
  
  initFilters();

  // clear filter function  
  const clearFilter = () => {
      initFilters();
  };
  
  const validateDateRange = (obj) => {
  const { start, end } = obj;
  if (!start || !end) {
    return true; // don't validate if either field is missing
  }
  return moment(end).isSameOrAfter(start); // custom validation logic
};

  const today = moment().format("YYYY-MM-DD");  
  //console.log('YYYY-MM-DD # ' + moment().format('YYYY-MM-DD'));
  //console.log('YYYY-MM-DD minus 1 # ' + moment(today).add(-1, 'days').format('YYYY-MM-DD'));   

  // form validation schema
  const schema = yup.object().shape({
    room_id: yup.number()
      .required('Room is required.')
      .positive()
      .integer(),
    client_id: yup.number()
      .required('Client is required.')
      .positive()
      .integer(),      
    date: yup.date()
      .required('Date is required.')      
      //.default(() => new Date())
      //.min(today)
      //.min(new Date(),'Date must be in future after ' + moment(today).format('DD-MM-YYYY'))
      //.min(today, 'Date must be after ' + moment(today).format('DD-MM-YYYY'))
      //.max(today, 'Date must be at earlier than ' + moment(today).format('DD-MM-YYYY'))
      //.min(moment(today).add(-1, 'days').format('YYYY-MM-DD'), 'Date must be after ' + moment(today).add(-1, 'days').format('DD-MM-YYYY'))
     
      /* * /
      .test(
        "dateIsGrater",
        "Date cannot be greater than today's date",
        (value) => {
          if (!value) return true;
          return moment(today).diff(value) > 0;
        }
      )
      /* */

      .typeError("Invalid date!"),

    /* * /
    startDate: yup.date().min(new Date(),'Please choose future date'),    
    endDate: yup.date()    
      .when('startDate',
        (startDate, schema) => {
          if (startDate) {
          const dayAfter = new Date(startDate.getTime() + 86400000);          
              return schema.min(dayAfter, 'End date has to be after than start date');
            }          
          return schema;            
      }),
    /* */

    /* * /
    startDate: Yup.date().required(),
    endDate: Yup.date().test(
      "same_dates_test",
      "Start and end dates must not be equal.",
      function (value) {
        const { startDate } = this.parent;
        return value.getTime() !== startDate.getTime();
      }
    ),
    /* */

    /* * /
    start: yup.date().required(),
    end: yup.date().test('date-range', 'End date must be after start date', validateDateRange),
    /* */

    no_days: yup.number()
      .required('No days is required.')
      .positive()
      .integer(),
    no_persons: yup.number()
      .required('No persons is required.')
      .positive()
      .integer(), 
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

        store.record.date = moment(store.record.date).format('YYYY-MM-DD');
        form.value = store.record;        

        //console.log('after delay editRecord form #' + JSON.stringify(form.value, null, 2));
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
        room_id: data.room_id,
        client_id: data.client_id,
        date: data.date,
        no_days: data.no_days,
        no_persons: data.no_persons        
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
      /*
      form.value = {
        id: form.value.id,
        room_id: form.value.room_id,
        client_id: form.value.client_id,        
        date: form.value.date,
        no_days: form.value.no_days,
        no_persons: form.value.no_persons,
      }      
      */
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