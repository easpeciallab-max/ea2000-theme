<?php
/**
 * Footer
 *
 * @package ea2000
 */

$ea2000_line  = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_line  = in_array( $ea2000_line, array( '', '#' ), true ) ? '' : $ea2000_line; // ยังไม่กรอก LINE OA: ซ่อนทุกปุ่ม LINE
$ea2000_intro = ea2000_lines( ea2000_mod( 'footer_tagline' ) );
$ea2000_prep  = ea2000_lines( ea2000_mod( 'footer_prep_items' ) );
$ea2000_mobile_nav = array(
	array(
		'label' => ea2000_mod( 'mobile_nav_home_label' ),
		'url'   => ea2000_link_url( ea2000_mod( 'mobile_nav_home_url' ) ),
		'icon'  => 'home',
	),
	array(
		'label' => ea2000_mod( 'mobile_nav_test_label' ),
		'url'   => ea2000_link_url( ea2000_mod( 'mobile_nav_test_url' ) ),
		'icon'  => 'chart',
	),
	array(
		'label' => ea2000_mod( 'mobile_nav_price_label' ),
		'url'   => ea2000_link_url( ea2000_mod( 'mobile_nav_price_url' ) ),
		'icon'  => 'tag',
	),
	array(
		'label' => ea2000_mod( 'mobile_nav_install_label' ),
		'url'   => ea2000_link_url( ea2000_mod( 'mobile_nav_install_url' ) ),
		'icon'  => 'download',
	),
);
if ( $ea2000_line ) {
	$ea2000_mobile_nav[] = array(
		'label'        => ea2000_mod( 'mobile_nav_line_label' ),
		'url'          => $ea2000_line,
		'icon'         => 'line',
		'is_action'    => true,
		'target_blank' => true,
	);
}
?>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) : ?>
<footer class="site-footer" id="contact">
	<div class="container">

		<div class="footer-cta">
			<div>
				<p class="footer-kicker"><?php echo esc_html( ea2000_mod( 'footer_kicker' ) ); ?></p>
				<h2><?php echo esc_html( ea2000_mod( 'footer_cta_title' ) ); ?></h2>
				<p><?php echo esc_html( ea2000_mod( 'footer_cta_text' ) ); ?></p>
			</div>
			<?php if ( $ea2000_line ) : ?>
			<a class="footer-primary" href="<?php echo esc_url( $ea2000_line ); ?>" target="_blank" rel="noopener">
				<?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( ea2000_mod( 'footer_line_text' ) ); ?></span>
			</a>
			<?php endif; ?>
		</div>

		<div class="footer-main">

			<div class="footer-brand">
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img class="brand-logo" src="<?php echo esc_url( ea2000_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="64" height="64">
					<span class="brand-name"><?php echo esc_html( ea2000_mod( 'brand_name' ) ); ?><small><?php echo esc_html( ea2000_mod( 'brand_tagline' ) ); ?></small></span>
				</a>
				<?php if ( $ea2000_intro ) : ?>
				<div class="footer-tagline">
					<?php foreach ( $ea2000_intro as $ea2000_paragraph ) : ?>
					<p><?php echo esc_html( $ea2000_paragraph ); ?></p>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php ea2000_social_row( 'footer-social' ); ?>
			</div>

			<div class="footer-prep">
				<h3 class="footer-head"><?php echo esc_html( ea2000_mod( 'footer_prep_title' ) ); ?></h3>
				<p><?php echo esc_html( ea2000_mod( 'footer_prep_text' ) ); ?></p>

				<?php if ( $ea2000_prep ) : ?>
				<ul class="footer-prep-list">
					<?php foreach ( $ea2000_prep as $ea2000_item ) : ?>
					<li>
						<?php echo ea2000_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( $ea2000_item ); ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>

		</div>

		<div class="footer-bottom">
			<p class="footer-copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> สงวนลิขสิทธิ์</p>
			<nav class="footer-legal" aria-label="ลิงก์ทางกฎหมาย">
				<?php
				foreach ( array(
					'about'          => 'เกี่ยวกับเรา',
					'privacy-policy' => 'นโยบายความเป็นส่วนตัว',
					'terms'          => 'เงื่อนไขการใช้บริการ',
				) as $ea2000_slug => $ea2000_label ) :
					$ea2000_legal_page = get_page_by_path( $ea2000_slug );
					if ( $ea2000_legal_page ) :
						?>
						<a href="<?php echo esc_url( get_permalink( $ea2000_legal_page ) ); ?>"><?php echo esc_html( $ea2000_label ); ?></a>
						<?php
					endif;
				endforeach;
				?>
				<?php /* slug หน้าคำเตือนความเสี่ยงตามแผนเพจ EA2000 (risk-disclosure) · ยังไม่มี setting เฉพาะสำหรับ URL นี้ */ ?>
				<a href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>"><?php echo esc_html( ea2000_mod( 'footer_risk_link' ) ); ?></a>
			</nav>
		</div>

	</div>
</footer>
<?php endif; ?>

<?php if ( ea2000_mod( 'show_mobile_nav' ) ) : ?>
<nav class="mobile-app-nav" aria-label="เมนูลัดมือถือ">
	<?php foreach ( $ea2000_mobile_nav as $ea2000_item ) : ?>
	<a class="mobile-app-nav-item<?php echo ! empty( $ea2000_item['is_action'] ) ? ' is-action' : ''; ?>" href="<?php echo esc_url( $ea2000_item['url'] ); ?>"<?php echo ! empty( $ea2000_item['target_blank'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
		<span class="mobile-app-nav-icon"><?php echo ea2000_icon( $ea2000_item['icon'], 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span><?php echo esc_html( $ea2000_item['label'] ); ?></span>
	</a>
	<?php endforeach; ?>
</nav>
<?php endif; ?>

<?php if ( $ea2000_line && ea2000_mod( 'show_float_line' ) ) : ?>
<a class="float-line" href="<?php echo esc_url( $ea2000_line ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ea2000_mod( 'float_line_text' ) ); ?>">
	<?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<span><?php echo esc_html( ea2000_mod( 'float_line_text' ) ); ?></span>
</a>
<?php endif; ?>

<?php if ( $ea2000_line && ! ea2000_mod( 'show_float_line' ) ) : ?>
<a class="line-fab" href="<?php echo esc_url( $ea2000_line ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ea2000_mod( 'float_line_text' ) ); ?>">
	<?php echo ea2000_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</a>
<?php endif; ?>

<?php if ( ea2000_mod( 'show_cookie_consent' ) ) : ?>
<div class="cookie-consent" role="region" aria-label="ความยินยอมการใช้คุกกี้">
	<button type="button" class="cookie-consent-close" aria-label="ปิด"><?php echo ea2000_icon( 'x', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	<p class="cookie-consent-text">
		<?php echo esc_html( ea2000_mod( 'cookie_consent_text' ) ); ?>
		<?php
		$ea2000_privacy = get_page_by_path( 'privacy-policy' );
		if ( $ea2000_privacy ) :
			?>
			<a class="cookie-consent-link" href="<?php echo esc_url( get_permalink( $ea2000_privacy ) ); ?>">อ่านนโยบาย</a>
		<?php endif; ?>
	</p>
	<div class="cookie-consent-actions">
		<button type="button" class="btn btn-ghost btn-sm cookie-decline">ปฏิเสธ</button>
		<button type="button" class="btn btn-fire btn-sm cookie-accept">ยอมรับ</button>
	</div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
