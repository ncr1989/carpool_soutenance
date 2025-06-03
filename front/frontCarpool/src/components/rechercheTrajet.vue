<template>
  <v-container class="fill-height pt-16" fluid>
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
          prepend-icon="mdi-city"
          density="comfortable"
        ></v-autocomplete>

        <v-autocomplete
          v-model="villeArrivee"
          label="Ville d'arrivée"
          :items="villes"
          variant="outlined"
          clearable
          prepend-icon="mdi-city"
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
  
    <v-container v-else fluid class="ma-0">
      <v-row class="ma-0 pt-2" dense>
        <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
          <v-btn @click="resetSearch" color="secondary">
            <v-icon start>mdi-arrow-left</v-icon>
            Nouvelle Recherche
          </v-btn>
        </v-col>

        <v-col 
          v-for="trajet in trajets" 
          :key="trajet.id" 
          cols="12" 
          sm="6" 
          md="4" 
          lg="3" 
          xl="2" 
          class="pa-2"
        >
          <v-card color="indigo" variant="elevated" class="mx-0">
            <v-card-title>
              {{ trajet.villeDepart }} → {{ trajet.villeArrivee }}
            </v-card-title>
            <v-divider></v-divider>
            <v-card-subtitle>
              Date: {{ formatDate(trajet.dateTrajet) }}
              <v-divider></v-divider>
              Places: {{ trajet.nbrPlaces }}
            </v-card-subtitle>
            
            <v-card-actions>
              <v-card-title>Details du Trajet</v-card-title>
              <v-spacer></v-spacer>
              <v-btn @click="toggleExpand(trajet.id)" icon>
                <v-icon>{{
                  expanded[trajet.id] ? "mdi-chevron-up" : "mdi-chevron-down"
                }}</v-icon>
              </v-btn>
            </v-card-actions>

            <v-expand-transition>
              <div v-show="expanded[trajet.id]">
                <v-card-text>
                  <v-alert
                    v-if="reservationStatus[trajet.id]"
                    :type="reservationStatus[trajet.id] === 'Success' ? 'success' : 'error'"
                    dismissible
                  >
                    {{ reservationMessages[trajet.id] }}
                  </v-alert>

                  <v-divider></v-divider>
                  <v-form>
                    <v-text-field
                      v-model="trajet.conducteur"
                      label="Conducteur"
                      readonly
                      :value="`${trajet.conducteur.nom}, ${trajet.conducteur.prenom}`"
                    ></v-text-field>
                    <v-text-field
                      v-model="trajet.villeDepart"
                      label="Depart"
                      readonly
                      :value="trajet.villeDepart"
                    ></v-text-field>
                    <v-text-field
                      v-model="trajet.villeArrivee"
                      label="Arrivee"
                      readonly
                      :value="trajet.villeArrivee"
                    ></v-text-field>
                    <v-text-field
                      v-model="trajet.dateTrajet"
                      label="Date"
                      readonly
                      :value="trajet.dateTrajet"
                    ></v-text-field>
                    <v-text-field
                      v-model="trajet.heureTrajet"
                      label="Heure"
                      readonly
                      :value="trajet.heureTrajet"
                    ></v-text-field>
                  </v-form>

                  <v-container>
                    <v-row>
                      <v-col cols="6">
                        <v-btn 
                          color="primary" 
                          class="mt-4 text-body-2" 
                          @click="reserver(trajet.id)"
                          :disabled="trajet.nbrPlaces === 0"
                        >
                          Réserver
                        </v-btn>
                      </v-col>
                      <v-col cols="6">
                        <v-btn color="primary" class="mt-4 text-body-2" @click="envoyerMail(trajet)">
                          Envoyer un Mail
                        </v-btn>
                      </v-col>
                     
                    </v-row>
                  </v-container>
                </v-card-text>
              </div>
            </v-expand-transition>
          </v-card>
          <v-row v-if="trajets.length === 0" justify="center">
        <v-col cols="12" class="text-center">
          <v-alert type="info" variant="outlined">
            Aucun trajet trouvé correspondant à vos critères.
          </v-alert>
        </v-col>
      </v-row>
        </v-col>
      </v-row>  
    </v-container>
    
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../service/api";

const trajets = ref([]);
const villes = ref([]);
const villeDepart = ref(null);
const villeArrivee = ref(null);
const dateTrajet = ref(new Date().toISOString().substr(0, 10));


const expanded = ref({});
const reservationStatus = ref({});
const reservationMessages = ref({});

const fetchVilles = async () => {
  try {
    const response = await api.get("/ville/listeVilles");
    villes.value = response.data.map((v) => v.nom);
  } catch (error) {
    console.error("Erreur lors de la récupération des villes:", error);
  }
};

const fetchTrajets = async () => {
  try {
    const response = await api.get(
      `/trajet/${villeDepart.value}/${villeArrivee.value}/${dateTrajet.value}`
    );
    trajets.value = response.data;
    
    // Initialize expanded and reservation states for each trajet
    trajets.value.forEach((trajet) => {
      expanded.value[trajet.id] = false;
      reservationStatus.value[trajet.id] = '';
      reservationMessages.value[trajet.id] = '';
    });
    
  } catch (error) {
    console.error("Erreur lors de la récupération des trajets:", error);
    trajets.value = [];
  }
};

const toggleExpand = (trajetId) => {
  expanded.value[trajetId] = !expanded.value[trajetId];
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
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const reserver = async (trajetId) => {
  try {
    const userId = localStorage.getItem("userId");
    const response = await api.post(`/reservation/${userId}/${trajetId}`);

    if (response.status === 200) {
      // Find and update the specific trajet
      const trajetIndex = trajets.value.findIndex(t => t.id === trajetId);
      if (trajetIndex !== -1) {
        trajets.value[trajetIndex].nbrPlaces -= 1;
      }
      
      // Set reservation success status
      reservationStatus.value[trajetId] = 'Success';
      reservationMessages.value[trajetId] = 'Réservation réussie!';
    }
  } catch (error) {
    // Set reservation error status
    reservationStatus.value[trajetId] = 'Error';
    reservationMessages.value[trajetId] = 'Erreur de réservation: ' + 
      (error.response?.data?.message || 'Réservation impossible');
  }
};

const envoyerMail = (trajet) => {
  console.log('Envoyer un mail pour le trajet:', trajet);
  
};


onMounted(() => {
  fetchVilles();
});
</script>