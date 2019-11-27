<?php
/**
 * Created by PhpStorm.
 * User: huaxi8
 * Date: 2018/8/20
 * Time: 下午6:54
 */

namespace App\Http\Controllers\V1;


class ViewPageController
{
    public function showHome()
    {
        return view('welcome');
    }

    public function showError()
    {
        return view('tokenError');
    }

    public function showReturn()
    {
        return "<script>alert('您已成功支付!系统将会为您自动加款!');window.close();</script>";
    }

}
