<?php
require_once '../bootstrap.php';
require_once '../models/Event.php';
require_once '../config/config.php';
$event = new Event();
$allEvents = $event->getAllEvents();
$upcomingEvents = $event->getAllEvents('upcoming');
$ongoingEvents = $event->getAllEvents('ongoing');
$completedEvents = $event->getAllEvents('completed');

// Get single event if ID is provided
$singleEvent = null;
if (isset($_GET['id'])) {
    $singleEvent = $event->getEventById($_GET['id']);
    if ($singleEvent) {
        $eventRegistrations = $event->getEventRegistrations($_GET['id']);
    }
}
?>
<?php include '../includes/header.php'; ?>

<?php if ($singleEvent): ?>
    <!-- Single Event View -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <!-- Event Header -->
            <div class="relative">
                <div class="h-48 bg-gradient-to-r from-purple-900 to-blue-900 flex items-center justify-center">
                    <h1 class="text-4xl font-bold text-center font-gaming text-white"><?php echo htmlspecialchars($singleEvent['title']); ?></h1>
                </div>
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-gray-900 to-transparent h-16"></div>
            </div>
            
            <!-- Event Details -->
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Main Content -->
                    <div class="w-full md:w-2/3">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium 
                                    <?php echo $singleEvent['status'] === 'upcoming' ? 'bg-blue-600 text-white' : 
                                          ($singleEvent['status'] === 'ongoing' ? 'bg-green-600 text-white' : 
                                          ($singleEvent['status'] === 'completed' ? 'bg-purple-600 text-white' : 'bg-gray-600 text-white')); ?>">
                                    <?php echo ucfirst($singleEvent['status']); ?>
                                </span>
                                <span class="px-3 py-1 rounded-full bg-gray-700 text-gray-300 text-sm font-medium">
                                    <?php echo htmlspecialchars($singleEvent['game_name']); ?>
                                </span>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'player'): ?>
                                <?php 
                                $isRegistered = false;
                                if (isset($eventRegistrations)) {
                                    foreach ($eventRegistrations as $reg) {
                                        if ($reg['user_id'] === $_SESSION['user_id']) {
                                            $isRegistered = true;
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <?php if ($isRegistered): ?>
                                    <span class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-medium">
                                        Registered
                                    </span>
                                <?php elseif ($singleEvent['status'] === 'upcoming' && 
                                           (!$singleEvent['max_participants'] || $singleEvent['current_participants'] < $singleEvent['max_participants'])): ?>
                                    <form action="../controllers/eventController.php?action=register" method="POST">
                                        <input type="hidden" name="action" value="register">
                                        <input type="hidden" name="event_id" value="<?php echo $singleEvent['id']; ?>">
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium transition">
                                            Register Now
                                        </button>
                                    </form>
                                <?php elseif ($singleEvent['status'] === 'upcoming' && $singleEvent['current_participants'] >= $singleEvent['max_participants']): ?>
                                    <span class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium">
                                        Event Full
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="prose prose-invert max-w-none">
                            <h3 class="text-xl font-semibold mb-3">About This Event</h3>
                            <p><?php echo nl2br(htmlspecialchars($singleEvent['description'])); ?></p>
                            
                            <h3 class="text-xl font-semibold mb-3 mt-6">Event Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="text-gray-400 text-sm">Start Date</h4>
                                            <p class="font-medium"><?php echo date('l, F j, Y', strtotime($singleEvent['start_date'])); ?></p>
                                            <p class="text-gray-400 text-sm"><?php echo date('g:i a', strtotime($singleEvent['start_date'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="text-gray-400 text-sm">End Date</h4>
                                            <p class="font-medium"><?php echo date('l, F j, Y', strtotime($singleEvent['end_date'])); ?></p>
                                            <p class="text-gray-400 text-sm"><?php echo date('g:i a', strtotime($singleEvent['end_date'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="text-gray-400 text-sm">Participants</h4>
                                            <p class="font-medium"><?php echo $singleEvent['current_participants']; ?> / <?php echo $singleEvent['max_participants'] ? $singleEvent['max_participants'] : '∞'; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="text-gray-400 text-sm">Prize Pool</h4>
                                            <p class="font-medium">$<?php echo number_format($singleEvent['prize_pool'], 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-semibold mb-3">Event Rules</h3>
                            <div class="bg-gray-700 p-4 rounded-lg mb-6">
                                <?php echo nl2br(htmlspecialchars($singleEvent['rules'])); ?>
                            </div>
                            
                            <?php if (isset($eventRegistrations) && !empty($eventRegistrations)): ?>
                                <h3 class="text-xl font-semibold mb-3">Participants</h3>
                                <div class="bg-gray-700 rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-600">
                                        <thead class="bg-gray-800">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Player</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                                <?php if ($singleEvent['status'] === 'completed'): ?>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Score</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rank</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-gray-700 divide-y divide-gray-600">
                                            <?php foreach ($eventRegistrations as $registration): ?>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                <img class="h-10 w-10 rounded-full" src="<?php echo BASE_URL; ?>assets/images/avatars/<?php echo htmlspecialchars($registration['avatar']); ?>" alt="">
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium"><?php echo htmlspecialchars($registration['username']); ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            <?php echo $registration['status'] === 'registered' ? 'bg-blue-600 text-white' : 
                                                                  ($registration['status'] === 'attended' ? 'bg-green-600 text-white' : 
                                                                  'bg-red-600 text-white'); ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $registration['status'])); ?>
                                                        </span>
                                                    </td>
                                                    <?php if ($singleEvent['status'] === 'completed'): ?>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                            <?php echo $registration['score']; ?>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                            <?php echo $registration['ranking']; ?>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="w-full md:w-1/3">
                        <div class="bg-gray-700 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4 border-b border-gray-600 pb-2">Event Host</h3>
                            <div class="flex items-center space-x-4">
                                <img src="../assets/images/avatars/default.png" alt="Host Avatar" class="w-12 h-12 rounded-full border-2 border-purple-500">
                                <div>
                                    <h4 class="font-medium"><?php echo htmlspecialchars($singleEvent['creator']); ?></h4>
                                    <p class="text-gray-400 text-sm">Event Organizer</p>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($singleEvent['status'] === 'completed'): ?>
                            <div class="bg-gray-700 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-semibold mb-4 border-b border-gray-600 pb-2">Winners</h3>
                                <?php 
                                $winners = array_filter($eventRegistrations, function($r) {
                                    return $r['ranking'] <= 3;
                                });
                                usort($winners, function($a, $b) {
                                    return $a['ranking'] <=> $b['ranking'];
                                });
                                ?>
                                <?php if (!empty($winners)): ?>
                                    <div class="space-y-4">
                                        <?php foreach ($winners as $winner): ?>
                                            <div class="flex items-center space-x-4 p-3 rounded-lg 
                                                <?php echo $winner['ranking'] === 1 ? 'bg-yellow-900' : 
                                                      ($winner['ranking'] === 2 ? 'bg-gray-600' : 'bg-amber-900'); ?>">
                                                <div class="relative">
                                                    <img src="../assets/images/avatars/<?php echo htmlspecialchars($winner['avatar']); ?>" alt="Winner Avatar" class="w-10 h-10 rounded-full border-2 
                                                        <?php echo $winner['ranking'] === 1 ? 'border-yellow-400' : 
                                                              ($winner['ranking'] === 2 ? 'border-gray-400' : 'border-amber-600'); ?>">
                                                    <span class="absolute -bottom-1 -right-1 bg-purple-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                                                        <?php echo $winner['ranking']; ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h4 class="font-medium"><?php echo htmlspecialchars($winner['username']); ?></h4>
                                                    <p class="text-gray-400 text-sm">Score: <?php echo $winner['score']; ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-400 text-center py-4">No winners recorded</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4 border-b border-gray-600 pb-2">Share This Event</h3>
                            <div class="flex space-x-4">
                                <a href="#" class="bg-blue-600 hover:bg-blue-700 w-10 h-10 rounded-full flex items-center justify-center transition">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"></path>
                                    </svg>
                                </a>
                                <a href="#" class="bg-blue-400 hover:bg-blue-500 w-10 h-10 rounded-full flex items-center justify-center transition">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                                    </svg>
                                </a>
                                <a href="#" class="bg-red-600 hover:bg-red-700 w-10 h-10 rounded-full flex items-center justify-center transition">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path>
                                    </svg>
                                </a>
                                <a href="#" class="bg-green-500 hover:bg-green-600 w-10 h-10 rounded-full flex items-center justify-center transition">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- All Events View -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold font-gaming text-purple-400">Gaming Events</h1>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin/events.php?action=create" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Create Event</span>
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Event Filters -->
        <div class="bg-gray-800 rounded-lg p-4 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex space-x-2">
                    <button class="px-4 py-2 rounded-lg bg-purple-600 text-white">All Events</button>
                    <button class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-gray-300 transition">Upcoming</button>
                    <button class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-gray-300 transition">Ongoing</button>
                    <button class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-gray-300 transition">Completed</button>
                </div>
                <div class="relative w-full md:w-auto">
                    <input type="text" placeholder="Search events..." class="bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 pl-10 w-full focus:outline-none focus:border-purple-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Events -->
        <?php if (!empty($upcomingEvents)): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6 border-b border-gray-700 pb-2">Upcoming Events</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="bg-gray-800 rounded-lg overflow-hidden border-l-4 border-purple-500 hover:border-purple-400 transition">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-xl mb-1"><?php echo htmlspecialchars($event['title']); ?></h3>
                                        <span class="text-sm text-gray-400"><?php echo htmlspecialchars($event['game_name']); ?></span>
                                    </div>
                                    <span class="bg-purple-600 text-white px-2 py-1 rounded-full text-xs">Upcoming</span>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($event['description']); ?></p>
                                <div class="flex items-center text-gray-400 text-sm mb-3">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php echo date('M j, Y', strtotime($event['start_date'])); ?>
                                </div>
                                <div class="flex items-center text-gray-400 text-sm mb-4">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo date('g:i a', strtotime($event['start_date'])); ?>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-400"><?php echo $event['current_participants'] . '/' . $event['max_participants']; ?> players</span>
                                    <a href="events.php?id=<?php echo $event['id']; ?>" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Ongoing Events -->
        <?php if (!empty($ongoingEvents)): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6 border-b border-gray-700 pb-2">Ongoing Events</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($ongoingEvents as $event): ?>
                        <div class="bg-gray-800 rounded-lg overflow-hidden border-l-4 border-green-500 hover:border-green-400 transition">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-xl mb-1"><?php echo htmlspecialchars($event['title']); ?></h3>
                                        <span class="text-sm text-gray-400"><?php echo htmlspecialchars($event['game_name']); ?></span>
                                    </div>
                                    <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Ongoing</span>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($event['description']); ?></p>
                                <div class="flex items-center text-gray-400 text-sm mb-3">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php echo date('M j, Y', strtotime($event['start_date'])); ?>
                                </div>
                                <div class="flex items-center text-gray-400 text-sm mb-4">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo date('g:i a', strtotime($event['start_date'])); ?>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-400"><?php echo $event['current_participants'] . '/' . $event['max_participants']; ?> players</span>
                                    <a href="events.php?id=<?php echo $event['id']; ?>" class="text-green-400 hover:text-green-300 text-sm font-medium transition">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Completed Events -->
        <?php if (!empty($completedEvents)): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-6 border-b border-gray-700 pb-2">Completed Events</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($completedEvents as $event): ?>
                        <div class="bg-gray-800 rounded-lg overflow-hidden border-l-4 border-purple-500 hover:border-purple-400 transition">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-xl mb-1"><?php echo htmlspecialchars($event['title']); ?></h3>
                                        <span class="text-sm text-gray-400"><?php echo htmlspecialchars($event['game_name']); ?></span>
                                    </div>
                                    <span class="bg-purple-600 text-white px-2 py-1 rounded-full text-xs">Completed</span>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($event['description']); ?></p>
                                <div class="flex items-center text-gray-400 text-sm mb-3">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php echo date('M j, Y', strtotime($event['start_date'])); ?>
                                </div>
                                <div class="flex items-center text-gray-400 text-sm mb-4">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo date('g:i a', strtotime($event['start_date'])); ?>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-400"><?php echo $event['current_participants']; ?> participants</span>
                                    <a href="events.php?id=<?php echo $event['id']; ?>" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition">View Results</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>