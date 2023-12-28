<?php

use App\Notifications\SlackErrorLog;
use Illuminate\Support\Facades\Notification;

if (!function_exists('logError')) {
    /**
     * Send error report to slack.
     *
     * @param string
     * @param Throwable
     *
     * @return object
     */
    function logError(string $process, Throwable $th): void
    {
        $user = request()->user();
        $moduleProcess = $process;
        $url = url()->current();

        // Trace Data
        $throw = [];
        $throw['message'] = $th->getMessage();
        $throw['line'] = $th->getLine();
        $throw['file'] = $th->getFile();
        $throw['trace'] = $th->getTraceAsString();

        if (is_null($user))
            Notification::route('slack', config('slack.error_channel'))->notify(new SlackErrorLog(['process' => $moduleProcess, 'url' => $url, 'th' => $throw]));
        else
            Notification::send($user, new SlackErrorLog(['process' => $moduleProcess, 'url' => $url, 'th' => $throw]));
    }
}
