
import { createApp } from 'vue'
import App from "./views/App.vue";
import router from './router';
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css';
import { VTimePicker } from 'vuetify/labs/VTimePicker'
// Import MDI icons


const vuetify = createVuetify({
  components,
  directives,
  VTimePicker
})

const app = createApp(App);


app.use(router);
app.use(vuetify); 
app.mount('#app')
