<?php
/**
 * Renders the README screenshots from the sample data, one standalone page per shot.
 * Usage: php dev/screenshots.php OUT_DIR   (then dev/screenshots.sh turns them into PNGs)
 *
 * @package GitWidget
 */

require __DIR__ . '/wp-stubs.php';
require dirname( __DIR__ ) . '/git-widget.php';
gw_dev_load_sample();

$out   = rtrim( $argv[1] ?? 'screenshots', '/' );
$shots = array(
	'profile'      => array( 'light', 520, array( 'sections' => 'profile,organisations' ) ),
	'calendar'     => array( 'light', 900, array( 'sections' => 'calendar' ) ),
	'calendar-dark' => array( 'dark', 900, array( 'sections' => 'calendar', 'theme' => 'dark' ) ),
	'overview'     => array( 'light', 900, array( 'sections' => 'overview,types', 'layout' => 'grid' ) ),
	'languages'    => array( 'light', 900, array( 'sections' => 'languages,repositories', 'count' => '4', 'layout' => 'grid' ) ),
	'achievements' => array( 'light', 520, array( 'sections' => 'achievements' ) ),
);

if ( ! is_dir( $out ) ) {
	mkdir( $out, 0777, true );
}
$css = file_get_contents( dirname( __DIR__ ) . '/assets/git-widget.css' );
$js  = file_get_contents( dirname( __DIR__ ) . '/assets/git-widget.js' );
foreach ( $shots as $name => list( $bg, $width, $atts ) ) {
	$body = 'dark' === $bg ? 'background:#0d1117;color:#e6edf3' : 'background:#fff;color:#1f2328';
	file_put_contents(
		"$out/$name.html",
		"<!doctype html><meta charset=utf-8><style>html,body{margin:0}body{font-family:system-ui,sans-serif;{$body}}"
		. "#shot{display:inline-block;padding:20px;width:{$width}px;box-sizing:border-box}{$css}</style>"
		. "<script>{$js}</script><div id=shot>" . gw_render( $atts ) . '</div>'
	);
	echo "$name\n";
}
