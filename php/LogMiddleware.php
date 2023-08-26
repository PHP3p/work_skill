<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

/**引入全局中间件
$app->middleware([
App\Http\Middleware\LogMiddleware::class
]);
 */

class  LogMiddleware
{

    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // 获取当前请求的路由信息
        $routeName = $request->getRequestUri();
        $routeAction = $request->getPathInfo();
        // 获取当前请求的参数信息
        $params = $request->all();

        // 创建日志记录器实例
        $logger = new Logger('my_logger');
        // 指定日志文件路径
        $logFilePath = storage_path('logs/local/test.log');
        $streamHandler = new StreamHandler($logFilePath, Logger::DEBUG);
        $logger->pushHandler($streamHandler);

        // 记录日志
//         $logger->info('This is an informational message.');
//         $logger->error('An error occurred.');
        /* $monolog = Log::getMonolog();
         $monolog->popHandler();*/
        // Log::useDailyFiles(storage_path('logs/local/test.log'));
        // 监听 SQL 查询事件并记录 SQL 语句和绑定参数信息

        DB::listen(function ($query) use ($logger) {
            // $sql = $query->sql;
            $bindings = $query->bindings;
            $time = $query->time;

            $sql = array_reduce($query->bindings, function ($sql, $binding) {
                return preg_replace('/\?/', is_numeric($binding) ? $binding : sprintf("'%s'", $binding), $sql, 1);
            }, $query->sql);

            // 记录 SQL 语句和绑定参数信息
            $logger->info("WyySQL: $sql, Bindings: " . json_encode($bindings) . ", Time: $time ms");
            // 记录 SQL 语句和绑定参数信息
            // Log::channel('request')->info("Wyy1SQL: $sql, Bindings: " . json_encode($bindings) . ", Time: $time ms");
        });

        // 记录请求信息
        $logger->info("WyyRoute: $routeName, Action: $routeAction, Params: " . json_encode($params));
// 记录请求信息
//         Log::channel('request')->info("Wyy1Route: $routeName, Action: $routeAction, Params: " . json_encode($params));
        // 继续处理请求
        return $next($request);
    }
}
