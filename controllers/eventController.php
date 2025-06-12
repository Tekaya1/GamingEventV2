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
            // Process video data
            $video_url = !empty($_POST['video_url']) ? trim($_POST['video_url']) : null;
            $video_type = !empty($_POST['video_type']) ? trim($_POST['video_type']) : null;
            $video_thumbnail = !empty($_POST['video_thumbnail']) ? trim($_POST['video_thumbnail']) : null;

            // Validate video data if provided
            if ($video_url && $video_type) {
                if ($video_type === 'youtube') {
                    // Extract YouTube ID from URL if full URL is provided
                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/([^"&?\/\s]{11})/', $video_url, $matches)) {
                        $video_url = $matches[1];
                    }
                    // Validate it's a valid YouTube ID (11 characters)
                    if (strlen($video_url) !== 11) {
                        header('Location: ../views/admin/events.php?action=create&error=invalid_youtube');
                        exit();
                    }
                } elseif ($video_type === 'twitch') {
                    // Extract Twitch video ID if full URL is provided
                    if (preg_match('/twitch\.tv\/videos\/(\d+)/i', $video_url, $matches)) {
                        $video_url = $matches[1];
                    }
                    // Validate it's a numeric ID
                    if (!is_numeric($video_url)) {
                        header('Location: ../views/admin/events.php?action=create&error=invalid_twitch');
                        exit();
                    }
                }
            }

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
                'creator' => $_SESSION['username'],
                'created_by' => $_SESSION['user_id'],
                'video_url' => $video_url,
                'video_type' => $video_type,
                'video_thumbnail' => $video_thumbnail
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

            // Process video data
            $video_url = !empty($_POST['video_url']) ? trim($_POST['video_url']) : null;
            $video_type = !empty($_POST['video_type']) ? trim($_POST['video_type']) : null;
            $video_thumbnail = !empty($_POST['video_thumbnail']) ? trim($_POST['video_thumbnail']) : null;

            // Validate video data if provided
            if ($video_url && $video_type) {
                if ($video_type === 'youtube') {
                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/([^"&?\/\s]{11})/', $video_url, $matches)) {
                        $video_url = $matches[1];
                    }
                    if (strlen($video_url) !== 11) {
                        header("Location: ../views/admin/events.php?action=edit&id={$id}&error=invalid_youtube");
                        exit();
                    }
                } elseif ($video_type === 'twitch') {
                    if (preg_match('/twitch\.tv\/videos\/(\d+)/i', $video_url, $matches)) {
                        $video_url = $matches[1];
                    }
                    if (!is_numeric($video_url)) {
                        header("Location: ../views/admin/events.php?action=edit&id={$id}&error=invalid_twitch");
                        exit();
                    }
                }
            }

            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'game_id' => isset($_POST['game_id']) ? (int)$_POST['game_id'] : null,
                'start_date' => trim($_POST['start_date']),
                'end_date' => trim($_POST['end_date']),
                'max_participants' => isset($_POST['max_participants']) ? (int)$_POST['max_participants'] : null,
                'prize_pool' => isset($_POST['prize_pool']) ? (float)$_POST['prize_pool'] : 0,
                'rules' => trim($_POST['rules']),
                'status' => trim($_POST['status']),
                'video_url' => $video_url,
                'video_type' => $video_type,
                'video_thumbnail' => $video_thumbnail
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

            // Call the method to register the user and update the participant count
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
?>
