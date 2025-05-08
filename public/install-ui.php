<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo shell_exec('composer require laravel/ui');
echo "Laravel UI installed successfully";