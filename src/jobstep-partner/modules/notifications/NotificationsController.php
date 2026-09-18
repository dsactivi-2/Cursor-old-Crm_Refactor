<?php
namespace modules\notifications;

use modules\user\UserService;

class NotificationsController {
    public function __construct(NotificationsService $notificationsService, UserService $userService)
    {
        $this->notificationsService = $notificationsService;
        $this->userService = $userService;
    }

    public function getNotifications(){
        $this->checkAuth();

        $order = $this->getOrderParam();
        $desc = $this->getDescParam();
        $page = $this->getPageParam();
        $limit = $this->getLimitParam();
        $lang = $this->getLangParam();

        $data = $this->notificationsService->getNotifications($order, $desc, $page, $limit, $lang);

        $desc = "User otvorio listu notifikacija.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched notifications.",
            "data" => $data
        ]);
    }

    public function togglePartnerNotifications(){
        $this->checkAuth();

        $data = json_decode(file_get_contents('php://input'), true);
        $toggle = $data['mute_all_notifications'];

        $this->notificationsService->togglePartnerNotifications($toggle);

        $desc = "User promenio globalne notifikacije.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully updated global notifications preferences."
        ]);
    }

    public function updateNotificationPreferences(){
        $this->checkAuth();

        $data = json_decode(file_get_contents('php://input'), true);
        $preferences = implode(",", $data['blocked_notification_types']);

        $this->notificationsService->updateNotificationPreferences($preferences);

        $desc = "User promenio preferencije notifikacija.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully updated notification preferences."
        ]);
    }

    public function markNotificationAsRead(){
        $this->checkAuth();

        $data = json_decode(file_get_contents('php://input'), true);
        $notification_id = $data['notification_id'];
        $read_at = date("Y-m-d H:i:s");
        $this->notificationsService->markNotificationAsRead($notification_id, $read_at);

        $desc = "User označio notifikaciju kao pročitanu.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully read notifications status."
        ]);
    }

    public function getNotificationTypePreference(){
        $this->checkAuth();

        $data = $this->notificationsService->getNotificationTypePreference();
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched notification type preferences.",
            "data" => $data
        ]);
    }

    public function getNumberOfUnreadNotifications(){
        $this->checkAuth();

        $data = $this->notificationsService->getNumberOfUnreadNotifications();
        http_response_code(200);
        echo json_encode([
            "count" => $data
        ]);
    }

    public function markNotificationAsDeleted(){
        $this->checkAuth();

        $data = json_decode(file_get_contents('php://input'), true);
        $notification_id = $data['notification_id'];
        $deleted_at = $data['deleted_at'];

        $this->notificationsService->markNotificationAsDeleted($notification_id, $deleted_at);

        $desc = "User označio notifikaciju kao obrisano.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully deleted notification."
        ]);
    }

    private function checkAuth(){
        if(!isset($_SESSION['token'])){
            http_response_code(401);
            die("Unauthorized.");
        }
        else if(!$this->userService->isAccountActive(($_SESSION['token']))){
            http_response_code(401);
            die("Unauthorized.");
        }
    }
    private function getOrderParam(){
        if(!isset($_GET['order'])){
            $order = null;
        } else {
            $order = $_GET['order'];
        }
        return $order;
    }

    private function getDescParam(){
        if(!isset($_GET['desc'])){
            $desc = null;
        } else {
            $desc = $_GET['desc'];
        }
        return $desc;
    }
    
    private function getPageParam(){
        if(!isset($_GET['page'])){
            $page = null;
        } else {
            $page = $_GET['page'];
        }
        return $page;
    }

    private function getLimitParam(){
        if(!isset($_GET['limit'])){
            $limit = null;
        } else {
            $limit = $_GET['limit'];
        }
        return $limit;
    }
    
    private function getLangParam(){
        if(!isset($_GET['lang'])){
            $lang = 'de';
        } else {
            $lang = $_GET['lang'];
        }
        return $lang;
    }
}