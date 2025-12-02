<?php

namespace App\Http\Controllers;

use App\Events\SupportMessageSent;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportMessageController extends Controller
{
    public function store(Request $request, $ticketId)
    {
        $request->validate([
            'content' => 'required_without:image|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $ticket = SupportTicket::findOrFail($ticketId);

        if ($ticket->user_id != Auth::id()) {
            return response()->json(['error' => __('Доступ запрещен')], 403);
        }

        $message = new SupportMessage();
        $message->ticket_id = $ticketId;
        $message->user_id = Auth::id();
        $message->content = $request->content;

        if ($request->hasFile('image')) {
            $message->image = $request->file('image')->store('support_messages');
        }

        $message->save();

        broadcast(new SupportMessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function index($ticketId)
    {
        $ticket = SupportTicket::findOrFail($ticketId);

        if ($ticket->user_id != Auth::id()) {
            abort(403, __('Доступ запрещен'));
        }

        $messages = SupportMessage::where('ticket_id', $ticketId)->get();
        return view('user.ticket.messages', compact('ticket', 'messages'));
    }
}
