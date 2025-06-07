<?php
// Check if user is logged in
require_once '../../bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/auth/login.php');
    exit();
}

$user = new User();
$currentUser = $user->getUserById($_SESSION['user_id']);
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
            </div>
            
            <div class="bg-gray-800 rounded-lg p-6">
                <h4 class="text-lg font-semibold mb-3 border-b border-gray-700 pb-2">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="dashboard.php" class="text-purple-400 hover:text-purple-300 transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a></li>
                    <li><a href="events.php" class="text-gray-300 hover:text-white transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>My Events</span>
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
                <h2 class="text-2xl font-bold mb-6">My Profile</h2>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="bg-green-600 text-white p-3 rounded-lg mb-6">
                        Profile updated successfully!
                    </div>
                <?php endif; ?>
                
                <form action="../../controllers/profileController.php" method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="username">
                                Username
                            </label>
                            <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-purple-500" 
                                   id="username" name="username" type="text" placeholder="Username" 
                                   value="<?php echo htmlspecialchars($currentUser['username']); ?>" required>
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="email">
                                Email
                            </label>
                            <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-purple-500" 
                                   id="email" name="email" type="email" placeholder="Email" 
                                   value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="avatar">
                            Avatar
                        </label>
                        <div class="flex items-center space-x-4">
                            <img src="../../assets/images/avatars/<?php echo htmlspecialchars($currentUser['avatar']); ?>" alt="Avatar" class="w-16 h-16 rounded-full border-2 border-purple-500">
                            <input type="file" id="avatar" name="avatar" class="text-gray-400 text-sm">
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="current_password">
                            Current Password (leave blank to keep unchanged)
                        </label>
                        <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-purple-500" 
                               id="current_password" name="current_password" type="password" placeholder="Current Password">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="new_password">
                                New Password
                            </label>
                            <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-purple-500" 
                                   id="new_password" name="new_password" type="password" placeholder="New Password">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="confirm_password">
                                Confirm New Password
                            </label>
                            <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-purple-500" 
                                   id="confirm_password" name="confirm_password" type="password" placeholder="Confirm New Password">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>