<template>
  <v-container fluid class="fill-height pt-16">
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4" xl="3">
        <v-card class="pa-4 elevation-2">
          <v-card-title class="text-h5">Ma Profile</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="update" ref="form">
              <v-text-field
                v-model="email"
                :label="profile.email || 'Email'"
                type="email"
                required
              ></v-text-field>
              <v-text-field
                v-model="password"
                label="Password"
                type="password"
                required
              ></v-text-field>
              <v-text-field
                v-model="confirmPassword"
                label="Confirm Password"
                type="password"
                required
              ></v-text-field>
              <v-text-field
                v-model="prenom"
                :label="profile.prenom || 'Prenom'"
                type="text"
                required
              ></v-text-field>
              <v-text-field
                v-model="nom"
                :label="profile.nom || 'Nom'"
                type="text"
                required
              ></v-text-field>
              <v-text-field
                v-model="pseudo"
                :label="profile.pseudo || 'Pseudo'"
                type="text"
                required
              ></v-text-field>
              <v-text-field
                v-model="tel"
                :label="profile.tel || 'Telephone'"
                type="text"
                required
              ></v-text-field>

              <v-checkbox
                v-model="showAdditionalFields"
                label="Je souhaite modifier une voiture"
              ></v-checkbox>

              <v-col v-if="showAdditionalFields">
                <v-text-field
                  v-model="modele"
                  :label="profile.modele || 'Modele'"
                ></v-text-field>
                <v-text-field
                  v-model="immatriculation"
                  :label="profile.immatriculation || 'Immatriculation'"
                ></v-text-field>
                <v-text-field
                  v-model="marque"
                  :label="profile.marque || 'Marque'"
                ></v-text-field>
              </v-col>

              <v-container>
                <v-row>
                  <v-col cols="6">
                    <v-btn color="primary" class="mt-4 text-body-2" block @click="update">
                      Mettre a jour
                    </v-btn>
                  </v-col>
                  <v-col cols="6">
                    <v-btn color="primary" class="mt-4 text-body-2" block @click="reset">
                      Effacer
                    </v-btn>
                  </v-col>
                </v-row>
              </v-container>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { ref } from "vue";
import api from "../service/api";
const userId = localStorage.getItem("userId");
const profile = ref({});

export default {
  data() {
    return {
      email: "",
      password: "",
      confirmPassword: "",
      prenom: "",
      pseudo: "",
      nom: "",
      modele: "",
      immatriculation: "",
      marque: "",
      showAdditionalFields: false,
      profile: {
        email: "",
        password: "",
        prenom: "",
        pseudo: "",
        nom: "",
        modele: "",
        immatriculation: "",
        marque: "",
      },
    };
  },

  mounted() {
    const userId = localStorage.getItem("userId");
    console.log(userId)
    if (userId) {
      this.fetchProfile(userId);
    }
    // Now use the userId to fetch the profile or for other operations
  },
  methods: {
    reset() {
      this.email = "";
      this.password = "";
      this.pseudo = "";
      this.confirmPassword = "";
      this.prenom = "";
      this.nom = "";
      this.modele = "";
      this.tel ="";
      this.immatriculation = "";
      this.marque = "";
      this.showAdditionalFields = false; // Reset checkbox state
      this.$refs.form.resetValidation();
    },

    async fetchProfile() {
        const userId = localStorage.getItem("userId");
      try {
        const response = await api.get(`/getCompte/${userId}`);
        profile.value = response.data;
      } catch (error) {
        console.error("Erreur lors de la récupération du profile", error);
      }
    },

    async update() {
        const userId = localStorage.getItem("userId");
      const isValid = this.$refs.form.validate();
      if (!isValid || this.password !== this.confirmPassword) {
        alert("Les mots de passe ne correspondent pas.");
        return;
      }
      try {
        const response = await api.put(`/editProfile/${userId}`, {
          email: this.email,
          mdp: this.password,
          tel: this.tel,
          confirmMdp: this.confirmPassword,
          prenom: this.prenom,
          pseudo: this.pseudo, // Optional: Send first name
          nom: this.nom,
          modele: this.modele,
          immatriculation: this.immatriculation,
          marque: this.marque, // Optional: Send last name
        });

        alert("Profile updates !") // Redirect after login
      } catch (error) {
        console.error("Echec mise a jour du profile:", error);
      }
    },
  },
};
</script>
