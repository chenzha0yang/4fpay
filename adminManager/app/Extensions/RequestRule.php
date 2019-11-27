<?php

namespace App\Extensions;

use Illuminate\Validation\Rule;

trait RequestRule
{

    // 公共参数
    public $pageLimitRule = [
        'page'  => 'required|integer|min:1',
        'limit' => 'required|integer|between:10,500',
    ];
    // 正则用法
    public $regexRule = [
        'page'  => [
            'required',
            'integer',
            'min:1',
        ],
        'limit' => [
            'required',
            'regex:/^\d+$/'
        ],
    ];
    public $apiParamsRule = [
        'clientName'   => 'required|string|between:1,50',
        'clientSecret' => 'required|string|between:10,100',
        'lang'         => 'nullable|string',
    ];

    public $createCodeRule = [
        'vt' => 'required|string',
    ];
    //入款商户列表删除
    public $inMerchantDelRule = [
        'Id' => 'required|integer|min:1',
    ];

    //管理员账号Select
    public $usersSelectRule = [
        'account' => 'nullable|String',
        'name'    => 'nullable|String',
        //'UpdateId' => 'nullable|integer',
    ];
    //管理员账号Update
    public $usersUpdateRule = [
        'Id'      => 'required|integer|min:1',
        'uName'   => 'nullable|String',
        'state'   => 'nullable|integer|min:1',
        'loginIp' => 'nullable|String',
        'agentId' => 'nullable|String',
    ];
    //管理员密码修改
    public $usersPwdUpdateRule = [
        'Id'       => 'required|integer|min:1',
        'password' => 'required|String',
    ];
    //用户密码修改
    public $editOwnPwdRule = [
        'oldPassword' => 'required|String',
        'password'    => 'required|String',
    ];
    //用户登录
    public $loginRule = [
        'username' => 'required|String',
        'password' => 'required|String|max:16',  //密码6-16位
        //'verification' => 'required|String|',
        //'vt'           => 'required|String'
    ];
    //用户退出登录
    public $outLoginRule = [
        'userId' => 'required|String|min:1',
    ];
    //管理员账号Del
    public $usersDelRule = [
        'Id' => 'required|integer|min:1',
    ];

    //管理员账号Add
    public $usersAddRule = [
        'account'  => 'required|String|unique:admin_users,username',                //用户名
        'uName'    => 'required|String',                //账号名称
        'password' => 'required|String|between:6,16',   //密码
        'roleId'   => 'required|integer|min:1',         //所属角色ID
        'state'    => 'required|integer|min:1',         //是否开启
        'loginIp'  => 'required|String',     //允许登录的IP
        'clientId' => 'required|integer|min:0',     //平台线路
        'agentId'  => 'required',     //代理线路
    ];
    //角色管理Select
    public $rolesSelectRule = [
        'Id' => 'nullable|integer|min:1',         //查询ID
    ];
    //角色管理Update
    public $rolesUpdateRule = [
        'Id'            => 'required|integer|min:1',         //查询ID
        'slug'          => 'nullable|String|',               //标识
        'uName'         => 'nullable|String|',               //名称
        'permissionIds' => 'nullable|Array',              //权限   ， 分隔
        'menuIds'       => 'nullable|Array',              //权限   ， 分隔
        'state'         => 'nullable|integer',              //权限   ， 分隔
        'isClient'      => 'nullable|integer|min:1',         //是否展示平台线路
        'isAgent'       => 'nullable|integer|min:1'         //是否展示代理线路
    ];
    //角色管理Del
    public $rolesDelRule = [
        'Id' => 'required|integer|min:1',
    ];
    //角色管理Add
    public $rolesAddRule = [
        'uName'         => 'required|String|unique:admin_roles,name',               //名称
        'menuIds'       => 'required|Array',              //级别
        'slug'          => 'required|String',              //标识
        'permissionIds' => 'required|Array',              //权限    ， 分隔
        'state'         => 'required|integer',              //权限    ， 分隔
        'isClient'      => 'required|integer|min:1',         //是否展示平台线路
        'isAgent'       => 'required|integer|min:1'         //是否展示代理线路
    ];
    //权限管理Select
    public $permissionSelectRule = [
        'Pid' => 'nullable|integer|min:1',               //查询条件ID
    ];
    //权限管理Update
    public $permissionUpdateRule = [
        'label'  => 'required|String',                    //权限名称
        'slug'   => 'required|String',                    //标识
        'method' => 'nullable|Array',                    //HTTP方法
        'path'   => 'nullable|String',                    //HTTP路径
        'Id'     => 'required|integer|min:1',             //更新ID

    ];
    //权限管理Del
    public $permissionDelRule = [
        'Id' => 'required|integer|min:1',               //删除条件ID
    ];
    //权限管理Add
    public $permissionAddRule = [
        'label'    => 'required|String|unique:admin_permissions,name',                    //权限名称
        'slug'     => 'required|String',                    //标识
        'method'   => 'nullable|Array',                    //HTTP方法
        'path'     => 'nullable|String',                    //HTTP路径
        'parentId' => 'nullable|int',                    //父级ID。不传默认0
    ];
    //菜单管理Update
    public $menusUpdateRule = [
        'Id'       => 'required|integer|min:1',                    //权限名称
        'parentId' => 'required|integer',                          //父级ID
        'label'    => 'required|String',                           //菜单名
        'icon'     => 'required|String',                           //图标
        'path'     => 'required|String',                           //路径
    ];
    //菜单管理Add
    public $menusAddRule = [
        'parentId' => 'nullable|integer|min:0',                          //父级ID
        'label'    => 'required|String',                           //菜单名
        'icon'     => 'required|String',                           //图标
        'path'     => 'required|String',                           //路径
    ];
    //菜单管理Del
    public $menusDelRule = [
        'Id' => 'required|integer|min:1',                    //权限名称
    ];
    //入款商户Select
    public $inMerchantSelectRule = [
        'Id'           => 'nullable|integer|min:1',
        'agentId'      => 'nullable|String',
        'businessNum'  => 'nullable|String',
        'payId'        => 'nullable|integer',
        'clientUserId' => 'nullable|integer',
    ];
    //新增入款商户获取所属支付方式

    public $getOwnPayTypeRule = [
        'payId' => 'required|integer',
    ];
    //入款商户Update
    public $inMerchantUpdateRule = [
        'Id'           => 'required|integer|min:1',
        'agentId'      => 'nullable',                  //代理线路
        'clientUserId' => 'nullable|integer',                 //平台线路
        'businessNum'  => 'nullable|String|unique:pay_merchant,business_num',                  //商户ID
        'callbackURL'  => 'nullable|String',                  //返回地址
        'status'       => 'nullable|integer',                 //是否开启
        'isApp'        => 'nullable|integer',                 //是否跳转
        'md5Key'       => 'nullable|String',                  //md5秘钥
        'privateKey'   => 'nullable|String',                  //rsa私钥
        'publicKey'    => 'nullable|String',                  //公钥
        'payId'        => 'nullable|integer',                 //三方类型ID
        'typeId'       => 'nullable|integer',                 //支付方式
        'msgOne'       => 'nullable|String',                  //预留字段信息1
        'msgTwo'       => 'nullable|String',                  //预留字段信息2
        'msgThr'       => 'nullable|String',                  //预留字段信息3
        'payCode'      => 'nullable|String',                  //支付编码
        'merURL'       => 'nullable|String',                  //支付网关自填
    ];
    //入款商户Add
    public $inMerchantAddRule = [
        'agentId'      => 'nullable|String',                        //代理线、下拉选择
        'clientUserId' => 'nullable|integer',                        //子代理线、下拉选择、传入ID int
        'businessNum'  => 'required|String|min:1|unique:pay_merchant,business_num',          //商户ID必传
        'callbackURL'  => 'required|String|min:1',          //返回地址必传
        'status'       => 'required|integer|min:1',         //是否开启、必传
        'isApp'        => 'required|integer|min:1',         //是否跳转APP、必传
        'md5Key'       => 'nullable|String',                         //Md5秘钥
        'privateKey'   => 'nullable|String',                         //RSA私钥
        'publicKey'    => 'nullable|String',                         //RSA公钥
        'payId'        => 'required|integer|min:1',         //支付三方ID
        'typeId'       => 'required',                //支付类型ID
        'msgOne'       => 'nullable|String',                //附加内容
        'msgTwo'       => 'nullable|String',                //附加内容
        'msgThr'       => 'nullable|String',                //附加内容
        'payCode'      => 'nullable|String',                //支付CODE码
        'merURL'       => 'nullable|String',                //支付网关
    ];
    public $outMerchantSelectRule = [
        'merchantId' => 'nullable|integer|min:1',
        'payId'      => 'nullable|integer',
    ];
    //出款商户Del
    public $outMerchantDelRule = [
        'Id' => 'required|integer|min:1',
    ];
    //出款商户Update
    public $outMerchantUpdateRule = [
        'Id'           => 'required|integer|min:1',
        'agentId'      => 'nullable|String',                        //代理线
        'clientUserId' => 'nullable|integer',                        //平台线路、下拉选择、传入ID int
        'businessNum'  => 'nullable|String|min:1',          //商户ID必传
        'callbackURL'  => 'nullable|String|min:1',          //返回地址必传
        'status'       => 'nullable|integer|min:1',         //是否开启、必传
        'md5Key'       => 'nullable|String',                         //Md5秘钥
        'privateKey'   => 'nullable|String',                         //RSA私钥
        'publicKey'    => 'nullable|String',                         //RSA公钥
        'payId'        => 'nullable|integer|min:1',         //出款三方ID
        'payCode'      => 'nullable|String',         //出款三方ID
    ];
    //出款商户Add
    public $outMerchantAddRule = [
        'agentId'      => 'nullable|String',                        //代理线、下拉选择、传入ID int
        'clientUserId' => 'nullable|integer',                        //平台、下拉选择、传入ID int
        'businessNum'  => 'required|String|min:1',          //商户ID必传
        'callbackURL'  => 'required|String|min:1',          //返回地址必传
        'status'       => 'required|integer|min:1',         //是否开启、必传
        'md5Key'       => 'nullable|String',                         //Md5秘钥
        'privateKey'   => 'nullable|String',                         //RSA私钥
        'publicKey'    => 'nullable|String',                         //RSA公钥
        'payId'        => 'required|integer|min:1',         //出款三方ID
    ];
    //入款订单列表
    public $inOrderSelectRule = [
        'clientUserId'  => 'nullable|integer',             //平台线路
        'agentId'       => 'nullable|String',              //代理线路
        'orderNumber'   => 'nullable|String',              //订单号
        'owOrderNumber' => 'nullable|String',              //平台订单号
        'businessNum'   => 'nullable|String',              //商户ID
        'payId'         => 'nullable|integer',             //支付类型
        'payWay'        => 'nullable|integer',             //支付类型
        'isStatus'      => 'nullable|integer|between:0,2', //订单状态
        'startDate'     => 'nullable|String',              //开始时间
        'endDate'       => 'nullable|String'               //结束时间
    ];
    //入款订单下发
    public $inOrderLowerRule = [
        'inOrderId' => 'required|integer|min:1',         //入款订单ID
    ];
    //入款订单下发详情
    public $inOrderFindRule = [
        'inOrderId' => 'required|integer|min:1',         //入款订单ID
    ];
    //出款订单列表
    public $outOrderSelectRule = [
        'clientUserId' => 'nullable|integer',              //平台线路
        'agentId'      => 'nullable|String',              //代理线路
        'orderNumber'  => 'nullable|String',              //订单号
        'businessNum'  => 'nullable|String',              //商户ID
        'payId'        => 'nullable|integer',             //商户类型
        'isStatus'     => 'nullable|integer|between:0,2', //订单状态
        'startDate'    => 'nullable|String',              //开始时间
        'endDate'      => 'nullable|String'               //结束时间
    ];
    //出款订单下发
    public $outOrderLowerRule = [
        'outOrderId' => 'required|integer|min:1',      //出款订单ID
    ];
    //出款订单下发详情
    public $outOrderFindRule = [
        'outOrderId' => 'required|integer|min:1',      //出款订单ID
    ];
    //维护管理Update
    public $MaintainUpdateRule = [
        'Id'    => 'required|integer|min:1',        //ID
        'state' => 'nullable|integer|min:1',        //是否维护
        'msg'   => 'nullable|String',               //维护信息
    ];
    //维护管理Select
    public $MaintainSelectRule = [
        'payId' => 'nullable|integer|min:1',        //三方类型ID
    ];
    //白名单列表
    public $payWhitelistSelectRule = [
        'payIp' => 'nullable|String|ip',        //IP
        'payId' => 'nullable|integer|min:1',    //三方类型ID
    ];
    //添加白名单
    public $payWhitelistAddRule = [
        'payId' => 'required|integer|min:1',       //商户类型
        'payIp' => 'required|String|ip',           //IP
        'state' => 'required|integer|between:1,2'  //是否启用此IP 1开启 2关闭
    ];
    //修改白名单
    public $payWhitelistUpdateRule = [
        'Id'    => 'required|integer|min:1',       //ID
        'payId' => 'nullable|integer|min:1',       //商户类型
        'payIp' => 'nullable|String|ip',           //IP
        'state' => 'nullable|integer|between:1,2'  //是否启用此IP 1开启 2关闭
    ];
    //三方列表
    public $payConfigSelectRule = [
        'payId'          => 'nullable|integer',      //商户类型ID
        'inState'        => 'nullable|integer',      //是否开启入款1开启 2关闭
        'whiteListState' => 'nullable|integer',      //ip白名单 1开启 2关闭
        'isStatus'       => 'nullable|integer',      //是否开启通道1开启 2关闭
    ];
    //新增三方
    public $payConfigAddRule = [
        'confName'       => 'required|String',              //商户类型(三方名称)
        'confMod'        => [
            'required',
            'String',
            'regex:/^[A-Z]*[A-Za-z]*$/',
            'unique:pay_config,mod'
        ],//模型
        'inState'        => 'required|integer|between:1,2',  //是否开启入款1开启 2关闭
        'outState'       => 'required|integer|between:1,2',  //是否开启出款1开启 2关闭
        'whiteListState' => 'required|integer|between:1,2',  //是否开启IP白名单 1开启 2关闭
        'isStatus'       => 'required|integer|between:1,2',  //是否开启 1开启 2关闭
        'payCode'        => 'nullable|String',               //三方扫码编码
        'bankUrl'        => 'nullable|String|url',          //网银支付网关
        'wechatUrl'      => 'nullable|String|url',           //微信支付网关
        'alipayUrl'      => 'nullable|String|url',           //支付宝支付网关
        'qqpayUrl'       => 'nullable|String|url',           //QQ支付网关
        'tenpayUrl'      => 'nullable|String|url',           //财付通支付网关
        'visapayUrl'     => 'nullable|String|url',           //银联支付网关
        'jdpayUrl'       => 'nullable|String|url',           //京东钱包支付网关
        'bdpayUrl'       => 'nullable|String|url',           //百度钱包支付网关
        'ylpayUrl'       => 'nullable|String|url',           //银联快捷支付网关
        'cardpayUrl'     => 'nullable|String|url',           //点卡支付网关
        'dispensingUrl'  => 'nullable|String|url',           //自动出款支付网关
        'extendName'     => 'nullable|String',               //扩展字段名称
        'typeId'         => 'required|array',                 //支付方式
        'remarkName'     => 'nullable|String',                 //备注
        'needQuery'      => 'required|integer|min:1',          //备注
//        'version'        => 'required|integer',
    ];
    //修改三方
    public $payConfigUpdateRule = [
        'payId'          => 'required|integer|min:1',       //传递的商户ID
        'confName'       => 'nullable|String',              //商户类型(三方名称)
        'confMod'        => [
            'nullable',
            'String',
            'regex:/^[A-Z]*[A-Za-z]*$/'
        ],//模型
        'inState'        => 'nullable|integer|between:1,2',  //是否开启入款1开启 2关闭
        'outState'       => 'nullable|integer|between:1,2',  //是否开启出款1开启 2关闭
        'whiteListState' => 'nullable|integer|between:1,2',  //是否开启IP白名单 1开启 2关闭
        'isStatus'       => 'nullable|integer|between:1,2',  //是否开启 1开启 2关闭
        'payCode'        => 'nullable|String',               //三方扫码编码
        'bankUrl'        => 'nullable|String|url',           //网银支付网关
        'wechatUrl'      => 'nullable|String|url',           //微信支付网关
        'alipayUrl'      => 'nullable|String|url',           //支付宝支付网关
        'qqpayUrl'       => 'nullable|String|url',           //QQ支付网关
        'tenpayUrl'      => 'nullable|String|url',           //财付通支付网关
        'visapayUrl'     => 'nullable|String|url',           //银联支付网关
        'jdpayUrl'       => 'nullable|String|url',           //京东钱包支付网关
        'bdpayUrl'       => 'nullable|String|url',           //百度钱包支付网关
        'ylpayUrl'       => 'nullable|String|url',           //银联快捷支付网关
        'cardpayUrl'     => 'nullable|String|url',           //点卡支付网关
        'dispensingUrl'  => 'nullable|String|url',           //自动出款支付网关
        'extendName'     => 'nullable|String',               //扩展字段名称
        'typeId'         => 'nullable|array',                 //支付方式
        'remarkName'     => 'nullable|String',                 //备注
//        'version'        => 'required|integer',
    ];
    //回调地址列表
    public $payCallBackUrlSelectRule = [
        'agentId'      => 'nullable|String',           //代理线
        'agentIp'      => 'nullable|String|ip',        //IP
        'agentPort'    => [
            'nullable',
            'integer',
            'regex:/^([0-9]|[1-9]\d{1,3}|[1-5]\d{4}|6[0-4]\d{4}|65[0-4]\d{2}|655[0-2]\d|6553[0-5])$/'
        ],//端口
        'siteUrl'      => 'nullable|String|url',          //站点域名
        'clientUserId' => 'nullable|integer|min:1'
    ];
    //修改、添加回调地址
    public $payCallBackUrlAdminRule = [
        'agentIp'        => 'nullable|String|ip',       //IP
        'agentPort'      => [
            'nullable',
            'integer',
            'regex:/^([0-9]|[1-9]\d{1,3}|[1-5]\d{4}|6[0-4]\d{4}|65[0-4]\d{2}|655[0-2]\d|6553[0-5])$/'
        ],       //端口
        'siteUrl'        => 'nullable|String|url',      //站点域名
        'callBackUrl'    => 'nullable|String',      //入款异步回调路由
        'outCallBackUrl' => 'nullable|String|url',      //出款异步回调路由
    ];
    //添加支付方式
    public $payTypeAddRule = [
        'typeName'    => 'required|String',               //支付方式
        'englishName' => 'nullable|String',               //支付方式别名
        'isStatus'    => 'required|integer|between:1,2',  //是否开启
    ];
    //修改支付方式
    public $payTypeUpdateRule = [
        'typeId'      => 'required|integer|min:1',       //支付ID
        'typeName'    => 'nullable|String',              //支付方式
        'englishName' => 'nullable|String',              //支付方式别名
        'isStatus'    => 'nullable|integer|between:1,2', //是否开启
    ];
    //添加客户接口
    public $apiClientsAddRule = [
        'userId'     => 'required|integer|min:1|unique:api_clients,user_id',    //用户ID
        'clientName' => 'required|String',                                      //接口名称
        'Secret'     => 'required|String',                                      //授权证书
        'Revoked'    => 'required|integer:between:1,2'                          //是否开启 1开启，2关闭
    ];
    //修改客户接口
    public $apiClientsUpdateRule = [
        'Id'         => 'required|integer|min:1',       //ID
        'clientName' => 'nullable|String',              //接口名称
        'Secret'     => 'required|String',              //授权证书
        'Revoked'    => 'required|integer|between:1,2'  //是否开启 1开启，2关闭
    ];
    //删除客户接口
    public $apiClientsDeleteRule = [
        'Id' => 'required|integer|min:1',  //ID
    ];
    public $apiAgentsSelectRule = [
        'agentName' => 'nullable|String',  //代理线路名称
    ];
    //添加代理
    public $apiAgentsAddRule = [
        'agentName' => 'required|String',               //代理线路名称
        'clientId'  => 'required|integer',              //所属平台线路ID   api_clients 表的ID
        'Revoked'   => 'required|integer|between:1,2'   //是否开启 1开启，2关闭
    ];
    //修改代理
    public $apiAgentsUpdateRule = [
        'agentId'   => 'required|String|min:1',        //ID
        'agentName' => 'required|String',               //代理线路名称
        'clientId'  => 'required|integer',              //所属平台线路ID   api_clients 表的ID
        'Revoked'   => 'required|integer|between:1,2'   //是否开启 1开启，2关闭
    ];
    //删除代理
    public $apiAgentsDeleteRule = [
        'agentId' => 'required|integer|min:1',   //ID
    ];
    //入款银行列表
    public $selectInBankRule = [
        'payId' => 'nullable|integer|min:1',  //银行列表id
    ];
    //入款银行新增
    public $addInBankRule = [
        'payId'    => 'required|integer|min:1',        //三方类型ID
        'uName'    => 'required|String',               //银行名称
        'state'    => 'required|integer|between:1,2',  //银行状态：2:关闭，1:开启
        'bankCode' => 'required|String',               //银行编码
    ];
    //入款银行编辑
    public $editInBankRule = [
        'Id'       => 'required|integer|min:1',       //银行列表id
        'payId'    => 'nullable|integer|min:1',       //三方类型ID
        'uName'    => 'nullable|String',              //银行名称
        'state'    => 'nullable|integer|between:1,2', //银行状态：2:关闭，1:开启
        'bankCode' => 'nullable|String',              //银行编码
    ];
    //入款银行删除
    public $delInBankRule = [
        'Id' => 'required|integer|min:1',  //银行列表id
    ];
    //出款银行列表
    public $selectOutBankRule = [
        'payId' => 'nullable|integer',  //银行列表id
    ];
    //出款银行新增
    public $addOutBankRule = [
        'payId'    => 'required|integer',        //三方类型ID
        'uName'    => 'required|String',         //银行名称
        'state'    => 'required|integer|between:1,2',        //银行状态：2:关闭，1:开启
        'bankCode' => 'required|String',         //银行编码
    ];
    //出款银行编辑
    public $editOutBankRule = [
        'Id'       => 'required|integer|min:1',               //银行列表id
        'payId'    => 'nullable|integer',                     //三方类型ID
        'uName'    => 'nullable|String',                      //银行名称
        'state'    => 'nullable|integer|between:1,2',         //银行状态：2:关闭，1:开启
        'bankCode' => 'nullable|String',                      //银行编码
    ];
    //入款银行删除
    public $delOutBankRule = [
        'Id' => 'required|integer|min:1',  //银行列表id
    ];
    //登陆日志
    public $loginLogSelectRule = [
        'loginIp'   => 'nullable|String|ip',  //登陆IP地址
        'account'   => 'nullable|String',           //用户名
        'startDate' => 'nullable|String',           //开始时间
        'endDate'   => 'nullable|String'            //结束时间
    ];
    //操作日志
    public $doLogSelectRule = [
        'account'     => 'nullable|String',  //用户ID
        'path'        => 'nullable|String',         //路径
        'operationIp' => 'nullable|String|ip',//IP地址
    ];
    //操作日志
    public $requestLogSelectRule = [
        'startTime' => 'nullable|String',  //用户ID
        'endTime'   => 'nullable|String',         //路径
    ];
    //回调日志
    public $callbackLogSelectRule = [
        'orderNumber' => 'nullable|String',         //订单号
        'payWay'      => 'nullable|integer',        //支付方式
        'callbackIp'  => 'nullable|String|ip',//回调来源IP
        'startDate'   => 'nullable|String',         //开始时间
        'endDate'     => 'nullable|String'          //结束时间
    ];
    //后台错误日志
    public $ErrorLogsAdminSelectRule = [
        'date' => [
            'nullable',
            'String',
            'regex:/^\d{4}(\-|\/|.)\d{1,2}\1\d{1,2}$/'
        ],
    ];
    //前台错误日志
    public $ErrorLogsFrontSelectRule = [
        'date' => [
            'nullable',
            'String',
            'regex:/^\d{4}(\-|\/|.)\d{1,2}\1\d{1,2}$/'
        ],
    ];
    //订单日志查询
    public $orderLogSelectRule = [
        'orderNumber' => 'required',         //订单号
        'date'        => 'required',        //支付方式
        //        'callbackIp'  => 'nullable|String|ip',//回调来源IP
        //        'startDate'   => 'nullable|String',         //开始时间
        //        'endDate'     => 'nullable|String'          //结束时间
    ];
    //外放接口操作日志
    public $apiOperationLogSelectRule = [
        'clientId'  => 'nullable|String',
        'startDate' => 'nullable|String',         //开始时间
        'endDate'   => 'nullable|String'          //结束时间
    ];
    //下发日志
    public $sendCallbackLogSelectRule = [
        'orderNumber' => 'nullable|String',              //订单号
        'isAutoSend'  => 'nullable|integer|between:1,2', //是否自动下发
        'way'         => 'nullable|integer|between:1,2', //入款出款下发 1为入款下发, 2为出款下发
        'startDate'   => 'nullable|String',              //开始时间
        'endDate'     => 'nullable|String'               //结束时间
    ];
    //获取订单信息API
    public $orderRunApiRule = [
        'clientUserId' => 'required|integer|min:1',
        'clientName'   => 'required|String',
        'clientSecret' => 'required|String',
        'page'         => 'required|integer|min:1',
        'startTime'    => 'required|String',
        'endTime'      => 'nullable|String',
        'business'     => 'nullable|String',
        'format'       => 'nullable|String',
        'orderId'      => 'nullable|String',
    ];
    //获取商户配置API
    public $MerchantRunApiRule = [
        'clientUserId' => [
            'required',
            'integer',
            'min:1',
        ],
        'clientId'     => [
            'required',
            'integer',
            'min:1',
        ],
        'clientName'   => [
            'required',
            'String',
        ],
        'clientSecret' => [
            'required',
            'String',
        ],
        'agentLine'    => [
            'required',
            'String',
            'regex:/^[a-zA-Z\#]{1,3}$/',
        ],
        'merId'        => [
            'nullable',
            'String',
        ],
        'payId'        => [
            'nullable',
            'integer',
        ],
        'payType'      => [
            'nullable',
            'integer',
            'min:1'
        ],
        'payState'     => [
            'nullable',
            'integer',
            'min:1'
        ],
        'merchantId'   => [
            'nullable',
            'integer',
        ],
    ];


    //获取银行配置API
    public $bankRunApiRule = [
        'clientUserId' => 'required|integer|min:1',
        'clientName'   => 'required|String',
        'clientSecret' => 'required|String',
        'payId'        => 'required|integer|min:1',
    ];
    //获取支付方式API
    public $typeRunApiRule = [
        'clientUserId' => 'required|integer|min:1',
        'clientName'   => 'required|String',
        'clientSecret' => 'required|String',
    ];
    //获取三方配置API
    public $configRunApiRule = [
        'clientUserId' => 'required|integer|min:1',
        'clientName'   => 'required|String',
        'clientSecret' => 'required|String',
        'page'         => 'nullable|integer|min:1',
        'limit'        => 'nullable|integer|min:10',
        'payName'      => 'nullable|String',
        'payState'     => 'nullable|integer|min:1',
    ];
    //添加修改商户API
    public $businessRunApiRule = [
        'clientUserId' => 'required|integer|min:1',
        'clientName'   => 'required|String',
        'clientSecret' => 'required|String',
        'agentLine'    => [
            'required',
            'String',
            'regex:/^[a-z0-9]{1,10}$/',
        ],
        'merId'        => [
            'nullable',
            'integer',
            'min:0',
            'regex:/^[0-9]+$/'
        ],
        'merchantId'   => 'required|String',                         //商户ID
        'payType'      => 'required|integer|min:1',                  //支付类型ID

        'notifyUrl' => [
            'nullable',
            'String',
            'regex:/^((ht|f)tps?):\/\/[\w\-]+(\.[\w\-]+)+([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?$/'
        ],         //回调地址
        'payId'     => [
            'regex:/^[0-9]{1,4}$/',
            'nullable',
            'integer',
            'min:1',
        ],                  //三方类型ID

        'md5PrivateKey' => 'nullable|String',                         //md5私钥
        'rsaPrivateKey' => 'nullable|String',                         //rsa私钥
        'publicKey'     => 'nullable|String',                         //公钥 (填写RSA私钥，公钥必填)
        'levelId'       => 'nullable|String',                         //层级
        'code'          => 'nullable|String',                         //支付编码，网银不填
        'isApp'         => 'nullable|integer',                        //跳转app/H5
        'message1'      => 'nullable|String',                         //预留参数1
        'message2'      => 'nullable|String',                         //预留参数2
        'message3'      => 'nullable|String',                         //预留参数3
        'merUrl'        => 'nullable|String',                         //自填写支付网关
    ];

    public $getCacheKeyRule = [
        'server'     => 'required|integer|between:1,2',
        'selectDB'   => 'required|string',
        'getKeyType' => 'required|integer|between:1,3',
        'getKeyName' => 'nullable|string',
    ];
    public $delCacheKeyRule = [
        'server'     => 'required|integer|between:1,2',
        'selectDB'   => 'required|string',
        'delKeyType' => 'required|integer|between:1,3',
        'delKeyName' => 'required|string',
    ];
    public $getCacheValRule = [
        'server'     => 'required|integer|between:1,2',
        'selectDB'   => 'required|string',
        'getValType' => 'required|integer|between:0,5',
        'getValName' => 'required|string',
        'jsonType'   => 'required|integer|between:1,2',
    ];
    public $lenCacheValRule = [
        'server'   => 'required|integer|between:1,2',
        'selectDB' => 'required|string',
        'lenType'  => 'required|integer|between:0,4',
        'lenName'  => 'required|string',
    ];

    public $payCallBackUrlAdd = [
        'clientUserId'   => 'nullable|integer|min:1',
        'agentId'        => 'nullable|string',
        'agentIp'        => 'nullable|string',
        'agentPort'      => 'nullable|string',
        'callBackUrl'    => 'required|string',
        'outCallBackUrl' => 'nullable|string',
        'siteUrl'        => 'nullable|string',
    ];

    public $payCallBackUrlUpdate = [
        'Id'             => 'required|integer|min:1',
        'clientUserId'   => 'nullable|integer|min:1',
        'agentId'        => 'nullable|string',
        'agentIp'        => 'nullable|string',
        'agentPort'      => 'nullable|string',
        'callBackUrl'    => 'required|string',
        'outCallBackUrl' => 'nullable|string',
        'siteUrl'        => 'nullable|string',
    ];

}
