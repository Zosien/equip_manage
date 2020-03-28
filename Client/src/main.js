import Vue from 'vue'
import App from './App.vue'
import router from './router'
import './assets/css/global.css'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import axios from 'axios'
import JSEncrypt from 'jsencrypt'

axios.defaults.baseURL = 'http://db.com/api'
// axios.interceptors.request.use(config => {
//   config.headers.token = window.sessionStorage.getItem('token')
//   return config
// })
Vue.prototype.$http = axios
Vue.prototype.$jse = JSEncrypt

Vue.config.productionTip = false
Vue.use(ElementUI)
new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
