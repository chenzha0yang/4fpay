import axios from 'axios'
import {
  Message,
  MessageBox
} from 'element-ui'
import store from '@/store'
import router from '@/router'
import {
  getToken,
  setToken
} from '@/utils/auth'
import Cookies from 'js-cookie'
import i18n from './../lang/index'

// 创建axios实例
// window.configs = {
//   net_url: 'http://admin-t.com:84/admin' //打包注释
// }

const service = axios.create({
  baseURL: window.configs.net_url,
  timeout: 15000
})

var MessageBoxFlag = true

// request拦截器
service.interceptors.request.use(config => {
  if (store.getters.token) {
    config.headers['C-Token'] = getToken('C-Token') || ''
    config.headers['R-Token'] = getToken('R-Token') || ''
  }
  config.headers['Lang'] = Cookies.get('language') || 'zh'
  return config
}, error => {
  Promise.reject(error)
})

// respone拦截器
service.interceptors.response.use(
  response => {
    /**
     * code为非200是抛错 可结合自己业务进行修改
     */
    const res = response.data
    const cToken = res['C-Token']
    const rToken = res['R-Token']
    if (cToken && rToken) {
      setToken(cToken, 'C-Token', true)
      setToken(rToken, 'R-Token', true)
    }

    if (
      res.code === 1013 ||
      res.code === 1019 ||
      res.code === 1033 ||
      res.code === 1050 ||
      res.code === 1066 ||
      res.code === 1093 ||
      res.code === 1103 ||
      res.code === 1007 ||
      res.code === 2008
    ) {
      if (router.history.current.path === '/login') {
        Message({
          showClose: true,
          message: res.msg,
          type: 'error',
          duration: 3 * 1000
        })
      } else {
        if (MessageBoxFlag) {
          MessageBoxFlag = false

          MessageBox.confirm(res.msg, i18n.messages[i18n.locale].alertMsg.confirm, {
            confirmButtonText: i18n.messages[i18n.locale].alertMsg.reLogin,
            cancelButtonText: i18n.messages[i18n.locale].alertMsg.cancel,
            type: 'warning'
          }).then(() => {
            store.dispatch('FedLogOut').then(() => {
              // location.reload() // 为了重新实例化vue-router对象 避免bug SUCCESS
            }).finally(() => {
              MessageBoxFlag = true
            })
          })
        }

      }
      return Promise.reject('error')
    } else if (
      res.code === 403 ||
      res.code === 422 ||
      res.code > 1000 &&
      res.code < 2000) {
      Message({
        showClose: true,
        message: res.msg,
        type: 'error',
        duration: 3 * 1000
      })
      return Promise.reject(response)
    }
    return response.data
  },
  error => {
    Message({
      showClose: true,
      message: i18n.messages[i18n.locale].alertMsg.networkError,
      type: 'error',
      duration: 3 * 1000
    })
    return Promise.reject(error)
  }
)

export default service
