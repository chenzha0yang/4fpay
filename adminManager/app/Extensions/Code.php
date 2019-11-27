<?php

namespace App\Extensions;

/**
 * 成功码 2000 开始
 * 错误码 1000 开始 小于 2000
 * 类型、状态等 从 1 开始 小于 100
 *
 * Class Code
 * @package App\Http\Extensions
 */
class Code
{
    const SUCCESS              = 200;
    const FAIL_TO_PARAM        = 422;     // 参数错误
    const LOGIN_SUCCESS        = 2000;
    const DELETE_SUCCESS       = 2001;
    const UPDATE_SUCCESS       = 2002;
    const ADD_SUCCESS          = 2003;
    const ROLE_DEL_SUCCESS     = 2004;
    const OUT_LOGIN_SUCCESS    = 2005;
    const NO_DATA              = 2006;
    const LOWER_SUCCESS        = 2007;
    const CHANGE_PASS_SUCCESS  = 2008;
    const CHANGE_OTHER_SUCCESS = 2009;
    const REDIS_DEL_FAIL       = 2010;

    const FAIL_TO_UNLOCK       = 1001;
    const NO_DEL_ID            = 1002;
    const FAIL_TO_DEL          = 1003;
    const FAIL_TO_UPDATE       = 1004;
    const FAIL_TO_ADD          = 1005;
    const FAIL_TO_PWD          = 1006;
    const PLEASE_LOGIN_AGAIN   = 1007;
    const FAIL_TO_LOGIN        = 1008;
    const FAIL_TO_CAPTCHA      = 1009;
    const FAIL_TO_REDIS        = 1010;
    const CAPTCHA_FAIL         = 1011;
    const USERNAME_PASS_FAIL   = 1012;
    const FAIL_TO_ID           = 1013;
    const FAIL_LOWER           = 1014;
    const FAIL_TO_IP           = 1015;
    const PASSWORD_IS_ERROR    = 1016;
    const PASSWORD_IS_SAME     = 1017;
    const NO_PERMISSION        = 1018;
    const FAIL_TO_CHANGE_PASS  = 1019;
    const HAVE_SAME_NAME       = 1020;
    const NOT_ALLOWED_LOGIN_IP = 1021;
    const HAVE_BEEN_MAINTAINED = 1022;
    const REMOTE_LOGIN         = 1033;
    const MORE_THAN_TWO_MONTH  = 1034;
    const BATCH_ADD_FAILURE    = 1035;
    const NO_THIS_OPTION       = 1036;
    const KEY_NOT_EXISTS       = 1037;
    const NO_THIS_KEY          = 1039; // key不存在
    const NO_ACCESS            = 1041; // 未授权访问
    const URL_NOT_FOUND        = 1042; // URL请求地址有误
    const BANK_TO_ALREADY      = 1044;
    const ALREADY_HAVE_BANK    = 1045;
    const ALREADY_HAVE_IP      = 1046;
    const USER_DISABLE         = 1047;
    const NO_SITE_AND_IP       = 1048;
    const NULL_DATE_LOGS       = 1049;
    const REQUEST_FALL         = 1051;
    const AGENT_IS_HAVE        = 1052;
    const PLEASE_LOGIN_AGAIN_2 = 1053;
    const PLEASE_LOGIN_AGAIN_3 = 1054;
    const PLEASE_LOGIN_AGAIN_4 = 1055;
    const PLEASE_LOGIN_AGAIN_5 = 1056;
    const PLEASE_LOGIN_AGAIN_6 = 1057;



    //外放接口
    const LINE_CLOSE     = 1023;
    const NO_CLIENT      = 1024;
    const NO_ORDER       = 1025;
    const TIME_ERROR     = 1026;
    const NO_CLIENT_NAME = 1027;
    const FAIL_TO_SECRET = 1028;
    const NO_NOTIFY_URL  = 1029;
    const NO_PAY_ID      = 1030;
    const NO_PUBLIC_KEY  = 1031;
    const LIMIT_AND_PAGE = 1032;
    const NULL_DATA      = 1043;
    const PAYID_PASS_FAIL = 1040;


    const ENABLED_STATUS     = 1;    //启用
    const DISABLED_STATUS    = 2;    //停用
    const MAINTENANCE_STATUS = 3;    // 维护

    const ZERO = 0;
    const ONE  = 1;
    const TWO  = 2;
    const THR  = 3;

    const SIX_DAY = 6;//时间天数

    //下发状态
    const ISSUED_NO_DOWN = 0; //未下发
    const ISSUED_SUCCESS = 1; //下发成功
    const ISSUED_FAILED  = 2;  //下发失败

    const REDIS_TYPE_STR  = 1;
    const REDIS_TYPE_SET  = 2;
    const REDIS_TYPE_LIST = 3;
    const REDIS_TYPE_HASH = 5;

    const KEY_ALL    = 1;
    const KEY_PREFIX = 2;
    const KEY_SUFFIX = 3;

    const YES   =1;


}
