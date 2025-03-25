<template>
  <div id="app">
   
    
    <!-- The main content of the app will be rendered here -->
    <main>
        <v-container fluid class="pa-0 ma-0">
      <router-view></router-view>
    </v-container>  <!-- This will render the matched route's component -->
    </main>

    
  </div>
</template>

<script>
export default {
  name: "App",
};
</script>

<style>
#app {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

/* Ensure main content takes full width */
main {
  width: 100%;
  margin: 0;
  padding: 0;
}

/* Optional: If you still see spacing, add these */
html, body {
  margin: 0;
  padding: 0;
  width: 100%;
}

</style>



