<?php
/**
 * Plugin Name: AffiliateWP - Starting Affiliate ID
 * Plugin URI: https://affiliatewp.com/
 * Description: Change the starting affiliate ID of your affiliate network.
 * Author: AffiliateWP, LLC
 * Author URI: https://affiliatewp.com
 * Version: 1.0.0
 * Text Domain: affiliatewp-starting-affiliate-id
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AffiliateWP_Starting_Affiliate_ID' ) ) {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 * @final
	 */
	final class AffiliateWP_Starting_Affiliate_ID {

		/**
		 * Holds the instance.
		 *
		 * Ensures that only one instance of the plugin bootstrap exists in memory at any
		 * one time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @since 1.0.0
		 * @var   \AffiliateWP_Starting_Affiliate_ID
		 * @static
		 */
		private static $instance;

		/**
		 * The version number.
		 *
		 * @since 1.0.0
		 * @var    string
		 */
		private $version = '1.0.0';

		/**
		 * Generates the main bootstrap instance.
		 *
		 * Insures that only one instance of bootstrap exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 *
		 * @return \AffiliateWP_Starting_Affiliate_ID The one true bootstrap instance.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Starting_Affiliate_ID ) ) {

				self::$instance = new AffiliateWP_Starting_Affiliate_ID;

				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->hooks();
				self::$instance->start();
			}

			return self::$instance;
		}

		/**
		 * Throws an error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		protected function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-starting-affiliate-id' ), '1.0' );
		}

		/**
		 * Disables unserialization of the class.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		protected function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-starting-affiliate-id' ), '1.0' );
		}

		/**
		 * Sets up the class.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Resets the instance of the class.
		 *
		 * @since 1.0.0
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Sets up plugin constants.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'AFFWP_SAI_VERSION' ) ) {
				define( 'AFFWP_SAI_VERSION', $this->version );
			}

			// Plugin Folder Path.
			if ( ! defined( 'AFFWP_SAI_PLUGIN_DIR' ) ) {
				define( 'AFFWP_SAI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'AFFWP_SAI_PLUGIN_URL' ) ) {
				define( 'AFFWP_SAI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'AFFWP_SAI_PLUGIN_FILE' ) ) {
				define( 'AFFWP_SAI_PLUGIN_FILE', __FILE__ );
			}
		}

		/**
		 * Loads the add-on language files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory.
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

			/**
			 * Filters the languages directory for the add-on.
			 *
			 * @since 1.0.0
			 *
			 * @param string $lang_dir Language directory.
			 */
			$lang_dir = apply_filters( 'affiliatewp_starting_affiliate_id_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter..
			$locale = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-starting-affiliate-id' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'affiliatewp-starting-affiliate-id', $locale );

			// Setup paths to current locale file.
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-starting-affiliate-id/' . $mofile;

			if ( file_exists( $mofile_global ) ) {

				// Look in global /wp-content/languages/affiliatewp-flag-affiliates/ folder.
				load_textdomain( 'affiliatewp-starting-affiliate-id', $mofile_global );

			} elseif ( file_exists( $mofile_local ) ) {

				// Look in local /wp-content/plugins/affiliatewp-flag-affiliates/languages/ folder.
				load_textdomain( 'affiliatewp-starting-affiliate-id', $mofile_local );

			} else {

				// Load the default language files.
				load_plugin_textdomain( 'affiliatewp-starting-affiliate-id', false, $lang_dir );

			}
		}

		/**
		 * Includes necessary files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function includes() {
			require_once AFFWP_SAI_PLUGIN_DIR . 'includes/class-admin.php';
		}

		/**
		 * Sets up the default hooks and actions.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function hooks() {
			// Plugin meta.
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );
		}

		private function start() {
			if ( is_admin() ) {
				// Include admin settings hooks
				AffiliateWP_Starting_Affiliate_ID_Admin::init();
			}
		}

		/**
		 * Modifies the plugin list table meta links.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $links The current links array.
		 * @param string $file  A specific plugin table entry.
		 * @return array The modified links array.
		 */
		public function plugin_meta( $links, $file ) {

		    if ( $file == plugin_basename( __FILE__ ) ) {

				$url = admin_url( 'admin.php?page=affiliate-wp-add-ons' );

				$plugins_link = array( '<a alt="' . esc_attr__( 'Get more add-ons for AffiliateWP', 'affiliatewp-flag-affiliates' ) . '" href="' . esc_url( $url ) . '">' . __( 'More add-ons', 'affiliatewp-flag-affiliates' ) . '</a>' );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;

		}
	}

	/**
	 * The main function responsible for returning the one true bootstrap instance
	 * to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_starting_affiliate_id = affiliatewp_starting_affiliate_id(); ?>
	 *
	 * @since 1.0.0
	 *
	 * @return \AffiliateWP_Starting_Affiliate_ID The one true bootstrap instance.
	 */
	function affiliatewp_starting_affiliate_id() {
	    if ( ! class_exists( 'Affiliate_WP' ) ) {

	        if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation->run();

	    } else {

	        return AffiliateWP_Starting_Affiliate_ID::instance();

	    }
	}
	add_action( 'plugins_loaded', 'affiliatewp_starting_affiliate_id', 100 );

}
