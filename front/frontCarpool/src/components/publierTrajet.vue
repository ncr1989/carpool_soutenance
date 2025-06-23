<template>
  <v-container class="fill-height pt-16" fluid>
    <v-sheet class="mx-auto pa-6" width="400" elevation="3" rounded="lg">
      <h2 class="text-h5 mb-6 text-center">Publier un trajet</h2>
      <v-form @submit.prevent="handleSubmit">
        <v-autocomplete v-model="villeDepart" label="Ville de départ" :items="villes" variant="outlined" clearable
          class="mb-4" density="comfortable" prepend-icon="mdi-city"></v-autocomplete>

        <v-autocomplete v-model="villeArrivee" label="Ville d'arrivée" :items="villes" variant="outlined" clearable
          class="mb-4" density="comfortable" prepend-icon="mdi-city"></v-autocomplete>


        <v-text-field v-model="nbrPlaces" label="Nombre de places" type="text" variant="outlined" density="comfortable"
          class="mb-4" prepend-icon="mdi-account"></v-text-field>

        <v-text-field v-model="dateTrajet" label="Date du trajet" type="date" variant="outlined" density="comfortable"
          class="mb-4" prepend-icon="mdi-calendar"></v-text-field>

        <v-menu v-model="timePicker" :close-on-content-click="false" transition="scale-transition" max-width="300">
          <template v-slot:activator="{ props }">
            <v-text-field v-bind="props" v-model="heureTrajet" label="Heure du trajet" variant="outlined"
              density="comfortable" class="mb-4" prepend-icon="mdi-clock" readonly></v-text-field>
          </template>

          <v-time-picker v-model="heureTrajet" format="24hr" @click:save="timePicker = false" full-width>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="timePicker = false"> OK </v-btn>
          </v-time-picker>
        </v-menu>

        <v-btn type="submit" block size="large" color="primary"
          :disabled="!villeDepart || !villeArrivee || !dateTrajet || !heureTrajet">
          Publier
          <v-icon end icon="mdi-magnify"></v-icon>
        </v-btn>
      </v-form>
    </v-sheet>
    <v-snackbar v-model="showSnackbar" :color="snackbarColor" timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>

  </v-container>

</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../service/api";
import { VTimePicker } from "vuetify/labs/components";
import { useRouter } from 'vue-router';

const router = useRouter();



const showSnackbar = ref(false);
const snackbarMessage = ref("");
const snackbarColor = ref("success");


const timePicker = ref(false);
const villes = ref([]);
const villeDepart = ref(null);
const villeArrivee = ref(null);
const dateTrajet = ref(new Date().toISOString().substr(0, 10));
const nbrPlaces = ref(null);
const heureTrajet = ref(null);
const userId = localStorage.getItem("userId");

const fetchVilles = async () => {
  try {
    const response = await api.get("/ville/listeVilles");
    villes.value = response.data.map((v) => v.nom);
  } catch (error) {
    console.error("Erreur lors de la récupération des villes:", error);
  }
};

const publierTrajet = async () => {
  try {
    const response = await api.post("/trajet/new", {
      villeDepart: villeDepart.value,
      villeArrivee: villeArrivee.value,
      personneId: userId,
      nbrPlaces: nbrPlaces.value,
      dateTrajet: `${dateTrajet.value}T${heureTrajet.value}:00`,
    });
    console.log("Trajet publié:", response.data);
    return true;
  } catch (error) {
    console.error("Erreur lors de la publicatiion du trajet:", error);
    return false;
  }
};

// const handleSubmit = async () => {
//   if (!villeDepart.value || !villeArrivee.value || !dateTrajet.value || !heureTrajet.value) return;

//   try {
//     const success = await publierTrajet();
//     if (success) {
//       // Redirect to mes trajets using query param
//       router.push({
//         path: '/accueil?tab=list',

//       });
//     }
//   } catch (error) {
//     console.error("Submission error:", error);
//   }
// };
// Fetch cities on mount
const handleSubmit = async () => {
  if (!villeDepart.value || !villeArrivee.value || !dateTrajet.value || !heureTrajet.value) return;

  try {
    const success = await publierTrajet();
    if (success) {
      snackbarMessage.value = "Trajet publié avec succès !";
      snackbarColor.value = "success";
      showSnackbar.value = true;

      // Optionally wait for the snackbar before redirect
      setTimeout(() => {
        router.push({ path: "/accueil?tab=list" });
      }, 1000); // let the snackbar show for a second
    } else {
      snackbarMessage.value = "Erreur lors de la publication du trajet.";
      snackbarColor.value = "error";
      showSnackbar.value = true;
    }
  } catch (error) {
    snackbarMessage.value = "Erreur lors de l'envoi du formulaire.";
    snackbarColor.value = "error";
    showSnackbar.value = true;
  }
};
onMounted(fetchVilles);
</script>
