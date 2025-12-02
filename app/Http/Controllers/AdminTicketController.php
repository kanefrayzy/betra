<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Events\SupportMessageSent;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    // Просмотр всех тикетов
    public function index()
    {
        // Загружаем тикеты с пользователями и подсчитываем количество сообщений
        $tickets = SupportTicket::with('user')->withCount('messages')->get();
        return view('admin.tickets.index', compact('tickets')); // Передаем переменную $tickets в представление
    }


    // Просмотр конкретного тикета
    public function show($ticketId)
    {
        $ticket = SupportTicket::with('messages', 'user')->findOrFail($ticketId);
        return view('admin.tickets.show', compact('ticket'));
    }


    // Закрытие тикета
    public function close($ticketId)
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->status = 'closed';
        $ticket->save();

        return redirect()->route('admin.tickets.index')->with('success', __('Тикет закрыт'));
    }

    // Удаление сообщения в тикете
    public function deleteMessage($ticketId, $messageId)
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        $message = $ticket->messages()->findOrFail($messageId);
        $message->delete();

        return redirect()->route('admin.tickets.show', $ticketId)->with('success', __('Сообщение удалено'));
    }

    public function storeMessage(Request $request, $ticketId)
    {
        $request->validate([
            'content' => 'required_without:image|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $ticket = SupportTicket::findOrFail($ticketId);

        $message = new SupportMessage();
        $message->ticket_id = $ticket->id;
        $message->user_id = auth()->id();
        $message->content = $request->content;

        if ($request->hasFile('image')) {
            $message->image = $request->file('image')->store('support_messages');
        }

        $message->save();

        // Загружаем связанные данные
        $message->load('user');

        // Отправка события в реальном времени
        broadcast(new SupportMessageSent($message))->toOthers();

        // Возвращаем JSON-ответ вместо редиректа
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

}
