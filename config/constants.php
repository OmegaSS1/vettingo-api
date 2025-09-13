<?php

define('ENV', parse_ini_file('.env'));
define('DNS', $_SERVER['ADDR_REMOTE'] ?? $_SERVER['SERVER_NAME'] ?? 'http://localhost');
define('IP', DNS . ENV['PATH_APPLICATION']);
define('HTTP', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://');
