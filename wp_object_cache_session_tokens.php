<?php

/*
Plugin Name: Object Cache Session Token
Plugin URI: https://hive-it.de
Description: !MU-Plugin! Stores session tokens in the object cache.
Version: 1.0.0
Author: Jan Thiel
Author URI: https://hive-it.de
License: GPL3
*/

require_once 'class-wp-object-cache-session-tokens.php';

// Setup this Session Storage Backend
add_filter( 'session_token_manager', function () {
	return 'WP_Object_Cache_Session_Tokens';
}, 0, 1 );