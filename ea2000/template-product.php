<?php
/**
 * Template Name: EA2000 · หน้าสินค้า
 *
 * หน้ารายละเอียดของตัว EA2000 เอง: สเปก สิ่งที่ได้รับ ข้อจำกัด และสิ่งที่ไม่รับประกัน
 * ไม่มีตัวเลขผลการเทรด ไม่ระบุกลยุทธ์
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	the_post();
}
$ea2000_title = get_the_title();
ea2000_page_hero( ea2000_mod( 'prod_kicker' ), $ea2000_title ? $ea2000_title : 'EA2000 สำหรับ MetaTrader 5', ea2000_mod( 'prod_sub' ) );

$ea2000_line_url = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_has_line = '' !== $ea2000_line_url && '#' !== $ea2000_line_url;

/* ตารางสเปก · บรรทัดละ "หัวข้อ | ค่า" ว่างแล้วข้าม */
$ea2000_spec = array();
foreach ( ea2000_lines( ea2000_mod( 'prod_spec' ) ) as $ea2000_row ) {
	$ea2000_parts = array_map( 'trim', explode( '|', $ea2000_row, 2 ) );
	if ( '' !== $ea2000_parts[0] && isset( $ea2000_parts[1] ) && '' !== $ea2000_parts[1] ) {
		$ea2000_spec[] = $ea2000_parts;
	}
}

/* สิ่งที่ได้รับ 3 ช่อง */
$ea2000_gets = array();
for ( $ea2000_i = 1; $ea2000_i <= 3; $ea2000_i++ ) {
	$ea2000_gt = trim( (string) ea2000_mod( 'prod_get' . $ea2000_i . '_title' ) );
	$ea2000_gd = trim( (string) ea2000_mod( 'prod_get' . $ea2000_i . '_desc' ) );
	if ( '' !== $ea2000_gt ) {
		$ea2000_gets[] = array( $ea2000_gt, $ea2000_gd, (string) ea2000_mod( 'prod_get' . $ea2000_i . '_icon' ) );
	}
}
?>

<main id="main">

<section class="section">
	<div class="container">

		<div class="prod-top">
			<?php ea2000_media_slot( 'prod', 1000, 1000 ); ?>

			<div class="prod-spec">
				<?php if ( '' !== trim( (string) ea2000_mod( 'prod_version' ) ) ) : ?>
					<p class="prod-version keep-case"><?php echo esc_html( ea2000_mod( 'prod_version' ) ); ?></p>
				<?php endif; ?>

				<h2><?php echo esc_html( ea2000_mod( 'prod_spec_title' ) ); ?></h2>

				<?php if ( ! empty( $ea2000_spec ) ) : ?>
				<div class="doc-table-wrap">
					<table class="doc-table">
						<tbody>
						<?php foreach ( $ea2000_spec as $ea2000_row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $ea2000_row[0] ); ?></th>
								<td data-label="<?php echo esc_attr( $ea2000_row[0] ); ?>"><?php echo esc_html( $ea2000_row[1] ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>

				<div class="prod-actions">
					<a class="btn btn-fire" href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>"><?php echo esc_html( ea2000_mod( 'prod_btn1' ) ); ?></a>
					<?php if ( $ea2000_has_line ) : ?>
						<a class="btn btn-line" href="<?php echo esc_url( $ea2000_line_url ); ?>" target="_blank" rel="noopener" data-line-pos="product-top"><?php echo esc_html( ea2000_mod( 'prod_btn2' ) ); ?></a>
					<?php else : ?>
						<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/go/' ) ); ?>"><?php echo esc_html( ea2000_mod( 'prod_btn2' ) ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php if ( ! empty( $ea2000_gets ) ) : ?>
		<div class="prod-gets">
			<h2 class="prod-gets-title"><?php echo esc_html( ea2000_mod( 'prod_gets_title' ) ); ?></h2>
			<div class="prod-get-grid">
				<?php foreach ( $ea2000_gets as $ea2000_get ) : ?>
					<article class="prod-get">
						<span class="prod-get-ic"><?php echo ea2000_icon( '' !== $ea2000_get[2] ? $ea2000_get[2] : 'check', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<h3><?php echo esc_html( $ea2000_get[0] ); ?></h3>
						<?php if ( '' !== $ea2000_get[1] ) : ?>
							<p><?php echo esc_html( $ea2000_get[1] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php ea2000_page_sections( 'proddoc', 4 ); ?>

		<?php if ( '' !== trim( (string) ea2000_mod( 'prod_limit_text' ) ) ) : ?>
		<aside class="prod-limit">
			<h2><?php echo esc_html( ea2000_mod( 'prod_limit_title' ) ); ?></h2>
			<p><?php echo esc_html( ea2000_mod( 'prod_limit_text' ) ); ?></p>
		</aside>
		<?php endif; ?>

	</div>
</section>

<?php if ( $ea2000_has_line ) : ?>
	<?php ea2000_line_cta( ea2000_mod( 'prod_cta_title' ), ea2000_mod( 'prod_cta_text' ) ); ?>
<?php endif; ?>

</main>

<?php
get_footer();
