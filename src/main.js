import Vue from 'vue'
import App from './App.vue'
import Home from './Home.vue'
import Vocabulary from './Vocabulary.vue'
import Test from './Test.vue'
import VueMaterial from 'vue-material'
import 'vue-material/dist/vue-material.min.css'
import VueRouter from 'vue-router'
import VueResource from 'vue-resource';
Vue.use(VueResource);

import 'bootstrap/dist/css/bootstrap.css'

Vue.use(VueRouter)

Vue.use(VueMaterial)

Vue.config.productionTip = false
Vue.http.options.root = '/api';

const routes = [
  { path: '/', name: 'home', component: Home },
  { path: '/vocabulary', name: 'vocabulary', component: Vocabulary },
  { path: '/test', name: 'test', component: Test },
]

const router = new VueRouter({
  routes // short for `routes: routes`
})

new Vue({
  render: h => h(App),
  router
}).$mount('#app')
