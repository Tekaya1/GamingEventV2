<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/User.php';

class EventController {
    private $event;
    private $user;

    public function __construct() {
        $this->event = new Event();
        $this->user = new User();
    }

    public function createEvent() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'game_id' => isset($_POST['game_id']) ? (int)$_POST['game_id'] : null,
                'start_date' => trim($_POST['start_date']),
                'end_date' => trim($_POST['end_date']),
                'max_participants' => isset($_POST['max_participants']) ? (int)$_POST['max_participants'] : null,
                'prize_pool' => isset($_POST['prize_pool']) ? (float)$_POST['prize_pool'] : 0,
                'rules' => trim($_POST['rules']),
                'status' => 'upcoming',
                'created_by' => $_SESSION['user_id']
            ];

            if ($this->event->createEvent($data)) {
                header('Location: ../views/admin/events.php?success=created');
            } else {
                header('Location: ../views/admin/events.php?action=create&error=failed');
            }
            exit();
        }
    }

    public function updateEvent() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'game_id' => isset($_POST['game_id']) ? (int)$_POST['game_id'] : null,
                'start_date' => trim($_POST['start_date']),
                'end_date' => trim($_POST['end_date']),
                'max_participants' => isset($_POST['max_participants']) ? (int)$_POST['max_participants'] : null,
                'prize_pool' => isset($_POST['prize_pool']) ? (float)$_POST['prize_pool'] : 0,
                'rules' => trim($_POST['rules']),
                'status' => trim($_POST['status'])
            ];

            if ($this->event->updateEvent($id, $data)) {
                header('Location: ../views/admin/events.php?success=updated');
            } else {
                header("Location: ../views/admin/events.php?action=edit&id={$id}&error=failed");
            }
            exit();
        }
    }

    public function deleteEvent() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            if ($this->event->deleteEvent($id)) {
                header('Location: ../views/admin/events.php?success=deleted');
            } else {
                header('Location: ../views/admin/events.php?error=failed');
            }
            exit();
        }
    }

    public function registerForEvent() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'player') {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event_id = (int)$_POST['event_id'];
            $user_id = $_SESSION['user_id'];

            if ($this->event->registerForEvent($user_id, $event_id)) {
                header("Location: ../views/events.php?id={$event_id}&success=registered");
            } else {
                header("Location: ../views/events.php?id={$event_id}&error=failed");
            }
            exit();
        }
    }

    public function updateRegistrationStatus() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $registration_id = (int)$_POST['registration_id'];
            $status = trim($_POST['status']);

            if ($this->event->updateRegistrationStatus($registration_id, $status)) {
                header('Location: ../views/admin/events.php?success=status_updated');
            } else {
                header('Location: ../views/admin/events.php?error=failed');
            }
            exit();
        }
    }

    public function updatePlayerScore() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $registration_id = (int)$_POST['registration_id'];
            $score = (int)$_POST['score'];
            $ranking = (int)$_POST['ranking'];

            if ($this->event->updatePlayerScore($registration_id, $score, $ranking)) {
                header('Location: ../views/admin/events.php?success=score_updated');
            } else {
                header('Location: ../views/admin/events.php?error=failed');
            }
            exit();
        }
    }
}

// Router
if (isset($_GET['action'])) {
    $eventController = new EventController();
    $action = $_GET['action'];

    switch ($action) {
        case 'create':
            $eventController->createEvent();
            break;
        case 'edit':
            $eventController->updateEvent();
            break;
        case 'delete':
            $eventController->deleteEvent();
            break;
        case 'register':
            $eventController->registerForEvent();
            break;
        case 'update_status':
            $eventController->updateRegistrationStatus();
            break;
        case 'update_score':
            $eventController->updatePlayerScore();
            break;
        default:
            header('Location: ../index.php');
            exit();
    }
}
