import './bootstrap';
import { createApp } from 'vue';

const app = createApp({});

/* contenido para las rutas*/

import Login from './vue/public/Login.vue';
app.component('login-vue', Login);

import Signup from './vue/public/Signup.vue';
app.component('signup-vue', Signup);

import Dashboard from './vue/admin/Dashboard.vue';
app.component('dashboard-vue', Dashboard);

import Usuarios from './vue/admin/Usuario.vue';
app.component('users-vue', Usuarios);

import Permisos from './vue/admin/Permisos.vue';
app.component('permisos-vue', Permisos);

import Roles from './vue/admin/Roles.vue';
app.component('roles-vue', Roles);

import Respaldos from './vue/admin/Respaldos.vue';
app.component('respaldos-vue', Respaldos);

import Password from './vue/admin/CambiasPassword.vue';
app.component('password-vue', Password);


app.mount('#app');
