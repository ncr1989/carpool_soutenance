<template>
  <v-container class="fill-height" fluid>
    <v-sheet
      v-if="trajets.length === 0"
      class="mx-auto pa-6"
      width="400"
      elevation="3"
      rounded="lg"
    >
      <h2 class="text-h5 mb-6 text-center">Rechercher un trajet</h2>
      <v-form @submit.prevent="handleSubmit">
        <v-autocomplete
          v-model="villeDepart"
          label="Ville de départ"
          :items="villes"
          variant="outlined"
          clearable
          class="mb-4"
          density="comfortable"
        ></v-autocomplete>

        <v-autocomplete
          v-model="villeArrivee"
          label="Ville d'arrivée"
          :items="villes"
          variant="outlined"
          clearable
          class="mb-4"
          density="comfortable"
        ></v-autocomplete>

        <v-text-field
          v-model="dateTrajet"
          label="Date du trajet"
          type="date"
          variant="outlined"
          density="comfortable"
          class="mb-4"
          prepend-icon="mdi-calendar"
        ></v-text-field>

        <v-text-field
          v-model="timeTrajet"
          label="Heure de départ"
          type="time"
          variant="outlined"
          density="comfortable"
          class="mb-4"
          format="24hr"
          prepend-icon="mdi-clock"
        ></v-text-field>

        <v-btn
          type="submit"
          block
          size="large"
          color="primary"
          :disabled="!villeDepart || !villeArrivee || !dateTrajet"
        >
          Rechercher
          <v-icon end icon="mdi-magnify"></v-icon>
        </v-btn>
      </v-form>
    </v-sheet>
  
    <ListeTrajets 
      v-else 
      :initial-trajets="trajets"
      @reset-search="resetSearch"
    />
    
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../service/api";


// Reactive references
const trajets = ref([]);
const villes = ref([]);
const villeDepart = ref(null);
const villeArrivee = ref(null);
const dateTrajet = ref(new Date().toISOString().substr(0, 10));
const timeTrajet = ref(null);

const fetchTrajets = async () => {
  try {
    const response = await api.get(
      `/trajet/${villeDepart.value}/${villeArrivee.value}/${dateTrajet.value}`
    );
    trajets.value = response.data;
  } catch (error) {
    console.error("Erreur lors de la récupération des trajets:", error);
  }
};

const fetchVilles = async () => {
  try {
    const response = await api.get("/ville/listeVilles");
    villes.value = response.data.map((v) => v.nom);
  } catch (error) {
    console.error("Erreur lors de la récupération des villes:", error);
  }
};

const handleSubmit = () => {
  if (villeDepart.value && villeArrivee.value && dateTrajet.value) {
    fetchTrajets();
  }
};

const resetSearch = () => {
  trajets.value = [];
  villeDepart.value = null;
  villeArrivee.value = null;
  dateTrajet.value = new Date().toISOString().substr(0, 10);
  timeTrajet.value = null;
};

onMounted(() => {
  fetchVilles();
});
</script>