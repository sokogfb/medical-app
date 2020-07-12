<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SystemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * returns the elapsed time
     * @param $time
     * @return false|string
     */
    public static function elapsedTime($time)
    {
        return Carbon::parse($time)->diffForHumans();
    }

    /**
     * Write the system log files
     * @param array $data
     * @param string $channel
     * @param string $fileName
     */
    public static function log(array $data, string $channel, string $fileName)
    {
        $file = storage_path('logs/' . $fileName . '.log');

        // finally, create a formatter
        $formatter = new JsonFormatter();

        // Create the log data
        $log = [
            'ip' => request()->getClientIp(),
            'data' => $data,
        ];
        // Create a handler
        $stream = new StreamHandler($file, Logger::INFO);
        $stream->setFormatter($formatter);

        // bind it to a logger object
        $securityLogger = new Logger($channel);
        $securityLogger->pushHandler($stream);
        $securityLogger->log('info', $channel, $log);
    }

    /**
     * get greetings here
     * @return string
     */
    public static function pass_greetings_to_user()
    {
        if (date("H") < 12) {
            return "Good Morning";
        } elseif (date("H") >= 12 && date("H") < 16) {
            return "Good Afternoon";
        } elseif (date("H") >= 16) {
            return "Good Evening";
        }
    }

    /**
     * get years
     * @param int $year
     * @return int
     */
    public static function getAge(int $year)
    {
        $to = now()->createFromFormat('Y-m-d H:s:i', Carbon::create($year));
        $from = now()->createFromFormat('Y-m-d H:s:i', today());
        return $to->diffInYears($from);
    }
}
