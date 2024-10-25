<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsController extends Controller
{
 /**
     * Get the new notification data for the navbar notification.
     *
     * @param Request $request
     * @return array
     */
    public function getNotificationsData(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications;
    
        $dropdownHtml = '';
    
        // Añadir un div inicial que indique cuántas notificaciones hay y el botón "Mark All as Read"
        $dropdownHtml .= "<div class='dropdown-header d-flex justify-content-between align-items-center'>
                            <span>{$notifications->count()} Notifications</span>
                            <form class='m-0' action='" . route('notifications.markAllAsRead') . "' method='POST'>
                                " . csrf_field() . "
                                <button type='submit' class='btn btn-sm btn-outline-success'>Mark All as Read</button>
                            </form>
                          </div>
                          <div class='dropdown-divider'></div>";
    
        foreach ($notifications as $key => $not) {
            $icon = "<i class='mr-2 fas fa-fw fa-bell'></i>";
    
            $time = "<span class='float-right text-muted text-sm'>
                       {$not->created_at->diffForHumans(now(), \Carbon\CarbonInterface::DIFF_ABSOLUTE)}
                     </span>";
    
            $markAsReadButton = "<form action='" . route('notifications.markAsRead', $not->id) . "' method='POST' style='display:inline;'>
                                    " . csrf_field() . "
                                    " . method_field('PUT') . "
                                    <button type='submit' class='pl-1 pr-1 mr-1 btn btn-sm btn-outline-success'>
                                        <i class='bi bi-eye-slash'></i>
                                    </button>
                                 </form>";
    
            $dropdownHtml .= "
                            <a href='#' class='dropdown-item'>
                                <div class='d-flex justify-content-between align-items-center'>
                                    <div class='mr-2'>{$icon}</div>
                                    <div class='flex-grow-1' style='overflow: hidden; text-overflow: ellipsis; white-space: nowrap;'>
                                        {$not->data['message']}
                                    </div>{$time}
                                    <div class='ml-2'>{$markAsReadButton}</div>
                                </div>
                                
                            </a>";
    
            if ($key < count($notifications) - 1) {
                $dropdownHtml .= "<div class='dropdown-divider'></div>";
            }
        }
    
        return [
            'label' => $notifications->count(),
            'label_color' => 'danger',
            'icon_color' => 'dark',
            'dropdown' => $dropdownHtml,
        ];
    }

    /**
     * Show all notifications.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showNotifications(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        return view('notifications.show', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}