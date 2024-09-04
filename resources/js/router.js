
import { createRouter, createWebHistory } from 'vue-router';
import Companies from './components/Companies.vue';
import Employees from './components/Employees.vue';
import CompanyCreate from './components/CompanyCreate.vue';

const routes = [
  { path: '/companies', component: Companies },
  { path: '/employees', component: Employees },
  { path: '/companies/create', component: CompanyCreate },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
