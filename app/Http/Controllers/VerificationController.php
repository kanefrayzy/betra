<?php

namespace App\Http\Controllers;

use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Notify;

class VerificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'document_type' => 'required|in:passport,id_card',
            // 'document_front' => 'required|image|max:5120', // 5MB
            // 'document_back' => 'required|image|max:5120',
            'selfie' => 'required|image|max:5120'
        ], [
            'first_name.required' => __('Введите имя'),
            'last_name.required' => __('Введите фамилию'),
            'birth_date.required' => __('Укажите дату рождения'),
            'birth_date.before' => __('Дата рождения должна быть в прошлом'),
            // 'document_front.required' => __('Загрузите фото лицевой стороны документа'),
            // 'document_back.required' => __('Загрузите фото обратной стороны документа'),
            'selfie.required' => __('Загрузите селфи с документом'),
            'document_front.image' => __('Файл должен быть изображением'),
            'document_back.image' => __('Файл должен быть изображением'),
            'selfie.image' => __('Файл должен быть изображением'),
            'document_front.max' => __('Размер файла не должен превышать 5MB'),
            'document_back.max' => __('Размер файла не должен превышать 5MB'),
            'selfie.max' => __('Размер файла не должен превышать 5MB')
        ]);

        $user = Auth::user();

        // Проверяем, нет ли уже активной заявки
        if ($user->verification && $user->verification->status === 'pending') {
            return back()->with('error', __('У вас уже есть активная заявка на верификацию'));
        }

        // Сохраняем файлы
        // $documentFront = $this->storeFile($request->file('document_front'), 'documents');
        // $documentBack = $this->storeFile($request->file('document_back'), 'documents');
        $selfie = $this->storeFile($request->file('selfie'), 'selfies');

        // Создаем или обновляем верификацию
        $verificationData = $request->only(['first_name', 'last_name', 'birth_date', 'document_type']);
        // $verificationData['document_front'] = $documentFront;
        // $verificationData['document_back'] = $documentBack;
        $verificationData['selfie'] = $selfie;
        $verificationData['status'] = 'pending';

        if ($user->verification) {
            // Удаляем старые файлы
            Storage::delete($user->verification->selfie);
            $user->verification->update($verificationData);
        } else {
            $user->verification()->create($verificationData);
        }

        // Отправляем уведомление пользователю
        $user->notify(Notify::send('verification', ['message' => __('Ваша заявка на верификацию отправлена на рассмотрение')]));

        return back()->with('success', __('Документы успешно отправлены на проверку'));
    }

    private function storeFile($file, $folder)
    {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs("verifications/{$folder}", $filename, 'public');
    }
}
