<?php
/**
 * Created by PhpStorm.
 * User: js-00035
 * Date: 2019/3/22
 * Time: 17:10
 */

namespace App\Models\Logs;


use App\Models\ApiModel;

class RequestLog extends ApiModel
{
    protected $table = 'request_api_logs';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];
}