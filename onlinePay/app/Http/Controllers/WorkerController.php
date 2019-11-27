<?php
namespace App\Http\Controllers;

class WorkerController  extends Controller
{
    /**
     * 协议
     * @param $port
     * @return string
     */
    public static function protocol($port)
    {
        return "text://0.0.0.0:{$port}";
    }

    /**
     * 返回提示信息
     *
     * @param $code
     * @param string $type
     * @param array $replace
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public static function translate($code, $type = 'success', $replace = [])
    {
        if ($code === Code::SUCCESS) {
            $type = 'success';
        }

        return trans("{$type}.{$code}", $replace);
    }
}
