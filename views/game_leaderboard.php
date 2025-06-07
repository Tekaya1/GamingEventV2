<?php
require_once '../bootstrap.php';
require_once '../models/Event.php';
if (!isset($_GET['game'])) {
    header('Location: leaderboard.php');
    exit();
}

$game = urldecode($_GET['game']);
$event = new Event();
$leaderboard = $event->getLeaderboard($game);
?>
<?php include '../includes/header.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold font-gaming text-purple-400"><?php echo htmlspecialchars($game); ?> Leaderboard</h1>
        <a href="leaderboard.php" class="text-gray-400 hover:text-white transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Back to Leaderboards</span>
        </a>
    </div>
    
    <?php if (empty($leaderboard)): ?>
        <div class="bg-gray-800 rounded-lg p-8 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="mt-4 text-gray-400">No leaderboard data available for <?php echo htmlspecialchars($game); ?></p>
        </div>
    <?php else: ?>
        <div class="bg-gray-800 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rank</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Player</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Score</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Events Played</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-gray-700">
                    <?php foreach ($leaderboard as $index => $player): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-lg font-bold">#<?php echo $index + 1; ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="../assets/images/avatars/<?php echo htmlspecialchars($player['avatar']); ?>" alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium"><?php echo htmlspecialchars($player['username']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                <?php echo $player['total_score']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                <?php echo $player['games_played']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>