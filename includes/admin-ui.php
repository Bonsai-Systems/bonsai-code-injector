<?php
/**
 * Bonsai admin UI: shared header and stylesheet for the settings screen.
 *
 * Markup and CSS follow the Bonsai admin design system
 * (assets/bonsai-admin-ui.css, canonical copy in bonsai-seo-geo-checker).
 * Self-contained: nothing here depends on another Bonsai plugin.
 *
 * @package BonsaiCodeInjector
 */

defined( 'ABSPATH' ) || exit;

define( 'BCI_REPO_URL', 'https://github.com/Bonsai-Systems/bonsai-code-injector' );
define( 'BCI_WEBSITE_URL', 'https://bonsaidigitalcollective.co.uk/' );

add_action( 'admin_enqueue_scripts', 'bci_enqueue_admin_ui' );
/**
 * Load the design-system stylesheet on this plugin's settings screen only.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function bci_enqueue_admin_ui( $hook_suffix ) {
	if ( 'settings_page_' . BCI_PAGE_SLUG !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style( 'bci-bonsai-admin-ui', BCI_URL . 'assets/bonsai-admin-ui.css', array(), BCI_VERSION );
}

/**
 * Print the Bonsai page header, followed by the marker core uses to place
 * admin notices, so notices sit below the header.
 *
 * @param string $title Page title (plain text).
 * @param string $lead  Short description (plain text).
 */
function bci_render_admin_header( $title, $lead = '' ) {
	$links = array(
		array(
			'label' => __( 'GitHub', 'bonsai-code-injector' ),
			'url'   => BCI_REPO_URL,
		),
		array(
			'label' => __( 'Changelog', 'bonsai-code-injector' ),
			'url'   => BCI_REPO_URL . '/releases',
		),
		array(
			'label' => __( 'The Bonsai Digital Collective', 'bonsai-code-injector' ),
			'url'   => BCI_WEBSITE_URL,
		),
	);
	?>
	<header class="bonsai-ui-header">
		<div class="bonsai-ui-header__main">
			<img class="bonsai-ui-header__logo" src="<?php echo esc_url( BCI_URL . 'assets/bonsai-avatar.jpg' ); ?>" width="412" height="108" alt="<?php esc_attr_e( 'The Bonsai Digital Collective', 'bonsai-code-injector' ); ?>">
			<h1 class="bonsai-ui-header__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( '' !== $lead ) : ?>
				<p class="bonsai-ui-header__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
			<ul class="bonsai-ui-header__links">
				<?php foreach ( $links as $link ) : ?>
					<li><a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $link['label'] ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'bonsai-code-injector' ); ?></span></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="bonsai-ui-header__meta">
			<span class="bonsai-ui-version">v<?php echo esc_html( BCI_VERSION ); ?></span>
		</div>
	</header>
	<hr class="wp-header-end">
	<?php
}
