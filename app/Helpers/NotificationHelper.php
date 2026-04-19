<?php

if (!function_exists('notify')) {
    function notify($title, $message, $type = 'info', $reference = null, $userId = null)
    {
        \App\Models\Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'reference' => $reference,
        ]);
    }
}