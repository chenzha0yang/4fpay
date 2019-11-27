<?php
/**
 * 若需要读取文件则继承此类
 * 或者App\Http\Models\File::getPath
 * App\Http\Models\File::getFile
 * 直接调用方法
 * 或者use App\Http\Models\File;
 * File::getFile
 */

namespace App\Extensions;

class File
{
    /**
     * 获取秘钥、公钥文件
     *
     * @param string $agentId 代理线
     * @param string $agentNum 子代理线
     * @param string $className 类名(三方模型名称)
     * @param string $fileType 文件名
     * @return bool|string
     */
    public static function getPubKey($agentId = '', $agentNum = '', $className = '', $fileType = '')
    {
        $fileName = $className;

        $filePath = "key/";

        $filePath .= $agentId && $agentNum
            ? "{$agentId}_{$agentNum}/"
            : '';

        $fileName = $fileType
            ? "{$fileName}{$fileType}"
            : $fileName;

        $file = resource_path("{$filePath}{$fileName}");
        return self::get($file);
    }


    /**
     * 检查目录是否存在
     *
     * @param $dir
     * @return bool
     */
    private static function checkDir($dir)
    {
        if (is_dir($dir)) {
            return true;
        }
        return false;
    }

    /**
     * 检查文件是否存在
     *
     * @param $file
     * @return bool
     */
    public static function checkFile($file)
    {
        if (file_exists($file)) {
            return true;
        }
        return false;
    }

    /**
     * 获取文件
     *
     * @param $file
     * @return bool|string
     */
    public static function get($file)
    {
        if (self::checkFile($file)) {
            return file_get_contents($file);
        }
        return false;
    }


    /**
     * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
     * 注意：服务器需要开通fopen配置
     * @param 要写入日志里的文本内容|string $word 要写入日志里的文本内容 默认值：空值
     * @param string $path
     */
    public static function logResult($word = '', $path='logs/callback.log')
    {
        $str = '### ';
        if (is_array($word) || is_object($word)) {
            $word = json_encode($word);
            $str  .= '(is-array)--';

        }
        $content = date('Y-m-d H:i:s') . $str . $word . PHP_EOL;
        file_put_contents(storage_path($path), $content, FILE_APPEND);
    }
}