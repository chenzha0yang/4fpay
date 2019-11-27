<?php

/**
 * 所有 固定的 redis key 集中管理
 */
return [
    'adminSession'           => [
        'connect' => 'session',     // 选择的链接
        'key'     => 'adminSession:key',  // key名 :Id 标识替换的值
    ],

];