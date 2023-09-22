import { defineStore } from "pinia";

const useGeneralStore = defineStore("general", {
  state: () => ({
    //
    API_URL: "http://localhost:8000/api/",
  }),
});

export default useGeneralStore;
