import request from '@/utils/request'
import url from './url'

export function getEcharts(params) {
  return request({
    url: url.echarts,
    method: 'get',
    params: {
      ...params
    }
  })
}
