<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../middlewares/authMiddleware.php';

// Authenticate as player
authenticate('player');

// Rest of your dashboard code
$user = new User();
$event = new Event();



$currentUser = $user->getUserById($_SESSION['user_id']);
// Additional validation in case user doesn't exist
if (!$currentUser) {
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php');
    exit();
}

$registeredEvents = $event->getUserRegistrations($_SESSION['user_id']);
$upcomingEvents = $event->getAllEvents('upcoming');
?>
<?php include '../../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        <div class="w-full md:w-1/4">
            <div class="bg-gray-800 rounded-lg p-6 mb-6">
                <div class="flex flex-col items-center">
                    <img src="../../assets/images/avatars/<?php echo htmlspecialchars($currentUser['avatar']); ?>" 
                         alt="Avatar" class="w-24 h-24 rounded-full border-4 border-purple-500 mb-4">
                    <h3 class="text-xl font-bold"><?php echo htmlspecialchars($currentUser['username']); ?></h3>
                    <p class="text-gray-400 mb-4"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                    <span class="bg-purple-600 text-white px-3 py-1 rounded-full text-xs">Player</span>
                </div>
                
                <div class="mt-6">
                    <h4 class="text-lg font-semibold mb-3 border-b border-gray-700 pb-2">Quick Stats</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Events Joined</span>
                            <span class="font-medium"><?php echo count($registeredEvents); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Wins</span>
                            <span class="font-medium"><?php 
                                $wins = 0;
                                foreach ($registeredEvents as $event) {
                                    if (isset($event['ranking']) && $event['ranking'] === 1) {
                                        $wins++;
                                    }
                                }
                                echo $wins;
                            ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-6">
                <h4 class="text-lg font-semibold mb-3 border-b border-gray-700 pb-2">Quick Actions</h4>
                <ul class="space-y-2">
                    <li><a href="profile.php" class="text-gray-300 hover:text-white transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Edit Profile</span>
                    </a></li>
                    <li><a href="events.php" class="text-gray-300 hover:text-white transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Browse Events</span>
                    </a></li>
                    <li><a href="leaderboard.php" class="text-gray-300 hover:text-white transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Leaderboard</span>
                    </a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="w-full md:w-3/4">
            <div class="bg-gray-800 rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Player Dashboard</h2>
                    <div class="relative">
                        <input type="text" placeholder="Search events..." class="bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:border-purple-500">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Upcoming Events Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4 border-b border-gray-700 pb-2">Upcoming Events</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach (array_slice($upcomingEvents, 0, 4) as $event): ?>
                            <div class="bg-gray-700 rounded-lg overflow-hidden border-l-4 border-purple-500">
                                <div class="p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-lg"><?php echo htmlspecialchars($event['title']); ?></h4>
                                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($event['game_name']); ?></p>
                                        </div>
                                        <span class="bg-purple-600 text-white px-2 py-1 rounded-full text-xs"><?php echo htmlspecialchars($event['status']); ?></span>
                                    </div>
                                    <div class="mt-3 text-sm">
                                        <div class="flex items-center text-gray-400 mb-1">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <?php echo date('M j, Y', strtotime($event['start_date'])); ?>
                                        </div>
                                        <div class="flex items-center text-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?php echo date('g:i a', strtotime($event['start_date'])) . ' - ' . date('g:i a', strtotime($event['end_date'])); ?>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-between items-center">
                                        <span class="text-sm text-gray-400"><?php echo $event['current_participants'] . '/' . $event['max_participants']; ?> players</span>
                                        <a href="../events.php?id=<?php echo $event['id']; ?>" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="../events.php" class="text-purple-400 hover:text-purple-300 font-medium transition">View All Events</a>
                    </div>
                </div>
                
                <!-- My Events Section -->
                <div>
                    <h3 class="text-xl font-semibold mb-4 border-b border-gray-700 pb-2">My Events</h3>
                    <?php if (empty($registeredEvents)): ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-2 text-gray-400">You haven't registered for any events yet.</p>
                            <a href="../events.php" class="mt-4 inline-block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">Browse Events</a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Event</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Game</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Ranking</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    <?php foreach ($registeredEvents as $event): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium"><?php echo htmlspecialchars($event['event_title']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-600 text-gray-300">
                                                    <?php echo htmlspecialchars($event['game_name']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                <?php echo date('M j, Y', strtotime($event['start_date'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php 
                                                $statusColor = '';
                                                switch ($event['status']) {
                                                    case 'registered':
                                                        $statusColor = 'bg-blue-600';
                                                        break;
                                                    case 'attended':
                                                        $statusColor = 'bg-green-600';
                                                        break;
                                                    case 'no_show':
                                                        $statusColor = 'bg-red-600';
                                                        break;
                                                    default:
                                                        $statusColor = 'bg-gray-600';
                                                }
                                                ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColor; ?> text-white">
                                                    <?php echo ucfirst(str_replace('_', ' ', $event['status'])); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                <?php 
                                                if (isset($event['ranking']) && $event['ranking'] > 0) {
                                                    echo '#' . $event['ranking'];
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="../events.php?id=<?php echo $event['id']; ?>" class="text-purple-400 hover:text-purple-300 transition">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>