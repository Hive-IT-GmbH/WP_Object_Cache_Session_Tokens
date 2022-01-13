<?php
/**
 * Session API: WP_Object_Cache_Session_Tokens class
 *
 * @package WordPress
 * @subpackage Session
 */

/**
 * Object Cache based user sessions token manager.
 * Based on the WP_User_Meta_Session_Tokens implementation
 *
 * @since 1.0.0
 *
 * @see WP_Session_Tokens
 */
class WP_Object_Cache_Session_Tokens extends WP_Session_Tokens {

	/**
	 * Retrieves all sessions of the user.
	 *
	 * @since 1.0.0
	 *
	 * @return array Sessions of the user.
	 */
	protected function get_sessions() {
		$sessions = wp_cache_get( $this->user_id, 'session_tokens', true );

		if ( ! is_array( $sessions ) ) {
			return array();
		}

		$sessions = array_map( array( $this, 'prepare_session' ), $sessions );
		return array_filter( $sessions, array( $this, 'is_still_valid' ) );
	}

	/**
	 * Converts an expiration to an array of session information.
	 *
	 * @param mixed $session Session or expiration.
	 * @return array Session.
	 */
	protected function prepare_session( $session ) {
		if ( is_int( $session ) ) {
			return array( 'expiration' => $session );
		}

		return $session;
	}

	/**
	 * Retrieves a session based on its verifier (token hash).
	 *
	 * @since 1.0.0
	 *
	 * @param string $verifier Verifier for the session to retrieve.
	 * @return array|null The session, or null if it does not exist
	 */
	protected function get_session( $verifier ) {
		$sessions = $this->get_sessions();

		if ( isset( $sessions[ $verifier ] ) ) {
			return $sessions[ $verifier ];
		}

		return null;
	}

	/**
	 * Updates a session based on its verifier (token hash).
	 *
	 * @since 1.0.0
	 *
	 * @param string $verifier Verifier for the session to update.
	 * @param array  $session  Optional. Session. Omitting this argument destroys the session.
	 */
	protected function update_session( $verifier, $session = null ) {
		$sessions = $this->get_sessions();

		if ( $session ) {
			$sessions[ $verifier ] = $session;
		} else {
			unset( $sessions[ $verifier ] );
		}

		$this->update_sessions( $sessions );
	}

	/**
	 * Updates the user's sessions in the usermeta table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sessions Sessions.
	 */
	protected function update_sessions( $sessions ) {
		if ( $sessions ) {
			wp_cache_set( $this->user_id, $sessions, 'session_tokens' );
		} else {
			wp_cache_delete( $this->user_id, 'session_tokens' );
		}
	}

	/**
	 * Destroys all sessions for this user, except the single session with the given verifier.
	 *
	 * @since 1.0.0
	 *
	 * @param string $verifier Verifier of the session to keep.
	 */
	protected function destroy_other_sessions( $verifier ) {
		$session = $this->get_session( $verifier );
		$this->update_sessions( array( $verifier => $session ) );
	}

	/**
	 * Destroys all session tokens for the user.
	 *
	 * @since 1.0.0
	 */
	protected function destroy_all_sessions() {
		$this->update_sessions( array() );
	}

	/**
	 * Destroys all sessions for all users.
	 *
	 * @since 1.0.0
	 */
	public static function drop_sessions() {
		$all_users = get_users( array( 'fields' => 'ID' ) );
		foreach ( $all_users as $userid ) {
			wp_cache_delete( $userid, 'session_tokens' );
		}
	}
}

// Setup this Session Storage Backend
add_filter( 'session_token_manager', function() { return 'WP_Object_Cache_Session_Tokens'; }, 0, 1 );
