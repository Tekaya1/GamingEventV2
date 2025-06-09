<?php
require_once '../models/Team.php';
require_once '../models/User.php';

class TeamController {
    private $team;
    private $user;
    private $db;
    
    public function __construct() {
        $this->team = new Team();
        $this->user = new User();
    }
    
    public function createTeam() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'max_members' => isset($_POST['max_members']) ? (int)$_POST['max_members'] : 10,
                'logo' => $this->handleLogoUpload(),
                'created_by' => $_SESSION['user_id']
            ];
            
            if ($this->team->createTeam($data)) {
                // Automatically add creator as team leader
                $teamId = $this->team->getLastInsertId();
                // Call to a member function lastInsertId() on null

                $this->team->joinTeam($teamId, $_SESSION['user_id']);
                
                header('Location: ../views/player/teams.php?success=created');
            } else {
                header('Location: ../views/player/teams.php?action=create&error=failed');
            }
            exit();
        }
    }
    
    public function joinTeam() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id'])) {
            $teamId = (int)$_POST['team_id'];
            $userId = $_SESSION['user_id'];
            
            if ($this->team->joinTeam($teamId, $userId)) {
                header('Location: ../views/player/team.php?id='.$teamId.'&success=joined');
            } else {
                header('Location: ../views/player/team.php?id='.$teamId.'&error=failed');
            }
            exit();
        }
    }
    
    public function leaveTeam() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id'])) {
            $teamId = (int)$_POST['team_id'];
            $userId = $_SESSION['user_id'];
            
            if ($this->team->leaveTeam($teamId, $userId)) {
                header('Location: ../views/player/teams.php?success=left');
            } else {
                header('Location: ../views/player/team.php?id='.$teamId.'&error=failed');
            }
            exit();
        }
    }
    
    public function deleteTeam() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id'])) {
            $teamId = (int)$_POST['team_id'];
            
            // Check if user is team leader
            if ($this->team->isTeamLeader($teamId, $_SESSION['user_id'])) {
                if ($this->team->deleteTeam($teamId)) {
                    header('Location: ../views/player/teams.php?success=deleted');
                } else {
                    header('Location: ../views/player/team.php?id='.$teamId.'&error=failed');
                }
            } else {
                header('Location: ../views/player/team.php?id='.$teamId.'&error=unauthorized');
            }
            exit();
        }
    }
    
    private function handleLogoUpload() {
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/images/teams/';
            $fileName = uniqid() . '_' . basename($_FILES['logo']['name']);
            $targetPath = $uploadDir . $fileName;
            
            // Check file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['logo']['type'];
            
            if (in_array($fileType, $allowedTypes) && move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                return $fileName;
            }
        }
        return 'default_team.png';
    }
}


if (isset($_GET['action'])) {
    $teamController = new TeamController();
    $action = $_GET['action'];
    
    switch ($action) {
        case 'create':
            $teamController->createTeam();
            break;
        case 'join':
            $teamController->joinTeam();
            break;
        case 'leave':
            $teamController->leaveTeam();
            break;
        case 'delete':
            $teamController->deleteTeam();
            break;
    }
}
?>

