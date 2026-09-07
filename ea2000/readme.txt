=== EA2000 · WordPress Theme ===

ธีม WordPress แบบ custom สำหรับเว็บ Landing ของ EA2000
EA (Expert Advisor) สำหรับ MetaTrader 5 · เป้าหมายของหน้าเว็บคือให้ผู้สนใจทัก LINE

ตัวธีมอยู่ในโฟลเดอร์ ea2000/ และ deploy ผ่าน WP Pusher โดยตั้ง
Repository subdirectory = ea2000

ไม่มี build step: แก้ PHP, CSS, JS แล้วใช้งานได้ทันที
ต้องการ WordPress 6.0 ขึ้นไป · แนะนำ PHP 8.1 ขึ้นไป

----------------------------------------------------------------
1) ภาพรวมการแก้ไข
----------------------------------------------------------------

เนื้อหาเกือบทั้งหมดของเว็บแก้ผ่าน WordPress Customizer ไม่ต้องแก้โค้ด

เข้าไปที่:
รูปแบบ > ปรับแต่ง > EA2000 · ตั้งค่าหน้าเว็บ

หลักการทำงานของ setting:
- ค่าเริ่มต้นทุกค่าอยู่ใน ea2000_defaults() ใน functions.php
- inc/customizer.php วนสร้าง section / setting / control จากรายการเดียวกัน
- template อ่านค่าด้วย ea2000_mod( 'key' ) เท่านั้น
  ห้าม hardcode ข้อความ ลิงก์ ราคา หรือตัวเลขใน template
- ค่าที่ขึ้นต้นด้วย "ระบุ" หรือ "เช่น " ถือเป็น placeholder (ea2000_is_placeholder())
  และจะไม่แสดงบนหน้าเว็บจนกว่าจะกรอกค่าจริง
- ลิงก์ที่เว้นว่างหรือเป็น # จะไม่แสดงปุ่ม / ไอคอนนั้นเลย
- จะเพิ่ม setting ใหม่ ให้เพิ่ม default ใน ea2000_defaults() คู่กับ control ใน inc/customizer.php

สิ่งที่แก้ได้จาก Customizer:
- ชื่อแบรนด์และ tagline (brand_name / brand_tagline) ที่ใช้ใน header และ footer
- ลิงก์ LINE OA และช่องทางติดต่อ / โซเชียล
- เปิด/ปิด section หน้าแรก
- ข้อความ Hero, About, Features, FAQ, Footer
- รูปภาพ Hero, Gallery, Install guide, Risk page และรูป OG สำหรับแชร์
- ราคาและแพ็กเกจ
- ผล Backtest / Forward Test
- เมนูลัดมือถือ
- Language switcher
- Cookie consent และ tracking (GA4 / Pixel)
- หน้า Link Hub สำหรับยิงแอด

สำคัญ:
- ห้ามแต่งรีวิวปลอม (show_reviews ปิดอยู่จนกว่าจะมีรีวิวจริง)
- ห้ามใส่ตัวเลขผลทดสอบสมมติ ปล่อยเป็น placeholder ให้เจ้าของกรอกผลจริง
- ห้ามลบหรือลดทอนคำเตือนความเสี่ยง / disclaimer
- ทุก output ต้อง escape ด้วย esc_html / esc_url / esc_attr
- ตัวอักษรอังกฤษถูกทำเป็นตัวพิมพ์ใหญ่ด้วย CSS (text-transform) ยกเว้น .keep-case
  จึงไม่ต้องพิมพ์ตัวพิมพ์ใหญ่เองในเนื้อหา
- ห้ามใช้ em dash / en dash ในโค้ดและเนื้อหา ใช้ · หรือ : แทน

----------------------------------------------------------------
2) หน้าเว็บหลักและ Template ที่ต้องใช้
----------------------------------------------------------------

สร้างหน้าใน WordPress แล้วเลือก Template ให้ตรงกับ slug ต่อไปนี้

หน้าแรก:
- slug: หน้าแรกของเว็บ
- template: ไม่ต้องเลือก หรือใช้ค่าเริ่มต้นของธีม
- render โดย front-page.php
- ใช้เป็นหน้า production หลัก

ผล Backtest:
- slug: backtest
- template: EA2000 · หน้า Backtest
- แก้เนื้อหาใน Customizer หมวดหน้า Backtest

ผล Forward Test:
- slug: forward-test
- template: EA2000 · หน้า Forward Test
- แก้เนื้อหาใน Customizer หมวดหน้า Forward Test

แพ็กเกจ:
- slug: pricing
- template: EA2000 · หน้า Pricing
- แก้แพ็กเกจและราคาใน Customizer

วิธีติดตั้ง:
- slug: how-to-install
- template: EA2000 · หน้า How to Install
- ใช้รูปคู่มือใน assets/img/install/ เป็นค่าเริ่มต้น (เป็นรูป placeholder ต้องเปลี่ยนเป็นรูปจริง)

คำเตือนความเสี่ยง:
- slug: risk-disclosure
- template: EA2000 · หน้า Risk Disclosure
- ต้องคงหน้านี้ไว้เสมอ และมีลิงก์จาก footer / บทความ

บทความทั้งหมด:
- slug: articles
- template: EA2000 · หน้ารวมบทความ
- ใช้สำหรับ SEO content / blog

หน้า Link Hub สำหรับยิงแอด:
- slug: go
- template: EA2000 · หน้า Link Hub (ยิงแอด)
- เป็นหน้า standalone ไม่มี header/footer เพื่อโฟกัส CTA ทัก LINE
- แก้ข้อความ ปุ่ม รูปโชว์ และลิงก์ได้ใน Customizer หมวดหน้า Link Hub
- ปุ่มเสริม (สมัครบัญชี คู่มือ โหลด MT5 กลุ่มแชท) จะแสดงเฉพาะเมื่อกรอก url แล้ว
- การ์ดดาวน์โหลด (links_fast_enabled) เปิดอยู่เป็นค่าเริ่มต้น ใช้รูป
  assets/img/link-download-ea2000.png ถ้า links_fast_url ว่าง การ์ดจะแสดงแบบไม่มีลิงก์
- ห้ามเก็บไฟล์ EA (.zip / .ex5) ไว้ใน repo นี้ (.gitignore กันไว้แล้ว)
  ถ้าต้องการให้โหลดไฟล์ ให้ใส่ลิงก์ภายนอกใน Customizer แทน

เมนูหลักที่วางแผนไว้: หน้าแรก · Backtest · Forward Test · แพ็กเกจ · วิธีติดตั้ง · บทความ
ไม่มีเมนูเปิดบัญชีโบรกเกอร์หรือคู่มือของแบรนด์อื่น

----------------------------------------------------------------
3) กติกา Theme vs Elementor
----------------------------------------------------------------

ธีมนี้รองรับ Elementor แต่ต้องแยกบทบาทให้ชัด

ใช้ Theme template สำหรับ:
- หน้าแรก production หลัก
- Backtest
- Forward Test
- Pricing
- How to Install
- Risk Disclosure
- Articles / Blog
- Link Hub /go/

เหตุผล:
- เนื้อหาถูกผูกกับ Customizer แล้ว
- SEO, schema, disclaimer, risk warning, footer, mobile nav ถูกคุมโดยธีม
- ลดโอกาส Elementor ทับ template แล้วเนื้อหาหาย

ใช้ Elementor สำหรับ:
- หน้า draft หรือ landing เฉพาะแคมเปญ
- หน้า sales page ที่ต้องลากวางเองจริง ๆ
- ทดลองดีไซน์ก่อนตัดสินใจย้ายเข้าธีม

ถ้าจะใช้ Elementor:
- เลือก Template แบบ Elementor ของธีม (Full Width หรือ Canvas)
- ห้ามเปิดหน้าที่ใช้ Template ของธีม (Backtest / Forward / Pricing / Install / Risk / Link Hub)
  ด้วย Elementor ถ้ายังต้องการให้ template ธีมแสดงข้อมูลจาก Customizer
- ลิงก์ LINE ในหน้า Elementor ต้องใส่เอง ธีมไม่ได้เติมให้

สรุปง่าย ๆ:
- เว็บหลัก = Theme custom
- หน้าแก้ไวหรือแคมเปญเฉพาะกิจ = Elementor
- หน้า /go/ ยิงแอด = Link Hub template

----------------------------------------------------------------
4) สิ่งที่ต้องตั้งค่าก่อนใช้จริง
----------------------------------------------------------------

1. ใส่ LINE OA
   Customizer > 1) แบรนด์และช่องทางติดต่อ
   ค่า line_url ว่างอยู่เป็นค่าเริ่มต้น และถูกใช้กับ CTA หลายจุดทั่วเว็บ
   โซเชียล (Facebook / Instagram / TikTok / YouTube) ก็ว่างอยู่ ใส่เฉพาะที่มีจริง

2. ตรวจชื่อแบรนด์
   brand_name / brand_tagline ใช้ใน header และ footer
   (schema และ og:site_name ใช้ชื่อเว็บจาก Settings > General ให้ตั้งชื่อเว็บเป็น EA2000 ด้วย)

3. ตรวจเมนูหลัก
   Appearance > Menus
   เมนูแนะนำ:
   - หน้าแรก
   - ผลทดสอบ
     - Backtest
     - Forward Test
   - แพ็กเกจ
   - วิธีติดตั้ง
   - บทความ
   ไม่จำเป็นต้องใส่ "ความเสี่ยง" ในเมนูหลัก แต่ต้องมีใน footer

4. กรอกผลทดสอบจริง
   Backtest / Forward Test ยังเป็นค่า placeholder เช่น "ระบุ..." หรือ "เช่น ..."
   ให้กรอกเฉพาะข้อมูลจริงเท่านั้น

5. ตั้งราคาและแพ็กเกจ
   ถ้ายังไม่อยากแสดงราคา ให้ใช้โหมดสอบถามทาง LINE

6. ตรวจรูปภาพ (ทั้งหมดเป็น placeholder ต้องเปลี่ยน)
   - โลโก้ assets/img/logo.png
   - Hero image
   - Gallery
   - Install guide
   - รูปการ์ดดาวน์โหลดหน้า /go/
   - OG default image (assets/img/og-default.png) สำหรับแชร์ LINE/Facebook

7. ตรวจคำเตือนความเสี่ยง
   คงข้อความไว้ครบ โดยเฉพาะหน้า Risk Disclosure และ disclaimer ใต้ผลทดสอบ
   วันที่อัปเดตของหน้า Risk ยังเป็น placeholder ต้องกรอกเอง

8. SEO
   search_console_verify ว่างอยู่ ใส่เมื่อสร้าง property ใหม่ของ EA2000 แล้ว
   ถ้าใช้ Site Kit หรือ Yoast ยืนยันแทน ให้เว้นว่างไว้ เพื่อไม่ให้มีแท็กซ้ำ
   noindex ยังเปิดอยู่ทั้งเว็บ เอาออกเมื่อเนื้อหาพร้อม

----------------------------------------------------------------
5) ปุ่ม LINE และมือถือ
----------------------------------------------------------------

ค่าเริ่มต้น:
- ปุ่ม LINE ลอยแบบยาว (float-line) ปิดอยู่
- ถ้ามี line_url และปิด float-line ธีมจะแสดงปุ่ม LINE วงกลมเฉพาะ desktop
- ถ้า line_url ว่าง จะไม่แสดงปุ่ม LINE ใด ๆ
- มือถือใช้เมนูลัดด้านล่างแทน และซ่อนปุ่มวงกลม

เมนูลัดมือถือแก้ได้ที่:
Customizer > เมนูลัดมือถือด้านล่าง

ค่าที่แนะนำ:
- หน้าแรก
- ผลทดสอบ
- แพ็กเกจ
- วิธีติดตั้ง
- ทัก LINE

----------------------------------------------------------------
6) ภาษาและ GTranslate
----------------------------------------------------------------

ธีมมีตำแหน่ง language switcher ใน header

ถ้าติดตั้ง GTranslate:
- ธีมจะใช้ shortcode [gtranslate] อัตโนมัติ
- ดีสำหรับแปลหน้าเว็บแบบฟรีและเร็ว
- SEO หลักยังควรเน้นภาษาไทย

ถ้าไม่มีปลั๊กอินภาษา:
- ธีมจะแสดง fallback language switcher จากค่าใน Customizer
- fallback เป็น UI เริ่มต้นเท่านั้น ไม่ได้แปลทั้งเว็บจริง

หน้า Link Hub จะซ่อน widget แปลภาษา เพื่อไม่ให้ทับดีไซน์หน้าแอด

----------------------------------------------------------------
7) บทความและ SEO
----------------------------------------------------------------

ธีมรองรับบทความ SEO:
- single.php สำหรับบทความเดี่ยว
- index.php / search.php สำหรับ archive และ search
- template-blog.php สำหรับหน้า articles

ฟีเจอร์บทความ:
- Table of contents จาก H2/H3
- Reading progress
- Share to LINE / Facebook / copy link
- Related posts
- Load more posts ด้วย AJAX
- Risk disclaimer ใต้บทความ

SEO ที่ธีมช่วยให้:
- Open Graph / Twitter card
- Organization / WebSite / FAQ schema
- BlogPosting schema
- Breadcrumb schema
- archive canonical
- robots noindex สำหรับ search/author/date

ถ้าติดตั้ง Yoast, Rank Math หรือ SEOPress:
- ธีมจะปิด OG/schema บางส่วนเองเพื่อลด tag ซ้ำ

บทความต้องเขียนใหม่สำหรับ EA2000 เท่านั้น ห้ามคัดลอกบทความจากเว็บอื่น (duplicate content)

----------------------------------------------------------------
8) รีวิวและผลทดสอบ
----------------------------------------------------------------

รีวิว:
- show_reviews ปิดเป็น default และข้อความตัวอย่างว่างอยู่
- เปิดเมื่อมีรีวิวจริงเท่านั้น
- ก่อนเปิด ต้องกรอกรีวิวจริงทั้งหมดใน Customizer

ผลทดสอบ:
- ค่า placeholder เช่น "ระบุ..." หรือ "เช่น ..." หมายถึงยังไม่กรอกข้อมูลจริง
- ห้ามใส่ตัวเลขเดาเอง
- ถ้ามีผลจริง ควรแนบภาพหรือลิงก์แหล่งข้อมูลภายนอกที่ตรวจสอบได้
- ต้องคง disclaimer ใต้ section ผลทดสอบไว้

----------------------------------------------------------------
9) Deploy flow
----------------------------------------------------------------

Flow:

แก้โค้ดในเครื่อง
> lint PHP
> git commit
> git push origin main
> WP Pusher deploy theme จาก subdirectory ea2000

ก่อน push ทุกครั้ง:
- git remote -v ต้องเป็น repo ของ EA2000 เท่านั้น
- Push-to-Deploy ปิดไว้จนกว่าจะตรวจรอบแรกผ่าน จากนั้นค่อยเปิด

ถ้าไม่อัปเดต:
- เข้า WP Pusher > Themes > Update theme
- เคลียร์ cache ถ้าใช้ cache plugin/CDN

ก่อน commit:
- รัน php -l กับไฟล์ PHP ที่แก้
- รัน php tests/link-hub-downloads.php (ต้องผ่านทุกข้อ)
- อย่า commit ไฟล์ลับ local config หรือไฟล์ EA
- .claude/ ถูก ignore แล้ว เป็น config local ของ Claude Code เท่านั้น

----------------------------------------------------------------
10) ไฟล์สำคัญ
----------------------------------------------------------------

functions.php:
- theme setup และ enqueue ฟอนต์ / CSS / JS
- defaults ทั้งหมดใน ea2000_defaults()
- helper functions (ea2000_mod, ea2000_is_placeholder, ea2000_link_url)
- SEO/schema
- REST namespace ea2000/v1 (authcheck, mods)
- AJAX load more

inc/customizer.php:
- register Customizer sections/settings/controls
- เพิ่ม setting ใหม่ที่นี่คู่กับ defaults ใน functions.php

front-page.php:
- หน้าแรก custom theme

header.php / footer.php:
- header, menu, language switcher, footer, mobile nav, LINE buttons, cookie bar
- ชื่อแบรนด์อ่านจาก brand_name / brand_tagline

style.css:
- theme header ต้องอยู่บนสุด
- design system (สีใน :root, ฟอนต์) และ responsive layout ทั้งหมด

assets/js/main.js:
- mobile nav
- submenu
- reveal animation
- load more
- cookie consent / tracking loader

template-links.php:
- หน้า Link Hub สำหรับ /go/

template-elementor-*.php:
- หน้า Elementor เฉพาะกรณีที่ต้องการลากวางเอง

tests/link-hub-downloads.php:
- สคริปต์ตรวจหน้า Link Hub ว่าไม่มีร่องรอยธีมต้นทางและไม่มีไฟล์ EA ในธีม
- รันด้วย php tests/link-hub-downloads.php จาก root ของ repo

----------------------------------------------------------------
11) เวอร์ชันและ cache
----------------------------------------------------------------

Theme header เป็น Version 1.0.0
ธีมใช้ filemtime() เป็น version ของ style.css และ main.js
ดังนั้นแก้ CSS/JS แล้ว browser จะได้ query string ใหม่อัตโนมัติ

ไม่จำเป็นต้องเพิ่มเลขเวอร์ชันทุกครั้งที่แก้เล็ก ๆ
ถ้าจะเพิ่มเวอร์ชัน ให้ทำเป็นรอบ release จริง เช่น 1.1.0, 1.2.0

----------------------------------------------------------------
12) สถานะที่ควรรู้ตอนนี้
----------------------------------------------------------------

- หน้าแรก production ใช้ Theme custom
- /go/ ใช้ Link Hub template
- /articles/ ใช้ template รวมบทความ
- ไม่มี product template ไม่ต้องสร้างหน้า product
- Risk Disclosure ต้องมีอยู่และไม่ควรถูกซ่อนจาก footer/legal links
- โลโก้ สี รูปภาพ และลิงก์ทั้งหมดยังเป็น placeholder รอเจ้าของยืนยัน

----------------------------------------------------------------
13) Changelog
----------------------------------------------------------------

1.0.0
- เวอร์ชันแรกของธีม EA2000
- โครงสร้าง Customizer-driven ครบทุก section
- Template: Backtest, Forward Test, Pricing, How to Install, Risk Disclosure, Articles, Link Hub, Elementor
- ค่าเริ่มต้นทั้งหมดเป็นของ EA2000 · ลิงก์ติดต่อ โซเชียล และผลทดสอบเว้นว่าง / placeholder รอเจ้าของกรอก
- รีวิวปิดอยู่ · disclaimer และ risk warning ครบ

จบคู่มือปัจจุบัน
