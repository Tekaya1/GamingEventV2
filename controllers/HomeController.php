<?php
require_once __DIR__ . '/../models/Event.php';

class HomeController {
    public function index() {
        // Get upcoming events
        $event = new Event();

        
        // Load the view
        require_once __DIR__ . '/../index.php';
    }
}