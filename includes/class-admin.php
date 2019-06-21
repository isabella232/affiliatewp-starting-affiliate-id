<?php
/**
 * Affiliate ID Admin Class: AffiliateWP_Starting_Affiliate_ID_Admin class
 *
 * Adds the starting affiliate ID admin settings fields and runs relevant actions.
 *
 * @package AffiliateWP
 * @since 1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements admin settings fields and relevant actions.
 *
 * @since 1.0.0
 */
class AffiliateWP_Starting_Affiliate_ID_Admin {

	private static $instance = false;

	private function __construct() { }

	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new self;

			// Filter the AffiliateWP misc settings
			add_filter( 'affwp_settings_misc', array( self::$instance, 'add_starting_affiliate_id_setting' ) );

			// Set the affiliate ID when the minimum ID is updated.
			add_action( 'pre_update_option_affwp_settings', array( self::$instance, 'sync_affiliate_id', ), 10, 3 );
		}
	}

	/**
	 * Synchronizes the starting affiliate id value with the auto increment value in the affiliate table.
	 *
	 * Performs checks to confirm that the new value is valid, and updates the auto increment if so.
	 * Otherwise, this option will set the starting affiliate id to the minimum possible auto_increment value.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $new_settings array of new settings passed by update_option.
	 * @param array  $old_settings array of previous settings passed by update_option.
	 * @param string $option       Name of the option being updated.
	 *
	 * @return array of filtered $new_settings value.
	 */
	public function sync_affiliate_id( $new_settings, $old_settings, $option ) {
		$new_auto_increment = isset( $new_settings['starting_affiliate_id'] ) ? $new_settings['starting_affiliate_id'] : 0;
		$old_auto_increment = isset( $old_settings['starting_affiliate_id'] ) ? $old_settings['starting_affiliate_id'] : 0;

		// If the value didn't change, bypass this function.
		if ( $new_auto_increment !== $old_auto_increment ) {
			$newest_affiliate = $this->get_newest_affiliate_id();
			$auto_increment   = $newest_affiliate > $new_auto_increment ? $newest_affiliate + 1 : $new_auto_increment;

			$updated = $this->update_affiliate_id_auto_increment( $auto_increment );

			//reset the option to the minimum auto increment value if something went wrong
			if ( ! $updated || $newest_affiliate > $new_auto_increment ) {
				$new_settings['starting_affiliate_id'] = $newest_affiliate + 1;
			}
		}

		return $new_settings;
	}


	/**
	 * Fetches the newest affiliate from the database.
	 *
	 * @since 1.0.0
	 *
	 * @return int|mixed int affiliate object or field(s). Otherwise returns 0
	 */
	private function get_newest_affiliate_id() {

		$affiliates = affiliate_wp()->affiliates->get_affiliates( array(
				'fields' => 'ids',
				'number' => 1,
				'order'  => 'DESC',
		) );

		return isset( $affiliates[0] ) ? $affiliates[0] : 0;
	}


	/**
	 * Updates the affiliate ID auto increment to the specified value.
	 *
	 * @since 1.0.0
	 *
	 * @param int $auto_increment The auto increment value to set.
	 * @return bool True if update was successful, otherwise false.
	 */
	private function update_affiliate_id_auto_increment( $auto_increment ) {
		global $wpdb;
		$table_name = affiliate_wp()->affiliates->table_name;

		$minimum_id = absint( $auto_increment );

		$sql = "ALTER TABLE {$table_name}
						MODIFY `affiliate_id` bigint(20) NOT NULL AUTO_INCREMENT,
						AUTO_INCREMENT={$minimum_id};";

		$result = $wpdb->query( $sql );

		return false !== $result;
	}

	/**
	 * Adds the starting affiliate ID setting to the AffiliateWP settings page.
	 * @param $settings array of settings provided by AffiliateWP.
	 *
	 * @since 1.0.0
	 *
	 * @return array of filtered settings.
	 */
	public function add_starting_affiliate_id_setting( $settings ) {

		if ( affiliate_wp()->affiliates->count() > 0 ) {
			/* translators: The description used when there are existing affiliates */
			$starting_affiliate_id_desc = __( 'The minimum ID to use for new affiliate registrations. Note: this number can only ever be greater than the ID used for the most recent affiliate.', 'affiliate-wp' );
		} else {
			/* translators: The description used when there are no affiliates yet */
			$starting_affiliate_id_desc = __( 'The starting ID to use once affiliate registrations begin. Note: this number can only ever be greater than the ID used for the most recent affiliate.', 'affiliate-wp' );
		}

		$settings['starting_affiliate_id'] = array(
				'name' => __( 'Starting Affiliate ID', 'affiliate-wp' ),
				'desc' => $starting_affiliate_id_desc,
				'type' => 'number',
				'max'  => 1000000,
				'min'  => affiliate_wp()->affiliates->get_newest_affiliate_id() + 1,
				'step' => 1,
		);

		return $settings;
	}

}