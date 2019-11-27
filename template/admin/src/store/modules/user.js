import {
  login,
  logout,
  getUserInfo,
  httpEditUserPassword
} from '@/api/login'

import {
  getToken,
  setToken,
  removeToken
} from '@/utils/auth'
import BgImages from '@/assets/images/user.jpg'

import i18n from '@/lang/index'

import {
  MessageBox
} from 'element-ui'

function setUserState(commit, data) {
  commit('SET_ROLES', data.roles)
  commit('SET_TOKEN', data.token)
  commit('SET_UNAME', data.userName)
  commit('SET_NICKNAME', data.nickName)
  commit('SET_VIEW', data.isView)
  commit('SET_LOGIN_IP', data.lastLoginIP)
  commit('SET_LOGIN_TIME', data.lastLoginTime)
  commit('SET_CLIENT', data.client)
}

const user = {
  state: {
    token: getToken(),
    userName: '',
    nickName: '',
    roles: {},
    avatar: BgImages,
    isView: {},
    lastLoginTime: '',
    lastLoginIP: '',
    client: 0
  },

  mutations: {
    SET_TOKEN: (state, token) => {
      state.token = token
    },
    SET_UNAME: (state, userName) => {
      state.userName = userName
    },
    SET_NICKNAME: (state, nickName) => {
      state.nickName = nickName
    },
    SET_ROLES: (state, roles) => {
      state.roles = roles
    },
    SET_CLIENT: (state, client) => {
      state.client = client
    },
    SET_VIEW: (state, isView) => {
      state.isView = isView
    },
    SET_LOGIN_TIME: (state, time) => {
      state.lastLoginTime = time
    },
    SET_LOGIN_IP: (state, ip) => {
      state.lastLoginIP = ip
    },
  },

  actions: {
    // 用户名登录
    Login({
      commit,
      dispatch
    }, userInfo) {
      const username = userInfo.username.trim()
      return new Promise((resolve, reject) => {
        login(username, userInfo.password, userInfo.verification).then(response => {
          const data = response.data
          const token = data.token
          setToken(token)
          sessionStorage.setItem('data-Info', JSON.stringify(data))

          setUserState(commit, data)

          console.log(data)

          dispatch({
            type: 'GenerateRoutes',
            menu: data.menu
          })

          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 登出
    LogOut({
      commit,
      state
    }) {
      return new Promise((resolve, reject) => {
        logout(state.token).then(() => {
          commit('SET_TOKEN', '')
          removeToken()
          sessionStorage.clear('data-Info')
          location.reload()
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 一键登陆
    DirectLogin({
      commit,
      dispatch
    }, key) {
      return new Promise((resolve, reject) => {
        directLogin(key).then(res => {
          const data = res.data
          const token = data.token
          setToken(token)
          sessionStorage.setItem('data-Info', JSON.stringify(data))
          resolve(res)
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 前端 登出
    FedLogOut({
      commit
    }) {
      return new Promise(resolve => {
        commit('SET_TOKEN', '')
        removeToken()
        sessionStorage.clear('data-Info')
        window.location.href = '/'
        location.reload()
        resolve()
      })
    },

    // 刷新页面时获取账号信息
    GetUserInfo({
      commit,
      dispatch
    }, role) {
      return new Promise((resolve, reject) => {
        const dataInfo = JSON.parse(sessionStorage.getItem('data-Info'))
        if (dataInfo) {

          setUserState(commit, dataInfo)

          dispatch({
            type: 'GenerateRoutes',
            menu: dataInfo.menu
          })
          resolve(dataInfo)
        } else {

          const token = getToken()

          if (token) {
            getUserInfo(token).then(res => {
              const data = res.data
              sessionStorage.setItem('data-Info', JSON.stringify(data))

              setUserState(commit, data)

              dispatch({
                type: 'GenerateRoutes',
                menu: data.menu
              })

              resolve(dataInfo)
            }).catch(error => {
              reject(error)
            })
          } else {
            MessageBox.alert(i18n.messages[i18n.locale].alertMsg.reLogin,
              i18n.messages[i18n.locale].alertMsg.confirm, {
                confirmButtonText: i18n.messages[i18n.locale].alertMsg.confirm,
                type: 'warning'
              }).then(() => {
              dispatch('FedLogOut')
            })
          }
        }
      })
    },


    UidEditUserPassword({
      commit,
      dispatch
    }, params) {
      return httpEditUserPassword(params).then(rps => {
        return {
          success: true,
          data: rps
        }
      })
    },

    // 设置时间
    // setTime({
    //   commit
    // }, time) {
    //   commit('SET_TIME', time)
    // },
  }
}

export default user
