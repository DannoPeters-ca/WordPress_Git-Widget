<?php
/**
 * Small formatting helpers.
 *
 * @package GitWidget
 */

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase {

	public function test_numbers_are_shortened_from_one_thousand() {
		$this->assertSame( '999', gw_num( 999 ) );
		$this->assertSame( '1.2k', gw_num( 1234 ) );
		$this->assertSame( '12k', gw_num( 12345 ) );
	}

	public function test_bio_underscore_runs_become_line_breaks() {
		$this->assertSame( 'Engineer<br>Pilot', gw_bio_html( 'Engineer ___ Pilot' ) );
		$this->assertSame( 'Engineer<br>Pilot', gw_bio_html( "Engineer\nPilot" ) );
	}

	public function test_single_underscores_are_kept() {
		$this->assertSame( 'snake_case fan', gw_bio_html( 'snake_case fan' ) );
	}

	public function test_bio_breaks_can_be_turned_off() {
		$this->assertSame( 'a ___ b', gw_bio_html( 'a ___ b', false ) );
	}

	public function test_bio_is_escaped() {
		$this->assertStringNotContainsString( '<script>', gw_bio_html( '<script>x</script> __ hi' ) );
		$this->assertStringNotContainsString( '<script>', gw_bio_html( '<script>x</script>', false ) );
	}
}
