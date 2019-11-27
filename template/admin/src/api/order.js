import request from '@/utils/request'
import url from './url'

// ===============================================================
// 订单管理管理 api
// ===============================================================

// =============================  入款订单管理 ================================== //

// 入款查询
export function httpSearchInOrderList(params) {

  return request({
    url: url.inOrder,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 入款下发
export function sendInOrder(params) {
  return request({
    url: url.inOrder,
    method: 'post',
    data: {
      ...params
    }
  })
}

export function inOrderFind(params) {
  return request({
    url: url.inOrderFind,
    method: 'get',
    params: {
      ...params
    }
  })
}


// 平台线路查询
export function searchOrderClientName(params) {
  return request({
    url: url.orderClientName,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 商户类型查询
export function searhOrderConfigLists(params) {
  return request({
    url: url.orderConfigLists,
    method: 'get',
    params: {
      ...params
    }
  })
}



// =============================  出款订单管理 ================================== //

// 出款查询
export function httpSearchOutOrderList(params) {

  return request({
    url: url.outOrder,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 入款下发
export function sendOutOrder(params) {
  return request({
    url: url.outOrder,
    method: 'post',
    params: {
      ...params
    }
  })
}

export function outOrderFind(params) {
  return request({
    url: url.outOrderFind,
    method: 'get',
    params: {
      ...params
    }
  })
}
