// import arrEdit from '@/utils/arrEdit'
import {
  httpSearchInOrderList,
  sendInOrder,
  inOrderFind,
  outOrderFind,
  httpSearchOutOrderList,
  sendOutOrder,
  searchOrderClientName,
  searhOrderConfigLists
} from '@/api/order'

const order = {
  state: {
    //  出入款列表
    inOrders: [],
    inOrdersCount: 0,

    orderClientName: [], //平台线路
    orderConfigLists: [], //商户类型

    // 出款
    outOrders: [],
    outOrdersCount: 0,

    outOrderClientID: '',
  },
  getters: {
    // 入款
    inOrders: state => state.inOrders,
    inOrdersCount: state => state.inOrdersCount,
    orderClientName: state => state.orderClientName, //平台线路

    orderConfigLists: state => state.orderConfigLists, //商户类型

    // 出款
    outOrders: state => state.outOrders,
    outOrdersCount: state => state.outOrdersCount,

    outOrderClientID: state => state.outOrderClientID,
  },
  mutations: {
    // 入款
    GET_INORDER_COUNT: (state, inOrdersCount) => {
      state.inOrdersCount = inOrdersCount
    },
    GET_INORDER_LIST: (state, inOrders) => {

      state.inOrders = inOrders

    },
    GET_ORDER_CLIENTNAME: (state, orderClientName) => {
      state.orderClientName = orderClientName
    },
    GET_ORDER_CONFIG_LIST: (state, orderConfigLists) => {
      state.orderConfigLists = orderConfigLists
    },

    // 出款
    GET_OUTORDER_COUNT: (state, outOrdersCount) => {
      state.outOrdersCount = outOrdersCount
    },
    GET_OUTORDER_LIST: (state, outOrders) => {
      state.outOrders = outOrders
    },

  },
  actions: {
    // 入款
    // 搜索入款列表
    searchInOrderList: ({
      commit
    }, params) => {
      return httpSearchInOrderList(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_INORDER_COUNT', count)
        commit('GET_INORDER_LIST', data)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 入款下发
    sendInOrder: ({
      commit
    }, params) => {
      return sendInOrder(params).then((rps) => {
        const {
          data,
          msg
        } = rps
        return {
          success: true,
          data,
          msg
        }

      })

    },
    //  平台线路
    searchOrderClientName: ({
      commit
    }) => {

      return searchOrderClientName().then((rps) => {
        const data = rps.data
        commit('GET_ORDER_CLIENTNAME', data)
        return {
          success: true,
          data
        }
      })
    },
    // 商户类型
    searhOrderConfigLists: ({
      commit
    }) => {
      return searhOrderConfigLists().then((rps) => {
        const data = rps.data
        commit('GET_ORDER_CONFIG_LIST', data)
        return {
          success: true,
          data
        }
      })
    },
    // 出款
    //搜索出款列表
    searchOutOrderList: ({
      commit
    }, params) => {
      //  console.log(params);
      return httpSearchOutOrderList(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_OUTORDER_COUNT', count)
        commit('GET_OUTORDER_LIST', data)
        return {
          success: true,
          data,
          count
        }
      })
    },
    // 出款下发
    sendOutOrder: ({
      commit
    }, params) => {
      return sendOutOrder(params).then((rps) => {
        const {
          data,
          msg
        } = rps
        return {
          success: true,
          data,
          msg
        }

      })
    },
    inOrderFind: ({
      commit
    }, params) => {
      return inOrderFind(params).then((rps) => {
        const {
          data,
          msg
        } = rps
        return {
          success: true,
          data,
          msg
        }
      })
    },

    outOrderFind: ({
      commit
    }, params) => {
      return outOrderFind(params).then((rps) => {
        const {
          data,
          msg
        } = rps
        return {
          success: true,
          data,
          msg
        }
      })
    }
  }
}

export default order
