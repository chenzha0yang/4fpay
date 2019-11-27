import {
  asyncRouterMap,
  constantRouterMap
} from '@/router'
import router from '@/router'

function hasPermission(name, route) {
  return route.findIndex(v => v.name === name);
}

function filterAsyncRouter(asyncRouterMap, menus = []) {
  const accessedRouters = asyncRouterMap.filter(route => {
    if (route.path === '*') {
      return true
    } else {
      const index = hasPermission(route.name, menus)
      if (index > -1) {
        if (menus[index].icon) {
          route.meta.icon = menus[index].icon
        }
        if (route.children && route.children.length > 1) {
          route.children = filterAsyncRouter(route.children, menus[index].children)
        }
        return true
      }
    }

    return false
  })
  return accessedRouters
}

const permission = {
  state: {
    routers: constantRouterMap,
    addRouters: []
  },
  mutations: {
    SET_ROUTERS: (state, routers) => {
      router.addRoutes(routers) // 动态添加可访问路由表
      state.addRouters = routers
      state.routers = constantRouterMap.concat(routers)
    }
  },
  actions: {
    GenerateRoutes({
      commit
    }, data) {
      return new Promise(resolve => {
        const {
          menu
        } = data
        const accessedRouters = filterAsyncRouter(asyncRouterMap, menu)
        commit('SET_ROUTERS', accessedRouters)
        resolve()
      })
    }
  }
}

export default permission
