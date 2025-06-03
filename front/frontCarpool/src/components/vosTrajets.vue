<template>
  <v-container fluid class="ma-0 pt-16">
  <v-container>
    <v-card>
      <v-card-title>Mes trajets</v-card-title>
      <v-card-text>
        <v-row class="ma-0 pt-2" dense>
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
                    <v-alert
                      v-if="reservationStatus[trajet.id]"
                      :type="
                        reservationStatus[trajet.id] === 'Success' ? 'success' : 'error'
                      "
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
                          <v-btn
                            color="primary"
                            class="mt-4 text-body-2"
                            @click="
                              console.log('Deleting:', trajet.id) || supprimer(trajet.id)
                            "
                          >
                            Supprimer
                          </v-btn>
                        </v-col>
                        <v-col cols="2"> </v-col>
                        <v-col cols="7">
                          <v-btn color="primary" class="mt-4 text-body-2" block
                            >Envoyer un Mail</v-btn
                          >
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
    <v-container>
    <v-card >
      <v-card-title>Mes Reservations</v-card-title>
      <v-card-text>
        <v-row class="ma-0 pt-2" dense>
          <v-col
            v-for="reservation in reservations"
            :key="reservation.id"
            cols="12"
            sm="6"
            md="4"
            lg="3"
            xl="2"
            class="pa-2"
          >
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
                <v-card-title>Details du Reservation</v-card-title>
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
                      :type="
                        reservationStatus[reservation.id] === 'Success'
                          ? 'success'
                          : 'error'
                      "
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
                          <v-btn
                            color="primary"
                            class="mt-4 text-body-2"
                            @click="annuler(reservation.id)"
                          >
                            Annuler
                          </v-btn>
                        </v-col>
                        <v-col cols="1"> </v-col>
                        <v-col cols="7">
                          <v-btn color="primary" class="mt-4 text-body-2" block
                            >Envoyer un Mail</v-btn
                          >
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
import { onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";

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
        console.log(userId);
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

    const supprimer = async (trajetId) => {
      if (!trajetId) {
        console.error("ID du trajet non défini");
        alert("Erreur : ID du trajet manquant");
        return;
      }
      try {
        const response = await api.delete(`/trajet/${trajetId}`);
        if (response.status === 200) {
          alert("Trajet supprimé avec succès");
          // Refresh trajets list
          await fetchTrajets();
        }
      } catch (error) {
        console.error("Erreur lors de la suppression:", error);
        alert("Échec de la suppression du trajet");
      }
    };

    const annuler = async (reservationId) => {
      try {
        const userId = localStorage.getItem("userId");
        const response = await api.delete(`/reservation/annuleReservation/${userId}/${reservationId}`);
        if (response.status === 200) {
          alert("Réservation annulée avec succès");
          // Refresh reservations list
          await fetchReservations();
        }
      } catch (error) {
        console.error("Erreur lors de l'annulation:", error);
        alert("Échec de l'annulation de la réservation");
      }
    };

    const toggleExpand = (trajetId) => {
      show.value[trajetId] = !show.value[trajetId];
    };

    const annulerReservation = async (reservationId) => {
      const response = await api.delete(`/reservation/${trajetId}`);
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
        reservationMessages.value[trajetId] =
          "Erreur lors de la réservation: Plus de places";
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
      annuler,
      supprimer,
      reservationStatus, // Bind reservation status
      reservationMessages, // Bind messages
    };
  },
};
</script>
