<?php
/**
 * Template Name: EA2000 · หน้า Pricing
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	the_post();
}
$ea2000_title = get_the_title();
ea2000_page_hero( 'Pricing', $ea2000_title ? $ea2000_title : 'แพ็กเกจ & ราคา', ea2000_mod( 'pricing_sub' ) );

$ea2000_line     = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_has_line = '' !== $ea2000_line && '#' !== $ea2000_line; /* เว้นว่าง LINE = ซ่อนปุ่มทัก LINE ทั้งหน้า */
$ea2000_go_page  = get_page_by_path( 'go' );
$ea2000_go_url   = ( $ea2000_go_page && 'publish' === $ea2000_go_page->post_status ) ? get_permalink( $ea2000_go_page ) : ''; /* ยังไม่มี LINE: ปุ่มแพ็กเกจส่งไปหน้าติดต่อ /go/ แทน */
$ea2000_mode = ea2000_mod( 'pricing_mode' );
?>

<main id="main">

<?php /* การ์ดแพ็กเกจ */ ?>
<section class="section">
	<div class="container">
		<div class="sec-head">
			<span class="kicker">Packages</span>
			<h2><?php echo esc_html( ea2000_mod( 'pricing_title' ) ); ?></h2>
			<p><?php echo esc_html( ea2000_mod( 'pricing_subtitle' ) ); ?></p>
		</div>

		<div class="pricing-grid">
			<?php
			for ( $i = 1; $i <= 3; $i++ ) :
				$k_name = ea2000_mod( 'pkg' . $i . '_name' );
				if ( ! $k_name ) {
					continue;
				}
				$k_featured = (bool) ea2000_mod( 'pkg' . $i . '_featured' );
				?>
				<article class="price-card <?php echo $k_featured ? 'is-featured' : ''; ?>">
					<?php if ( $k_featured ) : ?>
						<span class="price-flag">แนะนำ</span>
					<?php endif; ?>
					<h3 class="price-name"><?php echo esc_html( $k_name ); ?></h3>
					<p class="price-tag"><?php echo esc_html( ea2000_mod( 'pkg' . $i . '_tag' ) ); ?></p>
					<?php if ( 'price' === $ea2000_mode ) : ?>
						<div class="price-amount">
							<strong><?php echo esc_html( ea2000_mod( 'pkg' . $i . '_price' ) ); ?></strong>
							<span><?php echo esc_html( ea2000_mod( 'pkg' . $i . '_period' ) ); ?></span>
						</div>
					<?php else : ?>
						<div class="price-amount price-contact">
							<strong>สอบถามราคา</strong>
							<?php if ( $ea2000_has_line ) : ?><span>ทาง LINE</span><?php endif; ?>
						</div>
					<?php endif; ?>
					<ul class="price-feats">
						<?php foreach ( ea2000_lines( ea2000_mod( 'pkg' . $i . '_features' ) ) as $ea2000_item ) : ?>
							<li><?php echo ea2000_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $ea2000_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
					<?php if ( $ea2000_has_line ) : ?>
					<a class="btn <?php echo $k_featured ? 'btn-fire' : 'btn-ghost'; ?> btn-block" href="<?php echo esc_url( $ea2000_line ); ?>" target="_blank" rel="noopener" data-line-pos="pricing" data-line-pkg="<?php echo esc_attr( $k_name ); ?>">
						<?php echo esc_html( ea2000_mod( 'pricing_btn_text' ) ); ?>
					</a>
					<?php elseif ( $ea2000_go_url ) : ?>
					<a class="btn <?php echo $k_featured ? 'btn-fire' : 'btn-ghost'; ?> btn-block" href="<?php echo esc_url( $ea2000_go_url ); ?>">
						<?php echo esc_html( ea2000_mod( 'pricing_btn_text' ) ); ?>
					</a>
					<?php endif; ?>
				</article>
			<?php endfor; ?>
		</div>

		<?php if ( ea2000_mod( 'pricing_note' ) ) : ?>
			<p class="sec-note reveal"><?php echo esc_html( ea2000_mod( 'pricing_note' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php /* ตารางเปรียบเทียบ */ ?>
<?php
$ea2000_rows = ea2000_lines( ea2000_mod( 'compare_rows' ) );
if ( count( $ea2000_rows ) >= 2 ) :
	$ea2000_table = array();
	foreach ( $ea2000_rows as $ea2000_row ) {
		$ea2000_cells = array_map( 'trim', explode( '|', $ea2000_row ) );
		$ea2000_table[] = $ea2000_cells;
	}
	$ea2000_head = array_shift( $ea2000_table );
	?>
	<section class="section section-alt" id="compare">
		<div class="container container-narrow">
			<div class="sec-head reveal">
				<span class="kicker">Compare</span>
				<h2><?php echo esc_html( ea2000_mod( 'compare_title' ) ); ?></h2>
			</div>
			<div class="compare-wrap reveal">
				<table class="compare-table">
					<thead>
						<tr>
							<?php foreach ( $ea2000_head as $idx => $ea2000_cell ) : ?>
								<th class="<?php echo 0 === $idx ? 'compare-rowhead' : ''; ?>"><?php echo esc_html( $ea2000_cell ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $ea2000_table as $ea2000_trow ) : ?>
							<tr>
								<?php
								foreach ( $ea2000_trow as $idx => $ea2000_cell ) :
									$ea2000_cls = 0 === $idx ? 'compare-rowhead' : '';
									if ( '✓' === $ea2000_cell ) {
										$ea2000_cls .= ' cell-yes';
									} elseif ( '✗' === $ea2000_cell || 'x' === strtolower( $ea2000_cell ) ) {
										$ea2000_cls .= ' cell-no';
									}
									?>
									<td class="<?php echo esc_attr( trim( $ea2000_cls ) ); ?>">
										<?php
										if ( '✓' === $ea2000_cell ) {
											echo ea2000_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput
											echo '<span class="sr-only">มี</span>';
										} elseif ( '✗' === $ea2000_cell || 'x' === strtolower( $ea2000_cell ) ) {
											echo ea2000_icon( 'x', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput
											echo '<span class="sr-only">ไม่มี</span>';
										} else {
											echo esc_html( $ea2000_cell );
										}
										?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php ea2000_page_sections( 'pricingdoc', 5 ); ?>

<?php if ( $ea2000_has_line ) : ?>
	<?php ea2000_line_cta( 'ยังไม่แน่ใจว่าแพ็กเกจไหนเหมาะ?', 'ทักมาปรึกษาทีมงานเพื่อเลือกแพ็กเกจที่เหมาะกับทุนและการใช้งานของคุณ' ); ?>
<?php endif; ?>

</main>

<?php
get_footer();
