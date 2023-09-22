import { defineStore } from "pinia";
import useGeneralStore from "./general";
import useUserStore from "./user";

// Import axios to make HTTP requests
import axios from "axios";
import { useRouter, useRoute } from "vue-router";

const useCategoryStore = defineStore("category", {
  state: () => ({
    message: "",
    categories: [],
    loggingOut: false,
  }),
  getters: {
    getRecords(state) {
      return state.categories;
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

        const request = await axios.get(`${generalStore.API_URL}category`, {
          headers: {
            Authorization: `Bearer ${token}`,
            //'Content-Type': 'application/json',
          },
        });

        this.categories = request.data;
        /*
        console.log(
          "store category - fetchRecords # " +
            JSON.stringify(this.categories, null, 2)
        );
        */
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
    async editRecord(isEditCheck, form) {
      //try {

      const generalStore = useGeneralStore();
      const userStore = useUserStore();
      const token = userStore.token;
      const addAPI = `${generalStore.API_URL}category`;
      const editAPI = `${generalStore.API_URL}category/${form.id}`;

      //console.log("store editRecord - form # " + JSON.stringify(form, null, 2));
      /*
      form = {
        name: "test",
        prince: 10,
      };
      */

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

      /*
      } catch (error) {
        const userStore = useUserStore();
        if (error.response != null) {
          if (error.response.statusText == "Unauthorized") {
            userStore.logUserOut();
            this.loggingOut = true;

            window.location.reload();
          }

          this.message =
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
        } /* else if (error.message != null) {
          this.message = "Error: " + error.message;
          console.log("Error message: " + error.message);
        } * /
        else {
          this.message = "Error: " + error;
          console.log("Error #: " + error);
        }
      } // catch
      */
    }, // editRecord
    async deleteRecord(id) {
      const generalStore = useGeneralStore();
      const userStore = useUserStore();
      const token = userStore.token;
      const deleteAPI = `${generalStore.API_URL}category/${id}`;

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
  },
});

export default useCategoryStore;

//export { useCategoryStore };
