<?php
namespace App\Extensions;


class SignOutCheck
{
    public static $model = '';

    public static $sign = [];

    //验签
    public static function SignCheck($payConf)
    {
        $backFields = trans("outBackField." . self::$model); //根据$mod获取加密类型
        if (!empty($backFields['md5Sign']) ) {
            $signType = 'MD5';
        } elseif (!empty($backFields['rsaSign'])) {
            $signType = 'RSA';
        } else {
            $signType = 'other';
        }

        if ($signType == 'MD5') {

            return self::Md5SignCheck($payConf->md5_private_key);//MD5模式验签

        } else if ($signType == 'RSA') {

            return self::RSASignCheck($payConf);//RSA验签

        } elseif ($signType == 'other') {
            //其他奇葩类型
            global $app;
            $PayModel = $app->make("App\Http\PayModels\Online\\" . self::$model);

            return $PayModel::SignOther(self::$model, self::$sign, $payConf);

        } else {
            return self::Md5SignCheck($payConf->md5_private_key);
        }
    }

    //MD5
    private static function Md5SignCheck($Md5Key)
    {
        $signArr = self::paramFormat('md5');
        $Sign = str_replace("{@-PKMd5Key-@}", $Md5Key, $signArr['signString']);

        $Sign = md5($Sign);

        if (strtoupper(self::$sign[$signArr['signField']]) == strtoupper($Sign)) {
            return true;
        } else {
            return false;
        }
    }

    //RSA
    private static function RSASignCheck($payConf)
    {
        $signArr = self::paramFormat('rsa');

        $backSign = base64_decode(self::$sign[$signArr['signField']]);

        $publicKey = openssl_get_publickey($payConf->public_key);

        $flag = openssl_verify($signArr['signString'], $backSign, $publicKey,OPENSSL_ALGO_MD5);

        if ($flag) {
            return true;
        } else {
            return false;
        }
    }

    // 组装参数
    private static function paramFormat($sType = 'md5')
    {
        $fields     = trans("outBackField." . self::$model);
        $signField  = $fields["{$sType}Sign"]; // 签名字段
        $space      = trans("{$sType}Rules." . self::$model . ".space");//加密格式{$sType}类型 分隔符
        $signString = trans("{$sType}Rules." . self::$model . ".singRule");//加密格式{$sType}类型

        foreach ($fields as $val) {
            if (!is_array($val)) {
                if (isset(self::$sign[$val])) {
                    $signString = str_replace("{@-{$val}-@}", self::$sign[$val], $signString);
                } else {
                    $signString = str_replace("{$space}{$val}={@-{$val}-@}", '', $signString);
                    $signString = str_replace("{$val}={@-{$val}-@}", '', $signString);
                    $signString = str_replace("{$val}={@-{$val}-@}{$space}", '', $signString);
                }
            }
        }

        unset($fields);
        unset($space);

        return [
            'signString' => $signString,
            'signField'  => $signField,
        ];
    }

    //附加参数
    public static function getPaySignAttribute($conf, $result)
    {
        if (!empty($result['PKAdditionFields'])) {
            foreach ($result['PKAdditionFields'] as $key =>$val) {
                self::$sign[$key] = $conf->{$val};
            }
        }

        return self::$sign;
    }
}