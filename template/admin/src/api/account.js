import request from '@/utils/request'
import url from './url'

// ===============================================================
// 权限管理 api
// ===============================================================

// =============================  账号管理 ================================== //
// 查询
export function searchAccountSet(params) {

  return request({
    url: url.accountSet,
    method: 'get',
    params: {
      ...params
    }
  })
}
// 编辑
export function putAccountSet(params) {

  return request({
    url: url.accountSet,
    method: 'put',
    data: {
      ...params
    }
  })
}
// 新增
export function addAccountSet(params) {
  return request({
    url: url.accountSet,
    method: 'post',
    data: {
      ...params
    }
  })
}
// 修改密码
export function PeditPassword(params) {

  return request({
    url: url.PeditPassword,
    method: 'put',
    data: {
      ...params
    }
  })
}

// =============================  角色管理 ================================== //
// 查询
export function searchRoleSet(params) {

  return request({
    url: url.roleSet,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 新增
export function addRoleSet(params) {
  return request({
    url: url.roleSet,
    method: 'post',
    data: {
      ...params
    }
  })
}


// 角色修改
export function putRoleSet(params) {
  return request({
    url: url.roleSet,
    method: 'put',
    data: {
      ...params
    }
  })
}
// 获取角色选择
export function getRoleName() {
  return request({
    url: url.getRoleName,
    method: 'get'
  })
}


// =============================  权限管理 ================================== //
// 查询
export function searchPermissionSet(params) {

  return request({
    url: url.permissionSet,
    method: 'get',
    params: {
      ...params
    }
  })
}
// 新增
export function addPermissionSet(params) {
  return request({
    url: url.permissionSet,
    method: 'post',
    data: {
      ...params
    }
  })
}

// 删除
export function delPermissionSet(params) {
  return request({
    url: url.permissionSet,
    method: 'delete',
    params: {
      ...params
    }
  })
}

// 修改
export function putPermissionSet(params) {
  return request({
    url: url.permissionSet,
    method: 'put',
    data: {
      ...params
    }
  })
}

// =============================  菜单管理 ================================== //
// 查询
export function searchMenuSet(params) {
  return request({
    url: url.menuSet,
    method: 'get',
    params: {
      ...params
    }
  })
}

// 新增
export function addMenuSet(params) {
  return request({
    url: url.menuSet,
    method: 'post',
    data: {
      ...params
    }
  })
}

// 删除
export function delMenuSet(params) {
  return request({
    url: url.menuSet,
    method: 'delete',
    params: {
      ...params
    }
  })
}

// 修改
export function putMenuSet(params) {
  return request({
    url: url.menuSet,
    method: 'put',
    data: {
      ...params
    }
  })
}

export function PgetMenus() {
  return request({
    url: url.getMenus,
    method: 'get'
  })
}

export function PgetPermissions() {
  return request({
    url: url.getPermissions,
    method: 'get'
  })
}

export function PclientTree() {
  return request({
    url: url.clientTree,
    method: 'get'
  })
}
