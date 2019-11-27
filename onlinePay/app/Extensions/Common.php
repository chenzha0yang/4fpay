<?php
/**
 * 公共函数
 * 只负责逻辑处理
 * 不链接数据库
 */

namespace App\Extensions;


trait Common
{
    //time
    public static function getIntervalDate()
    {
        $time             = time();
        $newDate['start'] = date('Y-m-d H:i:d', $time - (60 * 60));
        $newDate['end']   = date('Y-m-d H:i:d', $time + (60 * 60));
        return $newDate;
    }

    /**
     * 判断提交类型
     *
     * @param string $resType
     * @param string $type
     * @return bool
     */
    public function checkType($resType, $type = 'JSON')
    {
        if (strcmp(strtoupper($resType), strtoupper($type)) == 0) {
            return true;
        }

        return false;
    }

    /**
     * 获取返回数据类型处理方法名
     *
     * @param $resType
     * @param $className
     * @return bool|string
     */
    public function getResFunc($resType, $className)
    {
        if ($this->checkType($resType, 'JSON')) {

            $func = 'json_decode';

        } elseif ($this->checkType($resType, 'XML')) {

            $func = 'App\Http\Extensions\Curl::xmlToData';

        } elseif ($this->checkType($resType, 'STR')) {

            $func = 'App\Http\Extensions\Curl::strToData';

        } elseif ($this->checkType($resType, 'OTHER')) {

            $func = "$className::getQrCode";

        } else {
            return false;
        }

        return $func;
    }

    /**
     * 获取提交方法名
     *
     * @param $reqType
     * @return string
     */
    public function getReqFunc($reqType)
    {
        if ($this->checkType($reqType, 'curl')) {

            $func = 'curlRequest';

        } elseif ($this->checkType($reqType, 'form')) {

            $func = 'buildForm';

        } elseif ($this->checkType($reqType, 'fileGet')) {

            $func = 'fileGetRequest';

        } else {

            $func = 'buildForm';

        }

        return $func;
    }

    /**
     * @param $className
     * @return mixed
     */
    public static function replaceName($className)
    {
        return str_replace('App\Http\PayModels\Online\\', '', $className);
    }

    /**
     * 极端情况下使用，如json_decode方法始终无法解析json串的情况
     *
     * @param $json
     * @return mixed
     */
    public static function json_decode($json)
    {
        $comment = false;
        $length  = strlen($json);
        $out     = '$x=';
        for ($i = 0; $i < $length; $i++) {
            if (!$comment) {
                if (($json[$i] === '{') || ($json[$i] === '['))
                    $out .= ' array(';
                else if (($json[$i] === '}') || ($json[$i] === ']'))
                    $out .= ')';
                else if ($json[$i] === ':')
                    $out .= '=>';
                else $out .= $json[$i];
            } else {
                $out .= $json[$i];
            }
            if ($json[$i] === '"' && $json[($i - 1)] !== "\\") {
                $comment = !$comment;
            }
        }
        eval($out . ';');
        return $x;
    }
}