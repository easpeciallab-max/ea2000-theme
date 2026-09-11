<?php
/**
 * Template Name: EA2000 · หน้า How to Install
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	the_post();
}
$ea2000_title = get_the_title();
ea2000_page_hero( 'How to Install', $ea2000_title ? $ea2000_title : 'วิธีติดตั้ง', ea2000_mod( 'install_sub' ) );

$ea2000_line_url = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_has_line = '' !== $ea2000_line_url && '#' !== $ea2000_line_url;
?>

<main id="main">

<section class="section">
	<div class="container container-narrow">

		<?php if ( ea2000_mod( 'install_intro' ) ) : ?>
			<p class="lead"><?php echo esc_html( ea2000_mod( 'install_intro' ) ); ?></p>
		<?php endif; ?>

		<?php
		$ea2000_reqs = ea2000_lines( ea2000_mod( 'install_req' ) );
		if ( ! empty( $ea2000_reqs ) ) :
			?>
			<aside class="guide-check">
				<h2 class="guide-check-title">สิ่งที่ต้องเตรียม</h2>
				<ul class="mono-marks">
					<?php foreach ( $ea2000_reqs as $ea2000_req ) : ?>
						<li><span class="mark mono keep-case" aria-hidden="true">[x]</span><span><?php echo esc_html( $ea2000_req ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</aside>
		<?php endif; ?>

	</div>

	<div class="container">
		<ol class="guide-steps">
			<?php
			for ( $i = 1; $i <= 6; $i++ ) :
				$g_title = ea2000_mod( 'inst_step' . $i . '_title' );
				$g_desc  = ea2000_mod( 'inst_step' . $i . '_desc' );
				if ( ! $g_title && ! $g_desc ) {
					continue;
				}
				?>
				<li class="guide-step">
					<div class="guide-step-body">
						<span class="guide-step-num mono keep-case"><?php echo esc_html( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="guide-step-text">
							<h2><?php echo esc_html( $g_title ); ?></h2>
							<p><?php echo esc_html( $g_desc ); ?></p>
						</div>
					</div>
					<div class="guide-step-media">
						<?php ea2000_media_slot( 'inst_step' . $i, 1280, 720 ); ?>
					</div>
				</li>
			<?php endfor; ?>
		</ol>
	</div>

	<?php if ( ea2000_mod( 'install_note' ) ) : ?>
		<div class="container container-narrow">
			<p class="sec-note reveal"><?php echo esc_html( ea2000_mod( 'install_note' ) ); ?></p>
		</div>
	<?php endif; ?>
</section>

<?php ea2000_page_sections( 'installdoc', 8 ); ?>

<?php if ( $ea2000_has_line ) : ?>
	<?php ea2000_line_cta( ea2000_mod( 'install_cta_title' ), ea2000_mod( 'install_cta_text' ) ); ?>
<?php endif; ?>

</main>

<?php
get_footer();
