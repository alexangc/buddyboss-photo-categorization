<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'PHOTOCAT_admin_enqueue_script' ) ) {
	function PHOTOCAT_admin_enqueue_script() {
		wp_enqueue_style( 'buddyboss-addon-admin-css', plugin_dir_url( __FILE__ ) . 'style.css' );
	}

	add_action( 'admin_enqueue_scripts', 'PHOTOCAT_admin_enqueue_script' );
}


/************ Setting block in 'BuddyBoss > Integrations > Add-on ************/

if ( ! function_exists( 'PHOTOCAT_get_settings_sections' ) ) {
	function PHOTOCAT_get_settings_sections() {

		$settings = array(
			'PHOTOCAT_settings_section' => array(
				'page'  => 'addon',
				'title' => __( 'Photo Categorization', 'buddyboss-photo-categorization' ),
			),
		);

		return (array) apply_filters( 'PHOTOCAT_get_settings_sections', $settings );
	}
}

if ( ! function_exists( 'PHOTOCAT_get_settings_fields_for_section' ) ) {
	function PHOTOCAT_get_settings_fields_for_section( $section_id = '' ) {

		// Bail if section is empty
		if ( empty( $section_id ) ) {
			return false;
		}

		$fields = PHOTOCAT_get_settings_fields();
		$retval = isset( $fields[ $section_id ] ) ? $fields[ $section_id ] : false;

		return (array) apply_filters( 'PHOTOCAT_get_settings_fields_for_section', $retval, $section_id );
	}
}

if ( ! function_exists( 'PHOTOCAT_get_settings_fields' ) ) {
	function PHOTOCAT_get_settings_fields() {

		$fields = array();

		$fields['PHOTOCAT_settings_section'] = array(

			'PHOTOCAT_field' => array(
				'title'             => __( 'Add-on Field', 'buddyboss-photo-categorization' ),
				'callback'          => 'PHOTOCAT_settings_callback_field',
				'sanitize_callback' => 'absint',
				'args'              => array(),
			),

		);

		return (array) apply_filters( 'PHOTOCAT_get_settings_fields', $fields );
	}
}

if ( ! function_exists( 'PHOTOCAT_settings_callback_field' ) ) {
	function PHOTOCAT_settings_callback_field() {
		?>
        <input name="PHOTOCAT_field"
               id="PHOTOCAT_field"
               type="checkbox"
               value="1"
			<?php checked( PHOTOCAT_is_addon_field_enabled() ); ?>
        />
        <label for="PHOTOCAT_field">
			<?php _e( 'Enable this option', 'buddyboss-photo-categorization' ); ?>
        </label>
		<?php
	}
}

if ( ! function_exists( 'PHOTOCAT_is_addon_field_enabled' ) ) {
	function PHOTOCAT_is_addon_field_enabled( $default = 1 ) {
		return (bool) apply_filters( 'PHOTOCAT_is_addon_field_enabled', (bool) get_option( 'PHOTOCAT_field', $default ) );
	}
}

/**************************************** MY PLUGIN INTEGRATION ************************************/

/**
 * Set up the my plugin integration.
 */
function PHOTOCAT_register_integration() {
	require_once dirname( __FILE__ ) . '/integration/buddyboss-integration.php';
	buddypress()->integrations['addon'] = new PHOTOCAT_BuddyBoss_Integration();
}
add_action( 'bp_setup_integrations', 'PHOTOCAT_register_integration' );
