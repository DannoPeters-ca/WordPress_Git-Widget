<?php
/**
 * Minimal WordPress stand-ins so the plugin can run outside WordPress.
 * Shared by dev/preview.php, dev/screenshots.php and the PHPUnit tests.
 *
 * @package GitWidget
 */

// Minimal stand-ins for the WordPress functions the plugin uses.
define( 'ABSPATH', __DIR__ );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'DAY_IN_SECONDS', 86400 );
define( 'WEEK_IN_SECONDS', 604800 );
define( 'YEAR_IN_SECONDS', 31536000 );
$GLOBALS['gw_t'] = array();
function __( $s ) { return $s; }
function esc_html__( $s ) { return htmlspecialchars( $s ); }
function esc_attr__( $s ) { return htmlspecialchars( $s ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function sanitize_html_class( $s ) { return preg_replace( '/[^A-Za-z0-9_-]/', '', $s ); }
function sanitize_hex_color( $c ) { return preg_match( '/^#[0-9a-f]{3,6}$/i', $c ) ? $c : null; }
function sanitize_title( $s ) { return strtolower( trim( preg_replace( '/[^A-Za-z0-9]+/', '-', $s ), '-' ) ); }
function wp_register_style() {}
function wp_add_inline_style() {}
function wp_parse_args( $a, $d ) { return array_merge( $d, (array) $a ); }
function wp_unique_id() { static $i = 0; return ++$i; }
function current_user_can() { return true; }
function get_option( $k, $d = false ) { return 'gw_settings' === $k ? array( 'username' => 'octocat', 'token' => getenv( 'GW_TOKEN' ) ? 'x' : '', 'cache_hours' => 6 ) : $d; }
function update_option() {}
function get_transient( $k ) { return $GLOBALS['gw_t'][ $k ] ?? false; }
function set_transient( $k, $v ) { $GLOBALS['gw_t'][ $k ] = $v; }
function is_wp_error( $x ) { return $x instanceof WP_Error; }
class WP_Error { public $m; function __construct( $c, $m ) { $this->m = $m; } function get_error_message() { return $this->m; } }
function _n( $s, $p, $n ) { return 1 == $n ? $s : $p; }
function number_format_i18n( $n ) { return number_format( $n ); }
function wp_enqueue_style() {}
function wp_enqueue_script() {}
function add_action() {} function register_deactivation_hook() {} function is_admin() { return false; } function wp_next_scheduled() { return false; } function wp_schedule_event() {} function wp_clear_scheduled_hook() {}
function add_shortcode() {}
function shortcode_atts( $d, $a ) { return array_merge( $d, array_intersect_key( $a, $d ) ); }
function plugin_dir_path( $f ) { return dirname( $f ) . '/'; }
function plugin_dir_url( $f ) { return ''; }
function date_i18n( $f, $t ) { return gmdate( $f, $t ); }
function wp_date( $f, $t ) { return gmdate( $f, $t ); }
function add_filter( $h, $f, $p = 10, $n = 1 ) { $GLOBALS['gw_f'][ $h ][] = array( $f, $n ); }
function apply_filters( $h, $v, ...$args ) { foreach ( $GLOBALS['gw_f'][ $h ] ?? array() as $f ) { $v = call_user_func_array( $f[0], array_slice( array_merge( array( $v ), $args ), 0, max( 1, $f[1] ) ) ); } return $v; }
function __return_true() { return true; }
function esc_html_e( $s ) { echo htmlspecialchars( $s ); }
function wp_remote_get() { return new WP_Error( 'offline', 'The preview has no network.' ); }
function wp_remote_post() { return new WP_Error( 'offline', 'The preview has no network.' ); }

/**
 * Seeds the cache with dev/sample.json so rendering never touches the network.
 */
function gw_dev_load_sample() {
	$sample = json_decode( file_get_contents( __DIR__ . '/sample.json' ), true );
	foreach ( $sample as $key => $value ) {
		set_transient( 'gw_' . md5( $key ), $value );
	}
}
