<?php
/**
 * Rendering widgets from the cached sample data in dev/sample.json.
 *
 * @package GitWidget
 */

use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase {

	/**
	 * Every preview case in dev/cases.json.
	 */
	public function cases() {
		$out = array();
		foreach ( json_decode( file_get_contents( dirname( __DIR__ ) . '/dev/cases.json' ), true ) as $label => $atts ) {
			$out[ $label ] = array( $atts );
		}
		return $out;
	}

	/**
	 * @dataProvider cases
	 */
	public function test_preview_case_renders_every_requested_section( $atts ) {
		$html = gw_render( $atts );
		$this->assertStringStartsWith( '<div id="gw-', $html );
		$this->assertStringNotContainsString( 'gw-note', $html, 'A section fell back to an error note.' );
		foreach ( array_map( 'trim', explode( ',', $atts['sections'] ) ) as $section ) {
			$this->assertStringContainsString( 'class="gw-section gw-' . $section . '"', $html );
		}
	}

	public function test_unknown_sections_are_ignored() {
		$html = gw_render( array( 'sections' => 'profile,nonsense' ) );
		$this->assertStringContainsString( 'gw-section gw-profile', $html );
		$this->assertStringNotContainsString( 'nonsense', $html );
	}

	public function test_invalid_accent_falls_back_to_github_green() {
		$html = gw_render( array( 'sections' => 'profile', 'accent' => 'red;background:url(x)' ) );
		$this->assertStringContainsString( '--gw-accent:#2da44e', $html );
	}

	public function test_custom_accent_is_a_pro_option() {
		$atts = array( 'sections' => 'profile', 'accent' => '#8250df' );
		$this->assertStringContainsString( '--gw-accent:#2da44e', gw_render( $atts ), 'Free edition keeps GitHub colours.' );

		add_filter( 'gw_is_pro', '__return_true' );
		try {
			$html = gw_render( $atts );
		} finally {
			unset( $GLOBALS['gw_f']['gw_is_pro'] );
		}
		$this->assertStringContainsString( '--gw-accent:#8250df', $html );
		$this->assertStringContainsString( 'data-gw-edition="pro"', $html );
	}

	public function test_title_is_escaped() {
		$html = gw_render( array( 'sections' => 'profile', 'title' => '<img src=x onerror=alert(1)>' ) );
		$this->assertStringNotContainsString( '<img src=x', $html );
		$this->assertStringContainsString( '&lt;img', $html );
	}

	public function test_view_on_github_link_can_be_hidden() {
		$this->assertStringContainsString( 'gw-footer', gw_render( array( 'sections' => 'profile' ) ) );
		$this->assertStringNotContainsString( 'gw-footer', gw_render( array( 'sections' => 'profile', 'show_link' => 'no' ) ) );
	}

	public function test_free_edition_keeps_the_credit_line() {
		$html = gw_render( array( 'sections' => 'profile' ) );
		$this->assertStringContainsString( 'data-gw-edition="free"', $html );
		$this->assertStringContainsString( 'gw-credit', $html );
		$this->assertStringContainsString( 'data-gw="' . GW_VERSION . '"', $html );
	}

	public function test_shortcode_drops_unknown_attributes() {
		$html = gw_shortcode( array( 'sections' => 'profile', 'onclick' => 'evil()' ) );
		$this->assertStringContainsString( 'gw-section gw-profile', $html );
		$this->assertStringNotContainsString( 'evil()', $html );
	}
}
