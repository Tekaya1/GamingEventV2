<?php
// Check if user is admin
require_once '../../bootstrap.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$user = new User();
$allUsers = $user->getAllUsers();

// Get single user if ID is provided for edit
$editUser = null;
if (isset($_GET['id'])) {
    $editUser = $user->getUserById($_GET['id']);
}
?>
<?php include '../../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="w-full md:w-3/4">
            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-600 text-white p-3 rounded-lg mb-6">
                    <?php
                    $success = htmlspecialchars($_GET['success']);
                    if ($success === 'updated') {
                        echo 'User updated successfully!';
                    } elseif ($success === 'deleted') {
                        echo 'User deleted successfully!';
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

            <?php if (isset($_GET['id'])): ?>
                <!-- Edit User Form -->
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold mb-6">Edit User: <?php echo htmlspecialchars($editUser['username']); ?>
                    </h2>

                    <form action="../../controllers/UserController.php?action=update" method="POST"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $editUser['id']; ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="username">
                                    Username
                                </label>
                                <input
                                    class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500"
                                    id="username" name="username" type="text" placeholder="Username"
                                    value="<?php echo htmlspecialchars($editUser['username']); ?>" required>
                            </div>
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="email">
                                    Email
                                </label>
                                <input
                                    class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500"
                                    id="email" name="email" type="email" placeholder="Email"
                                    value="<?php echo htmlspecialchars($editUser['email']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="role">
                                Role
                            </label>
                            <select
                                class="bg-gray-700 appearance-none border-2 border-gray-700 rounded w-full py-3 px-4 text-white leading-tight focus:outline-none focus:border-red-500"
                                id="role" name="role" required>
                                <option value="admin" <?php echo $editUser['role'] === 'admin' ? 'selected' : ''; ?>>Admin
                                </option>
                                <option value="player" <?php echo $editUser['role'] === 'player' ? 'selected' : ''; ?>>Player
                                </option>
                                <option value="visitor" <?php echo $editUser['role'] === 'visitor' ? 'selected' : ''; ?>>
                                    Visitor</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="avatar">
                                Avatar
                            </label>
                            <div class="flex items-center space-x-4">
                                <img src="../../assets/images/avatars/<?php echo htmlspecialchars($editUser['avatar']); ?>"
                                    alt="Avatar" class="w-16 h-16 rounded-full border-2 border-red-500">
                                <input type="file" id="avatar" name="avatar" class="text-gray-400 text-sm">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="users.php"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">Cancel</a>
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">Update
                                User</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Users List -->
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Manage Users</h2>
                        <div class="relative">
                            <input type="text" placeholder="Search users..."
                                class="bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:border-red-500">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        User</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Email</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Role</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Joined</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                <?php foreach ($allUsers as $user): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full"
                                                        src="<?php echo BASE_URL; ?>assets/images/avatars/<?php echo htmlspecialchars($user['avatar']); ?>"
                                                        alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium">
                                                        <?php echo htmlspecialchars($user['username']); ?>
                                                    </div>
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
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $roleColor; ?> text-white">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="users.php?id=<?php echo $user['id']; ?>"
                                                class="text-blue-400 hover:text-blue-300 transition mr-3">Edit</a>
                                            <form action="../../controllers/userController.php?action=delete" method="POST"
                                                class="inline">
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="text-red-400 hover:text-red-300 transition"
                                                    onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                            </form>
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