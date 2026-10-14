<?php
require 'config.php';
redirect(!isset($_SESSION['user_id']) ? 'login.php' : (is_admin() ? 'students.php' : 'profile.php'));
