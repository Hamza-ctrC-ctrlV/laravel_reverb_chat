<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Private chat channel (messages, read receipts)
|--------------------------------------------------------------------------
*/
Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Presence channel (online users + typing)
|--------------------------------------------------------------------------
*/
Broadcast::channel('chat-presence', function ($user) {
    return [
        'id'   => $user->id,
        'name' => $user->name,
    ];
});
