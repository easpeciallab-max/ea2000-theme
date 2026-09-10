<?php
/**
 * Template Name: EA2000 · หน้าคู่มือการใช้งาน
 *
 * ใช้ร่วมกันทุกหน้าคู่มือ เลือกชุดข้อมูลจาก slug ของเพจ (ดู ea2000_guide_map ใน inc/guide-pages.php)
 * ถ้า slug ไม่อยู่ในแผนที่ จะแสดงเนื้อหาของเพจตามปกติแทน เพื่อไม่ให้หน้าว่างเปล่า
 *
 * @package ea2000
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$ea2000_p     = function_exists( 'ea2000_guide_prefix' ) ? ea2000_guide_prefix() : '';
$ea2000_title = get_the_title();
$ea2000_sub   = $ea2000_p ? ea2000_mod( $ea2000_p . '_sub' ) : '';
$ea2000_kick  = $ea2000_p ? ea2000_mod( $ea2000_p . '_kicker' ) : 'คู่มือการใช้งาน';

ea2000_page_hero( $ea2000_kick, $ea2000_title ? $ea2000_title : 'คู่มือการใช้งาน', ea2000_is_placeholder( $ea2000_sub ) ? '' : $ea2000_sub );
?>

<main id="main" class="guide-page">

<?php if ( ! $ea2000_p ) : ?>

	<section class="section">
		<div class="container-narrow">
			<div class="entry-content doc-body"><?php the_content(); ?></div>
		</div>
	</section>

<?php else : ?>

	<section class="section guide-top">
		<div class="container-narrow">

			<?php if ( ea2000_mod( $ea2000_p . '_intro' ) ) : ?>
				<p class="lead"><?php echo esc_html( ea2000_mod( $ea2000_p . '_intro' ) ); ?></p>
			<?php endif; ?>

			<?php if ( ea2000_mod( $ea2000_p . '_quick' ) ) : ?>
				<aside class="guide-quick">
					<p class="guide-quick-label mono keep-case"><span aria-hidden="true">// </span><?php echo esc_html( ea2000_mod( $ea2000_p . '_quick_title' ) ); ?></p>
					<p class="guide-quick-text"><?php echo esc_html( ea2000_mod( $ea2000_p . '_quick' ) ); ?></p>
				</aside>
			<?php endif; ?>

		</div>
	</section>

	<?php
	$ea2000_steps = array();
	for ( $ea2000_i = 1; $ea2000_i <= ea2000_guide_step_count(); $ea2000_i++ ) {
		$ea2000_st = ea2000_mod( $ea2000_p . '_step' . $ea2000_i . '_title' );
		$ea2000_sd = ea2000_mod( $ea2000_p . '_step' . $ea2000_i . '_desc' );
		if ( ! $ea2000_st && ! $ea2000_sd ) {
			continue;
		}
		$ea2000_steps[] = array( $ea2000_i, $ea2000_st, $ea2000_sd );
	}
	?>

	<?php if ( ! empty( $ea2000_steps ) ) : ?>
	<section class="section guide-steps-section" id="steps">
		<div class="container">
			<ol class="guide-steps">
				<?php
				foreach ( $ea2000_steps as $ea2000_step ) :
					$ea2000_key = $ea2000_p . '_step' . $ea2000_step[0];
					/* ขั้นที่ไม่มีทั้งภาพและข้อความบอกให้ใส่ภาพ ให้กินความกว้างเต็มแถว ไม่ทิ้งช่องว่างเปล่า */
					$ea2000_has_media = (bool) ea2000_mod( $ea2000_key . '_img' ) || (bool) ea2000_mod( $ea2000_key . '_img_note' );
					$ea2000_has_cards = 1 === $ea2000_step[0] && function_exists( 'ea2000_guide_store_cards' );
					?>
					<li class="guide-step<?php echo $ea2000_has_media ? '' : ' guide-step--wide'; ?>">
						<div class="guide-step-body">
							<span class="guide-step-num mono keep-case"><?php echo esc_html( str_pad( (string) $ea2000_step[0], 2, '0', STR_PAD_LEFT ) ); ?></span>
							<div class="guide-step-text">
								<h2><?php echo esc_html( $ea2000_step[1] ); ?></h2>
								<p><?php echo esc_html( $ea2000_step[2] ); ?></p>
								<?php
								if ( $ea2000_has_cards ) {
									ea2000_guide_store_cards( $ea2000_p );
								}
								?>
							</div>
						</div>
						<?php if ( $ea2000_has_media ) : ?>
							<div class="guide-step-media">
								<?php ea2000_media_slot( $ea2000_key, 1280, 720 ); ?>
							</div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
	<?php endif; ?>

	<?php
	$ea2000_checks = ea2000_lines( ea2000_mod( $ea2000_p . '_check' ) );
	if ( ! empty( $ea2000_checks ) ) :
		?>
		<section class="section guide-check-section">
			<div class="container-narrow">
				<aside class="guide-check">
					<h2 class="guide-check-title"><?php echo esc_html( ea2000_mod( $ea2000_p . '_check_title' ) ); ?></h2>
					<ul class="mono-marks">
						<?php foreach ( $ea2000_checks as $ea2000_item ) : ?>
							<li><span class="mark mono keep-case" aria-hidden="true">[x]</span><span><?php echo esc_html( $ea2000_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</aside>
			</div>
		</section>
	<?php endif; ?>

	<?php ea2000_page_sections( $ea2000_p, 4 ); ?>

	<?php
	/* คู่มือหน้าอื่น · ลิงก์ภายในให้ผู้อ่านไปต่อได้ และช่วยให้เสิร์ชเอนจินเห็นความสัมพันธ์ของหน้า */
	$ea2000_others = array();
	foreach ( ea2000_guide_map() as $ea2000_slug => $ea2000_pref ) {
		if ( $ea2000_pref === $ea2000_p ) {
			continue;
		}
		$ea2000_page = get_page_by_path( $ea2000_slug );
		if ( ! $ea2000_page || 'publish' !== $ea2000_page->post_status ) {
			continue;
		}
		$ea2000_others[] = array( get_the_title( $ea2000_page ), get_permalink( $ea2000_page ), ea2000_mod( $ea2000_pref . '_sub' ) );
	}
	$ea2000_install_page = get_page_by_path( 'how-to-install' );
	if ( $ea2000_install_page && 'publish' === $ea2000_install_page->post_status ) {
		array_unshift( $ea2000_others, array( get_the_title( $ea2000_install_page ), get_permalink( $ea2000_install_page ), ea2000_mod( 'install_sub' ) ) );
	}
	?>

	<?php if ( ! empty( $ea2000_others ) ) : ?>
	<section class="section guide-more-section" id="guides">
		<div class="container">
			<h2 class="guide-more-title">คู่มืออื่นที่เกี่ยวข้อง</h2>
			<ul class="guide-more">
				<?php foreach ( $ea2000_others as $ea2000_idx => $ea2000_other ) : ?>
					<li class="guide-more-item">
						<a href="<?php echo esc_url( $ea2000_other[1] ); ?>">
							<span class="guide-more-idx mono keep-case"><?php echo esc_html( str_pad( (string) ( $ea2000_idx + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<span class="guide-more-body">
								<span class="guide-more-name"><?php echo esc_html( $ea2000_other[0] ); ?></span>
								<?php if ( $ea2000_other[2] && ! ea2000_is_placeholder( $ea2000_other[2] ) ) : ?>
									<span class="guide-more-sub"><?php echo esc_html( wp_trim_words( $ea2000_other[2], 18, '' ) ); ?></span>
								<?php endif; ?>
							</span>
							<?php echo ea2000_icon( 'arrow', 'icon icon-sm key-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php endif; ?>

<?php endif; ?>

<?php ea2000_line_cta( 'ติดขั้นตอนไหน ทักมาถามได้', 'ทีมงานช่วยดูให้ทีละขั้น บอกด้วยว่าติดตรงไหนและใช้โบรกเกอร์อะไร จะตอบได้ตรงกว่า' ); ?>

</main>

<?php
get_footer();
