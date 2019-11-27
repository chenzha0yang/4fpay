import request from '@/utils/request'
import url from './url'

export function login(username, password, verification) {
  var vt = sessionStorage.getItem('vt')
  return request({
    url: url.login,
    method: 'post',
    data: {
      vt,
      username,
      password,
      verification
    }
  })
}

export function getUserInfo(token) {
  return request({
    url: url.getinfo,
    method: 'get',
    params: {
      token
    }
  })
}

export function logout() {
  return request({
    url: url.logout,
    method: 'post'
  })
}

export function httpEditUserPassword(params) {
  return request({
    url: url.editOwnPwd,
    method: 'put',
    data: {
      ...params
    }
  })
}



//测试方法


// 引入request组件，处理所有ajax请求

// 设置一个方法名为loginByUsername，传入的参数是账号及密码
// export function loginByUsername(query) {
//   // 获取token
//   return request({
//     url: '/login/login',
//     method: 'post',
//     params: query
//   })
// }
