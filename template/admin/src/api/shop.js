import request from '@/utils/request'
import url from './url'

// ===============================================================
// 商户管理 api
// ===============================================================

// =============================  入款商户管理 ================================== //
// 查询
export function searchIncomeShop(params) {
  return request({
    url: url.incomeShop,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 添加
export function addIncomeShop(params) {
  return request({
    url: url.incomeShop,
    method: 'post',
    data: {
      ...params
    }
  })
}

// 修改
export function editIncomeShop(params) {
  return request({
    url: url.incomeShop,
    method: 'put',
    data: {
      ...params
    }
  })
}

// 删除
export function deleteIncomeShop(params) {
  return request({
    url: url.incomeShop,
    method: 'delete',
    params: {
      ...params
    }
  })
}


// =============================  出款商户管理 ================================== //
// 查询
export function searchOutcomeShop(params) {
  return request({
    url: url.outcomeShop,
    method: 'get',
    params: {
      ...params
    }
  })
}


// 添加
export function addOutcomeShop(params) {
  return request({
    url: url.outcomeShop,
    method: 'post',
    data: {
      ...params
    }
  })
}

// 修改
export function editOutcomeShop(params) {
  return request({
    url: url.outcomeShop,
    method: 'put',
    data: {
      ...params
    }
  })
}

// 删除
export function deleteOutcomeShop(params) {
  return request({
    url: url.outcomeShop,
    method: 'delete',
    params: {
      ...params
    }
  })
}

