// import arrEdit from '@/utils/arrEdit'
import {
  searchLoginLog,
  searchControlLog,
  searchDispatchLog,
  searchCallbackLog,
  searchOrderLog,
  searchErrorLogsF,
  searchErrorLogsA,
  searchRequestLog,
  // searchApiLogs
} from '@/api/logSet'

const logSet = {
  state: {
    // 查询登陆日志
    loginLogList: [],
    loginLogCount: 0,

    // 查询操作日志
    controlLogList: [],
    controlLogCount: 0,


    // 查询下发日志
    dispatchLogList: [],
    dispatchLogCount: 0,

    // 查询回调日志
    callbackLogList: [],
    callbackLogCount: 0,

    // 订单日志
    orderList: '',

    // 前台错误日志
    errorLogsFront: '',

    // 后台台错误日志
    errorLogsAdmin: '',

    //请求日志
    requestLogList: [],
    requestLogCount: 0,

    // API日志
    // apiLogList: [],
    // apiLogCount: 0
  },
  getters: {
    // 查询登陆日志
    loginLogList: state => state.loginLogList,
    loginLogCount: state => state.loginLogCount,

    // 查询操作日志
    controlLogList: state => state.controlLogList,
    controlLogCount: state => state.controlLogCount,

    // 查询下发日志
    dispatchLogList: state => state.dispatchLogList,
    dispatchLogCount: state => state.dispatchLogCount,

    // 查询回调日志
    callbackLogList: state => state.callbackLogList,
    callbackLogCount: state => state.callbackLogCount,

    // 查询登陆日志
    requestLogList: state => state.requestLogList,
    requestLogCount: state => state.requestLogCount,

    // 订单日志
    orderList: state => state.orderList,

    errorLogsFront: state => state.errorLogsFront,

    errorLogsAdmin: state => state.errorLogsAdmin


    // api日志
    // apiLogList: state => state.apiLogList,
    // apiLogCount: state => state.apiLogCount
  },
  mutations: {
    // 查询登陆日志
    GET_LOGINLOG_LIST: (state, loginLogList) => {
      state.loginLogList = loginLogList
    },

    GET_LOGINLOG_COUNT: (state, loginLogCount) => {
      state.loginLogCount = loginLogCount
    },

    // 查询操作日志
    GET_CONTROLLOG_LIST: (state, controlLogList) => {
      state.controlLogList = controlLogList
    },

    GET_CONTROLLOG_COUNT: (state, controlLogCount) => {
      state.controlLogCount = controlLogCount
    },

    // 查询下发日志
    GET_DISPATCHLOG_LIST: (state, dispatchLogList) => {
      state.dispatchLogList = dispatchLogList
    },

    GET_DISPATCHLOG_COUNT: (state, dispatchLogCount) => {
      state.dispatchLogCount = dispatchLogCount
    },

    // 查询回调日志
    GET_CALLBACKLOG_LIST: (state, callbackLogList) => {
      state.callbackLogList = callbackLogList
    },

    GET_CALLBACKLOG_COUNT: (state, callbackLogCount) => {
      state.callbackLogCount = callbackLogCount
    },

    // 查询登陆日志
    GET_REQUESTLOG_LIST: (state, requestLogList) => {
      state.requestLogList = requestLogList
    },

    GET_REQUESTLOG_COUNT: (state, requestLogCount) => {
      state.requestLogCount = requestLogCount
    },

    // 订单日志
    GET_ORDER_LIST: (state, orderList) => {
      state.orderList = orderList
    },

    // 前台错误日志
    GET_ERR_FRONT: (state, errorLogsFront) => {
      state.errorLogsFront = errorLogsFront
    },

    // 后台错误日志
    GET_ERR_ADMIN: (state, errorLogsAdmin) => {
      state.errorLogsAdmin = errorLogsAdmin
    },

    // api日志
    // GET_APILOG_LIST: (state, apiLogList) => {
    //   state.apiLogList = apiLogList
    // },

    // GET_APILOG_COUNT: (state, apiLogCount) => {
    //   state.apiLogCount = apiLogCount
    // }
  },
  actions: {

    // 查询登陆日志
    searchLoginLog: ({
      commit
    }, params) => {
      return searchLoginLog(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_LOGINLOG_LIST', data)
        commit('GET_LOGINLOG_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 查询操作日志
    searchControlLog: ({
      commit
    }, params) => {
      return searchControlLog(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_CONTROLLOG_LIST', data)
        commit('GET_CONTROLLOG_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 查询下发日志
    searchDispatchLog: ({
      commit
    }, params) => {
      return searchDispatchLog(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_DISPATCHLOG_LIST', data)
        commit('GET_DISPATCHLOG_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 查询回调日志
    searchCallbackLog: ({
      commit
    }, params) => {
      return searchCallbackLog(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_CALLBACKLOG_LIST', data)
        commit('GET_APILOG_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 查询登陆日志
    searchRequestLog: ({
      commit
    }, params) => {
      return searchRequestLog(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_REQUESTLOG_LIST', data)
        commit('GET_REQUESTLOG_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },


    // API日志
    // searchApiLogs: ({
    //   commit
    // }, params) => {
    //   return searchApiLogs(params).then((rps) => {
    //     const data = rps.data
    //     const count = rps.count
    //     commit('GET_APILOG_LIST', data)
    //     commit('GET_CALLBACKLOG_COUNT', count)
    //     return {
    //       success: true,
    //       data,
    //       count
    //     }
    //   })
    // },

    // 查询回调日志
    searchOrderLog: ({
      commit
    }, params) => {
      return searchOrderLog(params).then((rps) => {
        const data = rps.data
        commit('GET_ORDER_LIST', data)
        return {
          success: true,
          data
        }
      })
    },
    // 前台错误日志
    searchErrorLogsF: ({
      commit
    }, params) => {
      return searchErrorLogsF(params).then((rps) => {
        const data = rps.data
        commit('GET_ERR_FRONT', data)
        return {
          success: true,
          data
        }
      })
    },
    // 后台错误日志
    searchErrorLogsA: ({
      commit
    }, params) => {
      return searchErrorLogsA(params).then((rps) => {
        const data = rps.data
        commit('GET_ERR_ADMIN', data)
        return {
          success: true,
          data
        }
      })
    }
  }
}

export default logSet
