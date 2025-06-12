<?php
require_once '../../bootstrap.php';
require_once '../../models/Team.php';
require_once '../../models/User.php';

$teamModel = new Team();
$userModel = new User();

if (!isset($_GET['id'])) {
    header('Location: teams.php');
    exit();
}

$teamId = (int) $_GET['id'];
$team = $teamModel->getTeamById($teamId);
$members = $teamModel->getTeamMembers($teamId);

if (!$team) {
    header('Location: teams.php');
    exit();
}

$isLoggedIn = isset($_SESSION['user_id']);
$currentUserId = $isLoggedIn ? $_SESSION['user_id'] : null;
$isMember = $isLoggedIn ? $teamModel->isTeamMember($teamId, $currentUserId) : false;
$isLeader = $isLoggedIn ? $teamModel->isTeamLeader($teamId, $currentUserId) : false;

// Get pending invitations if leader
$pendingInvitations = $isLeader ? $teamModel->getPendingInvitations($teamId) : [];
?>
<?php include '../../includes/header.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <!-- Team Header -->
        <div class="relative">
            <div class="h-48 bg-gradient-to-r from-purple-900 to-blue-900 flex items-center justify-center">
                <div class="flex items-center space-x-6">
                    <img src="<?php echo BASE_URL; ?>assets/images/teams/<?php echo htmlspecialchars($team['logo'] ?? 'default_team.png'); ?>"
                        alt="Team Logo" class="w-24 h-24 rounded-full border-4 border-white">
                    <h1 class="text-4xl font-bold text-center font-gaming text-white">
                        <?php echo htmlspecialchars($team['name']); ?>
                    </h1>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-gray-900 to-transparent h-16"></div>
        </div>

        <!-- Team Details -->
        <div class="p-6 md:p-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Main Content -->
                <div class="w-full md:w-2/3">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 rounded-full bg-gray-700 text-gray-300 text-sm font-medium">
                                <?php echo count($members); ?> members
                            </span>
                            <?php if ($isLeader): ?>
                                <span class="px-3 py-1 rounded-full bg-yellow-600 text-white text-sm font-medium">
                                    Team Leader
                                </span>
                            <?php elseif ($isMember): ?>
                                <span class="px-3 py-1 rounded-full bg-green-600 text-white text-sm font-medium">
                                    Member
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php
                        $currentMembers = count($members);
                        $maxMembers = $team['max_members'] ?? 5;
                        $isTeamFull = $currentMembers >= $maxMembers;
                        ?>

                        <?php if ($isLoggedIn): ?>
                            <?php if ($isMember): ?>
                                <form action="../../controllers/teamController.php?action=leave" method="POST">
                                    <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                                    <button type="submit"
                                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">
                                        Leave Team
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="../../controllers/teamController.php?action=join" method="POST">
                                    <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                                    <button type="submit"
                                        class="px-4 py-2 rounded-lg <?php echo $isTeamFull ? 'bg-gray-500 cursor-not-allowed' : 'bg-purple-600 hover:bg-purple-700'; ?> text-white text-sm font-medium transition"
                                        <?php echo $isTeamFull ? 'disabled' : ''; ?>>
                                        <?php echo $isTeamFull ? 'Team Full' : 'Join Team'; ?>
                                    </button>
                                    <?php if ($isTeamFull): ?>
                                        <p class="text-xs text-gray-400 mt-1">
                                            This team has reached its maximum capacity
                                            (<?php echo $currentMembers; ?>/<?php echo $maxMembers; ?>)
                                        </p>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="prose prose-invert max-w-none">
                        <h3 class="text-xl font-semibold mb-3">About This Team</h3>
                        <p><?php echo nl2br(htmlspecialchars($team['description'])); ?></p>

                        <h3 class="text-xl font-semibold mb-3 mt-6">Team Members</h3>
                        <div class="bg-gray-700 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-600">
                                <thead class="bg-gray-800">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Member</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Role</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                            Joined</th>
                                        <?php if ($isLeader): ?>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                                Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-700 divide-y divide-gray-600">
                                    <?php foreach ($members as $member): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full"
                                                            src="<?php echo BASE_URL; ?>assets/images/avatars/<?php echo htmlspecialchars($member['avatar'] ?? 'default.png'); ?>"
                                                            alt="">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium">
                                                            <?php echo htmlspecialchars($member['username']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php echo $member['role'] === 'leader' ? 'bg-yellow-600 text-white' :
                                                        ($member['role'] === 'co-leader' ? 'bg-purple-600 text-white' : 'bg-blue-600 text-white'); ?>">
                                                    <?php echo ucfirst($member['role']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                <?php echo date('M j, Y', strtotime($member['joined_at'])); ?>
                                            </td>
                                            <?php if ($isLeader): ?>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                    <?php if ($member['role'] !== 'leader'): ?>
                                                        <div class="flex space-x-2">
                                                            <!-- Promote Button -->
                                                            <form action="../../controllers/teamController.php" method="POST">
                                                                <input type="hidden" name="action" value="update_role">
                                                                <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                                                                <input type="hidden" name="user_id"
                                                                    value="<?php echo $member['user_id']; ?>">
                                                                <input type="hidden" name="new_role"
                                                                    value="<?php echo $member['role'] === 'co-leader' ? 'member' : 'co-leader'; ?>">
                                                                <button type="submit"
                                                                    class="text-xs px-2 py-1 rounded <?php echo $member['role'] === 'co-leader' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700'; ?> text-white">
                                                                    <?php echo $member['role'] === 'co-leader' ? 'Demote' : 'Promote'; ?>
                                                                </button>
                                                            </form>

                                                            <!-- Transfer Leadership Button -->
                                                            <?php if ($member['role'] !== 'leader'): ?>
                                                                <form action="../../controllers/teamController.php" method="POST">
                                                                    <input type="hidden" name="action" value="transfer_leadership">
                                                                    <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                                                                    <input type="hidden" name="new_leader_id"
                                                                        value="<?php echo $member['user_id']; ?>">
                                                                    <button type="submit"
                                                                        class="text-xs px-2 py-1 rounded bg-yellow-600 hover:bg-yellow-700 text-white"
                                                                        onclick="return confirm('Are you sure you want to transfer leadership to <?php echo htmlspecialchars($member['username']); ?>?')">
                                                                        Make Leader
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>

                                                            <!-- Remove Member Button -->
                                                            <form action="../../controllers/teamController.php" method="POST">
                                                                <input type="hidden" name="action" value="remove_member">
                                                                <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                                                                <input type="hidden" name="user_id"
                                                                    value="<?php echo $member['user_id']; ?>">
                                                                <button type="submit"
                                                                    class="text-xs px-2 py-1 rounded bg-red-600 hover:bg-red-700 text-white"
                                                                    onclick="return confirm('Are you sure you want to remove <?php echo htmlspecialchars($member['username']); ?> from the team?')">
                                                                    Remove
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="w-full md:w-1/3">
                    <div class="bg-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 border-b border-gray-600 pb-2">Team Leader</h3>
                        <?php
                        $leader = array_filter($members, function ($m) {
                            return $m['role'] === 'leader';
                        });
                        $leader = reset($leader);
                        ?>
                        <div class="flex items-center space-x-4">
                            <img src="<?php echo BASE_URL; ?>assets/images/avatars/<?php echo htmlspecialchars($leader['avatar'] ?? 'default.png'); ?>"
                                alt="Leader Avatar" class="w-12 h-12 rounded-full border-2 border-yellow-400">
                            <div>
                                <h4 class="font-medium">
                                    <?php if (is_array($leader) && isset($leader['username'])): ?>
                                        <?php echo htmlspecialchars($leader['username']); ?>
                                    <?php else: ?>
                                        Leader inconnu
                                    <?php endif; ?>
                                </h4>
                                <p class="text-gray-400 text-sm">Team Leader</p>
                            </div>
                        </div>
                    </div>

                    <?php if ($isLeader): ?>
                        <div class="bg-gray-700 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4 border-b border-gray-600 pb-2">Team Management</h3>
                            <div class="space-y-3">
                                <!-- Invite Members Modal Trigger -->
                                <button onclick="document.getElementById('inviteModal').classList.remove('hidden')"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                    Invite Members
                                </button>

                                <a href="edit_team.php?id=<?php echo $teamId; ?>"
                                    class="block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition text-center">
                                    Edit Team
                                </a>

                                <form action="../../controllers/teamController.php?action=delete" method="POST">
                                    <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                                    <button type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition"
                                        onclick="return confirm('Are you sure you want to delete this team?')">
                                        Delete Team
                                    </button>
                                </form>
                            </div>

                            <!-- Pending Invitations Section -->
                            <?php if (!empty($pendingInvitations)): ?>
                                <div class="mt-6">
                                    <h4 class="text-md font-semibold mb-2 border-b border-gray-600 pb-2">Pending Invitations
                                    </h4>
                                    <ul class="space-y-2">
                                        <?php foreach ($pendingInvitations as $invite): ?>
                                            <li class="flex justify-between items-center bg-gray-800 p-2 rounded">
                                                <div class="flex items-center space-x-2">
                                                    <img src="<?php echo BASE_URL; ?>assets/images/avatars/<?php echo htmlspecialchars($invite['recipient_avatar'] ?? 'default.png'); ?>"
                                                        class="w-8 h-8 rounded-full">
                                                    <span><?php echo htmlspecialchars($invite['recipient_name']); ?></span>
                                                </div>
                                                <form action="../../controllers/teamController.php" method="POST">
                                                    <input type="hidden" name="action" value="cancel_invite">
                                                    <input type="hidden" name="invite_id" value="<?php echo $invite['id']; ?>">
                                                    <button type="submit"
                                                        class="text-xs px-2 py-1 bg-red-600 hover:bg-red-700 rounded text-white">
                                                        Cancel
                                                    </button>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="bg-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4 border-b border-gray-600 pb-2">Team Stats</h3>
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-gray-400 text-sm">Events Participated</h4>
                                <p class="font-medium"><?php echo $team['events_participated'] ?? 0; ?></p>
                            </div>
                            <div>
                                <h4 class="text-gray-400 text-sm">Wins</h4>
                                <p class="font-medium"><?php echo $team['wins'] ?? 0; ?></p>
                            </div>
                            <div>
                                <h4 class="text-gray-400 text-sm">Win Rate</h4>
                                <p class="font-medium">
                                    <?php
                                    $participated = $team['events_participated'] ?? 0;
                                    $wins = $team['wins'] ?? 0;
                                    echo $participated > 0 ? round(($wins / $participated) * 100) : 0;
                                    ?>%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invite Members Modal -->
<?php if ($isLeader): ?>
    <div id="inviteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Invite Members</h3>
                <button
                    onclick="document.getElementById('inviteModal').classList.add('hidden'); window.location.href='team.php?id=<?php echo $teamId; ?>'"
                    class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form action="../../controllers/teamController.php?action=search_users" method="POST">
                <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">

                <div class="mb-4">
                    <label for="searchUser" class="block text-sm font-medium text-gray-300 mb-2">Search Users</label>
                    <div class="flex">
                        <input type="text" id="searchUser" name="search" placeholder="Enter username"
                            value="<?php echo isset($_SESSION['search_query']) ? htmlspecialchars($_SESSION['search_query']) : ''; ?>"
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-l-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <button type="submit" name="search_action" value="search"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-r-md text-white">
                            Search
                        </button>
                    </div>

                    <?php if (isset($_SESSION['search_error'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $_SESSION['search_error']; ?></p>
                        <?php unset($_SESSION['search_error']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['search_results'])): ?>
                        <div class="mt-2 max-h-60 overflow-y-auto bg-gray-700 rounded-md">
                            <?php if (empty($_SESSION['search_results'])): ?>
                                <div class="p-2 text-gray-400">No users found</div>
                            <?php else: ?>
                                <?php foreach ($_SESSION['search_results'] as $user): ?>
                                    <div class="p-2 hover:bg-gray-600 flex items-center space-x-2">
                                        <img src="<?php echo BASE_URL; ?>assets/images/avatars/<?php echo htmlspecialchars($user['avatar'] ?? 'default.png'); ?>"
                                            class="w-8 h-8 rounded-full">
                                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                                        <form method="POST" action="../../controllers/teamController.php?action=invite" class="ml-auto">
                                            <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                                            <input type="hidden" name="recipient_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit"
                                                class="px-2 py-1 bg-green-600 hover:bg-green-700 rounded text-white text-xs">
                                                Select
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php unset($_SESSION['search_results']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Simple modal toggle functionality
        document.getElementById('inviteModal').addEventListener('click', function (e) {
            if (e.target === this) {
                window.location.href = 'team.php?id=<?php echo $teamId; ?>';
            }
        });
    </script>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>