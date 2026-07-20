<?php
/**
 * Plugin Name: Bonsai Code Injector
 * Plugin URI:  https://bonsaidigitalcollective.co.uk/
 * Description: Lets an administrator paste tracking / verification code (GA4, Google Tag Manager, Meta Pixel, etc.) into the site <head> and immediately after <body> — without editing theme files.
 * Version:     1.0.0
 * Author:      The Bonsai Digital Collective
 * Author URI:  https://bonsaidigitalcollective.co.uk/
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Text Domain: bonsai-code-injector
 */

defined( 'ABSPATH' ) || exit;

/*
|--------------------------------------------------------------------------
| Plugin Update Checker (via Composer)
|--------------------------------------------------------------------------
*/
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$bci_update_checker = PucFactory::buildUpdateChecker(
	'https://github.com/The-Bonsai-Digital-Collective/bonsai-code-injector',
	__FILE__,
	'bonsai-code-injector'
);

$bci_update_checker->setBranch( 'main' );
$bci_update_checker->setUpdateCheckInterval( 6 );
$bci_update_checker->getVcsApi()->enableReleaseAssets();

define( 'BCI_VERSION', '1.0.0' );
define( 'BCI_OPTION_GROUP', 'bci_settings_group' );
define( 'BCI_PAGE_SLUG', 'bonsai-code-injector' );
define( 'BCI_CAPABILITY', apply_filters( 'bonsai_code_injector_capability', 'manage_options' ) );

/**
 * NOTE ON ESCAPING (read before "fixing" this):
 * Every other Bonsai project escapes all output. This plugin is the deliberate
 * exception, by design — its entire purpose is to let a trusted admin inject raw
 * <script>/<noscript> tracking code (GA4, GTM, Meta Pixel). esc_html()/wp_kses_post()
 * would strip or mangle exactly the tags this plugin exists to output.
 *
 * The trade-off is contained by:
 *  - Capability gate (BCI_CAPABILITY, defaults to manage_options) on the settings
 *    page, the save handler (via the Settings API) and the sanitize callback.
 *  - No public-facing input — only an authenticated admin can ever set this value.
 *  - Textarea *display* in wp-admin still uses esc_textarea() so the raw code can't
 *    break out of the form field.
 */

// ---------------------------------------------------------------------------
// Settings registration
// ---------------------------------------------------------------------------

add_action( 'admin_init', 'bci_register_settings' );
function bci_register_settings() {
	register_setting(
		BCI_OPTION_GROUP,
		'bci_header_code',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'bci_sanitize_code',
			'default'           => '',
		)
	);

	register_setting(
		BCI_OPTION_GROUP,
		'bci_body_code',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'bci_sanitize_code',
			'default'           => '',
		)
	);

	add_settings_section(
		'bci_main_section',
		'',
		'__return_false',
		BCI_PAGE_SLUG
	);

	add_settings_field(
		'bci_header_code',
		__( 'Header Code', 'bonsai-code-injector' ),
		'bci_render_header_field',
		BCI_PAGE_SLUG,
		'bci_main_section'
	);

	add_settings_field(
		'bci_body_code',
		__( 'Body Code', 'bonsai-code-injector' ),
		'bci_render_body_field',
		BCI_PAGE_SLUG,
		'bci_main_section'
	);
}

/**
 * Trim only. Deliberately does not strip tags — see the escaping note above.
 */
function bci_sanitize_code( $value ) {
	if ( ! current_user_can( BCI_CAPABILITY ) ) {
		return '';
	}

	if ( ! is_string( $value ) ) {
		return '';
	}

	return trim( $value );
}

// ---------------------------------------------------------------------------
// Admin menu + page
// ---------------------------------------------------------------------------

add_action( 'admin_menu', 'bci_add_settings_page' );
function bci_add_settings_page() {
	add_options_page(
		__( 'Bonsai Code Injector', 'bonsai-code-injector' ),
		__( 'Code Injector', 'bonsai-code-injector' ),
		BCI_CAPABILITY,
		BCI_PAGE_SLUG,
		'bci_render_settings_page'
	);
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bci_add_settings_link' );
function bci_add_settings_link( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=' . BCI_PAGE_SLUG ) ) . '">' . esc_html__( 'Settings', 'bonsai-code-injector' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

function bci_render_header_field() {
	$value = get_option( 'bci_header_code', '' );
	?>
	<textarea
		name="bci_header_code"
		id="bci_header_code"
		class="large-text code"
		rows="10"
		placeholder="<?php esc_attr_e( 'e.g. GA4 gtag.js snippet, or the GTM <script> block', 'bonsai-code-injector' ); ?>"
	><?php echo esc_textarea( $value ); ?></textarea>
	<p class="description">
		<?php esc_html_e( 'Printed as high as possible inside <head> on every front-end page. Use for the GA4 gtag.js snippet, the Google Tag Manager <script> block, verification meta tags, etc.', 'bonsai-code-injector' ); ?>
	</p>
	<?php
}

function bci_render_body_field() {
	$value = get_option( 'bci_body_code', '' );
	?>
	<textarea
		name="bci_body_code"
		id="bci_body_code"
		class="large-text code"
		rows="6"
		placeholder="<?php esc_attr_e( 'e.g. the GTM <noscript> snippet', 'bonsai-code-injector' ); ?>"
	><?php echo esc_textarea( $value ); ?></textarea>
	<p class="description">
		<?php
		printf(
			/* translators: %s: wp_body_open */
			esc_html__( 'Printed immediately after the opening %s tag via the wp_body_open hook. Use for the Google Tag Manager <noscript> snippet. Requires the theme to call %s — Bonsai base themes do this by default.', 'bonsai-code-injector' ),
			'<code>&lt;body&gt;</code>',
			'<code>wp_body_open()</code>'
		);
		?>
	</p>
	<?php
}

function bci_render_settings_page() {
	if ( ! current_user_can( BCI_CAPABILITY ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'bonsai-code-injector' ) );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Bonsai Code Injector', 'bonsai-code-injector' ); ?></h1>
		<p><?php esc_html_e( 'Paste tracking or verification code below. Only add code from sources you trust — it will run, unescaped, on every front-end page.', 'bonsai-code-injector' ); ?></p>
		<form method="post" action="options.php">
			<?php
			settings_fields( BCI_OPTION_GROUP );
			do_settings_sections( BCI_PAGE_SLUG );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

// ---------------------------------------------------------------------------
// Front-end output
// ---------------------------------------------------------------------------

add_action( 'wp_head', 'bci_output_header_code', 1 );
function bci_output_header_code() {
	$code = get_option( 'bci_header_code', '' );

	if ( '' === $code ) {
		return;
	}

	echo "\n<!-- Bonsai Code Injector: Header -->\n";
	echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Intentional; see escaping note at top of file.
	echo "\n<!-- / Bonsai Code Injector: Header -->\n";
}

add_action( 'wp_body_open', 'bci_output_body_code' );
function bci_output_body_code() {
	$code = get_option( 'bci_body_code', '' );

	if ( '' === $code ) {
		return;
	}

	echo "\n<!-- Bonsai Code Injector: Body -->\n";
	echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Intentional; see escaping note at top of file.
	echo "\n<!-- / Bonsai Code Injector: Body -->\n";
}

// ---------------------------------------------------------------------------
// Uninstall cleanup
// ---------------------------------------------------------------------------

register_uninstall_hook( __FILE__, 'bci_uninstall' );
function bci_uninstall() {
	delete_option( 'bci_header_code' );
	delete_option( 'bci_body_code' );
}
