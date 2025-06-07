<?php include '../../includes/header.php'; ?>

<div class="max-w-md mx-auto bg-gray-800 rounded-xl shadow-md overflow-hidden md:max-w-2xl my-10">
    <div class="p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-blue-400 font-gaming">NEW PLAYER REGISTRATION</h2>
            <p class="text-gray-400">Create your account to join the gaming community</p>
        </div>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-600 text-white p-3 rounded-lg mb-4">
                <?php 
                $error = htmlspecialchars($_GET['error']);
                if ($error === 'username_taken') {
                    echo 'Username is already taken';
                } elseif ($error === 'email_taken') {
                    echo 'Email is already registered';
                } else {
                    echo 'Registration failed. Please try again.';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <form action="../../controllers/authController.php?action=register" method="POST">
            <div class="mb-4">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-blue-500" 
                       id="username" name="username" type="text" placeholder="Choose a username" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-blue-500" 
                       id="email" name="email" type="email" placeholder="Your email address" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-blue-500" 
                       id="password" name="password" type="password" placeholder="Create a password" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="confirm_password">
                    Confirm Password
                </label>
                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-blue-500" 
                       id="confirm_password" name="confirm_password" type="password" placeholder="Confirm your password" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg w-full transition" type="submit">
                    REGISTER
                </button>
            </div>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-gray-400">Already have an account? <a href="login.php" class="text-blue-400 hover:text-blue-300 transition">Login here</a></p>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>