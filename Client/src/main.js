import Vue from 'vue'
import App from './App.vue'
import router from './router'
import './assets/css/global.css'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import axios from 'axios'
import JSEncrypt from 'jsencrypt'
import common from './assets/js/common.js'
import Cookies from 'js-cookie'
axios.defaults.baseURL = 'http://db.com/api'

var isTokenRefreshing = false // 刷新token的请求不拦截
axios.interceptors.request.use(config => {
  config.headers.Authorization = Cookies.get('token')
  var exp = window.sessionStorage.getItem('exp')
  var time = (new Date()).getTime()
  if (exp && exp - time <= 50000 && exp - time > 0 && !isTokenRefreshing) { // 即将过期，刷新token
    return new Promise(() => {
      setTimeout(() => {
        isTokenRefreshing = true
        axios.get('/refresh_token').then(res => {
          console.log(res)
          sessionStorage.clear()
          sessionStorage.setItem('token', res.data.token)
          sessionStorage.setTime('exp', res.data.expire + new Date().getTime())
          isTokenRefreshing = false
          console.log('拦截器')
        }).catch(err => {
          console.log(err.msg)
          if (err.error_code === 1001) {
            sessionStorage.clear()
          }
        })
      }, 2000, config)
    })
  } else if (exp && time >= exp) { // 已过期，重新登陆
    window.sessionStorage.clear()
    this.$router.push('/login')
  }
  // else if (!exp && this.$router.path !== '/login') {
  //   console.log('test')
  //   this.$router.push('/login')
  // }
  return config
})

axios.interceptors.response.use(response => {
  return response
}, err => {
  if (err.response) {
    if (err.response.status === 401) {
      localStorage.removeItem('token')
      router.push('/login')
    }
  }
  return Promise.reject(err.response)
}
)
const jse = new JSEncrypt()
if (!localStorage.getItem('key')) {
  this.$http
    .get('/key')
    .then(res => {
      const PublicKeyEncode = res.data.key
      const PublicKey = window.atob(PublicKeyEncode)
      localStorage.setItem('key', PublicKey)
    })
    .catch(err => {
      this.$message.error(err.data.msg)
    })
}
jse.setPublicKey(localStorage.getItem('key'))
Vue.prototype.$http = axios
Vue.prototype.$jse = jse
Vue.prototype.$base = common
Vue.prototype.$cookie = Cookies
window.Cookie = Cookies

Vue.config.productionTip = false
Vue.use(ElementUI)
new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
