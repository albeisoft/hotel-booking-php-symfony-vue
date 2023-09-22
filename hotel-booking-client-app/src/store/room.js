import { defineStore } from "pinia";
import useGeneralStore from "./general";
import useUserStore from "./user";

// Import axios to make HTTP requests
import axios from "axios";
import { useRouter, useRoute } from "vue-router";

const useRoomStore = defineStore("room", {
  state: () => ({
    message: "",
    records: [],
    record: null,
    loggingOut: false,
  }),
  getters: {
    getRecords(state) {
      return state.records;
    },
    getRecord(state) {
      return state.record;
    },
    getRecordByIdFind(state) {
      //const records = state.records.filter((record) => record.active);
      //return (id) => records.find((record) => record.id === id);

      return (id) => state.records.find((record) => record.id === id);
    },
    getMessage(state) {
      return state.message;
    },
  },
  actions: {
    async fetchRecords() {
      try {
        const generalStore = useGeneralStore();
        const userStore = useUserStore();
        const token = userStore.token;

        const request = await axios.get(`${generalStore.API_URL}room`, {
          headers: {
            Authorization: `Bearer ${token}`,
            //'Content-Type': 'application/json',
          },
        });

        this.records = request.data;
        /* * /
        console.log(
          "store room - fetchRecords # " + JSON.stringify(this.records, null, 2)
        );
        /* */
      } catch (error) {
        const userStore = useUserStore();
        if (error.response != null) {
          if (error.response.statusText == "Unauthorized") {
            userStore.logoutUser();
            this.loggingOut = true;

            // refresh current page
            //windows.history.go();
            //location.reload(); - in some browsers can't reload
            //window.location.reload();

            window.location.reload();
          }

          this.message =
            "Error: " +
            error.response.statusText +
            " status " +
            error.response.status;

          console.log(
            "Error store: " +
              error.response.statusText +
              " status " +
              error.response.status
          );
        } else {
          /* else if (error.message != null) {
          this.message = "Error: " + error.message;
          console.log("Error message: " + error.message);
        } */
          this.message = "Error: " + error;
          console.log("Error store: " + error);
        }
      }
    }, // fetchRecords
    /* */
    async fetchRecordById(id) {
      const generalStore = useGeneralStore();
      const userStore = useUserStore();
      const token = userStore.token;
      const getAPI = `${generalStore.API_URL}room/${id}`;

      await axios({
        method: "get",
        url: getAPI,
        headers: {
          Authorization: `Bearer ${token}`,
          //'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          // data was fetched
          // response.data
          //id = response.data.id;

          this.record = response.data;

          /*
          console.log(
            "store room - this.record # " + JSON.stringify(this.record)
          );
          */
        })
        .catch((error) => {
          //console.log('error # ' + error);
          if (error.response != null) {
            this.message = error.response.data.message;
            if (this.message == "Expired JWT Token") {
              userStore.logUserOut();
            }
          } else {
            this.message = error.response;
            console.log("Error store: " + error);
          }

          if (error.request != null) {
            this.message = error;
            console.log("Error store request: " + error);
          }
        })
        .finally(() => {
          // if an error is catched or not this always will run
        });
    }, // fetch record by id
    /* */
    async editRecord(isEditCheck, form) {
      const generalStore = useGeneralStore();
      const userStore = useUserStore();
      const token = userStore.token;
      const addAPI = `${generalStore.API_URL}room`;
      const editAPI = `${generalStore.API_URL}room/${form.id}`;

      await axios({
        method: isEditCheck ? "put" : "post",
        url: isEditCheck ? editAPI : addAPI,
        data: form,
        headers: {
          Authorization: `Bearer ${token}`,
          //'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          // data was saved
          // response.data
          //id = response.data.id;

          // refresh records
          this.fetchRecords();
        })
        .catch((error) => {
          //console.log('error # ' + error);
          if (error.response != null) {
            this.message = error.response.data.message;
            if (this.message == "Expired JWT Token") {
              userStore.logUserOut();
            }
          } else {
            this.message = error.response;
            console.log("Error store: " + error);
          }

          if (error.request != null) {
            this.message = error;
            console.log("Error store request: " + error);
          }
        })
        .finally(() => {
          // if an error is catched or not this always will run
        });
    }, // editRecord
    async deleteRecord(id) {
      const generalStore = useGeneralStore();
      const userStore = useUserStore();
      const token = userStore.token;
      const deleteAPI = `${generalStore.API_URL}room/${id}`;

      await axios
        .delete(deleteAPI, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          // data was deleted
          // response.data

          // refresh records
          this.fetchRecords();
        })
        .catch((error) => {
          //console.log('error # ' + error);
          if (error.response != null) {
            this.message = error.response.data.message;
            /*
            if (this.message == "Expired JWT Token") {
              userStore.logUserOut();
            }
            */
          } else {
            this.message = error.response;
            console.log("Error store: " + error);
          }

          if (error.request != null) {
            this.message = error;
            console.log("Error store request: " + error);
          }
        })
        .finally(() => {
          // if an error is catched or not this always will run
        });
    }, // deleteRecord
    deleteSelectedRecords(selectedRecords) {
      try {
        if (selectedRecords != null) {
          selectedRecords.forEach((element) => {
            // delete record by id
            this.deleteRecord(element.id);
          });
        }
      } catch (error) {
        this.message = error;
        console.log("Error store deleteSelectedRecords: " + error);
      }
    }, // deleteSelectedRecords
  }, // actions
});

export default useRoomStore;

//export { useRoomStore };
