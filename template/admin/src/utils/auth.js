import Cookies from 'js-cookie'

const TokenKey = 'Admin-Token'

export function getToken(kenKey = TokenKey) {
  return Cookies.get(kenKey)
}

export function setToken(token, kenKey = TokenKey, flag = false) {
  if (flag) {
    let exp=new Date();
    // exp.setTime(exp.getTime() + 12*60 * 60 * 1000) // 12小时掉线
    exp = new Date(exp.getTime() + 12 * 60 * 60 * 1000)
    return Cookies.set(kenKey, token, {
      expires: exp
    })
  } else {
    return Cookies.set(kenKey, token)
  }
}

export function removeToken() {
  Cookies.remove('C-Token')
  Cookies.remove('R-Token')
  return Cookies.remove(TokenKey)
}

export function setStorage(info) {
  for (const key in info) {
    sessionStorage.setItem(key, info[key])
  }
}

export function getStorage(info) {
  for (var key in info) {
    info[key] = sessionStorage.getItem(key)
  }
  return info
}

export function getQueryVariable(variable) {
  var query = window.location.search.substring(1)

  if (query === '') {
    query = window.location.hash
    var index = query.indexOf('?')
    query = query.slice(index + 1)
  }
  var vars = query.split('&')
  for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split('=')
    if (pair[0] === variable) {
      return pair[1]
    }
  }
  return false
}
