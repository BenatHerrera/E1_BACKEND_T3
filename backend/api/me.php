<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
json($_SESSION['user']);
