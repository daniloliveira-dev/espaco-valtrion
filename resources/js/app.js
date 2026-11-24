import '../css/app.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './app/App.vue';
import router from './app/router';
import { initializeTheme } from './shared/composables/useTheme';

initializeTheme();

createApp(App)
    .use(createPinia())
    .use(router)
    .mount('#app');
