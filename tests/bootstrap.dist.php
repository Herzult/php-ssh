<?php

define('TEST_HOST', getenv('TEST_HOST') ?? 'localhost');
define('TEST_USER', getenv('TEST_USER') ?? get_current_user());
define('TEST_PASSWORD', getenv('TEST_PASSWORD') ?? '1234');
