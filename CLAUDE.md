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
- **ค้างตัดสินใจ**: สไลด์ขั้น 5 ที่ครอปไปใช้บนหน้าแรก 2 ช่อง (`what_img`, `how_img`) มีตัวเลขกำไร +588.39 (5.73%) ที่เป็นตัวเลขสมมติ · ตอนนี้ติดคำบรรยายกำกับไว้ว่าเป็นตัวอย่างไม่ใช่ผลจริง และเจ้าของอนุมัติให้ใช้แล้ว · **แก้ไข 11 ก.ย. 2026: คำบรรยายนี้ไม่เคยแสดงบนหน้าแรกจนถึง v2.6.0** เพราะ `ea2000_front_media()` อ่าน `{key}_img_caption` เฉพาะตอนบล็อกไม่ส่งคำบรรยาย แต่ทุกบล็อกส่งเลขภาพมา ดูข้อ 34 · ถ้าเจ้าของอยากให้สะอาดกว่านี้ ให้ลบบรรทัด Profit กับ Floating P/L ออกจากภาพแล้วส่งมาใหม่

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


## 34) ช่องภาพ Backtest และ Forward Test ใส่ภาพประกอบแล้ว และข้อความที่สื่อว่ามีผลทดสอบถูกแก้ทั้งเว็บ (11 ก.ย. 2026 · deploy แล้ว v2.6.1)
### คำสั่งเจ้าของและสิ่งที่ทำจริง
- เจ้าของสั่ง "ทำไมไม่ใส่รูปแบคเทส ฟอเวิดเทส ใส่ซะ ไม่ต้องอ้าง"
- **ภาพ Myfxbook ที่เจ้าของส่งมา (ข้อ 30) ไม่ได้ใช้** เพราะเลียนหน้า Myfxbook มีโลโก้ XM และตัวเลขกำไร +68.24% ในนามบัญชี EA2000 · บอกเจ้าของสั้น ๆ แล้ว
- **ภาพ Strategy Tester ที่เจ้าของส่งมาก็ไม่ได้ใช้ในที่สุด** ลองเบลอตารางพารามิเตอร์แล้วส่งให้ workflow ตรวจ ผู้ตรวจยืนยันว่าภาพ AI ปนช่องของ MT4 กับ MT5 (มี Spread ไม่มี Delays, ตาราง Inputs อยู่ใต้แท็บ Settings) ขัดกับขั้นตอนสอนบนหน้าเดียวกัน และค่า XAUUSD H1 1:100 จะถูกอ่านเป็นค่าที่ EA2000 แนะนำ
- **ทำภาพประกอบใหม่ 4 ใบ** โทนเดียวกับภาพ VPS (พื้น `--ink` ลายจุด ฟอนต์ Chakra Petch และ Bai Jamjuree) มีป้ายในภาพ "ภาพประกอบ · ไม่มีตัวเลขจริง" หรือ "ไม่มีผลทดสอบจริง" · ไม่มีตัวเลขผลลัพธ์ใด ๆ
  - media **169** `ea2000-backtest-settings-guide.webp` 1600x900 · **170** รุ่นมือถือ 1080x1350 · หน้าตั้งค่าแท็บ Settings (Expert, Symbol กับ Timeframe ตามคู่มือของ EA, Date ย้อนหลัง 1 ถึง 2 ปี, Delays แบบ Random delay, Modelling แบบ Every tick based on real ticks, Deposit กับ Leverage เท่ากับบัญชีจริง) และรายการค่าที่ควรอ่านหลังทดสอบ
  - media **171** `ea2000-forward-report-guide.webp` 1600x900 · **172** รุ่นมือถือ · วิธีอ่านรายงาน: การ์ด Drawdown สูงสุด Gain จำนวนออเดอร์ ระยะเวลา แบบเว้นว่าง · กราฟ Balance (สีเทา เป็นขั้นเฉพาะตอนปิดออเดอร์) กับ Equity (สีเขียว ดิ่งช่วงข่าวแรง) · วงเล็บ Drawdown สูงสุดวัดจากยอด Equity ถึงจุดต่ำสุด · ภาพรวมไม่สื่อว่ามีกำไร · รายการ "เช็กก่อนเชื่อรายงาน" ใช้กล่อง `[ ]` ไม่ติ๊ก
  - ต้นฉบับ HTML และสคริปต์อยู่ scratchpad `tests/gen.js` · เรนเดอร์ด้วย headless Chrome ที่ 2x
- ตั้ง mod ช่อง `tests_bt_img`, `tests_fw_img`, `backtest_img`, `forward_img` และ `*_img_mobile` ของทั้ง 4 ช่อง · **alt และคำบรรยายเป็นค่าเริ่มต้นในโค้ด** (รีเซ็ต mod แล้วไม่กลับไปเป็นคำว่าผลทดสอบ)

### ฟีเจอร์ใหม่ในธีม
- **ภาพสำหรับมือถือ `{key}_img_mobile`**: `ea2000_media_picture()` ใน `inc/page-content.php` พิมพ์ `<picture><source media="(max-width: 640px)" width="1080" height="1350">` เมื่อมีค่า · ใช้ทั้ง `ea2000_media_slot()` และ `ea2000_front_media()` · ภาพประกอบที่มีตัวหนังสือจากความกว้าง 1600px เหลือราว 325px บนมือถือจนอ่านไม่ออก จึงต้องจัดหน้าแนวตั้งแยก · เพิ่ม `_img_mobile` ใน regex ของ `ea2000_mods_sanitize` แล้ว
- **แก้บั๊กคำบรรยายหน้าแรก**: `ea2000_front_media()` ต่อ `{key}_img_caption` ท้ายเลขภาพเสมอเมื่อมีรูป ("ภาพ 01 · ตัวเลขในภาพเป็นตัวอย่าง...") · ก่อนหน้านี้ข้อความกำกับใต้ภาพหน้าแรก 2 ใบ (what, how) ไม่เคยขึ้นเลย
- แถบ LINE ท้ายหน้า /backtest/ และ /forward-test/ ย้ายเป็น setting `backtest_cta_title/_text`, `forward_cta_title/_text` · เดิม hardcode ว่า "อยากดูผลทดสอบชุดอื่น?" และ "ติดตามผลแบบเรียลไทม์?" ซึ่งสื่อว่ามีผลอยู่แล้ว

### ข้อความที่สื่อว่ามีผลทดสอบ แก้ครบ (ทุกข้อผ่าน adversarial verify ของ workflow)
- `forward_sec5` เคยเขียนว่า "ทีมงานกำลังเก็บสถิติบนบัญชีทดสอบ" · `backtest_sec5` เคยชวน "ดูไฟล์รายงานล่าสุด" และเสนอไฟล์ Preset ให้ใครก็ได้ (ขัดกับหน้าแพ็กเกจที่ส่งไฟล์เฉพาะ Pro และ VIP) · ตอนนี้บอกตรง ๆ ว่ายังไม่มีบัญชีทดสอบและยังไม่มีรายงาน
- `forward_intro` "ข้อมูลด้านล่างจะอัปเดตตามรอบการทดสอบ" · `backtest_intro` · `backtest_sub` `forward_sub` · `tests_title` (เป็น "วิธีทดสอบ EA2000 ด้วย Backtest และ Forward Test") · `tests_intro` · `what_principle` · `links_btn1_label` บน /go/ (เป็น "Forward Test คืออะไร อ่านผลอย่างไร" ไอคอน guide) · ป้ายเมนู item 56 และ `mobile_nav_test_label` และ fallback ใน footer.php, 404.php, functions.php เป็น **"การทดสอบ"**
- ขั้นตอน Strategy Tester ข้อ 2 เคยบอกให้เลือก EA ที่แท็บ Overview · แก้เป็นแท็บ Settings ช่อง Expert ตาม MT5 จริง · `forward_sec4` เคยเขียนว่า "สองเส้นห่างกันมาก = ออเดอร์ค้างขาดทุน" แก้เป็น "Equity ต่ำกว่า Balance มาก"

### ค้าง: รอเจ้าของตัดสินใจ (ผู้ตรวจให้ระดับ high)
- **สไลด์ของเจ้าของหลายใบแสดงว่า EA มี Stop Loss, Take Profit, Trailing Stop, Break Even** ซึ่งขัดคำสั่งเจ้าของเองที่ห้ามอ้างเรื่อง SL ไม่ว่าทางไหน · ตำแหน่ง: สไลด์ขั้น 3 (`install_step3_img` บนหน้าแรก ภาพ 03/03 และ `inst_step3_img`) · สไลด์ขั้น 6 (`inst_step6_img` เห็น Stop Loss 150 ไฮไลต์) · ภาพหน้าแรก `what_img` แท็บ Experts มีบรรทัด "SL: 2350.42 | TP: 2374.42", "Trailing stop updated", "Take profit hit" · ถ้าเจ้าของสั่งเบลอ ให้เบลอทีละกล่องทีละคำสั่ง (ข้อ 24) แล้วอัปโหลดชื่อไฟล์ใหม่
- ข้อ 27 รายการที่ 3 ที่เคยถามเจ้าของไว้คือเรื่องเดียวกัน ยังไม่มีคำตอบ

### บทเรียน
- **ตรวจก่อนเชื่อบันทึกของตัวเอง**: ข้อ 30 เขียนว่าติดคำบรรยายกำกับแล้ว แต่ไม่เคยเปิดหน้าเว็บดูว่าขึ้นจริง
- ภาพที่ AI สร้างเลียนหน้าจอโปรแกรมจริง มักผสมรุ่นโปรแกรมและใส่ค่าที่ดูเหมือนคำแนะนำ ก่อนใช้ประกอบบทสอน ให้เทียบกับขั้นตอนบนหน้าเดียวกันทีละช่อง
- ถ่ายภาพหน้าเว็บจริงแบบเวลาจริงได้ด้วย Chrome DevTools Protocol ผ่าน WebSocket ในตัว Node 25 (scratchpad `tests/cdp-shot.js`) · ต้องตั้ง `scrollBehavior = auto` ก่อน `scrollTo` ไม่งั้นหน้าไม่เลื่อน และภาพ lazy ไม่โหลด · ถ้า `--user-data-dir` เดิมถูกล็อก Chrome จะไม่เขียนภาพและไม่แจ้ง error ให้ใช้โฟลเดอร์ใหม่

## 35) ตรวจการตั้งค่า Yoast ทั้งหมด และแก้แล้ว (11 ก.ย. 2026 · deploy แล้ว v2.7.0)
### วิธีตรวจ (ใช้ซ้ำได้)
- `GET ea2000/v1/seo-options?dump=1` คืนค่า Yoast ทุกกลุ่ม (`wpseo`, `wpseo_titles`, `wpseo_social`, `wpseo_llmstxt`) แบบอ่านอย่างเดียว ค่าที่เป็นรหัสหรือโทเค็นจะแสดงแค่ว่ามีค่าหรือไม่
- scratchpad `yoast-audit/collect.js` เก็บ head จริงของทุกหน้า sitemap robots.txt และ URL พิเศษ 35 แบบ · `verify.js` ตรวจผลหลังแก้ 39 ข้อ
- workflow 9 agent (4 ด้าน + ผู้หักล้างด้านละคน + สรุป) ได้ 23 รายการที่ควรแก้ ผ่านแล้ว 47 รายการ ตีตก 11 รายการ

### แก้ในธีม (`inc/seo.php` ข้อ 6 และ `functions.php`)
- **oEmbed เคยบอกชื่อบัญชีผู้ดูแล**: `/wp-json/oembed/1.0/embed` ตอบ author_name และ author_url `/author/adminwp/` ทุกหน้า · `ea2000_oembed_hide_author()` ตัดออกทั้ง JSON และ XML
- `/page/2/`, `/page/999/` และ `/pricing/page/2/` เคยตอบ 200 เนื้อหาซ้ำ · `ea2000_redirect_paged_singular()` ส่ง 301 กลับหน้าจริงพร้อมพารามิเตอร์โฆษณา ยกเว้นหน้ารวมบทความ (is_home) และเพจที่มี `<!--nextpage-->`
- `/category/other/` ที่ว่างเคยเป็น index · `ea2000_noindex_empty_terms()` ใส่ noindex ให้หมวดหมู่หรือแท็กที่ยังไม่มีบทความ มีบทความแล้วกลับเป็น index เอง
- robots.txt เคยมีบรรทัด Sitemap ซ้ำ (ของธีมกับของ Yoast) · ธีมไม่เติมแล้วเมื่อ Yoast เปิด sitemap · **Cloudflare ต่อบล็อกของตัวเองไว้ด้านบนของ robots.txt ที่ WordPress สร้าง ไม่ได้เสิร์ฟไฟล์แทน** (คำอธิบายเดิมในโค้ดผิด)
- node สินค้าเคยฝัง Organization เต็มที่ใช้โลโก้แนวนอน ขณะที่ Yoast ใช้ตรากลม หน้าแรกกับหน้าแพ็กเกจจึงมีโลโก้สองแบบ · `ea2000_publisher_ref()` อ้าง `@id` อย่างเดียวเมื่อ `ea2000_yoast_prints_organization()` เป็นจริง · ไม่ใช้ลิงก์เพิ่มเพื่อน LINE เป็น sameAs แล้ว
- **Yoast ไม่อ่านช่องโซเชียลใน Customizer เอง** (ข้อ 17 เขียนผิด) · `ea2000_yoast_org_same_as()` บน `wpseo_schema_organization` เติม facebook_url, instagram_url, tiktok_url, youtube_url เข้า sameAs · เจ้าของกรอกที่ Customizer ข้อ 1 ที่เดียวพอ
- **endpoint เคยทำตัวแปรเสียตอนเขียน**: `sanitize_text_field` ของคอร์ลบ `%` ที่ตามด้วยเลขฐานสิบหกสองตัว `%%date%%` จึงกลายเป็น `%te%%` · ใช้ `WPSEO_Utils::sanitize_text_field()` แทน · GET อ่าน company_logo จาก `wpseo_titles` แล้ว (เดิมอ่านผิดกลุ่มเลยได้ null) · เพิ่มคีย์กลุ่ม `wpseo` ที่ตรวจแล้วว่าปลอดภัยใน whitelist

### ตั้งค่าผ่าน API แล้ว
- เปิด: remove_shortlinks, remove_rest_api_links, remove_oembed_links, remove_generator, remove_feed_global_comments, remove_feed_post_comments, remove_feed_search, remove_atom_rdf_feeds, remove_feed_categories, remove_feed_tags, search_cleanup, search_cleanup_patterns, redirect_search_pretty_urls
- ปิด: enable_enhanced_slack_sharing (เคยพิมพ์ป้าย "Est. reading time" ภาษาอังกฤษ และจะพิมพ์ "Written by ADMIN" บนบทความ)
- `title-archive-wpseo` = `%%date%% %%page%% %%sep%% %%sitename%%` · หัวเรื่องหมวดหมู่และแท็กเป็น "หมวดหมู่ ..." และ "แท็ก ..."
- meta description ของ /articles/ เคยบอกว่ามีบทความ แก้เป็นรวมคู่มือที่เผยแพร่แล้ว
- **ห้ามเปิด** `search_cleanup_emoji` (Yoast มองสระและวรรณยุกต์ไทยเป็นอีโมจิ ค้นหาภาษาไทยจะพาไปหน้าแรกหมด) · **ห้ามเปิด** `clean_permalinks` และ `clean_campaign_tracking_urls` (ตัด fbclid และรหัสคลิกโฆษณา) · คง `remove_feed_global` = false และ `deny_search_crawling` = false และไม่กัน AdsBot (ต้องตรวจหน้า /go/)

### ข้อเท็จจริงที่บันทึกก่อนหน้านี้ไม่ตรง
- ภาพตอนแชร์ลิงก์ (`og_default_image`) ตอนนี้คือ media **125** `EA2000-LOGO.png` (2001x364 พื้นโปร่ง) ไม่ใช่ media 69 ตามข้อ 32 · โลโก้ Organization ของ Yoast คือ media **124** `EA2000-ICON.png` 256x256 ไม่ใช่ 125 · มีคนเปลี่ยนใน wp-admin หลังข้อ 32
- focus keyphrase หน้าแรกตอนนี้เป็น `EA2000` ไม่ใช่ `EA2000 EA MT5` ตามข้อ 17 · มีคนเปลี่ยนภายหลัง จึงยังไม่ทับ
- `metadesc-home-wpseo` และ `title-home-wpseo` **ไม่ถูกใช้เลย** เพราะหน้าแรกเป็นเพจคงที่ (65) Yoast ใช้ meta ของเพจ 65 · ห้ามล้าง meta ของเพจ 65 เพราะค่าสำรองจริงคือ `metadesc-page` ที่ว่าง
- SoftwareApplication **ยังไม่ได้ rich result** แม้เปิด pricing_confirmed เพราะ Google ต้องมี aggregateRating หรือ review ด้วย (ข้อ 21 เข้าใจผิด) · ไม่เพิ่มจนกว่าจะมีรีวิวจริง

### ข้อควรทำทุกครั้งต่อจากนี้
- **เพจที่เนื้อหามาจากธีมไม่ขยับวันที่แก้ไข** เมื่อแก้ข้อความในโค้ดหรือ Customizer · sitemap lastmod และ dateModified จึงค้าง · หลัง deploy งานเนื้อหา ให้ส่ง `{"status":"publish"}` ไปที่ `wp/v2/pages/ID` ของหน้าที่เปลี่ยน (รอบนี้ทำแล้ว 65, 49 ถึง 53, 90 ถึง 94)

### รอเจ้าของตัดสินใจหรือทำเอง
1. เปลี่ยนภาพตอนแชร์ลิงก์เป็น `assets/img/og-share.jpg` 1200x630 หรือไม่ (ภาพปัจจุบันถูก LINE และ Facebook ตัดเหลือแค่ "A20")
2. focus keyphrase หน้าแรกจะใช้ `EA2000` ต่อ หรือกลับเป็น `EA2000 EA MT5`
3. ตั้ง Cloudflare Redirect Rule ให้ www ไปโดเมนหลัก
4. ลิงก์โปรไฟล์ Facebook, YouTube, TikTok ของแบรนด์ (กรอกที่ Customizer แล้ว sameAs ขึ้นเอง)
5. การวัดผล: Site Kit ติดตั้งแต่ยังไม่เชื่อม ไม่มี GA4 ในหน้าเว็บเลย · ถ้าใช้ GA4 ของธีม (`ga_measurement_id` + `show_cookie_consent`) ต้องแก้ให้แถบยินยอมคุกกี้ขึ้นบน /go/ ด้วย เพราะ `template-links.php` ไม่โหลด footer.php
- ไม่ได้ทำโดยตั้งใจ: redirect URL ตัวพิมพ์ใหญ่ (canonical ครอบแล้ว) · llms.txt (ปิดไว้ ไม่มีผลกับ Google)

## 36) บาร์ล่างมือถือและรูปแชร์ลิงก์ของเจ้าของ (11 ก.ย. 2026 · v2.7.2)
- **บาร์ล่างมือถือ เจ้าของกำหนดลิงก์เอง**: หน้าแรก `/` · การทดสอบ `/backtest/` (เดิม /forward-test/) · ทัก LINE `https://lin.ee/ye11pwm6` · แพ็กเกจ `/pricing/` · วิธีติดตั้ง `/how-to-install/` · ไอคอนปุ่มการทดสอบเปลี่ยนจากกราฟพุ่งขึ้นเป็นขวดทดลอง (`flask` ไอคอนใหม่ใน `ea2000_icon()`) และปุ่มวิธีติดตั้งจากลูกศรดาวน์โหลดเป็น `guide` · เมนูหัวเว็บ "การทดสอบ" (item 56) ยังชี้ /forward-test/ เพราะเจ้าของสั่งเฉพาะบาร์ล่าง
- **รูปแชร์ลิงก์ของทั้งเว็บ = รูปที่เจ้าของทำเอง** `D:\EA VIDEO\EA2000\ea2000-og-image.png` (1731x909 · สร้างด้วย AI มี C2PA) แปลงเป็น JPEG 1200x630 147 KB อัปโหลดเป็น media **173** `ea2000-share-card-1200x630-1.jpg` (WordPress เติม -1 เองเพราะชื่อไฟล์ลงท้ายด้วยขนาด) · ตั้งทั้ง Yoast `og_default_image` / `og_default_image_id` และ theme mod `og_default_image` (ใช้เป็น image ของ SoftwareApplication) · ตรวจแล้ว og:image ขึ้นทุกหน้า 1200x630 image/jpeg · **ข้อ 35 รายการรอเจ้าของข้อ 1 ปิดแล้ว**
- **ไม่ใส่ Featured image ให้เพจ**: รูปแชร์ของ Yoast ครอบทุกหน้าอยู่แล้ว ใส่ซ้ำไม่ได้อะไรเพิ่ม และ `page.php` แสดง Featured image เป็นรูปใหญ่หัวเนื้อหา (about, privacy-policy, terms-of-use, data-deletion) · ถ้าอยากให้หน้าไหนมีรูปแชร์ของตัวเอง ให้ใช้ Social image ในกล่อง Yoast ของหน้านั้นแทน · Featured image ใช้กับบทความในอนาคต

## 37) ตรวจความพร้อมทั้งเว็บ และแก้รอบ 1 ถึง 2 (11 ก.ย. 2026 · deploy แล้ว v2.9.0)
- เจ้าของบอกว่างานที่เหลือคือการ์ด PDPA กับบทความวันเว้นวัน แล้วถามว่าส่วนอื่นเสร็จหรือยัง → workflow ตรวจ 13 agent · รายงานฉบับเต็มส่งให้เจ้าของเป็นไฟล์แล้ว **ไม่เก็บใน repo เพราะ repo เป็นสาธารณะ**

### รอบ 1 (v2.8.x)
- /pricing/ มือถือ: ตารางเทียบแพ็กเกจเคยดันหน้ากว้างเกินจอ · แถว Starter ในตารางแก้ให้ตรงกับการ์ด
- /go/: การ์ดดาวน์โหลดแสดงเฉพาะเมื่อมีลิงก์ ช่อง placeholder เห็นเฉพาะแอดมิน · โลโก้ใช้ `logo-mark.webp` · เพิ่มปุ่ม LINE ท้ายหน้า (`data-line-pos` = `go-bottom`)
- ข้อความที่อ่านแล้วเหมือนบอกกลยุทธ์หรือรับประกัน (about, riskdoc, installdoc) และประโยคที่ยืนยันว่าต้นทุนเทรดกับ Zaurix ไม่เพิ่ม เปลี่ยนเป็นข้อความที่ยืนยันได้จริง
- ช่องรูปอ่านขนาดจริงจากคลังสื่อ (`ea2000_media_info`) ใส่ width, height, srcset ถูกต้อง · ตารางในเนื้อหาบนมือถือแสดงเป็นการ์ดตาม `data-label` · ฟิล์มติดตั้งหน้าแรกเป็น grid ที่ 1100px ขึ้นไป
- **ฟอนต์อยู่ในธีมเอง** `assets/fonts/` woff2 21 ไฟล์ (thai, latin, latin-ext) พร้อมใบอนุญาต OFL · `@font-face` อยู่ต้น style.css · preload 2 ไฟล์ · ไม่โหลด Google Fonts แล้ว

### รอบ 2 (v2.9.0)
- **ความซ้ำกับ FENIX** วัดแบบช่วงละ 25 ตัวอักษรไม่นับช่องว่าง: หน้า risk (`riskpage_intro`, `rp_block1..6`) จากเกือบ 100% เหลือ 1% · terms (WP 33) จาก 51% เหลือ 5% · about (WP 27) จาก 22% เหลือ 4% · ความหมายและความแรงของคำเตือนเท่าเดิม ไม่แตะกติกาคืนเงินและราคา · วันที่ปรับปรุง 11 กันยายน 2569
- `ea2000_keep_words()` และ `ea2000_text()` ใน `inc/page-content.php`: คำละตินที่มียัติภังค์ (Zaurix-Server, Wi-Fi) ห่อด้วย `span.nobr` ทั้งหัวข้อและเนื้อหา · **word joiner ใส่ให้คำไทยเฉพาะหัวข้อและข้อความสั้น** (`ทีม|งาน`, `ต่าง|จาก` แก้ได้ที่ฟิลเตอร์ `ea2000_keep_words`) ไม่ใส่ในย่อหน้าเนื้อหา · หัวข้อใช้ `text-wrap: balance`
- ทุกช่องรูปเป็นลิงก์เปิดไฟล์เต็มในแท็บใหม่ (`ea2000_media_open`) · ภาพแรกของหน้าคู่มือ `loading="eager"` · ภาพในขั้นตอนคู่มือไม่ขยายเกินขนาดจริง ภาพแนวตั้งสูงไม่เกิน 640px (มือถือ 520px)
- ภาพใหม่ media **177 ถึง 181**: /open-mt5-account/ ขั้น 3 ถึง 5 ครอปเฉพาะส่วนที่ขั้นนั้นพูดถึง พร้อมกรอบเขียวชี้ปุ่ม (ตัดช่องว่างกลางแถบแจ้งเตือนและกลางการ์ด ส่วนที่เบลอไว้คงเดิม) · /vps-ios/ ขั้น 3 ตัดพื้นดำครึ่งล่าง · หน้าแรก `how_img` ครอปชิดขอบแผงจากสไลด์ต้นฉบับ · สคริปต์ครอปอยู่ใน scratchpad `batch2/crops.sh`
- เส้นสัญญาณใน footer วาดด้วย `clip-path` บน path แทน stroke-dash (`pathLength` ใช้คู่ `non-scaling-stroke` ไม่ได้ เส้นเคยหยุดที่ 1208 จาก 1440)
- แถบ HUD ใน hero เป็น 2 x 2 ที่ 961px ขึ้นไป · สวิตช์ช่องทางใน footer แสดงสถานะเปิดบนจอสัมผัส · กล่องในหน้าคู่มือไม่ชิดเส้นแบ่ง · 404 ช่องค้นหา 16px และ placeholder "ค้นหาคู่มือ…" · เนื้อหาจอแรกของหน้าใน (lead, stats, sec-head, price-card, riskdoc-intro) ไม่รอ reveal
- ข้อความคู่มือ: บัญชี MT4 ให้ติดต่อโบรกเกอร์ · ตัดคำสัญญาเรื่องเวลา · iOS ใช้ชื่อ Credentials · ไอคอนสถานะ EA บรรยายแบบกลางและตรงกันทุกจุด · แผง Dashboard ดูผ่าน VPS ไม่ใช่แอปมือถือ
- ธีม: เทมเพลตทุกไฟล์มี `defined( 'ABSPATH' ) || exit;` · ถอด global-styles ที่ WordPress เติมกลับที่ `wp_footer` · **`ea2000/v1/authcheck` ต้องยืนยันตัวตนแล้ว** ทดสอบ header ต้องส่งรหัสไปด้วย ไม่ส่งจะได้ 401 (ข้อ 6 เดิมเขียนไว้ก่อนเปลี่ยน)
- ตรวจบนเว็บจริงด้วย CDP ทั้งมือถือและเดสก์ท็อป: 18 URL ตอบ 200 ไม่มี PHP error · re-save เพจ 53, 52, 90, 92, 93, 94, 65 แล้ว

### ยังค้างหลังรอบ 2
- งาน PDPA (การ์ดยินยอมคุกกี้ นโยบายความเป็นส่วนตัวที่ระบุข้อมูลที่เก็บจริง การนับคลิก LINE แยกจาก OpenChat) · งานบทความ · เรื่องที่รอเจ้าของตัดสินใจอยู่ในรายงาน
- `sizes` ของ srcset ในช่องรูปเขียน 1120px ทุกช่อง หน้าคู่มือที่คอลัมน์กว้าง 540px จึงโหลดไฟล์ใหญ่กว่าที่ใช้

## 38) ปุ่มเมนูมือถือและบาร์ล่าง ปรับตามเจ้าของ (11 ก.ย. 2026 · deploy แล้ว v2.9.1)
- เจ้าของสั่ง: ปุ่มเมนูเอาพื้นเขียวออก เหลือขีดขาว 3 เส้นที่หนาขึ้น · ปุ่ม LINE ในบาร์ล่างให้เหมือนอีก 4 ปุ่ม · ป้ายทั้ง 5 ปุ่มเป็นสีเขียว
- `.nav-toggle` (บล็อกแถบหัวเว็บสีเข้มใน style.css): พื้นและกรอบโปร่ง · ขีด 24x3 สี `--header-text` (เดิม 18x2 สีเข้มบนพื้นเขียว) · ตอนเปิดเมนูเลื่อนขีด 8px ให้ไขว้ตรงกลาง · สีเขียวตอน hover เฉพาะเมาส์ · พื้นที่กดยัง 44x44
- บาร์ล่าง: **ถอดสไตล์คีย์ LINE แบบยกทั้งชุด** (ข้อ 29 ส่วนที่บอกว่าคีย์ LINE ยกเจาะราง สันเขียว และไฟกะพริบ ถือเป็นประวัติ) · ถอดตัวแปร `--cap-h-line`, `--dock-ring`, `--dock-punch`, `--lift` และ `@keyframes dock-live` · คลาส `.is-action` ยังอยู่ใน footer.php เพราะ main.js ใช้ข้ามคีย์นี้ตอนหาหน้าปัจจุบัน
- ป้าย `.dock-label` ทุกคีย์ใช้ `--ink-accent` · คีย์ของหน้าที่เปิดอยู่ยังต่างด้วยตัวคีย์ที่ยก วงเล็บ HUD ไฟชี้ตำแหน่ง และป้ายตัวหนา
- ตรวจบนเว็บจริง (CDP จำลองมือถือ 375, 360, 320 บนหน้าแรก /pricing/ /how-to-install/): พื้นปุ่มเมนูโปร่งจริง ขีด 24x3 สีขาว · ป้ายทั้ง 5 สี rgb(140, 236, 78) · คีย์ LINE ขนาดและสีเท่าคีย์อื่น · บาร์สูง 85 เท่า spacer · ตอนย่อทุกคีย์สูง 32 เท่ากัน · ไม่มีหน้าล้นแนวนอน
- บันทึกใน `docs/home-v2-spec.md` ข้อ 8 แล้ว: **อย่าเอาคีย์ LINE แบบยกหรือปุ่มเมนูพื้นเขียวกลับมาโดยไม่ถามเจ้าของ**

## 39) การ์ด PDPA และนโยบายความเป็นส่วนตัวฉบับใหม่ (11 ก.ย. 2026 · deploy แล้ว v2.10.0)
- เจ้าของสั่งเริ่มการ์ด PDPA และอนุญาตให้ลงปลั๊กอิน · **ไม่ลงปลั๊กอิน** ทำในธีมเพราะเบากว่า ไม่ทำให้แคชของโฮสต์ BYPASS และหน้าตาเข้ากับเว็บ
- `inc/consent.php` (ไฟล์ใหม่): การ์ด `section#cookie-settings` พิมพ์ผ่าน `wp_footer` ลำดับ 5 ทุกหน้า รวม /go/ และ 404 · **PHP ไม่อ่านคุกกี้ความยินยอม** · ส่ง `ea2000Consent` = {version, ga, pixel} ให้ main.js · ตรวจรูปแบบรหัส `ea2000_sanitize_ga_id` (G-...) และ `ea2000_sanitize_pixel_id` (ตัวเลข 10 ถึง 20 หลัก) ทั้งใน Customizer (มีข้อความเตือนแอดมิน) และ REST
- main.js: การ์ดขึ้นเองเฉพาะเมื่อมีรหัส GA4 หรือ Pixel ที่ถูกต้อง · ชั้นแรก ปฏิเสธทั้งหมด กับ ยอมรับทั้งหมด หน้าตาเท่ากัน + ตั้งค่า · ชั้นตั้งค่า จำเป็น (เปิดตลอด) วิเคราะห์การใช้งาน การตลาด ปิดเป็นค่าเริ่มต้น · กดปิด = ไม่ยินยอมรอบนั้น (sessionStorage) · คุกกี้ `ea2000_consent=v<รุ่น>.a<0|1>.m<0|1>.t<unix>.id<สุ่ม>` 12 เดือน Secure SameSite=Lax · เพิ่ม `consent_version` แล้วทุกคนถูกถามใหม่
- โหลดแท็กทีละหมวดหลังยินยอมเท่านั้น (GA4 พร้อมสัญญาณ Consent Mode · Pixel พร้อม `fbq(consent, grant)`) · ถอนความยินยอม: หยุดแท็ก ลบ _ga _gid _gat _fbp _fbc บนโดเมนและ .โดเมน แล้วรีโหลด · ลิงก์ใดที่ลงท้าย `#cookie-settings` จะเปิดชั้นตั้งค่า (ท้ายเว็บคอลัมน์เอกสาร ท้ายหน้า /go/ ในนโยบาย และหน้าขอลบข้อมูล)
- ยังไม่มีรหัสจริง การ์ดจึงยังไม่ขึ้นเอง ลิงก์ตั้งค่าคุกกี้แสดงว่าใช้เฉพาะคุกกี้ที่จำเป็น · ข้อความทั้งหมดอยู่ Customizer หมวด 16 · `show_cookie_consent` และ `cookie_consent_text` เลิกใช้ · CSS แถบคุกกี้เก่าลบแล้ว
- นับคลิก LINE: `openchat_click` แยกจาก `line_click` · ส่ง Meta `Contact` เฉพาะ LINE OA · ตำแหน่งปุ่มใช้ `data-line-pos` หรือ id ของกล่องที่ใกล้ที่สุด · ปุ่มแพ็กเกจมี `data-line-pkg`
- ตรวจบนเว็บจริงด้วย CDP โดยแก้ HTML ระหว่างทางใส่รหัสจำลองและบล็อกคำขอไป Google/Meta: ก่อนยินยอมไม่มีคำขอแท็กเลย · ยอมรับเฉพาะวิเคราะห์โหลดแค่ gtag และลบ _fbp · ถอนแล้วรีโหลดและลบ _ga · การ์ดมือถืออยู่เหนือบาร์ล่างไม่บังปุ่ม LINE ใน hero และบน /go/ · จอคอมมุมซ้ายล่าง 420px
- **นโยบายความเป็นส่วนตัว (WP 3) เขียนใหม่ข้อ 2 ถึง 10**: ข้อมูลที่เก็บจริง (หมายเลขบัญชี MT5 หลักฐานชำระเงิน ภาพหน้าจอ รหัสดูอย่างเดียว ข้อมูลจาก Zaurix OpenChat) · ฐานความยินยอมสำหรับ GA และ Pixel · ผู้รับข้อมูล (Cloudflare, LY Corporation, Zaurix Ltd., Google, Meta) · `h2#cookies` ตารางคุกกี้ · การส่งข้อมูลไปต่างประเทศ · ระยะเวลาเก็บ · หน้าขอลบข้อมูล (WP 11) ปรับให้ตรงกัน
- **รอเจ้าของ**: ข้อมูลผู้ควบคุมข้อมูล (ชื่อ ที่อยู่ อีเมล) สำหรับข้อ 1 · ยืนยันว่า Zaurix ส่งข้อมูลอะไรมาจริง (เขียนไว้ว่า เช่น หมายเลขบัญชีและปริมาณการเทรด) · รหัส GA4 และ Meta Pixel ถ้าจะใช้ · **ห้ามเปิดการวางแท็ก Analytics ใน Site Kit และห้ามลง PixelYourSite** เพราะจะโหลดก่อนผู้ใช้ยินยอม
- **GA4 ต่อแล้ว 11 ก.ย. 2026**: เจ้าของเลือกสร้างเป็น property ใหม่ชื่อ EA2000 ในบัญชี GA เดิม "FENIX PRO EA" (ไม่แตะ property ของ fenixpro-th.com) · สตรีมเว็บ "EA2000 Web" https://ea2000.co · รหัส `G-YQ7NLF1EDS` ตั้งใน `ga_measurement_id` แล้ว · เก็บข้อมูลเหตุการณ์ 14 เดือน · การวัดที่ปรับปรุงแล้วเปิดตามค่าเริ่มต้น · **ยังไม่ได้ตั้ง line_click เป็นเหตุการณ์สำคัญ** เพราะ GA4 ให้ติดดาวได้เฉพาะเหตุการณ์ที่เข้ามาแล้ว ต้องรอคลิก LINE ครั้งแรกหลังผู้ใช้ยินยอม แล้วไปที่ ผู้ดูแลระบบ > กิจกรรม ติดดาว line_click · Chrome ที่ใช้คือบัญชี Google ลำดับที่สอง (authuser=1) · ห้ามเชื่อม Analytics ใน Site Kit

## 40) เอาเครื่องหมาย // หน้าป้ายหัวข้อเล็กออกทั้งเว็บ (11 ก.ย. 2026 · deploy แล้ว v2.10.3)
- เจ้าของไม่ชอบ บอกว่าดูเหมือนงานที่ยังไม่เสร็จ · เดิมเป็นลูกเล่นของแนว Control Room ให้ป้ายหัวข้อเล็กดูเหมือนคอมเมนต์ในโค้ดโปรแกรม (ข้อ 19 และ `docs/home-v2-spec.md`)
- ลบ `<span aria-hidden="true">// </span>` 8 จุด (boot-prefix, principle-label, phero-kicker, console-label ของ footer และแถบ LINE CTA, guide-quick-label, dl-title, consent-kicker) และกฎ `.kicker::before` · ป้ายสีเขียวตัวเล็กยังอยู่ ตรวจแล้ว 13 หน้าไม่มี // เหลือในข้อความ · **อย่าเอากลับมาโดยไม่ถามเจ้าของ**

## 41) ปุ่มการ์ด PDPA กดแล้วไปหน้าอื่น · ลิงก์เมนูมือถือล่องหนทับจอ (13 ก.ย. 2026 · deploy แล้ว v2.10.4)
- เจ้าของแจ้งว่ากดปุ่มไหนบนการ์ดคุกกี้ก็ไปหน้าอื่น กดปิดก็ไม่ปิด · ทำซ้ำได้ใน Browser pane: แตะ X บนหน้าแรกแล้วไปโผล่ /vps-ios/
- **ต้นเหตุไม่ได้อยู่ที่การ์ด**: ในจอกว้างไม่เกิน 960px ลิ้นชักเมนู `.site-nav` ตอนปิดแค่ `opacity: 0; visibility: hidden` แต่กฎ `.nav-list .sub-menu` ในลิ้นชักตั้ง `visibility: visible` ลูกที่ตั้ง visible จะแสดงและกดได้แม้พ่อเป็น hidden · header มี z-index 130 เหนือการ์ด (125) ลิงก์เมนูย่อย 8 ลิงก์ (การทดสอบ 2 · คู่มือ 6) จึงล่องหนทับครึ่งบนของจอทุกหน้าที่มี header · นอกจากการ์ด ยังกินการแตะเนื้อหาในแถบนั้นด้วย ตำแหน่งที่โดนขึ้นกับความสูงจอ
- แก้: เมนูย่อยในลิ้นชักใช้ `visibility: inherit` ทั้งกฎหลักและกฎ `li.is-open > .sub-menu` ในบล็อก 960 · ลิ้นชักตอนปิดใส่ `pointer-events: none` เปิดแล้วคืน `auto`
- ตรวจหลัง deploy: สแกนจุดแตะทุก 18px ทั้ง 18 หน้า x 4 ขนาดจอ (360x640, 390x664, 768x1024, 1440x900) ไม่เหลือจุดแตะล่องหน (ก่อนแก้เจอทุกหน้าที่มี header) · แตะจริงด้วย touch event ทุกปุ่มของการ์ด (ปิด ปฏิเสธ ยอมรับ ตั้งค่าแล้วบันทึก) 3 หน้า x 4 ขนาดจอ ผ่าน 48 จาก 48 · เปิดลิ้นชักแล้วแตะลิงก์เมนูย่อยยังไปหน้าได้ · ปุ่ม LINE ใน hero รับการแตะถูกตัว · สคริปต์อยู่ scratchpad `pdpa-bug/tap-test.js` และ `ghost.js`
- **บทเรียน: ตรวจปุ่มด้วยการแตะที่พิกัดจริง (`Input.dispatchTouchEvent` หรือ `elementFromPoint`) ไม่ใช่ `element.click()`** เพราะ click จากสคริปต์ไม่สนว่ามีอะไรทับอยู่ · การตรวจการ์ดรอบ v2.10.0 ผ่านเพราะใช้ click จากสคริปต์ และทดสอบที่จอสูง 812px ซึ่งปุ่มอยู่ต่ำกว่าแถบลิงก์ล่องหนพอดี
- หน้าที่โฮสต์แคชไว้ก่อน deploy ยังอ้าง style.css รุ่นเก่า (Cloudflare เก็บไฟล์นั้น 4 ชั่วโมง) ผู้เข้าชมบางคนยังเจอบั๊กจนแคชหมดอายุ ถ้าเจ้าของล้างแคชของโฮสต์และ Cloudflare จะหายทันที
- ธีม FENIX มีกฎ `visibility: visible` เดียวกัน (เปิดดูแบบอ่านอย่างเดียว) ไม่ได้แตะ

## 42) แผนบทความคลื่นแรก (16 ก.ย. 2026)
- เจ้าของขอแผนบทความทั้งหมด พร้อมแผนการลง คีย์ และจำนวน · ทำด้วย workflow: สำรวจสิ่งที่ 17 หน้าครองอยู่ · ยิง autocomplete Google, Bing, DuckDuckGo ใหม่ · ตรวจความพร้อมของระบบบทความ · ร่างแผน · ผู้ตรวจ SEO และผู้ตรวจ compliance ค้าน · แก้ตามรีวิว
- **ข้อมูลเต็มอยู่ที่ `docs/article-plan.json`** (บทความ, หัวข้อ H2, ลิงก์, งานเตรียม, สิ่งที่ต้องได้จากเจ้าของ, กติกา, หัวข้อพัก, คำที่ไม่ทำ) · หน้าสรุปให้เจ้าของ: artifact "แผนบทความ EA2000" https://claude.ai/artifact/JWMtHyrSX9C32yoMehKqTp
- **คลื่นแรก 13 บทความ ลงวันเว้นวัน 09:00 น. 23 ก.ย. ถึง 17 ต.ค. 2569** · คำค้นรวม 53 คำ (คำหลัก 13 คำรอง 40) · 3 หมวด: `mt5-fixes` ใช้งานและแก้ปัญหา MT5 6 ชิ้น · `risk-costs` ความเสี่ยงและต้นทุนการเทรด 4 ชิ้น · `ea-basics` EA และบอทเทรด 3 ชิ้น · 18 ถึง 25 ต.ค. อ่าน Search Console ไม่ลงหัวข้อใหม่ · คลื่นที่สองจากคำค้นจริงตั้งแต่ 26 ต.ค.
- ลำดับ: RM1 คำนวณ lot size · RM3 margin level คือ · EA1 ea forex คือ · RM2 1 lot ทอง เท่ากับกี่บาท · FX1 autotrading disabled by server mt5 · EA2 บอทเทรดทอง · FX2 mt5 ไม่มี xauusd · EA3 copy trade คือ · FX3 invalid stops mt5 · BA1 slippage forex คือ · MB1 mt5 ใช้ยังไง · FX4 mt5 กราฟไม่ขึ้น · MB4 vps forex ฟรี
- 5 ชิ้น (FX1, FX2, FX3, MB1, FX4) ต้องใช้ภาพจริงรอบเดียวจากเจ้าของภายใน 24 ก.ย. จากบัญชีเดโมที่ไม่เคยติดตั้ง EA2000 และต้องผ่านการตรวจ C2PA · พักไว้: คู่มือแอป MT5 มือถือ, เวลาเซิร์ฟเวอร์, IB และ rebate, prop firm (สำรอง), martingale, ล้างพอร์ต, news filter, ทุนน้อย
- **ยังไม่ได้เริ่ม** · บทความแรกลงได้เมื่อเจ้าของตัดสินภายใน 18 ก.ย.: โครง URL (แนะนำ `/articles/%postname%/`), ชื่อผู้เขียนและ bio, ชื่อหมวด, ข้อความคำเตือนท้ายบทความ, รายชื่อบทความ FENIX · และงานธีมของ Claude ผ่านการตรวจบนเว็บจริง (หมวด, author box, /articles/, ถอดยอดวิว, CSS บทความ, excerpt, disclaimer, สคริปต์สแกนร่าง, ทดสอบตั้งเวลา) · ถ้าช้าให้เลื่อนทั้งตาราง
- กติกาสำคัญ: เปิดเผยความเป็นพันธมิตร Zaurix ในย่อหน้าเดียวกับที่เอ่ยชื่อครั้งแรกหรือก่อนลิงก์แรกไป /open-mt5-account/ · ห้ามคำว่าฟรีหรือสื่อว่าได้ EA ฟรีจนกว่าเงื่อนไขจะเผยแพร่ · หมวดความเสี่ยงไม่ลิงก์ /pricing/ และ /open-mt5-account/ · ห้ามประโยคแนว "EA ที่ดีต้องมี SL" · เจ้าของอนุมัติในแชทภายใน 18:00 น. วันก่อนลง ไม่ทันให้ข้ามวัน

## 43) หน้า /go/ แบบขั้นตอน ตามแบบเว็บในเครือ (16 ก.ย. 2026 · deploy แล้ว v2.11.0)
- เจ้าของสั่งให้ทำหน้า /go/ ตามรูปแบบ fenixpro-th.com/go/ และทำปุ่มไว้ก่อนสำหรับหน้าที่ยังไม่มี · พอร์ตโครงจาก commit bb4a7aa และ d909fd7 ของ repo FENIX (อ่านอย่างเดียว) มาใส่ `template-links.php` แต่ใช้การ์ดพื้นขาวและสีของ EA2000
- ลำดับ: ติดขั้นตอนไหน ทักทีมงานได้ (LINE, OpenChat) → เริ่มใช้งาน EA2000 ใน 6 ขั้นตอน (เปิดบัญชี Zaurix · ติดตั้ง MT5 และล็อกอิน · ฝากเงิน · รับไฟล์ EA2000 · ติดตั้ง EA · VPS) → ข้อมูลก่อนตัดสินใจ → โซเชียล (ซ่อนเมื่อไม่มีลิงก์) → คำเตือนความเสี่ยง → ลิงก์นโยบาย เงื่อนไข ประกาศความเสี่ยง และตั้งค่าคุกกี้
- ปุ่มที่มีข้อความแต่ลิงก์ว่างแสดงเป็นปุ่มเส้นประจาง กดไม่ได้ พร้อมป้าย `links_pending_label` "เร็ว ๆ นี้" · ตอนนี้มี 2 จุด: คู่มือฝากเงิน (`links_deposit_guide_url`) และ VPS macOS (`links_guide5_url`)
- ยังไม่มีลิงก์สมัครของทีม (`links_signup_url`) จึงแสดงปุ่ม LINE "ขอลิงก์เปิดบัญชีจากทีมงานทาง LINE" (`data-line-pos` go-signup) · ยังไม่มีไฟล์ EA (`links_fast_url`) จึงแสดงปุ่ม LINE "ขอรับไฟล์ EA2000 ทาง LINE" (go-download) · กรอกลิงก์จริงเมื่อไร ปุ่มสมัครและการ์ดดาวน์โหลดจะแทนที่เอง
- ขั้นเปิดบัญชีมีหมายเหตุเปิดเผยความเป็นพันธมิตร Zaurix (`links_step1_note`) อยู่ก่อนปุ่ม · ปุ่ม Forward Test ยังเป็น "Forward Test คืออะไร อ่านผลอย่างไร" ไม่ใช่ "ดูผลเทรดจริง" เพราะยังไม่มีผลจริง
- หัวข้อเนื้อหายาวเดิม (linksdoc) ซ่อนด้วย `links_show_doc` = false · ปุ่ม LINE ท้ายหน้าเดิมถอดออกตามแบบ · ข้อความทุกคำเป็น setting ใน Customizer หมวด 22 · CSS section 48
- ตรวจบนเว็บจริงด้วยการแตะที่พิกัดจริง 390px: ทุกปุ่ม 22 จุด elementFromPoint โดนตัวเอง · ปุ่มคู่มือและ VPS ไปหน้าถูก · ปุ่ม "เร็ว ๆ นี้" ไม่พาไปไหน · ลิงก์ตั้งค่าคุกกี้เปิดชั้นตั้งค่าได้ · ไม่มีหน้าล้นแนวนอน

## 44) ลิงก์จริงของแบรนด์ ตั้งครบแล้ว (29 ต.ค. 2026)
- เจ้าของส่งลิงก์ 4 เส้น ตั้งผ่าน `ea2000/v1/mods` แล้ว (ไม่ต้อง deploy เพราะเป็นค่าตั้ง ไม่ใช่โค้ด)
  - `links_signup_url` = `https://portal.zaurix.com?ref=211281` (ลิงก์แนะนำของ EA2000 · **เลขต่างจากของ FENIX** ซึ่งเป็น ref=202859)
  - `line_url` = `https://lin.ee/Ylna3Jy` (เดิม `https://lin.ee/ye11pwm6`)
  - `facebook_url` = `https://www.facebook.com/ea2000.algo/`
  - `links_openchat_url` = ลิงก์ OpenChat เดิม ไม่เปลี่ยน
- **ลิงก์ LINE เก่ากับใหม่ชี้บัญชีเดียวกัน** ทั้งคู่ 301 ไป `line.me/R/ti/p/@033fmsxy` · **QR ในฟุตเตอร์ (media 116) จึงยังถูกต้อง ไม่ต้องเปลี่ยน**
- ผลที่หน้าเว็บ: ปุ่ม "เปิดบัญชี MT5" บน /go/ เป็นปุ่มจริงแล้ว แทนปุ่ม LINE สำรอง (`links_signup_line_label`) · ไอคอน Facebook โผล่ทั้งฟุตเตอร์และ /go/ · `sameAs` ของ Organization schema มีเพจ Facebook แล้ว ซึ่งปิดช่องว่างเรื่อง entity ของแบรนด์ในข้อ 17
- ยังเหลือปุ่ม LINE สำรองอยู่ 1 จุดบน /go/ คือขั้น 4 "ขอรับไฟล์ EA2000 ทาง LINE" เพราะ `links_fast_url` ยังว่าง · และยังไม่มีลิงก์ Instagram, TikTok, YouTube
- ตรวจบนเว็บจริงด้วยการแตะที่พิกัดจริง 390px: 23 จุดบน /go/ โดนตัวเองครบ ไม่มีหน้าล้นแนวนอน · ลิงก์ใหม่ขึ้นครบทุกหน้าที่ตรวจ (/go/, หน้าแรก, /pricing/, /how-to-install/)

## 45) หน้าคู่มือ VPS บน macOS (29 ต.ค. 2026 · deploy แล้ว v2.12.0)
- เจ้าของสั่ง "ทำตามที่เสนอได้เลย" หลังจากที่ผมเสนอไว้ว่าจะสร้างหน้าที่ยังเป็นปุ่ม "เร็ว ๆ นี้" บน /go/ · ทำได้หน้าเดียวคือ VPS macOS · **คู่มือฝากเงินยังทำไม่ได้** เหตุผลอยู่ท้ายข้อนี้
- **เพจใหม่ id 186** slug `vps-macos` ใช้ `template-guide.php` · prefix `gvmac` · เพิ่มใน `ea2000_guide_map()`, `ea2000_guide_dl_prefixes()` และ customizer หมวด 28 · **รวมเพจจริงของ EA2000 เป็น 18 หน้า**
- เนื้อหา 6 ขั้น: ติดตั้ง Windows App จาก Mac App Store → เพิ่มเครื่องด้วย Add PC → กรอก IP ในช่อง PC name → ตั้ง User Account ของ VPS → ตั้งค่า Display กับ Clipboard → ดับเบิลคลิกการ์ดเพื่อเชื่อมต่อ · หัวข้อเสริม 4 หัวข้อ: ทำไมคนใช้ Mac ต้องใช้ VPS · ตารางเทียบคีย์ลัด Mac กับ Windows (3 คอลัมน์) · ตารางแก้ปัญหา (5 แถว) · ใช้ VPS ตัวเดียวกันจากเครื่องอื่น
- **ข้อเท็จจริงที่ตรวจจริงจาก iTunes Lookup API**: แอปชื่อ Windows App ผู้พัฒนา Microsoft Corporation · Mac App Store id **1295203466** · **ต้องใช้ macOS 14 ขึ้นไป** (ใส่ไว้ทั้งในการ์ดดาวน์โหลดและตารางแก้ปัญหา) · ชื่อเดิมคือ Microsoft Remote Desktop
- **ไม่มีภาพประกอบเลย** เพราะถ่ายหน้าจอ macOS ไม่ได้ (เครื่องเป็นวินโดวส์) และฟินิกซ์ไม่มีหน้านี้ให้ยืม · ตั้งสมาชิกตัวที่ 3 ของทุก step เป็น `''` ทำให้ `_img_alt` กับ `_img_note` ว่าง ขั้นตอนจึงกินเต็มความกว้างและ**ไม่มีกรอบเส้นประค้าง** (วิธีตามข้อ 26)
- ตั้ง `links_guide5_url` = `/vps-macos/` ปุ่ม macOS บน /go/ จึงเปลี่ยนจาก "เร็ว ๆ นี้" เป็นลิงก์จริง · เพิ่มเมนูย่อย item 188 "ใช้ VPS บน Mac" ใต้ "คู่มือการใช้งาน" · รายการ "คู่มืออื่นที่เกี่ยวข้อง" ขึ้นเองทุกหน้าเพราะอ่านจาก `ea2000_guide_map()` · re-save เพจ 90 ถึง 94 และ 52 ให้ lastmod ขยับ
- Yoast: title 40 ตัวอักษร · desc 141 · focus keyphrase `vps mac` · og/twitter ครบ · เข้า page-sitemap แล้ว (16 URL)
- ตรวจบนเว็บจริง 390px และ 1280px: 6 ขั้นเต็มความกว้าง ไม่มีกรอบรูปว่าง · ตาราง 2 ตัวมี `data-label` ครบ ไม่ล้นแนวนอน · ลิงก์ในเนื้อหา `[ข้อความ](/slug/)` แปลงครบ 6 เส้น ไม่มีโค้ดดิบหลุด · ไม่มี PHP notice ไม่มี JS error · แตะปุ่ม macOS บน /go/ แล้วไปถึง /vps-macos/ จริง
- **คู่มือฝากเงิน (`links_deposit_guide_url`) ยังเป็นปุ่ม "เร็ว ๆ นี้" ต่อไป** เพราะหน้าฝากเงินจริงอยู่หลังล็อกอิน Portal ของ Zaurix · เว็บสาธารณะของ Zaurix บอกแค่ยอดฝากขั้นต่ำ 5 ถึง 10 ดอลลาร์ตามชนิดบัญชี ไม่ประกาศช่องทางการจ่ายเงิน · **ต้องได้จากเจ้าของก่อน**: ภาพหน้าจอหน้าฝากเงินใน Portal (ปิดข้อมูลส่วนตัว) · ช่องทางที่คนไทยใช้ได้จริงและค่าธรรมเนียม · ยอดฝากขั้นต่ำของชนิดบัญชีที่แนะนำ · เขียนเดาเองไม่ได้เพราะเป็นเรื่องการโอนเงินจริง

## 46) รีดีไซน์ทั้งเว็บเป็นโทนมืด (29 ต.ค. 2026 · commit 2a5e216 · deploy แล้ว v3.0.0)
- เจ้าของสั่งรีดีไซน์ตามเว็บในเครือ eawing.co แต่ใช้ดำกับเขียวมะนาว ห้ามแสงนีออน · ออกแบบเป็นภาพก่อน 11 แบบหน้า x 3 ขนาดจอ แล้วเจ้าของเลือกแบบ A (กระจกฝ้า) · ไฟล์แบบอยู่ที่ `design-mockups/` ในรีโปนี้ (ยังไม่ commit · 10 MB) และ artifact https://claude.ai/artifact/Kcns9AqDTNBNHUAiwkpwQk
- **สีที่เจ้าของเคาะ หลังเปลี่ยน 3 รอบ**: เขียว `#9EF01A` (ตก `#AFD83C` และ `#CCFF33`) · พื้น `#070707` · เทากลางจริง ไม่อมเขียว
- **บทเรียนสำคัญ 2 ข้อจากรอบนี้**
  1. รอบแรกผมใส่เขียวที่ป้ายหัวข้อ ไอคอน ตัวเลข เส้นขีด ทุกจุด **และตั้งสีเทากับดำเป็นเทาอมเขียว** (`#A8B29F`, `#060806`) ทั้งหน้าเลยเขียวไปหมด เจ้าของถามว่า "มึงพยายามจะใส่สีเขียวลงไปหรอ เพื่ออะไร" · กฎใหม่: **เทาและดำต้องเป็นกลางจริง (R=G=B) และเขียวใช้เฉพาะ ปุ่มหลัก ป้ายแนะนำ หน้าปัจจุบัน สัญญาณสถานะ**
  2. กระจกฝ้ารอบแรกดูโทรมเพราะเจ้าของสั่งตัดไล่สีและแสงพื้นหลังออกหมด พอข้างหลังเป็นดำเรียบ การ์ดจึงเหลือแค่กล่องเทา · แก้ด้วยการ**ใส่ของจริงไว้ข้างหลังให้ฝ้าทับ** (แท่งเทียนใน hero) + เนื้อกระจกทึบขึ้น + ขอบบนสว่างกว่าขอบอื่น + ถอด `saturate()` ที่ดูดสีเขียวข้างหลังมาขยาย
- **วิธีที่ใช้พลิกธีมทั้งเว็บโดยไม่แตะ CSS 8900 บรรทัด**: ธีมมีสี hardcode นอก `:root` แค่ 26 จุด (เกือบทั้งหมดเป็นสีโลโก้โซเชียล) จึง**เปลี่ยนค่า token ใน `:root` อย่างเดียว ชื่อ token เดิมทุกตัว** แล้วเพิ่ม section 49 ท้ายไฟล์สำหรับผิวกระจกและการถอดสีเขียว · ลบ section 49 ทิ้งแล้วโครงสีเดิมกลับมา (แต่ `:root` เปลี่ยนไปแล้ว)
  - `--primary` = เขียว (ใช้เป็นพื้นและขอบ 37 จุด) แต่ 24 จุดที่ใช้เป็นสีข้อความถูก override เป็น `var(--text)` ใน 49.3
  - `--primary-deep` = `#EDEDED` เพราะ 44 จุดเป็นสีข้อความ ส่วน 8 จุดที่เป็นพื้นปุ่ม override เป็น `--lime-deep`
  - `--ink-accent` = `#EDEDED` (เดิมเขียว ใช้เป็นสีข้อความ 22 จุดในฟุตเตอร์และบาร์ล่าง) · **ยกเว้น `.dock-label` ที่ยังเขียวตามคำสั่งเจ้าของข้อ 38**
  - `--accent-2` = ขาวโปร่ง 9% (เดิมเขียวอ่อน ใช้เป็นพื้นชิป)
- **ฉากหลังแท่งเทียนเดิน** `assets/js/candles.js` 5.5 KB canvas ล้วน · แท่งขวาสุดก่อตัวทุก 55 ms ครบ 2.6 วินาทีปิดแท่งแล้วเลื่อนซ้าย · สเกลราคาไล่เข้าหาเป้าช้า ๆ ไม่กระโดด · **หยุดวาดเมื่อเลื่อนพ้นจอหรือสลับแท็บ** และหยุดสนิทเมื่อตั้งลดการเคลื่อนไหว · `.hero-bg` ใน front-page.php · **ตัวเลขสุ่มด้วยเมล็ดคงที่ ไม่ใช่ราคาจริง ไม่ใช่ผลการเทรด**
- **วิธีตรวจก่อน deploy ที่ควรใช้ซ้ำ**: ดึง HTML ของหน้าจริงจากเว็บสดมาเก็บไว้ แล้ว**สลับ URL ของ style.css เป็นไฟล์ในเครื่อง** (`scratchpad/live/preview.js`) จะเรนเดอร์หน้าจริงกับ CSS ใหม่ได้โดยไม่ต้อง deploy · คู่กับ `audit.js` ที่**ผสมสีโปร่งขึ้นไปตามสายพ่อแม่ก่อนคำนวณคอนทราสต์** (รอบแรกไม่ผสมแล้วได้ผลบวกลวงทั้งหน้า) หาพื้นสว่างที่หลงเหลือ คู่สีที่ต่ำกว่าเกณฑ์ และหน้าล้นแนวนอน · ผลตรวจ 9 หน้า x 2 ขนาดจอ ทั้งก่อนและหลัง deploy: clean ทุกหน้า
- **ยังไม่ได้ทำ ต้องตัดสินใจก่อน**
  1. **ลำดับบล็อกของหน้าแรก**: ภาพแบบที่อนุมัติเป็นโครงสั้น 8 บล็อก แต่หน้าแรกจริงยังเป็นโครง Control Room 9 บท (ข้อ 16) ซึ่งเนื้อหาผ่านการตรวจมาหลายรอบแล้ว · รอบนี้**เปลี่ยนแค่สีและผิว ไม่ได้รื้อโครง** เพราะการรื้อจะทิ้งเนื้อหาที่เจ้าของอนุมัติไว้ · ถ้าเจ้าของอยากได้โครงสั้นตามภาพ ต้องสั่งอีกครั้ง
  2. **3 หน้าใหม่ในแบบที่ยังไม่ได้สร้าง**: `/ea2000/` หน้าสินค้า · `/tools/` เครื่องคำนวณ Lot และ Drawdown · `/deposit/` คู่มือฝากเงิน (ยังติดเรื่องข้อมูลจริงตามข้อ 45)
  3. ภาพหน้าจอ MT5 ในคู่มือเป็นพื้นขาว ตัดกับหน้ามืดแรง · ตอนนี้หรี่ลง 6% และคืนค่าเต็มเมื่อชี้เมาส์ · ถ้าอยากให้กลมกลืนกว่านี้ต้องถ่ายใหม่ด้วยธีมมืดของ MT5

## 47) สามหน้าใหม่ และบทเรียนเรื่อง %s ใน theme mod (29 ต.ค. 2026 · deploy แล้ว v3.1.2)
- เจ้าของสั่ง 3 ข้อ: หน้าแรกเอาโครงเดิมไว้ (ไม่ต้องรื้อ) · สร้าง 3 หน้าใหม่ได้เลย · ภาพหน้าจอ MT5 พื้นขาวปล่อยไว้ไม่ต้องแก้
- **เพจใหม่ 3 หน้า รวมเพจจริงของ EA2000 เป็น 21 หน้า**
  - **189 `ea2000`** หน้าสินค้า · `template-product.php` · ตารางสเปกจาก setting `prod_spec` (บรรทัดละ `หัวข้อ | ค่า`) · สิ่งที่ได้รับ 3 การ์ด · หัวข้อเสริม `proddoc_sec1..4` · กล่องสิ่งที่ไม่รับประกัน
  - **190 `tools`** เครื่องมือ · `template-tools.php` + `assets/js/tools.js` · เครื่องคำนวณขนาดออเดอร์ (คำนวณในเบราว์เซอร์ ไม่ส่งค่าออก ปัดลงเป็นช่วง 0.01 lot เสมอ) และตาราง drawdown ที่**คำนวณสดจากสูตร** ไม่ได้ hardcode · หัวข้อเสริม `toolsdoc_sec1..4`
  - **191 `deposit`** คู่มือฝากเงิน · ใช้ระบบหน้าคู่มือเดิม prefix `gdep` 6 ขั้น · ปิดงานค้างจากข้อ 45
- **หน้าแรกเคยใช้ slug `ea2000`** ต้องเปลี่ยนเป็น `home-ea2000` ก่อนจึงสร้างหน้าสินค้าที่ `/ea2000/` ได้ · หน้าแรกยังตอบ 200 ที่ `/` ตามเดิมเพราะผูกด้วย id
- **ข้อมูลขั้นตอนฝากเงินได้จากเว็บในเครือ eawing.co** ซึ่งใช้โบรกเกอร์ Zaurix เดียวกัน · ขั้นตอนจริง: Main Wallet กด Deposit (ถ้ายังไม่ผ่าน KYC ระบบพาไปหน้ายืนยันตัวตนแทน) → Add New Bank Account ครั้งแรกครั้งเดียว ช่องชื่อผู้ถือบัญชีระบบดึงจากเอกสาร KYC แก้เองไม่ได้ → เลือกธนาคาร → Funds แท็บ Deposit เลือก QR Payment, Bank Account, บัญชี MT5 ปลายทาง, ใส่ยอด USD หรือ THB → ตรวจ Payment Details ที่ Deposit To Account กับ Transfer Amount แล้วกด Continue → รอยอดขึ้นที่ Balance ใน MT5 · วิธีฝากมี **QR Payment กับ KuCoin** (ไม่ใช่ "คริปโต" ลอย ๆ) · **ไม่ระบุยอดขั้นต่ำเป็นตัวเลข** ให้ดูจากหน้า Portal ณ วันที่ทำรายการ
- **ภาพ 5 ใบยืมจากเว็บในเครือได้ทั้งหมด** (media 192 ถึง 196) เพราะเป็นฟอร์มเปล่า ยอด 0.00 ไม่มีชื่อ ไม่มีเลขบัญชี ไม่มีแบรนด์ของเขา · ขั้น 6 ไม่มีภาพ (ตั้ง `_img` และ `_img_note` เป็น `""`)
- เมนูหัวเว็บเรียงใหม่: หน้าแรก · **สินค้า** · การทดสอบ · คู่มือการใช้งาน · **เครื่องมือ** · แพ็กเกจ · บทความ · เมนูย่อยคู่มือเรียงตามลำดับการใช้งานจริง (สมัครบัญชี → ล็อกอิน MT5 → **ฝากเงิน** → ติดตั้ง EA → VPS 4 ระบบ)

### บทเรียนที่ต้องจำ
1. **ห้ามใช้ `%s` เป็นตัวยึดในค่า setting ของธีม** · `get_theme_mod()` ของคอร์ WordPress เรียก `sprintf( $mod, get_template_directory_uri(), get_stylesheet_directory_uri() )` กับค่าที่เป็นสตริงเสมอ · `%s` จึงกลายเป็น URL ของธีม · ใช้ตัวยึดแบบ `{amount}` แทน · **เจอจากการทดสอบเครื่องคำนวณบนเว็บจริง ไม่ได้เจอจากการอ่านโค้ด**
2. **`ea2000_media_slot()` รับ prefix ไม่ใช่ชื่อคีย์เต็ม** ส่ง `'prod_img'` จะไปอ่าน `prod_img_img` ที่ไม่มีอยู่ แล้ว**เงียบหายทั้งช่อง** ไม่มี error · ต้องส่ง `'prod'`
3. **`ea2000_rich_text()` ไม่รองรับ `**` ตัวหนา** เครื่องหมายจะโผล่เป็นตัวอักษรบนหน้าเว็บ · รูปแบบที่รองรับมีแค่ บรรทัดว่าง `- ` `1. ` `### ` ตาราง ` | ` และลิงก์ `[ข้อความ](/slug/)`
4. **ตัวตรวจที่ใช้ `elementFromPoint` ต้องข้ามของที่อยู่นอกกรอบจอ** ไม่งั้นได้ผลบวกลวง 1,137 จาก 1,514 จุด · หลังแก้เหลือ 49 จุดซึ่งทั้งหมดเป็นของที่ถูก header ติดขอบ บาร์ล่างมือถือ หรือการ์ดคุกกี้บังชั่วคราว ไม่ใช่บั๊ก

### ผลตรวจบนเว็บจริงรอบนี้
- เครื่องคำนวณ ทดสอบด้วยการพิมพ์จริง 5 กรณี ผ่านหมด (1000/1%/300/1 ได้ 0.03 · 5000/2%/200/1 ได้ 0.50 · ทุนน้อยได้ 0.00 · 10000/0.5%/250/2 ได้ 0.10 · stop 0 ได้ขีด) · ตาราง drawdown 50% ต้องได้คืน 100% ถูกต้อง
- 3 หน้าใหม่ไม่มีพื้นสว่างหลงเหลือ ไม่มีคู่สีต่ำกว่าเกณฑ์ ไม่มีหน้าล้นแนวนอน ทั้ง 1440 และ 390
- sitemap มี 19 URL แล้ว · Yoast title 40 ถึง 43 ตัวอักษร desc 136 ถึง 139 ครบทั้ง 3 หน้า

## 48) ถอดเครื่องแต่งกายแนวคอนโซล และช่องว่างในฟุตเตอร์ (6 พ.ย. 2569 · deploy แล้ว v3.2.2)
- เจ้าของบอกว่าเว็บ "ดูเป็นเอไอ" แล้วส่งภาพชี้ 3 จุด: รางเลขบทด้านซ้าย (00/09 แล้วไล่ 01 ถึง 09) · ตัวนับ `01 / 09` ที่หัวบท · ป้ายตัวเล็กแบบ monospace · แล้วสั่งให้หาจุดอื่นด้วย
- **CSS section 51** ถอดของประดับออกโดยไม่แตะเนื้อหา ลบทั้งบล็อกแล้วหน้าตาเดิมกลับมา
  - `.mono` เปลี่ยนจาก `--font-mono` เป็น `--font-display` · **นี่คือตัวการหลักของความรู้สึก "หน้าจอระบบ"** มี 104 จุดบนหน้าแรก
  - ซ่อน: รางเลขบท (`show_rail` = false ด้วย) · ตัวนับ `01 / 09` (เหลือชื่อบท) · เทอร์มินัลจำลอง (เล่าซ้ำกับรายการขั้นตอนข้าง ๆ) · นาฬิกาเดินจริง · บรรทัด `ea2000@line:~$` · เส้นสแกน · วงเล็บมุม HUD 56 จุดบนหน้าแรก
  - **ตัวเลขที่เป็นลำดับขั้นตอนจริงยังอยู่** (ขั้นตอนการทำงานหน้าแรก คู่มือ 6 ขั้น /go/ 6 ขั้น) · **ตัวเลขประดับบนรายการที่ไม่ใช่ลำดับถูกถอด** (คำถามที่พบบ่อย โมดูล แท็บ ปัญหา คู่มือที่เกี่ยวข้อง ดัชนีหน้าในฟุตเตอร์)
- **ช่องว่างใหญ่ในฟุตเตอร์ที่เจ้าของถาม**: เป็นตัวอักษร `EA2000` ขนาดยักษ์จาง ๆ (`p.watermark`) สูง **405px** · ถอดออกแล้วฟุตเตอร์จาก 1295px เหลือ 850px
- **บทเรียน: ซ่อนตัวเลขเฉย ๆ ไม่พอ** กริดที่กันคอลัมน์แรกไว้ให้ตัวเลข (`28px minmax(0,1fr)` เป็นต้น) พอซ่อนลูกตัวแรก ข้อความจะตกไปอยู่ในคอลัมน์กว้าง 28 ถึง 42px แล้วตัดบรรทัดจนกล่องสูงผิดปกติ · ฟุตเตอร์โตจาก 1295 เป็น 1417px ก่อนจะจับได้ · ต้องลดจำนวนคอลัมน์ด้วย: `.readout-list li`, `.diag-row`, `.q-sum`, `.guide-more-item > a`
- **บั๊กเก่าที่เจอระหว่างทาง (3.2.2)**: กฎผิวกระจกข้อ 49.1 คลุม `.lh-btn` ทั้งหมด จึงทับพื้นสีเขียวของปุ่ม LINE บน /go/ ทิ้งไว้แต่สีตัวอักษรเข้ม `#03240F` กลายเป็น **ตัวเข้มบนพื้นเข้ม 1.3:1** อ่านไม่ออกมาตั้งแต่ v3.0.0 · ป้าย "แนะนำ" ก็เช่นกัน พื้นกลายเป็นขาวโปร่ง (`--accent-2-rgb` เปลี่ยนเป็น 255,255,255) และตัวหนังสือกลายเป็นขาว (`--primary-deep` เปลี่ยนเป็น #EDEDED) · **บทเรียน: ตอนพลิก token ทั้งชุด ต้องไล่ดูทุกจุดที่ `color` เป็นค่าคงที่สีเข้ม เพราะพื้นที่เคยสว่างอาจกลายเป็นมืด**
- ตรวจหลัง deploy ด้วยตัวตรวจที่ผสมสีโปร่งขึ้นไปตามสายพ่อแม่: 15 หน้า x 1440 และ 390 ไม่มีคอนทราสต์ต่ำกว่าเกณฑ์ ไม่มีหน้าล้นแนวนอน ไม่มีกล่องที่สูงผิดส่วน · สคริปต์อยู่ scratchpad `ai/check.js` และ `ai/measure.js`
- **ยังเหลือที่ดูเป็นเอไอแต่ต้องให้เจ้าของตัดสิน**: ภาพกล่องสินค้าที่มีหุ่นยนต์ และภาพแผง Dashboard ทั้งหมดเป็นภาพที่สร้างด้วย AI (มี C2PA ตามข้อ 30) ซึ่งเป็นจุดที่คนดูออกง่ายที่สุดที่เหลืออยู่ · เปลี่ยนได้ต่อเมื่อมีภาพถ่ายจริง

## 49) หน้าตาใหม่รอบที่ 2 · ภาพร่างครบ 21 หน้า 3 ขนาดจอ (6 พ.ย. 2569 · **ยังไม่ขึ้นเว็บจริง**)
- เจ้าของสั่ง: รีเว็บเฉพาะหน้าตา **เนื้อหาทุกหน้าคงเดิม** · ธีมเขียวดำเหมือนเดิม · ห้ามแสงสี ห้ามนีออน · **ปุ่มมุมแหลม** · **ปุ่มหลักสีเขียวธีม** · **ปุ่มรองพื้นใส ขอบขาว ตัวอักษรขาว** · ให้ทำภาพร่างทุกหน้าทุกขนาดจอมาให้ตรวจก่อน ผ่านแล้วค่อยขึ้นเว็บ
- **วิธีทำที่ใช้ และควรใช้ซ้ำทุกครั้งที่จะรื้อหน้าตา**: ดึง HTML ของหน้าจริงจากเว็บสดมาเก็บไว้ (`grab.js` → `raw/`) แล้วเขียนสำเนาที่ต่อ `<link>` ชี้ไฟล์สไตล์ร่างในเครื่องเข้าท้าย `<head>` (`prep/`) · เรนเดอร์ด้วย headless Chrome (`shoot.js`) · ได้ภาพหน้าจริงกับสไตล์ใหม่โดยไม่แตะเว็บจริงเลย · สคริปต์อยู่ scratchpad `v2/`
- **กับดักของวิธีนี้**: สไตล์ของธีมมาจาก `https://ea2000.co` ผ่านเครือข่าย ถ้าวัดก่อนมันโหลดเสร็จจะได้หน้าที่ยังไม่มีสไตล์ แล้วตัวตรวจจะรายงานคอนทราสต์ต่ำเป็นสิบจุดแบบผลบวกลวง (ลิงก์เป็นสีน้ำเงิน `rgb(0,0,238)` คือสัญญาณ) · ทุกสคริปต์จึงต้องรอจน `performance.getEntriesByType('resource')` เห็น `themes/ea2000/style.css` และ `--font-display` มีค่าก่อน
- **ความสูงภาพบานปลาย**: ห้ามเลื่อนหน้าในสคริปต์ถ่ายภาพ เพราะ canvas แท่งเทียนขยายตามกรอบจนหน้าสูงถึง 46000px · ใช้ `captureBeyondViewport: true` แทน · และ `deviceScaleFactor` ต้องเป็น 1 ทุกความกว้าง ไม่งั้นหน้ายาวที่ 390px x2 จะค้างไปเลย
- **ผลงานที่ส่งให้เจ้าของ**: โฟลเดอร์ `design-mockups-v2/` ในรีโป (18 MB · **ไม่ commit** เหมือน `design-mockups/` รอบก่อน) แยก `desktop-1440/`, `tablet-768/`, `mobile-390/` หน้าละไฟล์ WebP พร้อม `index.html` เปิดดูทีละหน้าสลับขนาดจอได้ และ `skin-draft.css` ที่ใช้ทำภาพ · artifact https://claude.ai/artifact/369V3VW8kTL8SGatRcR6rD
- **สาระของแบบใหม่**: `*` รีเซ็ตมุมโค้ง เงา และกระจกฝ้าออกทั้งหมด (มีรายการยกเว้นของที่เป็นวงกลมจริง เช่น โลโก้ สวิตช์ จุดไฟ) · ผิวการ์ดเป็นแผ่นทึบ `#0C0C0C` ขอบ 1px · กริดการ์ดใช้ `gap: 1px` พื้นสีเส้น ทำให้อ่านเป็นตารางเส้นเดียวแทนกล่องลอย · หัวข้อส่วนมีเส้นคาดเต็มความกว้างแล้วหัวข้อนั่งบนเส้น · แถบสเปกเป็นแถวเส้นบางมีเส้นคั่นแนวตั้ง · เลขขั้นตอนเป็นบล็อกเขียวทึบ
- **บทเรียนจากรอบตรวจภาพ**: กริดที่ใช้ `gap: 1px` พื้นสีเส้น ถ้าการ์ดสูงไม่เท่ากันจะเห็นพื้นเส้นเป็นแถบเทาโผล่ ต้องบังคับ `align-items: stretch` (ธีมตั้ง `align-items: start` ไว้ที่ `.tools-grid`) · วงเล็บเหลี่ยม `[หน้าแรก]` ของเมนูอยู่ที่ `.nav-list > li::before/::after` ไม่ใช่ที่ `a` · `.dl-btn` ต้องกันไม่ให้กินเต็มความกว้างเมื่อการ์ดมีใบเดียว
- **ข้อที่รอเจ้าของตัดสิน**: ปุ่ม LINE ยังใช้เขียวของ LINE `#06C755` เป็นสีแบรนด์ ทำให้บางจุดมีเขียวสองเฉดติดกัน (เช่นบนหน้า /ea2000/ ปุ่ม "ดูแพ็กเกจและราคา" เขียวธีม ติดกับ "สอบถามทาง LINE" เขียว LINE) · ทางเลือกคือให้ปุ่ม LINE ใช้แบบปุ่มรอง (ใส ขอบขาว) แล้วเหลือสีแบรนด์ไว้ที่ไอคอน

## 50) หน้าตาใหม่รอบที่ 2 ขึ้นเว็บจริงแล้ว (6 พ.ย. 2569 · commit a9c375a และ 591be32 · deploy แล้ว v4.0.1)
- เจ้าของอนุมัติจากภาพร่างในข้อ 49 แล้วสั่ง "เริ่มได้เลย ทำเสร็จแล้ว อัพขึ้นเว็บ"
- **หน้าแรกใช้แบบ C แผงเต็มจอ** · **ปุ่ม LINE ใช้แบบ 4** พื้นโปร่ง ขอบและตัวอักษรเขียวธีม ชี้เมาส์แล้วเติมพื้น

### โครงสร้างที่เพิ่มใน style.css
- **section 52** คือไฟล์ร่าง `skin-draft.css` ทั้งก้อน (ผิวแผ่นสเปกทั้งเว็บ) · ลบทั้งบล็อกแล้วหน้าตา 49 ถึง 51 กลับมา
- **section 53** คือโครงหน้าแรกแบบ C · ลบทั้งบล็อกแล้วหน้าแรกกลับไปเป็นโครงเดิม (section 40)
- วิธีทำโครงบทโดย**ไม่แตะมาร์กอัปของบท**: `.home-v3 .ch > .container` เป็นกริด 2 คอลัมน์ · `.ch-head` ถูกวางที่ `grid-row: 1 / -1` คอลัมน์ 1 และ sticky · ลูกที่เหลือทุกตัวถูกบังคับเป็น `grid-column: 2` ด้วย `:not(.ch-head)` จึงเรียงต่อกันในคอลัมน์ขวาโดยไม่ต้องห่อ div ใหม่
- แถบสลับพื้นด้วย `.ch:nth-of-type(odd|even)` · คลาส `band-surface` เดิมถูกตั้งเป็น `background: inherit` ไม่ให้ไปขัดลำดับสลับ

### ที่แก้ในเทมเพลต
- `front-page.php` ส่วนเปิดแบ่งครึ่งจอ: `dl.hud` ย้ายออกจาก `.boot-copy` ไปอยู่ใน `div.boot-spec` คอลัมน์ขวา
- **กล่องสินค้าในส่วนเปิดถูกถอดออก** ตามแบบที่เจ้าของเลือก · setting `hero_image` และ `hero_img_note` ยังอยู่ ถ้าจะเอากลับมาให้เพิ่ม `figure` ในคอลัมน์ขวา
- `functions.php` ปิด hook `ea2000_preload_hero` (คอมเมนต์ไว้ ไม่ได้ลบฟังก์ชัน) เพราะไม่มีภาพนั้นในส่วนเปิดแล้ว
- ฉากหลังแท่งเทียนย้ายมาอยู่หลังคอลัมน์ขวาพอดี (`width: 44%`) และจางลงเป็น `opacity: .2` เพราะโครงใหม่มีตัวหนังสือทับจริง ไม่ใช่ที่ว่างแบบโครงเก่า · มือถือ `.1`

### บทเรียนรอบนี้
- **MSYS แปลง `/pricing/` เป็น `C:/Program Files/Git/pricing/`** เวลาส่งเป็นอาร์กิวเมนต์ใน Git Bash · ต้องใส่ `MSYS_NO_PATHCONV=1` ไม่งั้นสคริปต์ตรวจจะยิงไป URL ผิดแล้วรายงานผลของหน้าที่ไม่มีจริง
- `scratchpad/ai/check.js` ไม่ข้าม element ที่พ่อเป็น `display: none` จึงรายงาน `.wm-ea` กับ `.wm-num` (ลายน้ำที่ section 51 ซ่อนไว้) เป็นคอนทราสต์ 1.00:1 ทุกหน้า · เป็นผลบวกลวงของตัวตรวจ ไม่ใช่บั๊กของเว็บ · ตัวตรวจที่ถูกต้องกว่าคือ `scratchpad/v2/audit.js` ซึ่งข้ามของที่ขนาดเป็นศูนย์
- ตอนพลิกปุ่ม LINE จากพื้นทึบเป็นพื้นโปร่ง **ต้องตั้งสีให้ลูกของปุ่มด้วย** (`.lh-lbl`, `.lh-ic`, `.lh-ar` บน /go/ ตั้ง `#03240F` ไว้ที่ลูก) ไม่งั้นได้ตัวเข้มบนพื้นเข้ม 1.18:1

### ผลตรวจบนเว็บจริงหลัง deploy
- 17 หน้า x 1440 และหน้าหลัก x 390: ไม่มีหน้าล้นแนวนอน ไม่มีคอนทราสต์ต่ำกว่าเกณฑ์ ไม่มี PHP error
- ภาพร่างที่ใช้อนุมัติยังอยู่ที่ `design-mockups-v2/` (ไม่ commit) และ artifact https://claude.ai/artifact/369V3VW8kTL8SGatRcR6rD

### ค้าง
- ยังไม่ได้ re-save เพจเพื่อขยับ lastmod (ข้อ 35 บอกให้ทำหลัง deploy งานเนื้อหา · รอบนี้เปลี่ยนแค่หน้าตา ไม่ใช่เนื้อหา จึงยังไม่ทำ)
- `docs/home-v2-spec.md` ยังอธิบายโครง Control Room เดิม ให้ถือเป็นประวัติในส่วนที่ขัดกับ section 53

### แก้หลัง deploy · เมนูเดสก์ท็อปกลายเป็นกล่อง (v4.0.2 · commit 106cf39)
- เจ้าของส่งภาพว่าแถบเมนูมีกล่องสีต่างคร่อมอยู่ · วัดจริงแล้ว `nav.site-nav` ได้ `background: #101010` และ `border: 1px` จาก section 52
- สาเหตุ: `.site-nav` เป็น**ลิ้นชักเมนูในโหมดมือถือ** (ที่ไม่เกิน 960px) จึงต้องมีพื้นกับกรอบ · แต่ตั้งแต่ 961px ขึ้นไปมันเป็นแค่แถวลิงก์ในแถบหัวเว็บ · ของเดิม (section 49.2) ให้พื้นเป็น `--glass-pop` ซึ่งเกือบเท่าสีแถบหัวเว็บและไม่มีกรอบ จึงมองไม่เห็น พอเปลี่ยนเป็นสีทึบบวกกรอบจึงโผล่
- แก้ใน section 53.5 ด้วย `@media (min-width: 961px)` คืน `.site-nav` เป็นพื้นโปร่งไม่มีกรอบ · ตรวจแล้วลิ้นชักมือถือที่ 390px ยังมีพื้นกับกรอบเหมือนเดิม
- **บทเรียน: คลาสที่ใช้ร่วมกันระหว่างลิ้นชักมือถือกับแถบเดสก์ท็อป ต้องใส่สไตล์ของลิ้นชักในเบรกพอยต์เสมอ** ไม่ใช่เขียนลอยแล้วหวังว่าเดสก์ท็อปจะไม่เห็น

## 51) ชุดตรวจ 109 ตัวไล่ทุกหน้าทุกขนาดจอ แล้วแก้ครบ 76 จุด (6 พ.ย. 2569 · commit 14944a7 และ f5478af · deploy แล้ว v4.2.1)
- เจ้าของส่งภาพ 3 ใบชี้ว่าเส้นขอบซ้อนกันจนบางจุดดูหนา และตัวหนังสือชิดเส้น แล้วสั่งว่า "ช่วยสร้างคนงาน ตรวจเช็กพวกจุดบัคแบบนี้และจุดอื่น ๆ ทุกหน้า ทุกจุด ทุกขนาดหน้าจอเลย แบบนี้งานชุ่ยมาก ไม่ผ่าน"
- **ผมตรวจไม่ครบจริง** รอบ v4.0.1 ตรวจแค่คอนทราสต์กับหน้าล้นแนวนอน ไม่เคยตรวจเส้นกับระยะในเลย · เครื่องมือที่ขาดไปเขียนเพิ่มเป็น `scratchpad/edges/scan.js` (หาเส้นขนานที่ห่างกัน 0.5 ถึง 4px และทับกันตามยาวเกิน 120px · และกล่องที่มีกรอบแต่ padding ด้านนั้นน้อยกว่า 10px) คู่กับ `pad.js` (วัด padding/margin/border/ตำแหน่งของ selector ที่ระบุ) และ `shot.js` (ถ่ายเฉพาะบริเวณที่ 2x)
- workflow 109 agent: ผู้ตรวจ 11 คนแบ่งกันดู 21 หน้า x 1440/768/390 ด้วยเครื่องมือข้างบนบวกการดูภาพ · ทุกข้อที่พบส่งให้ผู้หักล้างวัดซ้ำเอง · **ยืนยัน 76 ตีตก 22** (ที่ตีตกส่วนใหญ่คือตัวเลขถูกแต่ตีความผิด เช่น แท่งเขียวหน้า h2 เป็นมาร์กเกอร์ของบรรทัดแรก ไม่ใช่เส้นที่ต้องสูงเท่าหัวข้อ)

### ต้นเหตุร่วม · 76 จุดมาจากไม่กี่เรื่อง
1. **section 52 เติมกรอบหรือพื้นให้ของที่ไม่เคยเป็นกล่อง** แล้วไม่ได้คืน padding ให้ · และของที่มีเส้นอยู่แล้วเลยกลายเป็นเส้นคู่
2. `.phero` มีเส้นล่าง และ `.section` แรกใน `main` มีเส้นบน มาเจอกันพอดี → **เส้นหนา 2px สองโทนใต้หัวหน้าเพจทุกหน้าย่อย** (9 หน้า) · แก้ด้วย `.phero + main > .section:first-of-type { border-top: 0 }`
3. **ตารางที่ยุบเป็นการ์ดบนมือถือ** · section 52 ให้ `border-bottom` กับ `th` และ `td` ของทุกตาราง ทับกฎ card mode ที่เขียนไว้ก่อนหน้า (มีเดียควีรีไม่เพิ่ม specificity ลำดับในไฟล์เป็นตัวตัดสิน) → เส้นท้ายการ์ดเป็นสองเส้นห่าง 13px และ **การ์ดใบสุดท้ายไม่มีเส้นในเลย** เพราะกฎเดิม `tr:last-child td { border-bottom: 0 }` ยัง specificity สูงกว่า · 16 รายการจากเรื่องเดียวนี้
4. **เลขลำดับขั้นกลายเป็นแผ่นพื้นเขียว** (`.ledger-idx`, `.guide-step-num`) แต่ของเดิมเป็นตัวหนังสือ ไม่มี padding และเป็นลูกกริดที่ยืดเต็มช่อง → แท่งเขียวสูง 109 ถึง 161px ไม่เท่ากัน และตัวเลขชิดขอบซ้าย 0.0px · แก้เป็นชิป inline-flex จัดกลาง
5. **ปุ่มลอย LINE ทับลิงก์ กลับด้านบน** ช่วง 761 ถึง 1180px (ปุ่มโผล่แล้วแต่คอนเทนเนอร์ยังไม่กว้างสุด) · บังข้อความและกินการแตะ 9 หน้า · แก้ด้วย `margin-right: 76px` ให้ลิงก์ในเบรกพอยต์นั้น · **ห้ามแก้ด้วย z-index** เพราะปุ่มเป็นพื้นทึบ จะยังบังตัวหนังสืออยู่

### ที่แก้ไว้ใน style.css section 55 (ลบทั้งบล็อกแล้วกลับสภาพเดิม)
- 55.1 เส้นใต้หัวหน้าเพจ · 55.2 แถบบท `band-surface` หน้าแรก · 55.3 ตารางโหมดการ์ดบนมือถือ (รวมทำให้ `th` ยุบเป็นบล็อกและขึ้นป้ายจาก `data-label` เอง) · 55.4 สีเส้นคั่นแถวของ `th` ให้เท่า `td` + ตารางในการ์ด `.tool-card` ไม่ล้น + คอลัมน์แรกกว้างพอไม่ตัดกลางคำไทย · 55.5 ชิปเลขขั้น · 55.6 ปุ่มลอย · 55.7 ฟิล์มภาพหน้าแรก · 55.8 แผงแพ็กเกจ · 55.9 การ์ดราคา · 55.10 รายการคู่มือ · 55.11 การ์ดดาวน์โหลด · 55.12 คอลัมน์ท้ายเว็บ · 55.13 แถบ CTA · 55.14 ระยะล่างของ `.doc-sections` ที่ซ้อน section · 55.15 `/articles/` · 55.16 หน้าความเสี่ยง · 55.17 ปุ่มคู่ท้ายเว็บ · 55.18 บาร์ล่างมือถือ · 55.19 ภาพในขั้นตอนคู่มือ
- **`.guide-more` รายการคู่มือ**: เดิมใบสุดท้ายในจำนวนคี่ถูกยืดเต็มสองคอลัมน์เพื่อไม่ให้เห็นช่องพื้นเทา (กริดใช้ `gap: 1px` บนพื้นสีเส้น) · เปลี่ยนเป็นเติมเซลล์อุดช่อง `.guide-more::after { content: ""; background: var(--bg) }` การ์ดจึงกว้างเท่ากันทุกใบและเส้นคั่นกลางวาดครบทุกแถว · `::after` ปิดที่ 640px เพราะเหลือคอลัมน์เดียว

### ที่แก้ในเทมเพลตและ PHP
- `template-guide.php` คำอธิบายขั้นตอนเปลี่ยนจาก `ea2000_text()` เป็น **`ea2000_rich_inline()`** · เดิมข้อความ `[ป้าย](/slug/)` ในขั้นที่ 1 และ 6 ของ `/deposit/` พิมพ์ออกมาเป็นตัวอักษรดิบให้ผู้อ่านเห็น · ตอนนี้ทุกหน้าคู่มือใส่ลิงก์ในคำอธิบายขั้นตอนได้แล้ว (keep_words ของ rich_inline เป็น latin อย่างเดียว ตรงกับที่ย่อหน้าเนื้อหาทั้งเว็บใช้)
- `template-product.php` ถอด `data-label` ออกจาก `<td>` เพราะ `<th scope="row">` เป็นชื่อการ์ดอยู่แล้ว · เดิมมือถือพิมพ์หัวข้อซ้ำสองครั้งทุกแถว
- `template-links.php` ป้ายปุ่ม `.lh-lbl` ใช้ `ea2000_text()` แทน `esc_html()` ป้ายจึงได้ `span.nobr` · `Zaurix-Server` เลิกตัดกลางที่ยัติภังค์
- `inc/page-content.php` เพิ่มคู่ `แปล|ว่า` ใน `ea2000_keep_words()` · หัวข้อคำถามหน้า `/backtest/` เคยตัดเป็น "แปล" / "ว่า"

### ผลตรวจหลัง deploy
- `scan.js` ทั้ง 21 หน้า x 1440/768/390: **เส้นซ้อน 0 แบบทุกหน้าทุกขนาด** · รายการ "ชิดเส้น" ที่เหลือ 14 แบบเป็นคอมโพเนนต์ที่เป็นแถวโดยตั้งใจ (ลิงก์ในฟุตเตอร์ แถวคำถาม ชิปเวอร์ชัน ป้าย hazard) ไม่ใช่กล่อง
- `audit-live.js` (ผสมสีโปร่งตามสายพ่อแม่): คอนทราสต์ผ่าน ไม่มีพื้นสว่างหลงเหลือ ไม่มีหน้าล้นแนวนอน ทั้งสามขนาด · เหลือธง 2 จุดที่เป็นช่องตารางซึ่งสูงตามเซลล์ข้างที่ข้อความยาวกว่า ไม่ใช่บั๊ก
- `verify55.js` วัดทีละรายการบนเว็บจริง 30 จุด ผ่านทั้งหมด (ชิปเลขสูง 27px เท่ากันทุกใบ padding 10px · ฟิล์มกับ ledger ขอบซ้าย 616.63 ตรงกัน · ปุ่มลอยไม่ทับและแตะโดน `a.status-top` · ตารางในการ์ด client เท่ากับ scroll · การ์ดคู่มือ 13 ใบกว้าง 560 เท่ากัน ลิงก์เต็มการ์ดทุกใบ)

### บทเรียน
- **มีเดียควีรีไม่เพิ่ม specificity** กฎ card mode ที่เขียนไว้ก่อนจึงแพ้กฎ `table td` ที่เขียนทีหลังในไฟล์เดียวกัน · ตอนเติม section ใหม่ท้ายไฟล์ ต้องไล่ดูว่ามันทับกฎในมีเดียควีรีเก่าหรือไม่
- **เครื่องมือตรวจต้องวัดสิ่งที่คนเห็น ไม่ใช่กล่อง** · รอบแรกผมวัด `.film` ที่กล่อง (มี margin ติดลบ 4px) แล้วคิดว่ายังไม่แก้ · ของจริงต้องวัด `figure` ใบแรก · เช่นเดียวกับ `.riskdoc-updated` ที่ต้องวัดตำแหน่งหมึกด้วย `Range.getBoundingClientRect()` ไม่ใช่ขอบกล่อง
- **`MSYS_NO_PATHCONV=1` ต้องใส่ทุกครั้ง** ที่ส่ง path แบบ `/deposit/` เป็นอาร์กิวเมนต์ใน Git Bash · ไม่งั้นสคริปต์ยิงไป URL ผิดแล้วรายงานว่า "ไม่พบ selector" ซึ่งอ่านเหมือนบั๊กของเว็บ (เสียเวลาไปรอบหนึ่ง)
- สคริปต์ตรวจอยู่ที่ scratchpad `edges/scan.js`, `edges/pad.js`, `edges/shot.js`, `edges/verify55.js` และ `v2/audit-live.js`

## 52) ตรวจส่วนที่เหลือทั้งหมด แล้วแก้ 24 จุดที่ยังค้าง (6 พ.ย. 2569 · commit 6f5bc11 และ 7c0d7e9 · deploy แล้ว v4.6.1)
- เจ้าของสั่งต่อจากข้อ 51 ว่า "ลองเช็กหน้าอื่นที่เหลือด้วย พร้อมแก้ให้เสร็จสิ้น" · 21 หน้าเนื้อหาถูกไล่ไปแล้วในข้อ 51 ส่วนที่เหลือจึงหมายถึงพื้นที่ที่**ไม่เคยตรวจเลย** คือ หน้า 404 · หน้าผลการค้นหา · เทมเพลตบทความเดี่ยว · ความกว้าง 1920, 1280, 1024, 480, 320 · สถานะที่ต้องกดถึงจะเห็น (ลิ้นชักเมนู แท็บ คำถามที่กางออก การ์ดคุกกี้ QR เครื่องคำนวณ) · โหมดปิด JavaScript · โหมดลดการเคลื่อนไหว · และเส้นทางรีไดเรกต์ทั้งแบบมีและไม่มี www
- workflow 67 agent (ผู้ตรวจ 12 ด้าน แต่ละข้อส่งให้ผู้หักล้างวัดซ้ำเอง) · **ยืนยัน 42 ข้อ** · 18 ข้อแก้และ deploy ไปแล้วระหว่างรอบตรวจ (v4.2.2 ถึง v4.5.0 · CSS section 55.20, 56, 57, 58, 59, 60) · อีก **24 ข้อที่เหลืออยู่ใน v4.6.0 (section 61) และ v4.6.1**

### การ์ดคุกกี้ · ข้อที่ร้ายแรงที่สุดของรอบนี้
- **การ์ดทับปุ่ม "สอบถามทาง LINE" ในส่วนเปิดของหน้าแรกจนกดไม่ได้เลย** สแกน elementFromPoint ทั่วพื้นที่ปุ่มได้ 0 จาก 160 จุดที่ 1440x640 และเป็นแบบเดียวกันที่ 1024x568, 1024x640, 480x568, 480x640, 390x640 · ที่ 390x812 ปุ่มที่สองถูกบังมิดทั้งปุ่ม · **1366x768 ซึ่งคือ viewport ราว 640 เป็นจอโน้ตบุ๊กที่พบบ่อยที่สุด** ผู้เข้าชมครั้งแรกจึงไม่เห็น CTA เดียวของเว็บเลย · ข้อ 39 เคยบันทึกว่า "การ์ดมือถือไม่บังปุ่ม LINE ใน hero" แต่ตรวจที่จอสูง 812 เท่านั้น
- แก้สองส่วนตามความกว้าง เพราะทรงปุ่มต่างกัน: ตั้งแต่ 961px ปุ่มกว้างตามเนื้อหา จึงย้ายการ์ดไปชิดขวา (`left: auto; right: 24px`) · ไม่เกิน 960px ปุ่มกินเต็มความกว้าง ไม่มีที่ให้หลบแนวนอน จึงเลิกเกาะขอบล่างเมื่อจอสูงไม่เกิน 860px แล้วขึ้นไปอยู่ใต้แถบหัวเว็บ (ทับแค่ kicker กับ H1 ที่เป็นข้อความล้วน) · กัน `/go/` ไว้ด้วย `body:not(.link-hub-page)` เพราะปุ่มบนหน้านั้นไม่เคยถูกบัง
- **ปุ่มลอย LINE ถูกซ่อนระหว่างที่การ์ดเปิด** (`main.js` ตั้งคลาส `has-consent` บน `<html>` · CSS ซ่อนที่ 961px ขึ้นไป) เพราะการ์ดย้ายไปอยู่ที่เดิมของปุ่มลอย และตอนการ์ดเปิด ปุ่ม LINE ในส่วนเปิดก็กดได้แล้ว ปุ่มลอยจึงเป็นของซ้ำซ้อน
- ปุ่มบนการ์ดมีพื้นเท่าพื้นการ์ดเป๊ะ เหลือกรอบจางกว่า `.btn-ghost` ของเว็บ · ตั้งกรอบเป็นขาวโปร่ง 55% ได้ 6.23:1 เท่ากับปุ่มอื่น
- ปุ่มตกสองบรรทัดทุกความกว้างไม่เกิน 413px เพราะ `!important` ของ section 14.1 ทับขนาดอักษรที่ธีมตั้งไว้ให้การ์ดนี้ · แถวปุ่มสูง 62.8px ลดเหลือ 52.0px เท่ากันทุกปุ่ม
- ลิงก์บรรทัดสุดท้ายในชั้นตั้งค่าแปะติดกรอบปุ่มบันทึก

### หน้าผลการค้นหา
- **"พบ 13 ผลลัพธ์" แต่แสดง 12 ใบ แล้วกดโหลดเพิ่มไม่ได้อะไรเลย** `ea2000_load_more()` บังคับ `post_type = 'post'` ขณะที่ main query ของการค้นหาครอบทุกชนิดที่ค้นหาได้ · เปลี่ยนเป็น `'any'` เมื่อมีคำค้น (ชุดเดียวกับที่ main query ใช้ จึงตรงกันเองถ้าวันหลังมี CPT เพิ่ม) · ตรวจจริงแล้วทั้งทาง admin-ajax และการกดปุ่มที่พิกัดจริง: การ์ด 12 เป็น 13 แล้วกล่องปุ่มหายไปถูกต้อง
- คำโปรยถูกตัดกลางคำ เพราะ `wp_trim_words` นับคำด้วยช่องว่างซึ่งภาษาไทยไม่มี · ส่งข้อความเต็มมาแล้วให้ `.post-card-body p` ตัดสองบรรทัดด้วย line-clamp · ถอดหาง `[…]` ที่ WordPress เติมมา และไม่พิมพ์ `<p>` เปล่าให้เพจที่ไม่มีเนื้อหา (เดิม 9 จาก 12 ใบเป็นแบบนี้)
- การ์ดยกตัวและเปลี่ยนสีกรอบตอนชี้เมาส์ มีคำว่า "อ่านต่อ" พร้อมลูกศรที่ขยับ แต่กดได้แค่ชื่อเรื่อง · ให้ลิงก์ชื่อเรื่องแผ่เต็มการ์ดด้วย `::after` (ตัวอ่านหน้าจอยังเห็นลิงก์เดียว) · **ข้อแลก: เลือกคลุมข้อความในการ์ดด้วยเมาส์ไม่ได้แล้ว**
- บรรทัด "อ่านต่อ" ของการ์ดในแถวเดียวกันอยู่คนละระดับ · `margin-top: auto` ต้องคู่กับการย้าย 4px ไปเป็น `padding-top` ไม่งั้นการ์ดที่เนื้อเต็มพอดีจะเตี้ยลง 4px
- พื้นที่กดของช่องค้นหาสูงแค่ 22px จากกรอบที่เห็น 66px (ฟอร์มเป็น flex ที่ `align-items: center`) · มีผลกับหน้า 404 ด้วย ซึ่งเป็นสิ่งที่ต้องการ · ตำแหน่งตัวอักษรไม่ขยับเลย (วัดได้ 531.0 ที่ 1440 เท่าเดิม)
- ช่องค้นหาลอยอยู่กลางหน้าขณะที่ทุกอย่างในหัวหน้าชิดซ้าย · คำค้นยาวไม่มีช่องว่างดันหัวเรื่องทะลุขอบขวา หน้าเลื่อนข้าง 54px · กดปุ่มค้นหาโดยไม่พิมพ์อะไรได้หัวเรื่อง `ผลการค้นหา: ""` แล้วไล่เพจทั้งเว็บมาแสดง (เติม `required` ที่ช่อง และเปลี่ยนหัวเรื่องเป็น "ค้นหาในเว็บไซต์" เมื่อคำค้นว่าง)

### เทมเพลตบทความเดี่ยว · เตรียมรับชุดบทความ MT5 ตามข้อ 42
ยังไม่มีบทความบนเว็บ (`x-wp-total: 0`) ผู้ตรวจจึงทดสอบด้วยการฉีดมาร์กอัปตาม `single.php` เข้าไปบนหน้าจริง · **ห้ามสร้างโพสต์จริงบนเว็บเพื่อทดสอบ**
- `.entry-content` ไม่มี `overflow-wrap` ที่ไหนเลย ย่อหน้าที่มีที่อยู่โฟลเดอร์ MQL5 หรือลิงก์ยาวจะดันหน้าให้เลื่อนข้าง (วัดได้ 921 และ 1005 ที่จอ 390) · บทความชุด MT5 ต้องบอกที่อยู่โฟลเดอร์อยู่แล้วจึงเจอแน่ · ใช้ `break-word` ไม่ใช่ `anywhere` เพราะไม่เปลี่ยนค่า min-content จึงไม่ไปบีบคอลัมน์ตาราง · บล็อกโค้ด `pre` ไม่มีกฎ overflow เลย ล้น 198px
- **ตารางที่เขียนด้วยตัวแก้ไข WordPress ไม่เหลือเส้นสักเส้นบนมือถือ** เพราะกฎถอดเส้นของ 55.3 เขียน selector กว้างเกิน (`.entry-content .table-scroll th/td`) คลุมทุกตาราง ไม่ใช่แค่ `.doc-table` ที่ยุบเป็นการ์ด · **แก้ selector ในที่** แล้วเส้นฐานที่ไม่ได้อยู่ในมีเดียควีรีกลับมาทำงานเอง ไม่ต้องเขียนกฎคืนเส้น
- หัวตารางไทยถูกตัวแบ่งคำหั่นกลางคำเมื่อคอลัมน์แคบ (ออเดอร์ เป็น ออเด กับ อร์) · `white-space: nowrap` ที่ `thead th` เป็นแพตเทิร์นเดิมของ `.doc-table` บังคับให้คอลัมน์กว้างพอตามหัวตารางเองโดยไม่ต้องกำหนด `min-width` ที่จะไปทับหน้าอื่น
- ปุ่มแชร์และปุ่มเลื่อนแถบบทความที่เกี่ยวข้องเป็น 42x42 · `width`/`height` ถูกตรึงไว้ `min-*` จึงไม่มีผล ต้องแก้ค่าตรง

### ที่เหลือ
- เส้นคาดเหนือหัวข้อหน้า `/pricing/` สั้นกว่าเนื้อหาใต้มัน 400px ที่ 1920 และ 400px ที่ 1440 เพราะ `.sec-head` มี `max-width: 720px` ขณะที่ section 52 ให้ `border-top` กับกล่องนี้ · ย้ายความกว้างอ่านง่ายไปไว้ที่ `h2` และ `p` · **ต้องประกาศ `max-width: none` ไม่ใช่ลบบรรทัดทิ้ง** เพราะมี `.sec-head { max-width: 720px }` ซ้ำอยู่ที่บรรทัด 476 · วัดหลังแก้: เส้นคาดตรงกับ `.pricing-grid` และ `.compare-wrap` พอดีทั้ง 1440 และ 1920 และตัวอักษรไม่ขยับ
- ปุ่มลอย LINE ทับปุ่มในแถบ CTA ท้ายหน้า 60px คงที่ตั้งแต่ 961 ถึง 1287px (14 หน้าที่เรียก `ea2000_line_cta()`) อ่านได้แค่ "ทัก LINE Official Acc" · เว้นที่ 76px ให้ `.cta-console-inner` และปิดช่วงที่ 1319 เพราะ 1288 ถึง 1319 ช่องไฟตามธรรมชาติยังเหลือ 0 ถึง 16px · หลังแก้ gap ไม่ต่ำกว่า 16px ทุกความกว้าง และแตะที่พิกัดจริงโดนปุ่มครบ 297 จาก 297 จุด
- คำโปรยการ์ด VIP สองบรรทัด ดันเส้นประ ราคา และรายการข้อดีของใบนั้นต่ำกว่าอีกสองใบทั้งก้อน ในช่วง 961 ถึงราว 1090px
- เส้นคั่นเหนือหมายเหตุในการ์ดดาวน์โหลดใบที่สามไม่ตรงแนว · ใช้ subgrid จำกัดที่ `.dl-cards[data-count="3"]` และห่อ `@supports` · **`grid-row: span 4` สมมติว่าการ์ดพิมพ์ครบสี่ชิ้น ถ้าเจ้าของล้างช่องหมายเหตุหรือขั้นตอนของใบใดใบหนึ่งใน Customizer ปุ่มจะเลื่อนไปอยู่ผิดแถว** · ราคาที่ยอมจ่าย: ที่ 992px การ์ดทั้งสามสูงขึ้น 21.8px
- ป้าย "ข้ามไปยังเนื้อหา" ได้ขาวบนเขียว 1.84:1 ตอนที่เมาส์วางอยู่ตรงนั้น (ซึ่งเป็นตำแหน่งโลโก้พอดี) เพราะ `a:hover` ของ 49.3 เปลี่ยนตัวอักษรเป็นขาว
- ลิงก์ข้ามไปหัวข้อในเอกสาร 15 หน้าไปจอดใต้แถบหัวเว็บ · เติม `.doc-section` และ `.entry-content :is(h2, h3, h4)[id]` เข้ากลุ่ม `scroll-margin-top` เดิม · **อย่าหักค่า padding ของ `.doc-section` ออก** เพราะหัวข้อแรกของแต่ละหน้าไม่มี padding (กฎที่ให้ padding คือ `.doc-section + .doc-section`)
- ปุ่มขีดสามเส้นตอนปิด JavaScript กดแล้วไม่เกิดอะไร เพราะลิ้นชักเปิดด้วย `body.nav-open` ที่ main.js ตั้งให้เท่านั้น · ซ่อนปุ่มในโหมด `no-js` (เมนูทั้งชุดยังเข้าถึงได้จากดัชนีหน้าในฟุตเตอร์) · **ห้ามแก้ด้วยการเปิดลิ้นชักค้าง** เพราะที่ไม่เกิน 960px มันเป็น `position: fixed` เต็มจอ

### บทเรียน
- **ตรรกะเดียวกันที่เขียนไว้สองที่ ต้องแก้ทั้งสองที่** · บาร์ล่างมือถือติดไฟว่าอยู่หน้าแรกขณะอยู่หน้าผลการค้นหา · แก้ฝั่ง PHP ไปแล้วใน 4.6.0 และ `curl` ยืนยันว่า HTML ที่เสิร์ฟถูกต้อง แต่ในเบราว์เซอร์ยังติดไฟ เพราะ `main.js` มีตัวกันเหนียวที่หาหน้าปัจจุบันเองเมื่อ HTML ไม่มีคีย์ `is-active` และตัวนั้นเทียบ `location.pathname` ซึ่งเป็น `/` สำหรับ `/?s=...` เหมือนหน้าแรก · **ถ้าสถานะหนึ่งถูกคำนวณได้ทั้งฝั่งเซิร์ฟเวอร์และฝั่งเบราว์เซอร์ ต้องตรวจทั้ง curl และเบราว์เซอร์** เห็นตรงกันแล้วจึงถือว่าผ่าน
- **ของที่ซ่อนอยู่ทำให้การวัดการซ้อนกันเป็นเท็จ** · การ์ดคุกกี้ซ่อนปุ่มลอยไว้ตอนเปิด ปุ่มลอยจึงมี rect เป็น 0 ทั้งหมด แล้วสูตร gap ให้ค่าติดลบมหาศาลที่อ่านเหมือนซ้อนกันหนัก · ก่อนวัดปุ่มลอย ต้องปิดการ์ดก่อนทุกครั้ง
- **`scrollIntoView` ไม่ทำงานใน headless** (ข้อ 34 เคยบันทึกเรื่อง `scrollBehavior` ไว้แล้วแต่ยังพลาดซ้ำ) ต้องตั้ง `scrollBehavior = auto` แล้วใช้ `window.scrollTo` เอง · และการวัดพื้นที่กดของ element ที่อยู่นอกจอจะคืน 0/0 หรือ 0/297 ซึ่งอ่านเหมือนบั๊กของเว็บ ทั้งที่เป็นความผิดของตัววัด
- **ที่ 390x640 ปุ่มที่สองในส่วนเปิดอยู่ใต้บาร์ล่างมือถือพอดีตอนยังไม่เลื่อนหน้า** ซึ่งเป็นธรรมชาติของบาร์ที่เกาะขอบล่าง ไม่ใช่บั๊ก (ผู้ตรวจรายงานว่าหลังแก้ปุ่มทั้งสอง "ว่างทั้งคู่" ซึ่งไม่จริง) · การ์ดคุกกี้ไม่เกี่ยวแล้ว ยืนยันด้วยการไล่สายพ่อแม่ของตัวที่โดนกด
- ผลตรวจหลัง deploy: `scan.js` ทั้ง 5 หน้าที่แตะ x 1440/1024/768/390 **เส้นซ้อน 0 แบบทุกค่า** · `audit-live.js` คอนทราสต์และหน้าล้นแนวนอนสะอาดทั้งสามความกว้าง (เหลือธงเดียวที่เป็นช่องตารางซึ่งสูงตามเซลล์ข้างที่ข้อความยาวกว่า ไม่ใช่บั๊ก) · สคริปต์ตรวจรอบนี้อยู่ scratchpad `edges/verify61.js`, `verify61d.js`, `verify61e.js`

## 53) แผนบทความ 114 บท วันละบท 4 เดือน (6 พ.ย. 2569 · `docs/content-plan-4months.json`)
- เจ้าของสั่งให้วางแผนลงบทความ **วันละบทตลอดสี่เดือนรวมเดือนนี้ เริ่มบทแรกพรุ่งนี้** พร้อมคีย์หลัก คีย์รอง ลองเทล ภาพประกอบ และลิงก์ภายใน และสั่งให้ไปค้นข้อมูลมาจริง
- **เก็บคำค้นใหม่ทั้งชุด 6 พ.ย. 2569**: autocomplete ของ Google, Bing, DuckDuckGo · คำขอ 4,751 ครั้งต่อเครื่อง (seed 89 คำ x 37 ส่วนต่อท้าย + probe 37 คำ) · ได้คำไม่ซ้ำ **10,971 คำ** คัดที่เกี่ยวข้อง **8,573 คำ** · สคริปต์อยู่ scratchpad `kw2/` (`harvest.js`, `filter.js`, `assemble.js`, `links.js`, `export.js`, `genhtml.js`)
- **ไม่มี search volume** ตัวเลข n คือจำนวน engine ที่คืนคำนั้น (1 ถึง 3) ห้ามอ้างเป็นปริมาณการค้นหา
- โครง: 114 บท · 7 พ.ย. 2569 ถึง 28 ก.พ. 2570 · 6 สปรินต์ สปรินต์ละคลัสเตอร์ (แก้ปัญหา MT5 24 · ต้นทุนและคำศัพท์ 22 · ทองและ XAUUSD 21 · EA และบอทเทรด 21 · อ่านกราฟ 17 · พื้นฐานและเก็บตก 9) · คำค้นรวม 372 (หลัก 114 รอง 258) · ภาพ 309 ใบ ผมทำเอง 298 · ต้องใช้ภาพหน้าจอจากเจ้าของ 11 บท · รวมราว 167,700 คำ
- ตรวจแล้ว: **คำหลักไม่ซ้ำกันเอง ไม่ชนคำของ 21 หน้าเดิม slug ไม่ซ้ำ** · ทุกบทมีลิงก์ขาเข้าอย่างน้อย 2 เส้นผ่านช่อง `backfill_on_publish_day` (งานเติมลิงก์ย้อนหลัง 244 ครั้ง)
- หลักฐานคำหลัก: ขึ้นครบ 3 engine 56 บท · 2 engine 14 บท · 1 engine 29 บท · **15 บทไม่มีหลักฐานตรงตัว** ซึ่งระบุไว้ในไฟล์ว่าเขียนเพื่อปิดช่องว่างของคลัสเตอร์และรับลิงก์ภายใน ไม่ใช่บทที่เดิมพันทราฟฟิก
- **ข้อเท็จจริงที่แก้จากแผนเดิมข้อ 42**: `/tools/` (สร้าง 29 ต.ค.) ถือคำ `คำนวณ lot size` อยู่แล้ว แผนเดิมตั้งคำนี้ให้บทความ RM1 ซึ่งจะชนกันเอง · แผนใหม่ให้บทความใช้ `lot size คือ` และลิงก์ไป /tools/ แทน
- ห้ามลืม: หมวดบทความยังมีแค่ OTHER (0 ชิ้น) · ผู้เขียนยังเป็น ADMIN · `date_format` ยังเป็น `F j, Y` ภาษาอังกฤษ · ยังไม่มี category.php และกล่องผู้เขียน · ทั้งหมดอยู่ในรายการ prerequisites ของไฟล์แผน
- หน้าสรุปให้เจ้าของ: artifact "แผนบทความ 114 บท" https://claude.ai/artifact/XKfVN9g8nq9sJdGVB1A3FJ
- **รอเจ้าของตัดสิน 3 เรื่องก่อนลงบทแรก**: โครง URL (เสนอ `/articles/%postname%/` ตั้งผ่าน REST ไม่ได้) · ชื่อผู้เขียนและประวัติสั้น (เสนอ "ทีมงาน EA2000") · ข้อความคำเตือนท้ายบทความ

## 54) เขียนบทความทั้ง 114 บทแล้วตั้งเวลา (6 พ.ย. 2569)
- เจ้าของสั่ง "ก็เขียนบทความทั้งหมดเลยสิ แล้วตั้งเวลา รออะไร" · ทำเป็นสายพานผลิตใน scratchpad `gen/` แล้วปล่อยเป็นคลื่น คลื่นละ 14 ถึง 24 บทด้วย workflow (หนึ่ง agent ต่อหนึ่งบท)
- **ธีม 4.8.1**: `ea2000_reading_time()` นับตัวอักษรหาร 700 ต่อนาที (ภาษาไทยไม่มีช่องว่างคั่นคำ) · `ea2000_author_box()` ใต้กล่องคำเตือน อ่านจาก Biographical Info ของผู้ใช้ก่อน ถ้าว่างใช้ `author_bio` ในธีม · **ถอดตัวนับยอดเข้าชมออกจากหน้าบทความ** เพราะขึ้นว่า "เข้าชม 0 ครั้ง" ทุกบท (ฟังก์ชันยังอยู่ ไม่ได้ลบ) · CSS section 62
- เปิด index ให้หน้า `/articles/` แล้ว (เดิม noindex ตามข้อ 32)

### สายพานผลิตใน scratchpad/gen/ (ใช้ซ้ำได้ทุกคลื่น)
| ไฟล์ | หน้าที่ |
|---|---|
| `briefs.js` + `addnotes.js` | แตก `docs/content-plan-4months.json` เป็นบรีฟรายบท 114 ไฟล์ พร้อมช่อง `note` ประจำคลัสเตอร์ และรายชื่อพี่น้องที่ลงก่อนบทนี้ |
| `WRITING-SPEC.md` | สเปกการเขียน ข้อห้าม โครงบท HTML ที่ใช้ได้ สเปกภาพ และรายการ path ที่ลิงก์ได้ |
| `wf-write.js` | workflow เขียนบท รับรายชื่อไฟล์บรีฟเป็น args · agent ต้องรัน `check.js` ของตัวเองจนไม่เหลือ error |
| `check.js` | ตัวตรวจ 8 ด้าน ขีดยาว คำต้องห้าม การอ้างกลยุทธ์ มาร์กเกอร์ภาพ `data-label` ลิงก์นอกรายการ ลิงก์ไปบทที่ยังไม่เผยแพร่ ความยาว Yoast |
| `stock.js` + `stockfilter.js` + `repick.js` | คลังภาพถ่ายฟรีจาก Pexels |
| `figs.js` | เรนเดอร์ภาพที่วาดเอง (cover, steps, flow, window) ด้วย headless Chrome และจองภาพถ่ายไม่ให้ซ้ำในบทเดียวกัน |
| `publish.js` | อัปโหลดภาพพร้อม alt และ caption แทนมาร์กเกอร์ `<!--FIGn-->` ด้วย `<figure>` แล้วสร้างโพสต์ `status: future` วันที่ตามแผน 09:00 น. · บทที่เคยลงแล้วจะอัปเดตแทนการสร้างซ้ำ (`published.json`) |
| `livecheck.js` + `shot.js` | สร้างโพสต์ทดสอบที่ใส่รหัสผ่านแล้วถ่ายภาพหน้าจริง · ลบด้วย `--clean` |

### ภาพประกอบ บทละ 4 ถึง 5 ใบ
- ปก 1 ใบ (1600x900 พื้น `--ink` ลายจุด ชื่อเรื่อง + ชิป EA2000) · ภาพถ่ายคลังภาพฟรี 2 ใบ · แผนภาพที่วาดเอง 1 ถึง 2 ใบ
- **ตารางให้เขียนเป็นตาราง HTML ในเนื้อหา ห้ามทำเป็นภาพ** เพราะภาพตารางอ่านไม่ออกบนมือถือ · แผนภาพใช้ตัวอักษรใหญ่กว่าภาพในหน้าเว็บ (1200x900) และจำกัดจำนวนบรรทัด
- ภาพที่วาดเองติดป้าย "ภาพประกอบ · EA2000" ทุกใบ และ caption ต้องบอกว่าเป็นแผนภาพ ไม่ใช่หน้าจอจริง
- ภาพถ่ายคลังภาพใช้ใบอนุญาต Pexels (เชิงพาณิชย์ได้ ไม่ต้องให้เครดิต) · โหลดด้วย `curl` เพราะ `fetch` ของ node ได้ 403 · คำค้นบางคำ Pexels ทำ 301 ต้องใส่ `-L`

### บทเรียนรอบนี้
1. **คำค้น `candlestick` ของ Pexels คืนภาพเทียนไข ไม่ใช่กราฟแท่งเทียน** · บทที่ 1 กับ 2 ได้ภาพเทียนหอมกับดอกไม้ไปขึ้นเว็บแล้วก่อนจะจับได้ · **ต้องเปิดดู contact sheet ของทุกหมวดจริง ๆ ไม่ใช่ดูแค่สองใบแรก** (ซ้ำรอยบทเรียนข้อ 22) · ตัดหมวด `person computer` ทิ้งทั้งหมวดด้วย เพราะได้จอ CRT ย้อนยุคและภาพจอที่เขียนว่า "we no longer need your services"
2. **ตัวตรวจที่จับคำข้ามแท็กให้ผลบวกลวง** · ลิงก์ชื่อ EA2000 ที่อยู่ติดกับหัวข้อเรื่อง hedging ถูกจับว่าอ้างกลยุทธ์ · แก้ด้วยการตัดแท็กเป็น `|` ก่อนแล้วให้ขอบเขตของ regex หยุดที่ `|`
3. **คนเขียนหลายคนรันตัวตรวจพร้อมกันแล้วเขียนไฟล์รายงานทับกัน** เหลือบทเดียว ทำให้ตั้งเวลารอบแรกขึ้นแค่ 1 บท · แก้ให้เขียน `check-report.json` เฉพาะตอนตรวจทั้งชุด
4. **อย่าส่งหัว Authorization ซ้ำสองครั้ง** nginx ตอบ 400 โดยไม่บอกเหตุผล (เจอตอนอัปโหลดภาพ เพราะตัวช่วยเติมหัวให้อยู่แล้ว)
5. **heredoc กับ `node -e` ใน Bash กลืน backslash** ทำให้ `\n` ในซอร์สกลายเป็นขึ้นบรรทัดจริงจน regex พัง · ไฟล์สคริปต์ที่มี backslash ให้เขียนด้วย Write tool เท่านั้น หรือออกแบบให้ไม่ต้องใช้ backslash เลย
6. **ลิงก์ย้อนหลัง 244 ครั้งในแผนไม่ต้องทำ** เพราะทุกบทลิงก์ไปพี่น้องที่ลงก่อนหน้าอยู่แล้ว บทที่ลงก่อนจึงสะสมลิงก์ขาเข้าเองจากบทที่ลงทีหลัง บวกกับแถบบทความที่เกี่ยวข้องที่ขึ้นเฉพาะบทที่เผยแพร่แล้ว

### ข้อควรรู้เรื่องการตั้งเวลา
- โซนเวลาเว็บคือ Asia/Bangkok · ส่ง `date: "YYYY-MM-DDT09:00:00"` คู่กับ `status: "future"`
- โพสต์ที่ยังไม่ถึงเวลาเปิดดูสาธารณะไม่ได้ · ตรวจหน้าตาด้วย `livecheck.js` ที่สร้างโพสต์ใส่รหัสผ่าน + noindex แล้วลบทิ้ง · ถ่ายภาพต้องกรอกฟอร์มรหัสผ่านในเบราว์เซอร์เอง (ยัดคุกกี้ผ่าน CDP ไม่ผ่าน เพราะแคชของโฮสต์เสิร์ฟหน้าฟอร์มกลับมา)

### ครบ 114 บทแล้ว (10 พ.ย. 2569 · ธีม v4.9.2)
- **114 บทอยู่บน WordPress ครบ** เผยแพร่แล้ว 4 บท (7 ถึง 10 พ.ย.) ตั้งเวลาอีก 110 บท · **7 พ.ย. 2569 ถึง 28 ก.พ. 2570 วันละบท 09:00 น. ไม่มีวันไหนว่าง ไม่มีวันไหนซ้ำ**
- ผลตรวจบนเว็บจริงผ่าน REST ทั้ง 114 บท: ภาพรวม **569 ใบ เฉลี่ย 5.0 ใบต่อบท** · **ภาพที่ไม่มี alt เป็นศูนย์** · ภาพหน้าปก หมวด excerpt Yoast title metadesc focuskw ครบทุกบท · ไม่มีมาร์กเกอร์ `<!--FIGn-->` ค้าง
- หมวด: MT5 24 · ต้นทุน 13 · ความเสี่ยง 9 · ทอง 21 · EA 21 · กราฟ 17 · พื้นฐาน 7 · VPS 2
- ลิงก์ภายใน **1,380 เส้น เฉลี่ย 12.1 เส้นต่อบท ไม่มีลิงก์เสียเลย** · ไม่มีบทไหนลิงก์ไปบทที่ยังไม่เผยแพร่ · รับลิงก์มากสุดคือบทเสา (mt5-basics 26 · what-is-ea-forex 23 · gold-trading-mt5 20)
- 11 บทท้ายคลัสเตอร์ไม่มีลิงก์ขาเข้าจากเนื้อหาบทอื่น เพราะไม่มีบทไหนลงทีหลังอีกแล้ว · **ไม่ใช่หน้ากำพร้า** เพราะแถบบทความที่เกี่ยวข้องใน `single.php` ลิงก์ให้เองจากทุกบทในหมวดเดียวกันที่เผยแพร่แล้ว (ตรวจบนหน้าสดแล้ว) บวกลิงก์หน้าหมวด
- ตรวจหน้าบทความสดทั้ง 4 บท: ภาพ 5 ใบ ตารางแสดงถูก กล่องผู้เขียนขึ้น สารบัญขึ้น ไม่มี PHP error เปิดให้เก็บดัชนี

### งานธีมที่ทำเพิ่มรอบนี้
- **v4.9.0** รายการคู่มือ 13 ลิงก์ในหน้า `/articles/` เคยขึ้นเฉพาะตอนยังไม่มีบทความ พอบทความแรกเผยแพร่ลิงก์ชุดนั้นหายไปทั้งชุด · ตอนนี้ขึ้นใต้รายการบทความด้วย เฉพาะหน้าแรกของหน้ารวม (`$ea2000_has_posts` จำสถานะก่อนลูป) · หัวข้อเปลี่ยนเป็น "คู่มือการใช้งานทั้งหมด" เมื่อมีบทความแล้ว
- **v4.9.1 และ 4.9.2** เพิ่ม `ea2000_noindex_empty_terms_array()` บน `wpseo_robots_array` · **เป็นการกันไว้ ไม่ใช่การแก้บั๊ก** commit 4.9.1 อ้างว่าหมวดว่างขึ้นเป็น index ซึ่งไม่จริง แก้คอมเมนต์ในโค้ดแล้วที่ 4.9.2

### บทเรียนเพิ่ม
7. **Yoast พิมพ์ meta ด้วยอัญประกาศเดี่ยว** `<meta name='robots' content='noindex, follow' />` · ตัวตรวจที่เขียน regex หา `content="..."` แบบอัญประกาศคู่จะรายงานว่า "ไม่มีแท็ก" ทุกหน้า แล้วอ่านเป็น index · **regex ที่อ่าน attribute ของ HTML ต้องรับทั้งสองแบบเสมอ** รอบนี้ทำให้ผม commit แก้สิ่งที่ไม่ได้พัง
8. **คำว่า "ล้างพอร์ต" เป็นคำหลักของบทที่ 113** (`ล้างพอร์ต คือ`) จึงห้ามแบนคำนี้ทื่อ ๆ · กฎใหม่ใน `check.js` จับเฉพาะรูปที่เป็นคำสัญญา (`ไม่|กัน|ป้องกัน|เลี่ยง|ไม่มีวัน|ไม่เคย` ตามด้วยคำนี้) และกรณีที่ผูกคำนี้กับชื่อ EA2000 ไม่ว่าจะยืนยันหรือปฏิเสธ
9. บทที่ 085 มีสเปกภาพ FIG5 แต่ agent ลืมวางมาร์กเกอร์ในเนื้อหา · ตัวตรวจจับได้ก่อนขึ้นเว็บ · **กฎมาร์กเกอร์ต้องตรงกับสเปกสองทาง** ไม่ใช่ตรวจทางเดียว

### สิ่งที่ต้องทำต่อ (เจ้าของ)
1. **ภาพหน้าจอ MT5 จริง 11 บท** ที่แผนระบุไว้ · ตอนนี้ใช้แผนภาพที่วาดเองแทนไปก่อนและอ่านรู้เรื่อง แต่บทแก้ปัญหาจะดีกว่ามากถ้ามีหน้าจอจริง · ถ่ายจากบัญชีเดโมที่ไม่เคยติดตั้ง EA2000 · บท: mt5-invalid-account, mt5-chart-not-loading, xauusd-missing-mt5, autotrading-disabled-mt5, mt5-invalid-stops, mt5-market-watch, mt5-journal-experts, ea-set-file, ea-inputs-tab, ea-not-trading, ema-mt5
2. กดขอจัดทำดัชนีใน Search Console สำหรับบทที่เผยแพร่แล้ว
3. ลิงก์ Instagram, TikTok, YouTube ของแบรนด์ ถ้ามี

## 55) จัดลำดับหน้า และทำให้จุดคะแนน Yoast มีสีจริงทั้งเว็บ (11 พ.ย. 2569 · ธีม v4.10.0)
- เจ้าของสั่งให้เรียงหน้าใน wp-admin ให้เป็นระบบ และทำให้จุดสองจุดของ Yoast ในรายการ "หน้า" กับ "เรื่อง" ไม่เป็นสีเทา โดยให้เป็นสีตามที่ Yoast ตรวจจริง

### ลำดับหน้า
- ตั้ง `menu_order` 1 ถึง 21 ให้ตรงกับลำดับเมนูหัวเว็บ แล้วต่อด้วยเอกสารท้ายเว็บ: หน้าแรก · สินค้า · backtest · forward-test · คู่มือ 8 หน้า (สมัคร Zaurix → ล็อกอิน MT5 → ฝากเงิน → ติดตั้ง EA → VPS 4 ระบบ) · เครื่องมือ · แพ็กเกจ · บทความ · /go/ · ความเสี่ยง · เกี่ยวกับ · เงื่อนไข · ความเป็นส่วนตัว · ขอลบข้อมูล
- หน้าจอ "หน้า" ของ WordPress เรียงตาม `menu_order` ก่อนชื่อเป็นค่าเริ่มต้นอยู่แล้ว จึงไม่ต้องเพิ่ม orderby ใน URL · สคริปต์ scratchpad `gen/order-pages.js`
- เดิมมี `menu_order` 1 ถึง 17 ค้างจากรอบก่อน และ 4 หน้าที่สร้างทีหลังเป็น 0 จึงลอยขึ้นไปอยู่บนสุด

### จุดคะแนน Yoast
- **ต้นเหตุของสีเทา: ตัววิเคราะห์ของ Yoast เป็น JavaScript ที่ทำงานเฉพาะในหน้าแก้ไข** เนื้อหาที่สร้างผ่าน REST จึงไม่เคยถูกวิเคราะห์ `_yoast_wpseo_linkdex` และ `_yoast_wpseo_content_score` ว่าง จุดจึงเทา · **คำนวณฝั่ง PHP ไม่ได้ และ Yoast ไม่มีปุ่มคำนวณใหม่ทั้งชุดแล้ว** (ถอดออกตั้งแต่ 3.x)
- วิธีที่ใช้: เปิดหน้าแก้ไขของแต่ละรายการใน **iframe ซ่อน** บนแท็บ wp-admin ที่ล็อกอินอยู่ รอจน `wp.data.select('yoast-seo/editor')` ให้คะแนนที่นิ่งแล้ว อ่านออกมา แล้วเขียนกลับผ่าน REST · เป็นคะแนนของ Yoast เองทุกตัว ไม่ได้คำนวณขึ้นมาเอง
- **กับดักที่เจอ: อ่านเร็วเกินจะได้ผลของเนื้อหาเปล่า** บทความ 2,700 คำอ่านรอบแรกได้ SEO 10 และบอกว่า "ไม่มีรูป" ทั้งที่มี 5 ใบ · ต้องรอจน `core/editor` รายงาน `getCurrentPost().id` ตรงกับ id ที่ขอ แล้วให้ค่าคะแนนนิ่งติดกัน 3 รอบ (เนื้อหาสั้นใช้ 5 รอบ) · หลังแก้ บทเดียวกันได้ SEO 84 อ่านง่าย 90
- `javascript_tool` หมดเวลาที่ 45 วินาที จึงต้องสั่งเป็นงานเบื้องหลังแล้ววนถามความคืบหน้า · ใช้ 3 iframe พร้อมกันได้ความเร็วราว 20 รายการต่อ 40 วินาที · ครบ 135 รายการไม่มีพลาดเลย
- ธีม v4.10.0 เพิ่ม `_yoast_wpseo_linkdex` และ `_yoast_wpseo_content_score` ใน `ea2000_register_yoast_meta_rest()` เพื่อให้เขียนผ่าน REST ได้ · สคริปต์ `gen/write-scores.js` · ผลดิบเก็บที่ `gen/scores.json`

### ผลที่ได้ และสิ่งที่ตัวเลขบอก
- **เรื่อง 114 บท: SEO เขียวทั้งหมด (69 ถึง 93) อ่านง่ายเขียว 90 ทุกบท**
- **หน้า 21 หน้า: 16 หน้าเป็นแดง (SEO 3 ถึง 12) อ่านง่ายส้ม 60** · ไม่ใช่เพราะเนื้อหาไม่ดี แต่เพราะ **เนื้อหาของหน้าพวกนี้อยู่ในธีมกับ Customizer ช่องเนื้อหาในตัวแก้ไขจึงว่างเปล่า** Yoast อ่านเฉพาะ `post_content` จึงเห็นว่าไม่มีข้อความ ไม่มีรูป ไม่มีลิงก์ · เป็นผลที่ถูกต้องตามที่ Yoast ตรวจ และแก้ไม่ได้ถ้ายังยึดหลักข้อ 3.1 ที่ให้ทุกข้อความเป็น setting
- 5 หน้าที่เนื้อหาอยู่ใน WordPress จริง (privacy 72 · terms 68 · about 61 · data-deletion 50 · articles 46) ได้คะแนนตามเนื้อหาจริง
- **คะแนนนี้ไม่มีผลต่ออันดับใน Google** เป็นเครื่องมือช่วยเตือนของปลั๊กอินเท่านั้น · สิ่งที่ส่งถึงเสิร์ชเอนจินจริง (title, meta description, canonical, schema, sitemap) ถูกต้องครบอยู่แล้ว
- **ถ้าแก้บทความแล้วอยากให้จุดอัปเดต** ต้องเปิดหน้าแก้ไขแล้วกดอัปเดต หรือรันวิธีเดียวกันนี้ซ้ำ

## 56) ขอบรูปสีเขียว แถบ LINE ที่อ่านเป็นที่ว่าง และสีตอนชี้เมาส์ (11 พ.ย. 2569 · deploy แล้ว v4.11.0)
เจ้าของแจ้งสามเรื่องในรอบเดียว · **CSS section 63 ลบทั้งบล็อกแล้วกลับไปเป็นแบบ section 62**

### ขอบรูป 1px สีเขียว ทุกรูปยกเว้นโลโก้
- token ใหม่ `--img-edge: rgba(158, 240, 26, 0.5)` ประกาศในบล็อกเดียวกัน เปลี่ยนความเข้มที่เดียว
- **รูปที่ยังไม่มีกรอบ ใส่เส้นที่ตัวรูป**: `.entry-content img` (ภาพในบทความอยู่ใน `figure.wp-block-image` ซึ่งไม่มีเส้น) · `.post-card-thumb img` · `.qr-flyout img` · `.qr-mobile img`
- **กรอบที่มีเส้นอยู่แล้ว เปลี่ยนสีเส้นแทนการเพิ่มเส้นที่สอง**: `.media-frame` `.guide-step-media` `.article-thumb` `.riskdoc-figure` `.lh-feature-frame` `.author-box-img`
- **โลโก้ไม่มีขอบ**: `.brand-logo` `.brand-wordmark` `.lh-logo img` `.footer-logo img`
- **กรอบเส้นประรอรูป (`.img-slot`) ไม่โดน** เพราะเลือกที่ `.media-frame` ซึ่งเป็นช่องรูปจริงเท่านั้น ทั้งที่สองคลาสอยู่บน `figure.hud-frame` เดียวกัน
- วิธีตรวจว่าชั้นไหนมีเส้นอยู่แล้ว: scratchpad `gen/borders.js` ไล่ `document.images` แล้วพิมพ์ `borderTopWidth` ของตัวรูปกับพ่อขึ้นไป 3 ชั้น · **ต้องดูก่อนเขียนกฎ ไม่งั้นได้เส้นคู่** (บทเรียนเดิมข้อ 51)

### "ช่องว่าง" ที่เจ้าของเห็น ไม่ใช่บั๊กของเลย์เอาต์
- เจ้าของส่งภาพช่องดำใต้กล่องผู้เขียนแล้วถามว่าตรวจไม่ครอบคลุมหรือเปล่า · วัดบนหน้าจริงทั้ง 1440, 1920, 1280, 390 แล้ว **ไม่มีช่องว่างเลย** กล่องผู้เขียนจบที่ไหน แถบ LINE เริ่มที่นั่นพอดี
- ของจริงคือ **แถบ LINE ท้ายหน้า (`.cta-console`) มองไม่เห็นว่าเป็นแถบ** เพราะพื้นเป็น `--ink` `#0A0A0A` ซึ่งห่างจากพื้นหน้า `#070707` แค่ 3 ระดับ · ระยะเว้นด้านบน 72px จึงอ่านเป็นที่ว่างเปล่าจนกว่าจะถึงป้าย "ติดต่อทีมงาน" ซึ่งเป็น `footer_console_label` ของแถบนั้นเอง
- แก้เป็นแผ่น `--surface` เหมือนการ์ดทั้งเว็บ มีเส้นบน 1px และลดระยะเว้นเป็น `clamp(32px, 4vw, 52px)` · **ไม่ใส่เส้นล่าง เพราะ `.site-footer` มี `border-top` ของตัวเองอยู่แล้ว** จะกลายเป็นเส้นคู่
- **บทเรียน: อาการที่ผู้ใช้เรียกว่าช่องว่าง อาจไม่ใช่ระยะห่างผิด แต่เป็นพื้นที่มีของอยู่แต่มองไม่เห็น** ให้วัดก่อนว่ามีช่องจริงไหม แล้วค่อยดูว่าอะไรอยู่ตรงนั้น

### สีตอนชี้เมาส์ของชิปหมวดหมู่
- `.article-cat:hover` ตั้ง `background: rgba(var(--accent-2-rgb), 0.7)` ซึ่งหลังพลิก token เป็น **ขาวโปร่ง 70%** ขณะที่ `color` ยังเป็น `--primary-deep` `#EDEDED` จึงได้ตัวสว่างบนพื้นสว่าง อ่านไม่ออก
- เปลี่ยนเป็นพื้นใส ขอบและตัวอักษรเป็นเขียวธีม แบบเดียวกับปุ่มรองทั้งเว็บ
- **เป็นกรณีเดียวกับบทเรียนข้อ 48** ตอนพลิก token ทั้งชุด กฎที่เขียนไว้สำหรับพื้นสว่างจะกลายเป็นตรงข้าม · ที่ยังไม่ได้ไล่คือกฎ `:hover` ซึ่งไม่โผล่ในภาพหน้าจอปกติ **ต้องไล่ `:hover` และ `:focus` แยกต่างหากเสมอ**

### เครื่องมือใหม่
- `gen/gapscan.js` ไล่พี่น้องทุกชั้นในหน้า หาช่องว่างแนวตั้งที่ไม่มีอะไรอยู่เกิน 70px · ตรวจ 10 หน้า x 1440 และ 390 แล้ว ไม่เจอช่องว่างเลยสักหน้า
- `gen/probe.js` วัด top/bottom/margin/padding ของ selector ที่ระบุบนหน้าจริง
- `gen/borders.js` ดูว่าเส้นขอบรูปอยู่ชั้นไหน

## 57) เส้นที่ทับกันสนิท · รูโหว่ของตัวสแกนเอง (11 พ.ย. 2569 · deploy แล้ว v4.12.0)
- เจ้าของส่งภาพกล่องผู้เขียนที่ชิดเส้นของส่วนถัดไปแล้วถามว่า "มันควรเป็นแบบนี้หรอ" · ตอนแรกผมตีความเป็นเรื่องช่องว่าง (ข้อ 56) ซึ่งตอบไม่ตรงคำถาม · **ของจริงคือเส้นสองเส้นอยู่พิกัดเดียวกันเป๊ะ**
- **ตัวสแกนเส้นซ้อน `edges/scan.js` ที่เขียนไว้ตั้งแต่ข้อ 51 มีรูโหว่**: เงื่อนไข `if (d < 0.4 || d > 4) continue;` ตั้งใจกรองเส้นเดียวกันที่ถูกนับซ้ำ แต่ผลคือ **เส้นที่ห่างกัน 0px ถูกข้ามทั้งหมด** ซึ่งเป็นกรณีที่เห็นชัดที่สุดด้วยตา · ทำสำเนา `edges/scan0.js` ที่ใช้ `if (d > 4) continue;` แทน
- ไล่ 22 หน้า x 1440, 768, 390 ด้วยตัวใหม่ · **เจอ 3 รูปแบบ ทั้งหมดอยู่หน้าบทความและหน้ารวมบทความ** หน้าอื่นอีก 20 หน้าสะอาด

### สามจุดที่แก้ (CSS section 64 · ลบทั้งบล็อกแล้วกลับไปเป็นแบบ 63)
1. **กล่องผู้เขียนจบพอดีกับเส้นบนของแถบ LINE** · `article.single-article` มี `padding-bottom: 0` กล่องผู้เขียนมี `margin-bottom: 0` ขอบล่างของกล่องจึงอยู่ที่ y เดียวกับ `border-top` ของ `.cta-console` · เว้นท้ายบทความ `clamp(40px, 4vw, 64px)`
2. **แถบ LINE มีทั้ง `border-top` และ `border-bottom`** เส้นล่างทับกับเส้นบนของส่วนถัดไปเสมอ ไม่ว่าจะเป็นแถบบทความที่เกี่ยวข้องหรือฟุตเตอร์ (ทั้งคู่มี `border-top` ของตัวเอง) · ถอด `border-bottom` ออก เหลือเส้นเดียว
3. **ขอบเขียวของภาพในการ์ดบทความทับกรอบการ์ด 15 จุด** · เป็นของที่ผมเพิ่งเพิ่มเองใน v4.11.0 (ข้อ 56) ภาพในการ์ดเต็มความกว้าง ขอบซ้าย ขวา บน จึงตรงกับกรอบการ์ดพอดี · **ในการ์ด กรอบของการ์ดแยกภาพออกจากพื้นอยู่แล้ว** จึงเหลือเส้นเขียวไว้ด้านล่างภาพเพื่อคั่นกับข้อความ
4. ชิปหมวดหมู่เหนือหัวบทความ ตัวอักษรห่างเส้น 5px · เพิ่ม padding เป็น 7px

### ผลหลัง deploy
- `scan0.js` ทั้ง 4 หน้าที่แตะ x 1440, 768, 390: **เส้นซ้อน 0 แบบทุกค่า**
- รายการ "ชิดเส้น" ที่เหลือตรวจแล้วเป็นผลบวกลวงของตัวสแกนทั้งหมด: `article.post-card` รายงาน padding 0 ทุกด้าน แต่วัดจริงแล้วข้อความอยู่ใน `.post-card-body` ที่มี padding 22px และภาพเต็มความกว้างโดยตั้งใจ · `a ล่าง 6px`, `a.switch-row ล่าง 6px`, `div.sheet-row ล่าง 5px` เป็นแถวในฟุตเตอร์ที่เป็นแถวโดยตั้งใจ (ข้อ 51 เคยตัดสินไว้แล้ว)

### บทเรียน
- **เครื่องมือตรวจเองก็มีจุดบอด และจุดบอดนั้นมักอยู่ตรงเคสที่ชัดที่สุด** · ตัวกรองที่เขียนไว้กันผลบวกลวง (d < 0.4) กลายเป็นตัวกันไม่ให้เห็นบั๊กจริง · เวลาผู้ใช้ชี้จุดที่ตัวสแกนบอกว่าผ่าน ให้สงสัยตัวสแกนก่อน ไม่ใช่สงสัยผู้ใช้
- **ของที่เพิ่งเพิ่มเข้าไปต้องรันตัวตรวจซ้ำทันที** · ขอบเขียวใน v4.11.0 สร้างเส้นซ้อน 15 จุดในรอบเดียว ถ้ารัน `scan.js` หลัง deploy รอบนั้นก็เจอแล้ว (รุ่นเดิมจับได้เพราะภาพกับการ์ดห่างกัน 0px พอดี แต่รุ่นเดิมข้าม 0px จึงไม่เจอ)

## 58) ไอคอนไม่อยู่กลางกล่อง ช่องว่างที่น้อยเกินไป และรูปซ้ำใน 114 บทความ (11 พ.ย. 2569 · deploy แล้ว v4.14.0)
### ไอคอนไม่อยู่กลางกล่อง
- เจ้าของส่งภาพปุ่มค้นหาสีเขียวที่ลูกศรไม่อยู่กลาง แล้วสั่งให้ไล่เช็กรูปแบบนี้ทุกจุด และไล่เช็กช่องว่างทุกหน้าจอด้วย
- วัดบนเว็บจริง: กล่อง 46x52 ลูกศรห่างซ้าย 23.0 ห่างขวา 3.4 คือ **เบี้ยวไปทางขวา 19.6px**
- ต้นเหตุ: กฎปุ่มทั่วไปข้อ 14.1 (`min-height: 52px; padding: 10px 22px !important`) ครอบ `.error-search button` ด้วย ทั้งที่เป็นกล่องไอคอนล้วนที่ตรึงความกว้างไว้ 42px · padding ซ้ายขวารวม 44 บวกกรอบ 2 เกินความกว้าง กล่องถูกดันเป็น 46px และกรอบเนื้อหาด้านในเหลือกว้าง 0 ไปจอดชิดขอบในด้านซ้าย ลูกศรจึงไปเกาะตรงนั้น · แก้เป็น 52x52 padding 0 (CSS 65.1)
- จุดที่สองที่เจอจากการไล่เช็ก ไม่ได้อยู่ในภาพ: **ติ๊กถูกกับกากบาทในตารางเปรียบเทียบแพ็กเกจ** ไอคอนเป็น inline ที่นั่งบนเส้นฐาน จึงลอยสูงกว่ากลางช่อง 2.9px ขณะที่ตัวหนังสือในแถวเดียวกันสูงกว่ากลางช่อง 0.8px ต่างกัน 2.1px · แก้ด้วย `display: block; margin: 0 auto` ให้ช่อง (`vertical-align: middle`) จัดกลางเอง (65.2)

### ช่องว่างที่น้อยเกินไป · ช่องโหว่ของชุดตรวจ
- **ตัวตรวจช่องว่างที่มีอยู่หาแต่ช่องว่างที่มากเกินไป ไม่เคยมีตัวที่หาช่องว่างที่น้อยเกินไปเลย** จึงหลุดสองรอบติด · เขียน `edges/tight.js` สองกฎ
  1. หัวข้อที่ที่ว่างด้านบนน้อยกว่าด้านล่าง = ไปเกาะก้อนก่อนหน้าแทนที่จะเกาะเนื้อหาของตัวเอง
  2. ก้อนใหญ่สองก้อนที่ **ระยะหมึกถึงหมึก** ชิดกันน้อยกว่า 14px · วัดจากของที่ตาเห็นจริง (ตัวหนังสือ เส้นขอบ พื้น รูป) ไม่ใช่ขอบกล่อง
- จุดที่เจ้าของเจอ: หัวข้อ "คู่มือการใช้งานทั้งหมด" ในหน้ารวมบทความมีที่ว่างด้านบน **0px** ด้านล่าง 22px · `.guide-more-title` ตั้ง `margin: 0 0 22px` เพราะในหน้าคู่มือมันเป็นลูกตัวแรกของ section ที่มี padding อยู่แล้ว แต่ในหน้ารวมบทความมันต่อท้ายกริดการ์ดตรง ๆ · แก้ด้วย `:not(:first-child)` ซึ่งแยกสองกรณีได้โดยไม่ต้องเพิ่มคลาส (65.5)
- **กว่า tight.js จะเชื่อถือได้ต้องแก้ 5 รอบ** ผลบวกลวงที่ต้องกรองออกทีละแบบ: หัวข้อกับย่อหน้าของตัวเอง · ป้ายนำหน้าหัวข้อ (kicker, วันที่) · ภาพเต็มความกว้างที่ชิดขอบการ์ดโดยตั้งใจ · แถบสลับสีของ section ที่พื้นสองก้อนมาชนกัน (แก้ด้วยการไม่นับพื้นและกรอบของตัวก้อนเอง นับเฉพาะของลูก) · และ **`.sr-only` ที่เป็น `position: absolute` ทำให้วัดได้ติดลบ 15px** (แก้ด้วยการข้ามของที่หลุดจากสายเนื้อหา)
- คู่ที่เปิดดูด้วยตาแล้วและถือว่าตั้งใจ เก็บเป็นรายการ `REVIEWED` ในสคริปต์ ไม่ซ่อนทิ้ง: คำบรรยายใต้ภาพที่มีเส้นคั่นให้แล้ว · หัวขั้นกับเนื้อขั้นบน /go/ · โลโก้กับชื่อหน้าบน /go/ · เลข 404 กับหัวข้อใต้มัน

### การ์ดผลการค้นหา
- การ์ดที่เป็นเพจไม่มีภาพหน้าปก จึงเตี้ยกว่าการ์ดบทความในแถวเดียวกัน แต่กริดยืดให้สูงเท่ากัน และ `margin-top: auto` ดันบรรทัด "อ่านต่อ" ลงไปก้นการ์ด เหลือรูว่างคั่นกลาง **291px**
- แก้สองชั้น: `ea2000_post_card()` เติมคำโปรยจาก `_yoast_wpseo_metadesc` เมื่อ `the_excerpt()` ว่าง (เพจที่เนื้อหาอยู่ในธีม) · และ `body.search .posts-grid { align-items: start }` ให้การ์ดสูงเท่าเนื้อหาตัวเอง เพราะหน้านี้ช่องไฟเป็น 22px ไม่ใช่เส้น 1px แบบ `.guide-more` (65.3, 65.4)

### รูปหน้าปกซ้ำใน 114 บทความ
- ภาพหน้าปกขึ้นสองครั้ง: จาก featured image ที่หัวบทความ และเป็น `<figure>` ตัวแรกในเนื้อหา เป็น media เดียวกัน · เกิดจากสายพานผลิตที่ใส่ปกเป็น FIG1 ทั้งที่ตั้งเป็นภาพหน้าปกไปแล้ว
- `edges/dedupe-cover.js` ลบ figure ตัวแรกออกจากเนื้อหา **เฉพาะเมื่อรูปในนั้นคือ featured image จริง ๆ** · ทดสอบกับบทที่เผยแพร่แล้วหนึ่งบทและบทที่ตั้งเวลาหนึ่งบทก่อน แล้วค่อยรันทั้งชุด · **ตรวจทุกบทว่า status กับวันที่ไม่เปลี่ยน** บทที่ตั้งเวลายังเป็น `future` วันเดิมครบ 114 บท · สคริปต์ทำซ้ำได้ ไม่ลบรูปอื่น

### บทเรียน
- **สคริปต์ตรวจใช้เลขกันแคชแบบคงที่ไม่ได้** รันซ้ำรอบสองจะไปเจอหน้าที่โฮสต์แคชไว้จากรอบแรก แล้วรายงานว่า "ยังไม่หาย" ทั้งที่แก้แล้ว · ใส่ `RUNSALT = Date.now() % 1000000` ให้ทุกตัวแล้ว
- **เครื่องมือตรวจต้องวัดสิ่งที่ตาเห็น ไม่ใช่ขอบกล่อง** กล่องสองก้อนที่ชิดกัน 0px อาจมี padding ด้านในรวมกัน 36px ซึ่งไม่ใช่ปัญหา
- เครื่องมือใหม่: `edges/icon.js` (ไอคอนในกล่องไอคอนล้วน + padding เกินขนาดกล่อง) · `edges/inline.js` (ไอคอนที่อยู่บรรทัดเดียวกับตัวหนังสือ เทียบกับหมึกของบรรทัดนั้น) · `edges/tight.js` · `edges/gap2.js` (MIN 100px) · `edges/figcheck.js`

## 59) หน้าแรกเพิ่มเนื้อหาให้แน่นขึ้นเพื่อ SEO (11 พ.ย. 2569 · deploy แล้ว v4.15.2)
- เจ้าของถามว่าหน้าแรกเนื้อหาน้อยไปไหม แล้วย้ำว่า "ให้ seo มันแน่นมากที่สุด"
- วัดก่อนแก้: 10 บล็อก 10,428 ตัวอักษร **แต่ลิงก์ออกไปหน้าอื่นแค่ 5 หน้า และไม่ลิงก์ไปบทความสักบทจาก 114 บท ไม่ลิงก์ไปคู่มือสักหน้าจาก 13 หน้า** นั่นคือช่องว่างจริง ไม่ใช่จำนวนตัวอักษร

| | เดิม | ใหม่ |
|---|---|---|
| บล็อก | 10 | 14 |
| ตัวอักษร | 10,428 | 15,862 |
| H2 | 9 | 13 |
| H3 | 22 | 34 |
| ลิงก์ภายใน | 5 | 16 |
| คำถามใน FAQPage | 8 | 14 |

### บล็อกใหม่ 4 บล็อก
- **`#fit` เหมาะกับใคร / ยังไม่เหมาะกับใคร** · ใช้คีย์ `fit_*` เดิมที่ยังอยู่ใน defaults ตั้งแต่ก่อนรื้อหน้าแรก v2 เพิ่มแค่ `fit_kicker` · วางหลังบล็อกปัญหา
- **`#start` เริ่มต้นใช้งานจริง** · สิ่งที่ต้องมี 4 ข้อ แล้ว 6 ขั้นเรียงตามที่คนทำจริง **แต่ละขั้นลิงก์เข้าคู่มือของตัวเอง** (open-mt5-account, mt5-login, deposit, vps-windows, how-to-install, tools) · ลิงก์ขึ้นเฉพาะหน้าที่ publish
- **`#terms` คำศัพท์ 10 คำ** Lot, Spread, Pip, Swap, Leverage, Margin Level, Margin Call, Drawdown, Slippage, Stop Out · `ea2000_term_link()` ตรวจ `get_page_by_path( slug, OBJECT, 'post' )` แล้ว**คืนลิงก์เฉพาะบทความที่ publish แล้ว** บทความชุดคำศัพท์ตั้งเวลาไว้เดือนธันวาคม ลิงก์จะขึ้นเองเมื่อถึงวัน ไม่มีลิงก์เสียระหว่างทาง
- **`#read` บทความล่าสุด** `WP_Query` เอาเฉพาะ `post_status => publish` จำนวนตาม `read_count` (ค่าเริ่มต้น 6 ตอนนี้มี 5 บทจึงขึ้น 5)
- ขยายของเดิม: ปัญหาที่คนเทรดมือเจอ 4 เป็น 6 ข้อ · FAQ 8 เป็น 14 ข้อ (ลูปใน `front-page.php`, `ea2000_faq_schema()` และ customizer ขยายเป็น 16 ช่อง)

### กติกาที่ยึดตอนเขียน
- หัวข้อย่อยของบล็อกคำศัพท์ใช้แค่ชื่อคำ **ไม่ใช้รูป "Lot คืออะไร"** เพราะนั่นเป็นคำหลักของบทความ ถ้าหน้าแรกจับด้วยจะแย่งอันดับกันเอง · หน้าแรกทำหน้าที่หน้าศูนย์กลางที่ตอบสั้นแล้วส่งต่อ
- ไม่มีตัวเลขผลทดสอบ ไม่มีคำรับประกัน ไม่แตะเรื่องกลยุทธ์ · คำตอบ "ต้องมีทุนเท่าไร" ไม่ระบุตัวเลข บอกว่าขึ้นกับชนิดบัญชีและความเสี่ยงที่รับได้ แล้วส่งไปเครื่องคำนวณ
- CSS section 66 (ลบทั้งบล็อกแล้วบล็อกใหม่ไม่มีสไตล์ แต่เนื้อหายังอ่านได้ตามลำดับ)

### บั๊กที่เจอ
- **คลาส `.term` ที่ตั้งให้บล็อกคำศัพท์ชนกับเทอร์มินัลจำลองบนหน้าแรก** ซึ่ง section 51 สั่ง `display: none` ไว้ · คำศัพท์ทั้ง 10 คำไม่ขึ้นเลยตอน deploy รอบแรก (`.gloss-grid` สูง 2px เหลือแต่กรอบ) · เปลี่ยนเป็น `.gloss*` · **ต้อง grep ชื่อคลาสก่อนตั้งทุกครั้ง** (บทเรียนเดียวกับ `.guide-step` ในข้อ 26)
- `.start-step-n` เป็นลูกกริดจึงยืดเต็มความสูงแถว กล่องสูง 143px ทั้งที่ตัวเลขสูง 27px · ใส่ `align-self: start`
- ผลตรวจหลัง deploy ทั้ง 21 หน้า x 1440 และ 390: เส้นซ้อน 0 · ช่องว่างมากเกินและน้อยเกิน 0 · ไอคอนเบี้ยว 0 · คอนทราสต์และหน้าล้นแนวนอนสะอาด (เหลือธงเดียวที่เป็นช่องตารางซึ่งสูงตามเซลล์ข้างที่ข้อความยาวกว่า ไม่ใช่บั๊ก)

## 60) ปกธีม คำโปรยเว็บ และปิดการแสดงความเห็น (11 พ.ย. 2569 · deploy แล้ว v4.16.0)
### ปกธีม `screenshot.png`
- เจ้าของบอกว่าปกธีม "ให้ดูเป็นปกมากกว่านี้" · ของเดิมคือภาพจับหน้าจอ hero ดิบ ๆ ที่**ล้าสมัยไปแล้ว** (ยังมีกล่องสินค้าและปุ่มลอย LINE ซึ่งถูกถอดไปตั้งแต่ v4.0.1) และขอบล่างถูกตัดกลางคัน
- ปกใหม่ 1200x900 ออกแบบจากภาษาของธีมเอง: ป้าย `WORDPRESS THEME` มีขีดเขียวนำ · wordmark 600px · เส้นคาดบางที่มีช่วงหัวเป็นเขียว · บรรทัดเดียวว่าธีมนี้ทำอะไร · แถบกราฟแท่งเทียน 204px · แถบสเปก 4 ช่องพื้น `--surface` คั่นด้วยเส้น 1px
- **แท่งเทียนใช้เมล็ดคงที่และเดินแบบดึงกลับค่ากลาง** (`d=(rnd()-0.5)*20-o*0.16`) เพื่อให้ไม่มีทิศทางขึ้น · รอบแรกเดินแบบสุ่มล้วนแล้วกราฟไต่ขึ้นทั้งแผง ซึ่งจะอ่านเป็นเส้นผลกำไร ขัดข้อ 3.2 · มีป้ายในภาพว่า "ภาพประกอบ · ไม่ใช่ราคาจริง" และกราฟจางหายไปทางขวาเพื่อเปิดที่ให้ป้าย
- ต้นฉบับอยู่ scratchpad `cover/cover.html` (ฝังฟอนต์ของธีมผ่าน `file://`) · เรนเดอร์ด้วย headless Chrome · **ต้องตั้ง `--user-data-dir` เป็นโฟลเดอร์ใหม่และใส่ path เต็มให้ `--screenshot`** ไม่งั้นได้ `Access is denied` แล้วไม่มีไฟล์ออกมา (ซ้ำบทเรียนข้อ 34)
- ไฟล์จาก 239 KB เหลือ 148 KB · ตรวจแล้วว่าเว็บเสิร์ฟไฟล์ใหม่จริง (`cf-cache MISS`) · ชื่อไฟล์เปลี่ยนไม่ได้เพราะ WordPress บังคับชื่อ `screenshot.png`

### คำโปรยเว็บ (tagline)
- เจ้าของสั่งให้ช่องนี้ "เป็นเอกลักษณ์มากกว่านี้" · เดิม `ระบบเทรดอัตโนมัติสำหรับ MetaTrader 5` ซึ่งบอกแค่หมวดหมู่สินค้า
- ใหม่: **`เทรดตามกฎที่ตั้งไว้ ไม่ใช่ตามอารมณ์ · EA สำหรับ MetaTrader 5`** · นำด้วยจุดยืนที่ต่างจากคู่แข่ง แล้วตามด้วยคำที่บอกว่าเป็นอะไร · ตรงกับข้อความใน hero ที่ใช้อยู่แล้ว และไม่แตะเรื่องกลยุทธ์หรือคำรับประกัน
- **ธีมไม่ได้พิมพ์ tagline ที่ไหนเลย** (ไม่มี `bloginfo('description')` ในโค้ด) แต่ Yoast ดึงไปใส่ `description` ของ node `WebSite` ในข้อมูลโครงสร้าง · ตรวจบนหน้าแรกแล้วขึ้นค่าใหม่จริง · นอกจากนั้นใช้ใน RSS และ wp-admin

### ปิดการแสดงความเห็นทั้งเว็บ
- ตั้งผ่าน `wp/v2/settings`: `default_comment_status` และ `default_ping_status` = `closed` (เดิมเป็นค่าว่าง ซึ่ง REST อ่านออกมาเป็น `null` · ผลเหมือนกันแต่ค่าว่างอ่านยาก)
- ไล่ตรวจ **114 เรื่องและ 21 หน้า ทุกรายการปิดอยู่แล้ว** ทั้ง comment และ ping ไม่มีรายการไหนต้องแก้ · ความเห็นบนเว็บมี 0 รายการ
- **ธีมปิดซ้ำอีกชั้นอยู่ก่อนแล้ว** ที่ `functions.php` หมวด security hardening: `remove_post_type_support` ของ post และ page · `comments_open`, `pings_open` คืน false · `comments_array` คืนอาร์เรย์ว่าง · ซ่อนเมนูความเห็นใน wp-admin และ admin bar · ต่อให้โพสต์ไหนตั้งค่าผิด หน้าเว็บก็ไม่มีฟอร์มให้พิมพ์
- ช่อง "ส่งแจ้งเตือนเมื่อมีบล็อกที่ลิงก์มาจากเรื่อง" (`default_pingback_flag`) ยังติ๊กอยู่ · ไม่ใช่เรื่องความเห็น เป็นการยิงแจ้งเตือนออกไปหาเว็บที่เราลิงก์ถึงตอนเผยแพร่ · **ไม่มีใน REST settings** ถ้าจะปิดต้องเอาติ๊กออกเองในหน้าเดียวกัน
