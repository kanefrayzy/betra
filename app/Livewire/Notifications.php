<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class Notifications extends Component
{
    public Collection $notifications;
    public int $newNotificationsCount = 0;
    public bool $showNotifications = false;
    public bool $isOpen = false; // Для Alpine привязки

    protected $listeners = [
        'notificationUpdated' => 'refreshNotifications',
        'refresh-notifications' => 'refreshNotifications'
    ];

    /**
     * ═══════════════════════════════════════════════════════════
     *  Lazy Loading Placeholder
     * ═══════════════════════════════════════════════════════════
     */
    public function placeholder()
    {
        return <<<'HTML'
        <div class="relative">
            <button class="relative text-gray-400 p-2 rounded-full bg-[#1a2c38] animate-pulse">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </button>
        </div>
        HTML;
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Component Mount
     * ═══════════════════════════════════════════════════════════
     */
    public function mount()
    {
        $this->notifications = collect([]);
        $this->refreshNotifications();
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Refresh Notifications with Caching
     * ═══════════════════════════════════════════════════════════
     */
    public function refreshNotifications()
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->notifications = collect([]);
            return;
        }

        $cacheKey = "user_{$user->id}_notifications";
        
        $cached = Cache::remember($cacheKey, 300, function () use ($user) {
            return $user->notifications()
                ->latest()
                ->limit(10)
                ->get()
                ->toArray();
        });
        
        $this->notifications = collect($cached);
        $this->newNotificationsCount = $user->unreadNotifications()->count();
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Toggle Notifications Dropdown
     * ═══════════════════════════════════════════════════════════
     */
    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
        $this->isOpen = $this->showNotifications; // Синхронизируем с Alpine

        if ($this->showNotifications) {
            $this->markAllAsRead();
        } else {
            $this->refreshNotifications();
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Mark All Notifications as Read
     * ═══════════════════════════════════════════════════════════
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
        
        $this->newNotificationsCount = 0;
        
        $this->clearCache($user->id);
        $this->refreshNotifications();
        
        $this->dispatch('allNotificationsRead');
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Mark Single Notification as Read
     * ═══════════════════════════════════════════════════════════
     */
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            
            $this->newNotificationsCount = max(0, $this->newNotificationsCount - 1);
            
            $this->clearCache($user->id);
            $this->refreshNotifications();
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Clear Notifications Cache
     * ═══════════════════════════════════════════════════════════
     */
    protected function clearCache(int $userId): void
    {
        Cache::forget("user_{$userId}_notifications");
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Render Component
     * ═══════════════════════════════════════════════════════════
     */
    public function render()
    {
        return view('livewire.notifications');
    }
}