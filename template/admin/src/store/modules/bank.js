import arrEdit from '../../utils/arrEdit'
import {
  searchIncomeBank,
  addIncomeBank,
  editIncomeBank,
  deleteIncomeBank,

  searchOutcomeBank,
  addOutcomeBank,
  editOutcomeBank,
  deleteOutcomeBank

} from '@/api/bank'

const bank = {
  state: {
    //  入款银行
    incomeBankList: [],
    incomeBankCount: 0,

    // 出款银行
    outcomeBankList: [],
    outcomeBankCount: 0,

    autoPayId: null
  },
  getters: {
    // 入款
    incomeBankList: state => state.incomeBankList,
    incomeBankCount: state => state.incomeBankCount,


    // 出款
    outcomeBankList: state => state.outcomeBankList,
    outcomeBankCount: state => state.outcomeBankCount,

    autoPayId: state => state.autoPayId,

  },
  mutations: {
    // 入款

    GET_INCOMEBANK_LIST: (state, incomeBankList) => {
      state.incomeBankList = incomeBankList
    },

    GET_INCOMEBANK_COUNT: (state, incomeBankCount) => {
      state.incomeBankCount = incomeBankCount
    },


    // 出款
    GET_OUTCOMEBANK_LIST: (state, outcomeBankList) => {
      state.outcomeBankList = outcomeBankList
    },
    GET_OUTCOMEBANK_COUNT: (state, outcomeBankCount) => {
      state.outcomeBankCount = outcomeBankCount
    },

    GET_AUTOPAYID: (state, index) => {
      state.autoPayId = index
    },

    SET_BANK_NEW_INFO: (state, params) => {

      const {
        current,
        data,
        name,
        type
      } = params

      arrEdit({
        state,
        arrName: name,
        type,
        newInfo: data,
        current,
        // arrCount: name + 'Count'
      })

    }

  },
  actions: {

    // ------------------------  入款银行  ------------------------- //


    _putbank: ({
      commit
    }, params) => {
      commit('SET_BANK_NEW_INFO', params)
    },
    // 列表
    searchIncomeBank: ({
      commit
    }, params) => {
      return searchIncomeBank(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_INCOMEBANK_LIST', data)
        commit('GET_INCOMEBANK_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 新增
    addIncomeBank: ({
      commit
    }, params) => {
      return addIncomeBank(params.data).then((rps) => {
        return {
          success: true,
          rps
        }
      })
    },

    // 修改
    editIncomeBank: ({
      commit
    }, params) => {
      return editIncomeBank(params.data).then((rps) => {
        params.data.updatedAt = rps.data.updatedTime
        commit('SET_BANK_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    deleteIncomeBank: ({
      commit
    }, params) => {
      return deleteIncomeBank(params.data).then((rps) => {
        commit('SET_BANK_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // ------------------------  出款银行  ------------------------- //

    // 列表
    searchOutcomeBank: ({
      commit
    }, params) => {
      return searchOutcomeBank(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_OUTCOMEBANK_LIST', data)
        commit('GET_OUTCOMEBANK_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 新增
    addOutcomeBank: ({
      commit
    }, params) => {
      return addOutcomeBank(params.data).then((rps) => {
        return {
          success: true,
          rps
        }
      })
    },

    // 修改
    editOutcomeBank: ({
      commit
    }, params) => {
      return editOutcomeBank(params.data).then((rps) => {
        params.data.updatedAt = rps.data.updatedTime
        commit('SET_BANK_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },

    // 删除
    deleteOutcomeBank: ({
      commit
    }, params) => {
      return deleteOutcomeBank(params.data).then((rps) => {
        commit('SET_BANK_NEW_INFO', params)
        return {
          success: true,
          rps
        }
      })
    },
    TriparJumpBank: ({
      commit
    }, params) => {
      commit('GET_AUTOPAYID', params)
    }
  }
}

export default bank
