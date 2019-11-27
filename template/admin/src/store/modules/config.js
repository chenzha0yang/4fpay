import arrEdit from '@/utils/arrEdit'
import {
  /* ----------三方列表---------- */
  httpSearchTripartiteList, //三方查询
  addTripartiteList, //新增三方
  editTripartiteList, //修改三方
  httpSearchTopTwenty, // 首页的三方

  /* ----------通知地址---------- */
  searchNotifyList, // 通知地址查询（超管用）
  searchNotifyAgent, //通知地址查询（代理用）
  editNotifyAgent, //通知地址修改
  addNotifyAgent,//通知地址新增

  /* ----------支付方式---------- */
  searchPayType, //支付方式查询
  addPayTypeList,
  editPayTypeList,
  httpPayTypeList, // 支付下拉
  // deletePayTypeList,

  /* ----------客户接口---------- */
  searchClientList, //客户接口查询
  editClientList,
  deleteClientList,
  addClientList,

  /* ----------客户ID---------- */
  getCientUsersList,

} from '@/api/config'

const config = {
  state: {
    //  三方列表
    tripartList: [],
    tripartListCount: 0,
    tripartItem: {},
    tripartItemIndex: 0,

    //  通知列表
    notifyList: [],
    notifyListCount: 0,

    // 支付方式
    payTypeList: [],
    payTypeCount: 0,

    // 客户接口
    apiClientsList: [],
    apiClientsCount: 0,

    clientUsersList: [],

    // 代理列表
    apiAgentsList: [],
    apiAgentsCount: 0,
  },
  getters: {
    //  三方列表
    tripartList: state => state.tripartList,
    tripartListCount: state => state.tripartListCount,

    tripartItem: state => state.tripartItem,
    tripartItemIndex: state => state.tripartItemIndex,

    //  通知地址
    notifyList: state => state.notifyList,
    notifyListCount: state => state.notifyListCount,

    // 支付方式
    payTypeList: state => state.payTypeList,
    payTypeCount: state => state.payTypeCount,

    // 客户接口
    apiClientsList: state => state.apiClientsList,
    apiClientsCount: state => state.apiClientsCount,
    clientUsersList: state => state.clientUsersList,

    // 代理列表
    apiAgentsList: state => state.apiAgentsList,
    apiAgentsCount: state => state.apiAgentsCount
  },
  mutations: {
    //  三方列表
    // 查询
    GET_TRIPART_LIST: (state, tripartList) => {
      state.tripartList = tripartList
    },
    GET_TRIPART_COUNT: (state, tripartListCount) => {
      state.tripartListCount = tripartListCount
    },
    SET_TRIPAR_ITEM: (state, params) => {
      state.tripartItem = params.item
      state.tripartItemIndex = params.index
    },

    //  通知地址
    GET_NOTIFY_LIST: (state, notifyList) => {
      state.notifyList = notifyList
    },
    GET_NOTIFY_COUNT: (state, notifyListCount) => {
      state.notifyListCount = notifyListCount
    },

    //  支付方式
    GET_PAYTYPE_LIST: (state, payTypeList) => {
      state.payTypeList = payTypeList
    },
    GET_PAYTYPE_COUNT: (state, payTypeCount) => {
      state.payTypeCount = payTypeCount
    },

    //客户接口
    GET_APICLIENTS_LIST: (state, apiClientsList) => {
      state.apiClientsList = apiClientsList
    },
    GET_APICLIENTS_COUNT: (state, apiClientsCount) => {
      state.apiClientsCount = apiClientsCount
    },

    //客户ID
    GET_CIENTUSERS_LIST: (state, clientUsersList) => {
      state.clientUsersList = clientUsersList
    },

    SET_MEMBER_NEW_INFO: (state, params) => {
      const {
        current,
        data,
        name,
        type
      } = params

      // console.log(4,params)

      arrEdit({
        state,
        arrName: name + 'List',
        type,
        newInfo: data,
        current,
        arrCount: name + 'Count'
      })
    }

  },
  actions: {
    // 本地修改
    _putConfig: ({
      commit
    }, params) => {
      commit('SET_MEMBER_NEW_INFO', params)
    },
    // ------------------------  三方列表  ------------------------- //

    // 查询
    searchTripartList: ({
      commit
    }, params) => {
      return httpSearchTripartiteList(params).then(rps => {
        const data = rps.data
        const count = rps.count
        commit('GET_TRIPART_LIST', data)
        commit('GET_TRIPART_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 首页的查询
    searchTopTwenty: ({
      commit
    }) => {
      return httpSearchTopTwenty().then(rps => {
        const data = rps.data
        return {
          success: true,
          data
        }
      })
    },

    // 新增
    addTripartiteList: ({
      commit
    }, params) => {
      return addTripartiteList(params.data).then((rps) => {
        params.data = rps.data[0]
        commit('SET_MEMBER_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // 修改
    editTripartiteList: ({
      commit
    }, params) => {
      return editTripartiteList(params.data).then((rps) => {

        commit('SET_MEMBER_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // ------------------------  通知地址  ------------------------- //

    // 查询 （超管用）
    searchNotifyList: ({
      commit
    }, params) => {
      return searchNotifyList(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_NOTIFY_LIST', data)
        commit('GET_NOTIFY_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 查询（代理用）
    searchNotifyAgent: ({
      commit
    }, params) => {
      return searchNotifyAgent(params).then((rps) => {
        const data = rps.data
        // const count = rps.count
        // commit('GET_NOTIFY_LIST', data)
        // commit('GET_NOTIFY_COUNT', count)
        return {
          success: true,
          data
          // count
        }
      })
    },

    //修改
    editNotifyAgent: ({
      commit
    }, params) => {
      return editNotifyAgent(params.data).then((rps) => {
        commit('SET_MEMBER_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },


    //新增
    addNotifyList: ({
      commit
    }, params) => {
      return addNotifyAgent(params.data).then((rps) => {
         params.data = rps.data[0]
        commit('SET_MEMBER_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // ------------------------  支付方式  ------------------------- //

    // 查询
    searchPayType: ({
      commit
    }, params) => {
      return searchPayType(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_PAYTYPE_LIST', data)
        commit('GET_PAYTYPE_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 添加
    addPayTypeList: ({
      commit
    }, params) => {
      return addPayTypeList(params.data).then((rps) => {
        params.data = rps.data[0]
        commit('SET_MEMBER_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // 修改
    editPayTypeList: ({
      commit
    }, params) => {
      return editPayTypeList(params.data).then((rps) => {
        commit('SET_MEMBER_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // 支付下拉
    getPayTypeLis: ({
      commit
    }) => {
      return httpPayTypeList().then(rps => {
        const data = rps.data
        return {
          success: true,
          data
        }
      })
    },

    // 删除
    // deletePayTypeList: ({
    //   commit
    // }, params) => {
    //   return deletePayTypeList(params.params).then((rps) => {
    //     commit('SET_MEMBER_NEW_INFO', params)
    //     return {
    //       success: true,
    //       rps
    //     }
    //   })
    // },

    // ------------------------  客户接口  ------------------------- //

    // 查询
    searchClientList: ({
      commit
    }, params) => {
      return searchClientList(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_APICLIENTS_LIST', data)
        commit('GET_APICLIENTS_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 新增
    addClientList: ({
      commit
    }, params) => {
      return addClientList(params.data).then((rps) => {
        params.data = rps.data[0]
        commit('SET_MEMBER_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // 修改
    editClientList: ({
      commit
    }, params) => {
      return editClientList(params.data).then((rps) => {
        params.data.updatedAt = rps.data.updatedTime
        commit('SET_MEMBER_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // 删除
    deleteClientList: ({
      commit
    }, params) => {
      return deleteClientList(params.data).then((rps) => {
        commit('SET_MEMBER_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },


    // ------------------------  客户ID  ------------------------- //

    getCientUsersList: ({
      commit
    }) => {
      return getCientUsersList().then((rps) => {
        const data = rps.data
        commit('GET_CIENTUSERS_LIST', data)
        return {
          success: true,
          data
        }
      })
    },
    setTripartItem: ({
      commit
    }, params) => {
      return new Promise((resolve, reject) => {
        commit('SET_TRIPAR_ITEM', params)
        resolve()
      })
    }
  }
}

export default config
