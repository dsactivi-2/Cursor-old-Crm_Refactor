<?php
namespace modules\notifications;
use modules\database\Database;
use modules\user\UserService;

class NotificationsService{
    public function __construct(Database $database, UserService $userService)
    {
        $this->conn = $database->getConnection();
        $this->userService = $userService;
    }

    public function getNotifications($order = null, $desc = null, $page = null, $limit = null, $lang){
        $user_id = $this->userService->getUserIdByToken();

        $order_query = " ORDER BY idk_partner_notification.notification_created_at DESC";
        if($desc != null || $desc == "false"){
            $order_query = " ORDER BY notification_created_at ASC";
        }

        $pagination_query = "";
        if($page != null && $limit != null){
            $offset = ($page - 1) * $limit;
            $pagination_query = " LIMIT $offset, $limit";
        }

        $sql = "SELECT idk_partner_notification.notification_id, notification_title, notification_content, notification_type_id, notification_payload, notification_action, notification_sent_at, read_at 
                FROM idk_partner_notification 
                LEFT JOIN idk_partner_notification_status ON idk_partner_notification.notification_id = idk_partner_notification_status.notification_id 
                WHERE notification_partner_id = :user_id AND idk_partner_notification_status.deleted_at IS NULL $order_query $pagination_query";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $notifications = $stmt->fetchAll();

        $data = [];
        foreach($notifications as $notification){
            $content_json = json_decode($notification['notification_content'], true);
            if($lang == "en"){
                $content = $content_json['en'];
            } else {
                $content = $content_json['de'];
            }

            $title_json = json_decode($notification['notification_title'], true);
            if($lang == "en"){
                $title = $title_json['en'];
            } else {
                $title = $title_json['de'];
            }
            $data[] = [
                'id' => $notification['notification_id'],
                'title' => $title,
                'body' => $content,
                'type_id' => $notification['notification_type_id'],
                'data' => $notification['notification_payload'],
                'action' => $notification['notification_action'],
                'sent_at' => $notification['notification_sent_at'],
                'read_at' => $notification['read_at']
            ];
        }
        return $data;
    }

    public function togglePartnerNotifications($toggle){
        $user_id = $this->userService->getUserIdByToken();

        $sql = "UPDATE idk_partner_personal_notifications_preferences SET mute_all_notifications = :toggle WHERE partner_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':toggle', $toggle);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    public function updateNotificationPreferences($preferences){
        $user_id = $this->userService->getUserIdByToken();

        $sql = "UPDATE idk_partner_personal_notifications_preferences SET blocked_notification_types = :blocked_notification_types WHERE partner_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':blocked_notification_types', $preferences);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    public function markNotificationAsRead($notification_id, $read_at){
        $date = $read_at;
        $status_id = $this->checkNotificationStatus($notification_id);
        if($status_id == null){
            $this->createNotificationStatusRead($notification_id, $date);
        } else {
            $this->updateNotificationStatusRead($status_id, $date);
        }
    }

    public function markNotificationAsDeleted($notification_id, $read_at){
        $date = date("Y-m-d H:i:s", strtotime($read_at));
        $status_id = $this->checkNotificationStatus($notification_id);

        if($status_id == null){
            $this->createNotificationStatusDeleted($notification_id, $date);
        } else {
            $this->updateNotificationStatusDeleted($status_id, $date);
        }
    }

    public function getNotificationTypePreference(){
        $user_id = $this->userService->getUserIdByToken();
        $data = [];

        $sql = "SELECT blocked_notification_types, mute_all_notifications FROM idk_partner_personal_notifications_preferences WHERE partner_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $preferences = $stmt->fetch();

        if($preferences != []){
            $mute = $preferences['mute_all_notifications'];
            $data["mute_all_notifications"] = $mute == 1 ? true : false;
        } else {
            $data["mute_all_notifications"] = false;
        }

        if($preferences['blocked_notification_types'] != null){
            $blocked = $preferences['blocked_notification_types'];
        } else {
            $blocked = 0;
        }

        $sql = "SELECT type_id, type_name, CASE WHEN type_id IN ($blocked) THEN 0 ELSE 1 END AS blocked FROM idk_partner_personal_notification_types";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $types = $stmt->fetchAll();

        foreach($types as $type){
            $nData = [];
            $nData["id"] = $type["type_id"];
            $nData["name"] = $type["type_name"];
            $nData["enabled"] = $type["blocked"] == 1 ? true : false;

            $data["notification_types"][] = $nData;
        }

        return $data;
    }

    public function getNumberOfUnreadNotifications(){
        $user_id = $this->userService->getUserIdByToken();
        $sql = "SELECT
                    count(idk_partner_notification.notification_id) as number
                FROM
                    idk_partner_notification
                LEFT JOIN idk_partner_notification_status ON idk_partner_notification.notification_id = idk_partner_notification_status.notification_id
                WHERE
                    (
                        read_at IS NULL OR status_id IS NULL
                    ) AND idk_partner_notification.notification_partner_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $unread_notifications = $stmt->fetch();

        return $unread_notifications['number'];
    }

    private function checkNotificationStatus($notification_id){
        $user_id = $this->userService->getUserIdByToken();
        $sql = "SELECT status_id FROM idk_partner_notification_status WHERE notification_id = :notification_id AND partner_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':notification_id', $notification_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $status = $stmt->fetch();
        return $status['status_id'];
    }

    private function createNotificationStatusRead($notification_id, $date){
        $user_id = $this->userService->getUserIdByToken();
        $sql = "INSERT INTO idk_partner_notification_status (notification_id, partner_id, read_at) VALUES (:notification_id, :user_id, :read_at)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':notification_id', $notification_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':read_at', $date);
        $stmt->execute();
    }

    private function updateNotificationStatusRead($status_id, $date){
        $sql = "UPDATE idk_partner_notification_status SET read_at = :read_at WHERE status_id = :status_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':read_at', $date);
        $stmt->bindParam(':status_id', $status_id);
        $stmt->execute();
    }

    private function createNotificationStatusDeleted($notification_id, $date){
        $user_id = $this->userService->getUserIdByToken();
        $sql = "INSERT INTO idk_partner_notification_status (notification_id, partner_id, read_at) VALUES (:notification_id, :user_id, :deleted_at)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':notification_id', $notification_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':deleted_at', $date);
        $stmt->execute();
    }

    private function updateNotificationStatusDeleted($status_id, $date){
        $sql = "UPDATE idk_partner_notification_status SET deleted_at = :deleted_at WHERE status_id = :status_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':deleted_at', $date);
        $stmt->bindParam(':status_id', $status_id);
        $stmt->execute();
    }
}