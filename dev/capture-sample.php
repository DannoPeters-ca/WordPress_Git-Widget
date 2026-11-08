<?php
/**
 * Replaces dev/sample.json with real data for one GitHub account, fetched through the plugin's own
 * functions, so the preview, the tests and the README screenshots show a real profile.
 *
 * Usage (needs PHP with the curl extension and network access to GitHub):
 *     GW_TOKEN=github_pat_xxx php dev/capture-sample.php DannoPeters
 *
 * The token is optional but recommended: with one, contribution types and yearly totals are exact, as
 * they are on a live site. It is only used for this run and is never written to sample.json.
 *
 * @package GitWidget
 */

$user = $argv[1] ?? '';
if ( ! preg_match( '/^[A-Za-z0-9-]{1,39}$/', $user ) ) {
	fwrite( STDERR, "Usage: GW_TOKEN=... php dev/capture-sample.php USERNAME\n" );
	exit( 1 );
}
putenv( "GW_USER=$user" );
define( 'GW_DEV_LIVE', true );

require __DIR__ . '/wp-stubs.php';
require dirname( __DIR__ ) . '/git-widget.php';
$GLOBALS['gw_capture'] = array();

// Everything the preloader would warm on a live site...
foreach ( gw_preload_jobs( $user ) as $job ) {
	gw_preload_run_job( $user, $job );
}
// ...plus everything the preview cases and screenshots draw, in every time frame.
$all   = implode( ',', array_keys( gw_sections() ) );
$cases = json_decode( file_get_contents( __DIR__ . '/cases.json' ), true );
$cases[] = array( 'sections' => $all );
$cases[] = array( 'sections' => $all, 'period' => 'all' );
$years = gw_contribution_years( $user );
foreach ( is_wp_error( $years ) ? array() : $years as $y ) {
	$cases[] = array( 'sections' => $all, 'year' => (string) $y, 'period' => 'year' );
}
$errors = array();
foreach ( $cases as $atts ) {
	$html = gw_render( $atts );
	if ( preg_match_all( '#<p class="gw-note">(.*?)</p>#', $html, $m ) ) {
		$errors = array_merge( $errors, $m[1] );
	}
}

if ( empty( $GLOBALS['gw_capture'][ "profile:$user" ] ) ) {
	fwrite( STDERR, "Could not fetch the profile for $user; sample.json left unchanged.\n" . implode( "\n", array_unique( $errors ) ) . "\n" );
	exit( 1 );
}
ksort( $GLOBALS['gw_capture'] );
file_put_contents( __DIR__ . '/sample.json', json_encode( $GLOBALS['gw_capture'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" );
printf( "Saved %d entries for %s to dev/sample.json\n", count( $GLOBALS['gw_capture'] ), $user );
foreach ( array_unique( $errors ) as $e ) {
	echo 'Note: ' . html_entity_decode( $e ) . "\n";
}
