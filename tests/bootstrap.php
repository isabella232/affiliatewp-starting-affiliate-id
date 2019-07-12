<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

$_affwp_dir = getenv( 'AFFWP_DIR' );
if ( ! $_affwp_dir ) {
	$_affwp_dir = '/tmp/wordpress/wp-content/plugins/affiliate-wp';
}

require_once $_tests_dir . '/includes/functions.php';
require_once $_affwp_dir . '/tests/phpunit/factories/class-affwp-factory-for-affiliates.php';

function _manually_load_plugin() {
	global $_affwp_dir;
	require $_affwp_dir . '/affiliate-wp.php';
	require dirname( __FILE__ ) . '/../affiliatewp-starting-affiliate-id.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );


require $_tests_dir . '/includes/bootstrap.php';
require dirname( __FILE__ ) . '/phpunit/affwp-testcase.php';

activate_plugin( 'affiliate-wp/affiliate-wp.php' );

// Install AffiliateWP
affiliate_wp_install();