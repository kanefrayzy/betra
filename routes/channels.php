<?php



Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    $ticket = \App\Models\SupportTicket::findOrFail($ticketId);
    return $user->id === $ticket->user_id || $user->is_support;
});

Broadcast::channel('test-channel', \App\Broadcasting\WSocketBroadcaster::class);
