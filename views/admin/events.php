<?php
require_once '../../bootstrap.php';
require_once '../../models/Event.php';
// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$event = new Event();
$allEvents = $event->getAllEvents();
// Get single event if ID is provided for edit
$editEvent = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editEvent = $event->getEventById($_GET['id']);
}
?>
<?php include '../../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
            <?php include __DIR__ . '/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="w-full md:w-3/4">
            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-600 text-white p-3 rounded-lg mb-6">
                    <?php 
                    $success = htmlspecialchars($_GET['success']);
                    if ($success === 'created') {
                        echo 'Event created successfully!';
                    } elseif ($success === 'updated') {
                        echo 'Event updated successfully!';
                    } elseif ($success === 'deleted') {
                        echo 'Event deleted successfully!';
                    } elseif ($success === 'status_updated') {
                        echo 'Registration status updated successfully!';
                    } elseif ($success === 'score_updated') {
                        echo 'Player score updated successfully!';
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-600 text-white p-3 rounded-lg mb-6">
                    <?php 
                    $error = htmlspecialchars($_GET['error']);
                    echo $error === 'failed' ? 'Operation failed. Please try again.' : $error;
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['action']) && ($_GET['action'] === 'create' || $_GET['action'] === 'edit')): ?>
                <!-- Create/Edit Event Form -->
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold mb-6">
                        <?php echo $_GET['action'] === 'create' ? 'Create New Event' : 'Edit Event'; ?>
                    </h2>
                    
                    <form action="../../controllers/eventController.php?action=<?php echo $_GET['action']; ?>" method="POST">
                        <?php if ($_GET['action'] === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $editEvent['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="title">
                                    Event Title
                                </label>
                                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500" 
                                       id="title" name="title" type="text" placeholder="Event title" 
                                       value="<?php echo isset($editEvent['title']) ? htmlspecialchars($editEvent['title']) : ''; ?>" required>
                            </div>
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="game_type">
                                    Game Type
                                </label>
                                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500" 
                                       id="game_type" name="game_type" type="text" placeholder="e.g. Fortnite, League of Legends" 
                                       value="<?php echo isset($editEvent['game_type']) ? htmlspecialchars($editEvent['game_type']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="description">
                                Description
                            </label>
                            <textarea class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500" 
                                      id="description" name="description" rows="4" placeholder="Event description" required><?php echo isset($editEvent['description']) ? htmlspecialchars($editEvent['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="start_date">
                                    Start Date & Time
                                </label>
                                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500" 
                                       id="start_date" name="start_date" type="datetime-local" 
                                       value="<?php echo isset($editEvent['start_date']) ? date('Y-m-d\TH:i', strtotime($editEvent['start_date'])) : ''; ?>" required>
                            </div>
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="end_date">
                                    End Date & Time
                                </label>
                                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500" 
                                       id="end_date" name="end_date" type="datetime-local" 
                                       value="<?php echo isset($editEvent['end_date']) ? date('Y-m-d\TH:i', strtotime($editEvent['end_date'])) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="max_participants">
                                    Max Participants (leave blank for unlimited)
                                </label>
                                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500" 
                                       id="max_participants" name="max_participants" type="number" min="1" 
                                       value="<?php echo isset($editEvent['max_participants']) ? $editEvent['max_participants'] : ''; ?>">
                            </div>
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="prize_pool">
                                    Prize Pool ($)
                                </label>
                                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500" 
                                       id="prize_pool" name="prize_pool" type="number" min="0" step="0.01" 
                                       value="<?php echo isset($editEvent['prize_pool']) ? $editEvent['prize_pool'] : '0'; ?>" required>
                            </div>
                        </div>
                        
                        <?php if ($_GET['action'] === 'edit'): ?>
                            <div class="mb-6">
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="status">
                                    Status
                                </label>
                                <select class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500" 
                                        id="status" name="status" required>
                                    <option value="upcoming" <?php echo isset($editEvent['status']) && $editEvent['status'] === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                    <option value="ongoing" <?php echo isset($editEvent['status']) && $editEvent['status'] === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                    <option value="completed" <?php echo isset($editEvent['status']) && $editEvent['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo isset($editEvent['status']) && $editEvent['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-6">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="rules">
                                Rules
                            </label>
                            <textarea class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500" 
                                      id="rules" name="rules" rows="6" placeholder="Event rules and guidelines" required><?php echo isset($editEvent['rules']) ? htmlspecialchars($editEvent['rules']) : ''; ?></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-4">
                            <a href="events.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">Cancel</a>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                                <?php echo $_GET['action'] === 'create' ? 'Create Event' : 'Update Event'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Events List -->
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Manage Events</h2>
                        <a href="events.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>New Event</span>
                        </a>
                    </div>
                    
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
                                <?php foreach ($allEvents as $event): ?>
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
                                            <a href="events.php?action=edit&id=<?php echo $event['id']; ?>" class="text-blue-400 hover:text-blue-300 transition mr-3">Edit</a>
                                            <a href="event_details.php?id=<?php echo $event['id']; ?>" class="text-red-400 hover:text-red-300 transition">Manage</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>