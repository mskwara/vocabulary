import Vue from 'vue'
import App from './App.vue'
import Home from './Home.vue'
import Vocabulary from './Vocabulary.vue'
import Test from './Test.vue'
import Stats from './Stats.vue'
import LoginRegister from './LoginRegister.vue'
import God from './God.vue'
import VueMaterial from 'vue-material'
import 'vue-material/dist/vue-material.min.css'
import VueRouter from 'vue-router'
import VueResource from 'vue-resource';
Vue.use(VueResource);
import VueGraph from 'vue-graph';
 
Vue.use(VueGraph);

import 'bootstrap/dist/css/bootstrap.css'

Vue.use(VueRouter)

Vue.use(VueMaterial)

Vue.config.productionTip = false
Vue.http.options.root = '/api';

const routes = [
  { path: '/', name: 'god', component: God },
  { path: '/app', name: 'app', component: App,
    children: [
      { path: '/home', name: 'home', component: Home },
      { path: '/vocabulary', name: 'vocabulary', component: Vocabulary },
      { path: '/test', name: 'test', component: Test },
      { path: '/stats', name: 'stats', component: Stats },
      
    ]
  },
  { path: '/login', name: 'login', component: LoginRegister },
]

const router = new VueRouter({
  routes // short for `routes: routes`
})

new Vue({
  render: h => h(God),
  router
}).$mount('#app')
