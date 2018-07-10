<?php

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/bootstrap.custom.php')) {
    require_once __DIR__ . '/bootstrap.custom.php';
} else {
    require_once __DIR__ . '/bootstrap.dist.php';
}
