<?php
/**
 * Front page · EA2000 v3 "Control Room" (สัญญาอยู่ใน docs/home-v2-spec.md)
 *
 * ลำดับบล็อก: boot (hero) · what · pain · how · features · tests · install · pricing · faq · risk
 * ทุกบล็อกหลัง hero คือ "บท" · เลขบทนับเฉพาะบทที่เปิดอยู่ (show_*) ตามลำดับจริง · แถบท้ายเว็บ (footer.php) ไม่ใช่บท
 * บล็อกเดิมที่ตัดออกจากหน้านี้ (highlight, live-strip, team, control-center, gallery, mid-cta, perf, fit, reviews,
 * assurance, explore, blog, cta) ยังมี key ใน ea2000_defaults() เพื่อ REST แต่ไม่พิมพ์อีก · เนื้อหา CTA ย้ายไป footer
 *
 * กฎ: ไม่มีตัวเลขผลเทรด ไม่มีเวลา/ราคาในเทอร์มินัล ไม่มีรีวิว · ทุกข้อความมาจาก setting · escape ทุก output
 * ไม่มี JS ก็อ่านได้ครบ (เทอร์มินัลมี ledger เป็นคู่แฝด · แท็บใช้ radio · FAQ ใช้ details)
 *
 * @package ea2000
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ea2000_has_elementor_content() && ea2000_uses_elementor_page_template() ) :
	?>
	<main id="main" class="elementor-page-shell elementor-page-shell--front">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'elementor-entry' ); ?>>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</main>
	<?php
	get_footer();
	return;
endif;

$ea2000_line = trim( (string) ea2000_mod( 'line_url' ) );
$ea2000_line = in_array( $ea2000_line, array( '', '#' ), true ) ? '' : $ea2000_line; // ยังไม่กรอก LINE OA: ปุ่มหลักชี้หน้า /go/ แทน (ถ้ามี) ไม่มีปุ่มใดชี้ #

$ea2000_go_page = get_page_by_path( 'go' );
$ea2000_go_url  = ( $ea2000_go_page && 'publish' === $ea2000_go_page->post_status ) ? get_permalink( $ea2000_go_page ) : '';

$ea2000_contact_text = trim( (string) ea2000_mod( 'contact_fallback_text' ) );
$ea2000_fig_label    = trim( (string) ea2000_mod( 'fig_label' ) );

/*
 * วงเล็บ HUD 4 มุม · ลูกของทุก .hud-frame (กล่องสินค้า hero, ช่องรูป, โมดูล features)
 */
if ( ! function_exists( 'ea2000_hud_corners' ) ) {
	function ea2000_hud_corners() {
		echo '<span class="hud-c tl" aria-hidden="true"></span><span class="hud-c tr" aria-hidden="true"></span><span class="hud-c bl" aria-hidden="true"></span><span class="hud-c br" aria-hidden="true"></span>';
	}
}

/*
 * ช่องรูป (image slot) v3 · อ่าน {key}_img, {key}_img_alt, {key}_img_note
 * มีรูป = .media-frame ในกรอบ HUD พร้อมคำบรรยาย monospace · ยังว่าง = .img-slot ที่บอกเจ้าของว่าต้องใส่รูปอะไร (เห็นสาธารณะโดยเจตนา)
 * ทั้งสองแบบมี .watch (observer เติม .in) และ .wipe (ปาด clip-path) · $extra_class ใช้กับ .frame ในฟิล์มติดตั้ง
 */
if ( ! function_exists( 'ea2000_front_media' ) ) {
	function ea2000_front_media( $key, $width = 1280, $height = 800, $caption = '', $extra_class = '' ) {
		$src  = trim( (string) ea2000_mod( $key . '_img' ) );
		$alt  = (string) ea2000_mod( $key . '_img_alt' );
		$note = trim( (string) ea2000_mod( $key . '_img_note' ) );
		if ( '' === $note ) {
			$note = $alt;
		}
		$caption = trim( (string) $caption );
		/* คำบรรยายจาก setting ของช่องนั้น ต่อท้ายเลขภาพที่บล็อกส่งมา
		   ใช้กำกับว่าตัวเลขในภาพเป็นตัวอย่าง ไม่ใช่ผลการเทรดจริง จึงต้องแสดงเสมอเมื่อมีค่า
		   (เดิมใช้เฉพาะตอนบล็อกไม่ส่งคำบรรยาย ทุกบล็อกส่งเลขภาพมา ข้อความกำกับจึงไม่เคยขึ้น) */
		$ea2000_setting_caption = trim( (string) ea2000_mod( $key . '_img_caption' ) );
		if ( '' !== $src && '' !== $ea2000_setting_caption ) {
			$caption = '' === $caption ? $ea2000_setting_caption : $caption . ' · ' . $ea2000_setting_caption;
		}
		$extra   = trim( (string) $extra_class );
		$classes = ( '' !== $src ? 'hud-frame media-frame watch wipe' : 'hud-frame img-slot watch wipe' ) . ( '' !== $extra ? ' ' . $extra : '' );

		if ( '' !== $src ) :
			?>
			<figure class="<?php echo esc_attr( $classes ); ?>">
				<?php ea2000_hud_corners(); ?>
				<?php echo ea2000_media_open( $src, ea2000_media_picture( $key, $src, $alt, $width, $height ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside ?>
				<?php if ( '' !== $caption ) : ?>
				<figcaption class="fig mono keep-case"><?php echo esc_html( $caption ); ?></figcaption>
				<?php endif; ?>
			</figure>
			<?php
		else :
			?>
			<figure class="<?php echo esc_attr( $classes ); ?>" aria-label="<?php echo esc_attr( $alt ); ?>">
				<?php ea2000_hud_corners(); ?>
				<span class="img-slot-icon"><?php echo ea2000_icon( 'image', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="img-slot-note"><?php echo esc_html( $note ); ?></span>
				<?php if ( '' !== $caption ) : ?>
				<figcaption class="fig mono keep-case"><?php echo esc_html( $caption ); ?></figcaption>
				<?php endif; ?>
			</figure>
			<?php
		endif;
	}
}

/*
 * หัวบท (สัญญาข้อ 0.4) · เลขบท / จำนวนบททั้งหมด / ป้ายบท (kicker) · h2 · บรรทัดรองละได้เมื่อว่าง
 */
if ( ! function_exists( 'ea2000_front_ch_head' ) ) {
	function ea2000_front_ch_head( $chapter, $total, $title, $sub = '' ) {
		$sub = trim( (string) $sub );
		?>
		<header class="ch-head">
			<p class="ch-index mono keep-case"><span class="ch-n"><?php echo esc_html( $chapter['n'] ); ?></span><span class="ch-sep">/</span><span class="ch-total"><?php echo esc_html( $total ); ?></span><span class="ch-label"><?php echo esc_html( $chapter['label'] ); ?></span></p>
			<h2 class="ch-title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( '' !== $sub ) : ?>
			<p class="ch-sub"><?php echo esc_html( $sub ); ?></p>
			<?php endif; ?>
		</header>
		<?php
	}
}

/*
 * แอตทริบิวต์บทบน <section> (data-chapter / data-chapter-label) · C อ่านค่านี้ไปขับรางเลขบท
 */
if ( ! function_exists( 'ea2000_front_ch_attrs' ) ) {
	function ea2000_front_ch_attrs( $chapter ) {
		echo ' data-chapter="' . esc_attr( $chapter['n'] ) . '" data-chapter-label="' . esc_attr( $chapter['label'] ) . '"';
	}
}

/*
 * ปุ่มติดต่อหลัก (คีย์) · มี LINE = ลิงก์ LINE เปิดแท็บใหม่ · ไม่มี LINE แต่มีหน้า /go/ = ลิงก์ /go/ กับ contact_fallback_text
 * ไม่มีทั้งคู่ = ไม่พิมพ์ปุ่ม (ไม่มีปุ่มใดชี้ #) · data-line-pos บอกตำแหน่งให้ main.js ส่งใน event line_click
 */
if ( ! function_exists( 'ea2000_front_contact_key' ) ) {
	function ea2000_front_contact_key( $line, $go_url, $text, $fallback_text, $pos ) {
		$text          = trim( (string) $text );
		$fallback_text = trim( (string) $fallback_text );
		if ( '' === $text ) {
			$text = $fallback_text;
		}
		if ( '' !== $line ) {
			?>
			<a class="key key-line" href="<?php echo esc_url( $line ); ?>" target="_blank" rel="noopener" data-line-pos="<?php echo esc_attr( $pos ); ?>"><?php echo ea2000_icon( 'line', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $text ); ?></span></a>
			<?php
		} elseif ( '' !== $go_url && '' !== $fallback_text ) {
			?>
			<a class="key key-line" href="<?php echo esc_url( $go_url ); ?>" data-line-pos="<?php echo esc_attr( $pos ); ?>"><?php echo ea2000_icon( 'chat', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fallback_text ); ?></span></a>
			<?php
		}
	}
}

/*
 * บทที่เปิดอยู่ ตามลำดับบนหน้า · เลข 2 หลักและป้ายบท (kicker ของบล็อก) ใช้ทั้งบนราง หัวบท และ data attribute
 */
$ea2000_chapter_defs = array(
	'what'     => array( 'show_what', 'what_kicker' ),
	'pain'     => array( 'show_pain', 'pain_kicker' ),
	'how'      => array( 'show_how', 'how_kicker' ),
	'features' => array( 'show_features', 'features_kicker' ),
	'tests'    => array( 'show_tests', 'tests_kicker' ),
	'install'  => array( 'show_install', 'install_kicker' ),
	'pricing'  => array( 'show_pricing_home', 'pricing_kicker' ),
	'faq'      => array( 'show_faq', 'faq_kicker' ),
	'risk'     => array( 'show_risk', 'risk_kicker' ),
);
$ea2000_chapters = array();
foreach ( $ea2000_chapter_defs as $ea2000_ch_id => $ea2000_ch_def ) {
	if ( ! ea2000_mod( $ea2000_ch_def[0] ) ) {
		continue;
	}
	$ea2000_chapters[ $ea2000_ch_id ] = array(
		'n'     => sprintf( '%02d', count( $ea2000_chapters ) + 1 ),
		'label' => trim( (string) ea2000_mod( $ea2000_ch_def[1] ) ),
	);
}
$ea2000_ch_count = count( $ea2000_chapters );
$ea2000_ch_total = sprintf( '%02d', $ea2000_ch_count );
?>

<main id="main" class="home-v3" data-chapters="<?php echo esc_attr( (string) $ea2000_ch_count ); ?>">

<?php /* ============ รางเลขบท (เดสก์ท็อป) + เส้น progress (จอเล็ก) · ไม่มี JS แสดง 00/NN นิ่ง ============ */ ?>
<?php if ( ea2000_mod( 'show_rail' ) && $ea2000_ch_count > 0 ) : ?>
<nav class="rail" aria-label="บทในหน้านี้" data-total="<?php echo esc_attr( $ea2000_ch_total ); ?>">
	<p class="rail-counter mono keep-case" aria-hidden="true"><span class="rail-n" data-rail-n>00</span><span class="rail-sep">/</span><span class="rail-total"><?php echo esc_html( $ea2000_ch_total ); ?></span></p>
	<span class="rail-track" aria-hidden="true"><i class="rail-fill"></i></span>
	<ol class="rail-list">
		<?php foreach ( $ea2000_chapters as $ea2000_ch_id => $ea2000_ch ) : ?>
		<li><a href="#<?php echo esc_attr( $ea2000_ch_id ); ?>" data-rail-link="<?php echo esc_attr( $ea2000_ch['n'] ); ?>" title="<?php echo esc_attr( $ea2000_ch['label'] ); ?>"><span class="mono"><?php echo esc_html( $ea2000_ch['n'] ); ?></span><span class="sr-only"><?php echo esc_html( $ea2000_ch['label'] ); ?></span></a></li>
		<?php endforeach; ?>
	</ol>
</nav>
<div class="rail-bar" aria-hidden="true"><i class="rail-bar-fill"></i></div>
<?php endif; ?>

<?php /* ============ 00) BOOT · hero (H1 = hero_title + hero_subtitle) ============ */ ?>
<?php if ( ea2000_mod( 'show_hero' ) ) : ?>
<?php
$ea2000_hero_badge = trim( (string) ea2000_mod( 'hero_badge' ) );
$ea2000_hero_sub   = trim( (string) ea2000_mod( 'hero_subtitle' ) );
$ea2000_hero_desc  = trim( (string) ea2000_mod( 'hero_desc' ) );
$ea2000_hero_btn2  = trim( (string) ea2000_mod( 'hero_btn2_text' ) );
$ea2000_hero_note  = trim( (string) ea2000_mod( 'hero_note' ) );
$ea2000_hero_img   = trim( (string) ea2000_mod( 'hero_image' ) );
$ea2000_hero_alt   = (string) ea2000_mod( 'hero_img_alt' );
$ea2000_hud_items  = ea2000_hero_hud_items();
$ea2000_hud_note   = trim( (string) ea2000_mod( 'hero_hud_note' ) );
?>
<section class="boot" id="hero" data-chapter="00" aria-labelledby="boot-title">
	<div class="container boot-grid">
		<div class="boot-copy">
			<?php if ( '' !== $ea2000_hero_badge ) : ?>
			<p class="boot-prefix mono keep-case"><span aria-hidden="true">// </span><?php echo esc_html( $ea2000_hero_badge ); ?></p>
			<?php endif; ?>
			<h1 class="boot-title" id="boot-title"><span class="boot-title-brand keep-case"><?php echo esc_html( ea2000_mod( 'hero_title' ) ); ?></span><?php if ( '' !== $ea2000_hero_sub ) : ?> <span class="boot-title-sub"><?php echo esc_html( $ea2000_hero_sub ); ?></span><?php endif; ?></h1>
			<?php if ( '' !== $ea2000_hero_desc ) : ?>
			<p class="boot-desc"><?php echo esc_html( $ea2000_hero_desc ); ?></p>
			<?php endif; ?>
			<div class="boot-actions">
				<?php ea2000_front_contact_key( $ea2000_line, $ea2000_go_url, ea2000_mod( 'hero_btn1_text' ), $ea2000_contact_text, 'hero' ); ?>
				<?php if ( '' !== $ea2000_hero_btn2 && isset( $ea2000_chapters['how'] ) ) : ?>
				<a class="textlink" href="#how"><?php echo esc_html( $ea2000_hero_btn2 ); ?><?php echo ea2000_icon( 'arrow', 'icon icon-sm key-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endif; ?>
			</div>
			<?php if ( '' === $ea2000_line && '' === $ea2000_go_url && current_user_can( 'customize' ) ) : ?>
			<p class="admin-hint">ยังไม่มีปุ่มติดต่อ: กรอกลิงก์ LINE OA ที่ ปรับแต่ง : แบรนด์และช่องทางติดต่อ หรือเผยแพร่หน้า slug go (ข้อความนี้เห็นเฉพาะแอดมิน)</p>
			<?php endif; ?>
			<?php if ( '' !== $ea2000_hero_note ) : ?>
			<p class="boot-note"><?php echo ea2000_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $ea2000_hero_note ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $ea2000_hud_items ) ) : ?>
			<dl class="hud mono keep-case" data-hud>
				<?php foreach ( $ea2000_hud_items as $ea2000_hud ) : ?>
				<div class="hud-item"><dt><?php echo esc_html( $ea2000_hud['label'] ); ?></dt><dd class="hud-val" data-text="<?php echo esc_attr( $ea2000_hud['value'] ); ?>"><?php echo esc_html( $ea2000_hud['value'] ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
			<?php if ( '' !== $ea2000_hud_note ) : ?>
			<p class="sr-only"><?php echo esc_html( $ea2000_hud_note ); ?></p>
			<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php if ( '' !== $ea2000_hero_img ) : ?>
		<figure class="boot-visual hud-frame" data-hud-frame>
			<?php ea2000_hud_corners(); ?>
			<img src="<?php echo esc_url( $ea2000_hero_img ); ?>" alt="<?php echo esc_attr( $ea2000_hero_alt ); ?>" width="1000" height="1000" loading="eager" decoding="async">
		</figure>
		<?php else : /* ยังไม่มีภาพกล่องสินค้า: ช่องว่างพร้อมคำสั่ง (ห้ามใช้โลโก้กลมแทน) */ ?>
		<figure class="boot-visual hud-frame img-slot" data-hud-frame aria-label="<?php echo esc_attr( $ea2000_hero_alt ); ?>">
			<?php ea2000_hud_corners(); ?>
			<span class="img-slot-icon"><?php echo ea2000_icon( 'image', 'icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="img-slot-note"><?php echo esc_html( ea2000_mod( 'hero_img_note' ) ); ?></span>
		</figure>
		<?php endif; ?>
	</div>
</section>
<?php else : /* ให้มี h1 เสมอ แม้ปิด hero (สำคัญต่อ SEO และ screen reader) */ ?>
<?php $ea2000_h1_text = trim( ea2000_mod( 'hero_title' ) . ' ' . ea2000_mod( 'hero_subtitle' ) ); ?>
<h1 class="sr-only"><?php echo esc_html( '' !== $ea2000_h1_text ? $ea2000_h1_text : get_bloginfo( 'name' ) ); ?></h1>
<?php endif; ?>

<?php /* ============ 01) SYSTEM BRIEF · #what (datasheet + หลักการ · คง #about เป็น anchor สำรอง) ============ */ ?>
<?php if ( isset( $ea2000_chapters['what'] ) ) : ?>
<section class="ch ch-what" id="what"<?php ea2000_front_ch_attrs( $ea2000_chapters['what'] ); ?>>
	<span id="about" class="sec-anchor" aria-hidden="true"></span>
	<div class="container">
		<?php ea2000_front_ch_head( $ea2000_chapters['what'], $ea2000_ch_total, ea2000_mod( 'what_title' ) ); ?>
		<div class="brief-grid">
			<div class="brief-copy">
				<?php
				foreach ( preg_split( '/\n\s*\n/', (string) ea2000_mod( 'what_text' ) ) as $ea2000_para ) {
					$ea2000_para = trim( $ea2000_para );
					if ( '' === $ea2000_para ) {
						continue;
					}
					echo '<p>' . nl2br( esc_html( $ea2000_para ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				$ea2000_sheet_rows = array();
				foreach ( ea2000_lines( ea2000_mod( 'what_points' ) ) as $ea2000_point ) {
					if ( false !== strpos( $ea2000_point, '|' ) ) {
						list( $ea2000_pt_label, $ea2000_pt_text ) = array_map( 'trim', explode( '|', $ea2000_point, 2 ) );
					} else {
						$ea2000_pt_label = '';
						$ea2000_pt_text  = $ea2000_point;
					}
					if ( '' === $ea2000_pt_text ) {
						continue;
					}
					if ( '' === $ea2000_pt_label ) {
						$ea2000_pt_label = sprintf( '%02d', count( $ea2000_sheet_rows ) + 1 );
					}
					$ea2000_sheet_rows[] = array( $ea2000_pt_label, $ea2000_pt_text );
				}
				$ea2000_principle       = trim( (string) ea2000_mod( 'what_principle' ) );
				$ea2000_principle_label = trim( (string) ea2000_mod( 'what_principle_label' ) );
				?>
				<?php if ( ! empty( $ea2000_sheet_rows ) ) : ?>
				<dl class="sheet keep-case">
					<?php foreach ( $ea2000_sheet_rows as $ea2000_row ) : ?>
					<div class="sheet-row"><dt class="mono"><?php echo esc_html( $ea2000_row[0] ); ?></dt><dd><?php echo esc_html( $ea2000_row[1] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
				<?php endif; ?>
				<?php if ( '' !== $ea2000_principle ) : ?>
				<aside class="principle">
					<?php if ( '' !== $ea2000_principle_label ) : ?>
					<p class="principle-label mono keep-case"><span aria-hidden="true">// </span><?php echo esc_html( $ea2000_principle_label ); ?></p>
					<?php endif; ?>
					<p class="principle-text"><?php echo esc_html( $ea2000_principle ); ?></p>
				</aside>
				<?php endif; ?>
			</div>
			<div class="brief-media">
				<?php ea2000_front_media( 'what', 1280, 800, trim( $ea2000_fig_label . ' 01' ) ); ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 02) DIAGNOSTIC LEDGER · #pain (แถวปัญหาถูกขีดฆ่าเมื่อเลื่อนถึง + แถวสรุป) ============ */ ?>
<?php if ( isset( $ea2000_chapters['pain'] ) ) : ?>
<?php
$ea2000_pains = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$ea2000_p_title = trim( (string) ea2000_mod( 'pain' . $i . '_title' ) );
	$ea2000_p_desc  = trim( (string) ea2000_mod( 'pain' . $i . '_desc' ) );
	if ( '' === $ea2000_p_title && '' === $ea2000_p_desc ) {
		continue;
	}
	$ea2000_pains[] = array( $ea2000_p_title, $ea2000_p_desc );
}
$ea2000_resolved_label = trim( (string) ea2000_mod( 'pain_resolved_label' ) );
$ea2000_resolved_text  = trim( (string) ea2000_mod( 'pain_resolved_text' ) );
?>
<section class="ch ch-pain band-surface" id="pain"<?php ea2000_front_ch_attrs( $ea2000_chapters['pain'] ); ?>>
	<div class="container diag-grid">
		<?php ea2000_front_ch_head( $ea2000_chapters['pain'], $ea2000_ch_total, ea2000_mod( 'pain_title' ), ea2000_mod( 'pain_subtitle' ) ); ?>
		<ol class="diag watch">
			<?php foreach ( $ea2000_pains as $ea2000_pi => $ea2000_pain ) : ?>
			<li class="diag-row" style="--i:<?php echo (int) $ea2000_pi; ?>"><span class="diag-idx mono"><?php echo esc_html( sprintf( '%02d', $ea2000_pi + 1 ) ); ?></span><i class="led" aria-hidden="true"></i>
				<div class="diag-body">
					<?php if ( '' !== $ea2000_pain[0] ) : ?>
					<h3 class="diag-title"><span class="strike"><?php echo esc_html( $ea2000_pain[0] ); ?></span></h3>
					<?php endif; ?>
					<?php if ( '' !== $ea2000_pain[1] ) : ?>
					<p><?php echo esc_html( $ea2000_pain[1] ); ?></p>
					<?php endif; ?>
				</div></li>
			<?php endforeach; ?>
			<?php if ( '' !== $ea2000_resolved_text ) : ?>
			<li class="diag-row diag-resolved" style="--i:<?php echo (int) count( $ea2000_pains ); ?>"><span class="diag-idx mono" aria-hidden="true">▸</span><i class="led led-ok" aria-hidden="true"></i>
				<div class="diag-body">
					<?php if ( '' !== $ea2000_resolved_label ) : ?>
					<p class="diag-resolved-label mono"><?php echo esc_html( $ea2000_resolved_label ); ?></p>
					<?php endif; ?>
					<p><?php echo esc_html( $ea2000_resolved_text ); ?></p>
				</div></li>
			<?php endif; ?>
		</ol>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 03) SYSTEM LOG · #how (เทอร์มินัล aria-hidden + ledger คู่แฝดที่มองเห็นเสมอ) ============ */ ?>
<?php if ( isset( $ea2000_chapters['how'] ) ) : ?>
<?php
$ea2000_how_steps = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$ea2000_how_title = trim( (string) ea2000_mod( 'how_step' . $i . '_title' ) );
	$ea2000_how_desc  = trim( (string) ea2000_mod( 'how_step' . $i . '_desc' ) );
	if ( '' === $ea2000_how_title && '' === $ea2000_how_desc ) {
		continue;
	}
	$ea2000_how_steps[] = array(
		'n'     => sprintf( '%02d', count( $ea2000_how_steps ) + 1 ),
		'title' => $ea2000_how_title,
		'desc'  => $ea2000_how_desc,
	);
}

/*
 * บรรทัดของเทอร์มินัล (สัญญาข้อ 2.4): cmd → idx หรือ log → ready
 * ห้ามมี timestamp เวลา ราคา หรือผลเทรด · บรรทัดที่เจ้าของกรอกเองใน how_log_lines ถ้ามีรูปแบบเวลา (hh:mm), % หรือคำว่า กำไร จะถูกข้าม
 */
$ea2000_term_lines = array();
$ea2000_log_cmd    = trim( trim( (string) ea2000_mod( 'how_log_prompt' ) ) . ' ' . trim( (string) ea2000_mod( 'how_log_start' ) ) );
$ea2000_log_ready  = trim( (string) ea2000_mod( 'how_log_ready' ) );
if ( '' !== $ea2000_log_cmd ) {
	$ea2000_term_lines[] = array(
		't' => $ea2000_log_cmd,
		'c' => 'cmd',
	);
}
$ea2000_log_custom = ea2000_lines( ea2000_mod( 'how_log_lines' ) );
if ( empty( $ea2000_log_custom ) ) {
	foreach ( $ea2000_how_steps as $ea2000_step ) {
		if ( '' === $ea2000_step['title'] ) {
			continue;
		}
		$ea2000_term_lines[] = array(
			't' => '[' . $ea2000_step['n'] . '] ' . $ea2000_step['title'],
			'c' => 'idx',
		);
	}
} else {
	foreach ( $ea2000_log_custom as $ea2000_log_line ) {
		if ( preg_match( '/\d{1,2}:\d{2}/', $ea2000_log_line ) || false !== strpos( $ea2000_log_line, '%' ) || false !== strpos( $ea2000_log_line, 'กำไร' ) ) {
			continue;
		}
		$ea2000_term_lines[] = array(
			't' => $ea2000_log_line,
			'c' => 'log',
		);
	}
}
if ( '' !== $ea2000_log_ready ) {
	$ea2000_term_lines[] = array(
		't' => '▸ ' . $ea2000_log_ready,
		'c' => 'ready',
	);
}
$ea2000_show_term = ea2000_mod( 'show_how_log' ) && ! empty( $ea2000_term_lines );
$ea2000_req_title = trim( (string) ea2000_mod( 'how_req_title' ) );
$ea2000_req_items = ea2000_lines( ea2000_mod( 'how_req_items' ) );
?>
<section class="ch ch-how" id="how"<?php ea2000_front_ch_attrs( $ea2000_chapters['how'] ); ?>>
	<div class="container">
		<?php ea2000_front_ch_head( $ea2000_chapters['how'], $ea2000_ch_total, ea2000_mod( 'how_title' ), ea2000_mod( 'how_intro' ) ); ?>
		<div class="how-grid">
			<div class="how-main">
				<?php if ( $ea2000_show_term ) : ?>
				<div class="term keep-case" aria-hidden="true" data-term data-lines="<?php echo esc_attr( wp_json_encode( $ea2000_term_lines, JSON_UNESCAPED_UNICODE ) ); ?>" data-plays="2">
					<div class="term-bar"><i></i><i></i><i></i><span class="term-title"><?php echo esc_html( ea2000_mod( 'how_log_title' ) ); ?></span></div>
					<pre class="term-out mono" data-term-out></pre>
				</div>
				<?php endif; ?>
				<?php if ( ! empty( $ea2000_how_steps ) ) : ?>
				<ol class="ledger how-ledger">
					<?php foreach ( $ea2000_how_steps as $ea2000_step ) : ?>
					<li class="ledger-row"><span class="ledger-idx mono"><?php echo esc_html( $ea2000_step['n'] ); ?></span><div class="ledger-body"><?php if ( '' !== $ea2000_step['title'] ) : ?><h3><?php echo esc_html( $ea2000_step['title'] ); ?></h3><?php endif; ?><?php if ( '' !== $ea2000_step['desc'] ) : ?><p><?php echo esc_html( $ea2000_step['desc'] ); ?></p><?php endif; ?></div></li>
					<?php endforeach; ?>
				</ol>
				<?php endif; ?>
			</div>
			<aside class="how-side">
				<?php ea2000_front_media( 'how', 1280, 800, trim( $ea2000_fig_label . ' 02' ) ); ?>
				<?php if ( '' !== $ea2000_req_title || ! empty( $ea2000_req_items ) ) : ?>
				<div class="req">
					<?php if ( '' !== $ea2000_req_title ) : ?>
					<h3 class="req-title"><?php echo esc_html( $ea2000_req_title ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $ea2000_req_items ) ) : ?>
					<ul class="req-list mono-marks">
						<?php foreach ( $ea2000_req_items as $ea2000_item ) : ?>
						<li><span class="mark mono keep-case" aria-hidden="true">[x]</span><span><?php echo esc_html( $ea2000_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</aside>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 04) MODULES · #features (เซลล์ hairline ไม่มีไอคอน · วงเล็บ HUD โผล่ตอน hover) ============ */ ?>
<?php if ( isset( $ea2000_chapters['features'] ) ) : ?>
<?php
$ea2000_modules = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$ea2000_f_title = trim( (string) ea2000_mod( 'feat' . $i . '_title' ) );
	$ea2000_f_desc  = trim( (string) ea2000_mod( 'feat' . $i . '_desc' ) );
	if ( '' === $ea2000_f_title && '' === $ea2000_f_desc ) {
		continue;
	}
	$ea2000_modules[] = array( $ea2000_f_title, $ea2000_f_desc );
}
$ea2000_module_label = trim( (string) ea2000_mod( 'feat_module_label' ) );
?>
<section class="ch ch-features" id="features"<?php ea2000_front_ch_attrs( $ea2000_chapters['features'] ); ?>>
	<div class="container">
		<?php ea2000_front_ch_head( $ea2000_chapters['features'], $ea2000_ch_total, ea2000_mod( 'features_title' ), ea2000_mod( 'features_subtitle' ) ); ?>
		<?php if ( ! empty( $ea2000_modules ) ) : ?>
		<ul class="modules">
			<?php foreach ( $ea2000_modules as $ea2000_mi => $ea2000_module ) : ?>
			<li class="module hud-frame">
				<?php ea2000_hud_corners(); ?>
				<i class="scan" aria-hidden="true"></i>
				<p class="module-id mono"><?php echo esc_html( trim( $ea2000_module_label . ' ' . sprintf( '%02d', $ea2000_mi + 1 ) ) ); ?></p>
				<?php if ( '' !== $ea2000_module[0] ) : ?>
				<h3 class="module-title"><?php echo esc_html( $ea2000_module[0] ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== $ea2000_module[1] ) : ?>
				<p class="module-desc"><?php echo esc_html( $ea2000_module[1] ); ?></p>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 05) DOSSIER TABS · #tests (แท็บ radio ไม่ใช้ JS · ไม่พิมพ์ค่า bt_stat* / fw_stat* · tests_note แสดงเสมอ) ============ */ ?>
<?php if ( isset( $ea2000_chapters['tests'] ) ) : ?>
<?php
$ea2000_dossier = array(
	'bt' => array(
		'radio' => 'dossier-bt',
		'panel' => 'panel-1',
		'idx'   => '01',
		'tab'   => trim( (string) ea2000_mod( 'tests_tab_bt_label' ) ),
		'url'   => home_url( '/backtest/' ),
		'fig'   => '03',
	),
	'fw' => array(
		'radio' => 'dossier-fw',
		'panel' => 'panel-2',
		'idx'   => '02',
		'tab'   => trim( (string) ea2000_mod( 'tests_tab_fw_label' ) ),
		'url'   => home_url( '/forward-test/' ),
		'fig'   => '04',
	),
);
$ea2000_tests_note = trim( (string) ea2000_mod( 'tests_note' ) );
?>
<section class="ch ch-tests band-surface" id="tests"<?php ea2000_front_ch_attrs( $ea2000_chapters['tests'] ); ?>>
	<div class="container">
		<?php ea2000_front_ch_head( $ea2000_chapters['tests'], $ea2000_ch_total, ea2000_mod( 'tests_title' ), ea2000_mod( 'tests_intro' ) ); ?>
		<div class="dossier">
			<?php /* radio ต้องอยู่ก่อน .tabs และ .tab-panels ในพาเรนต์เดียวกัน (sibling selector ของ CSS) */ ?>
			<?php foreach ( $ea2000_dossier as $ea2000_tk => $ea2000_td ) : ?>
			<input type="radio" id="<?php echo esc_attr( $ea2000_td['radio'] ); ?>" name="ea2000-tests" class="tab-radio"<?php echo 'bt' === $ea2000_tk ? ' checked' : ''; ?>>
			<?php endforeach; ?>
			<div class="tabs">
				<?php foreach ( $ea2000_dossier as $ea2000_td ) : ?>
				<label class="tab" for="<?php echo esc_attr( $ea2000_td['radio'] ); ?>"><span class="tab-idx mono"><?php echo esc_html( $ea2000_td['idx'] ); ?></span><?php echo esc_html( $ea2000_td['tab'] ); ?></label>
				<?php endforeach; ?>
				<i class="tab-line" aria-hidden="true"></i>
			</div>
			<div class="tab-panels">
				<?php foreach ( $ea2000_dossier as $ea2000_tk => $ea2000_td ) : ?>
				<?php $ea2000_test_btn = trim( (string) ea2000_mod( 'tests_' . $ea2000_tk . '_btn' ) ); ?>
				<article class="tab-panel <?php echo esc_attr( $ea2000_td['panel'] ); ?>">
					<div class="dossier-media">
						<?php ea2000_front_media( 'tests_' . $ea2000_tk, 1280, 720, trim( $ea2000_fig_label . ' ' . $ea2000_td['fig'] ) ); ?>
					</div>
					<div class="dossier-body">
						<h3 class="keep-case"><?php echo esc_html( ea2000_mod( 'tests_' . $ea2000_tk . '_title' ) ); ?></h3>
						<p><?php echo nl2br( esc_html( ea2000_mod( 'tests_' . $ea2000_tk . '_text' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
						<?php if ( '' !== $ea2000_test_btn ) : ?>
						<a class="key key-ghost" href="<?php echo esc_url( $ea2000_td['url'] ); ?>"><?php echo esc_html( $ea2000_test_btn ); ?><?php echo ea2000_icon( 'arrow', 'icon icon-sm key-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<?php endif; ?>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php if ( '' !== $ea2000_tests_note ) : ?>
		<p class="notice-row"><?php echo ea2000_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $ea2000_tests_note ); ?></span></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 06) FILMSTRIP + CHECKLIST · #install ============ */ ?>
<?php if ( isset( $ea2000_chapters['install'] ) ) : ?>
<?php
$ea2000_install_steps = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$ea2000_in_title = trim( (string) ea2000_mod( 'install_step' . $i . '_title' ) );
	$ea2000_in_desc  = trim( (string) ea2000_mod( 'install_step' . $i . '_desc' ) );
	if ( '' === $ea2000_in_title && '' === $ea2000_in_desc ) {
		continue;
	}
	$ea2000_install_steps[] = array(
		'key'   => 'install_step' . $i,
		'n'     => sprintf( '%02d', count( $ea2000_install_steps ) + 1 ),
		'title' => $ea2000_in_title,
		'desc'  => $ea2000_in_desc,
	);
}
$ea2000_install_count = count( $ea2000_install_steps );
$ea2000_install_total = sprintf( '%02d', $ea2000_install_count );
$ea2000_step_label    = trim( (string) ea2000_mod( 'install_step_label' ) );
$ea2000_install_mnote = trim( (string) ea2000_mod( 'install_mobile_note' ) );
$ea2000_install_btn   = trim( (string) ea2000_mod( 'install_btn' ) );
$ea2000_install_url   = trim( (string) ea2000_mod( 'install_btn_url' ) );
$ea2000_install_href  = '' !== $ea2000_install_url ? ea2000_link_url( $ea2000_install_url ) : home_url( '/how-to-install/' );
?>
<section class="ch ch-install" id="install"<?php ea2000_front_ch_attrs( $ea2000_chapters['install'] ); ?>>
	<div class="container">
		<?php ea2000_front_ch_head( $ea2000_chapters['install'], $ea2000_ch_total, ea2000_mod( 'install_title' ), ea2000_mod( 'install_intro' ) ); ?>
		<?php if ( $ea2000_install_count > 0 ) : ?>
		<div class="film" role="group" tabindex="0" aria-label="<?php echo esc_attr( sprintf( 'ภาพขั้นตอนการติดตั้ง %d ภาพ เลื่อนดูได้', $ea2000_install_count ) ); ?>">
			<?php foreach ( $ea2000_install_steps as $ea2000_step ) : ?>
			<?php ea2000_front_media( $ea2000_step['key'], 1280, 720, trim( $ea2000_fig_label . ' ' . $ea2000_step['n'] . '/' . $ea2000_install_total ), 'frame' ); ?>
			<?php endforeach; ?>
		</div>
		<ol class="ledger install-ledger">
			<?php foreach ( $ea2000_install_steps as $ea2000_step ) : ?>
			<li class="ledger-row"><span class="ledger-idx mono keep-case"><span aria-hidden="true">&gt; </span><?php echo esc_html( trim( $ea2000_step_label . ' ' . $ea2000_step['n'] ) ); ?></span><div class="ledger-body"><?php if ( '' !== $ea2000_step['title'] ) : ?><h3><?php echo esc_html( $ea2000_step['title'] ); ?></h3><?php endif; ?><?php if ( '' !== $ea2000_step['desc'] ) : ?><p><?php echo esc_html( $ea2000_step['desc'] ); ?></p><?php endif; ?></div></li>
			<?php endforeach; ?>
		</ol>
		<?php endif; ?>
		<?php if ( '' !== $ea2000_install_mnote ) : ?>
		<p class="notice-row"><?php echo ea2000_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $ea2000_install_mnote ); ?></span></p>
		<?php endif; ?>
		<?php if ( '' !== $ea2000_install_btn ) : ?>
		<p class="ch-actions"><a class="key key-ghost" href="<?php echo esc_url( $ea2000_install_href ); ?>"><?php echo esc_html( $ea2000_install_btn ); ?><?php echo ea2000_icon( 'arrow', 'icon icon-sm key-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 07) TIER SELECTOR · #pricing (แท็บ radio < 1100px · ตาราง 3 คอลัมน์ >= 1100px) ============ */ ?>
<?php if ( isset( $ea2000_chapters['pricing'] ) ) : ?>
<?php
$ea2000_pmode = ea2000_mod( 'pricing_mode' );
$ea2000_tiers = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$ea2000_pk_name = trim( (string) ea2000_mod( 'pkg' . $i . '_name' ) );
	if ( '' === $ea2000_pk_name ) {
		continue;
	}
	$ea2000_tiers[] = array(
		'n'        => count( $ea2000_tiers ) + 1,
		'name'     => $ea2000_pk_name,
		'tag'      => trim( (string) ea2000_mod( 'pkg' . $i . '_tag' ) ),
		'price'    => trim( (string) ea2000_mod( 'pkg' . $i . '_price' ) ),
		'period'   => trim( (string) ea2000_mod( 'pkg' . $i . '_period' ) ),
		'features' => ea2000_lines( ea2000_mod( 'pkg' . $i . '_features' ) ),
		'featured' => (bool) ea2000_mod( 'pkg' . $i . '_featured' ),
	);
}
/* แท็บที่เลือกตอนโหลด = แพ็กเกจ featured ตัวแรก ถ้าไม่มีให้ตัวแรก */
$ea2000_tier_checked = 1;
foreach ( $ea2000_tiers as $ea2000_tier ) {
	if ( $ea2000_tier['featured'] ) {
		$ea2000_tier_checked = $ea2000_tier['n'];
		break;
	}
}
$ea2000_rec_label     = trim( (string) ea2000_mod( 'pricing_recommended_label' ) );
$ea2000_contact_price = trim( (string) ea2000_mod( 'pricing_contact_text' ) );
$ea2000_margin_note   = trim( (string) ea2000_mod( 'risk_margin_note' ) );
$ea2000_pricing_more  = trim( (string) ea2000_mod( 'pricing_more_text' ) );
$ea2000_pricing_note  = trim( (string) ea2000_mod( 'pricing_note' ) );
?>
<section class="ch ch-pricing" id="pricing"<?php ea2000_front_ch_attrs( $ea2000_chapters['pricing'] ); ?>>
	<div class="container">
		<?php ea2000_front_ch_head( $ea2000_chapters['pricing'], $ea2000_ch_total, ea2000_mod( 'pricing_home_title' ), ea2000_mod( 'pricing_home_sub' ) ); ?>
		<?php if ( ! empty( $ea2000_tiers ) ) : ?>
		<div class="tiers">
			<?php foreach ( $ea2000_tiers as $ea2000_tier ) : ?>
			<input type="radio" id="tier-<?php echo (int) $ea2000_tier['n']; ?>" name="ea2000-tier" class="tab-radio"<?php echo $ea2000_tier['n'] === $ea2000_tier_checked ? ' checked' : ''; ?>>
			<?php endforeach; ?>
			<div class="tabs tier-tabs">
				<?php foreach ( $ea2000_tiers as $ea2000_tier ) : ?>
				<label class="tab" for="tier-<?php echo (int) $ea2000_tier['n']; ?>"><span class="tab-idx mono"><?php echo esc_html( sprintf( '%02d', $ea2000_tier['n'] ) ); ?></span><span class="keep-case"><?php echo esc_html( $ea2000_tier['name'] ); ?></span><?php if ( $ea2000_tier['featured'] && '' !== $ea2000_rec_label ) : ?><span class="tab-rec mono"><?php echo esc_html( $ea2000_rec_label ); ?></span><?php endif; ?></label>
				<?php endforeach; ?>
			</div>
			<div class="tab-panels tier-panels">
				<?php foreach ( $ea2000_tiers as $ea2000_tier ) : ?>
				<article class="tab-panel tier-panel panel-<?php echo (int) $ea2000_tier['n']; ?><?php echo $ea2000_tier['featured'] ? ' is-featured' : ''; ?>">
					<header class="tier-head">
						<h3 class="tier-name keep-case"><?php echo esc_html( $ea2000_tier['name'] ); ?><?php if ( $ea2000_tier['featured'] && '' !== $ea2000_rec_label ) : ?> <span class="tier-rec mono"><?php echo esc_html( $ea2000_rec_label ); ?></span><?php endif; ?></h3>
						<?php if ( '' !== $ea2000_tier['tag'] ) : ?>
						<p class="tier-tag"><?php echo esc_html( $ea2000_tier['tag'] ); ?></p>
						<?php endif; ?>
					</header>
					<p class="tier-price">
						<?php if ( 'price' === $ea2000_pmode && '' !== $ea2000_tier['price'] ) : ?>
						<span class="tier-amt keep-case"><?php echo esc_html( $ea2000_tier['price'] ); ?></span><?php if ( '' !== $ea2000_tier['period'] ) : ?> <span class="tier-period"><?php echo esc_html( $ea2000_tier['period'] ); ?></span><?php endif; ?>
						<?php else : ?>
						<span class="tier-amt tier-amt--contact"><?php echo esc_html( $ea2000_contact_price ); ?></span>
						<?php endif; ?>
					</p>
					<?php if ( ! empty( $ea2000_tier['features'] ) ) : ?>
					<ul class="tier-list mono-marks">
						<?php foreach ( $ea2000_tier['features'] as $ea2000_item ) : ?>
						<li><span class="mark mono keep-case" aria-hidden="true">[x]</span><span><?php echo esc_html( $ea2000_item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
					<?php ea2000_front_contact_key( $ea2000_line, $ea2000_go_url, ea2000_mod( 'pricing_btn_text' ), $ea2000_contact_text, 'pricing' ); ?>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
		<?php if ( '' !== $ea2000_margin_note ) : ?>
		<p class="margin-note mono"><?php echo ea2000_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $ea2000_margin_note ); ?></span></p>
		<?php endif; ?>
		<?php if ( '' !== $ea2000_pricing_more ) : ?>
		<p class="ch-actions"><a class="textlink" href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>"><?php echo esc_html( $ea2000_pricing_more ); ?><?php echo ea2000_icon( 'arrow', 'icon icon-sm key-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		<?php endif; ?>
		<?php if ( '' !== $ea2000_pricing_note ) : ?>
		<p class="ch-foot"><?php echo esc_html( $ea2000_pricing_note ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 08) QUERY LOG · #faq (details/summary ไม่ใช้ JS · ทุกข้อปิด · ea2000_faq_schema() อ่านจาก key เดิม) ============ */ ?>
<?php if ( isset( $ea2000_chapters['faq'] ) ) : ?>
<section class="ch ch-faq band-surface" id="faq"<?php ea2000_front_ch_attrs( $ea2000_chapters['faq'] ); ?>>
	<div class="container">
		<?php ea2000_front_ch_head( $ea2000_chapters['faq'], $ea2000_ch_total, ea2000_mod( 'faq_title' ), ea2000_mod( 'faq_subtitle' ) ); ?>
		<div class="qlog">
			<?php
			$ea2000_qn = 0;
			for ( $i = 1; $i <= 10; $i++ ) :
				$ea2000_q = trim( (string) ea2000_mod( 'faq' . $i . '_q' ) );
				$ea2000_a = trim( (string) ea2000_mod( 'faq' . $i . '_a' ) );
				if ( '' === $ea2000_q || '' === $ea2000_a ) {
					continue;
				}
				$ea2000_qn++;
				?>
				<details class="q" name="ea2000-faq">
					<summary class="q-sum"><span class="q-idx mono"><?php echo esc_html( sprintf( '%02d', $ea2000_qn ) ); ?></span><span class="q-text"><?php echo esc_html( $ea2000_q ); ?></span><i class="q-mark" aria-hidden="true"></i></summary>
					<div class="q-ans"><p><?php echo nl2br( esc_html( $ea2000_a ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p></div>
				</details>
			<?php endfor; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* ============ 09) HAZARD BAND · #risk (ข้อความเต็ม ไม่พับ ไม่ตัด · ห้ามลดทอน) ============ */ ?>
<?php if ( isset( $ea2000_chapters['risk'] ) ) : ?>
<?php
$ea2000_risk_label = trim( (string) ea2000_mod( 'risk_label' ) );
$ea2000_risk_more  = trim( (string) ea2000_mod( 'risk_more_text' ) );
?>
<section class="ch ch-risk hazard" id="risk"<?php ea2000_front_ch_attrs( $ea2000_chapters['risk'] ); ?>>
	<div class="container hazard-inner">
		<p class="hazard-label mono keep-case"><?php if ( '' !== $ea2000_risk_label ) : ?><span class="hazard-stamp"><?php echo esc_html( $ea2000_risk_label ); ?></span><?php endif; ?><span class="hazard-topic"><?php echo esc_html( $ea2000_chapters['risk']['label'] ); ?></span></p>
		<h2 class="hazard-title"><?php echo esc_html( ea2000_mod( 'risk_title' ) ); ?></h2>
		<p class="hazard-text"><?php echo nl2br( esc_html( ea2000_mod( 'risk_text' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<?php if ( '' !== $ea2000_risk_more ) : ?>
		<a class="key key-warn" href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>"><?php echo esc_html( $ea2000_risk_more ); ?><?php echo ea2000_icon( 'arrow', 'icon icon-sm key-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

</main>

<?php
get_footer();
