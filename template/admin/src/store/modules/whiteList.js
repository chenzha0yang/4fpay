import arrEdit from '@/utils/arrEdit'
import {
  searchWhiteList,
  putWhiteList,
  addWhiteList
} from '@/api/whiteList'

const whiteList = {
  state: {
    //  白名单列表
    whiteList: [],
    whiteListCount: 0,

  },
  getters: {

    whiteList: state => state.whiteList,
    whiteListCount: state => state.whiteListCount,

  },
  mutations: {

    GET_WHITELIST_COUNT: (state, whiteListCount) => {
      state.whiteListCount = whiteListCount
    },
    GET_WHITELIST_LIST: (state, whiteList) => {
      state.whiteList = whiteList
    },

    PUT_WHITELIST_STATE: (state, params) => {

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
        arrCount: name + 'Count'
      })
    }

  },
  actions: {

    // 本地修改
    _putWhiteList: ({
      commit
    }, params) => {
      commit('PUT_WHITELIST_STATE', params)
    },

    // 查询
    searchWhiteList: ({
      commit
    }, params) => {
      return searchWhiteList(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_WHITELIST_COUNT', count)
        commit('GET_WHITELIST_LIST', data)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 编辑
    putWhiteList: ({
      commit
    }, params) => {
      return putWhiteList(params.data).then((rps) => {
        const msg = rps.msg
        params.data.updatedAt = rps.data.updatedTime
        commit('PUT_WHITELIST_STATE', params)
        return {
          success: true,
          msg
        }
      })
    },
    // 添加
    addWhiteList: ({
      commit
    }, params) => {
      return addWhiteList(params.data).then((rps) => {
        const msg = rps.msg
        params.data = rps.data
        commit('PUT_WHITELIST_STATE', params)
        return {
          success: true,
          msg
        }
      })
    }
  }
}
export default whiteList
