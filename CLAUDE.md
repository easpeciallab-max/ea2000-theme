# CLAUDE.md · EA2000 (ea2000.co)

ไฟล์นี้คือบริบทตั้งต้นของโปรเจกต์ EA2000 เขียนเมื่อ 7 กันยายน 2026 จากผลสำรวจแบบอ่านอย่างเดียว อ่านให้ครบก่อนเริ่มงาน

## 1) โปรเจกต์นี้คืออะไร
- แบรนด์ใหม่ชื่อ **EA2000** · โดเมน **https://ea2000.co/** · ภาษาไทย · เป้าหมายเดียวกับ FENIX PRO: หน้า landing ขาย EA (MetaTrader 5) ให้คนทัก LINE
- สร้างโดย **โคลนธีม FENIX PRO แล้วรีแบรนด์** (ไม่เขียนธีมใหม่) ต้นทางอยู่ที่ `D:\EA VIDEO\fenix-pro-repo\fenix-pro` (อ่านอย่างเดียว)
- **ห้ามแตะ** repo/เว็บ FENIX (`fenix-pro-repo`, github easpeciallab-max/fenix-pro-theme, fenixpro-th.com) และ repo `falcon-pro-repo`, `easpecial-repo` ทุกกรณี
- โฟลเดอร์งานนี้: `D:\EA VIDEO\ea2000-repo` · GitHub repo ใหม่ (เจ้าของเป็นคนสร้าง ชื่อแนะนำ `ea2000-theme`) · ธีมอยู่ใน subfolder `ea2000/`

## 2) สถานะ ea2000.co ตอนนี้ (สำรวจ 7 ก.ย. 2026)
- เป็น WordPress 7.1 / PHP 8.3 ที่รันธีมเก่า `easpecial` v0.4.0 ("EA Special" เว็บรวม EA หลายตัว โทนเขียว-ขาว) ซึ่ง**จะถูกแทนที่**ด้วยธีม EA2000
- โครงเปล่า: 0 โพสต์, 0 สินค้า (CPT `ea_product`), หน้า results/guides/articles เป็น placeholder, ตั้ง `noindex,nofollow` ทั้งเว็บ, ไม่มีโลโก้/og:image, privacy policy เป็นข้อความ default อังกฤษ
- ปุ่ม LINE ยังชี้ `@fenixpro` (ของ FENIX) ต้องเปลี่ยนเป็น LINE OA ของ EA2000
- ปลั๊กอินที่เห็น: Yoast SEO, Elementor (ฟรี), Site Kit, PixelYourSite (ยังไม่มี pixel id), Cloudflare อยู่หน้าเว็บ, origin ส่ง header แบบ LiteSpeed/Nginx cache
- หน้าที่มีอยู่: /, /ea-products/, /results/, /guides/, /articles/, /about/, /risk-warning/, /privacy-policy/, /data-deletion/ · ต้องตัดสินใจกับเจ้าของว่าจะลบ/รีไดเรกต์อะไรตอนเปลี่ยนธีม

## 3) หลักการธีม (สืบทอดจาก FENIX ห้ามทำผิด)
1. **Customizer-driven**: ทุกข้อความ/รูป/ราคา/ลิงก์ ต้องเป็น setting (`fenix_defaults()` + `inc/customizer.php` loop) ห้าม hardcode ใน template
2. **ห้ามแต่งรีวิวปลอม** (`show_reviews` ปิดจนมีรีวิวจริง) · **ห้ามใส่ตัวเลขผลทดสอบสมมติ** (ปล่อยเป็น placeholder ให้เจ้าของกรอก)
3. **ห้ามลบหรือลดทอน** disclaimer / risk warning
4. อังกฤษพิมพ์ใหญ่ด้วย CSS (`text-transform: uppercase` บน body ยกเว้น `.keep-case`)
5. Escape ทุก output (`esc_html/esc_url/esc_attr`)
6. ไม่มี build step · WP 6.0+ · แนะนำ PHP 8.1+
7. ห้ามใช้ em dash / en dash ในโค้ด เนื้อหา และแชท ใช้ `·` หรือ `:` แทน

## 4) เช็กลิสต์รีแบรนด์ (ต้องเสร็จก่อน live) · อ้างอิงบรรทัดในธีม FENIX ณ commit 5fbb811
**อันตรายสูง ทำก่อน**
- `functions.php:213` token ยืนยัน Google Search Console ของ FENIX ฝังเป็น default และ `fenix_mod()` บังคับใช้ค่านี้เมื่อค่าว่าง (`:723-725`) → ตั้ง default เป็น `''` และ**ลบ fallback นี้ทิ้ง**
- `functions.php:660-735` ตาราง migration + fallback ของ LINE/โซเชียล (`:727-733`) ที่เด้งกลับค่าของ FENIX เมื่อเว้นว่าง → ลบ/เขียนใหม่ทั้งบล็อกให้เป็นของ EA2000 หรือไม่มี fallback
- ไฟล์ EA ของ FENIX `assets/downloads/FENIX_Fast_V4.0.zip` และ `fenix-pro-ea.zip` **ห้ามนำมา** และแก้ `.gitignore` ที่ whitelist ชื่อไฟล์นี้ (`*.zip` + `!...FENIX_Fast_V4.0.zip`)
- ลิงก์ Zaurix ref ของ FENIX (`:222`) และ Myfxbook fenix-smart-core (`:242`) → ใช้ลิงก์ของ EA2000 หรือเว้นว่างแล้วซ่อนปุ่ม

**แบรนด์ที่ฝังในโค้ด**
- `header.php:24`, `footer.php:63` ชื่อแบรนด์ hardcode "FENIX PRO EA for MT5" → ทำเป็น setting
- `functions.php:851-921` เมนูสำรอง + `wp_nav_menu_items` มี slug/label FENIX และ Zaurix → เขียนใหม่
- `fenix_defaults()` 387 ค่า: 34 ค่ามีคำ FENIX, LINE/โซเชียล/อีเมล/OG description/hero_title/links_fast_* → เขียน default ทั้งหมดใหม่เป็น EA2000
- `style.css:2-13` Theme Name/Author/Description/Text Domain → EA2000 · โทนสี `:root` (ember/flare/gold) เปลี่ยนตามแบรนด์ใหม่เมื่อเจ้าของกำหนดสี
- `assets/img/logo.png`, `logo-128.png`, `screenshot.png`, `img/install/step-01..06.jpg` (มีโลโก้ FENIX), รูปการ์ดดาวน์โหลด → เปลี่ยนทั้งหมด
- ชื่อ Template `FENIX · ...` ใน template-*.php 10 ไฟล์ · ชื่อ panel/section ใน `inc/customizer.php` (`:32`, `:94`, `:565`) · `readme.txt` เขียนใหม่
- `tests/link-hub-downloads.php` ผูกกับ FENIX FAST → เขียนใหม่ตามของ EA2000
- REST namespace `fenix/v1` (authcheck, mods) → เปลี่ยนเป็น `ea2000/v1` · prefix ฟังก์ชัน `fenix_` **เก็บไว้ได้** (ผู้ใช้ไม่เห็น เปลี่ยนแล้วเสี่ยงพัง ~900 จุด)
- **ไม่นำมา**: `content-drafts/`, `elementor-templates/`, `launch-content/`, `CLAUDE-HANDOFF.md`, `AGENTS.md`, `CLAUDE.md` ของ FENIX, บทความ 50 บท (duplicate content)

**ตรวจจบ**: `grep -ri "fenix\|fenixpro\|zaurix\|speccub\|@fenixpro"` ใน repo ใหม่ต้องเหลือเฉพาะ prefix `fenix_` ของโค้ด · `php -l` ทุกไฟล์ผ่าน

## 5) กันไม่ให้ปนกับ FENIX
- ก่อน `git push` ทุกครั้ง: `git remote -v` ต้องเป็น repo EA2000 และ `git rev-parse --show-toplevel` ต้องอยู่ใน `ea2000-repo`
- รหัส WordPress ของ EA2000 เก็บที่ `~/.ea2000-wp.env` เท่านั้น (WP_URL / WP_USER / WP_APP_PASSWORD) ห้ามอ่านหรืออ้าง `~/.fenix-wp.env`
- WP Pusher ของ ea2000.co ชี้ repo EA2000 · subdirectory `ea2000` · **ปิด Push-to-Deploy** จนตรวจรอบแรกผ่าน
- ไม่ restore backup/All-in-One export ของ FENIX ลงเว็บนี้
- อย่าเปิด template page ด้วย Elementor

## 6) เครื่องมือในเครื่อง
- PHP CLI (ไม่อยู่บน PATH): `C:\Users\THANAWUT HR\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe` · lint: `"<path>" -l <file>` ก่อน commit ทุกครั้ง
- Node มีบน PATH · Git Bash และ PowerShell 5.1
- API WordPress: บนโฮสต์ FENIX header `Authorization` ถูกตัด ต้องส่ง `X-Authorization` แทน (ธีมมี shim) · บน ea2000.co ให้ทดสอบด้วย `GET /wp-json/ea2000/v1/authcheck` ก่อน อย่าสรุปว่าเหมือนกัน · ทดสอบ REST ให้ใส่ query กันแคชและ `Cache-Control: no-cache` เสมอ · payload ภาษาไทยส่งจากไฟล์ UTF-8 (`--data-binary @file`) ห้ามใส่ inline

## 7) ลำดับงาน
| ขั้น | ใคร | งาน |
|---|---|---|
| 1 | เจ้าของ | ยืนยันโลโก้ สี LINE OA ใหม่ โซเชียลใหม่ · สร้าง GitHub repo เปล่า · ยืนยันว่าใช้ WordPress เดิมที่ ea2000.co |
| 2 | Claude | สร้าง repo ในโฟลเดอร์นี้ ก๊อปเฉพาะไฟล์ธีมจาก FENIX เข้า `ea2000/` เขียน readme · `git init` · ตั้ง remote ตามที่เจ้าของให้ · ยังไม่ push |
| 3 | Claude | รีแบรนด์ตามข้อ 4 ทั้งหมด · lint · grep ตรวจ 0 ร่องรอย · commit |
| 4 | เจ้าของ | ติดตั้ง/ตั้ง WP Pusher บน ea2000.co ชี้ repo ใหม่ (Push-to-Deploy ปิด) · สร้าง Application Password · เขียนไฟล์ `~/.ea2000-wp.env` เอง |
| 5 | Claude | push รอบแรก → เจ้าของกด Update/Activate ธีม → Claude สร้างเพจ+เทมเพลต (slug: backtest, forward-test, pricing, how-to-install, risk-disclosure, go) กรอก Customizer ผ่าน API ตรวจหน้าเว็บจริงแบบไม่ล็อกอิน |
| 6 | เจ้าของ | GA4 + GSC property ใหม่ · Yoast/Site Kit ตั้งใหม่ · เอา noindex ออกเมื่อพร้อม → Claude ใส่ค่า ตรวจว่ามี google-site-verification แค่แท็กเดียว |
| 7 | ทั้งคู่ | จัดการเนื้อหาเก่าของ EA Special (ลบ/รีไดเรกต์), Privacy/Terms ของ EA2000, เปิด Push-to-Deploy |

## 8) ตอนเปิดโปรเจกต์ครั้งแรก
ตรวจแบบอ่านอย่างเดียว สรุปสถานะ ถามค่าที่ยังขาด (ข้อ 7 ขั้น 1) แล้วเริ่มขั้น 2 ได้เลยเมื่อเจ้าของบอก "เริ่ม" · ไม่ push จนเจ้าของยืนยัน remote · ทุกอย่างที่เป็นข้อมูลจริง (ผลเทรด ราคา รีวิว) รอเจ้าของกรอก

## 9) การตัดสินใจของเจ้าของและสถานะล่าสุด (อัปเดต 7 ก.ย. 2026)
- GitHub repo: https://github.com/easpeciallab-max/ea2000-theme.git (public, สร้างเปล่า 7 ก.ย. 2026) · remote `origin` ตั้งแล้ว · **ยังไม่ push**
- ขั้น 2 เสร็จ: ธีมจาก FENIX commit 5fbb811 อยู่ใน `ea2000/` (ดึงด้วย `git archive` เฉพาะไฟล์ที่ track) โดย**ไม่นำมา** `assets/downloads/`, `assets/content/`, `logo.png`, `logo-128.png`, การ์ดดาวน์โหลด 2 รูป, `img/install/step-01..06.jpg`, `screenshot.png` · `tests/link-hub-downloads.php` ก๊อปมารอเขียนใหม่
- **ยังไม่ commit** จนกว่ารีแบรนด์ขั้น 3 เสร็จ เพื่อไม่ให้ประวัติ repo สาธารณะมี token/ลิงก์/แบรนด์ของ FENIX แม้แต่ commit เดียว
- Line endings: repo นี้ใช้ LF (`.gitattributes`) · เครื่องเจ้าของมี core.autocrlf=true จึงตั้ง `core.autocrlf=false` เฉพาะ repo นี้แล้ว
- LINE OA, โซเชียล, อีเมล, โลโก้, สี: เจ้าของยืนยันว่าจะมีแน่แต่ยังไม่ส่ง → ใช้ค่าว่าง/placeholder และทุกปุ่มต้องซ่อนเมื่อค่าว่าง
- ใช้ WordPress เดิมที่ ea2000.co และเปลี่ยนธีมทับ easpecial (ยืนยันแล้ว)
- หน้า /go/ (link hub): เปิดส่วนการ์ดดาวน์โหลดไว้ รอไฟล์และรูปของ EA2000
- ราคาและแพ็กเกจ: ใช้โครงเดียวกับ FENIX (pricing_mode `price`, 3 แพ็กเกจ, ราคาเดิม) ตามคำสั่งเจ้าของ · ไม่ใช่ตัวเลขผลทดสอบ จึงไม่ขัดหลักข้อ 3.2
- ทิศทางดีไซน์: **หน้าตาต้องไม่เหมือน FENIX** แต่โครงหน้าและฟังก์ชันใช้ FENIX เป็นต้นแบบ · เปลี่ยนสี ฟอนต์ องค์ประกอบ เมื่อได้โลโก้และสี
- (ก) prefix โค้ด `fenix_`/`fenix-`/camelCase: รอเจ้าของเลือก (Claude แนะนำ rename ทั้งหมดเป็น `ea2000` ทีเดียว เพราะ class/handle/cookie โผล่ใน page source)
- ผลตรวจต้นทาง 7 ก.ย. 2026: เลขบรรทัดในข้อ 4 บางจุดคลาดเคลื่อน ใช้ค่าจริงนี้แทน: `fenix_mod()` `:660-736` ลบ `:665-733` ทั้งก้อน (ตาราง stale เขียนทับค่าจริงด้วย ไม่ใช่แค่ค่าว่าง) · เมนู `:851-941` และ hotfix `wp_nav_menu_items` `:782-813` ลบได้ · template มี 9 ไฟล์ (Elementor 2 ไฟล์ใช้ `FENIX - ` ขีดกลาง) · `tests/`, `.gitignore`, `README.md` อยู่รากรีโป · `screenshot.png` อยู่รากธีม
- เพิ่มในเช็กลิสต์ข้อ 4: OpenChat FENIX `:229` · slug Zaurix `:224`, `:226` · `links_fast_enabled` `:230` · `links_guide1_*` `:253-254` · fallback hardcode ใน `template-links.php:32-41` และ `:143-155` · รีวิวตัวอย่าง `:406-411` · วันที่ risk page `:607` · `fenix_verification_meta` `:1424-1434` ไม่มี guard ปลั๊กอิน SEO · text domain `fenix-pro` (`functions.php:18, :994, :1054, :1857`, `style.css:13`) · `@package fenix-pro` 19 ไฟล์ · REST `fenix/v1` ที่ `:1732`, `:1767` · สี ember/gold hardcode ใน `style.css` ราว 100 จุดนอก `:root` · `logo-128.png` และ `assets/content/covers/` ไม่มีโค้ดอ้าง

### สถานะขั้น 3 (เสร็จ 7 ก.ย. 2026 · ยังไม่ push)
- rename ตัวระบุโค้ดทั้งหมด `fenix` → `ea2000` (1003 จุด ฟังก์ชัน 56 ตัวครบ) · text domain/@package `ea2000` · REST `ea2000/v1` · cookie `ea2000_consent` · CSS `.ea2000-*` · เลขบรรทัดในข้อ 4 ถือเป็นประวัติ ให้ใช้ grep แทน
- รีแบรนด์ครบ: `ea2000_defaults()` เขียนใหม่ทั้ง 389 key (เพิ่ม `brand_name`, `brand_tagline`) · `ea2000_mod()` เป็น lookup ล้วน **ไม่มี fallback** · `ea2000_verification_meta()` มี guard `ea2000_has_seo_plugin()` (Yoast/RankMath/SEOPress ไม่รวม Site Kit) · เมนู fallback ใช้ slug ตามแผน · ลบ hotfix chatgpt และ injection Zaurix · header/footer ใช้ setting แบรนด์ · ทุกปุ่ม LINE/โซเชียล/อีเมลซ่อนเมื่อค่าว่างหรือ `#` · การ์ดดาวน์โหลดใน /go/ แสดงเป็นรูปไม่มีลิงก์เมื่อ url ว่าง
- ตรวจแล้ว: `php -l` ผ่านทุกไฟล์ · grep `fenix|zaurix|speccub|myfxbook` ในโค้ด = 0 (เหลือเฉพาะคำสั่ง grep ใน README และบริบทใน CLAUDE.md) · ไม่มี em/en dash · LF ทั้งหมด · `tests/link-hub-downloads.php` ผ่าน
- **ค่าชั่วคราว** รอเจ้าของ: พาเลตน้ำเงิน (`--primary #3B82F6`, `--accent #22D3EE`, `--accent-2 #7DD3FC`) และฟอนต์ Chakra Petch + Bai Jamjuree ใน `style.css :root` (สี hardcode ถูก tokenize แล้ว เปลี่ยนที่เดียว) · รูป placeholder `assets/img/logo.png`, `og-default.png`, `link-download-ea2000.png`, `install/step-01..06.jpg`, `screenshot.png` ต้องแทนด้วยของจริง · `Tested up to: 6.8` ใน style.css ให้ปรับหลังทดสอบบน WP 7.1
- **รอเจ้าของยืนยัน**: ราคา/แพ็กเกจที่คัดลอกจาก FENIX (Starter ฟรี · Pro 6,990 บาท · VIP 9,990 บาท และตารางเปรียบเทียบ) · คำสัญญาบริการที่ติดมา (ทีมช่วยติดตั้ง/VPS, ไฟล์ Preset, Dashboard ใน EA, อัปเดตตามรอบ, Support ผ่าน LINE) · ป้ายเมนู fallback · slug `/risk-disclosure/` ที่ hardcode ใน footer/404/single/front-page · schema/og:site_name ใช้ Site Title ของ WordPress (ตั้งเป็น EA2000)

### สถานะขั้น 4 ถึง 5 (7 ก.ย. 2026)
- push รอบแรกขึ้น `easpeciallab-max/ea2000-theme` แล้ว (commit `3386d8d`) · บัญชี git ในเครื่องคือ `easpecial-th` ซึ่งเจ้าของเพิ่มเป็น collaborator (write) แล้ว
- WP Pusher 3.0.17 มีอยู่บน ea2000.co อยู่แล้ว (เคยใช้กับธีม easpecial) · เพิ่มธีม EA2000 จาก repo นี้ branch `main` subdirectory `ea2000` **Push-to-Deploy ปิด** · อัปเดตธีมครั้งถัดไป: WP Pusher > Themes > Update theme
- ธีม EA2000 **เปิดใช้งานแล้ว** บนเว็บจริง · ธีม EA Special ยังติดตั้งอยู่แต่ไม่ active (ลบได้ในขั้น 7)
- ยังค้าง: Application Password (`~/.ea2000-wp.env`) · เพจ+เทมเพลต 6 หน้า · Customizer · Yoast title ของหน้าแรกยังเป็น "EA Special | ..." และ Search Engine Visibility ยังปิดการ index (ขั้น 6)
- ควบคุมเบราว์เซอร์ผ่านส่วนขยาย Claude in Chrome ในโปรไฟล์ Chrome "SATOSHI" (easpeciallab@gmail.com) ซึ่งล็อกอิน wp-admin เป็น ADMIN · Claude ไม่พิมพ์รหัสผ่านให้

### สถานะขั้น 5 (เสร็จ 7 ก.ย. 2026)
- Application Password ใช้งานได้: `Authorization: Basic` ถึง PHP บนโฮสต์นี้ (ต่างจาก FENIX) และ `X-Authorization` ก็ใช้ได้ · helper อ่าน env อยู่ที่ scratchpad `wpapi.sh` (รหัสมีช่องว่าง ต้องอ่านแบบไม่ source และตัดช่องว่างออก)
- เพจใหม่ (publish): backtest 49 · forward-test 50 · pricing 51 · how-to-install 52 · risk-disclosure 53 · go 54 (ชื่อ "ติดต่อและลิงก์รวม EA2000") · หน้าแรกใหม่ `home-ea2000` id 65 ตั้งเป็น page_on_front (ไม่มี Elementor data) · posts page ยังเป็น articles 35
- เมนูใหม่: id 6 "EA2000 เมนูหลัก" (primary) · id 7 "EA2000 เมนูท้ายเว็บ" (footer) · เมนูเก่า id 4, 5 ยังอยู่แต่ไม่ผูก location
- ตั้งค่า: tagline "ระบบเทรดอัตโนมัติสำหรับ MetaTrader 5" · site icon = media 67 · featured image ทุกเพจ = og-default (ให้ Yoast ออก og:image) · GTranslate: Translate from = Thai และเปิด th ในรายการ
- เพจเก่า EA Special: `home` 20 และ `about` 27 เปลี่ยนเป็น draft (แบรนด์เก่าหลุดผ่าน footer) · ยัง publish: results 23, guides 25, risk-warning 29, data-deletion 11, privacy-policy 3 (ข้อความ default อังกฤษ) → ขั้น 7 ตัดสินใจลบ/redirect/เขียนใหม่
- **Deploy**: WP Pusher "Update theme" ไม่เขียนไฟล์ (ไม่มี GitHub token ใน WP Pusher > GitHub และ log ปิด) จึงอัปเดตด้วยการอัปโหลด zip จาก `git archive HEAD:ea2000` ผ่าน Themes > Add > Upload > Replace แทน · ถ้าเจ้าของใส่ GitHub token ใน WP Pusher แล้วให้ลอง Update theme ใหม่ก่อนใช้ zip
- ผล audit สด 8 หน้า (workflow): เทมเพลตถูกทุกหน้า ไม่มี PHP error ไม่มีร่องรอย FENIX เมนูตรงสเปก · แก้แล้ว: footer ไม่ auto-link about/terms, ปุ่มแพ็กเกจและ footer CTA ชี้ /go/ เมื่อยังไม่มี LINE, hint อัปโหลดรูปเห็นเฉพาะแอดมิน (commit 22826cf)
- ค้างสำหรับขั้น 6: Yoast meta description รายหน้า, breadcrumb "Home" → "หน้าแรก", GA4/GSC, เอา noindex ออก · ค้างขั้น 7: เพจเก่า, Privacy Policy, ธีม EA Special ยังติดตั้ง, PHPSESSID จากปลั๊กอินทำให้ cache BYPASS (ไม่ใช่ธีม)

### สถานะรีสไตล์โทนสว่าง (7 ก.ย. 2026 · commit 7401a3a · deploy แล้ว)
- เจ้าของส่งโลโก้ (ต้นฉบับใน `D:\EA VIDEO\EA2000\`), LINE OA `https://lin.ee/ye11pwm6`, OpenChat (ตั้งใน Customizer แล้ว) และสั่ง: สีตามโลโก้แต่เว็บโทนสว่าง มินิมอล โมเดิร์น
- `style.css` แปลงเป็นธีมสว่างทั้งไฟล์: token `--bg #FFFFFF`, `--surface #F6F8F7`, `--text #0B1210`, `--muted #5B6660`, `--primary #1A7F11` (ปรับจาก #1E8E14 ให้ผ่าน 4.5:1), `--primary-deep #166B0F`, `--accent #7CE43A`, `--accent-2 #C9F5A6`, `--steel #8A9199` · กล่องเตือนความเสี่ยงใช้โทนอุ่น `--warn-bg/--warn-text/--warn-border` · ตรวจ contrast แล้วทุกคู่หลัก
- แบรนด์: setting ใหม่ `brand_wordmark` (default `assets/img/logo-wordmark.webp`) header/footer แสดง wordmark เมื่อมีค่า ว่าง = โลโก้กลม + ชื่อ · `hero_image` default = `assets/img/hero-box.webp` (กล่องสินค้าพื้นโปร่ง) · ไอคอน badge hero = chart · site icon = media 76 (ตรากลมจริง)
- รูปที่สร้างจากโลโก้จริง: `logo.png` (ตรากลม 512), `logo-wordmark.webp`, `hero-box.webp`, `og-default.png`, การ์ดดาวน์โหลดและ step-01..06 แบบพื้นขาว, `screenshot.png` ชั่วคราว (ควรแทนด้วยภาพจริงของหน้าเว็บ) · สคริปต์สร้างอยู่ใน scratchpad `make-brand-assets.ps1`
- Deploy ผ่านอัปโหลด zip (WP Pusher ยังไม่มี token) · ตรวจแล้วหน้าเว็บจริงใช้ธีมสว่าง ไม่มี PHP error
- ค้าง: ให้เจ้าของดูภาพหน้าจอแล้วปรับตามคำสั่ง · ภาพ screenshot.png จริง · `Tested up to` ใน style.css
- แคช CDN: Cloudflare แคชรูปในธีมตาม URL ไม่มี version query ทำให้ `link-download-ea2000.png` ค้างเป็นรูปเก่า → commit b0c039c เปลี่ยนชื่อไฟล์เป็น `link-download-ea2000-light.png` (ยังไม่ deploy) และตั้ง theme_mod `links_fast_img` / `links_feature_img` ชั่วคราวเป็น URL เดิม + `?v=20260907` · **หลัง deploy commit นี้ให้ส่ง `{"links_fast_img":null,"links_feature_img":null}` ไปที่ `ea2000/v1/mods`** เพื่อกลับไปใช้ default ชื่อใหม่ · เมื่อเปลี่ยนรูปในธีมครั้งถัดไป ให้เปลี่ยนชื่อไฟล์เสมอ
- ภาพหน้าจอตรวจงาน: ใช้ headless Chrome (`chrome.exe --headless=new --screenshot`) ความกว้าง ≥ 500px เท่านั้น (ต่ำกว่านั้น Chrome บังคับความกว้างขั้นต่ำแล้วภาพจะถูกตัดขอบ ไม่ใช่บั๊กของธีม · ยืนยันด้วย emulation 375px ว่า scrollWidth = viewport) และห้ามใช้ query `?w=` / `?m=` เพราะเป็น query var ของ WordPress

## 10) การตัดสินใจของเจ้าของ 8 ก.ย. 2026 และแผนงานระยะถัดไป
- **เอกสารแผนอยู่ใน `docs/`** อ่าน `docs/roadmap.md` ก่อนเสมอ · อีก 4 ไฟล์: `redesign-plan.md`, `seo-content-plan.md`, `research-keywords.md`, `research-market.md`
- **สินทรัพย์ที่เทรด: หลายคู่เงิน ไม่เจาะจงทอง** → **ห้ามใช้ชุดคีย์เวิร์ดสายทอง** ที่เป็นข้อเสนอหลักใน `seo-content-plan.md` ให้ใช้ชุดสำรอง (EA MT5, ระบบเทรดอัตโนมัติ, บอทเทรด, วิธีติดตั้ง EA) และตัดคลัสเตอร์ทองออก · ผลคือแข่งยากกว่าเดิม ต้องพึ่งกลุ่มคำ "วิธีทำ" มากขึ้น
- **กลยุทธ์ EA (martingale/grid/SL): เจ้าของยังไม่เปิดเผย** → ห้ามเขียนอ้างว่า "ไม่ใช้ martingale" หรือ "มี SL ทุกออเดอร์" เด็ดขาด · หน้า risk disclosure ให้เขียนแบบทั่วไปตามความจริงที่ยืนยันได้ · เลื่อนบทความกลุ่ม "EA ไม่ล้างพอร์ต / martingale" ออกไปจนกว่าจะเปิดเผย
- **ชื่อแบรนด์ EA2000 ชนหนัก**: autocomplete 201 seed ได้ 68 คำ ไม่มีคำใดเกี่ยวกับเทรดเลย · ตัวชนคือหูฟัง SIMGOT EA2000, อุปกรณ์อุตสาหกรรม, หุ้น Energy Absolute (SET: EA), EA Sports · **อย่าสู้คำเปล่า "EA2000"** ให้ยึด `EA2000 EA MT5` และ `EA2000 ระบบเทรดอัตโนมัติ` · คำว่า "อีเอ 2000" สะกดไทยได้ 0 suggestion ทิ้งได้
- **ถอดปลั๊กอินแล้ว 8 ก.ย. 2026** (deactivate ผ่าน REST `wp/v2/plugins`): PixelYourSite และ GTranslate · ผลวัดจริง: `Set-Cookie: PHPSESSID` หายไป · `x-cache-status` เปลี่ยนจาก BYPASS ทุก request เป็น HIT · JS บนหน้าแรกเหลือ `main.js` 12.5 KB จากเดิมมี jQuery + PixelYourSite 5 ไฟล์ + GTranslate · **ถ้าจะยิงโฆษณาค่อยติดตั้ง PixelYourSite กลับ**
- **ลำดับงานที่เจ้าของเลือก**: header/footer โทนเขียวเข้มก่อน (แทนที่จะเริ่มจากเฟส 0 ถึง 1 ตาม roadmap) · งานเฟส 1 ที่เหลือ (301 หน้าเก่า, title/meta, about, Privacy ไทย, ปลด noindex) ยังค้างและต้องทำก่อนเว็บจะติดอันดับได้
- บั๊กที่ตรวจยืนยันบนเว็บสด 8 ก.ย. 2026: เมนูย่อยมือถือหลุดจอ (`.nav-list li.is-open > .sub-menu` specificity ชนะ media query) · ข้อความสั่งงานแอดมิน "อัปโหลดภาพ Dashboard" โผล่ให้ผู้เข้าชมเห็นที่ `front-page.php:387` · `.reveal { opacity: 0 }` ไม่มี no-js fallback
- **Header/footer โทนเขียวเข้ม deploy แล้ว 8 ก.ย. 2026 (commit 97bf98b)**: token ชุดมืด `--ink #08150E`, `--ink-deep #05100A`, `--ink-raise #0F2016`, `--ink-chip #16301F`, `--ink-text #F2F7F3`, `--ink-muted #AEBEB2`, `--ink-accent #8CEC4E` อยู่ท้ายไฟล์เป็น section 39 ก้อนเดียว **ถอดออกได้ด้วยการลบทั้งบล็อก** · เนื้อหาในหน้ายังสว่างเหมือนเดิม · โทนมืดใช้กับ header, footer, dropdown, ลิ้นชักมือถือ และบาร์ล่างมือถือเท่านั้น · `/go/` ไม่เปลี่ยนเพราะเป็น standalone template
- แก้พร้อมกัน: เมนูย่อยมือถือหลุดจอ (เพิ่ม `.nav-list li.is-open > .sub-menu { transform: none; left: auto }` ใน media 960 ให้ selector เท่ากันแล้วชนะด้วยลำดับ) · ข้อความ "อัปโหลดภาพ Dashboard" ที่หลุดให้ผู้เข้าชมเห็น (`front-page.php:387` ห่อด้วย `current_user_can('customize')`) · เพิ่ม `class="no-js"` บน `<html>` + สคริปต์สลับเป็น `js` และกฎ `.no-js .reveal { opacity: 1 }` · focus ring บนแถบมืดเปลี่ยนเป็น `--ink-accent` (12.65:1 จากเดิม 3.64:1)
- ปิด `show_language_switcher` (theme_mod) เพราะถอด GTranslate แล้ว ตัวสลับภาษาของธีมจะลิงก์ `?lang=en` ที่ไม่ทำอะไร · ล้าง theme_mod `links_fast_img` / `links_feature_img` กลับไปใช้ default ชื่อไฟล์ใหม่แล้ว (งานค้างจาก b0c039c ปิดแล้ว)

## 11) งานเฟส 1 ที่ทำเสร็จ 8 ก.ย. 2026 (commit 369c345 · **ยังไม่ deploy**)
- **`ea2000/inc/seo.php` ไฟล์ใหม่** (โหลดจาก functions.php บรรทัดเดียว)
  - 301 map หน้าเก่า EA Special: `/results/` → `/forward-test/`, `/guides/` → `/articles/`, `/risk-warning/` → `/risk-disclosure/`, `/ea-products/` → `/pricing/` · มี `ea2000_legacy_page_ids()` = 23, 25, 29 เพื่อข้าม guard ที่ปกติจะไม่ redirect หน้าที่ publish อยู่ · แก้ได้ผ่าน filter `ea2000_redirect_map` และ `ea2000_legacy_page_ids` · **ไม่มี `/about/` ในแผนที่แล้ว** เพราะหน้า about ต้องกลับมาเป็นหน้าจริงของ EA2000
  - robots.txt เพิ่มบรรทัด Sitemap (ทำงานเมื่อปลด noindex แล้วเท่านั้น)
  - noindex อัตโนมัติสำหรับหน้า backtest/forward-test ขณะที่ค่าสถิติยังเป็น placeholder ทุกช่องและยังไม่มีลิงก์ตรวจสอบ · **ปิดตัวเองเมื่อเจ้าของกรอกข้อมูลจริง** · แต่จะไม่ทำงานเลยขณะที่ Yoast active ให้ตั้ง noindex รายหน้าใน Yoast แทน
  - register_post_meta `_yoast_wpseo_title` และ `_yoast_wpseo_metadesc` ให้เขียนผ่าน REST ได้ (สิทธิ์ edit_posts) เพื่อให้ตั้ง title/meta ผ่าน API ได้หลัง deploy
- **Schema**: `ea2000_schema_jsonld()` ทำงานแม้มี Yoast แล้ว ออก `SoftwareApplication` เฉพาะหน้าแรกและหน้า pricing · `offers` ออกเฉพาะเมื่อ `pricing_mode` = price และราคาเป็นตัวเลขจริง และ `pricing_confirmed` เปิด (default false) · ไม่ซ้ำกับ node ของ Yoast · setting ใหม่: `product_name`, `product_os`, `product_requirements`, `product_version`, `pricing_confirmed` (**ยังไม่มี control ใน customizer** ตั้งผ่าน `ea2000/v1/mods` ได้)
- **ตัด asset**: dequeue `wp-block-library`, `global-styles`, `classic-theme-styles`, emoji บนหน้าเว็บ (ไม่แตะ admin และหน้าที่มี block จริง)
- **รูปใหม่ทั้งชุด ชื่อใหม่หมด** (กันแคช Cloudflare): `logo-mark.webp` 19KB, `wordmark.webp` 66KB, `card-download.webp` 32KB, `og-share.jpg` 68KB (JPEG เพราะ LINE/Facebook อ่าน WebP ได้ไม่ดี), `install/guide-01..06.webp` ~20KB ต่อไฟล์ · **ลบไฟล์เก่าทั้งหมด** · รวมรูปในธีมจาก 1.13 MB เหลือ 373 KB · `tests/link-hub-downloads.php` รับทั้ง PNG และ WebP แล้ว
- style.css: ลบบล็อก GTranslate ที่ตายแล้ว 170 บรรทัด · `Tested up to: 7.1`
- **เนื้อหาที่เขียนแล้วบน WordPress**: `/privacy-policy/` เป็น PDPA ภาษาไทย (publish แล้ว ใช้ LINE OA เป็นช่องทางใช้สิทธิ์) · `/data-deletion/` เดิมว่างเปล่า ตอนนี้มีขั้นตอนขอลบข้อมูลจริง (publish แล้ว) · `/about/` (id 27) และ `/terms-of-use/` (id 33) เขียนเป็น **ฉบับร่าง** รอเจ้าของเติมส่วนที่ต้องตัดสินใจเอง · ลบเมนูเก่า id 4, 5 แล้ว
- **ค้าง deploy**: เบราว์เซอร์ที่ล็อกอิน wp-admin หลุดการเชื่อมต่อ · zip พร้อมที่ scratchpad `ea2000-theme.zip` (682 KB) · หลัง deploy ต้องทำต่อ: ตั้ง title/meta 8 หน้าผ่าน REST, ตรวจว่า 301 ทำงาน, ตรวจ SoftwareApplication ด้วย Rich Results Test

## 12) วิธี deploy ที่ถูกต้อง (แก้ไข 8 ก.ย. 2026) และงานที่ทำเสร็จรอบนี้
- **Push-to-Deploy เปิดแล้วสำหรับธีม EA2000** ที่ WP Pusher > Themes > Edit EA2000 · ต่อจากนี้ push ขึ้น GitHub แล้วเว็บดึงเอง ไม่ต้องอัปโหลด zip อีก
- **ข้อเท็จจริงที่ผมเคยสรุปผิด**: WP Pusher **ไม่ต้องใช้ GitHub token** สำหรับ repo public (ทดสอบแล้ว API และไฟล์ zip ตอบ 200 โดยไม่ยืนยันตัวตน) · ปุ่ม "Update theme" ใช้ได้จริง แต่ต้องคลิกผ่าน JS (`btn.click()`) เพราะการคลิกด้วย ref บางครั้งไม่ส่งฟอร์ม · เหตุผล "ไม่มี token" ที่เคยบันทึกไว้ในข้อ 5 **ไม่ถูกต้อง**
- **UI wp-admin คลิกไม่ติดบ่อย**: ปุ่ม Install Now, Replace installed with uploaded, Update theme และ media picker ของ Yoast · แก้ด้วยการเรียก `javascript_tool` สั่ง `.click()` ตรง ๆ หรือเปิด href ของลิงก์โดยตรง
- **Yoast ตั้งค่าแล้ว**: Site representation = Organization ชื่อ `EA2000` ชื่อรอง `EA2000 EA MT5` (แก้ปัญหาชื่อชนหูฟัง SIMGOT) โลโก้ = media 76 · Site image (og:image) = media 84 `ea2000-share.jpg` · breadcrumb แปลไทยครบ (หน้าแรก, คลังบทความของ, ผลการค้นหาสำหรับ, ไม่พบหน้าที่ค้นหา)
- **title และ meta description ตั้งครบ 10 หน้าผ่าน REST** (`_yoast_wpseo_title`, `_yoast_wpseo_metadesc` ที่ inc/seo.php register ไว้) · ทุก title ไม่เกิน 60 ตัวอักษร ทุก description ไม่เกิน 155 · **ไม่มีคำว่าทองหรือ XAUUSD** ตามที่เจ้าของยืนยันว่าเทรดหลายคู่เงิน
- **redirect ทำงานจริงแล้ว**: /results/ → /backtest/ · /guides/ → /how-to-install/ · /risk-warning/ → /risk-disclosure/ · /ea-products/ → /pricing/ (301 ทั้งหมด) · **Cloudflare แคชหน้าเก่าไว้** ผู้เข้าชมบางคนจะยังเห็น 200 จนแคชหมดอายุ ควร purge จาก Cloudflare
- **น้ำหนักหน้าแรกหลังปรับ**: HTML 72 KB · CSS+JS 12.5 KB (เดิมเกือบ 500 KB) · รูป 151 KB
- **เจ้าของยืนยัน 8 ก.ย. 2026**: FENIX กับ EA2000 เป็นเครือเดียวกัน แยกแบรนด์เพื่อสร้างฐานลูกค้าคนละกลุ่ม → ราคา แพ็กเกจ คำสัญญาบริการ และเงื่อนไข **ใช้ตาม FENIX ได้เลย ไม่ต้องถามอีก** · เนื้อหาให้เรียบเรียงใหม่ ไม่ก๊อปคำต่อคำ เพื่อเลี่ยง duplicate content
- **คำสั่งเจ้าของ: ห้ามปลด noindex จนกว่าจะสั่ง** และห้ามทำงานนอกเหนือคำสั่ง

## 13) Deploy อัตโนมัติใช้งานได้แล้ว (9 ก.ย. 2026) และลบธีม EA Special แล้ว
- **วิธี deploy ต่อจากนี้: `git push origin main` อย่างเดียว** · GitHub ยิง webhook (id 676503056, push event, JSON) ไปที่ Push-to-Deploy URL ของ WP Pusher → WP Pusher ดึงโค้ดจาก repo public โดยไม่ต้องใช้ token · วัดจริง: push 11:44:04 → log "Push-to-Deploy was initiated" 11:44:05 → "Theme 'EA2000' was successfully updated" 11:44:06
- **ตรวจว่า deploy ถึงดิสก์จริง**: `GET /wp-json/wp/v2/themes?status=active&_fields=version` (auth) อ่าน `Version:` จาก style.css บนดิสก์ ข้ามแคชทุกชั้น → ให้ bump `Version` ใน style.css header ทุกครั้งที่ต้องการยืนยัน deploy (ตอนนี้ 1.0.1) · ถ้า push แรกไม่ขึ้นภายใน 1 นาที (เคยเกิด 1 ครั้ง น่าจะเพราะ GitHub ยังไม่สร้าง archive ของ commit ใหม่ทัน) ให้ push commit ถัดไปหรือกด WP Pusher > Themes > Update theme ผ่าน JS click
- WP Pusher **เปิด logging ไว้** (WP Pusher > Log) เพื่อดูผล deploy ทุกครั้ง · ถ้าไฟล์ log โตค่อยกด Disable logging
- **Push-to-Deploy URL เป็นความลับ** ไม่เก็บใน repo/แชท · เจ้าของเป็นคนคัดลอกจาก WP Pusher ไปวางใน GitHub เอง (Chrome ไม่ยอมให้สคริปต์คัดลอกจากแท็บที่ไม่ได้โฟกัส ต้องเป็นการคลิกของคนจริง)
- **ธีม EA Special ลบออกจากเซิร์ฟเวอร์แล้ว** (เจ้าของสั่ง 9 ก.ย. 2026) · รายการใน WP Pusher หายไปด้วย · repo `easpecial-th/easpecial-theme` ยังอยู่บน GitHub ถ้าต้องกู้คืน
- ข้อ 5 และข้อ 11 ที่เขียนว่า "ปิด Push-to-Deploy" และ "อัปโหลด zip" ถือเป็นประวัติ ไม่ใช้แล้ว

## 14) เก็บกวาดรายการเพจ (9 ก.ย. 2026) · หน้าจริงของ EA2000 มี 12 หน้าเท่านั้น
- **ย้ายลงถังขยะแล้ว** (กู้คืนได้ 30 วัน): home 20 (หน้าแรกเก่า Elementor), results 23, guides 25, risk-warning 29, contact 31 · redirect 301 ของ /results/ /guides/ /risk-warning/ ยังทำงานเพราะ inc/seo.php จับที่ path ไม่ได้พึ่งเพจ
- **หน้าจริง (publish ทั้งหมด ไม่มี draft ค้าง)**: 65 home-ea2000 (หน้าแรก) · 49 backtest · 50 forward-test · 51 pricing · 52 how-to-install · 53 risk-disclosure · 54 go · 35 articles (posts page) · 27 about (เขียนใหม่เป็นของ EA2000 จากข้อเท็จจริง FENIX) · 33 terms-of-use (เขียนใหม่ 11 ข้อตามเงื่อนไข FENIX: ไม่คืนเงินหลังส่งมอบ ยกเว้นความผิดพลาดของผู้ให้บริการ) · 3 privacy-policy · 11 data-deletion
- footer legal nav auto-link: about, privacy-policy, terms-of-use เฉพาะที่ publish (footer.php ตรวจ post_status แล้ว) · deploy f83ae42 ผ่าน webhook อัตโนมัติ ยืนยันจากลิงก์ที่โผล่บนหน้าเว็บ
- บทเรียน: ตอนสร้างเพจใหม่ต้องเก็บเพจเก่าทันที ไม่ปล่อยให้ชื่อซ้ำในรายการ

## 15) หน้าแรกใหม่ 10 บล็อก (9 ก.ย. 2026 · commit 55a13b4 + 73cb605 · deploy แล้ว v1.0.3)
- โครงตาม `docs/landing-page-plan.md`: hero → `#what` (EA2000 คืออะไร) → `#pain` → `#how` (4 ขั้น + การ์ด "ต้องมีอะไรบ้าง") → `#features` → `#tests` (Backtest/Forward Test อธิบาย ไม่พึ่งตัวเลข) → `#install` (3 ขั้น) → `#fit` → `#pricing` → `#faq` (8 ข้อ `<details>`) → `#risk` → `#cta` · H1 = `hero_title` + `hero_subtitle` ใน `<h1>` เดียว ("EA2000 EA MT5 ระบบเทรดอัตโนมัติ ...")
- section เก่าที่ยังอยู่ในไฟล์แต่ปิดด้วย `show_*` = false: highlight, live_status, team, control_center, gallery, perf, reviews, assurance, mid_cta, explore · section about เก่าและ steps เก่าถูกถอดจาก template (key `about_*`, `step*` ยังอยู่ใน defaults) · `show_steps`/`show_about` ไม่มีผลแล้ว
- key ใหม่ 62 ตัว (what_*, how_*, tests_*, install_*, hero_img_alt, show_what/how/tests/install/explore) มี control ใน Customizer ครบ · `inc/customizer.php` เรียง section ใหม่ตามลำดับหน้า (install ของหน้าแรกใช้ section id `ea2000_install_home`)
- **ช่องรูป (`.img-slot`)**: เมื่อ `*_img` ว่าง หน้าเว็บแสดงกรอบเส้นประพร้อมข้อความ `*_img_note` บอกว่าต้องใส่รูปอะไร **ทุกคนเห็น** (เจ้าของสั่งให้ระบุตำแหน่งรูป) · helper `ea2000_front_media( $prefix, $w, $h )` อยู่หัว front-page.php · ใส่รูปแล้วช่องจะเปลี่ยนเป็น `.media-frame` เอง · รูปที่รอ: what_img (MT5 ขณะรัน EA 1280x800), how_img (Dashboard 1280x800), tests_bt_img (Strategy Tester 1280x720), tests_fw_img (Myfxbook/FX Blue 1280x720), install_step1..3_img (ตอนนี้ใช้ guide-01..03.webp ที่เป็น placeholder)
- schema: `ea2000_faq_schema()` ออก FAQPage คู่กับ SoftwareApplication แม้ Yoast active · ตรวจบนเว็บสดแล้วมี FAQPage 8 Question
- CSS: บล็อก 38b ก่อน section 39 (dark bars) · container 1120 · section 96/64 · การ์ดแบน เงาเฉพาะ hover · `.card.test-card`/`.card.install-card` padding 0 ให้รูปชิดขอบ · FAQ เป็น `<details>` ไม่ใช้ JS
- คำต้องห้ามในเนื้อหา: ทอง/gold/XAUUSD/martingale/grid/ล้างพอร์ต/รับประกันกำไร/ดีที่สุด · disclaimer 3 จุดที่เคยมีคำว่า Gold เปลี่ยนเป็น "CFD และสินทรัพย์ทางการเงินอื่น ๆ" ความแรงเท่าเดิม
- เครื่องมือตรวจ: scratchpad `check-home.sh` (lint, dash, CR, key ที่ใช้ใน template ต้องมีใน defaults, brace/var ของ CSS) · `dump-defaults.php` แปลง `ea2000_defaults()` เป็น JSON เพื่อเทียบกับ `GET ea2000/v1/mods` (endpoint นี้คืนค่า effective ทั้ง 457 key ไม่ใช่เฉพาะ mod) · ภาพหน้าจอรายบล็อก: ถ่าย headless สูง 12500 แล้ว crop ด้วย System.Drawing ตาม offset ที่อ่านจาก JS (anchor `#id` ใน headless เลื่อนไม่ตรง)

## 16) หน้าแรก v2 "Control Room" + footer "Console" (9 ก.ย. 2026 · commit c7ac69f, a6f796a, 323733d · deploy แล้ว v1.1.2)
- เจ้าของสั่ง: หน้าแรกต้องแปลกใหม่ มีลูกเล่น ไม่เหมือน FENIX และทำ footer ใหม่ทั้งหมด → ออกแบบด้วย workflow (สำรวจความเหมือน FENIX → 5 แนวคิด → กรรมการ 3 มุม → สเปก) ผู้ชนะคือ "Control Room" · **สเปกฉบับสมบูรณ์อยู่ที่ `docs/home-v2-spec.md`** (สัญญา class/key/JS API อยู่ข้อ 0 · เช็กลิสต์ข้อ 7 · decisions log ข้อ 8) อ่านก่อนแก้หน้าแรกหรือ footer ทุกครั้ง
- โครง: `<main class="home-v3" data-chapters="9">` · บท 01..09 (`section.ch` + `data-chapter`) = what, pain, how, features, tests, install, pricing, faq, risk · hero = `section.boot` · รางบทซ้าย (`nav.rail`, sticky ≥ 1240) / เส้น progress (`.rail-bar`) ต่ำกว่านั้น · section เก่า (highlight, live, team, control, gallery, mid-cta, perf, fit, reviews, assurance, explore, blog, cta) **ถูกถอดจาก front-page.php แล้ว** key ยังอยู่ใน defaults
- ลูกเล่นที่ใช้: HUD bracket วาดมุม (`.hud-frame`/`.hud-c`), แถบข้อมูลระบบถอดรหัสเฉพาะตัวอักษรละติน (`dl.hud`), ปัญหาถูกขีดฆ่าเมื่อเลื่อนถึง (`.diag .strike`), เทอร์มินัลพิมพ์ log (`div.term[data-lines]` ห้ามมีเวลา/ราคา), โมดูล hairline + scan (`.modules`), แท็บ radio ไม่ใช้ JS (`.tab-radio`), ฟิล์มสตริป (`.film`), FAQ query log (`details.q`), hazard band · footer: signal line SVG, keycap LINE + QR (`footer_line_qr_img` ยังว่าง), prompt พิมพ์วน, spotlight ตามเมาส์ (เฉพาะ pointer fine), ลายน้ำ EA2000, นาฬิกาไทย, ดัชนีหน้าอ่านจากเมนู primary ก่อน
- JS: `window.ea2000 = { reduced, fine, observe, typewriter }` ใน main.js (19.2 KB) · `assets/js/home.js` (4.7 KB) โหลดเฉพาะหน้าแรก dependency `ea2000-main` · ไม่มี canvas ไม่มี scroll-jacking
- CSS: section 40 (home v3) และ 41 (footer v2 + dock + fab) ต่อท้าย 39 · section 20 footer เก่า, 17 FAQ, 18 risk-box, 38b ถูกถอด · `.card/.sec-head/.btn/.reveal/.section` ยังอยู่ให้หน้าใน · Version 1.1.2
- key ใหม่ราว 60 ตัว (hero_hud_items, what_principle, pain_kicker/resolved, how_log_*, feat_module_label, tests_tab_*, install_step*_img_note, pricing_kicker/recommended/more/contact, faq_kicker, risk_kicker/label/more/margin_note, footer_* 24 ตัว) มี control ครบ · customizer เรียงใหม่ 22 section
- ผลตรวจหลัง deploy (workflow 6 มุม + adversarial verify): ยืนยัน 21 จุด แก้ครบใน 323733d (a11y: prompt-static ใช้ sr-only, tier radio display none ≥ 1100, film role=group, reduced-motion clamp delay · CLS: term และ prompt จอง min-height ก่อนพิมพ์ · **เนื้อหา feat1..6, hero_note, pricing_note, pkg2/3_tag, footer_prep_* ที่ยังก๊อป FENIX คำต่อคำ เขียนใหม่แล้ว** · preload hero เฉพาะ ≥ 961px · `pre_option_elementor_optimized_image_loading` = 0 กัน Elementor ยัด fetchpriority · wordmark ใหม่ `wordmark-660.webp` 34 KB แทนไฟล์ 1000px) · ตีตก 15 ข้อที่เป็นความเห็น "ยังคล้าย FENIX ระดับโครงทั่วไป"
- ค้าง: site icon 32x32 ยังชี้ไฟล์ 150px (ต้องเลือกใหม่ใน Customizer > Site Identity ให้ WP สร้าง crop) · ปุ่ม cookie bar ยังใช้ class `.btn` เดิม (component ทั้งเว็บ นอกขอบเขต) · ภาพ `screenshot.png` ของธีมยังเป็นภาพชั่วคราว · รอรูปจากเจ้าของ 7 ช่อง + QR LINE
- บทเรียน: headless Chrome ที่มี `--virtual-time-budget` และแท็บเบื้องหลังของ Browser pane หยุด rAF จึงเห็นลูกเล่นค้างกลางทาง ต้องตรวจสถานะจบด้วย wrapper แบบ real-time หรืออ่านโค้ด · IntersectionObserver นับ `clip-path` เป็นพื้นที่ตัด ทำให้ element ที่ clip 100% ไม่เคย intersect → ใช้ overlay `::after` แทน (บันทึกในสเปก 3.6)

## 17) เนื้อหาหน้าย่อย 6 หน้า และ Yoast ทั้งเว็บ (10 ก.ย. 2026 · commit 33d5cf6, 03ddbaf, 3583589 · deploy แล้ว v1.2.2)
- **`ea2000/inc/page-content.php` ไฟล์ใหม่**: เนื้อหาหน้าย่อยเป็น setting (`{prefix}_secN_title` / `{prefix}_secN_text`) + ตัวแปลงข้อความ `ea2000_rich_text()` + ตัวพิมพ์ `ea2000_page_sections( $prefix, $max, $wrap )` + ตัวสร้างฟิลด์ `ea2000_page_section_fields()`
  - รูปแบบใน textarea: บรรทัดว่าง = ย่อหน้าใหม่ · `- ` = bullet · `1. ` = ลำดับ · `### ` = h3 · บล็อกที่ทุกบรรทัดมี ` | ` = ตาราง (บรรทัดแรกเป็นหัวตาราง) · escape ทุกช่องผ่าน `esc_html()` ก่อนประกอบ HTML
  - prefix ต่อหน้า: `backtest` 6 หัวข้อ · `forward` 6 · `pricingdoc` 5 · `installdoc` 8 · `riskdoc` 6 · `linksdoc` 4 (รวม 70 key) · ผนวกเข้า customizer section 17 ถึง 22 อัตโนมัติ
  - เทมเพลตเรียกก่อนบล็อก LINE CTA · `/go/` เรียกแบบ `$wrap = false` แล้วห่อด้วย `.lh-doc` เองเพราะเป็น standalone template
- **เนื้อหาที่เขียน** (ตาม `docs/seo-content-plan.md` ข้อ 2 ชุดสำรองที่ไม่ใช่สายทอง): backtest = สอนใช้ Strategy Tester 10 ขั้น + ตารางอ่านค่า 8 ตัว + ข้อจำกัด + FAQ · forward = นิยาม + ตารางเทียบ backtest/เดโม/cent/บัญชีจริง + วิธีอ่านผล + FAQ · pricingdoc = จ่ายแล้วได้อะไรที่ของฟรีไม่มี + ตารางจำนวนบัญชีต่อสิทธิ์ + ขั้นตอนสั่งซื้อ + คืนเงิน + FAQ 5 ข้อ · installdoc = .ex5 กับ .mq5 + วิธีลงแบบสรุป + EA ไม่ขึ้นใน Navigator + Algo Trading ไม่ทำงาน + มือถือ + VPS + วิธีตรวจว่าทำงาน + ตารางปัญหาที่พบบ่อย · riskdoc = ช่วงข่าว/spread + drawdown กับ margin call + ความเสี่ยงจากโบรกเกอร์ + เรื่องกลยุทธ์ที่ไม่เปิดเผย + สิ่งที่ไม่รับประกัน · linksdoc = ทักแล้วได้อะไร + สิ่งที่ทีมไม่ทำ + เตรียม 4 ข้อ + ช่องทาง
  - คำว่า `ล้างพอร์ต` ปรากฏ **ครั้งเดียว** ในหน้า risk เพื่ออธิบายว่า Stop Out คืออะไร (เป็นการเปิดเผยความเสี่ยงตามแผน ไม่ใช่จุดขาย) · ห้ามใช้เป็นคำโฆษณาเด็ดขาด
- CSS section 42 · `.doc-sections`, `.doc-section` (h2 มีแท่งเขียวหน้า), `.doc-table-wrap` (เลื่อนแนวนอนบนจอแคบ), `.lh-doc`
- **Yoast รายหน้า ตั้งครบ 12 หน้าผ่าน REST** (title, meta description, focus keyphrase, og/twitter title+description บน 7 หน้าหลัก) · ทุก title 28 ถึง 49 ตัวอักษร ทุก description 121 ถึง 155 · ทุก title มี MT5 หรือ MetaTrader ยกเว้นหน้ากฎหมาย · ไม่มีคำสายทอง
- **`ea2000/v1/seo-options` endpoint ใหม่** (`inc/seo.php`): GET/POST ตั้งค่า Yoast ระดับเว็บเฉพาะคีย์ใน `ea2000_seo_option_whitelist()` · ต้องมีสิทธิ์ `manage_options` · ใช้ `WPSEO_Options::set()` เมื่อมี · **ไม่มีคีย์กลุ่ม noindex/index ในรายการโดยตั้งใจ** เรื่องการเก็บข้อมูลของเสิร์ชเอนจินเป็นการตัดสินใจของเจ้าของเท่านั้น
- ค่าที่ตั้งระดับเว็บ: separator `·` (sc-middot) · metadesc หน้าแรกเป็นค่าสำรอง · title ของ author/date/search/404 เขียนเป็นไทย (เดิมเป็นอังกฤษของ Yoast) · `website_name` EA2000 + `alternate_website_name` EA2000 EA MT5 (ลง WebSite schema แล้ว) · RSS footer ไทย · breadcrumb sep `›` ให้ตรงกับธีม · ปิด archive ของผู้เขียน/วันที่/post format (ยืนยันแล้ว: `/author/adminwp/` = 301, `/2026/09/` = 404)
- register post meta ของ Yoast เพิ่ม: `_yoast_wpseo_focuskw`, `_yoast_wpseo_opengraph-title`, `_yoast_wpseo_opengraph-description`, `_yoast_wpseo_twitter-title`, `_yoast_wpseo_twitter-description`
- แก้ guard หน้าเปล่า: `ea2000_placeholder_page_rules()` เพิ่มคีย์ `doc` ทำให้ backtest และ forward-test **เข้า sitemap แล้ว** เพราะมีเนื้อหาสอนใช้งานจริง (ก่อนหน้านี้ถูกกันออกเพราะดูแค่ช่องสถิติ) · sitemap หน้ารวม 11 URL (articles เป็น posts page อยู่ใน post-sitemap)
- ค้าง: ตัวเลขผล Backtest/Forward Test จริง · รูป 7 ช่อง + QR LINE · site icon 32x32
- **focus keyphrase หน้าแรก = `EA2000 EA MT5`** (แก้จาก `EA MT5` เมื่อเจ้าของทักท้วง 10 ก.ย. 2026) · เหตุผล: หน้าแรกคือหน้าเดียวที่ควรถือ entity ของแบรนด์ และ H1 มีคำว่า `EA2000 EA MT5` ติดกันอยู่แล้ว จึงครอบทั้งชื่อแบรนด์และคำหมวดหมู่ในวลีเดียว · หน้าอื่นยังใช้คำเชิงข้อมูลตามแผน (1 หน้า 1 คำ ไม่ให้กินกันเอง)
- **ช่องว่างที่แท้จริงของคำค้นแบรนด์คือ `sameAs` ใน Organization schema ที่ยังว่างเปล่า** (ไม่มีลิงก์โซเชียลของแบรนด์เลย) ซึ่งเป็นสัญญาณหลักที่บอก Google ว่า EA2000 คือบริษัทนี้ ไม่ใช่หูฟัง SIMGOT หรือหุ้น EA · ต้องรอเจ้าของส่งลิงก์เพจ Facebook / YouTube / TikTok แล้วกรอกใน Customizer ข้อ 1 (`facebook_url`, `youtube_url`, `tiktok_url`, `instagram_url`) Yoast จะดึงเข้า sameAs เอง

## 18) หน้าคู่มือการใช้งาน 5 หน้า (10 ก.ย. 2026 · commit e1b322d · deploy แล้ว v1.3.0)
- เจ้าของทักว่าฟินิกซ์มีหน้าคู่มือ 6 หน้าใต้เมนู "คู่มือการใช้งาน" แต่ EA2000 ไม่มี → สร้างใหม่ 5 หน้า (หน้าที่ 6 คือ /how-to-install/ ที่มีอยู่แล้ว)
- **เพจใหม่**: 90 `vps-windows` · 91 `vps-android` · 92 `vps-ios` · 93 `mt5-login` · 94 `open-mt5-account` (publish ทั้งหมด ใช้ `template-guide.php`) · **รวมเพจจริงของ EA2000 เป็น 17 หน้า**
- **`ea2000/inc/guide-pages.php` ไฟล์ใหม่**: `ea2000_guide_map()` แม็ป slug → prefix (`gvwin`, `gvand`, `gvios`, `gmt5`, `gacct`) · `ea2000_guide_defaults()` เนื้อหาทั้งหมด · `ea2000_guide_fields()` สร้าง control · customizer section 23 ถึง 27
- **`template-guide.php` ไฟล์เดียวใช้ทุกหน้า** เลือกชุดข้อมูลจาก slug · โครง: page hero → intro + กล่องภาพรวม → 6 ขั้นสลับซ้ายขวา (`.guide-step`) → เช็กลิสต์ `[x]` → หัวข้อเสริม 4 หัวข้อผ่าน `ea2000_page_sections()` → คู่มืออื่นที่เกี่ยวข้อง → LINE CTA
- `ea2000_media_slot( $key, $w, $h, $caption )` ย้ายเข้า `inc/page-content.php` เป็นตัวกลางให้ทุกเทมเพลตใช้กรอบ HUD เดียวกับหน้าแรก · CSS section 43
- เมนูหัว: เปลี่ยน item 59 จาก "วิธีติดตั้ง" เป็น **"คู่มือการใช้งาน"** แล้วเพิ่มลูก 6 รายการ (id 100 ถึง 105) · **ลบ "ความเสี่ยง" ออกจากเมนูหัวตามคำสั่งเจ้าของ** (ยังอยู่ในคอลัมน์เอกสารท้ายเว็บ)
- Yoast ครบทั้ง 5 หน้า (title 39 ถึง 48 ตัวอักษร · desc 134 ถึง 146 · focus keyphrase · og/twitter)

### เรื่องรูปจากเว็บฟินิกซ์ (ตรวจจริง 10 ก.ย. 2026)
เจ้าของอนุญาตให้ใช้รูปของฟินิกซ์ได้เพราะเป็นบริษัทเดียวกัน · ดึงออกมาตรวจได้ 33 รูป (ฝังเป็น base64 ในหน้า ไม่ได้อยู่ในคลังสื่อ ต้อง `--dump-dom` ถึงจะเห็น) แบ่งเป็น
- **ใช้ได้และอัปโหลดแล้ว 9 รูป**: VPS Windows 6 รูป (media 106 ถึง 111) · VPS iOS 3 รูป (media 112 ถึง 114) เป็นหน้าจอ Windows และแอปล้วน ไม่มีแบรนด์ · ตั้ง alt ไทยครบ · ตั้ง theme mod ชี้ช่องรูปแล้ว
- **ใช้ไม่ได้เพราะมีชื่อ FENIX**: โฟลเดอร์ Experts เห็นไฟล์ "Fenix Pro" · Navigator เห็น "Fenix Pro" · กราฟตัวอย่างเป็น XAUUSD (ขัดกับจุดยืนหลายคู่เงิน)
- **ห้ามใช้เด็ดขาด**: รูปแท็บ Inputs ของคู่มือติดตั้ง เปิดพารามิเตอร์ทั้งชุด (`InpLotMultiplier 1.3`, `InpMaxOrders 120`, `InpGridPoints 550`, `InpUseDynamicGrid`, `InpGridMultiplierWhenDD 1.5`) = เปิดเผยว่าเป็นระบบ grid และคูณ lot ซึ่งขัดคำสั่งห้ามเปิดเผยกลยุทธ์ และมี magic number ด้วย · แจ้งเจ้าของแล้วว่ารูปนี้เปิดสาธารณะอยู่บนเว็บฟินิกซ์ การจัดการเป็นสิทธิ์ของเจ้าของ
- **ใช้ไม่ได้เพราะผูกโบรกเกอร์ Zaurix**: หน้าสมัคร 5 รูป · หน้าล็อกอิน MT5 11 รูป
- **หน้า VPS Android ของฟินิกซ์เองยังไม่มีภาพจริง** (เป็น SVG placeholder 183x150 ทั้ง 6 ช่อง) จึงไม่มีอะไรให้ยืม
- ช่องรูปที่ยังว่างและรอเจ้าของถ่าย: `gvand_step1..6` (Android 6 ช่อง) · `gvios_step2,5,6` (iOS 3 ช่อง) · `gmt5_step1..6` (MT5 6 ช่อง) · `gacct_step1..6` (เปิดบัญชี 6 ช่อง)
- **เนื้อหา 2 หน้าเขียนแบบไม่ผูกโบรกเกอร์** (`gmt5`, `gacct`) เพราะ EA2000 ยังไม่มีโบรกเกอร์ที่แนะนำ ถ้าเจ้าของยืนยันโบรกเกอร์เมื่อไร ให้เติมชื่อและลิงก์ในขั้นตอนที่เกี่ยวข้อง
- ค้าง (ตกลงกับเจ้าของแล้วว่าทำหลังหน้าคู่มือ): ปรับ 11 หน้าเดิมให้ใช้ภาษาออกแบบเดียวกับหน้าแรก (page hero, การ์ดราคา, guide 6 ขั้นของ /how-to-install/, แถบ LINE CTA, หน้า /articles/ ที่ยังว่าง)

## 19) ปรับ 11 หน้าเดิมให้เป็นภาษาออกแบบเดียวกับหน้าแรก (10 ก.ย. 2026 · commit 014a32d, cc2cf19, 9e3a1e6 · deploy แล้ว v1.4.2)
- **`ea2000_page_hero()` เขียนใหม่**: ตัดวงเบลอ `.ember` และพื้นไล่สีเขียวออก · ใช้พื้นลายจุด (`--dot-grid`) ชิดซ้าย · kicker เป็น `// ข้อความ` แบบ monospace (`.phero-kicker`) · `.page-hero` ของ page.php และ index.php ได้สไตล์เดียวกันผ่าน CSS
- **`ea2000_line_cta()` เขียนใหม่เป็นแถบคอนโซลเข้ม** (`.cta-console`) พื้น `--ink` + ลายจุด + ปุ่ม `.keycap` แบบเดียวกับ footer · ป้ายใช้ `footer_console_label` และข้อความปุ่มใช้ `footer_line_text` (ไม่มี key ใหม่) · แทนที่ `.cta.cta--slim` เดิม
- **หน้า /how-to-install/**: เปลี่ยนจาก `.guide` การ์ดเก่ามาใช้ `.guide-steps` ชุดเดียวกับหน้าคู่มือใหม่ · **จุดนี้แก้บั๊กที่ผมสร้างเองด้วย** เพราะคลาส `.guide-step` ของ markup เก่าชนกับ CSS section 43 ที่เพิ่งเพิ่ม · กล่อง `.req-box` เปลี่ยนเป็น `.guide-check` ที่ใช้เครื่องหมาย `[x]` · เพิ่ม key `inst_step1..6_img_alt` และ `_img_note` (12 key) พร้อม control
- **หน้า /backtest/ และ /forward-test/**: เอากล่องโลโก้เทา `.shot-placeholder` ออก ใช้ `ea2000_media_slot()` กรอบ HUD เดียวกับหน้าแรกแทน
- **หน้า /articles/**: เดิมขึ้นบรรทัดเดียวว่า "ยังไม่มีบทความ" ตอนนี้แสดงรายการคู่มือที่เผยแพร่แล้ว 7 รายการเป็นกริด (ใช้ `.guide-more`) ดึงเฉพาะเพจที่ publish จริง
- **CSS section 44**: หัวหน้าเพจ · แถบ CTA · แถวสถิติเป็นเส้นบาง · การ์ดราคาแบน (featured = เส้นเขียวด้านบนแทนเงา) · ตารางเปรียบเทียบเหมือน `.doc-table` · หัวข้อ `.riskdoc-block h2` และ `.entry-content > h2` มีแท่งเขียวหน้าเหมือน `.doc-section` · `.sec-head` และ `.kicker` (เหลือใช้ที่หน้าแพ็กเกจหน้าเดียว) เปลี่ยนเป็นชิดซ้าย ไม่มีเส้นใต้ไล่สี kicker เป็น `// ` monospace · `.lead` ชิดซ้าย
- ตรวจแล้วทั้ง 17 หน้าตอบ 200 ไม่มี PHP error ไม่มีโค้ดรั่วเป็นข้อความ · `/go/` ยังเป็น standalone ไม่เปลี่ยน (ตั้งใจ)
- บทเรียน: `php -l` ผ่านไม่ได้แปลว่าโค้ดถูก · ครั้งนี้วางบรรทัด `$var = ...` ไว้นอกแท็ก `<?php` ทำให้จะพิมพ์เป็นข้อความบนหน้าเว็บ lint ไม่จับเพราะเป็น HTML ที่ถูกต้อง ต้อง curl หน้าจริงมาดูทุกครั้ง

## 20) QR ของ LINE OA ใส่แล้ว (10 ก.ย. 2026)
- เจ้าของส่งไฟล์ `D:\EA VIDEO\EA2000\QR LINE EA2000.png` (360x360 RGBA พื้นโปร่ง ขอบขาวรอบโค้ดแคบกว่ามาตรฐาน)
- แปลงด้วย ffmpeg: `pad=424:424:32:32:white` แล้วแบนเป็น RGB · **ไม่ย่อขยาย** เพื่อไม่ให้โมดูลของ QR เบลอจนสแกนไม่ติด · เติมขอบขาว 32px รอบด้านให้ได้ quiet zone 4 โมดูลตามมาตรฐาน
- อัปโหลดเป็น media **116** `ea2000-line-qr-v1-1.png` (ชื่อลงท้าย `-1` เพราะรอบแรกอัปซ้ำ ลบ media 115 ที่ซ้ำแล้ว) · alt = `QR สำหรับเพิ่มเพื่อน LINE Official Account ของ EA2000` · ตั้ง theme mod `footer_line_qr_img` แล้ว
- ตรวจบนเว็บจริงด้วยการจำลองมือถือ (hover: none): `.qr-mobile` แสดงปุ่ม "แสดง QR" · เปิดแล้วรูปโหลด 424x424 แสดงที่ 200x200 alt ถูก · `.qr-flyout` ของเดสก์ท็อปซ่อนถูกต้องบนมือถือ และกางเป็น 160px ตอน hover/focus บนเดสก์ท็อป
- ข้อความเตือนแอดมินให้อัปโหลด QR หายไปแล้ว (เงื่อนไขถูกต้อง)
- **เจ้าของสแกนยืนยันแล้ว 10 ก.ย. 2026 ว่า QR ชี้ไป LINE OA เดียวกับ `line_url` (`https://lin.ee/ye11pwm6`)** ปุ่มกับ QR ตรงกัน

## 21) เปิดราคาในข้อมูลโครงสร้าง (10 ก.ย. 2026 · commit a6cabac · deploy แล้ว v1.4.3)
- ตั้ง `pricing_confirmed` = true (อ้างการยืนยันของเจ้าของ 8 ก.ย. ว่าราคาใช้ตาม FENIX ได้เลย และราคาแสดงบนหน้าเว็บอยู่แล้ว) → `SoftwareApplication` บนหน้าแรกและหน้า /pricing/ มี `offers` แล้ว
- **เจ้าของเลือก 10 ก.ย. 2026: ไม่รวมแพ็กเกจ Starter ที่เป็น "Free" ในข้อมูลโครงสร้าง** เพราะ Starter คือขั้นปรึกษาไม่ใช่การขายตัวโปรแกรมที่ราคา 0 และการปล่อยไว้จะทำให้ผลค้นหาขึ้นว่า EA2000 เริ่มต้น ฿0 ซึ่งดึงกลุ่มที่ตามหาของแจกฟรี ขัดกับแผนคีย์เวิร์ดที่เลี่ยงคำว่าฟรีบนหน้าขาย
- วิธีทำ: ใน `ea2000_offer_price()` ยังแปลง `Free`/`ฟรี` เป็น 0.0 เหมือนเดิม (พฤติกรรมเดิมของธีม ไม่ใช่บั๊ก) แต่ลูปสร้าง offers ข้ามแพ็กเกจที่ราคาเป็น 0 และ**ไม่นับเข้า `$visible`** เพื่อไม่ให้ไปทริกการ์ด "อ่านราคาไม่ครบ = ไม่ประกาศเลย" · ปิดพฤติกรรมนี้ได้ด้วยฟิลเตอร์ `ea2000_offer_skip_free`
- ผลบนเว็บจริง: `AggregateOffer` lowPrice 6990 highPrice 9990 offerCount 2 (Pro, VIP) · **หน้าเว็บยังแสดงคำว่า Free ของ Starter ตามเดิม** เปลี่ยนเฉพาะสิ่งที่ส่งให้เสิร์ชเอนจิน

## 22) แก้ข้อสรุปเรื่องรูปฟินิกซ์ที่เคยตัดสินหยาบไป (10 ก.ย. 2026)
- เจ้าของทักว่าทำไมไม่เอารูปที่มีอยู่แล้วมาใช้ → กลับไปเปิดดูทีละรูปแทนการตัดสินจาก alt text พบว่า **ข้อ 18 ที่เขียนว่า "ใช้ไม่ได้" ทั้งกลุ่มนั้นหยาบเกินไป** หลายรูปใช้ได้ บางรูปแค่ครอปส่วนที่มีชื่อแบรนด์หรือชื่อโบรกออก
- เพิ่มเข้าไปอีก 4 ช่อง (media 118 ถึง 121):
  - `inst_step1_img` และ `gmt5_step1_img` = หน้าดาวน์โหลด MetaTrader 5 ของ MetaQuotes (ถ่ายเองด้วย headless Chrome จาก metatrader5.com/en/download ไม่ผูกโบรกเกอร์)
  - `inst_step2_img` = เมนู File > Open Data Folder **ครอปตัดแท็บกราฟ XAUUSD ที่อยู่ขวามือออก** (ต้นฉบับ 620x300 เหลือ 345x300)
  - `gmt5_step3_img` = ช่องค้นหา "ใส่ชื่อบริษัทหรือเซิร์ฟเวอร์" ในแอป MT5 · **ไม่มีชื่อโบรกเลย ใช้ได้ทันที** (รูปนี้ผมเคยตัดทิ้งผิด)
  - `gmt5_step5_img` = ช่องกรอกชื่อผู้ใช้และรหัสผ่าน **ครอปตัดแถว "เซิร์ฟเวอร์ Zaurix-Server" ที่อยู่บนสุดออก** (710x470 เหลือ 710x338)
  - `gvand_step1_img` = หน้า Google Play ของแอป Windows App ภาษาไทย (ถ่ายเอง)
- **บทเรียน: อย่าตัดสินรูปจากคำบรรยาย ต้องเปิดดูจริงทุกใบ** และรูปที่มีแบรนด์ปนมักครอปแยกส่วนที่ใช้ได้ออกมาได้
- ยังใช้ไม่ได้จริง ๆ: โฟลเดอร์ Experts (เห็นชื่อไฟล์ Fenix Pro กลางภาพ) · Navigator (เห็น Fenix Pro) · กราฟ XAUUSD · แท็บ Inputs (เปิดพารามิเตอร์กลยุทธ์ ห้ามเด็ดขาด) · หน้าสมัคร Zaurix 5 รูป · รายการโบรกเกอร์และหน้าเลือกเซิร์ฟเวอร์ที่ Zaurix เป็นสาระของภาพ
- **ผมสร้างภาพเองไม่ได้** (ไม่มีเครื่องมือสร้างภาพ และถ่ายจอโปรแกรมบนเครื่องเจ้าของไม่ได้) แต่**ถ่ายหน้าเว็บสาธารณะได้** ด้วย headless Chrome ซึ่งใช้ได้กับช่องที่เป็นหน้าเว็บ เช่น Play Store, App Store, หน้าดาวน์โหลด MT5 และหน้าเว็บโบรกเกอร์เมื่อเจ้าของเลือกโบรกแล้ว
- สถานะช่องรูปหลังรอบนี้: /how-to-install/ ขั้น 1 และ 2 เป็นภาพจริง ขั้น 3 ถึง 6 ยังเป็น placeholder · /mt5-login/ 3 จาก 6 · /vps-windows/ ครบ 6 · /vps-ios/ 3 จาก 6 · /vps-android/ 1 จาก 6 · /open-mt5-account/ 0 จาก 6 (รอเลือกโบรกเกอร์)
- **/how-to-install/ ตอนนี้ 4 จาก 6 ขั้นเป็นภาพจริง** (media 118, 119, 122, 123): ขั้น 1 หน้าดาวน์โหลด MetaQuotes · ขั้น 2 เมนู File > Open Data Folder (ครอปแท็บ XAUUSD ออก) · ขั้น 3 หน้าต่าง Navigator ที่กาง Expert Advisors (**ครอปบรรทัด "Fenix Pro" ที่อยู่ล่างสุดออก** แล้วขยาย 3 เท่าแบบ nearest ให้คม) · ขั้น 4 ปุ่ม Algo Trading สีเขียวบนแถบเครื่องมือ (ครอปจากมุมซ้ายบนของภาพกราฟ ก่อนถึงแท็บ XAUUSD)
- **ขั้น 5 และ 6 เอาจากฟินิกซ์ไม่ได้จริง ๆ**: ขั้น 5 ต้องการภาพแผง Dashboard ซึ่ง**คู่มือฟินิกซ์ไม่มีภาพนี้เลย** (เขามี 6 ภาพคือ เมนู File, โฟลเดอร์ Experts, Navigator, กราฟ, แท็บ Common, แท็บ Inputs) และต่อให้มีก็เป็น Dashboard ของฟินิกซ์ไม่ใช่ของ EA2000 · ขั้น 6 ภาพที่ตรงกันคือแท็บ Inputs ซึ่งค่าพารามิเตอร์คือเนื้อหาทั้งหมดของภาพ ครอปแล้วไม่เหลืออะไร และเป็นการเปิดเผยกลยุทธ์ที่ห้ามไว้

## 23) หน้า /how-to-install/ ทำตามคู่มือฟินิกซ์ 1 ต่อ 1 (10 ก.ย. 2026 · commit 460e81f · deploy แล้ว v1.5.0)
- เจ้าของสั่งชัดว่า **ให้ทำตามฟินิกซ์ตรง ๆ จะได้ใช้รูปชุดเดียวกันได้** ไม่ต้องคิดโครงใหม่ · ผมเสนอจะแตกเป็น 8 ขั้นซึ่งทำให้รูปของฟินิกซ์ใช้ไม่ตรงขั้น เจ้าของไม่พอใจถูกต้องแล้ว · **บทเรียน: ถ้าเจ้าของบอกให้ทำตามต้นแบบ ให้ทำตามต้นแบบ อย่าเพิ่มขั้นตอนหรือปรับโครงเอง**
- ขั้นตอนใหม่ 6 ขั้น (`inst_step1..6_*` ใน `ea2000_defaults()`) เรียงตามคู่มือฟินิกซ์: เปิด Open Data Folder → วางไฟล์ใน MQL5 > Experts → หา EA ใน Navigator → ลากลงกราฟ → ติ๊ก Allow Algo Trading → ตรวจ Inputs แล้วกด OK · ขั้นที่ 7 ของฟินิกซ์ ("เช็กก่อนใช้งานจริง") ซ้ำกับกล่อง "สิ่งที่ต้องเตรียม" ที่มีอยู่แล้วบนหน้าเดียวกัน จึงไม่ทำซ้ำ · ข้อความเขียนใหม่เป็นสำนวน EA2000 ไม่ก๊อปคำต่อคำ
- **รูปจากฟินิกซ์ที่ครอปแล้วใช้ได้ 5 ช่อง** (ครอปด้วย System.Drawing แล้วอัปโหลดผ่าน REST · alt ไทยครบ):
  - ขั้น 1 media **119** `ea2000-install-02-open-data-folder.png` เมนู File ที่กาง Open Data Folder (ครอปแท็บ XAUUSD ออกแล้ว)
  - ขั้น 2 media **127** `ea2000-step2-experts-folder.png` 1040x272 โฟลเดอร์ Experts **ครอปแถวไฟล์ "Fenix Pro" ออก**
  - ขั้น 3 media **122** `ea2000-install-03-navigator.png` หน้าต่าง Navigator
  - ขั้น 4 media **128** `ea2000-step4-chart.png` 1400x560 กราฟแท่งเทียน **ครอปแท็บ XAUUSD.c และป้ายชื่อ EA ออก**
  - ขั้น 5 media **129** `ea2000-step5-allow-algo.png` 728x162 ช่อง Allow Algo Trading + ปุ่ม OK **ครอปแถบหัวหน้าต่าง "Fenix Pro 4.00" ออก**
- **ขั้น 6 เว้นเป็นช่องเส้นประไว้** (`inst_step6_img` = `""` ผ่าน `ea2000/v1/mods`) เพราะรูปแท็บ Inputs ของฟินิกซ์เปิดพารามิเตอร์ทั้งชุด ห้ามใช้ตามข้อ 18 · เจ้าของถ่ายเองโดยปิดบังค่าได้
- **ข้อควรระวังเรื่องค่า default ของช่องรูป**: ส่ง `null` ไปที่ `ea2000/v1/mods` = ลบ mod แล้วกลับไปใช้ default ซึ่งเป็นภาพ placeholder `assets/img/install/guide-0N.webp` ไม่ใช่ช่องว่าง · ถ้าต้องการให้ขึ้นกรอบเส้นประบอกให้ใส่รูป ต้องส่ง `""` เท่านั้น
- media 123 (ครอปปุ่ม Algo Trading บนแถบเครื่องมือ) ไม่ได้ใช้บนหน้านี้แล้ว · media 118 (หน้าดาวน์โหลด MetaQuotes) ยังใช้ที่ /mt5-login/ ขั้น 1
- `docs/image-shot-list.md` อัปเดตเป็น 22 ภาพ (จาก 23) หน้า /how-to-install/ เหลือช่องเดียว

### ขั้น 6 ใส่รูปแล้ว ไม่ต้องรอเจ้าของ (10 ก.ย. 2026 · commit ถัดจาก 5261f46 · deploy แล้ว v1.5.1)
- เจ้าของถามว่าทำไมขั้น 6 ยังว่าง · คำตอบเดิม "รูปแท็บ Inputs ของฟินิกซ์เปิดพารามิเตอร์ทั้งชุดจึงใช้ไม่ได้" ถูกเฉพาะกับ**ภาพต้นฉบับ** แต่ผมข้ามทางออกที่ง่ายที่สุดไป คือ **ปิดบังค่าแล้วใช้โครงหน้าต่างที่เหลือ**
- วิธีทำ: ffmpeg ครอปพื้นที่ตารางพารามิเตอร์ (`crop=528:573:19:72`) แล้ว `boxblur=9:2` สามรอบ ทับกลับด้วย `overlay=19:72` · เบลอสามชั้นระดับนี้กู้ข้อความคืนไม่ได้ · สิ่งที่ยังอ่านออกและเป็นสาระของภาพคือ แท็บ Common/Inputs · หัวตาราง Variable/Value · ปุ่ม Load, Save, OK, Cancel, Reset ซึ่งตรงกับสิ่งที่ข้อความขั้น 6 สอนพอดี · แถวแรก `InpEAName = Fenix Pro` อยู่ในพื้นที่ที่เบลอด้วย
- อัปโหลดเป็น media **130** `ea2000-step6-inputs.png` · ตั้ง `inst_step6_img` แล้ว · **/how-to-install/ มีภาพจริงครบทั้ง 6 ขั้น ไม่มีช่องเส้นประเหลือ**
- ต่อท้าย `inst_step6_desc` ว่า "ภาพตัวอย่างนี้เบลอค่าพารามิเตอร์ไว้ ค่าจริงทีมงานส่งให้ตอนติดตั้ง" เพื่อไม่ให้คนอ่านคิดว่าภาพเสีย · `inst_step6_img_alt` แก้ให้ตรงกับภาพที่เบลอ
- **บทเรียนซ้ำรอบสอง**: ก่อนจะสรุปว่ารูปใช้ไม่ได้ ให้ถามก่อนว่า "ครอปหรือปิดบังส่วนที่มีปัญหาออกแล้วยังเหลือสาระพอไหม" ครั้งนี้เหลือพอ · เรื่องนี้ต่อจากข้อ 22 ที่เคยตัดสินหยาบมาแล้วครั้งหนึ่ง
- `docs/image-shot-list.md` เหลือ 21 ภาพ ไม่มีรายการของหน้า /how-to-install/ แล้ว

## 24) /open-mt5-account/ เขียนใหม่เป็นคู่มือสมัคร Zaurix (10 ก.ย. 2026 · commit 3 ตัวถัดจาก b0a976a · deploy แล้ว v1.6.1)
- เจ้าของทักว่าหน้านี้น่าจะใช้รูปของฟินิกซ์ได้เลย · ตรวจแล้วพบว่า**สองหน้าไม่ใช่หน้าเดียวกัน** ของฟินิกซ์คือกรวยพาไปเปิดบัญชี ZAURIX เพื่อรับ EA ฟรี ส่วนของ EA2000 เดิมเขียนกลาง ๆ ว่าเลือกโบรกไหนก็ได้ และมีบรรทัด "ไม่ได้เป็นตัวแทนของโบรกเกอร์รายใด" ซึ่งจะขัดกับรูปที่มีโลโก้ ZAURIX เต็มทุกใบ
- **เจ้าของตัดสินใจ 10 ก.ย. 2026: เขียนเป็นคู่มือ Zaurix เลย และเอาข้อเสนอ "เปิดบัญชีแล้วรับ EA ฟรี" มาใช้เหมือนฟินิกซ์** → `$g['gacct']` ใน `inc/guide-pages.php` เขียนใหม่ทั้งบล็อก 6 ขั้น (สมัคร → ยืนยันอีเมล → Dashboard → แท็บ User verify → กรอก KYC → รับรหัส MT5 แล้วแจ้ง User ทาง LINE) · ลบบรรทัดความเป็นกลางเรื่องโบรกเกอร์ออกแล้ว
- **เพิ่มการเปิดเผยผลประโยชน์**: หัวข้อ "ทำไมต้องเปิดบัญชีผ่านลิงก์ของ EA2000" ระบุตรง ๆ ว่า EA2000 ได้ค่าตอบแทนในฐานะพันธมิตรของโบรกเกอร์ตามปริมาณการเทรด และค่าตอบแทนนี้ไม่ได้หักจากบัญชีลูกค้า · **ถ้าข้อเท็จจริงเรื่องรูปแบบค่าตอบแทนไม่ตรง ให้เจ้าของแก้ข้อความนี้** ผมเขียนจากตรรกะของข้อเสนอ ไม่ได้รับยืนยันรายละเอียดสัญญา
- **ไม่มีลิงก์สมัครบนหน้า** เพราะหน้าฟินิกซ์เองก็ไม่มี (ปุ่มเป็น anchor เลื่อนลงในหน้า) เข้าใจว่าส่งลิงก์ให้ทาง LINE → ข้อความจึงเขียนว่า "ขอลิงก์สมัครจากทีมงานทาง LINE" ทั้งใน intro, quick, ขั้น 1 และหัวข้อเสริม · ถ้าเจ้าของให้ลิงก์ IB มา ค่อยเพิ่ม setting กับปุ่มทีหลัง
- **รูป 5 ใบจากฟินิกซ์ ทำความสะอาดแล้วอัปโหลด media 131 ถึง 135** (`gacct_step1..5_img` · ขั้น 6 เว้นว่างเพราะเป็นขั้นทัก LINE ไม่มีหน้าจอ):
  - 131 หน้าสมัคร **ทับช่อง Email ที่มีอีเมลจริงของผู้สมัคร** และครอปพื้นหลังเหลือเฉพาะฟอร์ม
  - 132 อีเมลยืนยัน **ครอปแถบบุ๊กมาร์กกับ URL และกล่องจดหมายออก เหลือเฉพาะการ์ดอีเมล** แล้วเบลอลิงก์ยืนยันที่มี token
  - 133 หน้า Dashboard ครอป browser chrome ออก
  - 134 หน้า User Account **เบลอแถวชื่อจริง UID อีเมล เบอร์โทร**
  - 135 หน้า KYC **เบลอชื่อ นามสกุล และเบอร์โทร** แล้วครอปแถบบุ๊กมาร์กออก
- **ข้อควรระวังของ ffmpeg**: ต่อ overlay หลายชั้นใน `-filter_complex` เดียวโดยอ้าง `[0:v]` ซ้ำ **ทำงานแค่ชั้นสุดท้าย** ชั้นก่อนหน้าเงียบหายไปโดยไม่มี error ครั้งนี้ทำให้ชื่อในรูป KYC ไม่ถูกเบลอตอนแรก · ให้เบลอทีละกล่อง ทีละคำสั่ง แล้วเปิดดูผลทุกครั้ง
- ชื่อเพจ 94 เปลี่ยนเป็น "เปิดบัญชี MT5 กับ Zaurix แล้วรับ EA2000 ฟรี" (H1 ตามชื่อเพจ) · Yoast title/desc/og/twitter ตั้งใหม่ · focus keyphrase คงเป็น `เปิดบัญชี mt5` เพราะยังเป็นคำเชิงข้อมูลที่หน้านี้ตอบจริง · เมนูย่อย item 101 เปลี่ยนป้ายเป็น "สมัครบัญชีและรับ EA ฟรี"
- **ค้าง**: หน้า /pricing/ ยังไม่ได้พูดถึงทางเลือก "เปิดบัญชีแล้วได้ฟรี" เลย ตอนนี้สองหน้ายังไม่อ้างถึงกันแบบสองทาง (หน้าคู่มืออ้างไปหาหน้าแพ็กเกจแล้ว แต่หน้าแพ็กเกจยังไม่อ้างกลับ) ควรเติมเมื่อเจ้าของยืนยันเงื่อนไข เช่น ทุนขั้นต่ำ หรือระยะเวลาที่ต้องคงบัญชีไว้
- `docs/image-shot-list.md` เหลือ 15 ภาพ (ตัดรอบโบรกเกอร์ 6 ภาพออกทั้งรอบ)

## 25) /mt5-login/ เขียนใหม่ผูก Zaurix และใช้รูปฟินิกซ์ (10 ก.ย. 2026 · deploy แล้ว v1.7.1)
- ต่อเนื่องจากข้อ 24 · เมื่อเจ้าของยืนยันว่า EA2000 ใช้ Zaurix เหมือนกัน **รูปกลุ่ม "ใช้ไม่ได้เพราะผูกโบรกเกอร์ Zaurix" ในข้อ 18 ใช้ได้ทั้งหมดแล้ว** ให้ถือว่าข้อ 18 ส่วนนั้นเป็นประวัติ
- `$g['gmt5']` เขียนใหม่ทั้งบล็อก 6 ขั้นตามคู่มือฟินิกซ์: ติดตั้งแอป → เข้าเมนูเพิ่มบัญชี → พิมพ์ค้นหา zaurix → เลือก Zaurix Ltd. → ตรวจ Zaurix-Server แล้วกรอก Login กับ Password → กดลงชื่อเข้าใช้แล้วเช็ก · **ต่างจากฟินิกซ์ตรงที่ยังพูดถึงคอมพิวเตอร์ควบคู่มือถือทุกขั้น** เพราะแอปมือถือรัน EA ไม่ได้ และหน้าฟินิกซ์เป็นมือถือล้วนซึ่งเป็นช่องว่างของเขา
- เพิ่มหัวข้อเสริมที่ 4 ใหม่ "ล็อกอินบนมือถือได้แล้ว แต่ EA ยังไม่ทำงาน" อธิบายว่าแอปมือถือติดตั้ง EA ไม่ได้ ต้องใช้คอมหรือ VPS แล้วโยงไปคู่มือ VPS ทั้งสามหน้า
- ชื่อเพจ 93 เปลี่ยนเป็น "ติดตั้ง MT5 และล็อกอินเข้า Zaurix-Server" · Yoast ตั้งใหม่ครบ · focus keyphrase `ล็อกอิน mt5` · เมนูย่อย item 102 เป็น "ติดตั้ง MT5 และล็อกอิน Zaurix"
- **ใช้ workflow ตรวจรูป 13 ใบทีละใบ (27 agent)** แล้วได้ข้อเท็จจริงที่การดูผ่าน ๆ มองไม่เห็น
  - 13 ไฟล์เป็น **หน้าจอจริงแค่ 3 หน้า** ที่เหลือเป็นครอปย่อยและไอคอนประดับ (โลโก้ Apple กับหุ่นยนต์ Android)
  - `mt5-login-zaurix-server-08` เหมือน `-03` **ทุกไบต์** (md5 ตรงกัน) และ `-11` เหมือน `-13` ทุกไบต์ ถ้าไม่ตรวจจะอัปโหลดซ้ำ
  - ช่อง Login และ Password ในทุกภาพ **ว่างเปล่าเป็น placeholder** ไม่มีเลขบัญชีจริงหลุดเลย จึงไม่ต้องเบลออะไรทั้งชุด
  - ภาพเต็มจอ 710x1537 มีพื้นดำว่างกลางจอ 600 ถึง 900 พิกเซล → ตัดออกแล้วต่อกลับด้วย `split=2` + `vstack` เหลือ 637 ถึง 997 พิกเซล อ่านง่ายขึ้นมาก
  - `-12` เป็นปุ่มลงชื่อเข้าใช้ **สถานะกดไม่ได้ (สีเทา)** ถ้าเอาไปวางขั้น 6 ที่สั่งให้กดปุ่มจะขัดกันเอง จึงไม่ใช้
- รูปที่ใช้จริง media **138 ถึง 141**: ขั้น 2 รายการโบรกเกอร์ · ขั้น 3 ช่องค้นหาที่พิมพ์ zaurix พร้อมผลลัพธ์ · ขั้น 4 แถว Zaurix Ltd. · ขั้น 5 ฟอร์มล็อกอินที่เห็น Zaurix-Server · ขั้น 1 ยังใช้ media 118 หน้าดาวน์โหลด MetaQuotes · **ขั้น 6 เว้นว่างไว้** เพราะไม่มีภาพหลังล็อกอินสำเร็จในชุดของฟินิกซ์
- media 120 และ 121 (ครอปเก่าของหน้านี้) เลิกใช้แล้ว ยังอยู่ในคลังสื่อ
- **บทเรียนเรื่อง ffmpeg เพิ่มอีกข้อ**: การอ้าง `[0:v]` สองครั้งใน filter_complex เดียวเพื่อ crop สองส่วนแล้ว vstack **ต้องใส่ `split=2` ก่อน** ไม่งั้นได้ผลไม่ครบแบบเงียบ ๆ เหมือนกรณี overlay ในข้อ 24
- `docs/image-shot-list.md` เหลือ 13 ภาพ

## 26) การ์ดดาวน์โหลด ขั้นตอนแบบเต็มความกว้าง และหน้า VPS ครบทั้ง 3 หน้า (10 ก.ย. 2026 · deploy แล้ว v1.9.1)
### คอมโพเนนต์ใหม่ในธีม
- **การ์ดดาวน์โหลดแอป** `ea2000_guide_store_cards( $prefix )` ใน `inc/guide-pages.php` · แสดงใต้ข้อความของขั้นที่ 1 ของหน้าคู่มือ · เปิดใช้เฉพาะ prefix ใน `ea2000_guide_dl_prefixes()` = `gmt5`, `gvand`, `gvios` · การ์ดละ 6 คีย์ (`_label`, `_sub`, `_steps`, `_url`, `_icon`, `_note`) สูงสุด 3 การ์ด บวก `{p}_dl_title` · การ์ดจะซ่อนเองเมื่อไม่มีชื่อหรือไม่มีลิงก์
- ไอคอน Apple, Android, Windows วาดเป็น SVG ใน `ea2000_store_icon()` ใช้ `currentColor` ไม่ต้องโหลดไฟล์เพิ่ม
- **ขั้นตอนแบบเต็มความกว้าง** `template-guide.php` ตรวจว่าขั้นนั้นมี `_img` หรือ `_img_note` หรือไม่ · ถ้าไม่มีทั้งคู่จะไม่พิมพ์ `.guide-step-media` และใส่คลาส `.guide-step--wide` ให้ข้อความกินเต็มแถว · **วิธีทำให้ขั้นใดขั้นหนึ่งไม่มีภาพและไม่มีช่องเส้นประ: ตั้งทั้ง `_img` และ `_img_note` เป็น `""`**
- CSS section 45 · `.dl-block`, `.dl-cards`, `.dl-card`, `.dl-steps`, `.dl-btn` (ใช้ทรง keycap ย่อ), `.dl-note`, `.guide-step--wide`
- **บทเรียนเรื่อง CSS specificity**: `.guide-step-text p { margin: 0 }` มี specificity สูงกว่า `.dl-note` จึงลบ `margin-top: auto` ทิ้ง ทำให้ปุ่มในการ์ดไม่เรียงแนวเดียวกัน · ต้องเขียนเป็น `.dl-card .dl-note` · เจอบั๊กนี้ตอนดูภาพหน้าจอ ไม่ใช่ตอนอ่านโค้ด · ตรวจ specificity ทุกครั้งที่เพิ่มคอมโพเนนต์ใหม่ในบล็อกที่มีสไตล์อยู่แล้ว
- อีกบั๊กที่เจอ: คลาสมุม HUD คือ `.hud-c.tl` ไม่ใช่ `.hud-c.hud-tl` เขียนผิดแล้วมุมทั้งสี่ไปกองเป็นสี่เหลี่ยมเขียวที่มุมซ้ายบน

### /mt5-login/ ขั้น 1 และขั้น 6
- ขั้น 1 เปลี่ยนจากภาพหน้าดาวน์โหลด MetaQuotes มาเป็น **การ์ด 3 ใบ iPhone/iPad, Android, คอมพิวเตอร์ (Windows)** พร้อมปุ่มไปร้านแอปจริง (App Store id413251709 · Play Store net.metaquotes.metatrader5 · metatrader5.com/en/download) · เจ้าของสั่งให้ทำแบบฟินิกซ์ที่มีปุ่ม และผมเพิ่มการ์ดคอมพิวเตอร์เข้าไปด้วยเพราะเป็นเครื่องเดียวที่รัน EA ได้
- ขั้น 6 **ไม่มีภาพและไม่มีช่องเส้นประ** ตามคำสั่งเจ้าของว่าถ้าฟินิกซ์ไม่มีก็ไม่ต้องมี · ข้อความกินเต็มแถว
- media 118 (หน้าดาวน์โหลด MetaQuotes) เลิกใช้แล้ว

### หน้า VPS 3 หน้า · ตรวจแล้วว่าฟินิกซ์ไม่มีอะไรเหลือให้ยืมเพิ่ม
ดึงภาพจากทั้ง 3 หน้าของฟินิกซ์ใหม่ด้วย `--dump-dom` แล้วนับได้ตามนี้
- **/vps-desktop-windows/ 6 ภาพเป็น PNG หน้าจอจริง** ทั้งหมดอยู่บน EA2000 แล้ว (media 106 ถึง 111) ครบ 6 ขั้น ไม่มีอะไรต้องทำเพิ่ม
- **/vps-android/ ทั้ง 6 ภาพเป็น SVG ที่ฟินิกซ์วาดเอง** ขนาด 1.1 ถึง 1.7 KB ไม่ใช่ภาพถ่ายหน้าจอเลย
- **/vps-iphone-ios/ มีภาพถ่ายจริง 3 ใบ (01, 03, 04) และ SVG วาดเอง 3 ใบ (02, 05, 06)** ภาพถ่ายจริงทั้งสามอยู่บน EA2000 แล้ว (media 112 ถึง 114)
- ฟินิกซ์ไม่มีลิงก์ร้านแอปแปะไว้ในสองหน้านี้เลย

### เอา SVG ของฟินิกซ์มาปรับสีใช้ต่อ (media 142 ถึง 149)
- สคริปต์ `scratchpad/vps/recolor.js` แทนสี `#ff8400` เป็น `#8CEC4E` (`--ink-accent`) · `#ffb148` เป็น `#C9F5A6` (`--accent-2`) · `#1b140e` เป็น `#0F2016` (`--ink-raise`) · `#0b0b0c` เป็น `#08150E` (`--ink`) · สีเทาและสีที่สื่อความหมายเก็บไว้ตามเดิม
- **4 ไฟล์มีข้อความ "FENIX PRO VPS" อยู่ในภาพ** (android-05, android-06, ios-05, ios-06) เปลี่ยนเป็น "EA2000 VPS" · **จับได้จากการเปิดภาพดู ไม่ใช่จากการ grep รอบแรก** เพราะรอบแรกผมมองแต่โค้ดสี
- ใส่ font-family เป็นฟอนต์ระบบให้ root svg เพราะเว็บฟอนต์โหลดไม่ได้เมื่อ SVG ถูกฝังเป็น `<img>` (เดิมขึ้นเป็น Times)
- เรนเดอร์เป็น PNG 880x720 ด้วย headless Chrome พร้อม `--disable-lcd-text --font-render-hinting=none` เพื่อไม่ให้มีขอบสีฟ้ากับส้มจาก subpixel antialiasing (ผู้ตรวจสแกนพิกเซลแล้วเจอ 1,100 ถึง 1,500 จุดต่อภาพ ซึ่งไม่ใช่สีแบรนด์แต่ทำให้การตรวจในอนาคตขึ้นผลบวกลวง)
- **ตั้ง alt ทุกใบว่า "ภาพประกอบ..." ไม่ใช่ "ภาพหน้าจอ..."** เพราะเป็นภาพวาด ไม่ใช่ภาพถ่ายจริง · ห้ามเขียนให้เข้าใจผิดว่าเป็นหน้าจอจริง
- ตรวจด้วย workflow 12 agent (ตรวจทีละใบ แล้วส่งใบที่สงสัยไปให้ผู้ตรวจคนที่สองพยายามค้าน) ผ่าน 7 จาก 9 · **2 ใบที่ไม่ผ่านไม่ใช่ปัญหาของภาพ แต่เป็นเพราะหัวข้อขั้นที่ 6 ของ EA2000 เขียนรวมสองการกระทำ** ("บันทึกแล้วแตะเพื่อเชื่อมต่อ") ขณะที่ภาพสื่อแค่ตอนตรวจค่าก่อนกด Save · แก้หัวข้อเป็น "ตรวจค่าให้ครบแล้วกด Save" แล้วย้ายส่วนแตะเพื่อเชื่อมต่อกับ Certificate ไปอยู่ในคำอธิบาย ตรงกับที่ฟินิกซ์แบ่งไว้เดิม
- ผู้ตรวจจับได้อีกจุดที่ผมพลาด: ถ้าเปลี่ยนแค่หัวข้อ **`_img_alt` กับ `_img_note` ที่สร้างจากสมาชิกตัวที่ 3 ของ steps จะยังบรรยายภาพผิด** ต้องแก้ทั้งคู่
- **การ์ดดาวน์โหลด Windows App** เพิ่มในขั้นที่ 1 ของทั้งสองหน้า (Play Store `com.microsoft.rdc.androidx` · App Store `id714464092` ชื่อในร้านคือ Windows App Mobile) ตรวจแล้วว่าทั้งสองลิงก์ตอบ 200 และเป็นแอปของ Microsoft Corporation จริง
- ผลรวม: **ทั้ง 3 หน้า VPS มีภาพครบทุกขั้น ไม่มีช่องเส้นประเหลือ**
- `docs/image-shot-list.md` เขียนใหม่: จำเป็น 3 ภาพ (หน้าแรก 2 · /mt5-login/ ขั้น 6 อีก 1) · ผลทดสอบจริง 2 ภาพเมื่อมีข้อมูล · และรายการเสริม 8 ภาพถ้าเจ้าของอยากถ่ายจริงมาทับภาพวาด

## 27) หน้า /how-to-install/ สร้างใหม่รอบภาพที่เจ้าของทำเอง (10 ก.ย. 2026 · deploy แล้ว v2.0.0)
- เจ้าของส่งภาพ 6 ใบที่ทำเองไว้ที่ `D:\EA VIDEO\EA2000\ea2000-install-ea-mt5-step-01..06-*.png` (1672x941 · PNG ใบละ 1.4 ถึง 1.6 MB) มีเลขขั้นและหัวข้อไทยฝังอยู่ในภาพ
- **เลขขั้นและหัวข้ออยู่ในภาพ หน้าเว็บจึงต้องเรียงตามภาพเป๊ะ** ไม่ใช่ให้ภาพตามหน้าเว็บ · เขียน `inst_step1..6` ใหม่ทั้งหมดให้ตรงกับที่เขียนไว้บนภาพ
  1. ติดตั้ง MetaTrader 5 และเตรียม VPS
  2. เปิดโฟลเดอร์ Experts แล้วนำไฟล์ EA เข้า
  3. ลาก EA ขึ้นกราฟ และตั้งค่า
  4. เปิด AutoTrading
  5. ตรวจสอบการทำงานผ่าน Dashboard
  6. ปรับความเสี่ยงให้เหมาะกับตัวเอง
- โครงนี้**ไม่ใช่ของฟินิกซ์แล้ว** ต่างจากข้อ 23 ที่ทำตามฟินิกซ์ 6 ขั้น · ภาพจากฟินิกซ์ที่เคยใช้บนหน้านี้ (media 119, 122, 127, 128, 129, 130) เลิกใช้ทั้งหมด ยังอยู่ในคลังสื่อ
- แปลงเป็น WebP กว้าง 1400 คุณภาพ 90 ด้วย ffmpeg เหลือใบละ 119 ถึง 156 KB (จากเดิมรวม 8.9 MB เหลือ 807 KB) อัปโหลดเป็น media **150 ถึง 155**
- **ฟีเจอร์ใหม่ คำบรรยายใต้ภาพ**: `ea2000_media_slot()` อ่าน `{key}_img_caption` เองเมื่อเทมเพลตไม่ได้ส่ง caption มา · ใช้ได้กับทุกช่องรูปทั้งเว็บโดยไม่ต้องแก้เทมเพลต · เพิ่ม `inst_step1..6_img_caption` พร้อม control ใน customizer

### เรื่องที่ต้องบอกเจ้าของและยังรอคำตอบ
1. **ภาพขั้นที่ 5 มีตัวเลขกำไร** แผง Dashboard ในภาพเขียน Balance 10,254.32 · Equity 10,842.71 · Profit +588.39 (5.73%) · Floating P/L +156.72 และ log ข้างล่างเขียนว่า Take profit hit. Profit: 78.56 · ข้อ 3.2 ของไฟล์นี้ห้ามใส่ตัวเลขผลทดสอบสมมติบนเว็บ · **แก้ชั่วคราวด้วยการใส่คำบรรยายใต้ภาพว่า "ตัวเลขในภาพเป็นตัวอย่างเพื่อให้เห็นหน้าตาของแผง ไม่ใช่ผลการเทรดจริง"** ทางที่สะอาดกว่าคือเจ้าของลบบรรทัด Profit กับ Floating P/L ออกจากภาพ
2. **ภาพขั้น 1 ถึง 5 เป็นกราฟทองทั้งหมด** (XAUUSDm · Gold vs US Dollar) และแผง Dashboard เขียน Symbol XAUUSDm · ขัดกับจุดยืนที่เจ้าของสั่งไว้เองในข้อ 10 ว่าเทรดหลายคู่เงินไม่เจาะจงทอง ซึ่งเป็นเหตุผลที่ทั้งเว็บตัดคีย์เวิร์ดสายทองออก · **เนื้อหาตัวอักษรบนเว็บยังไม่มีคำว่าทองแม้แต่คำเดียว เปลี่ยนแค่ภาพ**
3. **ภาพขั้น 3 กับขั้น 6 เปิดค่าพารามิเตอร์** (Risk %, Max Drawdown, Stop Loss, Take Profit, Trailing Stop, Break Even, News Filter, Trading Hours, Magic Number) ซึ่งเป็นของประเภทเดียวกับที่เบลอทิ้งในข้อ 23 · และ **สองภาพให้ค่าไม่ตรงกัน** ขั้น 3 เขียน Magic 20240522 กับ Stop Loss 1500 points ส่วนขั้น 6 เขียน Magic 202505 กับ Stop Loss 150 pips · คนอ่านละเอียดจะจับได้
- ใส่คำบรรยายกำกับไว้ 3 ใบแล้ว (ขั้น 3, 5, 6) เพื่อไม่ให้ตัวเลขในภาพถูกอ่านเป็นผลจริงหรือค่าที่ต้องตั้งตาม

## 28) กล่องสินค้าใน hero หน้าแรก (10 ก.ย. 2026 · deploy แล้ว v2.0.1)
- เจ้าของทักว่ากล่องรูปด้านขวาของ hero อยู่ต่ำเกินไป · วัดที่ 1440x900 พบว่าขอบบนของกล่องอยู่ที่ 405px ขณะที่คอลัมน์ข้อความเริ่มที่ 184px ห่างกัน 221px และก้นกล่องตกขอบจอบนหน้าจอของเจ้าของ
- สาเหตุ: `.boot-visual` ตั้ง `align-self: end` คู่กับ `margin-bottom: -48px` เพื่อให้กล่องยื่นลงไปทับบล็อกถัดไป · กล่องเป็นสี่เหลี่ยมจัตุรัส 448px แต่คอลัมน์ข้อความสูง 621px จึงเหลือที่ว่างด้านบนขวาเยอะ
- แก้เป็น `align-self: center` และ `margin: 0` · คืน `.home-v3 .ch-what` เป็น `padding-top: var(--band-pad)` เพราะไม่มีอะไรยื่นลงมาให้ชดเชยแล้ว · ขอบบนขยับขึ้นเป็น 270px ตรวจแล้วทั้ง 1440, 1920 และ 375 (มือถือใช้กฎใน media query 960 อยู่แล้ว ไม่กระทบ)
- **บันทึกใน `docs/home-v2-spec.md` ข้อ 8 แล้วว่าลูกเล่นกล่องยื่นทับบล็อกถัดไปถือว่ายกเลิก** อย่าเอากลับมาโดยไม่ถามเจ้าของ
- วิธีวัดที่ใช้: เปิดหน้าใน Browser pane ตั้ง viewport แล้วอ่าน `getBoundingClientRect()` ของ `.boot-copy` กับ `.boot-visual` เทียบกัน แม่นกว่าการกะจากภาพหน้าจอ

## 29) บาร์ล่างมือถือเขียนใหม่ · Console Deck (10 ก.ย. 2026 · deploy แล้ว v2.3.0)
- เจ้าของบอกว่า "footer menu ออกแบบใหม่ทั้งหมด ไม่สวยเลย ย้ำว่าไม่สวยเลย ขอให้ดูมีลูกเล่นเหมือนเว็บแอปจริง ๆ" · **ผมตีความผิดรอบแรกไปทำคอลัมน์ในฟุตเตอร์เดสก์ท็อป** เจ้าของส่งภาพมาชี้ว่าหมายถึงบาร์ล่างบนมือถือ · **บทเรียน: ถ้าคำว่า footer กำกวม ให้ถามหรือดูภาพก่อนลงมือ**
- ออกแบบด้วย workflow 4 แนวคู่ขนาน (native, console, motion, wildcard) แล้วกรรมการ 3 มุมให้คะแนน (ความสวยกับความเข้ากับแบรนด์ / ทำได้จริง / มือถือกับการเข้าถึง) · คะแนนรวม console 24 · native 23.5 · wildcard 21 · motion 18
- **แนวที่ชนะคือ Console Deck** แก้ต้นเหตุด้วยการ **ถอดกรอบและพื้นของคีย์ที่ไม่ได้เลือกออกทั้งหมด** เหลือแค่ไอคอนกับป้าย ทำให้มีของที่ "มีวัสดุ" แค่สองชิ้นคือคีย์ของหน้าที่เปิดอยู่ กับคีย์ LINE ที่เจ้าของยืนยันว่าให้เด่นเหมือนเดิม
- องค์ประกอบ: รางไฟขอบบน (`.dock-rail`) · ดวงไฟชี้ตำแหน่งที่เลื่อนไปจอดตรงช่องของหน้าปัจจุบันพร้อมลำแสงสาดลง (`.dock-lamp` ตำแหน่งมาจากตัวแปร `--dock-x`) · เส้นบัสพาดใต้คีย์ · คีย์ที่เลือกยกตัวมีสันข้าง 3px ติดวงเล็บ HUD และ LED มุมคีย์ · คีย์ LINE ยกสูงจนเจาะรางบน · แตะแล้วคีย์ยุบจมลงในแผง · เลื่อนอ่านลงบาร์จะ **ย่อ ไม่ใช่ซ่อน** (85px เหลือ 57px ป้ายหุบ) เพื่อให้ปุ่ม LINE อยู่บนจอตลอด
- **หา active ฝั่ง PHP** ด้วย `$GLOBALS['wp']->request` เทียบกับ path ของแต่ละช่อง จึงถูกต้องตั้งแต่ HTML ที่เสิร์ฟ ปิด JS ก็ยังถูก และใส่ `aria-current="page"` ให้ · ตรวจจริงแล้ว: หน้าแรก `--dock-x:10%` · /pricing/ `70%` · /how-to-install/ `90%`
- **เว้นที่ท้ายหน้าเปลี่ยนวิธี** จาก `body.has-mobile-app-nav { padding-bottom }` (ต้องรอ JS) เป็น `div.dock-spacer` ที่เป็นกล่องจริงในสายเนื้อหา · สูง 85px เท่าความสูงบาร์พอดี · **ถ้าจะย้ายกลับไปใช้ body class ต้องลบ `.dock-spacer` พร้อมกัน ไม่งั้นเว้นที่ซ้อนสองชั้น**
- **แก้ `header.php` เติม `viewport-fit=cover`** ไม่งั้น `env(safe-area-inset-bottom)` คืน 0 เสมอบนไอโฟน โค้ด safe-area ทั้งชุดจะไม่ได้ทำอะไรเลย · **ยังไม่ได้ทดสอบบนไอโฟนที่มีติ่งจริง**
- **ห้ามใช้ `var(--ease-mech)` กับสิ่งที่เคลื่อนที่ในบล็อกนี้** เพราะของธีมคือ `steps(4, end)` จะกระตุกเป็น 4 จังหวะ · บล็อกนี้ประกาศ `--dock-ease` กับ `--dock-ease-press` เอง และเหลือ `--ease-mech` ไว้กับการเปลี่ยนสีเท่านั้น
- ตัวเลขของบาร์อยู่ในตัวแปรชุดเดียวที่ `html` ในเบรกพอยต์ 760px · `--dock-key-min` ต้องเท่ากับ cap + gap + label เสมอ ไม่งั้นจะมีแถบขาวโผล่เหนือบาร์ตอนเลื่อนถึงท้ายหน้า
- วัดจริงแล้ว: พื้นที่กด 60x72 px (เกิน 44x44) · หน้าไม่เลื่อนแนวนอน · ความสูงบาร์กับ spacer ตรงกันที่ 85px · คอนทราสต์ป้ายที่ไม่ได้เลือก 10:1 · ไอคอน LINE 13.2:1
- **สิ่งที่ยังไม่ได้ตรวจ**: การย่อบาร์ตอนเลื่อน (`.is-slim`) เพราะ Browser pane ไม่ยอมให้หน้าเลื่อนด้วยสคริปต์ในโหมดจำลองมือถือ `pageYOffset` ค้างที่ 2 ตลอด · โค้ดมี guard ครบและถ้าไม่ทำงานผลคือบาร์ไม่ย่อเฉย ๆ ไม่พัง · **ต้องลองด้วยนิ้วจริงบนมือถือ** พร้อมกับกรณีไม่มีลิงก์ LINE (เหลือ 4 ช่อง) ที่ยังไม่ได้ทดสอบเช่นกัน
- อัปเดตสัญญา class ใน `docs/home-v2-spec.md` แล้ว · `docs/redesign-plan.md` ยังอ้างคลาสเก่าอยู่แต่เป็นเอกสารประวัติ ไม่ได้แก้

## 30) ภาพผลทดสอบสองใบที่ยังไม่เอาขึ้นเว็บ (10 ก.ย. 2026)
- เจ้าของส่ง `ea2000-forward-test-dashboard.png` (หน้าที่แสดงตัวว่าเป็น Myfxbook ของบัญชี EA2000 Forward Test Demo · Gain +68.24% ใน 32 วัน) และ `ea2000-mt5-strategy-tester-setup.png` มาให้ใส่ช่อง `tests_bt_img` กับ `tests_fw_img` · **ยังไม่ได้เอาขึ้นเว็บ รอเจ้าของตอบก่อน**
- **วิธีตรวจที่มาของภาพ ใช้ซ้ำได้ทุกครั้ง**: ไฟล์ PNG ที่สร้างด้วย AI ของ OpenAI จะมี chunk ชื่อ `caBX` (C2PA Content Credentials) ฝังอยู่ · ตรวจเร็วด้วย
  ```
  node -e 'const b=require("fs").readFileSync(F);const s=b.toString("latin1");console.log(s.includes("gpt-image"), s.includes("trainedAlgorithmicMedia"))'
  ```
  ภาพจับหน้าจอจริงไม่มี chunk นี้ · เจ้าของตรวจเองได้ที่ contentcredentials.org/verify
- **ผลตรวจ: ภาพที่เจ้าของส่งมาทั้งหมดมี marker นี้** ทั้งสองใบใหม่ และ **สไลด์ติดตั้ง 6 ใบที่ขึ้นเว็บไปแล้ว** (`softwareAgent = gpt-image` · `digitalSourceType = trainedAlgorithmicMedia` · ลงนามโดย OpenAI Media Service)
- **หลักฐานที่ไม่ต้องพึ่ง metadata เลย**: ในภาพ Myfxbook เทรด 5 แถวที่มองเห็นรวม 0.70 lots แต่แถวสรุปบอกว่าวันนี้มี 18 เทรด รวม 0.72 lots ซึ่งเป็นไปไม่ได้ · 5 แถวรวม 2,036 pips แต่แถวสรุปบอก 412.6 pips · กราฟ 32 วันไม่มีช่วงราบวันเสาร์อาทิตย์เลยทั้งที่ทองหยุดเทรด · Drawdown ประกาศ 12.36% แต่กราฟไม่เคยย่อเกิน 4%
- **ภาพ Strategy Tester ไม่มีผลการทดสอบอยู่เลย** แท็บที่เปิดคือ Settings และปุ่ม Start ยังไม่ถูกกด · ชื่อไฟล์เจ้าของก็เขียนว่า `setup`
- **ท่าทีที่ยึด**: ใบ Myfxbook ไม่เอาขึ้นเว็บไม่ว่าจะติดคำกำกับอย่างไร เพราะเลียนหน้าตาของผู้ให้บริการตรวจสอบผลเทรดที่มีอยู่จริงและแสดงตัวเลขผลตอบแทน · การติดป้ายว่าเป็นตัวอย่างไม่ช่วย เพราะปัญหาคือการยืมความน่าเชื่อถือของบุคคลที่สาม ไม่ใช่แค่ตัวเลขสมมติ
- ทั้งสองใบยังมี **XAUUSD** และใบแรกมี **Zaurix** ซึ่งขัดกับจุดยืนหลายคู่เงินในข้อ 10 และคำต้องห้ามในข้อ 15
- **สิ่งที่ต้องได้จากเจ้าของก่อนจะมีตัวเลขผลขึ้นเว็บ**: ลิงก์ Myfxbook หรือ FX Blue แบบสาธารณะ หรือไฟล์รายงาน HTML จาก Strategy Tester (Save as Report) · ภาพนิ่งใช้เป็นหลักฐานไม่ได้ · ธีมมี setting `forward_link_label` กับ `forward_link_url` รออยู่แล้ว ยังว่าง
- **ค้างตัดสินใจ**: สไลด์ขั้น 5 ที่ครอปไปใช้บนหน้าแรก 2 ช่อง (`what_img`, `how_img`) มีตัวเลขกำไร +588.39 (5.73%) ที่เป็นตัวเลขสมมติ · ตอนนี้ติดคำบรรยายกำกับไว้ว่าเป็นตัวอย่างไม่ใช่ผลจริง และเจ้าของอนุมัติให้ใช้แล้ว · ถ้าเจ้าของอยากให้สะอาดกว่านี้ ให้ลบบรรทัด Profit กับ Floating P/L ออกจากภาพแล้วส่งมาใหม่

## 31) เจ้าของยืนยันเรื่องสินทรัพย์ที่เทรด (10 ก.ย. 2026) · แก้ข้อ 10
- ถามตรง ๆ ว่า EA2000 เทรดทองด้วยหรือไม่ เพราะภาพที่ส่งมาเป็น XAUUSD ทั้งหมด · **เจ้าของตอบว่า "ก็เทรดทั้งหมดอ่ะ"** คือเทรดทุกสินทรัพย์ รวมทองด้วย
- **ข้อ 10 ที่เขียนว่า "ไม่เจาะจงทอง" และข้อ 15 ที่ใส่คำว่า ทอง gold XAUUSD ไว้ในรายการคำต้องห้าม ถือเป็นประวัติแล้ว** · ทองไม่ใช่คำต้องห้ามอีกต่อไป
- **แต่ยังไม่ควรเปลี่ยนแผนคีย์เวิร์ดกลับไปสายทองทั้งชุด** เพราะเหตุผลเดิมยังอยู่คือคลัสเตอร์ทองแข่งดุกว่ามาก และเว็บยังไม่มีผลทดสอบจริงมาสู้ · สิ่งที่ควรทำคือ **แก้ข้อความบนเว็บให้ตรงข้อเท็จจริง** เช่นช่อง "สินทรัพย์" ที่เขียนว่า "หลายคู่เงิน" ซึ่งตอนนี้แคบกว่าความจริง
- **เจ้าของยืนยันด้วยว่ายังไม่มีบัญชีทดสอบจริง** ไม่มี Myfxbook ไม่มีรายงาน Backtest · ช่อง `tests_bt_img` และ `tests_fw_img` จึงยังเป็นกรอบเส้นประต่อไป และ `backtest_img` กับ `forward_img` ยังว่าง
- **ภาพผลทดสอบสองใบในข้อ 30 เจ้าของยืนยันแล้วว่าเป็นภาพตัวอย่าง ไม่ใช่ผลจริง** จึงไม่เอาขึ้นเว็บทั้งสองใบ · ใบ Strategy Tester ก็ไม่เอาไปใช้เป็นภาพประกอบสอนด้วย เพราะค่าพารามิเตอร์ในภาพเป็นค่าที่ AI แต่งขึ้น การเผยแพร่เท่ากับระบุค่าที่ไม่ใช่ของจริงของสินค้า

## 32) ปลดดัชนีทั้งเว็บ และเชื่อม Google Search Console แล้ว (11 ก.ย. 2026)
- **เจ้าของสั่งปลด noindex เอง 11 ก.ย. 2026** ("ต้องการปล่อยอินเดก") และเอาติ๊ก "ขอให้เสิร์ชเอนจินไม่ทำดัชนี" ใน Settings > Reading ออกเองแล้ว · **คำสั่ง "ห้ามปลด noindex จนกว่าจะสั่ง" ในข้อ 12 ถือว่าจบแล้ว**
- ตรวจก่อนปล่อย: http → https 301 · www → ea2000.co 301 · หน้าแรก 200 ไม่มี redirect วน (ผู้ตรวจรายหนึ่งในรอบ audit รายงานว่ามี 301 วน ตรวจซ้ำแล้วไม่จริง) · robots.txt อนุญาต Googlebot และประกาศ sitemap · Cloudflare ปิดเฉพาะบอทเก็บข้อมูลเทรน AI (Google-Extended, GPTBot, CCBot ฯลฯ) ซึ่งไม่กระทบการค้นหา
- ตรวจหลังปล่อย: **ทั้ง 15 URL ใน sitemap ตอบ 200 เป็น index และ canonical ชี้ตัวเองครบ** · /go/ กับ /articles/ ยังเป็น `noindex, follow` ตามที่ตั้งรายหน้า และไม่อยู่ใน sitemap
- **Search Console: เจ้าของเลือกแบบ URL prefix `https://ea2000.co/` ยืนยันด้วยวิธีไฟล์ HTML** ยืนยันผ่านและส่ง `sitemap_index.xml` แล้ว
  - ไม่ได้อัปโหลดไฟล์เข้ารากเว็บ ธีมตอบให้เองผ่าน `ea2000_search_console_file()` (functions.php ต่อจาก `ea2000_verification_meta`) อ่านชื่อไฟล์จาก setting `search_console_file` = `google0d5cbfcc13907327.html` · รับเฉพาะรูปแบบ `google[0-9a-f]{8,32}.html` ชื่ออื่นตอบ 404
  - **ห้ามล้างค่า `search_console_file`** ถ้าไฟล์หาย Search Console จะถอดสิทธิ์เจ้าของ
  - ไม่มีแท็ก `google-site-verification` ในหน้าเลย (ตรวจแล้ว 0) จึงไม่ซ้ำซ้อนตามเงื่อนไขข้อ 7 ขั้น 6
  - ทางที่ครอบคลุมกว่าคือ Domain property ผ่าน DNS ของ Cloudflare (ครอบ www, http, https ในทีเดียว) ถ้าวันหลังอยากย้าย ทำเพิ่มได้โดยไม่ต้องลบของเดิม
- **per-page robots**: register `_yoast_wpseo_meta-robots-noindex` และ `-nofollow` ให้เขียนผ่าน REST ได้แล้ว (`inc/seo.php`) · ค่า `"1"` = noindex · `"2"` = index · `""` = ตามค่าเริ่มต้น · ตั้ง /go/ (54) และ /articles/ (35) เป็น `"1"` แล้ว · **เปิด /articles/ กลับเมื่อมีบทความจริง**
- `ea2000/v1/seo-options` รับคีย์กลุ่ม `wpseo_social` เพิ่ม: `og_default_image(_id)`, `og_frontpage_image(_id)`, `company_logo(_id)`, `twitter_card_type` · ตั้ง `company_logo_id` แล้ว endpoint สร้าง `company_logo_meta` ให้ใหม่เอง (Yoast แคช URL กับขนาดไว้ในนั้น ถ้าไม่อัปเดตตาม JSON-LD จะพ่นไฟล์เก่า)
- **ของที่พังอยู่เงียบ ๆ ก่อนหน้านี้และแก้แล้ว**: ทั้งเว็บไม่มี og:image เลยเพราะ media 84 ถูกลบ → ตั้งเป็น media 69 `ea2000-og-default.png` 1200x630 · โลโก้ Organization ของ Yoast ชี้ `ea2000-icon-512.png` ที่ถูกลบ ตอบ 404 ทุกหน้า → ตั้งเป็น media 125 `EA2000-LOGO.png` ให้ตรงกับที่ธีมประกาศ
- **สิ่งที่ต้องทำใน Search Console ต่อ (เจ้าของทำ)**: ตรวจสอบ URL แล้วกดขอการจัดทำดัชนีหน้าแรก /pricing/ /how-to-install/ · กลับมาดูรายงานการจัดทำดัชนีหน้าหลังผ่านไป 3 ถึง 7 วัน


## 33) ลิงก์ภายในทั้งเว็บ และหน้าแรกวนรีไดเรกต์จากแคชของโฮสต์ (11 ก.ย. 2026 · deploy แล้ว v2.5.4)
### ใส่ลิงก์ในเนื้อหาหน้าย่อยได้แล้ว
- `ea2000_rich_inline()` ใหม่ใน `inc/page-content.php` · ในช่องเนื้อหาหัวข้อ (`{prefix}_secN_text`) พิมพ์ `[ข้อความ](/slug/)` แล้วจะเป็นลิงก์ · ใช้ได้ในย่อหน้า รายการ และช่องตาราง **ไม่ใช้ในหัวข้อ `###` และหัวตาราง** · รับเฉพาะ path ที่ขึ้นต้นด้วย `/` ตัวเดียว ลิงก์ภายนอก `//` และ `javascript:` แสดงเป็นข้อความตามเดิม ทุกอย่างอื่นยัง escape · ใส่ `#gmt5-sec-3` ต่อท้ายเพื่อลิงก์ไปหัวข้อได้ (id ของหัวข้อคือ `{prefix}-sec-{N}`)
- ใส่ลิงก์ในเนื้อหาแล้ว 46 จุดทั้ง 11 หน้าที่ใช้ระบบนี้ (backtest 3 · forward 5 · pricing 6 · how-to-install 7 · risk 4 · go 7 · คู่มือ 5 หน้ารวม 14) · ตรวจแล้วไม่มีลิงก์ชี้หน้าตัวเอง ปลายทางตอบ 200 ทุกเส้น
- หน้าที่เนื้อหาเก็บใน WordPress เติมลิงก์ผ่าน REST: about 27 (6 เส้น) · privacy-policy 3 (ไป data-deletion และ terms) · terms-of-use 33 (เพิ่ม risk-disclosure)
- **แก้ข้อความที่ขัดกันเอง**: หัวข้อความเสี่ยงจากโบรกเกอร์ในหน้า /risk-disclosure/ เคยเขียนว่า "EA2000 ไม่ได้เป็นตัวแทนหรือรับผลประโยชน์จากโบรกเกอร์รายใด" ซึ่งขัดกับการเปิดเผยว่าเป็นพันธมิตร Zaurix ในหน้า /open-mt5-account/ (ข้อ 24) · เปลี่ยนเป็นบอกตรง ๆ ว่าได้ค่าตอบแทนเมื่อเปิดบัญชีผ่านลิงก์ของทีมงาน แล้วลิงก์ไปหน้าที่เปิดเผยรายละเอียด
- หัวข้อ VPS ในหน้า /how-to-install/ (installdoc_sec6) เคยซ้ำกับ /vps-windows/ เกือบคำต่อคำ · เขียนใหม่ให้สั้นและลิงก์ไปคู่มือ VPS ทั้ง 3 หน้าแทน
- /go/ เคยเขียนว่า "อ่านนโยบายได้จากลิงก์ท้ายเว็บไซต์" ซึ่งหน้านี้ไม่มี footer · เปลี่ยนเป็นลิงก์จริงไป privacy, terms, risk และหน้าแรก
- /articles/ รายการคู่มือเพิ่ม vps-android และ vps-ios ที่ขาดไป · คำถาม faq4 หน้าแรกเปลี่ยนเป็น "EA2000 เทรดคู่เงินอะไรบ้าง เทรดทองได้ไหม" ให้ตรงกับคำตอบ
- title ของ Yoast ที่ขาดชื่อแบรนด์ แก้แล้ว 4 หน้า (50, 90, 91, 92) · H1 ของ vps-android และ vps-ios เติม "สำหรับ EA MT5" · title หน้า 94 ตัด " · EA2000" ท้ายออกเพราะชื่อแบรนด์อยู่ในประโยคแล้ว
- คู่มือ VPS iPhone เขียนใหม่ให้ไม่ซ้ำหน้า Android (วลี 5 คำที่ซ้ำกันลดจาก 61 เหลือ 15 ซึ่งเป็นหัวข้อขั้นตอนที่ต้องเหมือนกัน) · หัวข้อแก้ปัญหาเป็นตารางตามอาการที่เจาะจง iOS · **ขั้นที่ 2 ทั้งสองหน้าเคยเขียนว่าปุ่มอยู่มุมขวาบน แต่ภาพประกอบวางปุ่มไว้ขวาล่าง** เปลี่ยนเป็นไม่ระบุมุมและบอกว่าหน้าตาอาจต่างตามเวอร์ชันแอป

### หน้าแรกวนรีไดเรกต์ใส่ตัวเอง
- ตรวจพบ 11 ก.ย. 2026 หลังปลดดัชนีไม่กี่ชั่วโมง: `https://ea2000.co/` ตอบ 301 ชี้กลับมาที่ `https://ea2000.co/` พร้อม `x-cache-status: HIT` · เบราว์เซอร์และ Googlebot เปิดหน้าแรกไม่ได้
- **ต้นเหตุ: แคชหน้าเว็บของโฮสต์ใช้คีย์ที่ไม่รวมชื่อโดเมน** พิสูจน์ด้วย URL ทดสอบที่ไม่ซ้ำ: เปิดแบบ www ก่อนได้ 301 ถูกเก็บ แล้วเปิดแบบไม่มี www ได้ 301 ตัวเดียวกันเป็น HIT · กลับด้านก็เป็นเหมือนกัน (www ได้หน้า 200 ของโดเมนหลัก)
- แก้ใน `inc/seo.php` `ea2000_redirect_no_store()` ส่ง `nocache_headers()` และ `X-Accel-Expires: 0` ทุกครั้งที่ WordPress redirect · **ทดสอบแล้วโฮสต์ทำตาม** 301 ของ www เป็น MISS ทุกครั้ง · ตรวจแล้วทั้ง 17 หน้าตอบ 200
- ที่ยังเหลือ: คำขอ www อาจได้หน้า 200 ที่แคชไว้ของโดเมนหลักแทน 301 (มี canonical ชี้โดเมนหลักอยู่แล้ว ไม่ร้ายแรง) · **ทางแก้ถาวรคือให้เจ้าของตั้ง Redirect Rule ใน Cloudflare ย้าย www ไปโดเมนหลัก** คำขอ www จะไม่ถึงเซิร์ฟเวอร์เลย
- ข้อควรรู้เรื่องแคชนี้: ส่ง header `Cache-Control: no-cache` แล้วได้ BYPASS แต่ไม่อัปเดตค่าที่เก็บไว้ · บันทึกเพจซ้ำไม่ล้างแคช · query string เป็นคีย์แยก · ใน WordPress ไม่มีปลั๊กอินแคช เป็นของโฮสต์ล้วน · **ตรวจการ redirect ทุกครั้งให้ลองทั้งแบบมี www และไม่มี www**

### ไอคอนเว็บใน Search Console ขึ้นเป็นรูปลูกโลก
- เว็บตั้งไอคอนถูกแล้ว (`site_icon` media 124 ขนาด 256x256 · `/favicon.ico` ชี้ไปไฟล์นี้) · บริการไอคอนของ Google (`google.com/s2/favicons`) ตอบ 404 สำหรับ ea2000.co แต่ตอบ 200 สำหรับ fenixpro-th.com เพราะ Google ยังไม่เคยเก็บหน้าแรกของ ea2000.co (noindex จนถึงวันนี้ และวันนี้หน้าแรกยังวนรีไดเรกต์อยู่ช่วงหนึ่ง) · ไอคอนจะขึ้นเองหลัง Google เก็บหน้าแรก เร่งได้ด้วยการขอจัดทำดัชนีหน้าแรกใน Search Console
