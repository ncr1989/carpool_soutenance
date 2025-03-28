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
              Places: {{ trajet.nombrePlaces }}
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
                  <!-- Show reservation success/error message specific to this trajet -->
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
                      :value="`${trajet.conducteur.nom},${trajet.conducteur.prenom}`"
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
                        <v-btn color="primary" class="mt-4" @click="reserver(trajet.id)">
                    Réserver
                  </v-btn>
                      </v-col>
                      <v-col cols="5">
                        <v-btn color="primary" class="mt-4" block>Envoyer un Mail</v-btn>
                      </v-col>
                      <v-col cols="4">
                        <v-btn color="primary" class="mt-4" block>Annuler</v-btn>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
              </div>
            </v-expand-transition>
          </v-card>
        </v-col>
      </v-row>



      <v-container fluid class=" ma-0">
        <v-card>
          <v-card-title></v-card-title>
          <v-card-text>
      <v-row class="ma-0 pt-2" dense>
        <v-col v-for="reservation in reservation" :key="reservation.id" cols="12" sm="6" md="4" lg="3" xl="2" class="pa-2">
          <v-card color="indigo" variant="elevated" class="mx-0">
            <v-card-title>
              {{ reservation.villeDepart }} → {{ reservation.villeArrivee }}
            </v-card-title>
            <v-divider></v-divider>
            <v-card-subtitle>
              Date: {{ reservation.dateTrajet }}
              <v-divider></v-divider>
              <v-divider></v-divider>
              Places: {{ reservation.nombrePlaces }}
            </v-card-subtitle>
            <v-card-actions>
              <v-card-title>Details du Rservation</v-card-title>
              <v-spacer></v-spacer>
              <v-btn @click="toggleExpand(reservation.id)" icon>
                <v-icon>{{
                  show[reservation.id] ? "mdi-chevron-up" : "mdi-chevron-down"
                }}</v-icon>
              </v-btn>
            </v-card-actions>
  
            <v-expand-transition>
              <div v-show="show[reservation.id]">
                <v-card-text>
                  <!-- Show reservation success/error message specific to this trajet -->
                  <v-alert
                    v-if="reservationStatus[reservation.id]"
                    :type="reservationStatus[reservation.id] === 'Success' ? 'success' : 'error'"
                    dismissible
                  >
                    {{ reservationMessages[reservation.id] }}
                  </v-alert>
                  <v-divider></v-divider>
                  <v-form>
                    <v-text-field
                      v-model="reservation.conducteur"
                      label="Conducteur"
                      readonly
                      :value="`${reservation.conducteur.nom},${reservation.conducteur.prenom}`"
                    ></v-text-field>
                    <v-text-field
                      v-model="reservation.villeDepart"
                      label="Depart"
                      readonly
                      :value="reservation.villeDepart"
                    ></v-text-field>
                    <v-text-field
                      v-model="reservation.villeArrivee"
                      label="Arrivee"
                      readonly
                      :value="reservation.villeArrivee"
                    ></v-text-field>
                    <v-text-field
                      v-model="reservation.dateTrajet"
                      label="Date"
                      readonly
                    ></v-text-field>
                    <v-text-field
                      v-model="reservation.heureTrajet"
                      label="Heure"
                      readonly
                    ></v-text-field>
                  </v-form>
                

                  <v-container>
                    <v-row>
                      <v-col cols="3">
                        <v-btn color="primary" class="mt-4" @click="reserver(reservation.id)">
                    Réserver
                  </v-btn>
                      </v-col>
                      <v-col cols="5">
                        <v-btn color="primary" class="mt-4" block>Envoyer un Mail</v-btn>
                      </v-col>
                      <v-col cols="4">
                        <v-btn color="primary" class="mt-4" block>Annuler</v-btn>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
              </div>
            </v-expand-transition>
          </v-card>
        </v-col>
      </v-row>
      </v-card-text>
    </v-card>
    </v-container>

    </v-container>
  </template>
  
  <script>
  import api from "../service/api";
  import { ref } from "vue";
  import {onMounted, onUnmounted} from "vue";
  import { useRouter } from 'vue-router';
  
onMounted(() => {
  fetchReservations();
  
});

  export default {
    setup() {
      const reservations = ref([]);
      const trajets = ref([]);
      const show = ref({});
      const reservationStatus = ref({}); // Track status for each trajet
      const reservationMessages = ref({}); // Store messages per trajet
      const userId = localStorage.getItem("userId");
      
  
      const fetchReservations = async () => {
        try {
          const userId = localStorage.getItem("userId");
          console.log(userId)
          const response = await api.get(`/reservation/listeReservations/${userId}`);
          reservations.value = response.data;
          console.log(reservations.value);
          reservations.value.forEach((reservation) => {
            show.value[reservation.id] = false;
            reservationStatus.value[reservation.id] = ""; // Initialize status
            reservationMessages.value[reservation.id] = "";
          });
        } catch (error) {
          console.error("Erreur lors de la récupération des reservations:", error);
        }
      };




      const fetchTrajets = async () => {
        try {
          const userId = localStorage.getItem("userId");
          const response = await api.get(`/trajet/listeTrajetsProposes/${userId}`);
          trajets.value = response.data;
          console.log(trajets.value);
          trajets.value.forEach((trajet) => {
            show.value[trajet.id] = false;
            reservationStatus.value[trajet.id] = ""; // Initialize status
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
      fetchReservations();

      return {
        trajets,
        reservations,
        show,
        toggleExpand,
        reserver,
        reservationStatus, // Bind reservation status
        reservationMessages, // Bind messages
      };
    },
  };
  </script>
  
