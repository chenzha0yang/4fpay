<?php

namespace App\Models\Logs;

use App\Models\ApiModel;


class ApiOperationLog extends ApiModel
{
    protected $table = 'api_operation_log';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];


}