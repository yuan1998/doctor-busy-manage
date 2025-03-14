import {createApp} from 'vue';
import App from './App.vue';
import {createPinia} from 'pinia'
import router from './router'
import "./style.css";
import { createHead } from '@unhead/vue/client';


const head = createHead();
const app = createApp(App)
    .use(createPinia())
    .use(router)
    .use(head)
    .mount("#app");
