<?php
/**
 * Template Name: EA2000 · หน้าเครื่องมือคำนวณ
 *
 * เครื่องคำนวณขนาดออเดอร์ และตารางขาดทุนกับกำไรที่ต้องได้คืน
 * คำนวณในเบราว์เซอร์ทั้งหมด ไม่ส่งค่าที่ผู้ใช้กรอกไปที่ไหน
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	the_post();
}
$ea2000_title = get_the_title();
ea2000_page_hero( ea2000_mod( 'tools_kicker' ), $ea2000_title ? $ea2000_title : 'เครื่องมือคำนวณ', ea2000_mod( 'tools_sub' ) );

$ea2000_line_url = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_has_line = '' !== $ea2000_line_url && '#' !== $ea2000_line_url;

/* แถวของตาราง drawdown · คำนวณสด ไม่ได้ hardcode ตัวเลขไว้ */
$ea2000_dd_rows = array( 5, 10, 20, 30, 40, 50, 60, 70 );
?>

<main id="main">

<section class="section">
	<div class="container">

		<div class="tools-grid">

			<!-- เครื่องมือที่ 1 · ขนาดออเดอร์ -->
			<section class="tool-card" aria-labelledby="lot-title">
				<p class="tool-step">01</p>
				<h2 class="tool-title" id="lot-title"><?php echo esc_html( ea2000_mod( 'tools_lot_title' ) ); ?></h2>
				<p class="tool-lead"><?php echo esc_html( ea2000_mod( 'tools_lot_intro' ) ); ?></p>

				<form class="tool-form" id="lot-form" novalidate>
					<label class="tool-field">
						<span class="tool-label"><?php echo esc_html( ea2000_mod( 'tools_lot_f1' ) ); ?></span>
						<input type="number" id="lot-balance" inputmode="decimal" step="any" min="0" value="1000" autocomplete="off">
					</label>
					<label class="tool-field">
						<span class="tool-label"><?php echo esc_html( ea2000_mod( 'tools_lot_f2' ) ); ?></span>
						<input type="number" id="lot-risk" inputmode="decimal" step="any" min="0" max="100" value="1" autocomplete="off">
					</label>
					<label class="tool-field">
						<span class="tool-label"><?php echo esc_html( ea2000_mod( 'tools_lot_f3' ) ); ?></span>
						<input type="number" id="lot-stop" inputmode="decimal" step="any" min="0" value="300" autocomplete="off">
					</label>
					<label class="tool-field">
						<span class="tool-label"><?php echo esc_html( ea2000_mod( 'tools_lot_f4' ) ); ?></span>
						<input type="number" id="lot-value" inputmode="decimal" step="any" min="0" value="1" autocomplete="off">
					</label>
				</form>

				<output class="tool-out" id="lot-out" for="lot-balance lot-risk lot-stop lot-value" aria-live="polite">
					<span class="tool-out-label"><?php echo esc_html( ea2000_mod( 'tools_lot_out' ) ); ?></span>
					<strong class="tool-out-value" id="lot-result">0.03</strong>
					<span class="tool-out-sub" id="lot-money" data-template="<?php echo esc_attr( ea2000_mod( 'tools_lot_money' ) ); ?>"></span>
				</output>

				<p class="tool-note"><?php echo esc_html( ea2000_mod( 'tools_lot_note' ) ); ?></p>
			</section>

			<!-- เครื่องมือที่ 2 · ขาดทุนแล้วต้องได้คืนเท่าไร -->
			<section class="tool-card" aria-labelledby="dd-title">
				<p class="tool-step">02</p>
				<h2 class="tool-title" id="dd-title"><?php echo esc_html( ea2000_mod( 'tools_dd_title' ) ); ?></h2>
				<p class="tool-lead"><?php echo esc_html( ea2000_mod( 'tools_dd_intro' ) ); ?></p>

				<div class="doc-table-wrap">
					<table class="doc-table">
						<thead>
							<tr>
								<th scope="col"><?php echo esc_html( ea2000_mod( 'tools_dd_col1' ) ); ?></th>
								<th scope="col"><?php echo esc_html( ea2000_mod( 'tools_dd_col2' ) ); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach ( $ea2000_dd_rows as $ea2000_dd ) {
							$ea2000_back = ( $ea2000_dd / ( 100 - $ea2000_dd ) ) * 100;
							printf(
								'<tr><th scope="row" data-label="%1$s">%2$s%%</th><td data-label="%3$s"><b>%4$s%%</b></td></tr>',
								esc_attr( ea2000_mod( 'tools_dd_col1' ) ),
								esc_html( (string) $ea2000_dd ),
								esc_attr( ea2000_mod( 'tools_dd_col2' ) ),
								esc_html( number_format( $ea2000_back, $ea2000_back >= 100 ? 0 : 1 ) )
							);
						}
						?>
						</tbody>
					</table>
				</div>

				<p class="tool-note"><?php echo esc_html( ea2000_mod( 'tools_dd_note' ) ); ?></p>
			</section>

		</div>

		<?php ea2000_page_sections( 'toolsdoc', 4 ); ?>

	</div>
</section>

<?php if ( $ea2000_has_line ) : ?>
	<?php ea2000_line_cta( ea2000_mod( 'tools_cta_title' ), ea2000_mod( 'tools_cta_text' ) ); ?>
<?php endif; ?>

</main>

<?php
get_footer();
