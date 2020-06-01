import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '../components/Login.vue'
import Home from '../components/Home.vue'
import Welcome from '../components/Welcome.vue'
import UserList from '../components/user/List.vue'
import UserAdd from '../components/user/Add.vue'
import Cookies from 'js-cookie'
Vue.use(VueRouter)

const router = new VueRouter({
  routes: [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    {
      path: '/home',
      component: Home,
      redirect: '/welcome',
      children: [
        { path: '/welcome', component: Welcome },
        {
          path: '/user/list',
          component: UserList,
          meta: {
            requireAuth: true
          }
        },
        {
          path: '/user/add',
          component: UserAdd,
          meta: {
            requireAuth: true
          }
        }
      ]
    }
  ]
})
// 导航守卫，可以用axios拦截器代替
router.beforeEach((to, from, next) => {
  const token = Cookies.get('token')
  if (to.meta.requireAuth) { // 判断该路由是否需要登录权限
    if (token) {
      next()
    } else {
      next({
        path: '/login',
        query: { redirect: to.fullPath } // 将跳转的路由path作为参数，登录成功后跳转到该路由
      })
    }
  } else {
    next()
  }
})

export default router
