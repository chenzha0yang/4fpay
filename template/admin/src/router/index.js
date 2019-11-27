import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

import Layout from '@/views/layout/Layout'


export const constantRouterMap = [{
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/404',
    component: () => import('@/views/errorPage/404'),
    hidden: true
  },
  {
    path: '/401',
    component: () => import('@/views/errorPage/401'),
    hidden: true
  },
]

export default new Router({
  scrollBehavior: () => ({
    y: 0
  }),
  routes: constantRouterMap
})

export const asyncRouterMap = [
  // 主页
  {
    path: '',
    component: Layout,
    redirect: 'index',
    name: 'index',
    meta: {
      title: 'index',
      icon: 'home'
    },
    children: [{
      path: 'index',
      component: () => import('@/views/index/index'),
      name: 'home',
      meta: {
        title: 'index',
        icon: 'home',
        noCache: true
      }
    }]
  },
  // 订单管理
  {
    path: '/order',
    component: Layout,
    redirect: '/order/inOrder',
    name: 'order',
    meta: {
      title: 'orderList',
      icon: 'order3'
    },
    children: [
      // 入款订单
      {
        path: 'inOrder',
        name: 'inOrder',
        component: () => import('@/views/order/inOrder'),
        meta: {
          title: 'inOrderList',
          icon: 'order3'
        }
      },
      // 出款订单
      {
        path: 'outOrder',
        name: 'outOrder',
        component: () => import('@/views/order/outOrder'),
        meta: {
          title: 'outOrderList',
          icon: 'order3'
        }
      }
    ]
  },
  //配置管理
  {
    path: '/conf',
    component: Layout,
    redirect: '/conf/TripartiteList',
    name: 'Configuration',
    meta: {
      title: 'Configuration',
      icon: 'config'
    },
    children: [
      // 三方列表
      {
        path: 'TripartiteList',
        name: 'TripartiteList',
        component: () => import('@/views/conf/tripartite'),
        meta: {
          title: 'TripartiteList',
          icon: 'config'
        }
      },
      // 三方修改
      {
        path: 'changeTripart',
        name: 'changeTripart',
        component: () => import('@/views/conf/changeTripart'),
        hidden: true,
        visitedViews: true,
        meta: {
          title: 'changeTripart',
          icon: 'config'
        }
      },
      // 三方列表添加
      {
        path: 'addTripart',
        name: 'addTripart',
        component: () => import('@/views/conf/addTripart'),
        hidden: true,
        meta: {
          title: 'addTripart',
          icon: 'config'
        }
      },
      // 通知地址
      {
        path: 'NotifyList',
        name: 'NotifyList',
        component: () => import('@/views/conf/notify'),
        meta: {
          title: 'NotifyList',
          icon: 'config'
        }
      },
      //添加通知地址
        {
          path: 'addNotify',
          name: 'addNotify',
          hidden: true,
          component: () => import('@/views/conf/addNotify'),
          meta: {
            title: 'addNotify',
            icon: 'config'
          }
        },

      // 修改通知地址
      {
        path: 'changeNotify',
        name: 'changeNotify',
        hidden: true,
        component: () => import('@/views/conf/changeNotify'),
        meta: {
          title: 'notifyUrl',
          icon: 'config'
        }
      },
      // 支付方式
      {
        path: 'payType',
        name: 'payType',
        component: () => import('@/views/conf/type'),
        meta: {
          title: 'payType',
          icon: 'config'
        }
      },
      // 支付方式添加
      {
        path: 'addPayType',
        name: 'addPayType',
        component: () => import('@/views/conf/addType'),
        hidden: true,
        meta: {
          title: 'addPayType',
          icon: 'config'
        }
      },
      // 客户接口
      {
        path: 'ClientList',
        name: 'ClientList',
        component: () => import('@/views/conf/client'),
        meta: {
          title: 'ClientList',
          icon: 'config'
        }
      },
      // 客户接口添加
      {
        path: 'addClientList',
        name: 'addClientList',
        component: () => import('@/views/conf/addClient'),
        hidden: true,
        meta: {
          title: 'addClientList',
          icon: 'config'
        }
      }
    ]
  },
  //商户管理
  {
    path: '/shop',
    component: Layout,
    redirect: '/shop/income_shop',
    name: 'shopManage',
    meta: {
      title: 'shopManage',
      icon: 'merchant'
    },
    children: [
      // 入款商户
      {
        path: 'income_shop',
        name: 'incomeShop',
        component: () => import('@/views/shop/incomeShop'),
        meta: {
          title: 'incomeShop',
          icon: 'merchant'
        }
      },
      // 入款商户添加
      {
        path: 'changeShop',
        name: 'changeShop',
        component: () => import('@/views/shop/changeShop'),
        hidden: true,
        meta: {
          title: 'changeShop',
          icon: 'merchant'
        },
      },
      // 出款商户
      {
        path: 'outcome_shop',
        name: 'outcomeShop',
        component: () => import('@/views/shop/outcomeShop'),
        meta: {
          title: 'outcomeShop',
          icon: 'merchant'
        }
      },
      // 出款商户添加
      {
        path: 'addoutshop',
        name: 'addOutShop',
        component: () => import('@/views/shop/addOutShop'),
        hidden: true,
        meta: {
          title: 'addOutShop',
          icon: 'merchant'
        },
      }
    ]
  },
  // 银行列表
  {
    path: '/bank',
    component: Layout,
    redirect: '/bank/income_bank',
    name: 'bankManage',
    meta: {
      title: 'bankManage',
      icon: 'bank'
    },
    children: [
      // 入款银行
      {
        path: 'income_bank',
        name: 'incomeBank',
        component: () => import('@/views/bank/incomeBank'),
        meta: {
          title: 'incomeBank',
          icon: 'bank'
        }
      },
      // 入款银行添加
      {
        path: 'addInBank',
        name: 'addInBank',
        component: () => import('@/views/bank/addInBank'),
        hidden: true,
        meta: {
          title: 'addInComeBank',
          icon: 'bank'
        },
      },
      // 出款银行
      {
        path: 'outcome_bank',
        name: 'outcomeBank',
        component: () => import('@/views/bank/outcomeBank'),
        meta: {
          title: 'outcomeBank',
          icon: 'bank'
        }
      },
      // 出款银行添加
      {
        path: 'addOutBank',
        name: 'addOutBank',
        component: () => import('@/views/bank/addOutBank'),
        hidden: true,
        meta: {
          title: 'addOutComeBank',
          icon: 'bank'
        },
      }
    ]
  },
  // 白名单管理
  {
    path: '/white_list',
    component: Layout,
    redirect: '/white_list/index',
    name: 'whiteList',
    meta: {
      title: 'whiteList',
      icon: 'whitelist'
    },
    children: [{
        // 白名单列表
        path: 'index',
        name: 'whiteIndex',
        component: () => import('@/views/whiteList/whiteList'),
        meta: {
          title: 'whiteIndex',
          icon: 'whitelist'
        }
      },
      {
        // 白名单添加
        path: 'whiteAdd',
        name: 'whiteAdd',
        hidden: true,
        component: () => import('@/views/whiteList/whiteAdd'),
        meta: {
          title: 'whiteAdd',
          icon: 'whitelist'
        }
      }
    ]
  },
  // 维护管理
  {
    path: '/maintain',
    component: Layout,
    redirect: '/maintain/index',
    name: 'maintain',
    meta: {
      title: 'maintain',
      icon: 'maintain'
    },
    children: [{
      // 维护管理
      path: 'index',
      name: 'maintainIndex',
      component: () => import('@/views/maintain/maintain'),
      meta: {
        title: 'maintainIndex',
        icon: 'maintain'
      }
    }]
  },

  // 日志管理
  {
    path: '/log_set',
    component: Layout,
    redirect: '/log_set/login_log',
    name: 'logSet',
    meta: {
      title: 'logSet',
      icon: 'log'
    },
    children: [{
        // 日志管理
        path: 'login_log',
        name: 'loginLog',
        component: () => import('@/views/logSet/loginLog'),
        meta: {
          title: 'loginLog',
          icon: 'log'
        }
      },
      {
        // 操作管理
        path: 'operate_log',
        name: 'operateLog',
        component: () => import('@/views/logSet/operateLog'),
        meta: {
          title: 'operateLog',
          icon: 'log'
        }
      },
      {
        // 下发日志
        path: 'dispatch_log',
        name: 'dispatchLog',
        component: () => import('@/views/logSet/dispatchLog'),
        meta: {
          title: 'dispatchLog',
          icon: 'log'
        }
      },
      {
        // 回调日志
        path: 'callback_log',
        name: 'callbackLog',
        component: () => import('@/views/logSet/callbackLog'),
        meta: {
          title: 'callbackLog',
          icon: 'log'
        }
      },
      // {
      //   // API日志
      //   path: 'api_log',
      //   name: 'apiLogs',
      //   component: () => import('@/views/logSet/apiLogs'),
      //   meta: {
      //     title: 'apiLogs',
      //     icon: 'log'
      //   }
      // },
      {
        // 订单日志
        path: 'order_log',
        name: 'orderLog',
        component: () => import('@/views/logSet/orderLog'),
        meta: {
          title: 'orderLog',
          icon: 'log'
        }
      },
      {
        // 前台错误日志
        path: 'error_logs_front',
        name: 'errorLogsFront',
        component: () => import('@/views/logSet/errorLogsFront'),
        meta: {
          title: 'errorLogsFront',
          icon: 'log'
        }
      },
      {
        // 后台错误日志
        path: 'error_logs_admin',
        name: 'errorLogsAdmin',
        component: () => import('@/views/logSet/errorLogsAdmin'),
        meta: {
          title: 'errorLogsAdmin',
          icon: 'log'
        }
      },
      {
        // 请求日志
        path: 'request_log',
        name: 'requestLog',
        component: () => import('@/views/logSet/requestLog'),
        meta: {
          title: 'requestLog',
          icon: 'log'
        }
      },
    ]
  },
  // 缓存管理
  {
    path: '/cache',
    component: Layout,
    redirect: '/cache/index',
    name: 'cache',
    meta: {
      title: 'cache',
      icon: 'cache'
    },
    children: [{
      // 缓存管理
      path: 'cacheIndex',
      name: 'cacheIndex',
      component: () => import('@/views/cache/cache'),
      meta: {
        title: 'cacheIndex',
        icon: 'cache'
      }
    }]
  },
  // 权限管理
  {
    path: '/permission',
    component: Layout,
    redirect: '/permission/account_set',
    name: 'permission',
    meta: {
      title: 'permissionSet',
      icon: 'permission1'
    },
    children: [{
        // 账号管理
        path: 'account_set',
        name: 'accountSet',
        component: () => import('@/views/permission/accountSet'),
        meta: {
          title: 'accountSet',
          icon: 'permission1'
        }
      },
      {
        // 账号添加
        path: 'account_add',
        name: 'accountAdd',
        hidden: true,
        component: () => import('@/views/permission/accountAdd'),
        meta: {
          title: 'accountAdd',
          icon: 'permission1'
        }
      },
      {
        // 角色管理
        path: 'role_set',
        name: 'roleSet',
        component: () => import('@/views/permission/roleSet'),
        meta: {
          title: 'roleSet',
          icon: 'permission1'
        }
      },
      {
        // 角色添加
        path: 'role_add',
        name: 'roleAdd',
        hidden: true,
        component: () => import('@/views/permission/roleAdd'),
        meta: {
          title: 'roleAdd',
          icon: 'permission1'
        }
      },
      {
        // 权限管理
        path: 'permission_set',
        name: 'permissionSet',
        component: () => import('@/views/permission/permissionSet'),
        meta: {
          title: 'permissionSet',
          icon: 'permission1'
        }
      },
      {
        // 菜单管理
        path: 'menu_set',
        name: 'menuSet',
        component: () => import('@/views/permission/menuSet'),
        meta: {
          title: 'menuSet',
          icon: 'permission1'
        }
      }
    ]
  },
  {
    path: '*',
    redirect: '/404',
    hidden: true
  }
]
