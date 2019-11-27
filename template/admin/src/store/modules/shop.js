import arrEdit from '../../utils/arrEdit'
import {
  searchIncomeShop,
  editIncomeShop,
  addIncomeShop,
  deleteIncomeShop,

  searchOutcomeShop,
  addOutcomeShop,
  editOutcomeShop,
  deleteOutcomeShop,

} from '@/api/shop'

const shop = {
  state: {
    inShop: [],
    inShopCount: 0,
    outShop: [],
    outShopCount: 0
  },
  getters: {
    inShop: state => state.inShop,
    inShopCount: state => state.inShopCount,

    outShop: state => state.outShop,
    outShopCount: state => state.outShopCount
  },
  mutations: {
    GET_INSHOP_COUNT: (state, inShopCount) => {
      state.inShopCount = inShopCount
    },
    GET_INSHOP_LIST: (state, inShop) => {
      state.inShop = inShop
    },

    GET_OUTSHOP_COUNT: (state, outShopCount) => {
      state.outShopCount = outShopCount
    },
    GET_OUTSHOP_LIST: (state, outShop) => {
      state.outShop = outShop
    },
    PUT_INSHOP_NEW_INFO: (state, params) => {

      const {
        type,
        name,
        current,
        data
      } = params

      arrEdit({
        state,
        arrName: name,
        type,
        newInfo: data,
        current,
        arrCount: name + 'Count'
      })
    },
    // DELETE_MANAGE: (state, index) => {
    //   state.inShop.splice(index, 1)
    // }

  },
  actions: {

    // 本地修改
    _putShopList: ({
      commit
    }, params) => {
      commit('PUT_INSHOP_NEW_INFO', params)
    },

    // ------------------------  入款商户  ------------------------- //

    // 搜索入款商户
    searchInShopList: ({
      commit
    }, params) => {
      return searchIncomeShop(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_INSHOP_LIST', data)
        commit('GET_INSHOP_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 修改入款商户
    editManageChild: ({
      commit
    }, params) => {
      return editIncomeShop(params.data).then((rps) => {
        params.data.updatedTime = rps.data.updatedTime
        commit('PUT_INSHOP_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // 新增入款商户
    addManageChild: ({
      commit
    }, params) => {
      return addIncomeShop(params.data).then((rps) => {
        params.data = rps.data
        commit('PUT_INSHOP_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // 删除入款商户
    deleteManageChild: ({
      commit
    }, params) => {
      return deleteIncomeShop(params.data).then((rps) => {
        commit('PUT_INSHOP_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // ------------------------  出款商户  ------------------------- //

    //搜索
    searchOutShopList: ({
      commit
    }, params) => {
      return searchOutcomeShop(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_OUTSHOP_COUNT', count)
        commit('GET_OUTSHOP_LIST', data)
        return {
          success: true,
          data,
          count
        }
      })
    },

    //添加
    addOutShopChild: ({
      commit
    }, params) => {
      return addOutcomeShop(params.data).then((rps) => {
        params.data = rps.data[0]
        commit('PUT_INSHOP_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    //修改
    editOutShopChild: ({
      commit
    }, params) => {
      return editOutcomeShop(params.data).then((rps) => {
        params.data.updatedTime = rps.data.updatedTime
        commit('PUT_INSHOP_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // 删除
    deleteOutShopChild: ({
      commit
    }, params) => {
      return deleteOutcomeShop(params.data).then((rps) => {
        commit('PUT_INSHOP_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },
  }
}

export default shop
