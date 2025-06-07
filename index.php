<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/bootstrap.php';
$event = new Event();
$upcomingEvents = $event->getAllEvents('upcoming');
$ongoingEvents = $event->getAllEvents('ongoing');
$featuredGames = ['Fortnite', 'League of Legends', 'Valorant', 'Call of Duty', 'Dota 2'];
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-purple-900 to-blue-900 rounded-xl overflow-hidden mb-12">
        <div class="absolute inset-0 bg-black opacity-40"></div>
        <div class="relative px-8 py-16 md:py-24 text-center">
            <h1 class="text-4xl md:text-6xl font-bold font-gaming text-white mb-6">GAME EVENTS HUB</h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto mb-8">Join the ultimate gaming community. Compete in tournaments, climb leaderboards, and win amazing prizes!</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="./views/events.php" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-lg text-lg font-medium transition transform hover:scale-105">
                    Browse Events
                </a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="./views/auth/register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg text-lg font-medium transition transform hover:scale-105">
                        Join Now
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Events Section -->
    <div class="mb-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold font-gaming text-purple-400">UPCOMING EVENTS</h2>
            <a href="events.php" class="text-gray-400 hover:text-white transition flex items-center">
                <span>View All</span>
                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <?php if (empty($upcomingEvents)): ?>
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-4 text-gray-400">No upcoming events scheduled. Check back later!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach (array_slice($upcomingEvents, 0, 3) as $event): ?>
                    <div class="bg-gray-800 rounded-lg overflow-hidden border-l-4 border-purple-500 hover:border-purple-400 transition transform hover:-translate-y-1">
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
                                <a href="./views/events.php?id=<?php echo $event['id']; ?>" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ongoing Events Section -->
    <?php if (!empty($ongoingEvents)): ?>
        <div class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold font-gaming text-green-400">LIVE EVENTS</h2>
                <a href="events.php?status=ongoing" class="text-gray-400 hover:text-white transition flex items-center">
                    <span>View All</span>
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach (array_slice($ongoingEvents, 0, 3) as $event): ?>
                    <div class="bg-gray-800 rounded-lg overflow-hidden border-l-4 border-green-500 hover:border-green-400 transition transform hover:-translate-y-1">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-bold text-xl mb-1"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <span class="text-sm text-gray-400"><?php echo htmlspecialchars($event['game_name']); ?></span>
                                </div>
                                <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Live Now</span>
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
                                <a href="<?php echo BASE_URL; ?>views/events.php?id=<?php echo $event['id']; ?>" class="text-green-400 hover:text-green-300 text-sm font-medium transition">Watch Live</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Featured Games Section -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold font-gaming text-blue-400 mb-8">FEATURED GAMES</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <?php foreach ($featuredGames as $game): ?>
                <a href="./views/game_leaderboard.php?game=<?php echo urlencode($game); ?>" class="bg-gray-800 hover:bg-gray-700 rounded-lg p-6 text-center transition transform hover:scale-105">
                    <div class="bg-gray-700 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium"><?php echo htmlspecialchars($game); ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-gradient-to-r from-purple-900 to-blue-900 rounded-xl p-8 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Ready to Compete?</h2>
        <p class="text-gray-300 max-w-2xl mx-auto mb-6">Join our gaming community today and start participating in exciting tournaments with amazing prizes!</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="bg-white hover:bg-gray-200 text-purple-900 px-8 py-3 rounded-lg text-lg font-medium transition transform hover:scale-105">
                    Sign Up Now
                </a>
                <a href="events.php" class="bg-transparent border-2 border-white hover:bg-white hover:bg-opacity-10 text-white px-8 py-3 rounded-lg text-lg font-medium transition transform hover:scale-105">
                    Browse Events
                </a>
            <?php else: ?>
                <a href="events.php" class="bg-white hover:bg-gray-200 text-purple-900 px-8 py-3 rounded-lg text-lg font-medium transition transform hover:scale-105">
                    Join an Event
                </a>
                <a href="leaderboard.php" class="bg-transparent border-2 border-white hover:bg-white hover:bg-opacity-10 text-white px-8 py-3 rounded-lg text-lg font-medium transition transform hover:scale-105">
                    View Leaderboard
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>