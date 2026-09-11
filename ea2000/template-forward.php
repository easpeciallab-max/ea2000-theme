<?php
/**
 * Template Name: EA2000 · หน้า Forward Test
 *
 * ค่าที่ขึ้นต้นด้วย "ระบุ" หรือ "เช่น" ถือเป็น placeholder และจะไม่ถูกแสดง (ดู ea2000_is_placeholder)
 *
 * @package ea2000
 */

get_header();

if ( have_posts() ) {
	the_post();
}
$ea2000_title = get_the_title();
$ea2000_sub   = ea2000_mod( 'forward_sub' );
ea2000_page_hero( 'Forward Test', $ea2000_title ? $ea2000_title : 'ผล Forward Test', ea2000_is_placeholder( $ea2000_sub ) ? '' : $ea2000_sub );

$ea2000_stats = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$s_label = ea2000_mod( 'fw_stat' . $i . '_label' );
	$s_value = ea2000_mod( 'fw_stat' . $i . '_value' );
	if ( ea2000_is_placeholder( $s_label ) || ea2000_is_placeholder( $s_value ) ) {
		continue;
	}
	$ea2000_stats[] = array( $s_label, $s_value );
}

$ea2000_link_url   = trim( (string) ea2000_mod( 'forward_link_url' ) );
$ea2000_link_label = trim( (string) ea2000_mod( 'forward_link_label' ) );
$ea2000_has_link   = '' !== $ea2000_link_url && '#' !== $ea2000_link_url && '' !== $ea2000_link_label;

$ea2000_line_url = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_has_line = '' !== $ea2000_line_url && '#' !== $ea2000_line_url;
?>

<main id="main">

<section class="section">
	<div class="container">

		<?php if ( ! ea2000_is_placeholder( ea2000_mod( 'forward_intro' ) ) ) : ?>
			<p class="lead reveal"><?php echo esc_html( ea2000_mod( 'forward_intro' ) ); ?></p>
		<?php endif; ?>

		<?php if ( $ea2000_stats ) : ?>
			<div class="stats-grid reveal">
				<?php foreach ( $ea2000_stats as $ea2000_stat ) : ?>
					<div class="stat">
						<span class="stat-label"><?php echo esc_html( $ea2000_stat[0] ); ?></span>
						<span class="stat-value"><?php echo esc_html( $ea2000_stat[1] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php
		$ea2000_img_cap = ea2000_is_placeholder( ea2000_mod( 'forward_img_caption' ) ) ? '' : ea2000_mod( 'forward_img_caption' );
		ea2000_media_slot( 'forward', 1280, 720, $ea2000_img_cap );
		?>

		<?php if ( ! ea2000_is_placeholder( ea2000_mod( 'forward_note' ) ) ) : ?>
			<p class="sec-note reveal"><?php echo esc_html( ea2000_mod( 'forward_note' ) ); ?></p>
		<?php endif; ?>

		<div class="disclaimer reveal">
			<?php echo ea2000_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p><?php echo esc_html( ea2000_mod( 'forward_disclaimer' ) ); ?></p>
		</div>

	</div>
</section>

<?php ea2000_page_sections( 'forward', 6 ); ?>

<?php if ( $ea2000_has_line ) : ?>
	<?php ea2000_line_cta( ea2000_mod( 'forward_cta_title' ), ea2000_mod( 'forward_cta_text' ) ); ?>
<?php endif; ?>

</main>

<?php
get_footer();
