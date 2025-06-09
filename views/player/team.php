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
$isMember = $isLoggedIn ? $teamModel->isTeamMember($teamId, $_SESSION['user_id']) : false;
$isLeader = $isLoggedIn ? $teamModel->isTeamLeader($teamId, $_SESSION['user_id']) : false;
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
                        $maxMembers = $team['max_members'] ?? 5; // Default to 5 if not set
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
                                            This team has reached its maximum capacity (<?php echo $currentMembers; ?>/<?php echo $maxMembers; ?>)
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
                                                    <?php echo $member['role'] === 'leader' ? 'bg-yellow-600 text-white' : 'bg-blue-600 text-white'; ?>">
                                                    <?php echo ucfirst($member['role']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                <?php echo date('M j, Y', strtotime($member['joined_at'])); ?>
                                            </td>
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
                                <a href="#"
                                    class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition text-center">
                                    Invite Members
                                </a>
                                <a href="#"
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
                        </div>
                    <?php endif; ?>

                    <div class="bg-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4 border-b border-gray-600 pb-2">Team Stats</h3>
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-gray-400 text-sm">Events Participated</h4>
                                <p class="font-medium">12</p>
                            </div>
                            <div>
                                <h4 class="text-gray-400 text-sm">Wins</h4>
                                <p class="font-medium">8</p>
                            </div>
                            <div>
                                <h4 class="text-gray-400 text-sm">Win Rate</h4>
                                <p class="font-medium">67%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>