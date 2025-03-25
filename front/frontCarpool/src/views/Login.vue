<template>
  <v-app>
    <v-main>
      <v-container fluid class="fill-height">
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="6" lg="4" xl="3">
            <v-card class="pa-4 elevation-2">
              <v-card-title class="text-h5">Login</v-card-title>
              <v-card-text>
                <v-form @submit.prevent="login" ref="form">
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
                  <v-container>
                    <v-row>
                      <v-col cols="3">
                        <!-- Each button takes 1/3 of the width -->
                        <v-btn type="submit" color="primary" class="mt-4" block
                          >Login</v-btn
                        >
                      </v-col>
                      <v-col cols="5">
                        <v-btn color="primary" class="mt-4" block>Inscription</v-btn>
                      </v-col>
                      <v-col cols="4">
                        <v-btn color="primary" class="mt-4" block @click="reset">Effacer</v-btn>
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
import { ref } from 'vue'
import api from "../service/api";
const form = ref()
function reset() {
    form.value.reset()
  }
export default {
  data() {
    return {
      email: "",
      password: "",
    };
  },
  methods: {

    reset() {
      this.email = "";
      this.password = "";
      this.$refs.form.resetValidation();
    },

    async login() {
      try {
        const response = await api.post("/login", {
          email: this.email,
          mdp: this.password, // Ensure this matches your Symfony API
        });

        const token = response.data.token;
        const userId = response.data.id;

        if (token) {
          localStorage.setItem("jwt", token); // Store JWT
          localStorage.setItem("userId", userId); // Store user ID (optional)

          api.defaults.headers.common["x-auth-token"] = token; // Use 'x-auth-token' header

          this.$router.push("/accueil"); // Redirect after login
        }
      } catch (error) {
        console.error("Login failed:", error);
      }
    },
  },
};
</script>
