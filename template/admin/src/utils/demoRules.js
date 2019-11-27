import {
  validateIp,
  isAccount,
  isNickname,
  isNumber,
  // valiSpecial,
  isSpace,
  validateAlphabets,
  validateURL,
  // valiSpecialChinese,
  validateEnglishAndNumber,
  valiChinese
} from './validate'
import i18n from '@/lang/index'

const account = (rule, value, callback) => {

  switch (isAccount(value)) {
    case 'isSpace':
      callback(new Error(i18n.messages[i18n.locale].valiMsg.spaceMsg))
      break
    case false:
      callback(new Error(i18n.messages[i18n.locale].valiMsg.accountMsg))
      break
    default:
      callback()
      break
  }
}
// const uName = (rule, value, callback) => {
//   switch (isNickname(value)) {
//     case 'isSpace':
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.spaceMsg))
//       break
//     case false:
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.nickNameWeak))
//       break
//     default:
//       callback()
//       break
//   }

// }

const state = (rule, value, callback) => {
  if (!value) callback(new Error(i18n.messages[i18n.locale].valiMsg.statusWeak))
  else callback()
}

const bankCode = (rule, value, callback) => {
  const reg = /^[0-9A-Za-z\u4e00-\u9fa5_\-]+$/;
  if (reg.test(value)) {
    callback()
  } else {
    callback(new Error(i18n.messages[i18n.locale].valiMsg.bankCode))
  }
}

const loginIp = (rule, value, callback) => {
  // value = String(value)
  if (value) {
    if (value[value.length - 1] === ',') value = value.slice(0, value.length - 1)

    var arr = value.split(',')

    for (let i = 0; i < arr.length; i++) {
      if (!validateIp(arr[i])) {
        callback(new Error(i18n.messages[i18n.locale].valiMsg.regIpMsg))
        return
      }
    }
  } else {
    callback(new Error(i18n.messages[i18n.locale].valiMsg.regIpMsg))
    return
  }
  callback()
}

const menuIds = (rule, value, callback) => {
  if (!value || !value.length) callback(new Error(i18n.messages[i18n.locale].valiMsg.menuIdsMsg))
  else callback()
}

const permissionIds = (rule, value, callback) => {
  if (!value || !value.length) callback(new Error(i18n.messages[i18n.locale].valiMsg.permIdsMsg))
  else callback()
}

const roleId = (rule, value, callback) => {
  if (!value) callback(new Error(i18n.messages[i18n.locale].valiMsg.roleMsg))
  else callback()
}

const payId = (rule, value, callback) => {
  if (!value) callback(new Error(i18n.messages[i18n.locale].valiMsg.payIdMsg))
  else callback()
}

const payIp = (rule, value, callback) => {
  if (!validateIp(value)) callback(new Error(i18n.messages[i18n.locale].valiMsg.IpMsg))
  else callback()
}

const clientId = (rule, value, callback) => {
  if (!value) callback(new Error(i18n.messages[i18n.locale].valiMsg.clientMsg))
  else callback()
}

// const transaction = (rule, value, callback) => {
//   switch (isNumber(value)) {
//     case 'isSpace':
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.spaceMsg))
//       break
//     case false:
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.moneyWeak))
//       break
//     default:
//       callback()
//       break
//   }
// }

const transaction_ = (rule, value, callback) => {
  switch (isNumber(value)) {
    case 'isSpace':
      callback(new Error(i18n.messages[i18n.locale].valiMsg.spaceMsg))
      break
    case false:
      callback(new Error(i18n.messages[i18n.locale].valiMsg.moneyWeak))
      break
    default:
      value ? callback() : callback(new Error(i18n.messages[i18n.locale].valiMsg.moneyWeak_))
      break
  }
}


const validatetopId = (rule, value, callback) => {
  if (!value) callback(new Error(i18n.messages[i18n.locale].valiMsg.topId))
  else callback()
}

// const label = (rule, value, callback) => {
//   const v = String(value)

//   if (valiSpecial(v) || isSpace(v)) {
//     callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
//   } else if (value === undefined || value === null || !value.length) {
//     callback(new Error(i18n.messages[i18n.locale].valiMsg.nameMsg))
//   } else {
//     callback()
//   }
// }

// const icon = (rule, value, callback) => {
//   const v = String(value)

//   if (valiSpecial(v) || isSpace(v)) {
//     callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
//   } else if (value === undefined || value === null || !value.length) {
//     callback(new Error(i18n.messages[i18n.locale].valiMsg.iconMsg))
//   } else {
//     callback()
//   }
// }

const path = (rule, value, callback) => {
  const reg = /^\w+((\/\w+)?)*$/g
  if (reg.test(value)) {
    callback()
  } else {
    callback(new Error(i18n.messages[i18n.locale].valiMsg.pathMsg))
  }
}

// const slug = (rule, value, callback) => {
//   const v = String(value)
//   if (valiSpecialChinese(v) || isSpace(v)) {
//     callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
//   } else if (value === undefined || value === null || !value.length) {
//     callback(new Error(i18n.messages[i18n.locale].valiMsg.slugMsg))
//   } else {
//     callback()
//   }
// }

// const agentId = (rule, value, callback) => {

//   switch (isNickname(value)) {
//     case 'isSpace':
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
//       break
//     case false:
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.agentIdMsg))
//       break
//     default:
//       callback()
//       break
//   }

// }
// const businessNum = (rule, value, callback) => {

//   switch (isNickname(value)) {
//     case 'isSpace':
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
//       break
//     case false:
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.agentIdMsg))
//       break
//     default:
//       callback()
//       break
//   }

// }


const method = (rule, value, callback) => {
  if (Array.isArray(value) && value.length) {
    callback()
  } else {
    callback(new Error(i18n.messages[i18n.locale].valiMsg.getTypeMsg))
  }
}

// +++
// 合法的URL
const url = (rule, value, callback) => {
  if (validateURL(value)) {
    callback()
  } else {
    if (valiChinese(value)) {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.ChineseMsg))
    } else {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.urlMsg))
    }
  }
}
// 选题的URL
const urlNull = (rule, value, callback) => {
  if (validateURL(value) || value === '') {
    callback()
  } else {
    if (valiChinese(value)) {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.ChineseMsg))
    } else {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.urlMsg))
    }
  }
}

// 必填的名称
const isString = (rule, value, callback) => {
  // console.log(value)
  // console.log(rule)
  if (isNickname(value)) {
    callback()
  } else {
    if (value === '') {
      callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField]))
    } else {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
    }
  }
}

// 必填英文的
const isEnglish = (rule, value, callback) => {
  console.log(value)
  if (validateAlphabets(value)) {
    callback()
  } else {
    if (value === '') {
      callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField]))
    } else if (isSpace(value)) {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
    } else {
      callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField + 'Msg']))
    }
  }
}

// 数组或者数字
const typeId = (rule, value, callback) => {
  // console.log(value)
  // console.log(rule.fullField)

  if ((Array.isArray(value) && value.length) || isNumber(value)) {
    callback()
  } else {
    if (!value || value.length === 0) {
      callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField]))
    } else {
      callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField + 'Msg'] || ''))
    }
  }
}

// 数字和英文
const isEnglishAndNumber = (rule, value, callback) => {

  // console.log(rule.fullField)

  if (validateEnglishAndNumber(value)) {
    callback()
  } else {
    if (value === '') {
      callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField]))
    } else {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
      // callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField + 'Msg']))
    }
  }
}

// 选填数字和英文
// const isEnglishAndNumberNull = (rule, value, callback) => {

//   // console.log(rule.fullField)
//   // console.log(value)
//   if (validateEnglishAndNumber(value) || value === '') {
//     callback()
//   } else {
//     if (valiChinese(value)) {
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.ChineseMsg))
//     } else {
//       callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
//     }
//   }
// }
// code
const payCode = (rule, value, callback) => {

  const arr = value.split(',')

  const reg = /([0-9])-([0-9a-zA-Z_@]+)/

  if (arr[arr.length - 1] === '') {
    arr.splice(arr.length - 1, 1)
  }

  for (const iterator of arr) {
    if (isSpace(iterator)) {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.spaceMsg))
      return
    }

    if (!reg.test(iterator)) {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.payCode))
      return
    }
  }
  callback()
}

// 必填的地址
// const isUrl = (rule, value, callback) => {

//   console.log(rule.fullField)

//   if (validateURL(value)) {
//     callback()
//   } else {
//     callback(new Error(i18n.messages[i18n.locale].valiMsg.url))
//   }
// }

// 选填的地址
const isUrlAndNull = (rule, value, callback) => {

  // console.log(rule.fullField)

  if (validateURL(value) || value === '') {
    callback()
  } else {
    callback(new Error(i18n.messages[i18n.locale].valiMsg.url))
  }
}

// 选填的名称
const isStringAndNull = (rule, value, callback) => {
  // console.log(rule.fullField)

  if (isNickname(value) || value === '') {
    callback()
  } else {
    callback(new Error(i18n.messages[i18n.locale].valiMsg.specialSymbol))
    // callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField]))
  }
}

// 非中文
const isChinese = (rule, value, callback) => {
  // console.log(rule.fullField)
  // console.log(value)
  if (valiChinese(value)) {
    callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField + 'Msg']))
  } else {
    if (value === '') {
      callback(new Error(i18n.messages[i18n.locale].valiMsg[rule.fullField]))
    } else if (isSpace(value)) {
      callback(new Error(i18n.messages[i18n.locale].valiMsg.spaceMsg))
    } else {
      callback()
    }
  }
}
const demoRules = {

  // 状态
  state: [{
    required: true,
    validator: state,
    trigger: 'blur'
  }],

  // 权限
  permissionIds: [{
    required: true,
    validator: permissionIds,
    trigger: 'blur'
  }],

  menuIds: [{
    required: true,
    validator: menuIds,
    trigger: 'blur'
  }],

  // 额度分配
  transaction: [{
    required: true,
    validator: transaction_,
    trigger: 'blur'
  }],

  // 上级
  topId: [{
    required: true,
    validator: validatetopId,
    trigger: 'blur'
  }],


  // 非空

  clientId: [{
    required: true,
    validator: clientId,
    trigger: 'blur'
  }],

  roleId: [{
    required: true,
    validator: roleId,
    trigger: 'blur'
  }],
  payId: [{
    required: true,
    validator: payId,
    trigger: 'blur'
  }],
  payIp: [{
    required: true,
    validator: payIp,
    trigger: 'blur'
  }],

  typeId: [{
    required: true,
    validator: typeId,
    trigger: 'blur'
  }],




  //
  confName: [{
    required: true,
    validator: isString,
    trigger: 'blur'
  }],

  confMod: [{
    required: true,
    validator: isEnglish,
    trigger: 'blur'
  }],

  payCode: [{
    required: false,
    validator: payCode,
    trigger: 'blur'
  }],

  dispensingUrl: [{
    required: false,
    validator: isUrlAndNull,
    trigger: 'blur'
  }],

  extendName: [{
    required: false,
    validator: isStringAndNull,
    trigger: 'blur'
  }],

  agentId: [{
    required: true,
    validator: isEnglishAndNumber,
    trigger: 'blur'
  }],

  siteUrl: [{
    required: false,
    validator: isUrlAndNull,
    trigger: 'blur'
  }],

  typeName: [{
    required: true,
    validator: isString,
    trigger: 'blur'
  }],

  englishName: [{
    required: true,
    validator: isEnglish,
    trigger: 'blur'
  }],

  userId: [{
    required: true,
    validator: typeId,
    trigger: 'blur'
  }],
  clientName: [{
    required: true,
    validator: isEnglish,
    trigger: 'blur'
  }],
  Secret: [{
    required: true,
    validator: isEnglishAndNumber,
    trigger: 'blur'
  }],
  clientUserId: [{
    required: true,
    validator: typeId,
    trigger: 'blur'
  }],
  businessNum: [{
    required: true,
    validator: isChinese,
    trigger: 'blur'
  }],
  callbackURL: [{
    required: true,
    validator: url,
    trigger: 'blur'
  }],

  // privateKey: [{
  //   required: false,
  //   validator: isEnglishAndNumberNull,
  //   trigger: 'blur'
  // }],

  // publicKey: [{
  //   required: false,
  //   validator: isEnglishAndNumberNull,
  //   trigger: 'blur'
  // }],

  merURL: [{
    required: false,
    validator: urlNull,
    trigger: 'blur'
  }],

  msgOne: [{
    required: true,
    validator: isChinese,
    trigger: 'blur'
  }],

  // md5Key: [{
  //   required: false,
  //   validator: isEnglishAndNumberNull,
  //   trigger: 'blur'
  // }],

  bankCode: [{
    required: true,
    validator: bankCode,
    trigger: 'blur'
  }],
  // 昵称
  uName: [{
    required: true,
    validator: isString,
    trigger: 'blur'
  }],
  msg: [{
    required: true,
    validator: isString,
    trigger: 'blur'
  }],
  // 账号
  account: [{
    required: true,
    validator: account,
    trigger: 'blur'
  }],

  // 登陆Ip
  loginIp: [{
    required: true,
    validator: loginIp,
    trigger: 'blur'
  }],
  slug: [{
    required: true,
    validator: isEnglishAndNumber,
    trigger: 'blur'
  }],


  label: [{
    required: true,
    validator: isString,
    trigger: 'blur'
  }],

  icon: [{
    required: true,
    validator: isChinese,
    trigger: 'change'
  }],
  path: [{
    required: true,
    validator: path,
    trigger: 'blur'
  }],

  method: [{
    required: true,
    validator: method,
    trigger: 'blur'
  }],

}

export default demoRules
