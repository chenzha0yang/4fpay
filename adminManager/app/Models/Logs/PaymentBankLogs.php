<?php

namespace App\Models\Logs;

use App\Models\ApiModel;

class PaymentBankLogs extends ApiModel
{
    protected $table = 'shorturls';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

}