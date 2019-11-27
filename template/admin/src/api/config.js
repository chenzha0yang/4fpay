import request from '@/utils/request'
import url from './url'

// 配置管理 api

// =============================  三方列表 ==================================

// 查询
export function httpSearchTripartiteList(params) {
  return request({
    url: url.TripartiteList,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 首页的查询
export function httpSearchTopTwenty() {
  return request({
    url: url.topTwenty,
    method: 'get'
  })
}

// 新增
export function addTripartiteList(params) {
  return request({
    url: url.TripartiteList,
    method: 'post',
    data: {
      ...params
    }
  })
}

// 修改
export function editTripartiteList(params) {
  return request({
    url: url.TripartiteList,
    method: 'put',
    data: {
      ...params
    }
  })
}


// =============================  通知地址==================================


// 查询（超管）
export function searchNotifyList(params) {
  return request({
    url: url.NotifyList,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 查询
export function searchNotifyAgent(params) {
  return request({
    url: url.NotifAgent,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 修改
export function editNotifyAgent(params) {
  return request({
    url: url.NotifAgent,
    method: 'put',
    data: {
      ...params
    }
  })
}


// 修改
export function addNotifyAgent(params) {
  return request({
    url: url.NotifAgent,
    method: 'post',
    data: {
      ...params
    }
  })
}


// =============================  支付方式==================================
// 查询

export function searchPayType(params) {
  return request({
    url: url.payType,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 支付下拉
export function httpPayTypeList() {
  return request({
    url: url.payTypeList,
    method: 'get'
  })
}

// 添加
export function addPayTypeList(params) {
  return request({
    url: url.payType,
    method: 'post',
    params: {
      ...params
    }
  })
}

// 修改
export function editPayTypeList(params) {
  return request({
    url: url.payType,
    method: 'put',
    data: {
      ...params
    }
  })
}

// 删除
// export function deletePayTypeList(params) {
//   return request({
//     url: url.payType,
//     method: 'delete',
//     params: {
//       ...params
//     }
//   })
// }


// =============================  客户接口==================================
// 查询

export function searchClientList(params) {
  return request({
    url: url.ClientList,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 修改
export function editClientList(params) {
  return request({
    url: url.ClientList,
    method: 'put',
    params: {
      ...params
    }
  })
}

// 删除
export function deleteClientList(params) {
  return request({
    url: url.ClientList,
    method: 'delete',
    params: {
      ...params
    }
  })
}

// 新增
export function addClientList(params) {
  return request({
    url: url.ClientList,
    method: 'post',
    params: {
      ...params
    }
  })
}

// =============================  客户ID ==================================

export function getCientUsersList(params) {
  return request({
    url: url.clientUsersList,
    method: 'get',
    params: {
      ...params
    }
  })
}
