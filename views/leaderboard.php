<?php
require_once '../bootstrap.php';
require_once '../models/Event.php';
$event = new Event();
$popularGames = ['Fortnite', 'League of Legends', 'Valorant', 'Call of Duty', 'Dota 2'];
?>
<?php include '../includes/header.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold font-gaming text-purple-400 mb-8">Leaderboards</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <?php foreach ($popularGames as $game): ?>
            <div class="bg-gray-800 rounded-lg overflow-hidden border-l-4 border-purple-500">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4">Top <?php echo htmlspecialchars($game); ?> Players</h3>
                    
                    <?php $leaderboard = $event->getLeaderboard(); ?>
                    <?php if (empty($leaderboard)): ?>
                        <p class="text-gray-400 text-center py-4">No data available</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($leaderboard as $index => $player): ?>
                                <div class="flex items-center justify-between bg-gray-700 p-3 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-gray-400 w-6 text-center"><?php echo $index + 1; ?></span>
                                        <img src="../assets/images/avatars/<?php echo htmlspecialchars($player['avatar']); ?>" alt="Avatar" class="w-8 h-8 rounded-full">
                                        <span class="font-medium"><?php echo htmlspecialchars($player['username']); ?></span>
                                    </div>
                                    <span class="bg-purple-600 text-white px-2 py-1 rounded-full text-xs"><?php echo $player['total_score']; ?> pts</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="game_leaderboard.php?game=<?php echo urlencode($game); ?>" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition">View Full Leaderboard</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">All Games</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <?php 
            $allGames = ['Apex Legends', 'CS:GO', 'Overwatch', 'Rocket League', 'Rainbow Six Siege', 
                         'PUBG', 'Minecraft', 'FIFA', 'NBA 2K', 'Street Fighter'];
            ?>
            <?php foreach ($allGames as $game): ?>
                <a href="game_leaderboard.php?game=<?php echo urlencode($game); ?>" class="bg-gray-700 hover:bg-gray-600 p-4 rounded-lg text-center transition">
                    <?php echo htmlspecialchars($game); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>