import request from '@/utils/request'
import url from './url'

// ===============================================================
// 维护管理 api
// ===============================================================

// =============================  维护管理 ================================== //
// 查询
export function searchMaintain(params) {

  return request({
    url: url.maintain,
    method: 'get',
    params: {
      ...params
    }
  })
}

export function putMaintain(params) {
  return request({
    url: url.maintain,
    method: 'put',
    data: {
      ...params
    }
  })
}

export function ownType(params) {
  return request({
    url: url.ownType,
    method: 'get',
    params: {
      ...params
    }
  })
}
