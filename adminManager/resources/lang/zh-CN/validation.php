<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'paramError'           => '参数错误',
    'accepted'             => ':attribute 必须接受.',
    'active_url'           => ':attribute 不是有效的 URL.',
    'after'                => ':attribute 必须是 :date 之后的日期.',
    'after_or_equal'       => ':attribute 必须大于或等于 :date.',
    'alpha'                => ':attribute 只能包含字母.',
    'alpha_dash'           => ':attribute 只能包含字母，数字，下划线.',
    'alpha_num'            => ':attribute 只能包含字母和数字.',
    'array'                => ':attribute 必须是一个数组.',
    'before'               => ':attribute 必须小于 :date.',
    'before_or_equal'      => ':attribute 必须小于等于 :date.',
    'between'              => [
        'numeric' => ':attribute 必须在 :min 和 :max 之间.',
        'file'    => ':attribute 必须是在 :min 兆 和 :max 兆之间.',
        'string'  => ':attribute 必须是在 :min 和 :max 之间的字符.',
        'array'   => ':attribute 大小必须在 :min 和 :max 之间.',
    ],
    'boolean'              => ':attribute 必须是 true 或者 false.',
    'confirmed'            => ':attribute 确认不匹配.',
    'date'                 => ':attribute 不是有效日期.',
    'date_format'          => ':attribute 格式不匹配 :format.',
    'different'            => ':attribute 不能与:other 相同.',
    'digits'               => ':attribute 必须是长度不能能超过 :digits 的数字.',
    'digits_between'       => ':attribute 必须是介于 :min 和 :max 之间的数字.',
    'dimensions'           => ':attribute 图片尺寸不符.',
    'distinct'             => ':attribute 验证字段重复.',
    'email'                => ':attribute 电子邮件格式错误.',
    'exists'               => ':attribute 不存在.',
    'file'                 => ':attribute 必须是上传成功的文件.',
    'filled'               => ':attribute 不能为空.',
    'image'                => ':attribute 必须是图片.',
    'in'                   => ':attribute 不存在.',
    'in_array'             => ':attribute 必须存在于 :other 中.',
    'integer'              => ':attribute 必须是整型.',
    'ip'                   => ':attribute 必须为IP地址格式',
    'ipv4'                 => ':attribute 必须是 IPv4 地址.',
    'ipv6'                 => ':attribute 必须是 IPv6 地址.',
    'json'                 => ':attribute 必须是 JSON 字符串.',
    'max'                  => [
        'numeric' => ':attribute 必须小于等于 :max.',
        'file'    => ':attribute 不能大于 :max 兆.',
        'string'  => ':attribute 长度不能超过 :max.',
        'array'   => ':attribute 长度不能大于 :max.',
    ],
    'mimes'                => ':attribute 类型只能是: :values.',
    'mimetypes'            => ':attribute 类型只能是: :values.',
    'min'                  => [
        'numeric' => ':attribute 必须大于等于 :min.',
        'file'    => ':attribute 必须大于等于:min 兆.',
        'string'  => ':attribute 长度必须大于等于 :min.',
        'array'   => ':attribute 长度必须大于等于:min.',
    ],
    'not_in'               => '不能包含:attribute.',
    'numeric'              => ':attribute 必须是数字.',
    'present'              => ':attribute 没有输入.',
    'regex'                => ':attribute 正则不匹配.',
    'required'             => ':attribute 不能为空.',
    'required_if'          => ':other 等于 :value时，:attribute 是必须的.',
    'required_unless'      => ':other 等于 :values时，:attribute不能为空.',
    'required_with'        => ':values指定时 :attribute 是必须的.',
    'required_with_all'    => ':values指定时 :attribute 是必须的.',
    'required_without'     => ':values不存在时 :attribute 是必须的.',
    'required_without_all' => ':values不存在时 :attribute 是必须的.',
    'same'                 => ':attribute 和 :other 不匹配.',
    'size'                 => [
        'numeric' => ':attribute 长度必须是 :size.',
        'file'    => ':attribute 大小必须是 :size 兆.',
        'string'  => ':attribute 长度必须是 :size 的字符串.',
        'array'   => ':attribute 长度必须是 :size .',
    ],
    'string'               => ':attribute 必须是字符串.',
    'timezone'             => ':attribute 无效时区标识.',
    'unique'               => ':attribute 已存在.',
    'uploaded'             => ':attribute 上传失败.',
    'url'                  => ':attribute 格式错误.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
