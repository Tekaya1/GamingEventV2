<?php
require_once '../../bootstrap.php';
require_once '../../models/Event.php';
require_once '../../models/Game.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$event = new Event();
$gameModel = new Game();
$allEvents = $event->getAllEvents();
$games = $gameModel->getAllGames();

// Get single event if ID is provided for edit
$editEvent = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editEvent = $event->getEventById($_GET['id']);
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <?php include __DIR__ . '/sidebar.php'; ?>

        <div class="w-full md:w-3/4">
            <?php if (isset($_GET['action']) && ($_GET['action'] === 'create' || $_GET['action'] === 'edit')): ?>
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold mb-6">
                        <?php echo $_GET['action'] === 'create' ? 'Create New Event' : 'Edit Event'; ?>
                    </h2>

                    <form action="../../controllers/eventController.php?action=<?php echo $_GET['action']; ?>"
                        method="POST">
                        <?php if ($_GET['action'] === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $editEvent['id']; ?>">
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="title">Event Title</label>
                                <input class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="title"
                                    name="title" type="text" required
                                    value="<?php echo isset($editEvent['title']) ? htmlspecialchars($editEvent['title']) : ''; ?>">
                            </div>
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="game_id">Game</label>
                                <select id="game_id" name="game_id" required
                                    class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white">
                                    <option value="">-- Select a game --</option>
                                    <?php foreach ($games as $game): ?>
                                        <option value="<?php echo $game['id']; ?>" <?php echo (isset($editEvent['game_id']) && $editEvent['game_id'] == $game['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($game['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="description">Description</label>
                            <textarea class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="description"
                                name="description" rows="4"
                                required><?php echo isset($editEvent['description']) ? htmlspecialchars($editEvent['description']) : ''; ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="start_date">Start Date &amp; Time</label>
                                <input class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="start_date"
                                    name="start_date" type="datetime-local"
                                    value="<?php echo isset($editEvent['start_date']) ? date('Y-m-d\TH:i', strtotime($editEvent['start_date'])) : ''; ?>"
                                    required>
                            </div>
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="end_date">End Date &amp; Time</label>
                                <input class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="end_date"
                                    name="end_date" type="datetime-local"
                                    value="<?php echo isset($editEvent['end_date']) ? date('Y-m-d\TH:i', strtotime($editEvent['end_date'])) : ''; ?>"
                                    required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="max_participants">Max Participants</label>
                                <input class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white"
                                    id="max_participants" name="max_participants" type="number" min="1"
                                    value="<?php echo isset($editEvent['max_participants']) ? $editEvent['max_participants'] : ''; ?>">
                            </div>
                            <div>
                                <label class="block text-gray-300 text-sm font-bold mb-2" for="prize_pool">Prize Pool ($)</label>
                                <input class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="prize_pool"
                                    name="prize_pool" type="number" min="0" step="0.01"
                                    value="<?php echo isset($editEvent['prize_pool']) ? $editEvent['prize_pool'] : '0'; ?>"
                                    required>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="rules">Rules</label>
                            <textarea class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="rules"
                                name="rules" rows="6"
                                required><?php echo isset($editEvent['rules']) ? htmlspecialchars($editEvent['rules']) : ''; ?></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="video_type">Video Type</label>
                            <select class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="video_type"
                                name="video_type">
                                <option value="">-- No Video --</option>
                                <option value="youtube" <?php echo (isset($editEvent['video_type']) && $editEvent['video_type'] === 'youtube' ? 'selected' : ''); ?>>YouTube</option>
                                <option value="twitch" <?php echo (isset($editEvent['video_type']) && $editEvent['video_type'] === 'twitch' ? 'selected' : ''); ?>>Twitch</option>
                                <option value="custom" <?php echo (isset($editEvent['video_type']) && $editEvent['video_type'] === 'custom' ? 'selected' : ''); ?>>Custom URL</option>
                            </select>
                        </div>

                        <div class="mb-6 video-url-field"
                            style="<?php echo (!isset($editEvent['video_type']) || empty($editEvent['video_type']) ? 'display: none;' : ''); ?>">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="video_url">Video URL/Embed Code</label>
                            <input class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="video_url"
                                name="video_url" type="text"
                                value="<?php echo isset($editEvent['video_url']) ? htmlspecialchars($editEvent['video_url']) : ''; ?>">
                        </div>

                        <div class="mb-6 video-thumbnail-field"
                            style="<?php echo (!isset($editEvent['video_type']) || empty($editEvent['video_type']) ? 'display: none;' : ''); ?>">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="video_thumbnail">Video Thumbnail</label>
                            <input class="bg-gray-700 border-2 rounded w-full py-3 px-4 text-white" id="video_thumbnail"
                                name="video_thumbnail" type="text"
                                value="<?php echo isset($editEvent['video_thumbnail']) ? htmlspecialchars($editEvent['video_thumbnail']) : ''; ?>">
                        </div>

                        <script>
                            document.getElementById('video_type').addEventListener('change', function () {
                                const videoUrlField = document.querySelector('.video-url-field');
                                const videoThumbnailField = document.querySelector('.video-thumbnail-field');

                                if (this.value) {
                                    videoUrlField.style.display = 'block';
                                    videoThumbnailField.style.display = 'block';
                                } else {
                                    videoUrlField.style.display = 'none';
                                    videoThumbnailField.style.display = 'none';
                                }
                            });
                        </script>

                        <div class="flex justify-end space-x-4">
                            <a href="events.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">Cancel</a>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                                <?php echo $_GET['action'] === 'create' ? 'Create Event' : 'Update Event'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Events listing -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">All Events</h2>
                <?php if (count($allEvents) > 0): ?>
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700 text-gray-300">
                            <tr>
                                <th class="px-6 py-3 text-left">Title</th>
                                <th class="px-6 py-3 text-left">Game</th>
                                <th class="px-6 py-3 text-left">Start</th>
                                <th class="px-6 py-3 text-left">End</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 text-white divide-y divide-gray-600">
                            <?php foreach ($allEvents as $ev): ?>
                                <tr>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($ev['title']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($ev['game_name'] ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4"><?php echo date('Y-m-d H:i', strtotime($ev['start_date'])); ?></td>
                                    <td class="px-6 py-4"><?php echo date('Y-m-d H:i', strtotime($ev['end_date'])); ?></td>
                                    <td class="px-6 py-4"><?php echo ucfirst($ev['status']); ?></td>
                                    <td class="px-6 py-4 space-x-2">
                                        <a href="events.php?action=edit&id=<?php echo $ev['id']; ?>"
                                            class="text-yellow-400 hover:underline">Edit</a>
                                        <form action="../../controllers/eventController.php?action=delete" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this event?');">
                                            <input type="hidden" name="id" value="<?php echo $ev['id']; ?>">
                                            <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                        </form>
                                        <!-- Add the register button -->
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'player'): ?>
                                            <form action="../../controllers/eventController.php?action=register" method="POST" class="inline">
                                                <input type="hidden" name="event_id" value="<?php echo $ev['id']; ?>">
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Register</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-gray-400 mt-4">No events found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
