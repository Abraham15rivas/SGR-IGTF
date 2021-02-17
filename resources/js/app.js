// Importar las configuraciones
require('./bootstrap');

// Asignar vue al objeto de ventana para que este disponible de manera global
window.Vue = require('vue').default;

// Definir los componentes
Vue.component('home-component', require('./components/HomeComponent.vue').default);
Vue.component('index-component', require('./components/admin/IndexComponent.vue').default);

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
});
