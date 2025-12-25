<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{receiver_id}', function ($user, $receiver_id) {
    return (int) $user->id === (int) $receiver_id;
});
Broadcast::channel('chat-presence', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});
