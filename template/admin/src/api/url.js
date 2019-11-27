const url = {
  // url: 'http://sportagent.pk051.com/api/v1',
  // url: 'http://online.com/admin',
  // url: 'http://192.168.2.50/admin',
  // url:' http://localhost:9527',

  // =============================  登陆  ==================================
  login: '/index/login', // 登陆
  getinfo: '/auth/getInfo', // 获取账号信息
  logout: '/index/logout', // 登出
  verification: '/index/verification', // 验证码
  editUserPassword: '/index/editUserPassword', // 修改密码
  // =============================  主页  ==================================

  echarts: '/index/echarts', // 修改密码

  // =============================  订单管理  ==================================
  // 入款
  inOrder: '/order/inOrder',
  inOrderFind: '/order/inOrderFind',

  //平台线路
  orderClientName: '/client',
  // 商户类型
  orderConfigLists: '/config/configLists',
  //出款
  outOrder: '/order/outOrder',
  outOrderFind: '/order/outOrderFind',
  // =============================  配置管理  ==================================
  // 三方列表
  TripartiteList: '/config/payConfig',
  topTwenty: '/config/topTwenty',
  // 通知地址（代理用）
  NotifAgent: '/config/callBack',
  // 通知地址列表
  NotifyList: '/config/callBack',
  // 支付方式
  payType: '/config/payType',
  payTypeList: '/config/payTypeList',
  // 客户接口
  ClientList: '/config/apiClients',
  // 客户ID
  clientUsersList: '/auth/clientUsers',

  // =============================  商户管理  ==================================
  // 入款商户
  incomeShop: '/merchant/inMerchant',
  // 出款商户
  outcomeShop: '/merchant/outMerchant',
  ownType: '/merchant/ownType',

  // =============================  银行列表 ==================================
  // 入款银行
  incomeBank: '/bank/inBank',
  // 出款银行
  outcomeBank: '/bank/outBank',

  // =============================  白名单管理 ==================================
  // 白名单管理
  whiteList: '/whitelist/payWhitelist',

  // =============================  维护管理 ==================================
  // 维护管理
  maintain: '/maintain/Maintain',
  // 三方列表下拉
  // getConfigLists: '/config/getConfigLists',

  // =============================  日志管理 ==================================
  // 登陆日志
  loginLog: '/logs/loginLogs',
  // 操作日志
  operateLog: '/logs/operateLogs',
  // 下发日志
  dispatchLog: '/logs/sendCallbackLogs',
  // 回调日志
  callbackLog: '/logs/callbackLogs',
  // 请求日志
  requestLog: '/logs/requestLogs',
  // 订单日志
  orderLogs: '/logs/orderLogs',
  // API日志
  // apiLogs: '/logs/apiLogs',

  // 前台错误日志
  errorLogsF: '/logs/errorLogsF',
  // 后台错误日志
  errorLogsA: '/logs/errorLogsA',

  // =============================  权限管理 ==================================
  // 账号管理
  accountSet: '/auth/users',
  // 角色管理
  roleSet: '/auth/roles',
  getRoleName: '/auth/rolesList',
  // 权限管理
  permissionSet: '/auth/permission',
  // 菜单管理
  menuSet: '/auth/menu',
  // 角色管理页面用的获取菜单、权限
  getMenus: '/auth/getMenus',
  getPermissions: '/auth/getPermissions',
  // 账号管理页面的用的代理、平台
  // clientTree: '/auth/clientTree',
  // 账号管理页面的修改密码
  PeditPassword: '/auth/editPassword',
  // 当前账号的修改密码
  editOwnPwd: '/auth/editOwnPwd',

  // =============================  缓存管理 ==================================
  cacheKey: '/cache/cache/key',
  cacheLen: '/cache/cache/len',
  cacheVal: '/cache/cache/val',
  cacheDel: '/cache/cache/del'
}
export default url
