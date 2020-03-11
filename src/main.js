import Vue from 'vue'
import App from './App.vue'
import Home from './Home.vue'
import Vocabulary from './Vocabulary.vue'
import VueMaterial from 'vue-material'
import 'vue-material/dist/vue-material.min.css'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

Vue.use(VueMaterial)

Vue.config.productionTip = false

const routes = [
  { path: '/', component: Home },
  { path: '/vocabulary', name: 'vocabulary', component: Vocabulary }
]

const router = new VueRouter({
  routes // short for `routes: routes`
})

new Vue({
  render: h => h(App),
  router
}).$mount('#app')
