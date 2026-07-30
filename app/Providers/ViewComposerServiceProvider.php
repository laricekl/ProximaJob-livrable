<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification; 

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Partager les variables avec toutes les vues
        View::composer('*', function ($view) {
            if (auth()->check()) {
                // Count des notifications non lues
                $unreadNotificationsCount = Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
                
                // Notifications récentes (pour le modal)
                $notifications = Notification::where('user_id', auth()->id()) -> where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();

                $view->with([
                    'unreadNotificationsCount' => $unreadNotificationsCount,
                    'notifications' => $notifications,
                ]);
            } else {
                $view->with([
                    'unreadNotificationsCount' => 0,
                    'notifications' => collect() // Collection vide
                ]);
            }
        });

    }
}
