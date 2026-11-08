<?php
/**
 * PHPUnit bootstrap: load the plugin outside WordPress with sample data and no network.
 *
 * @package GitWidget
 */

require dirname( __DIR__ ) . '/dev/wp-stubs.php';
require dirname( __DIR__ ) . '/git-widget.php';
gw_dev_load_sample();
