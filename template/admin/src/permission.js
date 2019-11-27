import router from './router'
import store from './store'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import {
  getToken,
  getQueryVariable
} from '@/utils/auth' // getToken from cookie

NProgress.configure({
  showSpinner: false
})

const whiteList = ['/login', '/authredirect'] // no redirect whitelist

router.beforeEach((to, from, next) => {
  NProgress.start() // start progress bar\
  const key = getQueryVariable('key')
  if (getToken()) { // determine if there has token
    if (to.path === '/login') {
      next({
        path: '/'
      })
      NProgress.done()
    } else {
      if (store.getters.userName) {
        next()
      } else {
        store.dispatch('GetUserInfo').then(res => { // 拉取用户信息
          if (key) {
            window.location.href = '/'
          } else {
            next({ ...to,
              replace: true
            })
          }
        })
      }
    }
  } else {
    if (key) {
      store.dispatch('DirectLogin', key).then(res => {
        window.location.href = '/'
      }).catch(() => {
        NProgress.done()
      })
    } else {
      /* has no token*/
      if (whiteList.indexOf(to.path) !== -1) { // 在免登录白名单，直接进入
        next()
      } else {
        next('/login') // 否则全部重定向到登录页
        NProgress.done() // if current page is login will not trigger afterEach hook, so manually handle it
      }
    }
  }
})

router.afterEach(() => {
  NProgress.done() // finish progress bar
})
