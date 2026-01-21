<?php
namespace App\Controllers\Api;

use App\Models\Calendar\Calendar;
use App\Models\Calendar\Event;

class CalendarController
{
    public function handle()
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized"); 
            return;
        }

        $method = $_SERVER['REQUEST_METHOD'];
        
        // Xử lý theo method
        if ($method === 'GET') {
            $this->handleGetRequest();
        } elseif ($method === 'POST') {
            $this->handlePostRequest();
        } else {
            $this->json(false, "Method not allowed");
        }
    }

    private function handleGetRequest()
    {
        $action = $_GET['action'] ?? 'getEvents';
        
        match ($action) {
            'getEvents' => $this->getEvents(),
            'getStats' => $this->getStats(),
            'getEventById' => $this->getEventById(),
            default => $this->json(false, "Action không hợp lệ")
        };
    }

    private function handlePostRequest()
    {
        // Xử lý cả application/x-www-form-urlencoded và application/json
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        
        if (strpos($contentType, 'application/json') !== false) {
            // JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? $_POST['action'] ?? 'createEvent';
        } else {
            // Form data
            $action = $_POST['action'] ?? 'createEvent';
        }
        
        match ($action) {
            'createEvent' => $this->createEvent(),
            'updateEvent' => $this->updateEvent(),
            'updateEventStatus' => $this->updateEventStatus(),
            'deleteEvent' => $this->deleteEvent(),
            default => $this->json(false, "Action không hợp lệ")
        };
    }

    // =========================================
    //              API METHODS
    // =========================================

    public function getEvents()
    {
        $startDate = $_GET['start'] ?? date('Y-m-01');
        $endDate = $_GET['end'] ?? date('Y-m-t');
        
        $userId = $_SESSION['user_id'];
        
        $calendarModel = new Calendar();
        $eventModel = new Event();
        
        // Lấy calendar của user
        $calendar = $calendarModel->getUserCalendar($userId);
        if (!$calendar) {
            $calendar = $calendarModel->createUserCalendar($userId);
        }
        
        // Lấy events
        $events = $eventModel->getUserEvents($userId, $startDate, $endDate);
        
        // Format events cho calendar grid
        $formattedEvents = array_map(function($event) {
            return [
                'event_id' => $event['event_id'],
                'title' => $event['title'],
                'description' => $event['description'],
                'start_at' => $event['start_at'],
                'end_at' => $event['end_at'],
                'is_all_day' => (int)$event['is_all_day'],
                'location' => $event['location'],
                'event_type' => $event['event_type'],
                'status' => $event['status'],
                'priority' => $event['priority'],
                'calendar_color' => $event['calendar_color'],
                'calendar_id' => $event['calendar_id']
            ];
        }, $events);
        
        $this->json(true, null, [
            'events' => $formattedEvents,
            'calendar' => $calendar
        ]);
    }

    public function createEvent()
    {
        // Nhận dữ liệu từ POST
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start_at = $_POST['start_at'] ?? null;
        $end_at = $_POST['end_at'] ?? null;
        $location = trim($_POST['location'] ?? '');
        $event_type = $_POST['event_type'] ?? 'event';
        $priority = $_POST['priority'] ?? 'medium';
        $is_all_day = $_POST['is_all_day'] ?? 0;
        
        // Validation
        if (empty($title)) {
            $this->json(false, "Event title is required");
            return;
        }
        
        if (empty($start_at)) {
            $this->json(false, "Start date is required");
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        $data = [
            'title' => $title,
            'description' => $description,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'location' => $location,
            'event_type' => $event_type,
            'priority' => $priority,
            'is_all_day' => $is_all_day
        ];
        
        $eventModel = new Event();
        $eventId = $eventModel->createEvent($userId, $data);
        
        if ($eventId) {
            // Get the created event
            $event = $eventModel->getEventById($eventId);
            $this->json(true, "Event created successfully", ["event" => $event]);
        } else {
            $this->json(false, "Failed to create event");
        }
    }

    public function updateEvent()
    {
        // Nhận dữ liệu từ POST
        $eventId = $_POST['id'] ?? null;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start_at = $_POST['start_at'] ?? null;
        $end_at = $_POST['end_at'] ?? null;
        $location = trim($_POST['location'] ?? '');
        $event_type = $_POST['event_type'] ?? 'event';
        $priority = $_POST['priority'] ?? 'medium';
        $is_all_day = $_POST['is_all_day'] ?? 0;
        $status = $_POST['status'] ?? 'scheduled';
        
        if (!$eventId) {
            $this->json(false, "Event ID is required");
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->checkEventOwnership($eventId, $userId)) {
            $this->json(false, "You don't have permission to edit this event");
            return;
        }
        
        $data = [
            'title' => $title,
            'description' => $description,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'location' => $location,
            'event_type' => $event_type,
            'priority' => $priority,
            'is_all_day' => $is_all_day,
            'status' => $status
        ];
        
        // Loại bỏ các field rỗng
        $updateData = [];
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                $updateData[$key] = $value;
            }
        }
        
        $eventModel = new Event();
        $success = $eventModel->update($eventId, $updateData);
        
        if ($success) {
            $event = $eventModel->getEventById($eventId);
            $this->json(true, "Event updated successfully", ["event" => $event]);
        } else {
            $this->json(false, "Failed to update event");
        }
    }

    public function updateEventStatus()
    {
        $eventId = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
        
        if (!$eventId) {
            $this->json(false, "Event ID is required");
            return;
        }
        
        if (!$status) {
            $this->json(false, "Status is required");
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->checkEventOwnership($eventId, $userId)) {
            $this->json(false, "You don't have permission to edit this event");
            return;
        }
        
        $eventModel = new Event();
        $success = $eventModel->changeStatus($eventId, $userId, $status);
        
        if ($success) {
            $event = $eventModel->getEventById($eventId);
            $this->json(true, "Event status updated successfully", ["event" => $event]);
        } else {
            $this->json(false, "Failed to update event status");
        }
    }

    public function deleteEvent()
    {
        $eventId = $_POST['id'] ?? null;
        
        if (!$eventId) {
            $this->json(false, "Event ID is required");
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->checkEventOwnership($eventId, $userId)) {
            $this->json(false, "You don't have permission to delete this event");
            return;
        }
        
        $eventModel = new Event();
        $success = $eventModel->softDelete($eventId);
        
        if ($success) {
            $this->json(true, "Event deleted successfully");
        } else {
            $this->json(false, "Failed to delete event");
        }
    }

    public function getEventById()
    {
        $eventId = $_GET['id'] ?? null;
        
        if (!$eventId) {
            $this->json(false, "Event ID is required");
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->checkEventOwnership($eventId, $userId)) {
            $this->json(false, "You don't have permission to view this event");
            return;
        }
        
        $eventModel = new Event();
        $event = $eventModel->getEventById($eventId);
        
        if ($event) {
            $this->json(true, null, ["event" => $event]);
        } else {
            $this->json(false, "Event not found");
        }
    }

    public function getStats()
    {
        $userId = $_SESSION['user_id'];
        
        $calendarModel = new Calendar();
        $stats = $calendarModel->getCalendarWithStats($userId);
        
        $this->json(true, null, [
            'total' => $stats['total_events'] ?? 0,
            'scheduled' => $stats['scheduled_events'] ?? 0,
            'completed' => $stats['completed_events'] ?? 0,
            'upcoming' => $stats['upcoming_events'] ?? 0
        ]);
    }

    // =========================================
    //              HELPER METHODS
    // =========================================

    private function checkEventOwnership($eventId, $userId)
    {
        $eventModel = new Event();
        
        // Cần thêm method getEventById trong Event model
        $event = $eventModel->getEventById($eventId);
        
        if (!$event) {
            return false;
        }
        
        // Kiểm tra calendar thuộc về user
        $calendarModel = new Calendar();
        $calendar = $calendarModel->getUserCalendar($userId);
        
        return $calendar && $event['calendar_id'] === $calendar['calendar_id'];
    }

    private function json($success, $message = null, $extra = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge([
            "success" => $success,
            "message" => $message
        ], $extra));
        exit;
    }
}
?>