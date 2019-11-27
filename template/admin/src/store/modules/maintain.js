import arrEdit from '@/utils/arrEdit'
import {
  searchMaintain,
  putMaintain,
  ownType
} from '@/api/maintain'

const maintain = {
  state: {
    // 查询
    maintainList: [],
    maintainCount: 0,
  },
  getters: {
    // 查询
    maintainList: state => state.maintainList,
    maintainCount: state => state.maintainCount,


  },
  mutations: {
    // 查询
    GET_MAINTAIN_LIST: (state, maintainList) => {
      state.maintainList = maintainList
    },

    GET_MAINTAIN_COUNT: (state, maintainCount) => {
      state.maintainCount = maintainCount
    },
    // 修改
    PUT_MAINTAIN_STATE: (state, params) => {
      const {
        current,
        data,
        name,
        type
      } = params

      const arrName = name + 'List',
        arrCount = name + 'Count'

      arrEdit({
        state,
        arrName,
        type,
        newInfo: data,
        current,
        arrCount
      })
    }
  },
  actions: {

    // 本地修改
    _putMaintain: ({
      commit
    }, params) => {
      commit('PUT_MAINTAIN_STATE', params)
    },

    // 查询
    searchMaintain: ({
      commit
    }, params) => {
      return searchMaintain(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_MAINTAIN_LIST', data)
        commit('GET_MAINTAIN_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },

    // 编辑
    putMaintain: ({
      commit
    }, params) => {
      return putMaintain(params.data).then((rps) => {
        const msg = rps.msg
        commit('PUT_MAINTAIN_STATE', params)
        return {
          success: true,
          msg
        }
      })
    },
    // // 添加
    ownType: ({
      commit
    }, params) => {
      return ownType(params).then((rps) => {
        const msg = rps.msg
        const data = rps.data
        return {
          success: true,
          msg,
          data
        }
      })
    }
  }
}

export default maintain
