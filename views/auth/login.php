<?php include '../../includes/header.php'; ?>

<div class="max-w-md mx-auto bg-gray-800 rounded-xl shadow-md overflow-hidden md:max-w-2xl my-10">
    <div class="p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-purple-400 font-gaming">PLAYER LOGIN</h2>
            <p class="text-gray-400">Enter your credentials to join the battle</p>
        </div>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-600 text-white p-3 rounded-lg mb-4">
                <?php 
                $error = htmlspecialchars($_GET['error']);
                echo $error === 'invalid' ? 'Invalid username or password' : $error;
                ?>
            </div>
        <?php endif; ?>
        
        <form action="../../controllers/authController.php?action=login" method="POST">
            <div class="mb-4">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-purple-500" 
                       id="username" name="username" type="text" placeholder="Your username" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-300 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-purple-500" 
                       id="password" name="password" type="password" placeholder="Your password" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg w-full transition" type="submit">
                    LOGIN
                </button>
            </div>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-gray-400">Don't have an account? <a href="register.php" class="text-purple-400 hover:text-purple-300 transition">Register here</a></p>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>