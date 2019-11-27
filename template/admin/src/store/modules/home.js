import {
  getEcharts
} from '@/api/home'

const account = {
  state: {
    echartsData: {},
  },
  getters: {
    //  账号管理
    echartsData: state => state.echartsData,

  },
  mutations: {
    //权限管理
    SET_E_CHARTS: (state, echarts) => {
      echarts.reverse()
      // var obj = {}
      // echarts.forEach((ele, index) => {
      //   for (const k in ele) !index ? obj[k] = [ele[k]] : obj[k].push(ele[k])
      // })
      state.echartsData = echarts
    }

  },
  actions: {
    // 获取图表
    getEcharts: ({
      commit
    }, params) => {
      return getEcharts(params).then((rps) => {
        const data = rps.data
        commit('SET_E_CHARTS', data)
        return {
          success: true
        }
      })
    },

  }
}

export default account
