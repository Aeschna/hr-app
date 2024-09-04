import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import axios from 'axios';
import { createApp } from 'vue';
import App from './components/Companies.vue';
import App2 from './components/Employees.vue';
import router from './router'; // Import the router


const app = createApp(App);
const app2 = createApp(App2);

// Set the token from local storage if it exists
const token = localStorage.getItem('token');


axios.defaults.withCredentials = true; // Send cookies with requests
axios.defaults.baseURL = 'http://localhost:8000';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Authorization'] = 'Bearer ' + window.api_token;

app.use(router);

app.config.globalProperties.$axios = axios;

app.mount('#app');
app2.mount('#app2');

