import request from '@/utils/request'
import url from './url'

// ===============================================================
// 银行列表管理 api
// ===============================================================

// =============================  入款银行管理 ================================== //
// 查询
export function searchIncomeBank(params) {
  return request({
    url: url.incomeBank,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 新增
export function addIncomeBank(params) {
  return request({
    url: url.incomeBank,
    method: 'post',
    data: {
      ...params
    }
  })
}

// 修改
export function editIncomeBank(params) {
  return request({
    url: url.incomeBank,
    method: 'put',
    data: {
      ...params
    }
  })
}

// 删除
export function deleteIncomeBank(params) {
  return request({
    url: url.incomeBank,
    method: 'delete',
    data: {
      ...params
    }
  })
}


// =============================  出款银行管理 ================================== //

// 查询
export function searchOutcomeBank(params) {
  return request({
    url: url.outcomeBank,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 添加
export function addOutcomeBank(params) {
  return request({
    url: url.outcomeBank,
    method: 'post',
    data: {
      ...params
    }
  })
}

// 修改
export function editOutcomeBank(params) {
  return request({
    url: url.outcomeBank,
    method: 'put',
    data: {
      ...params
    }
  })
}

// 删除
export function deleteOutcomeBank(params) {
  return request({
    url: url.outcomeBank,
    method: 'delete',
    data: {
      ...params
    }
  })
}
