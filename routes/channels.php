<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Chat channels
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Inventory channels
Broadcast::channel('inventory.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Product update channel
Broadcast::channel('product.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Workforce channels
Broadcast::channel('workforce.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
