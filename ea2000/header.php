<?php
/**
 * Header
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<script>document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/, 'js' );</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">ข้ามไปยังเนื้อหา</a>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>
<header class="site-header" id="top">
	<div class="container header-inner">

		<?php $ea2000_wordmark = trim( (string) ea2000_mod( 'brand_wordmark' ) ); ?>
		<?php if ( $ea2000_wordmark ) : /* โลโก้แนวนอน */ ?>
		<a class="brand brand--wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img class="brand-wordmark" src="<?php echo esc_url( $ea2000_wordmark ); ?>" alt="<?php echo esc_attr( ea2000_mod( 'brand_name' ) ); ?>" width="660" height="120" fetchpriority="low">
		</a>
		<?php else : /* โลโก้กลม + ชื่อแบรนด์ */ ?>
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img class="brand-logo" src="<?php echo esc_url( ea2000_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="46" height="46">
			<span class="brand-name"><?php echo esc_html( ea2000_mod( 'brand_name' ) ); ?><small><?php echo esc_html( ea2000_mod( 'brand_tagline' ) ); ?></small></span>
		</a>
		<?php endif; ?>

		<nav class="site-nav" id="site-nav" aria-label="เมนูหลัก">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-list',
					'fallback_cb'    => 'ea2000_fallback_menu',
					'depth'          => 2,
				)
			);
			?>
			<?php ea2000_social_row( 'nav-social' ); ?>
		</nav>

		<?php
		$ea2000_dash_href  = trim( (string) ea2000_mod( 'header_dash_url' ) );
		$ea2000_dash_label = trim( (string) ea2000_mod( 'header_dash_label' ) );
		if ( '' !== $ea2000_dash_href && '' !== $ea2000_dash_label ) :
			$ea2000_dash_out = (bool) wp_parse_url( $ea2000_dash_href, PHP_URL_HOST )
				&& wp_parse_url( $ea2000_dash_href, PHP_URL_HOST ) !== wp_parse_url( home_url( '/' ), PHP_URL_HOST );
			?>
		<a class="header-dash" href="<?php echo esc_url( ea2000_link_url( $ea2000_dash_href ) ); ?>"<?php echo $ea2000_dash_out ? ' target="_blank" rel="noopener"' : ''; ?>>
			<?php echo ea2000_icon( 'gauge', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="header-dash-label"><?php echo esc_html( $ea2000_dash_label ); ?></span>
		</a>
		<?php endif; ?>

		<?php ea2000_language_switcher(); ?>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="เปิด/ปิดเมนู">
			<span></span><span></span><span></span>
		</button>

	</div>
</header>
<?php endif; ?>
