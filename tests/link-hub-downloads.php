<?php
/**
 * EA2000 · ตรวจหน้า Link Hub และไฟล์ในธีม
 *
 * Run with: php tests/link-hub-downloads.php (จาก root ของ repo)
 *
 * ตรวจว่า:
 * 1. template-links.php ไม่อ้างถึงแบรนด์ต้นทาง โบรกเกอร์เก่า หรือโฟลเดอร์ assets/downloads
 * 2. รูปการ์ดดาวน์โหลด placeholder ของ EA2000 มีอยู่จริงและเป็น PNG
 * 3. ไม่มีไฟล์ .zip หรือ .ex5 อยู่ใต้ ea2000/ (ไฟล์ EA ต้องไม่อยู่ใน repo)
 *
 * สคริปต์นี้ไม่โหลด WordPress หรือ functions.php ของธีม จึงรันได้ด้วย PHP CLI เปล่า ๆ
 */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

$theme_dir = dirname( __DIR__ ) . '/ea2000';
$failures  = 0;

function check( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures++;
		fwrite( STDERR, "FAIL: $message\n" );
		return;
	}
	echo "PASS: $message\n";
}

check( is_dir( $theme_dir ), 'theme folder ea2000/ exists' );

/* 1) template-links.php ต้องไม่มีร่องรอยของธีมต้นทาง
 * ชื่อแบรนด์เก่าถูกต่อ string จากสองส่วน เพื่อให้ grep ตรวจร่องรอยทั้ง repo ไม่เจอไฟล์ทดสอบนี้ */
$legacy_tokens = array(
	'fe' . 'nix',
	'zau' . 'rix',
	'assets/downloads',
);

$template = $theme_dir . '/template-links.php';
check( is_file( $template ), 'template-links.php exists' );

$source = is_file( $template ) ? (string) file_get_contents( $template ) : '';
check( '' !== $source, 'template-links.php is not empty' );

foreach ( $legacy_tokens as $token ) {
	$hits = array();
	foreach ( explode( "\n", $source ) as $index => $line ) {
		if ( false !== stripos( $line, $token ) ) {
			$hits[] = 'line ' . ( $index + 1 );
		}
	}
	check( empty( $hits ), 'template-links.php has no reference to "' . $token . '"' . ( $hits ? ' (' . implode( ', ', $hits ) . ')' : '' ) );
}

/* 2) รูปการ์ดดาวน์โหลด placeholder ของ EA2000 */
$card = $theme_dir . '/assets/img/card-download.webp';
check( is_file( $card ) && filesize( $card ) > 0, 'placeholder download card assets/img/card-download.webp exists' );

$card_header = is_file( $card ) ? (string) file_get_contents( $card, false, null, 0, 12 ) : '';
$is_png      = 0 === strpos( $card_header, "\x89PNG" );
$is_webp     = 0 === strpos( $card_header, 'RIFF' ) && 'WEBP' === substr( $card_header, 8, 4 );
check( $is_png || $is_webp, 'download card image is a valid PNG or WebP file' );

/* 3) ต้องไม่มีไฟล์ EA หรือ archive ใต้ ea2000/ */
$found = array();
if ( is_dir( $theme_dir ) ) {
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $theme_dir, FilesystemIterator::SKIP_DOTS )
	);
	foreach ( $iterator as $file ) {
		$ext = strtolower( pathinfo( $file->getFilename(), PATHINFO_EXTENSION ) );
		if ( in_array( $ext, array( 'zip', 'ex5' ), true ) ) {
			$found[] = str_replace( '\\', '/', substr( $file->getPathname(), strlen( $theme_dir ) + 1 ) );
		}
	}
}
check( empty( $found ), 'no .zip or .ex5 file under ea2000/' . ( $found ? ' (found: ' . implode( ', ', $found ) . ')' : '' ) );

if ( $failures ) {
	fwrite( STDERR, "$failures check(s) failed\n" );
	exit( 1 );
}
echo "All checks passed\n";
