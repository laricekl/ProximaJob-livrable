<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        try {
            // Vérifier l'authentification
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();
            
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification non trouvée'
                ], 404);
            }
            
            if (!$notification->is_read) {
                $notification->update(['is_read' => true]);
            }
                
            return response()->json([
                'success' => true,
                'unread_count' => Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur markAsRead: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'notification_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        // Vérification que l'utilisateur est bien authentifié
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        try {
            // Utilisation d'une transaction pour plus de sécurité
            DB::beginTransaction();

            // Mise à jour directe sans charger les modèles (plus performant)
            $updated = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'updated_at' => now()
                ]);

            DB::commit();

            Log::info('Notifications marquées comme lues', [
                'user_id' => Auth::id(),
                'updated_count' => $updated
            ]);

            return response()->json([
                'success' => true,
                'updated_count' => $updated,
                'unread_count' => 0,
                'message' => $updated . ' notifications marquées comme lues'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Journalisation de l'erreur
            Log::error('Erreur dans markAllAsRead: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compter les notifications non lues
     */
    public function unreadCount()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'count' => 0
                ], 401);
            }

            $count = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur unreadCount: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Erreur serveur'
            ], 500);
        }
    }

    /**
     * Récupérer les notifications (pour la page complète)
     */
    public function index()
    {
        try {
            $notifications = Notification::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('notifications.index', compact('notifications'));
            
        } catch (\Exception $e) {
            Log::error('Erreur notifications index: ' . $e->getMessage());
            return back()->withError('Erreur lors du chargement des notifications');
        }
    }

    public function adminmarkAsRead($id)
{
    $notification = Notification::findOrFail($id);
    
    // Vérifier que l'utilisateur peut accéder à cette notification
    if ($notification->user_id !== auth()->id()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }
    
    $notification->update(['is_read' => true]);
    
    $unreadCount = Notification::where('user_id', auth()->id())
                                ->where('is_read', false)
                                ->count();
    
    return response()->json([
        'success' => true,
        'unread_count' => $unreadCount
    ]);
}
}