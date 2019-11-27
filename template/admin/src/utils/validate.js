/**
 * Created by jiachenpan on 16/11/18.
 */

export function isvalidUsername(str) {
  const valid_map = ['admin', 'editor']
  return valid_map.indexOf(str.trim()) >= 0
}


// 验证空格
export function isSpace(value) {
  var reg = /\s+/g
  return reg.test(value)
}

// 验证账号，只能 大小写英文、数字、_，必须有英文
export function isAccount(value) {
  if (isSpace(value)) return 'isSpace'

  const reg = /([a-zA-Z]+)[0-9a-zA-Z_]*$/
  const regLnegth = /^[0-9A-Za-z_]{5,12}$/

  return reg.test(value) && regLnegth.test(value)
}

// 验证昵称 +
export function isNickname(value) {
  // if (isSpace(value)) return 'isSpace'

  const reg = /^[0-9A-Za-z\u4e00-\u9fa5]+$/
  // const regLnegth = /^[0-9A-Za-z\u4e00-\u9fa5]{1,}$/

  return reg.test(value)
}

// 验证数字
export function isNumber(value) {

  const reg = /^[0-9]+$/

  return reg.test(value)
}

// 验证密码 必须同时有英文、数字，或者符号，但不能有中文
export function isPassword(value) {
  if (isSpace(value)) return 'isSpace'
  const reg = /(?=.*[0-9])(?=.*[a-zA-Z]).{6,12}/
  const regChinese = /^[\u4e00-\u9fa5]/

  return reg.test(value) && !regChinese.test(value)
}

// 特殊符号验证
export function valiSpecial(str) {
  const regEn = /[`~!@#$%^&*()-_.,+<>?:"{}\/;'[\]]/im
  const regCn = /[·！#￥（——）：；“”‘、，|《。》？、【】[\]]/im
  return regEn.test(str) || regCn.test(str) || str.indexOf(' ') > -1
}

// 特殊符号验证&&中文
export function valiSpecialChinese(str) {
  const regEn = /[`~!@#$%^&*()-.,+<>?:"{}\/;'[\]]/im
  const regCn = /[·！#￥（——）：；“”‘、，|《。》？、【】[\]]/im
  const regChinese = /^[\u4e00-\u9fa5]/
  return regEn.test(str) || regCn.test(str) || str.indexOf(' ') > -1 || regChinese.test(str)
}

// // 特殊符号验证&&中文 除了 _ 和 -
export function valiSpecialChinese_(str) {
  const regEn = /[`~!@#$%^&*().,+<>?:"{}\/;'[\]]/im
  const regCn = /[·！#￥（——）：；“”‘、，|《。》？、【】[\]]/im
  const regChinese = /^[\u4e00-\u9fa5]/
  return regEn.test(str) || regCn.test(str) || str.indexOf(' ') > -1 || regChinese.test(str)
}

// 中文
export function valiChinese(str) {
  const regCh = /[\u4E00-\u9FA5\uF900-\uFA2D]/
  return regCh.test(str)
}

/* 合法uri*/
export function validateURL(textval) {
  const urlregex = /^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/
  return urlregex.test(textval)
}

/* 小写字母*/
export function validateLowerCase(str) {
  const reg = /^[a-z]+$/
  return reg.test(str)
}

/* 大写字母*/
export function validateUpperCase(str) {
  const reg = /^[A-Z]+$/
  return reg.test(str)
}

/* 大小写字母*/
export function validateAlphabets(str) {
  const reg = /^[A-Za-z]+$/
  return reg.test(str)
}

export function validateEnglishAndNumber(str) {
  const reg = /^[A-Za-z0-9]+$/
  return reg.test(str)
}

/**
 * validate email
 * @param email
 * @returns {boolean}
 */
export function validateEmail(email) {
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  return re.test(email)
}
// IP
export function validateIp(str) {
  const reg = /^(?:(?:2[0-4][0-9]\.)|(?:25[0-5]\.)|(?:1[0-9][0-9]\.)|(?:[1-9][0-9]\.)|(?:[0-9]\.)){3}(?:(?:2[0-9][0-9])|(?:25[0-5])|(?:1[0-9][0-9])|(?:[1-9][0-9])|(?:[0-9]))$/
  return reg.test(str)
}
// 密码强度
export function VerifPassword(string) {
  return checkStrong(string)

  function CharMode(iN) {
    if (iN >= 48 && iN <= 57) {
      return 1
    }
    if (iN >= 65 && iN <= 90) {
      return 2
    }
    if (iN >= 97 && iN <= 122) {
      return 4
    }
    return 8
  }

  function bitTotal(num) {
    var modes = 0
    for (var i = 0; i < 4; i++) {
      if (num & 1) modes++
      num >>>= 1
    }
    return modes
  }

  function checkStrong(sPW) {
    if (sPW.length < 6) {
      return 0
    }
    var Modes = 0
    for (var i = 0; i < sPW.length; i++) {
      Modes |= CharMode(sPW.charCodeAt(i))
    }
    return bitTotal(Modes)
  }
}
