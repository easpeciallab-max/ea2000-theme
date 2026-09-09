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
