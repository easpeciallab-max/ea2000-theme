<?php
/**
 * Header
 *
 * @package ea2000
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
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
			<img class="brand-wordmark" src="<?php echo esc_url( $ea2000_wordmark ); ?>" alt="<?php echo esc_attr( ea2000_mod( 'brand_name' ) ); ?>" width="1400" height="255">
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

		<?php ea2000_language_switcher(); ?>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="เปิด/ปิดเมนู">
			<span></span><span></span><span></span>
		</button>

	</div>
</header>
<?php endif; ?>
