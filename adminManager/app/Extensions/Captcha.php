<?php

namespace App\Extensions;

class Captcha
{
    private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ123456789';//随机因子

    private $code;//验证码

    private $codeLen = 4;//验证码长度

    private $width = 116;//宽度

    private $height = 55;//高度

    private $img;//图形资源句柄

    private $font;//指定的字体

    private $fontSize = 20;//指定字体大小

    private $fontColor;//指定字体颜色

    private static $_instance = null;

    //构造方法初始化
    public function __construct()
    {
        $this->font = public_path("font/elephant.ttf");//注意字体路径要写对，否则显示不了图片
    }

    //生成随机码
    private function createCode()
    {
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->codeLen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $_len)];
        }
    }

    //生成背景
    private function createBg()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color     = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    //生成文字
    private function createFont()
    {
        $_x = $this->width / $this->codeLen;
        for ($i = 0; $i < $this->codeLen; $i++) {
            $this->fontColor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontSize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontColor, $this->font, $this->code[$i]);
        }
    }

    //生成线条、雪花
    private function createLine()
    {
        //线条
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        //雪花
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
    }

    //输出
    private function outPut()
    {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

    //对外生成
    public function doImg()
    {
        $this->createBg();
        $this->createLine();
        $this->createFont();
        $this->outPut();
    }

    //获取验证码
    public function getCode()
    {
        $this->createCode();
        return strtolower($this->code);
    }

    // 验证验证码
    public function check($code, $token)
    {
        $verKey       = getRedis('captcha', ['token' => md5($token)]);
        $verification = (string)RedisConPool::get($verKey);
        RedisConPool::del($verKey);
        unset($verKey);
        return (string)strtolower($code) !== $verification;
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}