<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Events\QueryExecuted;

class QueryLoggingServiceProvider extends ServiceProvider
{
    // Property to hold the total execution time
    protected $totalExecutionTime = 0;

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Listen to all query executed events
        DB::listen(function (QueryExecuted $query) {
            // Format the query with bindings
            $sql = $this->formatQuery($query->sql, $query->bindings);

            // Add the execution time to the total
            $this->totalExecutionTime += $query->time;

            // Log each query with its execution time
            $log = [
                'sql' => $sql,
                'time' => $query->time . ' ms',
                'connection' => $query->connectionName,
            ];
            Log::channel('querylog')->info(json_encode($log));
        });

        // Log the total execution time at the end of the request
        app()->terminating(function () {
            Log::channel('querylog')->info('Total execution time of all the queries: ' . $this->totalExecutionTime . ' ms');
        });
    }

    /**
     * Format the SQL query with bindings.
     *
     * @param string $sql
     * @param array $bindings
     * @return string
     */
    protected function formatQuery($sql, $bindings)
    {
        foreach ($bindings as $binding) {
            // Properly format each binding
            if (is_numeric($binding)) {
                $value = $binding;
            } else {
                $value = "'" . addslashes($binding) . "'";
            }

            // Replace the first occurrence of the placeholder with the binding
            $sql = preg_replace('/\?/', $value, 1);
        }

        return $sql;
    }
}
