<?php
/**
 * Template Name: EA2000 · หน้า How to Install
 *
 * @package ea2000
 */

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
			<p class="lead reveal"><?php echo esc_html( ea2000_mod( 'install_intro' ) ); ?></p>
		<?php endif; ?>

		<?php
		$ea2000_reqs = ea2000_lines( ea2000_mod( 'install_req' ) );
		if ( ! empty( $ea2000_reqs ) ) :
			?>
			<div class="req-box reveal">
				<h3><?php echo ea2000_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> สิ่งที่ต้องเตรียม</h3>
				<ul>
					<?php foreach ( $ea2000_reqs as $ea2000_req ) : ?>
						<li><?php echo ea2000_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $ea2000_req ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

	</div>

	<div class="container">
		<ol class="guide">
			<?php
			for ( $i = 1; $i <= 6; $i++ ) :
				$g_title = ea2000_mod( 'inst_step' . $i . '_title' );
				$g_desc  = ea2000_mod( 'inst_step' . $i . '_desc' );
				$g_img   = ea2000_mod( 'inst_step' . $i . '_img' );
				if ( ! $g_title && ! $g_desc ) {
					continue;
				}
				?>
				<li class="guide-step reveal">
					<div class="guide-body">
						<span class="guide-num"><?php echo esc_html( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="guide-text">
							<h3><?php echo esc_html( $g_title ); ?></h3>
							<p><?php echo esc_html( $g_desc ); ?></p>
						</div>
					</div>
					<?php if ( $g_img ) : ?>
						<figure class="guide-img">
							<img src="<?php echo esc_url( $g_img ); ?>" alt="<?php echo esc_attr( $g_title ); ?>" loading="lazy">
						</figure>
					<?php endif; ?>
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
	<?php ea2000_line_cta( 'อยากให้ทีมงานช่วยติดตั้ง?', 'ทักมาทาง LINE ทีมงานช่วยติดตั้งและตั้งค่าให้จนระบบพร้อมใช้งาน' ); ?>
<?php endif; ?>

</main>

<?php
get_footer();
