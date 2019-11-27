<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;

class SqlListener
{
    /**
     * SqlListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        $sql = str_replace("?", "'%s'", $event->sql);

        $log = vsprintf($sql, $event->bindings);

        $log = '[' . date('Y-m-d H:i:s') . '] ' . $log . "\r\n";
        $filepath = storage_path('logs/sql.log');
        file_put_contents($filepath, $log, FILE_APPEND);
    }
}
