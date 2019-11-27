import request from '@/utils/request'
import url from './url'

// ===============================================================
// 日志管理 api
// ===============================================================

// =============================  登陆日志 ================================== //
// 查询
export function searchLoginLog(params) {

  return request({
    url: url.loginLog,
    method: 'get',
    params: {
      ...params
    }
  })
}


// =============================  操作日志 ================================== //
// 查询
export function searchControlLog(params) {
  return request({
    url: url.operateLog,
    method: 'get',
    params: {
      ...params
    }
  })
}


// =============================  下发日志 ================================== //
// 查询
export function searchDispatchLog(params) {

  return request({
    url: url.dispatchLog,
    method: 'get',
    params: {
      ...params
    }
  })
}

// =============================  回调日志 ================================== //
// 查询
export function searchCallbackLog(params) {

  return request({
    url: url.callbackLog,
    method: 'get',
    params: {
      ...params
    }
  })
}


// =============================  请求日志 ================================== //
// 查询
export function searchRequestLog(params) {

  return request({
    url: url.requestLog,
    method: 'get',
    params: {
      ...params
    }
  })
}

// =============================  订单日志 ================================== //
// 查询
export function searchOrderLog(params) {

  return request({
    url: url.orderLogs,
    method: 'get',
    params: {
      ...params
    }
  })
}
// =============================  前台错误日志 ================================== //
// 查询
export function searchErrorLogsF(params) {

  return request({
    url: url.errorLogsF,
    method: 'get',
    params: {
      ...params
    }
  })
}
// =============================  后台错误日志 ================================== //
// 查询
export function searchErrorLogsA(params) {

  return request({
    url: url.errorLogsA,
    method: 'get',
    params: {
      ...params
    }
  })
}
// =============================  API日志 ================================== //
// 查询
// export function searchApiLogs(params) {

//   return request({
//     url: url.apiLogs,
//     method: 'get',
//     params: {
//       ...params
//     }
//   })
// }
