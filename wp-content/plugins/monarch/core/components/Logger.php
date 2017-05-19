<?php


class ET_Core_Logger {

	/**
	 * Writes a message to the WP Debug and PHP Error logs.
	 *
	 * @param mixed $message
	 */
	private static function _write_log( $message ) {
		if ( ! is_string( $message ) ) {
			$message = print_r( $message, true );
		}

		$backtrace = debug_backtrace( 1, 3 );
		$caller    = $backtrace[2];
		$message   = "{$caller['function']}(): {$message}";

		error_log( $message );
	}

	/**
	 * Writes message to the logs if {@link WP_DEBUG} is `true`, otherwise does nothing.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $message
	 */
	public static function debug( $message ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			self::_write_log( $message );
		}
	}

	/**
	 * Writes an error message to the logs regardless of whether or not debug mode is enabled.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $message
	 */
	public static function error( $message ) {
		self::_write_log( $message );
	}
}
