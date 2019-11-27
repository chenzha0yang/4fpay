import arrEdit from '@/utils/arrEdit'

import {
  PgetMenus,
  PgetPermissions,
  // PclientTree,
  PeditPassword, // 修改密码
  searchAccountSet, // 账号管理
  putAccountSet, // 账号编辑
  addAccountSet, // 账号添加

  getRoleName,
  searchRoleSet, // 角色管理
  putRoleSet, // 角色编辑
  addRoleSet, // 角色添加

  searchPermissionSet, //权限管理
  addPermissionSet, // 添加权限
  delPermissionSet, // 删除权限
  putPermissionSet, // 编辑权限

  searchMenuSet, // 搜索菜单
  addMenuSet, // 新增菜单
  delMenuSet, // 删除菜单
  putMenuSet // 编辑菜单

} from '@/api/account'

import {
  delList,
  putList,
  addList
} from '@/utils/manipulateList'

const account = {
  state: {
    //  账号管理
    accountSetList: [],
    accountSetCount: 0,

    //  角色管理
    roleSetList: [],
    roleSetCount: 0,

    // 权限管理
    permissionSetList: [],
    permissionSetCount: 0,

    // 菜单管理
    menuSetList: [],
    menuSetCount: 0,

  },
  getters: {
    //  账号管理
    accountSetList: state => state.accountSetList,
    accountSetCount: state => state.accountSetCount,

    //  角色管理
    roleSetList: state => state.roleSetList,
    roleSetCount: state => state.roleSetCount,

    // 权限管理
    permissionSetList: state => state.permissionSetList,
    permissionSetCount: state => state.permissionSetCount,

    // 菜单管理
    menuSetList: state => state.menuSetList,
    menuSetCount: state => state.menuSetCount,


  },
  mutations: {

    PUT_ACCOUNTSET_STATE: (state, params) => {
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
    },

    //  账号管理
    GET_ACCOUNTSET_LIST: (state, accountSetList) => {
      state.accountSetList = accountSetList
    },
    GET_ACCOUNTSET_COUNT: (state, accountSetCount) => {
      state.accountSetCount = accountSetCount
    },
    // 账号编辑
    // GET_ACCOUNTSET_PUT: (state, data) => {
    //   const newData = data.data
    //   const index = data.current
    //   state.accountSetList[index] = Object.assign(state.accountSetList[index], newData)
    // },
    // 账号添加
    // GET_ACCOUNTSET_ADD: (state, data) => {
    //   const newData = data[0]
    //   state.accountSetList.unshift(newData)
    // },
    //  角色管理
    GET_ROLESET_LIST: (state, roleSetList) => {
      state.roleSetList = roleSetList
    },
    GET_ROLESET_COUNT: (state, roleSetCount) => {
      state.roleSetCount = roleSetCount
    },
    // 角色编辑
    // GET_ROLESET_PUT: (state, data) => {

    //   const {
    //     index,
    //     menuIds,
    //     permissionIds
    //   } = data

    //   var newData = data.data
    //   newData.menuIds = menuIds
    //   newData.permissionIds = permissionIds

    //   state.roleSetList[index] = Object.assign(state.roleSetList[index], newData)
    // },
    // 角色添加
    // GET_ROLESET_ADD: (state, data) => {
    //   // const newData = data[0]
    //   // state.roleSetList.unshift(newData)
    //   // reverse
    // },

    //权限管理
    GET_PERMISSIONSET_LIST: (state, permissionSetList) => {
      state.permissionSetList = permissionSetList
    },
    GET_PERMISSIONSET_COUNT: (state, permissionSetCount) => {
      state.permissionSetCount = permissionSetCount
    },

    // 权限删除
    GET_PERMISSIONSET_DEL: (state, data) => {
      state.permissionSetList = delList(data, state.permissionSetList)
    },
    // 权限修改
    GET_PERMISSIONSET_PUT: (state, data) => {
      state.permissionSetList = putList(data, state.permissionSetList, 'parentId')
    },
    // 权限添加
    GET_PERMISSIONSET_ADD: (state, data) => {
      const newData = data[0]
      if (newData.parentId === 0) {
        state.permissionSetList.push(newData)
      } else {
        state.permissionSetList = addList(newData, state.permissionSetList, 'parentId')
      }
    },

    //菜单管理
    GET_MENUSET_LIST: (state, menuSetList) => {
      state.menuSetList = menuSetList
    },
    GET_MENUSET_COUNT: (state, menuSetCount) => {
      state.menuSetCount = menuSetCount
    },
    // 菜单删除
    GET_MENUSET_DEL: (state, data) => {
      state.menuSetList = delList(data, state.menuSetList)
    },
    // 菜单修改
    GET_MENUSET_PUT: (state, data) => {
      state.menuSetList = putList(data, state.menuSetList, 'parentId')
    },
    // 菜单添加
    GET_MENUSET_ADD: (state, data) => {
      const newData = data[0]
      if (newData.parentId === 0) {
        state.menuSetList.push(newData)
      } else {
        state.menuSetList = addList(newData, state.menuSetList, 'parentId')
      }
    }
  },
  actions: {

    // 本地修改
    _putAccount: ({
      commit
    }, params) => {
      commit('PUT_ACCOUNTSET_STATE', params)
    },

    //账号管理

    searchAccountSet: ({
      commit
    }, params) => {
      return searchAccountSet(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_ACCOUNTSET_LIST', data)
        commit('GET_ACCOUNTSET_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },
    // 账号添加
    addAccountSet: ({
      commit
    }, params) => {
      return addAccountSet(params.data).then((rps) => {
        const msg = rps.msg
        params.data = rps.data
        // commit('GET_ACCOUNTSET_ADD', data)
        commit('PUT_ACCOUNTSET_STATE', params)
        return {
          success: true,
          msg
        }
      })
    },
    // 账号编辑
    putAccountSet: ({
      commit
    }, params) => {
      return putAccountSet(params.data).then((rps) => {
        const msg = rps.msg
        params.data.updatedTime = rps.data.updatedTime

        commit('PUT_ACCOUNTSET_STATE', params)
        return {
          success: true,
          msg
        }
      })
    },

    getRoleName: ({
      commit
    }, ) => {
      return getRoleName().then(rps => {
        const data = rps.data
        return {
          success: true,
          data
        }
      })
    },

    //角色管理
    searchRoleSet: ({
      commit
    }, params) => {
      return searchRoleSet(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_ROLESET_LIST', data)
        commit('GET_ROLESET_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },
    // 编辑
    putRoleSet: ({
      commit
    }, params) => {

      return putRoleSet(params.data).then((rps) => {
        const msg = rps.msg
        params.data.updatedTime = rps.data.updatedTime

        commit('PUT_ACCOUNTSET_STATE', params)
        return {
          success: true,
          msg
        }
      })
    },
    // 添加
    addRoleSet: ({
      commit
    }, params) => {
      return addRoleSet(params.data).then((rps) => {
        const msg = rps.msg
        params.data = rps.data[0]
        commit('PUT_ACCOUNTSET_STATE', params)
        return {
          success: true,
          msg
        }
      })
    },

    // 获取菜单
    PgetMenus: ({
      commit
    }) => {
      return PgetMenus().then((rps) => {
        const data = rps.data
        return {
          success: true,
          data
        }
      })
    },
    // 获取权限
    PgetPermissions: ({
      commit
    }) => {
      return PgetPermissions().then((rps) => {
        const data = rps.data
        return {
          success: true,
          data
        }
      })
    },
    // 获取平台、代理
    // PclientTree: ({
    //   commit
    // }) => {
    //   return PclientTree().then((rps) => {
    //     const data = rps.data
    //     return {
    //       success: true,
    //       data
    //     }
    //   })
    // },
    // 修改密码
    PeditPassword: ({
      commit
    }, params) => {
      return PeditPassword(params).then((rps) => {
        const msg = rps.msg
        return {
          success: true,
          msg
        }
      })
    },

    //权限管理
    searchPermissionSet: ({
      commit
    }, params) => {
      return searchPermissionSet(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_PERMISSIONSET_LIST', data)
        commit('GET_PERMISSIONSET_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },
    // 新增权限
    addPermissionSet: ({
      commit
    }, params) => {
      return addPermissionSet(params).then((rps) => {
        const data = rps.data
        const msg = rps.msg
        commit('GET_PERMISSIONSET_ADD', data)
        return {
          success: true,
          data,
          msg
        }
      })
    },
    // 删除权限
    delPermissionSet: ({
      commit
    }, params) => {
      return delPermissionSet(params).then((rps) => {
        commit('GET_PERMISSIONSET_DEL', params)
        const msg = rps.msg
        return {
          success: true,
          msg
        }
      })
    },
    // 修改权限
    putPermissionSet: ({
      commit
    }, params) => {

      const {
        parentId,
        label,
        path,
        slug,
        method,
        Id
      } = params

      return putPermissionSet({
        parentId,
        label,
        path,
        slug,
        method,
        Id
      }).then((rps) => {
        const msg = rps.msg
        commit('GET_PERMISSIONSET_PUT', params)
        return {
          success: true,
          msg
        }
      })
    },

    //菜单管理
    searchMenuSet: ({
      commit
    }, params) => {
      return searchMenuSet(params).then((rps) => {
        const data = rps.data
        const count = rps.count
        commit('GET_MENUSET_LIST', data)
        commit('GET_MENUSET_COUNT', count)
        return {
          success: true,
          data,
          count
        }
      })
    },
    // 新增菜单
    addMenuSet: ({
      commit
    }, params) => {
      return addMenuSet(params).then((rps) => {
        const data = rps.data
        const msg = rps.msg
        commit('GET_MENUSET_ADD', data)
        return {
          success: true,
          data,
          msg
        }
      })
    },
    // 删除菜单
    delMenuSet: ({
      commit
    }, params) => {
      return delMenuSet({
        Id: params.Id
      }).then((rps) => {
        commit('GET_MENUSET_DEL', params)
        const msg = rps.msg
        return {
          success: true,
          msg
        }
      })
    },
    // 修改菜单
    putMenuSet: ({
      commit
    }, params) => {
      const {
        parentId,
        label,
        icon,
        path,
        Id,
      } = params

      return putMenuSet({
        parentId,
        label,
        icon,
        path,
        Id,
      }).then((rps) => {
        const msg = rps.msg
        commit('GET_MENUSET_PUT', params)
        return {
          success: true,
          msg
        }
      })
    }
  }
}

export default account
