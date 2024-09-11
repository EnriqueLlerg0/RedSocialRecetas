import './bootstrap';
import './fontawesome6';

import '../scss/app.scss';
import '../css/app.css'
import "../../node_modules/bootstrap/scss/bootstrap.scss";
import * as bootstrap from 'bootstrap';

import {createApp} from 'vue';

const app = createApp({});

import router from "./router/index";

window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest'
};

//login components
// import PortalComponent from "./components/portal/layout/PortalComponent";
// app.component('portal-component', PortalComponent);

import ExampleComponent from "./components/ExampleComponent";
import Vue from "./components/Vue.vue";

app.component('Vue', Vue);

app.use(router);
app.mount('#app');
