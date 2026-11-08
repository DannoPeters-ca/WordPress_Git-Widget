<?php
/**
 * Development preview: renders widgets with sample data outside WordPress.
 * Usage: php dev/preview.php > preview.html
 *
 * @package GitWidget
 */

require __DIR__ . '/wp-stubs.php';

if ( getenv( 'GW_PRO' ) ) {
	add_filter( 'gw_is_pro', '__return_true' );
	// Point GW_PRO_DIR at a Git Widget Pro checkout to preview its styling too.
	if ( getenv( 'GW_PRO_DIR' ) ) {
		define( 'GWP_VERSION', 'dev' );
		require getenv( 'GW_PRO_DIR' ) . '/includes/style.php';
		add_filter( 'gw_defaults', 'gwp_defaults' );
		add_filter( 'gw_widget_style', 'gwp_widget_style', 10, 2 );
		add_filter( 'gw_widget_classes', 'gwp_widget_classes', 10, 2 );
	}
}

require dirname( __DIR__ ) . '/git-widget.php';

// Sample data, cached under the same keys the plugin uses.
gw_dev_load_sample();

$css = file_get_contents( dirname( __DIR__ ) . '/assets/git-widget.css' );
echo "<!doctype html><meta charset=utf-8><title>Git Widget preview</title><style>body{font-family:system-ui;margin:24px;max-width:1100px}"
	. ".pair{display:grid;grid-template-columns:1fr 1fr;gap:24px}.dark{background:#1e1e1e;color:#ddd;padding:16px}{$css}</style>";
echo '<script>' . file_get_contents( dirname( __DIR__ ) . '/assets/git-widget.js' ) . '</script>';
$cases = json_decode( file_get_contents( __DIR__ . '/cases.json' ), true );
foreach ( $cases as $label => $atts ) {
	echo '<h2>' . esc_html( $label ) . '</h2><div class="pair"><div>' . gw_render( $atts ) . '</div><div class="dark">' . gw_render( $atts ) . '</div></div>';
}
echo '<h2>Shortcodes side by side</h2><div class="pair"><div>' . gw_shortcode( array( 'sections' => 'profile,organisations' ) ) . '</div><div>'
	. gw_shortcode( array( 'sections' => 'overview,calendar', 'theme' => 'dark', 'accent' => '#8250df', 'title' => 'This year on GitHub' ) ) . '</div></div>';
