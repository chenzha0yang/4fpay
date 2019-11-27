import request from '@/utils/request'
import url from './url'

// ===============================================================
// 白名单管理 api
// ===============================================================

// =============================  白名单管理 ================================== //
// 查询
export function searchWhiteList(params) {
  return request({
    url: url.whiteList,
    method: 'get',
    params: {
      ...params
    }
  })
}
export function putWhiteList(params) {
  return request({
    url: url.whiteList,
    method: 'put',
    data: {
      ...params
    }
  })
}
export function addWhiteList(params) {
  return request({
    url: url.whiteList,
    method: 'post',
    data: {
      ...params
    }
  })
}
