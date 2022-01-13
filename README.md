# WP_Object_Cache_Session_Tokens
If you have a fast and reliable object cache backend and want to skip the default WP_User_Meta Session Store, use this class instead. It stores the Session Tokens directly into your configured Object Cache and skips user_meta.
It also makes sure to avoid concurrency issues with `user_meta` and Object Caches.

## Install

**Make sure you have a persistent Object Cache installed and activated!**
_Like WP-Redis, Object Cache Pro, ..._

1. Drop the `class-wp-object-cache-session-tokens.php` file into your `wp-content/mu-plugins` folder

Now you are all set.

## Benefits

If your Object Cache is fast and persistent, skip the hassle to end up in there using the WP Meta system.
In advance this implementation solves concurrency issues with Object Caching backends. Calls to `get_user_meta` can access stale runtime cache data even when the data in the caching backend already changed by a concurrent process.
