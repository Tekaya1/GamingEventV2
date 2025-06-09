<?php
require_once '../../bootstrap.php';
require_once '../../models/Team.php';
require_once '../../config/database.php';

$team = new Team();
$teamModel = $team; // Alias for clarity in view
$allTeams = $team->getAllTeams();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userTeams = [];
if ($isLoggedIn) {
    // Get teams the user is a member of
    $stmt = $pdo->prepare("
        SELECT t.* 
        FROM teams t
        JOIN team_members tm ON t.id = tm.team_id
        WHERE tm.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $userTeams = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php include '../../includes/header.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold font-gaming text-purple-400">Teams</h1>
        <?php if ($isLoggedIn): ?>
            <a href="teams.php?action=create"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                <span>Create Team</span>
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['action']) && $_GET['action'] === 'create'): ?>
    <!-- Create Team Form -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <h2 class="text-2xl font-bold mb-6">Create New Team</h2>
        <form action="../../controllers/teamController.php?action=create" method="POST" enctype="multipart/form-data">
            <div class="mb-6">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="name">Team Name</label>
                <input class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="name" name="name"
                    type="text" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="description">Description</label>
                <textarea class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="description"
                    name="description" rows="4" required></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="max_members">Maximum Members</label>
                <input class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="max_members" 
                    name="max_members" type="number" min="1" max="20" value="5" required>
                <p class="text-gray-400 text-xs mt-1">Set the maximum number of team members (1-20)</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="logo">Team Logo</label>
                <input type="file" id="logo" name="logo"
                    class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white">
            </div>

            <div class="flex justify-end space-x-4">
                <a href="teams.php"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">Cancel</a>
                <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                    Create Team
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

    <!-- My Teams Section -->
    <?php if ($isLoggedIn && !empty($userTeams)): ?>
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6 border-b border-gray-700 pb-2">My Teams</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($userTeams as $team): ?>
                    <div
                        class="bg-gray-800 rounded-lg overflow-hidden border-l-4 border-purple-500 hover:border-purple-400 transition">
                        <div class="p-6">
                            <div class="flex items-center space-x-4 mb-4">
                                <img src="<?php echo BASE_URL; ?>assets/images/teams/<?php echo htmlspecialchars($team['logo'] ?? 'default_team.png'); ?>"
                                    alt="Team Logo" class="w-12 h-12 rounded-full border-2 border-purple-500">
                                <div>
                                    <h3 class="font-bold text-xl"><?php echo htmlspecialchars($team['name']); ?></h3>
                                    <span class="text-sm text-gray-400"><?php echo $team['member_count'] ?? 1; ?> members</span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                                <?php echo htmlspecialchars($team['description']); ?></p>
                            <div class="flex justify-between items-center">
                                <a href="team.php?id=<?php echo $team['id']; ?>"
                                    class="text-purple-400 hover:text-purple-300 text-sm font-medium transition">View Team</a>
                                <span class="text-xs text-gray-400">Created by
                                    <?php echo htmlspecialchars($team['name']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- All Teams Section -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6 border-b border-gray-700 pb-2">All Teams</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($allTeams as $team): ?>
                <div
                    class="bg-gray-800 rounded-lg overflow-hidden border-l-4 border-purple-500 hover:border-purple-400 transition">
                    <div class="p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <img src="<?php echo BASE_URL; ?>assets/images/teams/<?php echo htmlspecialchars($team['logo'] ?? 'default_team.png'); ?>"
                                alt="Team Logo" class="w-12 h-12 rounded-full border-2 border-purple-500">
                            <div>
                                <h3 class="font-bold text-xl"><?php echo htmlspecialchars($team['name']); ?></h3>
                                <span class="text-sm text-gray-400"><?php echo $team['member_count'] ?? 0; ?> members</span>
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($team['description']); ?></p>
                        <div class="flex justify-between items-center">
                            <a href="team.php?id=<?php echo $team['id']; ?>"
                                class="text-purple-400 hover:text-purple-300 text-sm font-medium transition">View Team</a>
                            <?php if ($isLoggedIn): ?>
                                <?php if ($teamModel->isTeamMember($team['id'], $_SESSION['user_id'])): ?>
                                    <span class="text-green-400 text-sm">Already a member</span>
                                <?php else: ?>
                                    <form action="../../controllers/teamController.php?action=join" method="POST">
                                        <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                                        <button type="submit"
                                            class="text-purple-400 hover:text-purple-300 text-sm font-medium transition">Join
                                            Team</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>