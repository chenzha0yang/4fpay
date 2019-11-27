<?php

/**
 * 配置三方回调字段  出款
 */

return [
    // 出款 特别处理
    'backSpecial' => [
//        'Jiutong'      => 1,
    ],

    'Jiutong' => [//md5格式
         'order'       => 'orderNum',  //固定参数，订单号
         'md5Sign'     => '',            //固定参数，签名
         'callbackStatus' => [           // 回调固定参数，订单状态
              'remitResult',          // 键名
              '00'               // 对应的成功的值
         ],
          'sendStatus' => [           // 请求结果同步返回固定参数，订单状态
              'remitResult',          // 键名
              '00'               // 对应的成功的值
          ],

    ],
    'Boshunpay' => [//md5格式
                  'order'       => 'orderNum',  //固定参数，订单号
                  'md5Sign'     => '',            //固定参数，签名
                  'callbackStatus' => [           // 回调固定参数，订单状态
                                                  'remitResult',          // 键名
                                                  '00'               // 对应的成功的值
                  ],
                  'sendStatus' => [           // 请求结果同步返回固定参数，订单状态
                          'respCode',          // 键名
                          '0000',               // 对应的成功的值
                          'respDesc'            // 出款请求失败的原因
                  ],

    ],
];