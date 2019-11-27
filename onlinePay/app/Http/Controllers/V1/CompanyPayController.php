<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\APIController;
use App\Http\Models\CompanyPay;
use Illuminate\Http\Request;

class CompanyPayController extends APIController
{
    public function toPay(Request $request)
    {
        $data = $request;

        $result = CompanyPay::getUrls($data);
        if ($result) {
            $aliUrl = env('ALI_URL');
            $url    = str_replace('[url]', $aliUrl, $result['long_url']);
            $money  = !empty($data['money']) ? $data['money'] : 0;
            $url    = str_replace('[url]', $money, $url);
            header("location:{$url}");
        } else {
            return $this->responseJson(trans('error.PayUrlError'));
        }
    }
}