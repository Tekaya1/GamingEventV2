<?php
require_once '../../bootstrap.php';
// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: events.php');
    exit();
}

$event = new Event();
$currentEvent = $event->getEventById($_GET['id']);
$registrations = $event->getEventRegistrations($_GET['id']);

if (!$currentEvent) {
    header('Location: events.php');
    exit();
}
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
                    <h2 class="text-2xl font-bold">Manage Event: <?php echo htmlspecialchars($currentEvent['title']); ?></h2>
                    <a href="events.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">Back to Events</a>
                </div>
                
                <!-- Event Info -->
                <div class="bg-gray-700 rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-semibold mb-4 border-b border-gray-600 pb-2">Event Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-gray-400 text-sm font-medium mb-1">Title</h4>
                            <p class="font-medium"><?php echo htmlspecialchars($currentEvent['title']); ?></p>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm font-medium mb-1">Game Type</h4>
                            <p class="font-medium"><?php echo htmlspecialchars($currentEvent['game_type']); ?></p>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm font-medium mb-1">Start Date</h4>
                            <p class="font-medium"><?php echo date('M j, Y g:i a', strtotime($currentEvent['start_date'])); ?></p>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm font-medium mb-1">End Date</h4>
                            <p class="font-medium"><?php echo date('M j, Y g:i a', strtotime($currentEvent['end_date'])); ?></p>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm font-medium mb-1">Participants</h4>
                            <p class="font-medium"><?php echo $currentEvent['current_participants'] . '/' . $currentEvent['max_participants']; ?></p>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm font-medium mb-1">Prize Pool</h4>
                            <p class="font-medium">$<?php echo number_format($currentEvent['prize_pool'], 2); ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-gray-400 text-sm font-medium mb-1">Description</h4>
                            <p class="font-medium"><?php echo nl2br(htmlspecialchars($currentEvent['description'])); ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-gray-400 text-sm font-medium mb-1">Rules</h4>
                            <p class="font-medium"><?php echo nl2br(htmlspecialchars($currentEvent['rules'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Participants Management -->
                <div class="bg-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-4 border-b border-gray-600 pb-2">Participants Management</h3>
                    
                    <?php if (empty($registrations)): ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-2 text-gray-400">No participants registered for this event yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-600">
                                <thead class="bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Player</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Registration Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                        <?php if ($currentEvent['status'] === 'completed'): ?>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Score</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rank</th>
                                        <?php endif; ?>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-700 divide-y divide-gray-600">
                                    <?php foreach ($registrations as $registration): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full" src="../../assets/images/avatars/<?php echo htmlspecialchars($registration['avatar']); ?>" alt="">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium"><?php echo htmlspecialchars($registration['username']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                <?php echo date('M j, Y', strtotime($registration['registration_date'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="../../controllers/eventController.php?action=update_status" method="POST" class="inline">
                                                    <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                                    <select name="status" onchange="this.form.submit()" class="bg-gray-600 border border-gray-500 text-white text-sm rounded-lg px-2 py-1 focus:ring-red-500 focus:border-red-500">
                                                        <option value="registered" <?php echo $registration['status'] === 'registered' ? 'selected' : ''; ?>>Registered</option>
                                                        <option value="attended" <?php echo $registration['status'] === 'attended' ? 'selected' : ''; ?>>Attended</option>
                                                        <option value="no_show" <?php echo $registration['status'] === 'no_show' ? 'selected' : ''; ?>>No Show</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <?php if ($currentEvent['status'] === 'completed'): ?>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <form action="../../controllers/eventController.php?action=update_score" method="POST" class="inline">
                                                        <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                                        <input type="number" name="score" value="<?php echo $registration['score']; ?>" class="bg-gray-600 border border-gray-500 text-white text-sm rounded-lg px-2 py-1 w-16 focus:ring-red-500 focus:border-red-500">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                        <input type="number" name="ranking" value="<?php echo $registration['ranking']; ?>" class="bg-gray-600 border border-gray-500 text-white text-sm rounded-lg px-2 py-1 w-16 focus:ring-red-500 focus:border-red-500">
                                                        <button type="submit" class="ml-2 text-red-400 hover:text-red-300 transition">Update</button>
                                                    </form>
                                                </td>
                                            <?php endif; ?>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="../player/profile.php?id=<?php echo $registration['user_id']; ?>" class="text-blue-400 hover:text-blue-300 transition">View Profile</a>
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