<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    // Страница для создания нового тикета
    public function create()
    {
        return view('user.ticket.create');
    }

    // Создание нового тикета
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
        ]);

        $ticket = new SupportTicket();
        $ticket->user_id = Auth::id(); // ID текущего пользователя
        $ticket->subject = $request->subject;
        $ticket->save();

        return redirect()->route('support.ticket.index')->with('success', __('Тикет создан.'));
    }

    // Получение всех тикетов текущего пользователя
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())->get();
        return view('user.ticket.index', compact('tickets'));
    }

    // Просмотр одного тикета
    public function show($ticketId)
    {
        $ticket = SupportTicket::findOrFail($ticketId);

        if ($ticket->user_id != Auth::id()) {
            abort(403, __('Доступ запрещен'));
        }

        return view('user.ticket.show', compact('ticket'));
    }
}
