export default {
  route: {
    index: 'home',
    // 订单管理
    orderList: 'Order Manage',
    inOrderList: 'Inorder List',
    outOrderList: 'OutOrder List',
    // Configuration 配置管理
    Configuration: 'Configuration',
    TripartiteList: 'Tripartite List',
    changeTripart: 'Tripartite List add',
    TripartIndex: 'Tripartite List',
    addNotifyUrl: 'OtifyUrl List add',

    NotifyList: 'Notify List',
    addNotify: 'Add Notify',
    payType: 'Pay Type',
    addPayType: 'PayType List add',
    ClientList: 'Client List',
    addClientList: 'Client List add',
    AgentList: 'Agent List',
    // 商户管理
    shopManage: 'Shop Manage',
    incomeShop: 'Income Shop',
    outcomeShop: 'Outcome Shop',
    changeShop: 'Income Shop add',
    addOutShop: 'Outcome Shop add',
    // 银行列表
    bankManage: 'Bank Manage',
    incomeBank: 'Income Bank',
    outcomeBank: 'Outcome Bank',
    addInComeBank: 'Income Bank add',
    addOutComeBank: 'Outcome Bank add',
    // 白名单管理
    whiteList: 'white Manage',//一级
    whiteIndex: 'white List',
    // 维护管理
    maintain: 'maintain',//一级
    maintainIndex: 'maintain',
    // 日志管理
    logSet: 'log Set',
    loginLog: 'login Log',
    operateLog: 'operate Log',
    dispatchLog: 'dispatch Log',
    callbackLog: 'callback Log',
    requestLog: 'request Log',
    // 权限管理
    permissionList: 'permission List',//一级
    accountSet: 'account Set',
    roleSet: 'role Set',
    permissionSet: 'permission Set',
    menuSet: 'menu Set'
  },
  navbar: {
    logOut: 'Log Out',
    index: 'home',
    screenfull: 'screenfull',
    theme: 'theme',
    Time: 'Now Time'
  },
  login: {
    title: 'Login Form',
    logIn: 'Log in',
    username: 'Username',
    password: 'Password',
    specialSymbol: 'Please do not enter special symbols',
    validationLogin: 'Verification failed, please log in again',
    passLength: 'The password cannot be less than 6 digits',
    inputUsername: 'Please input Username',
    inputPassword: 'Please enter your password',
    inputCheckPassword: 'Please enter your password again',
    verification: 'Verification',
    correctVerification: 'please enter verification',
    notSame: 'Inconsistent entry password',
    formatError: 'User name format error',
    sizeError: 'User name format error',
    sizeError2: 'Please enter 2 to 8 digits of uppercase and lowercase alphanumeric characters',
    passError: 'The password format is incorrect (length 6-14, no spaces)',
    passWeak: 'Weak password strength',
    passMiddle: 'Password strength',
    passHigh: 'Strong password strength'
  },
  alertMsg: {
    prompt: 'prompt',
    cancel: 'cancel',
    confirm: 'confirm',
    toDelete: 'Continue to eelete?',
    cencelOperation: 'Cancel operation',
    IllegalOperation: 'Illegal operation',
    networkError: 'Network Error',
    reLogin: 'Re-login',
    reLoginSuccess: 'Password reset complete,Re-login',
    getCode: 'Please get the verification code first',
    changeLanguage: 'Successful language switching',
    toSend: 'Whether to issue',
    getCode: 'Please get the verification code first',
    changeLanguage: 'switch language success'
  },
  index: {
    userMessage: 'user info',
    account: 'account',
    nickname: 'nickname',
    roles: 'roles',
    lastLoginTime: 'last login Time',
    lastLoginIp: 'last login ip'
  },
  table: {
    latestWeek: 'Last week',
    lastMonth: 'Last month',
    lastThreeMonths: 'Last three months',
    dragTips1: 'The default order',
    dragTips2: 'The after dragging order',
    title: 'Title',
    importance: 'Imp',
    type: 'Type',
    search: 'Search',
    export: 'Export',
    input: 'input',
    page: 'page',
    reviewer: 'reviewer',
    date: 'Date',
    time: 'time',
    author: 'Author',
    readings: 'Readings',
    status: 'Status',
    prompt: 'prompt',
    actions: 'Actions',
    reset: 'reset',

    cancel: 'Cancel',
    confirm: 'Confirm',
    pagenumber: 'show count',
    number: 'number',
    account: 'account',
    ip: 'IP',
    port: 'port',

    accountType: 'account type',
    Pselect: 'Please select',
    Searchdata: 'Click [Search] to get data',
    searchMsg: 'Give me some time',
    all: 'All',
    success: 'Success',
    error: 'Error',
    pending: 'Pending',
    normal: 'normal',
    disable: 'deactivate',
    enable: 'Enable',
    formal: 'official',
    try: 'Try it',
    roles: 'role',
    rolesName: 'role name',
    permission: 'permission',
    setPermission: 'set permissions',
    selectPermission: 'Click to select permission',
    getWayWeak: 'Please select the acquisition method',
    reg: 'Register',
    to: 'to',
    start: 'start',
    end: 'end',
    money: 'Amount',
    theName: 'name',
    password: 'password',
    repassword: 'confirm password',
    editPassword: 'Change password',
    transaction: 'transaction amount',
    transactionHour: 'transaction time',
    Superior: 'belonging to superior',
    reg_ip: 'IP',
    getWay: 'Get mode',
    orderNumber: 'order number',
    proxy: 'proxy',
    proxyAccount: 'proxy account',
    orders: 'Order',
    source: 'source',
    content: 'content',
    roleID: 'role ID',
    message: 'information',
    address: 'address',
    searchUser: 'search account',
    searchName: 'search nickname',
    id: 'ID',
    client: 'client',
    user: 'account',
    nickname: 'nickname',
    Update: 'Edit',
    create: 'Add',
    details: 'Details',
    delete: 'delete',
    timestamp: 'add time',
    editTimestamp: 'Modify time',
    loginIp: 'Login IP',
    oldPass: 'original password',
    username: 'username',
    pay: 'Pay',
    send: 'Send',
    searchId: 'search ID',
    agentId: 'Proxy line',
    agentNumber: 'Subagent line',
    payId: 'Three party type',
    isApp: 'Whether or not to jump',
    merURL: 'payment gateway',
    md5Key: 'MD5 secret key',
    rsaKey: 'RSA secret key',
    publicKey: 'public key',
    callBackURL: 'Return address',

    //出入款
    businessNum: 'shop ID',
    clientName: 'platform Line',
    confName: 'shop Type',
    payWay: 'pay Type',
    payMoney: 'order Mondy',
    orderStatus: 'order Status',
    dispatchStatus: 'dispatch Status',
    createdAt: 'create Time',
    updatedAt: 'update Time',
    agentName: 'agent Name',
    extendData: 'Extended data one',
    owOrder: 'Platform order',

    // 配置管理
    // 三方ID
    whiteListState: 'ip whiteList',
    inSate: 'whether inOrder',
    agentName: 'agent Line',
    tripartPayID: 'tripartite ID',
    tripartConfName: 'tripartite name',
    payMod: 'model name',
    tripartPayCode: 'tripartite code',
    typeName: 'pay way',
    ifNeedQuery: 'pay query',
    ifStatus: 'whether open',
    inState: 'open income',
    outState: 'open outcome',
    inComeBankList: 'income bank list',
    payCode: 'pay code',
    yes: 'yes',
    no: 'no',

    // 通知地址
    siteUrl: 'site url',
    incomeCallbackUrl: 'income callback',
    outcomeCallbackUrl: 'outcome callback',

    // 支付方式
    whetherOpen: 'whether open',
    OpenAppH5: 'open APP/H5',
    englishName: 'Payment method alias',

    //客户接口
    portName: 'port name',
    certificate: 'certificate of authorization',
    // 代理列表
    clientId: 'client platform id',
    // 银行列表
    bankName: 'bank name',
    bankCode: 'bank code',
    bankStatus: 'bank status',

    // 维护管理
    maintainProg: 'maintain programme',
    whetherMaintain: 'whether maintain',
    maintainInfo: 'maintain info',

    // 登陆日志
    loginMessage: 'login message',
    loginClient: 'line',
    agent: 'agent',
    loginClientName: 'line name',
    name: 'name',

    // 操作日志
    userID: 'user ID',
    path: 'path',
    interaction: 'interaction way',

    // 下发日志
    whetherSend: 'whether auto send',
    inOutcomeSend: 'income or outcome send',
    data: 'data',
    httpCode: 'http code',
    reponseMessage: 'response message',
    auto: 'auto',
    manual: 'manual',
    income: 'income',
    outcome: 'outcome',
    check: 'check',

    //回调日志
    callback: 'callback',
    errorType: 'error type',

    refresh: 'refresh',
    filter: 'filter',
    add: 'add',
    submit: 'submit',

    paySuccess: 'pay Success',
    payFail: 'pay Fail',
    notPay: 'not Pay',

    //请求日志
    req_clientName:'client name',
    req_aim:'aim',
    req_ip: 'ip',
    req_route:'route',
    req_data:'data',
    req_time:'time',

    // 角色管理
    identify: 'identify',
    level: 'level',

  },
  callback: {
    clientID: 'client ID',
    clientName: 'client name'
  },
  menu: {
    root: 'root',
    title: 'title',
    icon: 'icon',
    path: 'path',
    role: 'role'
  },
  tagsView: {
    open: 'open',
    close: 'Close',
    closeOthers: 'Close Others',
    closeAll: 'Close All',
    back: 'back'
  },


  payName: {
    'bank': 'bank',
    'wechat': 'wechat',
    'alipay': 'alipay',
    'qqpay': 'qq pay',
    "tenpay": 'tencent pay',
    "visapay": 'Unionpay scan',
    "jdpay": 'jd pay',
    "bdpay": 'baidu pay',
    "ylpay": 'Unionpay quick pay',
    "cardpay": 'card',
    "allpay": 'one pay',
    "dispensing": 'auto outcome',
    "extendName": 'extend name',
    "extendName1": 'extend name one',
    "extendName2": 'extend name two',
    "extendName3": 'extend name three'

  },

  statusMsg: {
    1: 'normal',
    2: 'Disable'
  },

  getWay: {
    GET: 'GET',
    POST: 'POST',
    PUT: 'PUT',
    DELETE: 'DELETE'
  }
}



