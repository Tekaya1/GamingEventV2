<?php
require_once '../models/Team.php';
require_once '../models/User.php';

class TeamController
{
    private $team;
    private $user;

    public function __construct()
    {
        $this->team = new Team();
        $this->user = new User();
    }

    public function createTeam()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'max_members' => isset($_POST['max_members']) ? (int) $_POST['max_members'] : 10,
                'logo' => $this->handleLogoUpload(),
                'created_by' => $_SESSION['user_id']
            ];

            if ($this->team->createTeam($data)) {
                $teamId = $this->team->getLastInsertId();

                // Add creator as team leader
                $this->team->addMember($teamId, $_SESSION['user_id'], 'leader');

                header('Location: ../views/player/teams.php?success=created');
                exit();
            }

            header('Location: ../views/player/teams.php?action=create&error=failed');
            exit();
        }
    }

    public function joinTeam()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id'])) {
            $teamId = (int) $_POST['team_id'];
            $userId = $_SESSION['user_id'];

            if ($this->team->joinTeam($teamId, $userId)) {
                header('Location: ../views/player/team.php?id=' . $teamId . '&success=joined');
                exit();
            }

            header('Location: ../views/player/team.php?id=' . $teamId . '&error=failed');
            exit();
        }
    }

    public function leaveTeam()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id'])) {
            $teamId = (int) $_POST['team_id'];
            $userId = $_SESSION['user_id'];

            if ($this->team->leaveTeam($teamId, $userId)) {
                header('Location: ../views/player/teams.php?success=left');
                exit();
            }

            header('Location: ../views/player/team.php?id=' . $teamId . '&error=failed');
            exit();
        }
    }

    public function deleteTeam()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id'])) {
            $teamId = (int) $_POST['team_id'];

            if ($this->team->isTeamLeader($teamId, $_SESSION['user_id'])) {
                if ($this->team->deleteTeam($teamId)) {
                    header('Location: ../views/player/teams.php?success=deleted');
                    exit();
                }

                header('Location: ../views/player/team.php?id=' . $teamId . '&error=failed');
                exit();
            }

            header('Location: ../views/player/team.php?id=' . $teamId . '&error=unauthorized');
            exit();
        }
    }

    public function inviteToTeam()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id']) && isset($_POST['recipient_id'])) {
            $teamId = (int) $_POST['team_id'];
            $recipientId = (int) $_POST['recipient_id'];
            $senderId = $_SESSION['user_id'];

            // Verify requester is team leader
            if (!$this->team->isTeamLeader($teamId, $senderId)) {
                $_SESSION['invite_error'] = 'Unauthorized action';
                header("Location: ../views/player/team.php?id=$teamId");
                exit();
            }

            if ($this->team->sendInvitation($teamId, $senderId, $recipientId)) {
                $_SESSION['invite_success'] = 'Invitation sent successfully';
            } else {
                $_SESSION['invite_error'] = 'Failed to send invitation';
            }

            header("Location: ../views/player/team.php?id=$teamId");
            exit();
        }


    }

    public function removeMember()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id']) && isset($_POST['user_id'])) {
            $teamId = (int) $_POST['team_id'];
            $memberId = (int) $_POST['user_id'];
            $requesterId = $_SESSION['user_id'];

            // Verify requester is team leader and not trying to remove themselves
            if (!$this->team->isTeamLeader($teamId, $requesterId) || $memberId === $requesterId) {
                header('Location: ../views/player/team.php?id=' . $teamId . '&error=unauthorized');
                exit();
            }

            if ($this->team->removeMember($teamId, $memberId)) {
                header('Location: ../views/player/team.php?id=' . $teamId . '&success=member_removed');
                exit();
            }

            header('Location: ../views/player/team.php?id=' . $teamId . '&error=remove_failed');
            exit();
        }
    }

    public function updateMemberRole()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id']) && isset($_POST['user_id']) && isset($_POST['new_role'])) {
            $teamId = (int) $_POST['team_id'];
            $memberId = (int) $_POST['user_id'];
            $newRole = $_POST['new_role'];
            $requesterId = $_SESSION['user_id'];

            // Verify requester is team leader
            if (!$this->team->isTeamLeader($teamId, $requesterId)) {
                header('Location: ../views/player/team.php?id=' . $teamId . '&error=unauthorized');
                exit();
            }

            if ($this->team->updateMemberRole($teamId, $memberId, $newRole)) {
                header('Location: ../views/player/team.php?id=' . $teamId . '&success=role_updated');
                exit();
            }

            header('Location: ../views/player/team.php?id=' . $teamId . '&error=role_update_failed');
            exit();
        }
    }

    public function transferLeadership()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id']) && isset($_POST['new_leader_id'])) {
            $teamId = (int) $_POST['team_id'];
            $newLeaderId = (int) $_POST['new_leader_id'];
            $currentLeaderId = $_SESSION['user_id'];

            // Verify current user is team leader
            if (!$this->team->isTeamLeader($teamId, $currentLeaderId)) {
                header('Location: ../views/player/team.php?id=' . $teamId . '&error=unauthorized');
                exit();
            }

            if ($this->team->transferLeadership($teamId, $currentLeaderId, $newLeaderId)) {
                header('Location: ../views/player/team.php?id=' . $teamId . '&success=leadership_transferred');
                exit();
            }

            header('Location: ../views/player/team.php?id=' . $teamId . '&error=transfer_failed');
            exit();
        }
    }

    private function handleLogoUpload()
    {
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


    public function searchUsers()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/auth/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_action']) && $_POST['search_action'] === 'search') {
            $searchQuery = trim($_POST['search'] ?? '');
            $teamId = isset($_POST['team_id']) ? (int) $_POST['team_id'] : 0;

            // Validate input
            if (empty($searchQuery)) {
                $_SESSION['search_error'] = 'Search query cannot be empty';
                header("Location: ../views/player/team.php?id=$teamId");
                exit();
            }

            if ($teamId <= 0) {
                $_SESSION['search_error'] = 'Invalid team ID';
                header("Location: ../views/player/team.php?id=$teamId");
                exit();
            }

            try {
                $results = $this->user->searchUsers($searchQuery, $teamId);
                $_SESSION['search_results'] = $results;
                $_SESSION['search_query'] = $searchQuery;
            } catch (Exception $e) {
                $_SESSION['search_error'] = 'Error searching users';
                error_log("Search error: " . $e->getMessage());
            }

            header("Location: ../views/player/team.php?id=$teamId");
            exit();
        }

        header('Location: ../views/player/teams.php');
        exit();
    }
}
if (isset($_GET['action'])) {
    $teamController = new teamController();
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
        case 'search_users':
            $teamController->searchUsers();
            break;
        case 'invite':
            $teamController->inviteToTeam();
            break;
        case 'remove_member':
            $teamController->removeMember();
            break;
        case 'update_role':
            $teamController->updateMemberRole();
            break;
        case 'transfer_leadership':
            $teamController->transferLeadership();
            break;
        default:
            header('Location: ../views/player/teams.php');
            exit();
    }
}