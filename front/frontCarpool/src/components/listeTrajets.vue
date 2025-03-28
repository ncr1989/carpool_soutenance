<template>
    <v-container fluid class=" ma-0">
      <v-row class="ma-0 pt-2" dense>
        <v-col v-for="trajet in trajets" :key="trajet.id" cols="12" sm="6" md="4" lg="3" xl="2" class="pa-2">
          <v-card color="indigo" variant="elevated" class="mx-0">
            <v-card-title>
              {{ trajet.villeDepart }} → {{ trajet.villeArrivee }}
            </v-card-title>
            <v-divider></v-divider>
            <v-card-subtitle>
              Date: {{ trajet.dateTrajet }}
              <v-divider></v-divider>
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
                  <v-divider></v-divider>
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
                      <v-col cols="3">
                        <v-btn color="primary" class="mt-4 text-body-2 " @click="reserver(trajet.id)">
                    Réserver
                  </v-btn>
                      </v-col>
                      <v-col cols="6">
                        <v-btn color="primary" class="mt-4 text-body-2" block>Envoyer un Mail</v-btn>
                      </v-col>
                      <v-col cols="3">
                        <v-btn color="primary" class="mt-4 text-body-2" block>Annuler</v-btn>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
              </div>
            </v-expand-transition>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </template>
  
  <script>
  import api from "../service/api";
  import { ref } from "vue";
  
  export default {
    setup() {
      const trajets = ref([]);
      const show = ref({});
      const reservationStatus = ref({}); 
      const reservationMessages = ref({}); 
      const userId = localStorage.getItem("userId");
  
      const fetchTrajets = async () => {
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
        }
      };
  
      const toggleExpand = (trajetId) => {
        show.value[trajetId] = !show.value[trajetId];
      };
  
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
  
      fetchTrajets();
  
      return {
        trajets,
        show,
        toggleExpand,
        reserver,
        reservationStatus, // Bind reservation status
        reservationMessages, // Bind messages
      };
    },
  };
  </script>
  