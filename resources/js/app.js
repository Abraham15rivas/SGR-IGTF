// Importar las configuraciones
require('./bootstrap');

// Asignar vue al objeto de ventana para que este disponible de manera global
window.Vue = require('vue').default;

// Importar librerias
import store from './store'
import VueExcelEditor from 'vue-excel-editor'
import HighchartsVue from 'highcharts-vue'

// Definir los componentes globales
Vue.component('welcome-component', require('./components/WelcomeComponent.vue').default);
Vue.component('home-component', require('./components/HomeComponent.vue').default);
Vue.component('index-component', require('./components/admin/IndexComponent.vue').default);
Vue.component('show-component', require('./components/profile/ShowComponent.vue').default);
Vue.component('change-password-component', require('./components/profile/ChangePasswordComponent.vue').default);
Vue.component('show-transaction-component', require('./components/report/ShowTransactionComponent.vue').default);
Vue.component('show-xml-component', require('./components/report/ShowFileXmlComponent.vue').default);
Vue.component('calendar-component', require('./components/manager/calendar/CalendarComponent.vue').default);
Vue.component('statistics-component', require('./components/manager/audit/StatisticsComponent.vue').default);
Vue.component('user-activity-component', require('./components/manager/audit/UserActivityComponent.vue').default);
Vue.component('user-list-component', require('./components/admin/users/UserListComponent.vue').default);
Vue.component('status-component', require('./components/admin/status/StatusComponent.vue').default);

// Components de las librerias
Vue.use(VueExcelEditor)
Vue.use(HighchartsVue)

/* 
    Obtener y asignar un atributo id con el nombre "app" a la primera etiqueta <div> 
    de la plantilla AdminLTE con el propósito de poder renderizar los componentes de
    Vue en las vistas del proyecto, puesto que esta etiqueta envuelve al resto
*/
let app_wrapper = document.querySelector("div")
app_wrapper.setAttribute("id", "app")

// Instancia de vue
const app = new Vue({
    el: '#app',
    store
});
