<?php
session_start();
echo "Active Role: " . ($_SESSION['user_role'] ?? 'none') . "\n";
echo "All Roles: ";
print_r($_SESSION['user_roles'] ?? []);
echo "\n";
