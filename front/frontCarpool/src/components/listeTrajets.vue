<template>
  <v-container fluid class="ma-0 pt-16">
    <!-- Add loading progress when trajets are being fetched -->
    <v-row v-if="loading" justify="center" align="center" class="py-10">
      <v-col cols="12" class="text-center">
        <v-progress-circular
          :size="70"
          :width="7"
          color="primary"
          indeterminate
        ></v-progress-circular>
        <p class="mt-3 text-h6">Chargement des trajets...</p>
      </v-col>
    </v-row>

    <!-- Show trajets when loading is complete -->
    <v-row v-else-if="trajets.length > 0" class="ma-0 pt-2" dense>
      <v-col v-for="trajet in trajets" :key="trajet.id" cols="12" sm="6" md="4" lg="3" xl="2" class="pa-2">
        <v-card color="indigo" variant="elevated" class="mx-0">
          <v-card-title>
            {{ trajet.villeDepart }} → {{ trajet.villeArrivee }}
          </v-card-title>
          
          <v-card-subtitle>
            Date: {{ trajet.dateTrajet }}
            <v-divider></v-divider>
            Places: {{ trajet.nbrPlaces }}
          </v-card-subtitle>
          <v-card-actions>
            <v-card-title>Details du Trajet</v-card-title>
            <v-spacer></v-spacer>
            <v-btn @click="toggleExpand(trajet.id)" icon>
              <v-icon>{{
                show[trajet.id] ? "mdi-chevron-up" : "mdi-chevron-down"
              }}</v-icon>
            </v-btn>
          </v-card-actions>

          <v-expand-transition>
            <div v-show="show[trajet.id]">
              <v-card-text>
                <v-alert
                  v-if="reservationStatus[trajet.id]"
                  :type="reservationStatus[trajet.id] === 'Success' ? 'success' : 'error'"
                  dismissible
                >
                  {{ reservationMessages[trajet.id] }}
                </v-alert>
  
                <v-form>
                  <v-text-field
                    v-model="trajet.conducteur"
                    label="Conducteur"
                    readonly
                    :value="`${trajet.conducteur[0]}, ${trajet.conducteur[1]}`"
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
                  ></v-text-field>
                  <v-text-field
                    v-model="trajet.heureTrajet"
                    label="Heure"
                    readonly
                  ></v-text-field>
                </v-form>
                <v-container>
                  <v-row>
                    <v-col cols="6">
                      <v-btn color="primary" class="mt-4 text-body-2" block>Envoyer un Mail</v-btn>
                    </v-col>
                    <v-col cols="6">
                      <v-btn color="primary" class="mt-4 text-body-2" @click="reserver(trajet.id)" block>Reserver</v-btn>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
            </div>
          </v-expand-transition>
        </v-card>
      </v-col>
    </v-row>

    <!-- Show message if no trajets after loading -->
    <v-row v-else justify="center" align="center" class="py-10">
      <v-col cols="12" class="text-center">
        <v-icon size="x-large" color="grey">mdi-folder-open-outline</v-icon>
        <p class="mt-3 text-h6 grey--text">Aucun trajet disponible</p>
      </v-col>
    </v-row>
  </v-container>
</template>
  
<script>
import api from "../service/api";
import { ref, onMounted } from "vue";
export default {
  setup() {
    const trajets = ref([]);
    const show = ref({});
    const reservationStatus = ref({}); 
    const reservationMessages = ref({}); 
    const userId = localStorage.getItem("userId");
    
    // Add loading state
    const loading = ref(true);
  
    const fetchTrajets = async () => {
      // Set loading to true at start of fetch
      loading.value = true;
      try {
        const response = await api.get("/trajet/listeTrajets");
        trajets.value = response.data;
        trajets.value.forEach((trajet) => {
          show.value[trajet.id] = false;
          reservationStatus.value[trajet.id] = ""; 
          reservationMessages.value[trajet.id] = "";
        });
      } catch (error) {
        console.error("Erreur lors de la récupération des trajets:", error);
      } finally {
        // Set loading to false when fetch is complete (success or error)
        loading.value = false;
      }
    };
  
    const toggleExpand = (trajetId) => {
      show.value[trajetId] = !show.value[trajetId];
    };

    const annulerReservation = async (reservationId) => {
      const response = await api.delete(`/reservation/${reservationId}`);
    }
  
    const reserver = async (trajetId) => {
      try {
        const response = await api.post(`/reservation/${userId}/${trajetId}`);
  
        if (response.status === 200) {
          reservationStatus.value[trajetId] = "Success";
          reservationMessages.value[trajetId] = "Reservation successful!";
        } else {
          reservationStatus.value[trajetId] = "Error";
          reservationMessages.value[trajetId] = "Something went wrong. Please try again.";
        }
      } catch (error) {
        reservationStatus.value[trajetId] = "Error";
        reservationMessages.value[trajetId] = "Erreur lors de la réservation: Plus de places" ;
      }
  
      console.log("Reservation for trajet:", trajetId);
    };
  
    // Use onMounted to fetch trajets
    onMounted(fetchTrajets);
  
    return {
      trajets,
      show,
      loading,
      toggleExpand,
      reserver,
      reservationStatus,
      reservationMessages,
    };
  },
};
</script>