<template>
    <v-sheet class="mx-auto" width="300">
      <v-form @submit.prevent>
        <v-text-field
          v-model="villeDepart"
          :rules="rules"
          label="Depart"
        ></v-text-field>
        <v-text-field
          v-model="villeArrivee"
          :rules="rules"
          label="Depart"
        ></v-text-field>
        <v-autocomplete
        label="Depart"
        :items="villes"
      ></v-autocomplete>
      <v-autocomplete
        label="Arrivee"
        :items="villes"
      ></v-autocomplete>
       
        <v-btn class="mt-2" type="submit" block>Submit</v-btn>
      </v-form>
    </v-sheet>
  </template>
  <script setup>
  import { ref, onMounted } from 'vue';


  import api from "../service/api";
  const trajets = ref([]);
  const firstName = ref('');
  const villes = ref([]);
  


  const fetchTrajets = async () => {
        try {
          const response = await api.post(`/trajet/${villeD}/${villeA}/${dateTrajet}`);
          trajets.value = response.data;
        } catch (error) {
          console.error("Erreur lors de la récupération des trajets:", error);
        }
      };

      const fetchVilles = async () => {
        try {
          const response = await api.get("/ville/listeVilles");

          villes.value = response.data.map(v => v.nom); 
        } catch (error) {
          console.error("Erreur lors de la récupération des trajets:", error);
        }
      };
  

  

  onMounted(() => {
  fetchVilles();
});
</script>
