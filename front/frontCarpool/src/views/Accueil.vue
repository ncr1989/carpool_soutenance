<template>
  <v-app>
    <v-app-bar extended color="primary" density="compact">
    
      <v-app-bar-nav-icon @click="drawer = !drawer" class="d-sm-none"></v-app-bar-nav-icon>
      
      <v-app-bar-title>
        <span class="d-none d-sm-inline">SlanAbhaile</span>
        <span class="d-inline d-sm-none">SA</span>
      </v-app-bar-title>

      <v-tabs 
        v-model="tab" 
        centered 
        class="d-none d-sm-flex"
        :grow="$vuetify.display.smAndDown"
      >
        <v-tab value="list">
          <span class="d-none d-md-inline">Liste des Trajets</span>
          <v-icon icon="mdi-format-list-bulleted" class="d-md-none"></v-icon>
        </v-tab>
        <v-tab value="search">
          <span class="d-none d-md-inline">Rechercher</span>
          <v-icon icon="mdi-magnify" class="d-md-none"></v-icon>
        </v-tab>
        <v-tab value="my-trips">
          <span class="d-none d-md-inline">Vos Trajets</span>
          <v-icon icon="mdi-account-group" class="d-md-none"></v-icon>
        </v-tab>
        <v-tab value="publish">
          <span class="d-none d-md-inline">Publier</span>
          <v-icon icon="mdi-plus-circle" class="d-md-none"></v-icon>
        </v-tab>
        <v-tab value="account">
          <v-icon icon="mdi-account"></v-icon>
        </v-tab>
      </v-tabs>
    </v-app-bar>

    
    <v-navigation-drawer v-model="drawer" temporary location="left" class="d-sm-none">
      <v-list nav density="compact">
        <v-list-item
          v-for="item in navItems"
          :key="item.value"
          :value="item.value"
          @click="tab = item.value; drawer = false"
        >
          <template v-slot:prepend>
            <v-icon :icon="item.icon"></v-icon>
          </template>
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main class="bg-grey-lighten-3 pt-10 pt-sm-16">
      <v-window v-model="tab" touchless>
        <v-window-item value="list">
          <liste-des-trajets />
        </v-window-item>
        <v-window-item value="search">
          <recherche-trajet />
        </v-window-item>
        <v-window-item value="my-trips">
          <mes-trajets />
        </v-window-item>
        <v-window-item value="publish">
          <publier-trajet />
        </v-window-item>
        <v-window-item value="account">
          <profile />
        </v-window-item>
      </v-window>
    </v-main>

    <!-- Mobile Bottom Navigation -->
    <v-bottom-navigation 
      v-model="tab"
      grow
      class="d-sm-none"
      color="primary"
    >
      <v-btn value="list">
        <v-icon>mdi-format-list-bulleted</v-icon>
        <span class="text-caption">Liste</span>
      </v-btn>
      
      <v-btn value="search">
        <v-icon>mdi-magnify</v-icon>
        <span class="text-caption">Recherche</span>
      </v-btn>
      
      <v-btn value="my-trips">
        <v-icon>mdi-account-group</v-icon>
        <span class="text-caption">Mes Trajets</span>
      </v-btn>
      
      <v-btn value="publish">
        <v-icon>mdi-plus-circle</v-icon>
        <span class="text-caption">Publier</span>
      </v-btn>
      
      <v-btn value="account">
        <v-icon>mdi-account</v-icon>
        <span class="text-caption">Compte</span>
      </v-btn>
    </v-bottom-navigation>
  </v-app>
</template>

<script>
import ListeDesTrajets from "../components/listeTrajets.vue";
import MesTrajets from "../components/vosTrajets.vue";
import RechercheTrajet from "../components/rechercheTrajet.vue";
import PublierTrajet from "../components/publierTrajet.vue";
import Profile from "../components/profile.vue";

export default {
  data() {
    return {
      
      tab: this.$route.query.tab || "list",
      drawer: false,
      navItems: [
        { value: "list", title: "Liste des Trajets", icon: "mdi-format-list-bulleted" },
        { value: "search", title: "Rechercher un trajet", icon: "mdi-magnify" },
        { value: "my-trips", title: "Vos Trajets", icon: "mdi-account-group" },
        { value: "publish", title: "Publier un Trajet", icon: "mdi-plus-circle" },
        { value: "account", title: "Mon compte", icon: "mdi-account" }
      ]
    };
  },

  
  
  components: {
    ListeDesTrajets,
    MesTrajets,
    RechercheTrajet,
    PublierTrajet,
    Profile
  }
};
</script>
