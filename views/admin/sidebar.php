<?php
// views/admin/sidebar.php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Event.php';
require_once __DIR__ . '/../../config/config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
$user = new User();
$event = new Event();
$userCount = count($user->getAllUsers());
$eventCount = count($event->getAllEvents());
$activeEventCount = count(array_filter($event->getAllEvents(), function ($e) {
    return $e['status'] === 'upcoming' || $e['status'] === 'ongoing';
}))
    ?>

<div class="w-full md:w-1/4">
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <div class="flex flex-col items-center">
            <img src="<?php echo htmlspecialchars(BASE_URL . 'assets/images/avatars/' . ($currentUser['avatar'] ?? 'default.png')); ?>"
                alt="Admin Avatar" class="w-24 h-24 rounded-full border-4 border-red-500 mb-4">
            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($currentUser['username']); ?></h3>
            <p class="text-gray-400 mb-4"><?php echo htmlspecialchars($currentUser['email']); ?></p>
            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-xs">Admin</span>
        </div>

        <div class="mt-6">
            <h4 class="text-lg font-semibold mb-3 border-b border-gray-700 pb-2">System Stats</h4>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400">Total Users</span>
                    <span class="font-medium"><?php echo $userCount; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Total Events</span>
                    <span class="font-medium"><?php echo $eventCount; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Active Events</span>
                    <span class="font-medium"><?php echo $activeEventCount ?? ""; ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <h4 class="text-lg font-semibold mb-3 border-b border-gray-700 pb-2">Admin Menu</h4>
        <ul class="space-y-2">
            <li>
                <a href="<?php echo BASE_URL; ?>views/admin/dashboard.php"
                    class="<?php echo $currentPage === 'dashboard.php' ? 'text-red-400' : 'text-gray-300'; ?> hover:text-white transition flex items-center space-x-2 p-2 rounded-lg <?php echo $currentPage === 'dashboard.php' ? 'bg-gray-700' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>views/admin/events.php"
                    class="<?php echo $currentPage === 'events.php' ? 'text-red-400' : 'text-gray-300'; ?> hover:text-white transition flex items-center space-x-2 p-2 rounded-lg <?php echo $currentPage === 'events.php' ? 'bg-gray-700' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span>Manage Events</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>views/admin/users.php"
                    class="<?php echo $currentPage === 'users.php' ? 'text-red-400' : 'text-gray-300'; ?> hover:text-white transition flex items-center space-x-2 p-2 rounded-lg <?php echo $currentPage === 'users.php' ? 'bg-gray-700' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span>Manage Users</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>views/admin/reports.php"
                    class="<?php echo $currentPage === 'reports.php' ? 'text-red-400' : 'text-gray-300'; ?> hover:text-white transition flex items-center space-x-2 p-2 rounded-lg <?php echo $currentPage === 'reports.php' ? 'bg-gray-700' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span>Reports</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>views/admin/settings.php"
                    class="<?php echo $currentPage === 'settings.php' ? 'text-red-400' : 'text-gray-300'; ?> hover:text-white transition flex items-center space-x-2 p-2 rounded-lg <?php echo $currentPage === 'settings.php' ? 'bg-gray-700' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </div>
</div>