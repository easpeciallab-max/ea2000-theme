<?php
/**
 * Template Name: EA2000 · หน้า Risk Disclosure
 *
 * @package ea2000
 */

get_header();

if ( have_posts() ) {
	the_post();
}
$ea2000_title = get_the_title();
ea2000_page_hero( 'Risk Disclosure', $ea2000_title ? $ea2000_title : 'คำเตือนความเสี่ยง', ea2000_mod( 'riskpage_sub' ) );

$ea2000_line_url = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_has_line = '' !== $ea2000_line_url && '#' !== $ea2000_line_url;
?>

<main id="main">

<section class="section">
	<div class="container container-narrow">

		<div class="riskdoc-intro reveal">
			<?php echo ea2000_icon( 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p><?php echo esc_html( ea2000_mod( 'riskpage_intro' ) ); ?></p>
		</div>

		<?php if ( ea2000_mod( 'riskpage_image' ) ) : ?>
			<figure class="riskdoc-figure reveal">
				<img src="<?php echo esc_url( ea2000_mod( 'riskpage_image' ) ); ?>" alt="<?php echo esc_attr( ea2000_mod( 'riskpage_image_caption' ) ); ?>" loading="lazy">
				<?php if ( ea2000_mod( 'riskpage_image_caption' ) ) : ?>
					<figcaption><?php echo esc_html( ea2000_mod( 'riskpage_image_caption' ) ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="riskdoc">
			<?php
			$ea2000_n = 0;
			for ( $i = 1; $i <= 6; $i++ ) :
				$b_title = ea2000_mod( 'rp_block' . $i . '_title' );
				$b_text  = ea2000_mod( 'rp_block' . $i . '_text' );
				if ( ! $b_title && ! $b_text ) {
					continue;
				}
				$ea2000_n++;
				?>
				<article class="riskdoc-block reveal">
					<h2><span class="riskdoc-num"><?php echo esc_html( str_pad( (string) $ea2000_n, 2, '0', STR_PAD_LEFT ) ); ?></span><?php echo esc_html( $b_title ); ?></h2>
					<p><?php echo nl2br( esc_html( $b_text ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				</article>
			<?php endfor; ?>
		</div>

		<?php /* วันที่ปรับปรุงที่ยังเป็น placeholder (ขึ้นต้นด้วย "ระบุ") จะไม่แสดง */ ?>
		<?php if ( ! ea2000_is_placeholder( ea2000_mod( 'riskpage_updated' ) ) ) : ?>
			<p class="riskdoc-updated reveal"><?php echo esc_html( ea2000_mod( 'riskpage_updated' ) ); ?></p>
		<?php endif; ?>

	</div>
</section>

<?php if ( $ea2000_has_line ) : ?>
	<?php ea2000_line_cta( 'มีคำถามเรื่องความเสี่ยง?', 'ทักมาสอบถามทีมงานก่อนตัดสินใจใช้งานได้ทาง LINE' ); ?>
<?php endif; ?>

</main>

<?php
get_footer();
