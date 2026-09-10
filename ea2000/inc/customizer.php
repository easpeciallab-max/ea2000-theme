<?php
/**
 * EA2000 · Customizer (หน้า "ปรับแต่ง")
 * ทุกข้อความ รูปภาพ ลิงก์ และการเปิดและปิด section แก้ได้จากที่นี่
 *
 * ลำดับหมวด: หมวด 1 ถึง 12 เรียงตามบล็อกบนหน้าแรก v3 "Control Room" · หมวด 13 ถึง 16 ส่วนกลางของเว็บ (footer, เมนูมือถือ, SEO, คุกกี้)
 * หมวด 17 ถึง 22 หน้าย่อยและ Link Hub
 * บล็อกเก่าของหน้าแรก (ไฮไลต์, Control Center, การ์ดนำทาง, about/steps ชุดเดิม, แกลเลอรี, ตารางผลทดสอบ, เหมาะกับใคร, รีวิว, ทีมงาน,
 * ความมั่นใจ, บทความล่าสุด, CTA กลางหน้า) ไม่มี control แล้วตั้งแต่ 9 ก.ย. 2026 · key ยังอยู่ใน ea2000_defaults() เพื่อ REST
 *
 * @package ea2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------- Sanitize helpers ---------------- */

function ea2000_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === (bool) $checked );
}

function ea2000_sanitize_pricing_mode( $value ) {
	return in_array( $value, array( 'price', 'contact' ), true ) ? $value : 'contact';
}

/* ---------------- Register ---------------- */

function ea2000_customize_register( $wp_customize ) {

	$d = ea2000_defaults();

	/* กติกาเนื้อหา (spec ข้อ 0.6) ต่อท้าย description ของทุกช่องข้อความใหม่ที่ผู้เข้าชมอ่านได้ */
	$rule = 'ห้ามระบุกลยุทธ์ ตัวเลขผลเทรด หรือคำรับประกัน';

	$wp_customize->add_panel(
		'ea2000_panel',
		array(
			'title'       => 'EA2000 · ตั้งค่าหน้าเว็บ',
			'description' => 'แก้ไขข้อความ รูปภาพ ลิงก์ และเปิด/ปิดแต่ละส่วนของหน้าแรกได้ทั้งหมดจากเมนูนี้ กด "เผยแพร่" เพื่อบันทึก',
			'priority'    => 10,
		)
	);

	/*
	 * โครงสร้าง: section_id => [ title, description, fields ]
	 * field: id => [ label, type, (choices/description) ]
	 */
	$sections = array(

		'ea2000_general' => array(
			'title'       => '1) แบรนด์และช่องทางติดต่อ (สำคัญ ตั้งค่าก่อน)',
			'description' => 'ชื่อแบรนด์จะแสดงข้างโลโก้บนเมนูและ footer · ลิงก์ LINE จะถูกใช้กับปุ่มทุกปุ่มบนเว็บโดยอัตโนมัติ ช่องทางที่เว้นว่างจะไม่แสดงปุ่ม',
			'fields'      => array(
				'brand_name'      => array( 'ชื่อแบรนด์ (แสดงข้างโลโก้)', 'text', 'ค่าเริ่มต้น EA2000' ),
				'brand_tagline'   => array( 'คำต่อท้ายชื่อแบรนด์ (บรรทัดเล็กใต้ชื่อ)', 'text', 'เช่น EA for MT5' ),
				'brand_wordmark'  => array( 'โลโก้แบบ wordmark (แนวนอน)', 'image', 'แสดงแทนโลโก้กลม + ชื่อแบรนด์บนเมนูและ footer · เว้นว่างเพื่อกลับไปแสดงโลโก้กลมพร้อมชื่อแบรนด์ · แนะนำ PNG พื้นโปร่ง กว้างประมาณ 660px (สูง 120px)' ),
				'line_url'        => array( 'ลิงก์ LINE OA', 'url', 'เช่น https://lin.ee/xxxxx หรือ https://line.me/R/ti/p/@xxxxx' ),
				'facebook_url'    => array( 'ลิงก์ Facebook Page (ถ้ามี)', 'url' ),
				'instagram_url'   => array( 'ลิงก์ Instagram (ถ้ามี)', 'url' ),
				'tiktok_url'      => array( 'ลิงก์ TikTok (ถ้ามี)', 'url' ),
				'youtube_url'     => array( 'ลิงก์ YouTube (ถ้ามี)', 'url' ),
				'contact_email'   => array( 'อีเมลติดต่อ (ถ้ามี)', 'text' ),
				'show_float_line' => array( 'แสดงปุ่ม LINE ลอยมุมขวาล่าง', 'checkbox' ),
				'float_line_text' => array( 'ข้อความบนปุ่ม LINE ลอย', 'text' ),
				'contact_fallback_text' => array( 'ข้อความปุ่มติดต่อเมื่อยังไม่กรอก LINE OA', 'text', 'ใช้กับปุ่มหลักบนหน้าแรกและแถบท้ายเว็บ ปุ่มจะชี้ไปหน้า /go/ (ต้องเผยแพร่แล้ว) · ' . $rule ),
				'show_language_switcher' => array( 'แสดงตัวสลับภาษาในเมนูบน', 'checkbox' ),
				'language_fallback_items' => array( 'รายการภาษาสำรอง (code|ธง|ป้ายสั้น|ชื่อภาษา)', 'textarea' ),
			),
		),

		'ea2000_hero' => array(
			'title'       => '2) บล็อก 0 · Boot (hero) และรางเลขบท',
			'description' => 'หัวข้อหลัก (H1) ของหน้าแรกคือ "ชื่อหลัก" ต่อด้วย "ประโยคหลัก" · ควรมีคำว่า EA MT5 หรือ ระบบเทรดอัตโนมัติ อยู่ในประโยคหลักเสมอ · แถบ HUD ใต้ปุ่มแสดงเฉพาะข้อเท็จจริงของระบบ ค่าที่เป็น % หรือมีคำว่า กำไร จะไม่ถูกแสดง',
			'fields'      => array(
				'show_hero'      => array( 'แสดงส่วนนี้', 'checkbox' ),
				'show_rail'      => array( 'แสดงรางเลขบทด้านซ้าย (เดสก์ท็อป) และเส้น progress ใต้เมนู (จอเล็ก)', 'checkbox' ),
				'hero_badge'     => array( 'บรรทัดนำเหนือชื่อ (monospace)', 'text', $rule ),
				'hero_title'     => array( 'ชื่อหลัก (Headline)', 'text' ),
				'hero_subtitle'  => array( 'ประโยคหลัก (Sub headline · ต่อท้ายชื่อหลักใน H1)', 'text' ),
				'hero_desc'      => array( 'คำอธิบายสั้น', 'textarea' ),
				'hero_btn1_text' => array( 'ข้อความปุ่มหลัก (ลิงก์ไป LINE)', 'text' ),
				'hero_btn2_text' => array( 'ข้อความลิงก์รอง (เลื่อนไปบท "ลำดับการทำงาน")', 'text' ),
				'hero_note'      => array( 'ข้อความเตือนความเสี่ยงใต้ปุ่ม', 'text' ),
				'hero_hud_items' => array( 'แถบ HUD ใต้ปุ่ม (บรรทัดละ ป้าย|ค่า)', 'textarea', 'เช่น แพลตฟอร์ม|MetaTrader 5 · บรรทัดที่ไม่มี | จะถูกข้าม · ' . $rule ),
				'hero_hud_note'  => array( 'คำอธิบายแถบ HUD สำหรับ screen reader', 'text', 'ไม่แสดงบนจอ · ' . $rule ),
				'hero_image'     => array( 'ภาพกล่องสินค้า (Hero)', 'image', 'ค่าเริ่มต้นคือภาพกล่องสินค้าพื้นโปร่ง 1000x1000 px · เว้นว่างจะแสดงช่องรอใส่รูปพร้อมข้อความด้านล่าง (ไม่ใช้โลโก้แทน)' ),
				'hero_img_alt'   => array( 'ข้อความอธิบายภาพ Hero (Alt)', 'text', 'ควรมีคำว่า EA2000 และ MT5' ),
				'hero_img_note'  => array( 'ข้อความแนะนำรูปที่ต้องใส่ (แสดงเมื่อยังไม่มีรูป)', 'text', $rule ),
				'fig_label'      => array( 'คำนำหน้าคำบรรยายภาพ', 'text', 'เช่น ภาพ จะแสดงเป็น ภาพ 01 ใต้ช่องรูปทุกช่อง · ' . $rule ),
			),
		),

		'ea2000_what' => array(
			'title'       => '3) บล็อก 1 · ระบบคืออะไร (datasheet)',
			'description' => 'อธิบายว่า EA2000 คืออะไรและทำงานอย่างไร ใช้คำพ้อง Expert Advisor / บอทเทรด / โรบอทเทรด ให้ครบ · รูปเว้นว่างจะแสดงช่องรอใส่รูปพร้อมข้อความแนะนำ (ผู้เข้าชมเห็นด้วย)',
			'fields'      => array(
				'show_what'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'what_kicker'   => array( 'ป้ายบท (แสดงบนรางเลขบทและหัวบท)', 'text', $rule ),
				'what_title'    => array( 'หัวข้อ (H2)', 'text' ),
				'what_text'     => array( 'เนื้อหา (เว้นบรรทัด = ขึ้นย่อหน้าใหม่)', 'textarea' ),
				'what_points'   => array( 'ตารางสรุป (บรรทัดละ ป้าย|ข้อความ)', 'textarea', 'บรรทัดที่ไม่มี | จะใช้เลขลำดับเป็นป้าย · ' . $rule ),
				'what_principle_label' => array( 'กล่องหลักการ · ป้าย', 'text', $rule ),
				'what_principle'       => array( 'กล่องหลักการ · ประโยค', 'text', 'ประโยคหลักการของทีม ห้ามใส่คำพูดลูกค้าหรือผลเทรด · ' . $rule ),
				'what_img'      => array( 'ภาพประกอบ (หน้าจอ MT5 ขณะรัน EA2000)', 'image', 'แนะนำ 1280x800 px' ),
				'what_img_alt'  => array( 'ข้อความอธิบายภาพ (Alt)', 'text' ),
				'what_img_note' => array( 'ข้อความแนะนำรูปที่ต้องใส่ (แสดงเมื่อยังไม่มีรูป)', 'text' ),
			),
		),

		'ea2000_pain' => array(
			'title'       => '4) บล็อก 2 · ปัญหาของการเทรดมือ (ledger ขีดฆ่า)',
			'description' => 'แต่ละข้อจะถูกขีดฆ่าเมื่อผู้เข้าชมเลื่อนถึง แล้วปิดท้ายด้วยแถวสรุปว่าระบบอัตโนมัติเข้ามาแทนอะไร',
			'fields'      => array(
				'show_pain'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'pain_kicker'   => array( 'ป้ายบท (แสดงบนรางเลขบทและหัวบท)', 'text', $rule ),
				'pain_title'    => array( 'หัวข้อ', 'text' ),
				'pain_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'pain1_title'   => array( 'ข้อ 1 · หัวข้อ', 'text' ),
				'pain1_desc'    => array( 'ข้อ 1 · รายละเอียด', 'textarea' ),
				'pain2_title'   => array( 'ข้อ 2 · หัวข้อ', 'text' ),
				'pain2_desc'    => array( 'ข้อ 2 · รายละเอียด', 'textarea' ),
				'pain3_title'   => array( 'ข้อ 3 · หัวข้อ', 'text' ),
				'pain3_desc'    => array( 'ข้อ 3 · รายละเอียด', 'textarea' ),
				'pain4_title'   => array( 'ข้อ 4 · หัวข้อ', 'text' ),
				'pain4_desc'    => array( 'ข้อ 4 · รายละเอียด', 'textarea' ),
				'pain_resolved_label' => array( 'แถวสรุป · ป้าย', 'text', $rule ),
				'pain_resolved_text'  => array( 'แถวสรุป · ข้อความ', 'textarea', $rule ),
			),
		),

		'ea2000_how' => array(
			'title'       => '5) บล็อก 3 · ลำดับการทำงาน (เทอร์มินัล + 4 ขั้น)',
			'description' => 'อธิบายวงจรการทำงานของ EA บน MetaTrader 5 แบบทั่วไป ห้ามเขียนอ้างกลยุทธ์ภายในของระบบ · เทอร์มินัลจำลองพิมพ์ชื่อขั้นทีละตัว ไม่มีเวลา ราคา หรือผลเทรด · กล่องข้าง "ต้องมีอะไรบ้าง" บรรทัดละ 1 ข้อ',
			'fields'      => array(
				'show_how'        => array( 'แสดงส่วนนี้', 'checkbox' ),
				'how_kicker'      => array( 'ป้ายบท (แสดงบนรางเลขบทและหัวบท)', 'text', $rule ),
				'how_title'       => array( 'หัวข้อ (H2)', 'text' ),
				'how_intro'       => array( 'ประโยคเกริ่นนำ', 'textarea' ),
				'show_how_log'    => array( 'แสดงเทอร์มินัลจำลองลำดับการทำงาน', 'checkbox' ),
				'how_log_title'   => array( 'เทอร์มินัล · ชื่อหน้าต่าง', 'text', $rule ),
				'how_log_prompt'  => array( 'เทอร์มินัล · prompt', 'text', 'เช่น ea2000@mt5:~$ · ' . $rule ),
				'how_log_start'   => array( 'เทอร์มินัล · คำสั่งเริ่ม (ต่อท้าย prompt)', 'text', $rule ),
				'how_log_lines'   => array( 'เทอร์มินัล · บรรทัด log กำหนดเอง', 'textarea', 'เว้นว่าง = ใช้ชื่อ 4 ขั้นด้านบนอัตโนมัติ · บรรทัดละ 1 ข้อความ · ห้ามระบุกลยุทธ์ ตัวเลข เวลา หรือผลการเทรด' ),
				'how_log_ready'   => array( 'เทอร์มินัล · บรรทัดปิดท้าย (สถานะพร้อม)', 'text', $rule ),
				'how_step1_title' => array( 'ขั้น 1 · หัวข้อ', 'text' ),
				'how_step1_desc'  => array( 'ขั้น 1 · รายละเอียด', 'textarea' ),
				'how_step2_title' => array( 'ขั้น 2 · หัวข้อ', 'text' ),
				'how_step2_desc'  => array( 'ขั้น 2 · รายละเอียด', 'textarea' ),
				'how_step3_title' => array( 'ขั้น 3 · หัวข้อ', 'text' ),
				'how_step3_desc'  => array( 'ขั้น 3 · รายละเอียด', 'textarea' ),
				'how_step4_title' => array( 'ขั้น 4 · หัวข้อ', 'text' ),
				'how_step4_desc'  => array( 'ขั้น 4 · รายละเอียด', 'textarea' ),
				'how_req_title'   => array( 'กล่องข้าง · หัวข้อ', 'text' ),
				'how_req_items'   => array( 'กล่องข้าง · รายการ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'how_img'         => array( 'ภาพประกอบ (Dashboard ของ EA2000)', 'image', 'แนะนำ 1280x800 px' ),
				'how_img_alt'     => array( 'ข้อความอธิบายภาพ (Alt)', 'text' ),
				'how_img_note'    => array( 'ข้อความแนะนำรูปที่ต้องใส่ (แสดงเมื่อยังไม่มีรูป)', 'text' ),
			),
		),

		'ea2000_features' => array(
			'title'       => '6) บล็อก 4 · โมดูลของระบบ (6 ข้อ)',
			'description' => 'ตารางเซลล์ hairline 3x2 ไม่มีไอคอน · แต่ละเซลล์มีเลขลำดับนำหน้า',
			'fields'      => array(
				'show_features'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'features_kicker'   => array( 'ป้ายบท (แสดงบนรางเลขบทและหัวบท)', 'text', $rule ),
				'feat_module_label' => array( 'คำนำหน้าเลขในแต่ละเซลล์', 'text', 'เช่น โมดูล จะแสดงเป็น โมดูล 01 · ' . $rule ),
				'features_title'    => array( 'หัวข้อ', 'text' ),
				'features_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'feat1_title'       => array( 'จุดเด่น 1 · หัวข้อ', 'text' ),
				'feat1_desc'        => array( 'จุดเด่น 1 · รายละเอียด', 'textarea' ),
				'feat2_title'       => array( 'จุดเด่น 2 · หัวข้อ', 'text' ),
				'feat2_desc'        => array( 'จุดเด่น 2 · รายละเอียด', 'textarea' ),
				'feat3_title'       => array( 'จุดเด่น 3 · หัวข้อ', 'text' ),
				'feat3_desc'        => array( 'จุดเด่น 3 · รายละเอียด', 'textarea' ),
				'feat4_title'       => array( 'จุดเด่น 4 · หัวข้อ', 'text' ),
				'feat4_desc'        => array( 'จุดเด่น 4 · รายละเอียด', 'textarea' ),
				'feat5_title'       => array( 'จุดเด่น 5 · หัวข้อ', 'text' ),
				'feat5_desc'        => array( 'จุดเด่น 5 · รายละเอียด', 'textarea' ),
				'feat6_title'       => array( 'จุดเด่น 6 · หัวข้อ', 'text' ),
				'feat6_desc'        => array( 'จุดเด่น 6 · รายละเอียด', 'textarea' ),
			),
		),

		'ea2000_tests' => array(
			'title'       => '7) บล็อก 5 · การทดสอบ (แท็บ Backtest / Forward Test)',
			'description' => 'บล็อกนี้อธิบายว่าการทดสอบสองแบบคืออะไรและลิงก์ไปหน้า /backtest/ กับ /forward-test/ · ไม่มีช่องตัวเลข ตัวเลขจริงกรอกที่หมวดหน้า Backtest และหน้า Forward Test เท่านั้น · ภาพใส่เมื่อมีผลจริงเท่านั้น · อย่าลบหมายเหตุท้ายบล็อก',
			'fields'      => array(
				'show_tests'        => array( 'แสดงส่วนนี้', 'checkbox' ),
				'tests_kicker'      => array( 'ป้ายบท (แสดงบนรางเลขบทและหัวบท)', 'text', $rule ),
				'tests_title'       => array( 'หัวข้อ (H2)', 'text' ),
				'tests_intro'       => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'tests_tab_bt_label' => array( 'แท็บ 1 · ชื่อแท็บ Backtest', 'text', $rule ),
				'tests_tab_fw_label' => array( 'แท็บ 2 · ชื่อแท็บ Forward Test', 'text', $rule ),
				'tests_bt_title'    => array( 'แผง Backtest · หัวข้อ', 'text' ),
				'tests_bt_text'     => array( 'แผง Backtest · รายละเอียด', 'textarea' ),
				'tests_bt_btn'      => array( 'แผง Backtest · ข้อความปุ่ม (ลิงก์ไป /backtest/)', 'text' ),
				'tests_bt_img'      => array( 'แผง Backtest · ภาพ', 'image', 'แนะนำ 1280x720 px · ใส่เมื่อมีผลจริงเท่านั้น' ),
				'tests_bt_img_alt'  => array( 'แผง Backtest · ข้อความอธิบายภาพ (Alt)', 'text' ),
				'tests_bt_img_note' => array( 'แผง Backtest · ข้อความแนะนำรูปที่ต้องใส่', 'text' ),
				'tests_fw_title'    => array( 'แผง Forward Test · หัวข้อ', 'text' ),
				'tests_fw_text'     => array( 'แผง Forward Test · รายละเอียด', 'textarea' ),
				'tests_fw_btn'      => array( 'แผง Forward Test · ข้อความปุ่ม (ลิงก์ไป /forward-test/)', 'text' ),
				'tests_fw_img'      => array( 'แผง Forward Test · ภาพ', 'image', 'แนะนำ 1280x720 px · ใส่เมื่อมีข้อมูลจริงเท่านั้น' ),
				'tests_fw_img_alt'  => array( 'แผง Forward Test · ข้อความอธิบายภาพ (Alt)', 'text' ),
				'tests_fw_img_note' => array( 'แผง Forward Test · ข้อความแนะนำรูปที่ต้องใส่', 'text' ),
				'tests_note'        => array( 'หมายเหตุท้ายบล็อก (จำเป็นต้องมี)', 'textarea' ),
			),
		),

		'ea2000_install_home' => array(
			'title'       => '8) บล็อก 6 · การติดตั้ง 3 ขั้น (ฟิล์มภาพ + รายการ)',
			'description' => 'สรุปย่อของคู่มือติดตั้ง · ประโยคเกริ่นนำใช้ค่าเดียวกับหน้า How to Install (แก้ได้ที่หมวดหน้า How to Install) · ภาพแต่ละขั้นค่าเริ่มต้นใช้ภาพจากคู่มือ เว้นว่างจะแสดงช่องรอใส่รูปพร้อมข้อความแนะนำ',
			'fields'      => array(
				'show_install'          => array( 'แสดงส่วนนี้', 'checkbox' ),
				'install_kicker'        => array( 'ป้ายบท (แสดงบนรางเลขบทและหัวบท)', 'text', $rule ),
				'install_step_label'    => array( 'คำนำหน้าเลขขั้นในรายการ', 'text', 'เช่น ขั้น จะแสดงเป็น ขั้น 01 · ' . $rule ),
				'install_title'         => array( 'หัวข้อ (H2)', 'text' ),
				'install_step1_title'   => array( 'ขั้น 1 · หัวข้อ', 'text' ),
				'install_step1_desc'    => array( 'ขั้น 1 · รายละเอียด', 'textarea' ),
				'install_step1_img'     => array( 'ขั้น 1 · ภาพ', 'image', 'แนะนำ 1280x720 px' ),
				'install_step1_img_alt' => array( 'ขั้น 1 · ข้อความอธิบายภาพ (Alt)', 'text' ),
				'install_step1_img_note' => array( 'ขั้น 1 · ข้อความแนะนำรูปที่ต้องใส่ (แสดงเมื่อยังไม่มีรูป)', 'text', $rule ),
				'install_step2_title'   => array( 'ขั้น 2 · หัวข้อ', 'text' ),
				'install_step2_desc'    => array( 'ขั้น 2 · รายละเอียด', 'textarea' ),
				'install_step2_img'     => array( 'ขั้น 2 · ภาพ', 'image', 'แนะนำ 1280x720 px' ),
				'install_step2_img_alt' => array( 'ขั้น 2 · ข้อความอธิบายภาพ (Alt)', 'text' ),
				'install_step2_img_note' => array( 'ขั้น 2 · ข้อความแนะนำรูปที่ต้องใส่ (แสดงเมื่อยังไม่มีรูป)', 'text', $rule ),
				'install_step3_title'   => array( 'ขั้น 3 · หัวข้อ', 'text' ),
				'install_step3_desc'    => array( 'ขั้น 3 · รายละเอียด', 'textarea' ),
				'install_step3_img'     => array( 'ขั้น 3 · ภาพ', 'image', 'แนะนำ 1280x720 px' ),
				'install_step3_img_alt' => array( 'ขั้น 3 · ข้อความอธิบายภาพ (Alt)', 'text' ),
				'install_step3_img_note' => array( 'ขั้น 3 · ข้อความแนะนำรูปที่ต้องใส่ (แสดงเมื่อยังไม่มีรูป)', 'text', $rule ),
				'install_mobile_note'   => array( 'หมายเหตุเรื่องมือถือ', 'text' ),
				'install_btn'           => array( 'ข้อความปุ่มไปคู่มือฉบับเต็ม', 'text' ),
				'install_btn_url'       => array( 'ลิงก์ปุ่ม (slug เช่น /how-to-install/ หรือ URL เต็ม)', 'text' ),
			),
		),

		'ea2000_pricing' => array(
			'title'       => '9) บล็อก 7 · แพ็กเกจ (ตัวเลือกแพ็กเกจ)',
			'description' => 'เลือกได้ว่าจะโชว์ราคา หรือให้สอบถามราคาทาง LINE · หน้าแรกแสดงแพ็กเกจชุดเดียวกับหน้า Pricing เป็นแท็บบนจอเล็กและตารางเปรียบเทียบบนจอกว้าง · แพ็กเกจที่ติ๊ก "แนะนำ" ตัวแรกจะถูกเลือกไว้ก่อน',
			'fields'      => array(
				'show_pricing'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'pricing_title'    => array( 'หัวข้อ', 'text' ),
				'pricing_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'pricing_mode'     => array(
					'รูปแบบการแสดงราคา',
					'radio',
					array(
						'price'   => 'แสดงตัวเลขราคา',
						'contact' => 'ไม่แสดงราคา · ให้สอบถามทาง LINE',
					),
				),
				'pkg1_name'        => array( 'แพ็กเกจ 1 · ชื่อ', 'text' ),
				'pkg1_tag'         => array( 'แพ็กเกจ 1 · คำอธิบายสั้น', 'text' ),
				'pkg1_price'       => array( 'แพ็กเกจ 1 · ราคา', 'text' ),
				'pkg1_period'      => array( 'แพ็กเกจ 1 · หน่วย/ระยะเวลา', 'text' ),
				'pkg1_features'    => array( 'แพ็กเกจ 1 · สิ่งที่ได้ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'pkg1_featured'    => array( 'แพ็กเกจ 1 · ติดป้าย "แนะนำ"', 'checkbox' ),
				'pkg2_name'        => array( 'แพ็กเกจ 2 · ชื่อ', 'text' ),
				'pkg2_tag'         => array( 'แพ็กเกจ 2 · คำอธิบายสั้น', 'text' ),
				'pkg2_price'       => array( 'แพ็กเกจ 2 · ราคา', 'text' ),
				'pkg2_period'      => array( 'แพ็กเกจ 2 · หน่วย/ระยะเวลา', 'text' ),
				'pkg2_features'    => array( 'แพ็กเกจ 2 · สิ่งที่ได้ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'pkg2_featured'    => array( 'แพ็กเกจ 2 · ติดป้าย "แนะนำ"', 'checkbox' ),
				'pkg3_name'        => array( 'แพ็กเกจ 3 · ชื่อ', 'text' ),
				'pkg3_tag'         => array( 'แพ็กเกจ 3 · คำอธิบายสั้น', 'text' ),
				'pkg3_price'       => array( 'แพ็กเกจ 3 · ราคา', 'text' ),
				'pkg3_period'      => array( 'แพ็กเกจ 3 · หน่วย/ระยะเวลา', 'text' ),
				'pkg3_features'    => array( 'แพ็กเกจ 3 · สิ่งที่ได้ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'pkg3_featured'    => array( 'แพ็กเกจ 3 · ติดป้าย "แนะนำ"', 'checkbox' ),
				'pricing_btn_text' => array( 'ข้อความปุ่มบนการ์ดราคา', 'text' ),
				'pricing_note'     => array( 'หมายเหตุท้ายส่วนราคา', 'text' ),
				'show_pricing_home'  => array( 'แสดงตัวเลือกแพ็กเกจบนหน้าแรก', 'checkbox' ),
				'pricing_kicker'     => array( 'หน้าแรก · ป้ายบท (แสดงบนรางเลขบทและหัวบท)', 'text', $rule ),
				'pricing_home_title' => array( 'หน้าแรก · หัวข้อแพ็กเกจ', 'text' ),
				'pricing_home_sub'   => array( 'หน้าแรก · คำอธิบายแพ็กเกจ', 'textarea' ),
				'pricing_recommended_label' => array( 'หน้าแรก · คำหลังชื่อแพ็กเกจที่ติ๊ก "แนะนำ"', 'text', $rule ),
				'pricing_contact_text'      => array( 'หน้าแรก · ข้อความแทนราคาเมื่อไม่แสดงราคา', 'text', 'ใช้เมื่อเลือก "ไม่แสดงราคา" หรือช่องราคาว่าง · ' . $rule ),
				'pricing_more_text'         => array( 'หน้าแรก · ข้อความลิงก์ไปหน้าแพ็กเกจฉบับเต็ม', 'text', $rule ),
			),
		),

		'ea2000_faq' => array(
			'title'       => '10) บล็อก 8 · คำถามที่พบบ่อย (query log)',
			'description' => 'มีช่องให้ 10 ข้อ ข้อไหนเว้นว่างไว้จะไม่แสดงผล · ทุกข้อที่กรอกจะถูกส่งให้ Google เป็นข้อมูลโครงสร้าง FAQPage อัตโนมัติ ตอบตามจริง ไม่อ้างผลกำไร',
			'fields'      => array(
				'show_faq'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'faq_kicker'   => array( 'ป้ายบท (แสดงบนรางเลขบทและหัวบท)', 'text', $rule ),
				'faq_title'    => array( 'หัวข้อ', 'text' ),
				'faq_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			),
		),

		'ea2000_risk' => array(
			'title'       => '11) บล็อก 9 · ประกาศความเสี่ยง (แถบเตือน)',
			'description' => 'ส่วนนี้สำคัญต่อความน่าเชื่อถือของแบรนด์ ไม่แนะนำให้ปิด และห้ามลดทอนข้อความ · บรรทัดเตือนสั้นจะแสดงใต้ตัวเลือกแพ็กเกจด้วย',
			'fields'      => array(
				'show_risk'        => array( 'แสดงส่วนนี้', 'checkbox' ),
				'risk_kicker'      => array( 'ป้ายบท (แสดงบนรางเลขบทและแถบเตือน)', 'text', $rule ),
				'risk_label'       => array( 'ตราประทับหน้าป้ายบท', 'text', 'ค่าเริ่มต้น NOTICE (คงตัวพิมพ์ตามที่กรอก) · เว้นว่างเพื่อซ่อน' ),
				'risk_title'       => array( 'หัวข้อ', 'text' ),
				'risk_text'        => array( 'ข้อความคำเตือน', 'textarea' ),
				'risk_more_text'   => array( 'ข้อความปุ่มไปหน้าประกาศฉบับเต็ม (/risk-disclosure/)', 'text', $rule ),
				'risk_margin_note' => array( 'บรรทัดเตือนสั้นใต้ตัวเลือกแพ็กเกจ', 'text', 'ห้ามลดทอนความหมาย · ' . $rule ),
			),
		),

		'ea2000_cta' => array(
			'title'       => '12) ข้อความสำรองของ launch console',
			'description' => 'ข้อความชุดนี้เป็นค่าสำรองของหัวข้อใน launch console ของแถบท้ายเว็บ',
			'fields'      => array(
				'cta_title'    => array( 'หัวข้อ', 'text' ),
				'cta_subtitle' => array( 'คำอธิบาย', 'textarea' ),
			),
		),

		'ea2000_footer' => array(
			'title'       => '13) Footer ท้ายเว็บ (Console)',
			'description' => 'แถบท้ายเว็บ 5 แถว: เส้นสัญญาณ, launch console (ปุ่ม LINE + QR), ดัชนีหน้า, ลายน้ำ, แถบสถานะ · หัวข้อและคำอธิบายของ launch console เว้นว่างจะใช้ค่าจากหมวด 12 · เวลาตอบแชทเว้นว่างจะซ่อนแถวนั้น',
			'fields'      => array(
				'footer_console_label'  => array( 'บรรทัดนำเหนือหัวข้อ launch console', 'text', $rule ),
				'footer_headline'       => array( 'หัวข้อ launch console (เว้นว่าง = ใช้หัวข้อจากหมวด 12)', 'text', $rule ),
				'footer_sub'            => array( 'คำอธิบาย launch console (เว้นว่าง = ใช้คำอธิบายจากหมวด 12)', 'textarea', $rule ),
				'footer_line_text'      => array( 'ข้อความปุ่ม LINE (คีย์กด)', 'text' ),
				'footer_line_qr_img'    => array( 'QR ของ LINE OA', 'image', 'ขนาด 600x600 px พื้นขาว · โผล่ข้างปุ่มเมื่อชี้เมาส์ และเปิดดูได้บนมือถือ · เว้นว่างแอดมินจะเห็นข้อความเตือนให้อัปโหลด' ),
				'footer_line_qr_alt'    => array( 'ข้อความอธิบาย QR (Alt)', 'text', 'อ่านโดย screen reader และ Google Images · ' . $rule ),
				'footer_line_qr_note'   => array( 'ข้อความเตือนแอดมินเมื่อยังไม่มี QR', 'text' ),
				'footer_qr_toggle_text' => array( 'ข้อความปุ่มเปิด QR บนมือถือ', 'text', $rule ),
				'footer_facebook_text'  => array( 'ข้อความลิงก์ Facebook', 'text' ),
				'footer_email_text'     => array( 'ข้อความลิงก์ Email', 'text' ),
				'footer_prep_title'     => array( 'หัวข้อรายการเตรียมข้อมูลก่อนทัก LINE', 'text' ),
				'footer_prep_text'      => array( 'คำอธิบายรายการเตรียมข้อมูลก่อนทัก LINE', 'textarea' ),
				'footer_prep_items'     => array( 'รายการที่ควรเตรียม (บรรทัดละ 1 รายการ)', 'textarea' ),
				'footer_hours_title'    => array( 'เวลาตอบแชท · หัวข้อ', 'text', $rule ),
				'footer_hours_text'     => array( 'เวลาตอบแชท · ข้อความ (บรรทัดละ 1 บรรทัด)', 'textarea', 'เว้นว่าง = ซ่อนแถวนี้ · เจ้าของกรอกเอง ไม่มีสัญญาบริการฝังในธีม · ' . $rule ),
				'show_footer_console'   => array( 'แสดงบรรทัด prompt พิมพ์ทีละตัว', 'checkbox' ),
				'footer_console_prompt' => array( 'prompt ของบรรทัดพิมพ์', 'text', 'เช่น ea2000@line:~$ · ' . $rule ),
				'footer_console_lines'  => array( 'ข้อความที่พิมพ์วนสลับกัน (บรรทัดละ 1 ข้อความ)', 'textarea', $rule ),
				'footer_index_title'    => array( 'ดัชนี · หัวข้อคอลัมน์หน้า', 'text', $rule ),
				'footer_channels_title' => array( 'ดัชนี · หัวข้อคอลัมน์ช่องทาง', 'text', $rule ),
				'footer_docs_title'     => array( 'ดัชนี · หัวข้อคอลัมน์เอกสาร', 'text', $rule ),
				'footer_risk_link'      => array( 'ดัชนี · ข้อความลิงก์ประกาศความเสี่ยง', 'text' ),
				'footer_spec_title'     => array( 'ดัชนี · หัวข้อคอลัมน์ข้อมูลระบบ', 'text', $rule ),
				'footer_spec_items'     => array( 'ดัชนี · ข้อมูลระบบ (บรรทัดละ ป้าย|ค่า)', 'textarea', 'เช่น แพลตฟอร์ม|MetaTrader 5 · ' . $rule ),
				'show_footer_watermark' => array( 'แสดงลายน้ำชื่อแบรนด์ตัวใหญ่', 'checkbox' ),
				'footer_watermark_text' => array( 'ข้อความลายน้ำ', 'text', 'ส่วนก่อนตัวเลขตัวแรกเป็นสีเงิน ตั้งแต่ตัวเลขตัวแรกเป็นสีเขียว · ' . $rule ),
				'footer_status_text'    => array( 'แถบสถานะ · ข้อความกลาง', 'text', $rule ),
				'footer_copyright_text' => array( 'แถบสถานะ · ข้อความหลังชื่อเว็บและปี', 'text', $rule ),
				'show_footer_clock'     => array( 'แถบสถานะ · แสดงนาฬิกาเวลาไทย', 'checkbox' ),
				'footer_clock_label'    => array( 'แถบสถานะ · ป้ายหน้านาฬิกา', 'text', $rule ),
				'footer_backtop_text'   => array( 'แถบสถานะ · ข้อความลิงก์กลับด้านบน', 'text', $rule ),
				'show_footer_spotlight' => array( 'เปิดแสงตามเมาส์บนพื้นแถบท้ายเว็บ (เดสก์ท็อป)', 'checkbox' ),
				'show_footer_signal'    => array( 'แสดงเส้นสัญญาณวาดตัวเองบนขอบบน', 'checkbox' ),
			),
		),

		'ea2000_mobile_nav' => array(
			'title'       => '14) เมนูลัดมือถือด้านล่าง',
			'description' => 'เมนูลัดด้านล่างบนมือถือ ใช้สำหรับพาผู้เข้าชมไปยังหน้าหลักที่สำคัญและปุ่มทัก LINE',
			'fields'      => array(
				'show_mobile_nav'        => array( 'แสดงเมนูลัดด้านล่างบนมือถือ', 'checkbox' ),
				'mobile_nav_home_label'  => array( 'เมนู 1 · ชื่อ', 'text' ),
				'mobile_nav_home_url'    => array( 'เมนู 1 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_test_label'  => array( 'เมนู 2 · ชื่อ', 'text' ),
				'mobile_nav_test_url'    => array( 'เมนู 2 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_price_label'   => array( 'เมนู 3 · ชื่อ', 'text' ),
				'mobile_nav_price_url'     => array( 'เมนู 3 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_install_label' => array( 'เมนู 4 · ชื่อ', 'text' ),
				'mobile_nav_install_url'   => array( 'เมนู 4 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_line_label'    => array( 'เมนู 5 · ชื่อปุ่ม LINE', 'text' ),
			),
		),

		'ea2000_seo' => array(
			'title'       => '15) SEO / แชร์ลิงก์',
			'description' => 'รูปและคำโปรยเวลาแชร์ลิงก์ใน LINE/Facebook (Open Graph) บทความจะใช้ภาพหน้าปก (Featured image) อัตโนมัติ',
			'fields'      => array(
				'og_default_image'       => array( 'รูปสำหรับแชร์ (แนะนำ 1200×630px)', 'image' ),
				'og_default_description' => array( 'คำโปรยเวลาแชร์หน้าหลัก/หน้าที่ไม่มีภาพ', 'textarea' ),
				'search_console_verify'  => array( 'โค้ดยืนยัน Google Search Console (เฉพาะค่าใน content="...")', 'text' ),
				'bing_verify'            => array( 'โค้ดยืนยัน Bing Webmaster (msvalidate.01)', 'text' ),
			),
		),

		'ea2000_cookie' => array(
			'title'       => '16) คุกกี้ / Tracking',
			'description' => 'แถบขอความยินยอมคุกกี้ และโค้ดติดตาม (Google Analytics / Facebook Pixel จะโหลดเฉพาะหลังผู้ใช้กด "ยอมรับ" เท่านั้น)',
			'fields'      => array(
				'show_cookie_consent' => array( 'แสดงแถบขอความยินยอมคุกกี้', 'checkbox' ),
				'cookie_consent_text' => array( 'ข้อความบนแถบคุกกี้', 'textarea' ),
				'ga_measurement_id'   => array( 'Google Analytics 4 ID (เช่น G-XXXXXXXXXX)', 'text' ),
				'fb_pixel_id'         => array( 'Facebook Pixel ID', 'text' ),
			),
		),

		/* ===================================================
		 * หน้าย่อย (multipage) และ Link Hub
		 * =================================================== */

		'ea2000_backtest' => array(
			'title'       => '17) หน้า Backtest',
			'description' => 'กรอกผลการทดสอบย้อนหลังจริงเท่านั้น และอย่าลบ Disclaimer',
			'fields'      => array(
				'backtest_sub'         => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
				'backtest_intro'       => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
				'bt_stat1_label'       => array( 'สถิติ 1 · หัวข้อ', 'text' ),
				'bt_stat1_value'       => array( 'สถิติ 1 · ค่า', 'text' ),
				'bt_stat2_label'       => array( 'สถิติ 2 · หัวข้อ', 'text' ),
				'bt_stat2_value'       => array( 'สถิติ 2 · ค่า', 'text' ),
				'bt_stat3_label'       => array( 'สถิติ 3 · หัวข้อ', 'text' ),
				'bt_stat3_value'       => array( 'สถิติ 3 · ค่า', 'text' ),
				'bt_stat4_label'       => array( 'สถิติ 4 · หัวข้อ', 'text' ),
				'bt_stat4_value'       => array( 'สถิติ 4 · ค่า', 'text' ),
				'bt_stat5_label'       => array( 'สถิติ 5 · หัวข้อ', 'text' ),
				'bt_stat5_value'       => array( 'สถิติ 5 · ค่า', 'text' ),
				'bt_stat6_label'       => array( 'สถิติ 6 · หัวข้อ', 'text' ),
				'bt_stat6_value'       => array( 'สถิติ 6 · ค่า', 'text' ),
				'bt_stat7_label'       => array( 'สถิติ 7 · หัวข้อ', 'text' ),
				'bt_stat7_value'       => array( 'สถิติ 7 · ค่า', 'text' ),
				'bt_stat8_label'       => array( 'สถิติ 8 · หัวข้อ', 'text' ),
				'bt_stat8_value'       => array( 'สถิติ 8 · ค่า', 'text' ),
				'backtest_img'         => array( 'ภาพกราฟ/รายงานผล Backtest', 'image' ),
				'backtest_img_caption' => array( 'คำบรรยายภาพ', 'text' ),
				'backtest_note'        => array( 'หมายเหตุเงื่อนไขการทดสอบ', 'textarea' ),
				'backtest_disclaimer'  => array( 'Disclaimer (จำเป็น)', 'textarea' ),
			),
		),

		'ea2000_forward' => array(
			'title'       => '18) หน้า Forward Test',
			'description' => 'กรอกผลการทดสอบจริงจากบัญชีจริงหรือบัญชีเดโมเท่านั้น และอย่าลบ Disclaimer',
			'fields'      => array(
				'forward_sub'         => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
				'forward_intro'       => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
				'fw_stat1_label'      => array( 'สถิติ 1 · หัวข้อ', 'text' ),
				'fw_stat1_value'      => array( 'สถิติ 1 · ค่า', 'text' ),
				'fw_stat2_label'      => array( 'สถิติ 2 · หัวข้อ', 'text' ),
				'fw_stat2_value'      => array( 'สถิติ 2 · ค่า', 'text' ),
				'fw_stat3_label'      => array( 'สถิติ 3 · หัวข้อ', 'text' ),
				'fw_stat3_value'      => array( 'สถิติ 3 · ค่า', 'text' ),
				'fw_stat4_label'      => array( 'สถิติ 4 · หัวข้อ', 'text' ),
				'fw_stat4_value'      => array( 'สถิติ 4 · ค่า', 'text' ),
				'fw_stat5_label'      => array( 'สถิติ 5 · หัวข้อ', 'text' ),
				'fw_stat5_value'      => array( 'สถิติ 5 · ค่า', 'text' ),
				'fw_stat6_label'      => array( 'สถิติ 6 · หัวข้อ', 'text' ),
				'fw_stat6_value'      => array( 'สถิติ 6 · ค่า', 'text' ),
				'forward_img'         => array( 'ภาพผลการทดสอบ', 'image' ),
				'forward_img_caption' => array( 'คำบรรยายภาพ', 'text' ),
				'forward_link_label'  => array( 'ข้อความปุ่มลิงก์ผลเรียลไทม์ (ถ้ามี)', 'text' ),
				'forward_link_url'    => array( 'ลิงก์ผลเรียลไทม์ที่ตรวจสอบได้ (แสดงปุ่มเมื่อกรอกเท่านั้น)', 'url' ),
				'forward_note'        => array( 'หมายเหตุ', 'textarea' ),
				'forward_disclaimer'  => array( 'Disclaimer (จำเป็น)', 'textarea' ),
			),
		),

		'ea2000_install' => array(
			'title'       => '19) หน้า How to Install',
			'description' => 'คู่มือติดตั้งทีละขั้นตอน อัปโหลดภาพประกอบแต่ละขั้นได้ (เว้นว่างได้) · "ย่อหน้าเกริ่นนำ" ใช้ร่วมกับบล็อกติดตั้งใน 3 ขั้นบนหน้าแรก',
			'fields'      => array(
				'install_sub'      => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
				'install_intro'    => array( 'ย่อหน้าเกริ่นนำ (ใช้ร่วมกับหน้าแรก)', 'textarea' ),
				'install_req'      => array( 'สิ่งที่ต้องเตรียม (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'inst_step1_title' => array( 'ขั้นตอน 1 · หัวข้อ', 'text' ),
				'inst_step1_desc'  => array( 'ขั้นตอน 1 · รายละเอียด', 'textarea' ),
				'inst_step1_img'   => array( 'ขั้นตอน 1 · ภาพ', 'image' ),
				'inst_step2_title' => array( 'ขั้นตอน 2 · หัวข้อ', 'text' ),
				'inst_step2_desc'  => array( 'ขั้นตอน 2 · รายละเอียด', 'textarea' ),
				'inst_step2_img'   => array( 'ขั้นตอน 2 · ภาพ', 'image' ),
				'inst_step3_title' => array( 'ขั้นตอน 3 · หัวข้อ', 'text' ),
				'inst_step3_desc'  => array( 'ขั้นตอน 3 · รายละเอียด', 'textarea' ),
				'inst_step3_img'   => array( 'ขั้นตอน 3 · ภาพ', 'image' ),
				'inst_step4_title' => array( 'ขั้นตอน 4 · หัวข้อ', 'text' ),
				'inst_step4_desc'  => array( 'ขั้นตอน 4 · รายละเอียด', 'textarea' ),
				'inst_step4_img'   => array( 'ขั้นตอน 4 · ภาพ', 'image' ),
				'inst_step5_title' => array( 'ขั้นตอน 5 · หัวข้อ', 'text' ),
				'inst_step5_desc'  => array( 'ขั้นตอน 5 · รายละเอียด', 'textarea' ),
				'inst_step5_img'   => array( 'ขั้นตอน 5 · ภาพ', 'image' ),
				'inst_step6_title' => array( 'ขั้นตอน 6 · หัวข้อ', 'text' ),
				'inst_step6_desc'  => array( 'ขั้นตอน 6 · รายละเอียด', 'textarea' ),
				'inst_step6_img'   => array( 'ขั้นตอน 6 · ภาพ', 'image' ),
				'inst_step1_img_alt'  => array( 'ขั้นที่ 1 · ข้อความแทนภาพ (Alt)', 'text' ),
				'inst_step1_img_note' => array( 'ขั้นที่ 1 · ข้อความในช่องรอรูป', 'text' ),
				'inst_step1_img_caption' => array( 'ขั้นที่ 1 · คำบรรยายใต้ภาพ', 'text' ),
				'inst_step2_img_alt'  => array( 'ขั้นที่ 2 · ข้อความแทนภาพ (Alt)', 'text' ),
				'inst_step2_img_note' => array( 'ขั้นที่ 2 · ข้อความในช่องรอรูป', 'text' ),
				'inst_step2_img_caption' => array( 'ขั้นที่ 2 · คำบรรยายใต้ภาพ', 'text' ),
				'inst_step3_img_alt'  => array( 'ขั้นที่ 3 · ข้อความแทนภาพ (Alt)', 'text' ),
				'inst_step3_img_note' => array( 'ขั้นที่ 3 · ข้อความในช่องรอรูป', 'text' ),
				'inst_step3_img_caption' => array( 'ขั้นที่ 3 · คำบรรยายใต้ภาพ', 'text' ),
				'inst_step4_img_alt'  => array( 'ขั้นที่ 4 · ข้อความแทนภาพ (Alt)', 'text' ),
				'inst_step4_img_note' => array( 'ขั้นที่ 4 · ข้อความในช่องรอรูป', 'text' ),
				'inst_step4_img_caption' => array( 'ขั้นที่ 4 · คำบรรยายใต้ภาพ', 'text' ),
				'inst_step5_img_alt'  => array( 'ขั้นที่ 5 · ข้อความแทนภาพ (Alt)', 'text' ),
				'inst_step5_img_note' => array( 'ขั้นที่ 5 · ข้อความในช่องรอรูป', 'text' ),
				'inst_step5_img_caption' => array( 'ขั้นที่ 5 · คำบรรยายใต้ภาพ', 'text' ),
				'inst_step6_img_alt'  => array( 'ขั้นที่ 6 · ข้อความแทนภาพ (Alt)', 'text' ),
				'inst_step6_img_note' => array( 'ขั้นที่ 6 · ข้อความในช่องรอรูป', 'text' ),
				'inst_step6_img_caption' => array( 'ขั้นที่ 6 · คำบรรยายใต้ภาพ', 'text' ),
				'install_note'     => array( 'ข้อความปิดท้าย', 'text' ),
			),
		),

		'ea2000_pricing_extra' => array(
			'title'       => '20) หน้า Pricing (เพิ่มเติม)',
			'description' => 'หน้านี้ใช้แพ็กเกจจากหมวด "แพ็กเกจราคา" ร่วมกัน และเพิ่มตารางเปรียบเทียบได้ที่นี่',
			'fields'      => array(
				'pricing_sub'   => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
				'compare_title' => array( 'หัวข้อตารางเปรียบเทียบ', 'text' ),
				'compare_rows'  => array( 'ตารางเปรียบเทียบ (บรรทัดละ 1 แถว คั่นช่องด้วย | บรรทัดแรกคือหัวตาราง ใช้ ✓ และ ✗ ได้)', 'textarea' ),
			),
		),

		'ea2000_riskpage' => array(
			'title'       => '21) หน้า Risk Disclosure',
			'description' => 'หน้าประกาศความเสี่ยงฉบับเต็ม มี 6 หัวข้อ เว้นว่างหัวข้อที่ไม่ใช้ได้',
			'fields'      => array(
				'riskpage_sub'     => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
				'riskpage_intro'   => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
				'riskpage_image'   => array( 'ภาพประกอบความเสี่ยง', 'image' ),
				'riskpage_image_caption' => array( 'คำบรรยายภาพประกอบความเสี่ยง', 'text' ),
				'rp_block1_title'  => array( 'หัวข้อ 1', 'text' ),
				'rp_block1_text'   => array( 'เนื้อหา 1', 'textarea' ),
				'rp_block2_title'  => array( 'หัวข้อ 2', 'text' ),
				'rp_block2_text'   => array( 'เนื้อหา 2', 'textarea' ),
				'rp_block3_title'  => array( 'หัวข้อ 3', 'text' ),
				'rp_block3_text'   => array( 'เนื้อหา 3', 'textarea' ),
				'rp_block4_title'  => array( 'หัวข้อ 4', 'text' ),
				'rp_block4_text'   => array( 'เนื้อหา 4', 'textarea' ),
				'rp_block5_title'  => array( 'หัวข้อ 5', 'text' ),
				'rp_block5_text'   => array( 'เนื้อหา 5', 'textarea' ),
				'rp_block6_title'  => array( 'หัวข้อ 6', 'text' ),
				'rp_block6_text'   => array( 'เนื้อหา 6', 'textarea' ),
				'riskpage_updated' => array( 'วันที่ปรับปรุงล่าสุด', 'text' ),
			),
		),

		'ea2000_links' => array(
			'title'       => '22) หน้า Link Hub (สำหรับยิงแอด)',
			'description' => 'หน้า "ลิงก์รวม" สไตล์ Linktree สำหรับใช้เป็นปลายทางยิงแอด: สร้างเพจใหม่ เลือกเทมเพลต "EA2000 · หน้า Link Hub" แล้วตั้ง slug เช่น go ปุ่มที่เว้นว่าง (ทั้งข้อความและลิงก์) จะถูกซ่อนอัตโนมัติ',
			'fields'      => array(
				'links_logo'       => array( 'โลโก้ (ไม่ใส่ = ใช้โลโก้เว็บ)', 'image' ),
				'links_title'      => array( 'ชื่อแบรนด์', 'text' ),
				'links_tagline'    => array( 'คำโปรยใต้ชื่อ (เว้นบรรทัด = ขึ้นบรรทัดใหม่)', 'textarea' ),
				'links_badges'     => array( 'ป้ายเล็ก (บรรทัดละ 1 ป้าย)', 'textarea' ),
				'links_signup_label' => array( 'ข้อความปุ่มสมัคร', 'text' ),
				'links_signup_url'   => array( 'ลิงก์ปุ่มสมัคร (เว้นว่าง = ซ่อนปุ่ม · ใส่ /pricing/ หรือ URL เต็ม)', 'text' ),
				'links_account_guide_label' => array( 'ข้อความปุ่มสอนเปิดบัญชี', 'text' ),
				'links_account_guide_url'   => array( 'ลิงก์ปุ่มสอนเปิดบัญชี (เว้นว่าง = ซ่อนปุ่ม)', 'text' ),
				'links_mt5_download_label'  => array( 'ข้อความปุ่มโหลด MT5', 'text' ),
				'links_mt5_download_url'    => array( 'ลิงก์ปุ่มโหลด MT5 (เว้นว่าง = ซ่อนปุ่ม)', 'text' ),
				'links_line_label' => array( 'ข้อความปุ่ม LINE (ปุ่มหลัก)', 'text', 'ลิงก์ใช้ค่า LINE OA จากหมวด 1 อัตโนมัติ' ),
				'links_openchat_label' => array( 'ข้อความปุ่ม LINE OpenChat', 'text' ),
				'links_openchat_url'   => array( 'ลิงก์ LINE OpenChat (เว้นว่าง = ซ่อนปุ่ม)', 'url' ),
				'links_fast_enabled' => array( 'แสดงการ์ดดาวน์โหลด EA แทนรูปโชว์ทั้งสองใบ', 'checkbox' ),
				'links_fast_img'     => array( 'การ์ดดาวน์โหลด EA · รูปการ์ด', 'image', 'แนะนำภาพแนวนอน 1200×630px' ),
				'links_fast_url'     => array( 'การ์ดดาวน์โหลด EA · ลิงก์ไฟล์ดาวน์โหลด (เว้นว่าง = แสดงการ์ดโดยไม่มีลิงก์)', 'text' ),
				'links_fast_alt'     => array( 'การ์ดดาวน์โหลด EA · ข้อความอธิบายรูป (Alt)', 'text' ),
				'links_feature_img'     => array( 'รูปโชว์ (ใต้ปุ่ม LINE) · ไม่ใส่ = ใช้รูปดาวน์โหลดค่าเริ่มต้น', 'image', 'แนะนำภาพแนวนอน 1200×630px สำหรับ Link Hub' ),
				'links_feature_url'     => array( 'รูปโชว์ · ลิงก์เมื่อคลิก (เว้นว่าง = แสดงรูปโดยไม่มีลิงก์)', 'text' ),
				'links_feature_caption' => array( 'รูปโชว์ · ข้อความใต้รูป (ถ้ามี)', 'text' ),
				'links_feature2_img'     => array( 'รูปโชว์ 2 (เว้นว่าง = แสดงโครงรอใส่รูป)', 'image', 'แนะนำภาพแนวนอน 1200×630px' ),
				'links_feature2_url'     => array( 'รูปโชว์ 2 · ลิงก์เมื่อคลิก', 'text' ),
				'links_feature2_caption' => array( 'รูปโชว์ 2 · ข้อความใต้รูป (ถ้ามี)', 'text' ),
				'links_feature2_placeholder' => array( 'รูปโชว์ 2 · ข้อความในโครงรูป', 'text' ),
				'links_btn1_label' => array( 'ปุ่ม 1 · ข้อความ', 'text' ),
				'links_btn1_url'   => array( 'ปุ่ม 1 · ลิงก์ (ใส่ slug เช่น /forward-test/ หรือ URL เต็ม)', 'text' ),
				'links_btn2_label' => array( 'ปุ่ม 2 · ข้อความ', 'text' ),
				'links_btn2_url'   => array( 'ปุ่ม 2 · ลิงก์', 'text' ),
				'links_btn3_label' => array( 'ปุ่ม 3 · ข้อความ', 'text' ),
				'links_btn3_url'   => array( 'ปุ่ม 3 · ลิงก์', 'text' ),
				'links_btn4_label' => array( 'ปุ่ม 4 · ข้อความ', 'text' ),
				'links_btn4_url'   => array( 'ปุ่ม 4 · ลิงก์', 'text' ),
				'links_btn5_label' => array( 'ปุ่ม 5 · ข้อความ (เว้นว่าง = ซ่อน)', 'text' ),
				'links_btn5_url'   => array( 'ปุ่ม 5 · ลิงก์', 'text' ),
				'links_btn6_label' => array( 'ปุ่ม 6 · ข้อความ (เว้นว่าง = ซ่อน)', 'text' ),
				'links_btn6_url'   => array( 'ปุ่ม 6 · ลิงก์', 'text' ),
				'links_guide1_label' => array( 'คู่มือ 1 · ข้อความ', 'text' ),
				'links_guide1_url'   => array( 'คู่มือ 1 · ลิงก์', 'text' ),
				'links_guide2_label' => array( 'คู่มือ 2 · ข้อความ', 'text' ),
				'links_guide2_url'   => array( 'คู่มือ 2 · ลิงก์', 'text' ),
				'links_guide3_label' => array( 'คู่มือ 3 · ข้อความ', 'text' ),
				'links_guide3_url'   => array( 'คู่มือ 3 · ลิงก์', 'text' ),
				'links_guide4_label' => array( 'คู่มือ 4 · ข้อความ', 'text' ),
				'links_guide4_url'   => array( 'คู่มือ 4 · ลิงก์', 'text' ),
				'links_guide5_label' => array( 'คู่มือ 5 · ข้อความ', 'text' ),
				'links_guide5_url'   => array( 'คู่มือ 5 · ลิงก์', 'text' ),
				'links_note'       => array( 'ข้อความเตือนความเสี่ยง (ด้านล่างสุด)', 'textarea' ),
			),
		),
	);

	/* หน้าคู่มือการใช้งาน 5 หน้า (inc/guide-pages.php) */
	if ( function_exists( 'ea2000_guide_fields' ) ) {
		$ea2000_guides = array(
			'gvwin' => '23) หน้าคู่มือ · VPS บน Windows',
			'gvand' => '24) หน้าคู่มือ · VPS บน Android',
			'gvios' => '25) หน้าคู่มือ · VPS บน iPhone',
			'gmt5'  => '26) หน้าคู่มือ · ติดตั้ง MT5 และล็อกอิน',
			'gacct' => '27) หน้าคู่มือ · เปิดบัญชี MT5',
		);
		foreach ( $ea2000_guides as $ea2000_gp => $ea2000_gtitle ) {
			$sections[ 'ea2000_guide_' . $ea2000_gp ] = array(
				'title'       => $ea2000_gtitle,
				'description' => 'เนื้อหาคู่มือทีละขั้น · ช่องรูปที่เว้นว่างจะแสดงกรอบบอกว่าต้องใส่ภาพอะไร',
				'fields'      => ea2000_guide_fields( $ea2000_gp ),
			);
		}
	}

	/* เนื้อหาหัวข้อของหน้าย่อย (inc/page-content.php) */
	if ( function_exists( 'ea2000_page_section_fields' ) ) {
		$ea2000_docs = array(
			'ea2000_backtest'      => array( 'backtest', 6 ),
			'ea2000_forward'       => array( 'forward', 6 ),
			'ea2000_pricing_extra' => array( 'pricingdoc', 5 ),
			'ea2000_install'       => array( 'installdoc', 8 ),
			'ea2000_riskpage'      => array( 'riskdoc', 6 ),
			'ea2000_links'         => array( 'linksdoc', 4 ),
		);
		foreach ( $ea2000_docs as $ea2000_sec => $ea2000_doc ) {
			if ( isset( $sections[ $ea2000_sec ] ) ) {
				$sections[ $ea2000_sec ]['fields'] = array_merge(
					$sections[ $ea2000_sec ]['fields'],
					ea2000_page_section_fields( $ea2000_doc[0], $ea2000_doc[1] )
				);
			}
		}
	}

	/* เพิ่ม FAQ 10 ข้อเข้า section FAQ */
	for ( $i = 1; $i <= 10; $i++ ) {
		$sections['ea2000_faq']['fields'][ 'faq' . $i . '_q' ] = array( 'คำถามข้อ ' . $i, 'text' );
		$sections['ea2000_faq']['fields'][ 'faq' . $i . '_a' ] = array( 'คำตอบข้อ ' . $i, 'textarea' );
	}

	$priority = 10;

	foreach ( $sections as $section_id => $section ) {

		$wp_customize->add_section(
			$section_id,
			array(
				'panel'       => 'ea2000_panel',
				'title'       => $section['title'],
				'description' => isset( $section['description'] ) ? $section['description'] : '',
				'priority'    => $priority,
			)
		);
		$priority += 10;

		foreach ( $section['fields'] as $field_id => $field ) {

			$label = $field[0];
			$type  = $field[1];
			$extra = isset( $field[2] ) ? $field[2] : '';

			switch ( $type ) {
				case 'checkbox':
					$sanitize = 'ea2000_sanitize_checkbox';
					break;
				case 'url':
				case 'image':
					$sanitize = 'esc_url_raw';
					break;
				case 'textarea':
					$sanitize = 'sanitize_textarea_field';
					break;
				case 'radio':
					$sanitize = 'ea2000_sanitize_pricing_mode';
					break;
				default:
					$sanitize = 'sanitize_text_field';
			}

			$wp_customize->add_setting(
				$field_id,
				array(
					'default'           => isset( $d[ $field_id ] ) ? $d[ $field_id ] : '',
					'type'              => 'theme_mod',
					'sanitize_callback' => $sanitize,
				)
			);

			if ( 'image' === $type ) {
				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize,
						$field_id,
						array(
							'label'       => $label,
							'section'     => $section_id,
							'description' => is_string( $extra ) ? $extra : '',
						)
					)
				);
			} elseif ( 'radio' === $type ) {
				$wp_customize->add_control(
					$field_id,
					array(
						'label'   => $label,
						'section' => $section_id,
						'type'    => 'radio',
						'choices' => is_array( $extra ) ? $extra : array(),
					)
				);
			} else {
				$wp_customize->add_control(
					$field_id,
					array(
						'label'       => $label,
						'section'     => $section_id,
						'type'        => $type,
						'description' => is_string( $extra ) ? $extra : '',
					)
				);
			}
		}
	}
}
add_action( 'customize_register', 'ea2000_customize_register' );
