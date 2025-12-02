<?php
namespace App\Livewire;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Notifications extends Component
{
    public $notifications = [];
    public $newNotificationsCount = 0;
    public $showNotifications = false;

    protected $listeners = ['notificationUpdated' => 'refreshNotifications'];

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        $user = Auth::user();
        if ($user) {
            $cacheKey = "user_{$user->id}_notifications";
            $this->notifications = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
                return $user->notifications()->latest()->limit(10)->get();
            });
            $this->newNotificationsCount = $user->unreadNotifications()->count();
        }
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;

        if ($this->showNotifications) {
            $this->markAllAsRead();
        } else {
            $this->refreshNotifications();
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            $this->newNotificationsCount = 0;
            $this->dispatch('allNotificationsRead');
            Cache::forget("user_{$user->id}_notifications");
            $this->refreshNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            if ($notification && is_null($notification->read_at)) {
                $notification->markAsRead();
                $this->newNotificationsCount = max(0, $this->newNotificationsCount - 1);
                Cache::forget("user_{$user->id}_notifications");
                $this->refreshNotifications();
            }
        }
    }

    public function render()
    {
        return view('livewire.notifications');
    }
}
