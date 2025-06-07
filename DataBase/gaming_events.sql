-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 07 juin 2025 à 19:38
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gaming_events`
--

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `current_participants` int(11) DEFAULT 0,
  `prize_pool` decimal(10,2) DEFAULT NULL,
  `rules` text DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `game_id`, `start_date`, `end_date`, `max_participants`, `current_participants`, `prize_pool`, `rules`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'aa', 'aaa', NULL, '2025-06-20 10:10:00', '2025-06-25 14:13:00', 199, 3, 1000.00, 'aaa', 'upcoming', 5, '2025-06-07 09:08:30', '2025-06-07 16:19:12'),
(2, 'aaa', 'Hello', NULL, '2025-06-07 14:45:00', '2025-06-07 21:51:00', 10, 0, 120.00, '22ZSS', 'completed', 5, '2025-06-07 13:45:32', '2025-06-07 15:25:31');

-- --------------------------------------------------------

--
-- Structure de la table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'default_game.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `games`
--

INSERT INTO `games` (`id`, `name`, `type`, `platform`, `image`, `created_at`) VALUES
(1, 'Fortnite', 'Battle Royale', 'PC', 'fortnite.png', '2025-06-07 16:44:52'),
(2, 'valorant', 'batlleroyal', 'pc', 'default_game.png', '2025-06-07 17:01:48'),
(3, 'valorant', 'batlleroyal', 'pc', 'default_game.png', '2025-06-07 17:02:19'),
(4, 'valorant', 'batlleroyal', 'pc', 'default_game.png', '2025-06-07 17:04:17'),
(5, 'valorant10', 'batlleroyal', 'ps5', '1749316421_valorant.jpg', '2025-06-07 17:13:41'),
(6, 'valorantqqqq', 'batlleroyal', 'dqdsq', '1749316449_valorant.jpg', '2025-06-07 17:14:09'),
(7, 'valorant1111111', 'batlleroyal', 'ps5', '1749316928_valorant.jpg', '2025-06-07 17:22:08');

-- --------------------------------------------------------

--
-- Structure de la table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `total_score` int(11) DEFAULT 0,
  `games_played` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('registered','attended','no_show') DEFAULT 'registered',
  `score` int(11) DEFAULT 0,
  `ranking` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `registrations`
--

INSERT INTO `registrations` (`id`, `user_id`, `event_id`, `registration_date`, `status`, `score`, `ranking`) VALUES
(1, 7, 1, '2025-06-07 12:37:09', 'registered', 0, 0),
(2, 8, 1, '2025-06-07 15:24:57', 'registered', 0, 0),
(3, 9, 1, '2025-06-07 16:19:12', 'registered', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','player','visitor') DEFAULT 'visitor',
  `avatar` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `avatar`, `created_at`, `updated_at`) VALUES
(5, 'ghassntech', 'ghassntech@gmail.com', '$2y$10$OurIEWTWTGex7tb7WWWBtuiDh80Xw7S118T3HUBfVTAO8568eyv4i', 'admin', '684420338ee55.png', '2025-06-06 23:34:24', '2025-06-07 11:19:15'),
(7, 'zilla123\'5', 'zilla123@gmail.com', '$2y$10$wzL0wyV.yyG/yveXTdgkO.5TVvhfgWOW6o.qd2ME0xNSWboNBwZ0u', 'player', 'default.png', '2025-06-07 12:25:23', '2025-06-07 15:27:41'),
(8, 'zilla123OO', 'seifbaar123772@gmail.com', '$2y$10$oVy3bZUAFi06427m0SiXv.F4AzC11WYDxGFiAItot/yucnzYcsz1K', 'player', 'default.png', '2025-06-07 15:24:48', '2025-06-07 15:24:48'),
(9, 'user1', 'wahbijouini169@gmail.com', '$2y$10$pvHMdxW0SuBYrHABXwOZyufL/qprjKN94RPYy8f7bwP7U6Pii8.te', 'player', 'default.png', '2025-06-07 16:17:59', '2025-06-07 16:17:59');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `fk_event_game` (`game_id`);

--
-- Index pour la table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Index pour la table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_registration` (`user_id`,`event_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_event_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD CONSTRAINT `leaderboard_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `leaderboard_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);

--
-- Contraintes pour la table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
