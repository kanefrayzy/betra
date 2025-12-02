<?php
namespace App\Livewire;

class MobileNotifications extends Notifications
{
    // Наследуем всю функциональность от основного компонента Notifications
    // Изменяем только представление (view)
    
    public function render()
    {
        return view('livewire.mobile-notifications');
    }
}