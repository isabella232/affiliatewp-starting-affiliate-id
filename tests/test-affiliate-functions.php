<?php

use AffiliateWP_Starting_Affiliate_ID\Admin;
use AffWP\Tests\UnitTestCase;

require dirname( __FILE__ ) . '/../includes/class-admin.php';

/**
 * Tests for Affiliate functions in affiliate-functions.php.
 *
 * @group affiliates
 * @group functions
 */
class Tests extends UnitTestCase {

	/**
	 * Users fixture.
	 *
	 * @access protected
	 * @var array
	 * @static
	 */
	protected static $users = array();

	/**
	 * Affiliates fixture.
	 *
	 * @access protected
	 * @var array
	 * @static
	 */
	protected static $affiliates = array();

	/**
	 * Set up fixtures once.
	 */
	public static function wpSetUpBeforeClass() {

		// Reset the affiliate ID auto increment value
		Admin::update_affiliate_id_auto_increment( 0 );

		self::$users = parent::affwp()->user->create_many( 3 );

		foreach ( self::$users as $user ) {
			self::$affiliates[] = parent::affwp()->affiliate->create( array(
					'user_id' => $user,
			) );
		}

	}

	//
	// Tests
	//
	/**
	 * @covers \Affiliate_WP_DB_Affiliates::get_newest_affiliate_id
	 */
	public function test_get_newest_affiliate_id_should_return_newest_affiliate_id() {
		$highest_affiliate_id      = max( self::$affiliates );
		$test_highest_affiliate_id = Admin::get_newest_affiliate_id();

		$this->assertSame( $test_highest_affiliate_id, $highest_affiliate_id );
	}

	/**
	 * @covers \Affiliate_WP_DB_Affiliates::update_affiliate_id_auto_increment
	 */
	public function test_update_auto_increment_will_reset_if_invalid_value_is_provided() {
		global $wpdb;

		Admin::update_affiliate_id_auto_increment( 'this_should_convert_to_a_zero' );

		$query = $wpdb->get_results(
				"SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES
				 WHERE TABLE_NAME = '{$wpdb->prefix}affiliate_wp_affiliates'" );

		$this->assertGreaterThan( 0, $query[0]->AUTO_INCREMENT );
	}

	/**
	 * @covers \Affiliate_WP_DB_Affiliates::update_affiliate_id_auto_increment
	 */
	public function test_update_auto_increment_will_auto_set_auto_increment_correctly_if_increment_number_is_less_than_what_is_possible() {
		global $wpdb;

		$impossible_auto_increment = max( self::$affiliates );
		Admin::update_affiliate_id_auto_increment( $impossible_auto_increment );

		$query = $wpdb->get_results(
				"SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES
				 WHERE TABLE_NAME = '{$wpdb->prefix}affiliate_wp_affiliates'" );

		$this->assertGreaterThan( $impossible_auto_increment, $query[0]->AUTO_INCREMENT );
	}

	/**
	 * @covers \Affiliate_WP_DB_Affiliates::update_affiliate_id_auto_increment
	 */
	public function test_adding_new_affiliate_after_auto_increment_is_increased_sets_affiliate_id_correctly() {
		$auto_increment = max( self::$affiliates ) + 5;
		Admin::update_affiliate_id_auto_increment( $auto_increment );

		$new_user      = parent::affwp()->user->create();
		$new_affiliate = affiliate_wp()->affiliates->add( $new_user );

		$this->assertSame( $auto_increment, $new_affiliate );
	}
}
