<template>
  <v-app>
    <v-main>
      <v-container fluid class="fill-height">
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="6" lg="4" xl="3">
            <v-card class="pa-4 elevation-2">
              <v-card-title class="text-h5">Inscription</v-card-title>
              <v-card-text>
                <v-form @submit.prevent="inscription" ref="form">
                  <v-text-field
                    v-model="email"
                    label="Email"
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
                    label="Prenom"
                    type="text"
                    required
                  ></v-text-field>
                  <v-text-field
                    v-model="nom"
                    label="Nom"
                    type="text"
                    required
                  ></v-text-field>
                  <v-text-field
                    v-model="pseudo"
                    label="Pseudo"
                    type="text"
                    required
                  ></v-text-field>

                  <!-- Checkbox to toggle additional fields -->
                  <v-checkbox
                    v-model="showAdditionalFields"
                    label="Je souhaite enregistrer une voiture"
                  ></v-checkbox>

                  <!-- Additional fields that appear when checkbox is checked -->
                  <v-col v-if="showAdditionalFields">
                    <v-text-field v-model="modele" label="Modele"></v-text-field>
                    <v-text-field
                      v-model="immatriculation"
                      label="Immatriculation"
                    ></v-text-field>
                    <v-text-field v-model="marque" label="Marque"></v-text-field>
                  </v-col>

                  <v-container>
                    <v-row>
                      <v-col cols="6">
                        <v-btn color="primary" class="mt-4" block @click="inscription">
                          Valider l'Inscription
                        </v-btn>
                      </v-col>
                      <v-col cols="6">
                        <v-btn color="primary" class="mt-4" block @click="reset">
                          Annuler
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
    </v-main>
  </v-app>
</template>

<script>
import { ref } from "vue";
import api from "../service/api";

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
    };
  },
  methods: {
    reset() {
      this.email = "";
      this.password = "";
      this.pseudo = "";
      this.confirmPassword ="";
      this.prenom = "";
      this.nom = "";
      this.modele = "";
      this.immatriculation = "";
      this.marque = "";
      this.showAdditionalFields = false; // Reset checkbox state
      this.$refs.form.resetValidation();
    },

    async inscription() {
      const isValid = this.$refs.form.validate();
      if (!isValid || this.password !== this.confirmPassword) {
        alert("Les mots de passe ne correspondent pas.");
        return;
        }
      try {
        const response = await api.post("/inscription", {
          email: this.email,
          mdp: this.password,
          confirmMdp: this.confirmPassword,
          prenom: this.prenom,
          pseudo: this.pseudo, // Optional: Send first name
          nom: this.nom,
          modele: this.modele,
          immatriculation: this.immatriculation,
          marque: this.marque, // Optional: Send last name
        });

        this.$router.push("/login"); // Redirect after login
      } catch (error) {
        console.error("Inscription failed:", error);
      }
    },
  },
};
</script>
