<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../middlewares/authMiddleware.php';

// Authenticate as admin
authenticate('admin');

// Rest of your dashboard code
$user = new User();
$event = new Event();

// Get current user data
$currentUser = $user->getUserById($_SESSION['user_id']);
if (!$currentUser) {
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php');
    exit();
}

// Get data for dashboard
$allUsers = $user->getAllUsers();
$allEvents = $event->getAllEvents();
$upcomingEvents = $event->getAllEvents('upcoming');
?>
<?php include '../../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="w-full md:w-3/4">
            <div class="bg-gray-800 rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Admin Dashboard</h2>
                    <a href="events.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>New Event</span>
                    </a>
                </div>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-gray-700 rounded-lg p-4 border-l-4 border-red-500">
                        <h3 class="text-gray-400 text-sm font-medium">Total Users</h3>
                        <p class="text-2xl font-bold"><?php echo count($allUsers); ?></p>
                        <div class="mt-2 flex items-center text-green-400 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            +12% from last month
                        </div>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4 border-l-4 border-blue-500">
                        <h3 class="text-gray-400 text-sm font-medium">Total Events</h3>
                        <p class="text-2xl font-bold"><?php echo count($allEvents); ?></p>
                        <div class="mt-2 flex items-center text-green-400 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            +5% from last month
                        </div>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4 border-l-4 border-purple-500">
                        <h3 class="text-gray-400 text-sm font-medium">Active Events</h3>
                        <p class="text-2xl font-bold"><?php 
                            $activeEvents = array_filter($allEvents, function($e) {
                                return $e['status'] === 'upcoming' || $e['status'] === 'ongoing';
                            });
                            echo count($activeEvents);
                        ?></p>
                        <div class="mt-2 flex items-center text-green-400 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            +8% from last month
                        </div>
                    </div>
                </div>
                
                <!-- Recent Events -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4 border-b border-gray-700 pb-2">Recent Events</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Event</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Game</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Participants</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <?php foreach (array_slice($allEvents, 0, 5) as $event): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium"><?php echo htmlspecialchars($event['title']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-600 text-gray-300">
                                                <?php echo htmlspecialchars($event['game_type']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            <?php echo date('M j, Y', strtotime($event['start_date'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php 
                                            $statusColor = '';
                                            switch ($event['status']) {
                                                case 'upcoming':
                                                    $statusColor = 'bg-blue-600';
                                                    break;
                                                case 'ongoing':
                                                    $statusColor = 'bg-green-600';
                                                    break;
                                                case 'completed':
                                                    $statusColor = 'bg-purple-600';
                                                    break;
                                                case 'cancelled':
                                                    $statusColor = 'bg-red-600';
                                                    break;
                                                default:
                                                    $statusColor = 'bg-gray-600';
                                            }
                                            ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColor; ?> text-white">
                                                <?php echo ucfirst($event['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            <?php echo $event['current_participants'] . '/' . $event['max_participants']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="events.php?id=<?php echo $event['id']; ?>" class="text-red-400 hover:text-red-300 transition mr-3">View</a>
                                            <a href="events.php?action=edit&id=<?php echo $event['id']; ?>" class="text-blue-400 hover:text-blue-300 transition">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="events.php" class="text-red-400 hover:text-red-300 font-medium transition">View All Events</a>
                    </div>
                </div>
                
                <!-- Recent Users -->
                <div>
                    <h3 class="text-xl font-semibold mb-4 border-b border-gray-700 pb-2">Recent Users</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Username</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Joined</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <?php foreach (array_slice($allUsers, 0, 5) as $user): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="../../assets/images/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="font-medium"><?php echo htmlspecialchars($user['username']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php 
                                            $roleColor = $user['role'] === 'admin' ? 'bg-red-600' : ($user['role'] === 'player' ? 'bg-purple-600' : 'bg-gray-600');
                                            ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $roleColor; ?> text-white">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="users.php?id=<?php echo $user['id']; ?>" class="text-red-400 hover:text-red-300 transition">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="users.php" class="text-red-400 hover:text-red-300 font-medium transition">View All Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>