# EA2000 · หน้าแรก v2 "Control Room" และแถบท้ายเว็บใหม่ · Build spec

สถานะ: definitive · 9 กันยายน 2026 · design lead sign-off · อ้างอิงธีมที่ commit 29f6c8f (`ea2000/`)

เอกสารนี้เขียนให้ผู้ลงมือ 3 คนทำงานขนานกันโดยไม่ต้องคุยกัน:

| ผู้ลงมือ | ไฟล์ที่เป็นเจ้าของ | สิ่งที่ห้ามแตะ |
|---|---|---|
| **A** | `front-page.php`, `functions.php` (defaults, preload, enqueue home.js, helper ใหม่), `inc/customizer.php` | `style.css`, `assets/js/*`, `footer.php` |
| **B** | `style.css` (tokens, section 40, section 41, การถอนกฎเก่า, header hover) | ไฟล์ PHP ทุกไฟล์, JS |
| **C** | `assets/js/main.js`, `assets/js/home.js` (ไฟล์ใหม่), `footer.php` | `front-page.php`, `functions.php`, `style.css` |

สัญญาที่ทุกคนต้องยึด (contract) อยู่ในข้อ 0 · ใครเปลี่ยนชื่อ class, data attribute, key หรือ API โดยไม่แก้เอกสารนี้ = งานพัง

กฎตายตัวที่สืบทอดจาก CLAUDE.md: ไม่มีตัวเลขผลเทรด ไม่มีรีวิว ไม่มี testimonial · ห้ามพูดถึงทองหรือ XAUUSD · ห้ามอธิบายกลยุทธ์ (ห้ามอ้างทั้ง "มี" และ "ไม่มี" martingale / grid / stop loss) · ห้ามลดทอนคำเตือนความเสี่ยง · เนื้อหาไทย · ห้ามใช้ em dash / en dash ทุกที่ (ใช้ `·` หรือ `:`) · ทุกข้อความและรูปที่มองเห็นต้องเป็น setting · escape ทุก output · ไม่มี JS ภายนอก ไม่มี build step · งบ JS รวมไม่เกิน +15 KB raw · เคารพ `prefers-reduced-motion` · ไม่มี JS ก็ต้องอ่านเนื้อหาได้ครบ · hero เป็นมิตรกับ LCP · ไม่มี horizontal overflow ที่ 360px · header เขียวเข้มคงเดิม (แก้เฉพาะ hover marker) · footer สร้างใหม่ทั้งหมด

---

## 0) สัญญาร่วม (อ่านก่อนเริ่ม ทั้ง A B C)

### 0.1 โครง DOM ระดับหน้า
- `front-page.php` พิมพ์ `<main id="main" class="home-v3">` · ทุกกฎ CSS ของหน้าแรก v3 scope ด้วย `.home-v3` · ไม่มี `.section`, `.section-alt`, `.sec-head`, `.kicker`, `.badge`, `.card`, `.grid-3`, `.grid-4`, `.reveal`, `.ember`, `.cta`, `.risk-box`, `.price-flag`, `.btn` บนหน้าแรกอีกต่อไป (ยกเว้น `.sr-only`, `.container`, `.icon`, `.keep-case`, `.sec-anchor`)
- `footer.php` พิมพ์ `<footer class="site-footer console" id="contact">` · scope ด้วย `.site-footer.console` · ไม่มี `.footer-cta`, `.footer-main`, `.footer-brand`, `.footer-prep`, `.footer-bottom`, `.footer-legal`, `.footer-social`, `.footer-tagline`
- บล็อกหน้าแรกทุกบล็อกหลัง hero = `<section class="ch ch-{name}" id="{id}" data-chapter="NN" data-chapter-label="{ป้ายบท}">` · hero = `<section class="boot" id="hero" data-chapter="00">`
- หัวบททุกบทใช้โครงเดียวกัน (ข้อ 0.4) · ไม่มี kicker chip ไม่มีเส้นใต้ไล่สี

### 0.2 คลาสร่วม (B ต้องนิยาม · A และ C ใช้ได้เท่านั้น ห้ามคิดคลาสใหม่นอกรายการในเอกสารนี้)
| คลาส | ความหมาย |
|---|---|
| `.mono` | ฟอนต์ monospace (`--font-mono`) tabular-nums letter-spacing .04em · ใช้กับดัชนี ป้ายกำกับ prompt |
| `.keep-case` | ยกเว้นกฎ uppercase ของ body (มีอยู่แล้ว section 27) · ต้องใส่ทุกที่ที่มี Latin ที่ต้องคงตัวพิมพ์: `ea2000@mt5:~$`, `EA2000`, `MetaTrader 5`, ราคา `Free` · รวม footer `dl.sheet--ink` (spec sheet) และ `p.status-text` (ข้อ 4) · ปุ่มยังใช้ uppercase ตามปกติของเว็บ |
| `.watch` | hook ให้ IntersectionObserver ตัวเดิมใน main.js เติม `.in` · **ไม่มีผลทางสายตาในตัวเอง** (ต่างจาก `.reveal` ที่ fade-up) · ใช้เป็นสถานะสำหรับ strike-through, wipe, HUD bracket, signal line, watermark |
| `.wipe` | ใช้คู่กับ `.watch` บนช่องรูป: clip-path inset ปาดจากซ้าย (moment 6) |
| `.hud-frame` | กรอบ HUD: ลูก 4 ตัว `<span class="hud-c tl\|tr\|bl\|br" aria-hidden="true"></span>` วางมุม · ใช้กับกล่องสินค้า hero, ทุกช่องรูป, โมดูล features |
| `.fig` | คำบรรยายภาพ monospace `ภาพ 01` (ใน `<figcaption>`) |
| `.key` | ปุ่มสี่เหลี่ยม radius 4px มุมวงเล็บ `[ ]` วาดด้วย ::before/::after · **ไม่มี translateY ตอน hover** · variants `.key-line` (ทึบ), `.key-ghost` (ขอบ), `.key-warn` (โทน warn) |
| `.textlink` | ลิงก์ข้อความ + ลูกศรที่เลื่อนขวา 4px ตอน hover |
| `.key-arrow` | class เพิ่มบน svg ลูกศรใน `.key` / `.textlink` (พิมพ์ด้วย `ea2000_icon( 'arrow', 'icon icon-sm key-arrow' )`) |
| `.ledger` / `.ledger-row` / `.ledger-idx` | รายการเลขดัชนี + เนื้อหา คั่น hairline ไม่มีการ์ด (how, install) |
| `.sheet` / `.sheet-row` | datasheet `<dl>` แถว `dt : dd` คั่น hairline (what, footer spec) · `.sheet--ink` = โทนมืด |
| `.mono-marks` / `.mark` | รายการ checklist ที่ใช้ `[x]` เป็นเครื่องหมาย (how_req, tier features) |
| `.tabs` / `.tab` / `.tab-idx` | แถบแท็บที่ขับด้วย radio (tests, pricing) |
| `.led` / `.led-ok` | จุดสถานะ 6px (pain ledger) |
| `.band-surface` | พื้น `--surface` เต็มกว้าง + hairline บนล่าง (pain, tests, faq) |
| `.hazard` | แถบเตือนความเสี่ยงลายทาง (risk) |
| `.margin-note` | บรรทัดคำเตือนสั้น monospace มีไอคอน warn (pricing) |
| `.admin-hint` | ข้อความเห็นเฉพาะแอดมิน (A/C ต้องห่อด้วย `current_user_can( 'customize' )` เสมอ) |

### 0.3 API JavaScript (C นิยามใน main.js · home.js และ footer ใช้)
```js
window.ea2000 = {
  reduced: Boolean,                       // matchMedia('(prefers-reduced-motion: reduce)').matches
  fine: Boolean,                          // matchMedia('(hover: hover) and (pointer: fine)').matches
  observe(el, cb, opts),                  // IntersectionObserver ตัวเดียว (threshold .12 rootMargin -40px) · cb(entry) ครั้งเดียวแล้ว unobserve · ถ้าไม่มี IO หรือ reduced → cb ทันที
  typewriter(outEl, lines, opts)          // ดูข้อ 6.1 · คืน { stop() }
};
```
- `.in` คือ class สถานะเดียวที่ IO เติม (ทั้ง `.reveal` เดิมและ `.watch` ใหม่)
- home.js ถูก enqueue โดย A เฉพาะหน้าแรก ด้วย dependency `ea2000-main` จึงเรียก `window.ea2000` ได้แน่นอน

### 0.4 หัวบท (A พิมพ์ · B จัด)
```html
<header class="ch-head">
  <p class="ch-index mono keep-case"><span class="ch-n">01</span><span class="ch-sep">/</span><span class="ch-total">09</span><span class="ch-label">{kicker}</span></p>
  <h2 class="ch-title">{title}</h2>
  <p class="ch-sub">{subtitle}</p>   <!-- ละได้ถ้าค่าว่าง -->
</header>
```
เลขบท: A นับเฉพาะบทที่เปิดอยู่ (`show_*` true) เรียงตามลำดับจริง ใส่เลข 2 หลัก `01`..`NN` และ total `NN` เดียวกันทุกบท และใส่ `data-chapter` / `data-chapter-label` บน `<section>` ให้ตรงกัน · hero ไม่มี `.ch-head`

### 0.5 Data attributes (A/C พิมพ์ · C อ่าน)
| ที่ | attribute | ค่า |
|---|---|---|
| `main.home-v3` | `data-chapters` | จำนวนบททั้งหมด เช่น `9` |
| `section.ch` | `data-chapter`, `data-chapter-label` | `01`, ป้ายบท |
| `nav.rail` | `data-total` | `09` |
| `figure.boot-visual` | `data-hud-frame` | (ว่าง) |
| `dl.hud` | `data-hud` | (ว่าง) |
| `dd.hud-val` | `data-text` | ข้อความจริง (ซ้ำกับ textContent) |
| `div.term` | `data-term`, `data-lines` (JSON), `data-plays="2"` | ดู 3.2 |
| `div.prompt` | `data-prompt`, `data-lines` (JSON), `data-loops="3"` | ดู 4.3 |
| `footer.console` | `data-spotlight` | มีเมื่อ `show_footer_spotlight` |
| `time[data-clock-out]` | `data-tz="Asia/Bangkok"` | ดู 4.3 |
| ทุกลิงก์ LINE / go | `data-line-pos` | `hero`, `pricing`, `footer`, `dock`, `fab` |

JSON ใน data attribute พิมพ์ด้วย `esc_attr( wp_json_encode( $arr ) )` เสมอ

### 0.6 กติกาเนื้อหาสำหรับค่าเริ่มต้นและ control description
ทุก key ใหม่ที่เป็น textarea/text ที่ผู้เข้าชมอ่านได้ ต้องมี description ลงท้ายว่า `ห้ามระบุกลยุทธ์ ตัวเลขผลเทรด หรือคำรับประกัน` · ค่าเริ่มต้นทุกตัวในเอกสารนี้ผ่านการตรวจแล้ว ห้ามแก้เพิ่มคำต่อไปนี้: ทอง, XAUUSD, gold, martingale, grid, Stop Loss, SL, กำไร...%, รับประกัน, 24 ชม. (เชิงสัญญาบริการ), ทุกออเดอร์

---

## 1) Vision (5 บรรทัด)

1. หน้าแรกคือห้องควบคุมของโต๊ะเทรดตอนเช้า: ผนังขาว กระดาษกราฟจุดจาง เส้น hairline และจอสีเขียวดำเพียงจอเดียว (เทอร์มินัลใน "ทำงานอย่างไร") ก่อนจะเดินเข้าผนังควบคุมสีเขียวเข้มที่ท้ายหน้า (footer)
2. เนื้อหาเรียงเป็นบท `01/09` บนรางซ้ายที่เลื่อนตาม ทุกบทเป็น datasheet, ledger, module grid, filmstrip, แท็บ radio หรือ query log · ไม่มีการ์ดไอคอน ไม่มี chip ไม่มีบลอบเบลอ ไม่มีของลอย
3. การเคลื่อนไหวเป็นกลไก (steps(), ปาด clip-path, วาดเส้น, พิมพ์ทีละตัว) และทุกอันอธิบายสินค้า: วงเล็บ HUD วาดกรอบ, ปัญหาถูกขีดฆ่า, ลำดับการทำงานถูกพิมพ์เป็น log, สัญญาณวาดตัวเองบนขอบ footer
4. ไม่มีตัวเลขใดบนหน้าที่อ่านเป็นผลเทรดได้: HUD แสดงเฉพาะข้อเท็จจริงของระบบ, เทอร์มินัลไม่มีเวลา ไม่มีราคา, ช่องรูปผลทดสอบเป็นกรอบว่างพร้อมคำสั่งจนกว่าจะมีของจริง
5. ปุ่ม LINE คือคีย์กดจริง (keycap) โผล่ 3 ครั้ง: hero, ตัวเลือกแพ็กเกจ, และ launch console ใน footer ที่มี QR · ทางไป LINE ห่างไม่เกิน 1 หน้าจอจากทุกบท

---

## 2) Page map

ลำดับบล็อกบนหน้า (ทั้งหมดผ่าน `show_*` เดิม ยกเว้นที่ระบุ):

| # | id | ชื่อ | พื้น | บท |
|---|---|---|---|---|
| 00 | `#hero` | Boot (hero) | ขาว + dot grid | 00 |
| 01 | `#what` (+ `#about`) | System brief · datasheet | ขาว | 01 |
| 02 | `#pain` | Diagnostic ledger · ขีดฆ่า | surface | 02 |
| 03 | `#how` | System log · เทอร์มินัล + ledger | ขาว | 03 |
| 04 | `#features` | Module grid · hairline | ขาว | 04 |
| 05 | `#tests` | Dossier tabs · backtest / forward | surface | 05 |
| 06 | `#install` | Filmstrip + checklist | ขาว | 06 |
| 07 | `#pricing` | Tier selector | ขาว | 07 |
| 08 | `#faq` | Query log | surface | 08 |
| 09 | `#risk` | Hazard band | warn | 09 |
| :: | footer | Console (LINE launch) | ink | (ไม่ใช่บท) |

ตัดออกจาก `front-page.php` ทั้งก้อน (ไม่ต้องคง markup): highlight, live-strip, team, control-center, gallery, mid-cta, perf, fit, reviews, assurance, explore, blog และ `#cta` เดิม (ย้ายเนื้อหาไป footer) · keys ของบล็อกเหล่านี้คงไว้ใน `ea2000_defaults()` เพื่อ REST · **A ลบ section 17, 19, 21, 22, 23, 24, 25, 26 ใน customizer.php** หลัง grep ยืนยันว่า key ของ section นั้นไม่ถูกใช้ในไฟล์อื่น (section 18 `about_*` และ 20 `steps_*` ให้ grep ก่อน ถ้าไม่มีใครใช้ก็ลบ)

### 2.0 Chapter rail (global บนหน้าแรก · A พิมพ์ครั้งเดียวหลังเปิด `<main>` ก่อน hero · แสดงเมื่อ `show_rail`)
```html
<nav class="rail" aria-label="บทในหน้านี้" data-total="09">
  <p class="rail-counter mono keep-case" aria-hidden="true"><span class="rail-n" data-rail-n>00</span><span class="rail-sep">/</span><span class="rail-total">09</span></p>
  <span class="rail-track" aria-hidden="true"><i class="rail-fill"></i></span>
  <ol class="rail-list">
    <li><a href="#what" data-rail-link="01" title="{what_kicker}"><span class="mono">01</span><span class="sr-only">{what_kicker}</span></a></li>
    <!-- ...ทุกบทที่เปิดอยู่ ตามลำดับ... -->
  </ol>
</nav>
<div class="rail-bar" aria-hidden="true"><i class="rail-bar-fill"></i></div>
```
- Desktop ≥ 1240px: `.rail` sticky ในคอลัมน์ซ้ายนอก `.container` (ดู 5.3) · ต่ำกว่านั้นซ่อน `.rail` และแสดง `.rail-bar` (เส้น 2px sticky ใต้ header เติมตาม progress)
- keys: `show_rail` (ใหม่ checkbox default true) · ป้ายบทมาจาก kicker ของแต่ละบล็อก (ดูรายบล็อก)

### 2.1 Block 00 · Boot (hero) · `#hero`
**Layout**: grid 12 คอลัมน์บน `.container` · copy คอลัมน์ 1 ถึง 7 · กล่องสินค้า คอลัมน์ 8 ถึง 12 ชิดล่าง ยื่นล้ำขอบล่างของ section 48px (desktop ≥ 960 เท่านั้น · บล็อกถัดไปมี padding-top ชดเชย) · พื้นหลัง dot grid บน `::before` (mask จางที่ขอบ) ไม่มีบลอบ ไม่มีแท่งเทียน ไม่มีลอย · ต่ำกว่า 960 เรียงบนล่าง: copy แล้วรูป ไม่ยื่น
```html
<section class="boot" id="hero" data-chapter="00" aria-labelledby="boot-title">
  <div class="container boot-grid">
    <div class="boot-copy">
      <p class="boot-prefix mono keep-case"><span aria-hidden="true">// </span>{hero_badge}</p>
      <h1 class="boot-title" id="boot-title"><span class="boot-title-brand keep-case">{hero_title}</span> <span class="boot-title-sub">{hero_subtitle}</span></h1>
      <p class="boot-desc">{hero_desc}</p>
      <div class="boot-actions">
        <!-- มี LINE -->
        <a class="key key-line" href="{line_url}" target="_blank" rel="noopener" data-line-pos="hero">{icon line}<span>{hero_btn1_text}</span></a>
        <!-- ไม่มี LINE แต่มีหน้า /go/ (publish) -->
        <a class="key key-line" href="{go_url}" data-line-pos="hero">{icon chat}<span>{contact_fallback_text}</span></a>
        <!-- ไม่มีทั้งคู่: ไม่พิมพ์ปุ่มหลัก -->
        <a class="textlink" href="#how">{hero_btn2_text}{icon arrow key-arrow}</a>
      </div>
      <p class="boot-note">{icon warn icon-sm}{hero_note}</p>
      <dl class="hud mono keep-case" data-hud>
        <div class="hud-item"><dt>{label}</dt><dd class="hud-val" data-text="{value}">{value}</dd></div>
        <!-- ...1 รายการต่อบรรทัดของ hero_hud_items (รูปแบบ ป้าย|ค่า · บรรทัดที่ไม่มี | ข้าม) ... -->
        <!-- ไม่มี element สำหรับ sweep · แถบกวาดวาดด้วย .hud::after (ข้อ 3.1) เพราะ <dl> รับลูกได้เฉพาะ dt/dd/div -->
      </dl>
      <p class="sr-only">{hero_hud_note}</p>
    </div>
    <figure class="boot-visual hud-frame" data-hud-frame>
      <span class="hud-c tl" aria-hidden="true"></span><span class="hud-c tr" aria-hidden="true"></span><span class="hud-c bl" aria-hidden="true"></span><span class="hud-c br" aria-hidden="true"></span>
      <img src="{hero_image}" alt="{hero_img_alt}" width="1000" height="1000" loading="eager" decoding="async">   <!-- ไม่มี fetchpriority บน img · ให้ preload (เดสก์ท็อปเท่านั้น) เป็นตัวตั้ง priority -->
    </figure>
  </div>
</section>
```
- `hero_image` ว่าง: แทน `<img>` ด้วย `<span class="img-slot-icon">{icon image}</span><span class="img-slot-note">{hero_img_note}</span>` และเพิ่มคลาส `img-slot` บน figure · **ห้ามใช้โลโก้กลม/orb แทน**
- ถ้า `show_hero` ปิด: คง `<h1 class="sr-only">` เดิม
- **Preload (A ใน functions.php)**: `add_action( 'wp_head', 'ea2000_preload_hero', 1 )` พิมพ์ `<link rel="preload" as="image" href="{hero_image}" media="(min-width: 961px)" fetchpriority="high">` เฉพาะ `is_front_page() && show_hero && hero_image !== ''` · **เดสก์ท็อปเท่านั้น**: ต่ำกว่า 960 กล่องสินค้าอยู่ใต้ fold ทั้งกล่อง (LCP บนมือถือคือข้อความ) จึงไม่ preload และ `<img>` ไม่ใส่ `fetchpriority` เพื่อไม่ให้แย่งคิวกับฟอนต์
- **Keys เดิม**: `hero_badge` (เปลี่ยน default เป็น `Expert Advisor สำหรับ MetaTrader 5`), `hero_title`, `hero_subtitle`, `hero_desc`, `hero_btn1_text`, `hero_btn2_text`, `hero_note`, `hero_image`, `hero_img_alt`
- **Keys ใหม่**: `hero_hud_items` (textarea) default
  ```
  แพลตฟอร์ม|MetaTrader 5
  สินทรัพย์|หลายคู่เงิน
  รูปแบบ|เทรดอัตโนมัติตามกฎที่ตั้งไว้
  การส่งมอบ|ไฟล์ EA และคู่มือ
  ```
  `hero_hud_note` (text) `ป้ายข้อมูลระบบ ไม่ใช่ผลการเทรด` · `hero_img_note` (text) `รูปที่ต้องใส่: ภาพกล่องสินค้าพื้นโปร่ง 1000x1000 px` · `contact_fallback_text` (text, ใช้ร่วมทั้งหน้าและ footer) `ติดต่อทีมงาน` · `fig_label` (text) `ภาพ` · `show_rail` (checkbox true)
- Guard ใน PHP: ค่าฝั่งขวาของ `hero_hud_items` ที่ขึ้นต้นด้วยตัวเลขแล้วตามด้วย `%` หรือมีคำว่า `กำไร` ให้ข้ามบรรทัดนั้น (กันเจ้าของใส่ผลเทรด)

### 2.2 Block 01 · System brief · `#what`
**Layout**: grid 7/5 (≥ 960) · ซ้าย: ย่อหน้า + datasheet + กล่องหลักการ · ขวา: ช่องรูปใน HUD
```html
<section class="ch ch-what" id="what" data-chapter="01" data-chapter-label="{what_kicker}">
  <span id="about" class="sec-anchor" aria-hidden="true"></span>
  <div class="container">
    {ch-head: what_kicker / what_title / (ไม่มี sub)}
    <div class="brief-grid">
      <div class="brief-copy">
        <p>...</p>   <!-- what_text แยกย่อหน้าด้วยบรรทัดว่างเหมือนเดิม -->
        <dl class="sheet">
          <div class="sheet-row"><dt class="mono">{label หรือ 01}</dt><dd>{text}</dd></div>
          <!-- what_points บรรทัด "ป้าย|ข้อความ" → dt=ป้าย · บรรทัดไม่มี | → dt=เลข 2 หลัก -->
        </dl>
        <aside class="principle">
          <p class="principle-label mono keep-case"><span aria-hidden="true">// </span>{what_principle_label}</p>
          <p class="principle-text">{what_principle}</p>
        </aside>
      </div>
      <div class="brief-media">{ea2000_front_media( 'what', 1280, 800, 'ภาพ 01' )}</div>
    </div>
  </div>
</section>
```
- **Keys เดิม**: `what_kicker` (default ใหม่ `ระบบคืออะไร`), `what_title`, `what_text` (**A แก้ default**: ลบวลี `ตั้ง Lot และ Stop Loss ตามค่าที่กำหนด` เป็น `จัดการขนาดออเดอร์และเงื่อนไขปิดตามค่าที่ตั้งไว้`), `what_points` (default ใหม่ 3 บรรทัดแบบ `ป้าย|ข้อความ`: `แพลตฟอร์ม|ทำงานบน MetaTrader 5 โดยตรง ติดตั้งครั้งเดียวแล้วรันต่อเนื่องบนคอมพิวเตอร์หรือ VPS` / `วินัย|ทำตามกฎเดิมทุกครั้ง ไม่ให้ความกลัวหรือความโลภมาแทรกการตัดสินใจ` / `การควบคุม|ผู้ใช้กำหนดทุน ขนาดออเดอร์ และระดับความเสี่ยงเอง พร้อม Dashboard บนกราฟให้ตรวจสถานะได้ตลอด`), `what_img`, `what_img_alt`, `what_img_note` (default ใหม่ `รูปที่ต้องใส่: ภาพหน้าจอ MT5 ขณะ EA2000 ทำงาน เห็นกราฟและแผง Dashboard ไม่ต้องเห็นตัวเลขบัญชี · แนะนำ 1280x800 px`)
- **Keys ใหม่**: `what_principle_label` `หลักการของเรา` · `what_principle` `เราไม่แสดงตัวเลขที่ยังตรวจสอบไม่ได้ และไม่รับประกันผลกำไร` (description: `ประโยคหลักการของทีม ห้ามใส่คำพูดลูกค้าหรือผลเทรด`)
- ช่องรูป: `ea2000_front_media()` v3 (ข้อ 2.11)

### 2.3 Block 02 · Diagnostic ledger · `#pain` · `.band-surface`
**Layout**: grid 5/7 (≥ 960): ซ้าย `.ch-head` (ตำแหน่ง sticky ไม่ต้อง) · ขวา ledger 4 แถว + แถวสรุป · ไม่มีการ์ด ไม่มีไอคอน ไม่มีคำ outline ภาษาอังกฤษ
```html
<section class="ch ch-pain band-surface" id="pain" data-chapter="02" data-chapter-label="{pain_kicker}">
  <div class="container diag-grid">
    {ch-head: pain_kicker / pain_title / pain_subtitle}
    <ol class="diag watch">
      <li class="diag-row" style="--i:0"><span class="diag-idx mono">01</span><i class="led" aria-hidden="true"></i>
        <div class="diag-body"><h3 class="diag-title"><span class="strike">{pain1_title}</span></h3><p>{pain1_desc}</p></div></li>
      <!-- ...02..04 (--i:1..3) ข้ามคู่ที่ว่างทั้ง title และ desc... -->
      <li class="diag-row diag-resolved" style="--i:4"><span class="diag-idx mono" aria-hidden="true">▸</span><i class="led led-ok" aria-hidden="true"></i>
        <div class="diag-body"><p class="diag-resolved-label mono">{pain_resolved_label}</p><p>{pain_resolved_text}</p></div></li>
    </ol>
  </div>
</section>
```
- `style="--i:N"` พิมพ์ด้วย `(int)` เท่านั้น
- **Keys เดิม**: `pain_title`, `pain_subtitle`, `pain1..4_title`, `pain1..4_desc`
- **Keys ใหม่**: `pain_kicker` `ปัญหาของการเทรดมือ` · `pain_resolved_label` `สิ่งที่ระบบอัตโนมัติเข้ามาแทน` · `pain_resolved_text` `ระบบทำตามกฎเดิมทุกครั้ง ส่วนทุน ความเสี่ยง และการตัดสินใจเริ่มหรือหยุดยังเป็นของคุณ`

### 2.4 Block 03 · System log · `#how`
**Layout**: grid 58/42 (≥ 960) · ซ้าย: เทอร์มินัล (จอมืด `--ink-deep` วางบนพื้นขาว · เป็นวัตถุมืดชิ้นเดียวในเนื้อหา) แล้ว ledger 4 ขั้น (semantic twin มองเห็นเสมอ) · ขวา: ช่องรูป how + รายการ requirement แบบ `[x]` · ต่ำกว่า 960 เรียง: head, term, ledger, รูป, req
```html
<section class="ch ch-how" id="how" data-chapter="03" data-chapter-label="{how_kicker}">
  <div class="container">
    {ch-head: how_kicker / how_title / how_intro}
    <div class="how-grid">
      <div class="how-main">
        <!-- เมื่อ show_how_log -->
        <div class="term keep-case" aria-hidden="true" data-term data-lines="{json}" data-plays="2">
          <div class="term-bar"><i></i><i></i><i></i><span class="term-title">{how_log_title}</span></div>
          <pre class="term-out mono" data-term-out></pre>
        </div>
        <ol class="ledger how-ledger">
          <li class="ledger-row"><span class="ledger-idx mono">01</span><div class="ledger-body"><h3>{how_step1_title}</h3><p>{how_step1_desc}</p></div></li>
          <!-- ...02..04... -->
        </ol>
      </div>
      <aside class="how-side">
        {ea2000_front_media( 'how', 1280, 800, 'ภาพ 02' )}
        <div class="req">
          <h3 class="req-title">{how_req_title}</h3>
          <ul class="req-list mono-marks"><li><span class="mark mono keep-case" aria-hidden="true">[x]</span><span>{item}</span></li></ul>
        </div>
      </aside>
    </div>
  </div>
</section>
```
- `data-lines` = JSON array ของ object `{ "t": "ข้อความ", "c": "cmd|idx|ready|log" }` ที่ A สร้างดังนี้:
  1. `{t: how_log_prompt + ' ' + how_log_start, c: 'cmd'}`
  2. ถ้า `how_log_lines` ว่าง: 1 รายการต่อขั้นที่มี title `{t: '[0N] ' + how_stepN_title, c: 'idx'}` · ถ้าไม่ว่าง: 1 รายการต่อบรรทัด `{t: บรรทัด, c: 'log'}`
  3. `{t: '▸ ' + how_log_ready, c: 'ready'}`
  · **ห้ามมี timestamp, เวลา, ราคา, OK/FILLED ในบรรทัดใด** (judges)
- **Keys เดิม**: `how_kicker` (default ใหม่ `ลำดับการทำงาน`), `how_title`, `how_intro`, `how_step1..4_title/desc` (**A แก้ default** `how_step3_desc`: `พร้อมจัดการ Lot และ Stop Loss ตามค่าที่คุณตั้งไว้` → `พร้อมจัดการขนาดออเดอร์และเงื่อนไขปิดตามค่าที่คุณตั้งไว้`), `how_req_title`, `how_req_items`, `how_img`, `how_img_alt`, `how_img_note` (default ใหม่ `รูปที่ต้องใส่: ภาพกราฟ MT5 ที่แนบ EA2000 แล้ว เห็นแผง Dashboard ปิดตัวเลขบัญชีได้ · แนะนำ 1280x800 px`)
- **Keys ใหม่**: `show_how_log` (checkbox true) · `how_log_title` `ภาพจำลองลำดับการทำงาน` · `how_log_prompt` `ea2000@mt5:~$` · `how_log_start` `เริ่มลำดับการทำงาน` · `how_log_lines` (textarea, default ว่าง, description: `เว้นว่าง = ใช้ชื่อ 4 ขั้นด้านบนอัตโนมัติ · บรรทัดละ 1 ข้อความ · ห้ามระบุกลยุทธ์ ตัวเลข เวลา หรือผลการเทรด`) · `how_log_ready` `พร้อมทำงาน · รอเงื่อนไขตามกฎที่ตั้งไว้`

### 2.5 Block 04 · Module grid · `#features`
**Layout**: grid 3x2 (≥ 960), 2 คอลัมน์ (640 ถึง 960), 1 คอลัมน์ (< 640) · เซลล์ไม่มีขอบของตัวเอง ใช้ `gap:1px` บนพื้น `--border` และเซลล์ทาสี `--bg` = hairline grid · ไม่มีไอคอน ไม่มี lift
```html
<section class="ch ch-features" id="features" data-chapter="04" data-chapter-label="{features_kicker}">
  <div class="container">
    {ch-head: features_kicker / features_title / features_subtitle}
    <ul class="modules">
      <li class="module hud-frame">
        <span class="hud-c tl" aria-hidden="true"></span><span class="hud-c tr" aria-hidden="true"></span><span class="hud-c bl" aria-hidden="true"></span><span class="hud-c br" aria-hidden="true"></span>
        <i class="scan" aria-hidden="true"></i>
        <p class="module-id mono">{feat_module_label} 01</p>
        <h3 class="module-title">{feat1_title}</h3>
        <p class="module-desc">{feat1_desc}</p>
      </li>
      <!-- ...02..06 ข้ามคู่ที่ว่าง... -->
    </ul>
  </div>
</section>
```
- **Keys เดิม**: `features_title`, `features_subtitle`, `feat1..6_title/desc` (**A แก้ default** `feat4_desc`: `วางแผนเรื่อง Lot, Stop Loss และ Drawdown ได้ตามระดับความเสี่ยงที่เหมาะกับทุนของคุณ` → `วางแผนขนาดออเดอร์และระดับ Drawdown ที่ยอมรับได้ ให้เหมาะกับทุนของคุณ`)
- **Keys ใหม่**: `features_kicker` `โมดูลของระบบ` · `feat_module_label` `โมดูล`

### 2.6 Block 05 · Dossier tabs · `#tests` · `.band-surface`
**Layout**: แท็บ radio 2 ตัวเต็มความกว้าง แผงเดียวสลับ · แผง = grid 7/5: ช่องรูปใหญ่ซ้าย (16:9 ใน HUD) ข้อความ + ปุ่มขวา · ไม่มี demo chart ไม่มี canvas · `tests_note` พิมพ์ใต้แผงเสมอ
```html
<section class="ch ch-tests band-surface" id="tests" data-chapter="05" data-chapter-label="{tests_kicker}">
  <div class="container">
    {ch-head: tests_kicker / tests_title / tests_intro}
    <div class="dossier">
      <input type="radio" id="dossier-bt" name="ea2000-tests" class="tab-radio" checked>
      <input type="radio" id="dossier-fw" name="ea2000-tests" class="tab-radio">
      <div class="tabs">
        <label class="tab" for="dossier-bt"><span class="tab-idx mono">01</span>{tests_tab_bt_label}</label>
        <label class="tab" for="dossier-fw"><span class="tab-idx mono">02</span>{tests_tab_fw_label}</label>
        <i class="tab-line" aria-hidden="true"></i>
      </div>
      <div class="tab-panels">
        <article class="tab-panel panel-1">
          <div class="dossier-media">{ea2000_front_media( 'tests_bt', 1280, 720, 'ภาพ 03' )}</div>
          <div class="dossier-body">
            <h3 class="keep-case">{tests_bt_title}</h3>
            <p>{tests_bt_text}</p>
            <a class="key key-ghost" href="{home_url('/backtest/')}">{tests_bt_btn}{icon arrow key-arrow}</a>
          </div>
        </article>
        <article class="tab-panel panel-2"> <!-- tests_fw_* · 'ภาพ 04' · /forward-test/ --> </article>
      </div>
    </div>
    <p class="notice-row">{icon warn icon-sm}<span>{tests_note}</span></p>
  </div>
</section>
```
- radio ต้องอยู่ก่อน `.tabs` และ `.tab-panels` ในพาเรนต์เดียวกัน (sibling selector) · radio ซ่อนด้วย `position:absolute; opacity:0` **ไม่ใช่** `display:none`
- **Keys เดิม**: `tests_kicker` (default ใหม่ `การทดสอบ`), `tests_title`, `tests_intro`, `tests_bt_title/text/btn/img/img_alt/img_note`, `tests_fw_*`, `tests_note` (ห้ามลบ ห้ามย่อ) · default note ใหม่: bt `รูปที่ต้องใส่: ภาพรายงาน Backtest จาก MT5 Strategy Tester ใส่เมื่อมีผลจริงเท่านั้น · แนะนำ 1280x720 px` · fw `รูปที่ต้องใส่: ภาพบัญชี Forward Test จริงพร้อมลิงก์ตรวจสอบ ใส่เมื่อมีข้อมูลจริงเท่านั้น · แนะนำ 1280x720 px`
- **Keys ใหม่**: `tests_tab_bt_label` `ทดสอบย้อนหลัง` · `tests_tab_fw_label` `ทดสอบเดินหน้า`
- ห้ามพิมพ์ค่าจาก `bt_stat*` / `fw_stat*` ในบล็อกนี้

### 2.7 Block 06 · Filmstrip + checklist · `#install`
```html
<section class="ch ch-install" id="install" data-chapter="06" data-chapter-label="{install_kicker}">
  <div class="container">
    {ch-head: install_kicker / install_title / install_intro}
    <div class="film" role="group" tabindex="0" aria-label="ภาพขั้นตอนการติดตั้ง 3 ภาพ เลื่อนดูได้">   <!-- role="group" จำเป็น: div เปล่าห้ามมี aria-label (ARIA 1.2) -->
      {ea2000_front_media( 'install_step1', 1280, 720, fig_label . ' 01/03' )}
      {ea2000_front_media( 'install_step2', 1280, 720, fig_label . ' 02/03' )}
      {ea2000_front_media( 'install_step3', 1280, 720, fig_label . ' 03/03' )}
    </div>
    <ol class="ledger install-ledger">
      <li class="ledger-row"><span class="ledger-idx mono keep-case"><span aria-hidden="true">&gt; </span>{install_step_label} 01</span><div class="ledger-body"><h3>{install_step1_title}</h3><p>{install_step1_desc}</p></div></li>
      <!-- ...02, 03... -->
    </ol>
    <p class="notice-row">{icon warn icon-sm}<span>{install_mobile_note}</span></p>
    <p class="ch-actions"><a class="key key-ghost" href="{install_btn_url → ea2000_link_url, fallback /how-to-install/}">{install_btn}{icon arrow key-arrow}</a></p>
  </div>
</section>
```
- ช่องรูปใน `.film` ได้คลาส `frame` เพิ่ม (A ส่ง `$extra_class = 'frame'` ให้ helper) · แต่ละ frame กว้างสูงสุด 420px, scroll-snap x mandatory, ซ่อน scrollbar, `.film` มี `overflow-x:auto` และพาเรนต์ `min-width:0` · หน้าไม่ overflow
- **Keys เดิม**: `install_kicker` (default ใหม่ `การติดตั้ง`), `install_title`, `install_intro`, `install_step1..3_title/desc/img/img_alt`, `install_mobile_note`, `install_btn`, `install_btn_url`
- **Keys ใหม่**: `install_step1_img_note` `รูปที่ต้องใส่: ภาพหน้าจอขั้นเตรียมบัญชี MT5 และดาวน์โหลดไฟล์ · แนะนำ 1280x720 px` · `install_step2_img_note` `รูปที่ต้องใส่: ภาพโฟลเดอร์ Experts ใน MT5 และปุ่ม Algo Trading · แนะนำ 1280x720 px` · `install_step3_img_note` `รูปที่ต้องใส่: ภาพ EA2000 บนกราฟพร้อมแผง Dashboard · แนะนำ 1280x720 px` · `install_step_label` `ขั้น`

### 2.8 Block 07 · Tier selector · `#pricing` (gate `show_pricing_home`)
**Layout** < 1100: แท็บ radio 3 ตัว + แผงเดียว · ≥ 1100: ซ่อนแท็บ แสดง 3 แผงเป็นตารางเปรียบเทียบ 3 คอลัมน์คั่น hairline · แพ็กเกจ featured: เส้นบน 2px `--accent` + คำ `แนะนำ` monospace หลังชื่อ (ไม่มี pill)
```html
<section class="ch ch-pricing" id="pricing" data-chapter="07" data-chapter-label="{pricing_kicker}">
  <div class="container">
    {ch-head: pricing_kicker / pricing_home_title / pricing_home_sub}
    <div class="tiers">
      <input type="radio" id="tier-1" name="ea2000-tier" class="tab-radio" checked>  <!-- checked = แพ็กเกจ featured ตัวแรก ถ้าไม่มีให้ตัวแรก -->
      <input type="radio" id="tier-2" name="ea2000-tier" class="tab-radio">
      <input type="radio" id="tier-3" name="ea2000-tier" class="tab-radio">
      <div class="tabs tier-tabs">
        <label class="tab" for="tier-1"><span class="tab-idx mono">01</span><span class="keep-case">{pkg1_name}</span><span class="tab-rec mono">{pricing_recommended_label}</span></label>
        <!-- tab-rec เฉพาะ featured -->
      </div>
      <div class="tab-panels tier-panels">
        <article class="tab-panel tier-panel panel-1 is-featured">
          <header class="tier-head">
            <h3 class="tier-name keep-case">{pkg1_name} <span class="tier-rec mono">{pricing_recommended_label}</span></h3>
            <p class="tier-tag">{pkg1_tag}</p>
          </header>
          <p class="tier-price">
            <!-- pricing_mode = price และ pkg1_price ไม่ว่าง -->
            <span class="tier-amt keep-case">{pkg1_price}</span> <span class="tier-period">{pkg1_period}</span>
            <!-- อื่น ๆ --> <span class="tier-amt tier-amt--contact">{pricing_contact_text}</span>
          </p>
          <ul class="tier-list mono-marks"><li><span class="mark mono keep-case" aria-hidden="true">[x]</span><span>{บรรทัดจาก pkg1_features}</span></li></ul>
          <a class="key key-line" href="{line_url}" target="_blank" rel="noopener" data-line-pos="pricing">{icon line}<span>{pricing_btn_text}</span></a>
          <!-- ไม่มี LINE: href=/go/ + contact_fallback_text · ไม่มีทั้งคู่: ไม่พิมพ์ปุ่ม -->
        </article>
        <!-- panel-2, panel-3 · ข้ามแพ็กเกจที่ pkgN_name ว่าง -->
      </div>
    </div>
    <p class="margin-note mono">{icon warn icon-sm}<span>{risk_margin_note}</span></p>
    <p class="ch-actions"><a class="textlink" href="{home_url('/pricing/')}">{pricing_more_text}{icon arrow key-arrow}</a></p>
    <p class="ch-foot">{pricing_note}</p>
  </div>
</section>
```
- **Keys เดิม**: `pricing_home_title`, `pricing_home_sub`, `pricing_mode`, `pricing_btn_text`, `pricing_note`, `pkg1..3_name/tag/price/period/features/featured`
- **Keys ใหม่**: `pricing_kicker` `แพ็กเกจ` · `pricing_recommended_label` `แนะนำ` · `pricing_more_text` `ดูรายละเอียดแพ็กเกจทั้งหมด` · `pricing_contact_text` `สอบถามราคา`
- `risk_margin_note` นิยามในบล็อก risk (2.10)

### 2.9 Block 08 · Query log · `#faq` · `.band-surface`
```html
<section class="ch ch-faq band-surface" id="faq" data-chapter="08" data-chapter-label="{faq_kicker}">
  <div class="container">
    {ch-head: faq_kicker / faq_title / faq_subtitle}
    <div class="qlog">
      <details class="q" name="ea2000-faq">
        <summary class="q-sum"><span class="q-idx mono">01</span><span class="q-text">{faq1_q}</span><i class="q-mark" aria-hidden="true"></i></summary>
        <div class="q-ans"><p>{faq1_a (nl2br)}</p></div>
      </details>
      <!-- ...ข้ามคู่ที่ว่าง · ทุกข้อปิด (ไม่มี open) · เลขนับเฉพาะข้อที่แสดง -->
    </div>
  </div>
</section>
```
- ≥ 960: grid 2 คอลัมน์ `align-items:start` (8 ข้อ = 4 แถว) · `name="ea2000-faq"` คงไว้ (เปิดทีละข้อ) · `ea2000_faq_schema()` ไม่แตะ
- **Keys เดิม**: `faq_title`, `faq_subtitle`, `faq1..10_q/a` · **ใหม่**: `faq_kicker` `คำถามที่พบบ่อย`

### 2.10 Block 09 · Hazard band · `#risk`
```html
<section class="ch ch-risk hazard" id="risk" data-chapter="09" data-chapter-label="{risk_kicker}">
  <div class="container hazard-inner">
    <p class="hazard-label mono keep-case"><span class="hazard-stamp">{risk_label}</span><span class="hazard-kicker">{risk_kicker}</span></p>
    <h2 class="hazard-title">{risk_title}</h2>
    <p class="hazard-text">{risk_text (nl2br)}</p>
    <a class="key key-warn" href="{home_url('/risk-disclosure/')}">{risk_more_text}{icon arrow key-arrow}</a>
  </div>
</section>
```
- ข้อความเต็ม ตัวอักษร 18px ไม่พับ ไม่ตัด ไม่ sticky · ไม่มี `.ch-head` (ใช้ hazard-label แทน แต่ยังนับเป็นบทบนราง)
- **Keys เดิม**: `risk_title`, `risk_text` · **ใหม่**: `risk_kicker` `ประกาศความเสี่ยง` · `risk_label` `NOTICE` (keep-case, 1 ใน 2 stamp อังกฤษที่อนุญาต) · `risk_more_text` `อ่านประกาศความเสี่ยงฉบับเต็ม` · `risk_margin_note` `ผลในอดีตไม่รับประกันผลในอนาคต · การเทรดมีความเสี่ยง โปรดอ่านประกาศฉบับเต็มก่อนตัดสินใจ`

### 2.11 ช่องรูป · `ea2000_front_media()` v3 (A แก้ใน front-page.php)
ลายเซ็นใหม่: `ea2000_front_media( $key, $width = 1280, $height = 800, $caption = '', $extra_class = '' )` · อ่าน `{key}_img`, `{key}_img_alt`, `{key}_img_note` เหมือนเดิม
```html
<!-- มีรูป -->
<figure class="hud-frame media-frame watch wipe {extra}">
  <span class="hud-c tl" aria-hidden="true"></span><span class="hud-c tr" aria-hidden="true"></span><span class="hud-c bl" aria-hidden="true"></span><span class="hud-c br" aria-hidden="true"></span>
  <img src="{src}" alt="{alt}" loading="lazy" decoding="async" width="{w}" height="{h}">
  <figcaption class="fig mono keep-case">{caption}</figcaption>   <!-- เฉพาะเมื่อ caption ไม่ว่าง -->
</figure>
<!-- ไม่มีรูป · เห็นสาธารณะโดยเจตนา -->
<figure class="hud-frame img-slot watch wipe {extra}" aria-label="{alt}">
  (4 hud-c)
  <span class="img-slot-icon">{icon image}</span>
  <span class="img-slot-note">{note หรือ alt}</span>
  <figcaption class="fig mono keep-case">{caption}</figcaption>
</figure>
```
รายการช่องรูปและสิ่งที่เจ้าของต้องส่ง (ข้อความ note คือ default ของ `*_img_note`): `hero_image` (มีแล้ว), `what_img`, `how_img`, `tests_bt_img`, `tests_fw_img`, `install_step1..3_img` (default guide-01..03.webp) · **ไม่มี slot ใหม่ที่ต้องอัปโหลดเพิ่มนอกจาก `footer_line_qr_img`**

---

## 3) Signature moments

ทุก moment: (ก) เนื้อหาอยู่ใน HTML ตั้งแต่แรก (ข) สถานะเริ่มต้นที่ "ซ่อน/ยังไม่วาด" ต้อง prefix `.js` (ค) `.no-js` = สถานะจบ (ง) reduced motion = สถานะจบทันที (บล็อก clamp 0.01ms ใน section 25 ช่วยอยู่แล้ว แต่ต้องมีกฎ explicit ด้วย)

### 3.1 Boot sequence · วงเล็บ HUD วาดกรอบ + HUD strip decode (hero)
- **DOM**: `figure[data-hud-frame]` (4 `.hud-c`), `dl[data-hud]` + `dd.hud-val[data-text]` · แถบกวาดเป็น `.hud::after` (ไม่มี element ใน dl)
- **CSS (B)**:
  ```css
  .hud-c { position:absolute; width:var(--hud-size); height:var(--hud-size); border:2px solid var(--hud); pointer-events:none; }
  .hud-c.tl { top:-2px; left:-2px; border-right:0; border-bottom:0 } /* tr bl br ตามมุม */
  .js .home-v3 [data-hud-frame]:not(.on) .hud-c { width:0; height:0; }
  .home-v3 .hud-c { transition: width .35s steps(6), height .35s steps(6) .1s; }
  .hud::after { content:""; position:absolute; inset:0; pointer-events:none; opacity:0;
    background: linear-gradient(90deg, transparent, rgba(var(--accent-rgb), .55), transparent) -80px 0 / 80px 100% no-repeat; }
  .js .hud.on::after { opacity:1; animation: hud-sweep .9s linear .15s 1 both; }
  @keyframes hud-sweep { from { background-position:-80px 0 } to { background-position: calc(100% + 80px) 0 } }
  .no-js .hud-c { width:var(--hud-size); height:var(--hud-size); }
  @media (prefers-reduced-motion: reduce) { .hud-c { width:var(--hud-size) !important; height:var(--hud-size) !important } .hud::after { display:none } }
  ```
- **JS (C, home.js ~1.0 KB)**:
  ```
  frame = $('[data-hud-frame]'); hud = $('[data-hud]')
  onReady: frame.classList.add('on'); hud.classList.add('on')
  if ea2000.reduced: return   // ข้อความจริงอยู่แล้ว
  GLYPHS = '#/\\|<>=+*'; queue = [...hud.querySelectorAll('.hud-val')]
  decode(el): final = el.dataset.text; t0 = now
    frame(): p = min(1, (now - t0) / 400); n = floor(p * final.length)
      out = final.split('').map((ch, i) => i < n || !/[A-Za-z0-9]/.test(ch) ? ch : GLYPHS[rand]).join('')
      el.textContent = out; if p < 1 rAF(frame) else { el.textContent = final; next() }
  run sequentially with 60ms gap
  ```
  ตัวอักษรไทย ช่องว่าง เครื่องหมาย ไม่ถูกสุ่มเด็ดขาด (regex ต่อตัว) · ความกว้างคงที่เพราะ glyph เป็น ASCII บน monospace
- **งบ**: CSS 0.7 KB · JS 1.0 KB · LCP: `<img>` ไม่ถูกแตะ ข้อความไม่เคย opacity 0

### 3.2 System log typewriter (how)
- **DOM**: `div.term[data-term][data-lines][data-plays]` > `pre[data-term-out]` · twin: `ol.how-ledger`
- **CSS (B)**: `.term { background:var(--ink-deep); color:var(--ink-text); border:1px solid var(--ink-border); border-radius:6px; }` · `.term-bar i { width:8px; height:8px; border:1px solid var(--ink-muted) }` (สี่เหลี่ยมกลวง 3 อัน) · `.term-out { font:500 .875rem/1.7 var(--font-mono); white-space:pre-wrap; min-height:11em; margin:0; padding:16px 18px }` · `.term-line.idx { color:var(--ink-text) } .term-line.cmd { color:var(--ink-muted) } .term-line.ready { color:var(--ink-accent) }` · cursor: `.term-out::after { content:''; display:inline-block; width:.6em; height:1.1em; background:var(--ink-accent); vertical-align:-.2em; animation: term-blink 1s steps(2) infinite }` · `.no-js .term { display:none }`
- **JS (C, home.js ~1.2 KB + typewriter ใน main.js)**:
  ```
  term = $('[data-term]'); lines = JSON.parse(term.dataset.lines); out = term.querySelector('[data-term-out]')
  plays = 0; max = +term.dataset.plays || 2
  play(): out.style.minHeight = ''; out.textContent = lines.map(l => l.t).join('\n'); out.style.minHeight = out.offsetHeight + 'px'   // จองความสูงสุดท้ายก่อนพิมพ์ กัน CLS ใต้เทอร์มินัล วัดใหม่ทุกครั้งที่เล่น
         ea2000.typewriter(out, lines, { cps: 36, linePause: 320, wrap: 'span.term-line' })   // typewriter ล้าง out เองตอนเริ่ม
  ea2000.observe(term, play, { threshold: .4, repeat: true })   // repeat: observe ต่อ ไม่ unobserve จนกว่า plays >= max
  visibilitychange → typewriter.stop() เมื่อ hidden
  reduced → out.textContent = lines.map(l => l.t).join('\n') ครั้งเดียว ไม่มีอนิเมชัน
  ```
- **งบ**: CSS 0.9 KB · JS 1.2 KB · ห้ามมี timestamp

### 3.3 Diagnostic strike-through (pain)
- **DOM**: `ol.diag.watch` > `li.diag-row[style=--i]` > `.strike`, `.led`, `.diag-resolved`
- **CSS (B, 0 JS)**:
  ```css
  .strike { background: linear-gradient(var(--primary), var(--primary)) 0 55% / 0% 2px no-repeat; -webkit-box-decoration-break: clone; box-decoration-break: clone; transition: background-size .5s cubic-bezier(.65,0,.35,1) calc(var(--i) * 140ms); }
  .diag.in .strike { background-size: 100% 2px; }
  .led { width:6px; height:6px; border-radius:50%; background:var(--warn); box-shadow:0 0 0 3px rgba(var(--warn-rgb), .18); transition: background .2s calc(var(--i) * 140ms + .4s), box-shadow .2s calc(var(--i) * 140ms + .4s); }
  .diag.in .led, .led-ok { background:var(--primary); box-shadow:0 0 0 3px rgba(var(--primary-rgb), .18); }
  .js .diag-resolved { opacity:0; transition: opacity .3s .9s; } .diag.in .diag-resolved { opacity:1; }
  .no-js .strike { background-size:100% 2px } .no-js .diag-resolved { opacity:1 }
  @media (prefers-reduced-motion: reduce) { .strike { background-size:100% 2px } .diag-resolved { opacity:1 } }
  ```
- ใช้ `.in` จาก observer เดิม (C เพิ่ม `.watch` ใน selector) · เส้นขีดที่ 55% ไม่ทับสระบนล่างของไทย

### 3.4 Chapter rail + progress track
- **DOM**: `nav.rail[data-total]`, `[data-rail-n]`, `.rail-fill`, `a[data-rail-link]`, `div.rail-bar > i.rail-bar-fill`, `section[data-chapter]`
- **CSS (B)**:
  ```css
  .rail-fill, .rail-bar-fill { transform-origin: top; transform: scaleY(var(--p, 0)); }   /* rail-bar ใช้ scaleX แนวนอน */
  @supports (animation-timeline: scroll()) {
    .rail-fill, .rail-bar-fill { animation: rail-fill linear both; animation-timeline: scroll(root); }
  }
  @keyframes rail-fill { from { transform: scaleY(0) } to { transform: scaleY(1) } }
  .rail a[aria-current="true"] { color: var(--primary); }
  .rail-n[data-tick] { animation: rail-tick .18s steps(2) 1; }
  @keyframes rail-tick { from { transform: translateY(-40%); opacity: 0 } }
  ```
- **JS (C, home.js ~1.0 KB)**:
  ```
  if !CSS.supports('animation-timeline: scroll()'): scroll(passive) + rAF → html.style.setProperty('--p', scrollY / (scrollHeight - innerHeight))
  IO(sections[data-chapter], rootMargin '-45% 0px -45% 0px', threshold 0) → เมื่อ isIntersecting: n = section.dataset.chapter
     railN.textContent = n; railN.toggleAttribute('data-tick') (ลบแล้วใส่ใหม่ใน rAF ถัดไปเพื่อ retrigger)
     links.forEach(a => a.toggleAttribute('aria-current', a.dataset.railLink === n))
  reduced → ไม่ตั้ง data-tick (เปลี่ยนตัวเลขเฉย ๆ) · --p ยังอัปเดต (เป็น state ไม่ใช่ตกแต่ง)
  ```
- no-JS: rail แสดง `00/09` และ track ว่าง · เนื้อหาไม่กระทบ · < 1240px: `.rail { display:none }` และ `.rail-bar` ทำงานด้วย `--p` เดียวกัน

### 3.5 Module HUD focus + scanline (features) · CSS only
```css
.module .hud-c { --hud: var(--primary); opacity:0; transform: scale(1.15); transition: opacity .16s steps(3), transform .16s steps(3); }
@media (hover:hover) {
  .module:hover .hud-c { opacity:1; transform:none; }
  .module:hover .scan { animation: module-scan .5s linear 1; }
}
.scan { position:absolute; left:0; right:0; top:0; height:1px; background:var(--accent); opacity:0; pointer-events:none; }
@keyframes module-scan { from { top:0; opacity:.8 } to { top:100%; opacity:0 } }
@media (hover:none) { .module:first-child .hud-c { opacity:1; transform:none } }
```
grid: `.modules { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:1px; background:var(--border); width:100%; }` `.module { background:var(--bg); padding:28px; position:relative; margin:0; }` ไม่มี negative margin

### 3.6 Un-mask wipe สำหรับทุกช่องรูป · CSS only (graft ISSUE 01)
```css
.js .home-v3 .wipe::after { content:""; position:absolute; inset:-4px; z-index:3; background:var(--bg); transform-origin:right center; transition: transform .8s cubic-bezier(.2,.7,.2,1); pointer-events:none; }
.js .home-v3 .band-surface .wipe::after { background:var(--surface); }
.js .home-v3 .wipe.in::after { transform: scaleX(0); }
.no-js .wipe::after { display:none }
@media (prefers-reduced-motion: reduce) { .wipe::after { display:none !important } }
```
(-4px ให้ชั้นทับคลุมวงเล็บ HUD ที่ยื่น 2px) · ช่องรูปใน HUD ได้ `.watch` จึงวาดวงเล็บพร้อมกัน: `.js .home-v3 .hud-frame.watch:not(.in) .hud-c { width:0; height:0 }`
- **แก้จากร่างแรก (9 ก.ย. 2026 ตอนรวมงาน)**: ร่างแรกใช้ `clip-path: inset(-4px 100% -4px -4px)` บนตัว figure · Chrome นำ clip-path ไปคิดใน IntersectionObserver ด้วย (ทดสอบแล้ว: intersectionRatio = 0 ขณะถูกตัด 100%) ทำให้ช่องรูปไม่เคยถึง threshold .12 และไม่เคยได้ `.in` · ชั้นทับ `::after` เป็นเรื่องของการวาดล้วน observer จึงเห็นกล่องเต็ม · markup ของ A และ observer ของ C ไม่ต้องเปลี่ยน

### 3.7 Tabs ด้วย radio (tests, pricing) · 0 JS
```css
.tab-radio { position:absolute; width:1px; height:1px; opacity:0; pointer-events:none; }
.tabs { display:flex; gap:0; border-bottom:1px solid var(--border); position:relative; }
.tab { padding:14px 18px; cursor:pointer; color:var(--muted); font-family:var(--font-display); font-weight:600; position:relative; }
.tab::after { content:''; position:absolute; left:0; right:0; bottom:-1px; height:2px; background:var(--accent); transform:scaleX(0); transform-origin:left; transition: transform .22s steps(4); }
#dossier-bt:checked ~ .tabs label[for="dossier-bt"], #dossier-fw:checked ~ .tabs label[for="dossier-fw"],
#tier-1:checked ~ .tabs label[for="tier-1"], #tier-2:checked ~ .tabs label[for="tier-2"], #tier-3:checked ~ .tabs label[for="tier-3"] { color:var(--text); }
(selector ชุดเดียวกัน)::after { transform:scaleX(1); }
.tab-panel { display:none; }
#dossier-bt:checked ~ .tab-panels .panel-1, #dossier-fw:checked ~ .tab-panels .panel-2,
#tier-1:checked ~ .tab-panels .panel-1, #tier-2:checked ~ .tab-panels .panel-2, #tier-3:checked ~ .tab-panels .panel-3 { display:grid; }
.tab-radio:focus-visible ~ .tabs label { /* วง focus บน label ที่ตรงกัน ใช้ selector รายตัวเหมือนด้านบน */ outline:2px solid var(--primary); outline-offset:2px; }
@media (min-width:1100px) { .tier-tabs { display:none } .tiers .tab-radio { display:none } .tier-panels { display:grid; grid-auto-flow:column; grid-auto-columns:minmax(0,1fr); gap:1px; background:var(--border) } .tier-panel { display:block !important; background:var(--bg) } }
```
ที่ ≥ 1100: radio ของ tiers ต้อง `display:none` ด้วย ไม่ให้เป็น Tab stop ที่ไม่มี focus ให้เห็น (label ซ่อนแล้ว) · จำนวนคอลัมน์ใช้ `grid-auto-flow:column` + `grid-auto-columns` ให้ตามจำนวนแพ็กเกจที่พิมพ์จริง (2 หรือ 3) ห้าม `repeat(3, ...)` และห้าม `repeat(auto-fit, minmax(0,1fr))`
`.tab-line` (เส้นไถล) เป็น enhancement ใน `@supports selector(:has(*))` เท่านั้น ถ้าไม่ทำก็ไม่ผิดสัญญา

### 3.8 Footer moments (ดูรายละเอียดข้อ 4.3): signal line วาดตัวเอง · keycap LINE + QR · typing prompt · spotlight · watermark power-on · นาฬิกาไทย

---

## 4) Footer · "Console"

### 4.1 หลักการ
พื้น `--ink` เต็มกว้าง ไม่มีการ์ด ไม่มี CTA card ไม่มีโลโก้กลม ไม่มีเส้นไล่สีบน ไม่มีชิปกลม ไม่มี checklist แบบแถวการ์ด · 5 แถว: signal line, launch console, index grid, watermark, status bar · `overflow:hidden` บน footer (กัน watermark ดันหน้า) · เนื้อหา `position:relative; z-index:1` เหนือ dot grid + spotlight · CSS อยู่ section 41 ทั้งหมด ใช้ token `--ink-*` จาก `:root` โดยตรง (ไม่ผ่าน alias `--footer-*`)

### 4.2 Markup (C เขียนใน footer.php · คง Elementor wrapper และ `wp_footer()` เดิม)
```html
<?php /* ตัวแปร: $line (line_url ไม่ว่างและไม่ใช่ #), $go_url (หน้า go publish), $qr (footer_line_qr_img), $openchat (links_openchat_url ไม่ว่าง/ไม่ใช่ #) */ ?>
<footer class="site-footer console" id="contact" <?php if show_footer_spotlight ?>data-spotlight<?php endif ?>>

  <?php if show_footer_signal ?>
  <svg class="signal watch" viewBox="0 0 1200 40" preserveAspectRatio="none" aria-hidden="true" focusable="false">
    <path pathLength="1" vector-effect="non-scaling-stroke" d="M0 30 H230 l16 -22 l14 44 l16 -22 H640 l16 -22 l14 44 l16 -22 H1200"/>
  </svg>
  <?php endif ?>

  <div class="container console-inner">

    <section class="launch" aria-labelledby="launch-title">
      <div class="launch-main">
        <p class="console-label mono keep-case"><span aria-hidden="true">// </span>{footer_console_label}</p>
        <h2 class="launch-title" id="launch-title">{footer_headline ?: cta_title}</h2>
        <p class="launch-sub">{footer_sub ?: cta_subtitle}</p>
        <div class="launch-keys">
          <div class="keycap-wrap">
            <?php if $line ?>
            <a class="keycap" href="{$line}" target="_blank" rel="noopener" data-line-pos="footer">{icon line}<span class="keycap-text">{footer_line_text}</span></a>
            <?php if $qr ?>
            <div class="qr-flyout" aria-hidden="true"><img src="{$qr}" alt="" width="160" height="160" loading="lazy" decoding="async"></div>
            <details class="qr-mobile"><summary>{footer_qr_toggle_text}</summary><img src="{$qr}" alt="{footer_line_qr_alt}" width="200" height="200" loading="lazy" decoding="async"></details>
            <?php elseif current_user_can('customize') ?><p class="admin-hint">{footer_line_qr_note} (ข้อความนี้เห็นเฉพาะแอดมิน)</p><?php endif ?>
            <?php elseif $go_url ?>
            <a class="keycap" href="{$go_url}" data-line-pos="footer">{icon chat}<span class="keycap-text">{contact_fallback_text}</span></a>
            <?php endif ?>
          </div>
          <?php if $openchat ?><a class="textlink launch-openchat" href="{links_openchat_url}" target="_blank" rel="noopener">{links_openchat_label}{icon arrow key-arrow}</a><?php endif ?>
        </div>
      </div>
      <div class="launch-side">
        <?php if prep items ?>
        <div class="readout">
          <h3 class="readout-title mono">{footer_prep_title}</h3>
          <p class="readout-text">{footer_prep_text}</p>
          <ol class="readout-list"><li><span class="mono">01</span><span>{item}</span></li></ol>
        </div>
        <?php endif ?>
        <?php if footer_hours_text ไม่ว่าง ?>
        <div class="hours"><h3 class="mono">{footer_hours_title}</h3><p>{บรรทัดของ footer_hours_text คั่น <br>}</p></div>
        <?php endif ?>
        <?php if show_footer_console และมีบรรทัด ?>
        <div class="prompt keep-case" data-prompt data-lines="{json array of strings}" data-loops="3">
          <p class="prompt-line" aria-hidden="true"><span class="prompt-prefix mono">{footer_console_prompt}</span> <span class="prompt-typed mono" data-prompt-out></span><i class="cursor"></i></p>
          <ul class="prompt-static"><li>{แต่ละบรรทัดของ footer_console_lines}</li></ul>
        </div>
        <?php endif ?>
      </div>
    </section>

    <nav class="index" aria-label="ดัชนีเว็บไซต์">
      <div class="index-col">
        <h3 class="index-title mono">{footer_index_title}</h3>
        <ol class="index-list">
          <li><a href="{url}"><span class="idx mono">01</span><span class="index-label">{title}</span></a></li>
          <!-- จาก wp_get_nav_menu_items( location 'footer' ) เฉพาะระดับบน · fallback เมื่อไม่มีเมนู: หน้าแรก, ผลทดสอบ (/forward-test/), แพ็กเกจ, วิธีติดตั้ง, บทความ (page_for_posts ถ้ามี), ติดต่อ (/go/) -->
        </ol>
      </div>
      <div class="index-col">
        <h3 class="index-title mono">{footer_channels_title}</h3>
        <ul class="index-list channels">
          <li><a class="switch-row" href="{$line}" target="_blank" rel="noopener" data-line-pos="footer-index"><span class="index-label">{footer_line_text}</span><i class="switch" aria-hidden="true"></i></a></li>
          <!-- OpenChat (links_openchat_label), Facebook (footer_facebook_text), Instagram, TikTok, YouTube (ป้ายคงที่), อีเมล (footer_email_text · mailto + antispambot) · เฉพาะที่มีค่า · คอลัมน์หายทั้งคอลัมน์ถ้าไม่มีสักช่องทาง -->
        </ul>
      </div>
      <div class="index-col">
        <h3 class="index-title mono">{footer_docs_title}</h3>
        <ul class="index-list">
          <!-- about, privacy-policy, terms-of-use เฉพาะที่ publish (guard เดิม) · data-deletion เฉพาะที่ publish ป้าย 'ขอลบข้อมูล' · risk-disclosure ป้าย footer_risk_link เสมอ -->
        </ul>
      </div>
      <div class="index-col">
        <h3 class="index-title mono">{footer_spec_title}</h3>
        <dl class="sheet sheet--ink keep-case"><div class="sheet-row"><dt class="mono">{ป้าย}</dt><dd>{ค่า}</dd></div></dl>   <!-- footer_spec_items ป้าย|ค่า · keep-case เพราะค่ามี MetaTrader 5 / Windows (ข้อ 0.2) -->
      </div>
    </nav>

  </div>

  <?php if show_footer_watermark ?>
  <p class="watermark keep-case watch" aria-hidden="true"><span class="wm-ea">{ส่วนก่อนตัวเลขตัวแรก}</span><span class="wm-num">{ตั้งแต่ตัวเลขตัวแรก}</span></p>
  <?php endif ?>

  <div class="statusbar">
    <div class="container statusbar-inner">
      <p class="status-copy mono keep-case">&copy; {gmdate Y} {bloginfo name} · {footer_copyright_text}</p>
      <p class="status-text keep-case">{footer_status_text}</p>   <!-- keep-case: ค่าเริ่มต้นมี Expert Advisor / MetaTrader 5 (ข้อ 0.2) -->
      <?php if show_footer_clock ?>
      <p class="status-clock mono keep-case"><span class="clock-label">{footer_clock_label}</span> <time data-clock-out data-tz="Asia/Bangkok" datetime="{wp_date('c', null, new DateTimeZone('Asia/Bangkok'))}">{wp_date('H:i', null, new DateTimeZone('Asia/Bangkok'))}</time></p>
      <?php endif ?>
      <a class="status-top mono" href="#top">{footer_backtop_text} <span aria-hidden="true">▲</span></a>
    </div>
  </div>
</footer>
```
หลัง `</footer>` (คงลำดับเดิม): `.mobile-app-nav` (ดู 4.5), `.float-line` / `.line-fab`, `.cookie-consent` (ไม่แตะ), `wp_footer()`

### 4.3 พฤติกรรม
**Signal line (CSS)**
```css
.signal { display:block; width:100%; height:40px; }
.signal path { fill:none; stroke:var(--ink-accent); stroke-width:2; stroke-dasharray:1; stroke-dashoffset:1; }
@supports (animation-timeline: view()) { .signal path { animation: signal-draw linear both; animation-timeline: view(); animation-range: entry 0% entry 90%; } }
@supports not (animation-timeline: view()) { .signal.in path { stroke-dashoffset:0; transition: stroke-dashoffset 1.2s linear; } }
@keyframes signal-draw { from { stroke-dashoffset:1 } to { stroke-dashoffset:0 } }
.no-js .signal path, @media (prefers-reduced-motion: reduce) → stroke-dashoffset:0
```
**Keycap (CSS, graft SWITCHBOARD)**
```css
.keycap { display:inline-grid; grid-auto-flow:column; align-items:center; gap:12px; min-height:64px; padding:0 28px; border-radius:6px;
  background:var(--ink-accent); color:var(--ink-deep); font:700 1.05rem var(--font-display);
  box-shadow: 0 6px 0 var(--primary-deep), 0 8px 0 rgba(0,0,0,.35), 0 20px 34px rgba(0,0,0,.45);
  transition: transform .08s, box-shadow .08s, filter .15s; }
.keycap:hover { filter: brightness(1.06); }              /* ไม่ยก */
.keycap:active { transform: translateY(6px); box-shadow: 0 0 0 var(--primary-deep), 0 2px 0 rgba(0,0,0,.35); }
.keycap:focus-visible { outline:2px solid var(--ink-accent); outline-offset:4px; }
.keycap-wrap { position:relative; display:inline-block; }
.qr-flyout { position:absolute; left:calc(100% + 12px); top:0; width:0; overflow:hidden; background:#fff; border-radius:6px; transition: width .28s steps(5); }
.qr-flyout img { display:block; width:160px; height:160px; }
@media (hover:hover) { .keycap-wrap:hover .qr-flyout, .keycap-wrap:focus-within .qr-flyout { width:160px; } .qr-mobile { display:none } }
@media (hover:none) { .qr-flyout { display:none } .qr-mobile { margin-top:14px; color:var(--ink-muted) } .qr-mobile img { background:#fff; padding:8px; border-radius:6px } }
```
ปุ่มมองเห็นตั้งแต่ paint แรกเสมอ ไม่ผูกกับอนิเมชันใด

**Typing prompt (JS main.js ~0.5 KB + typewriter)**
```
p = $('[data-prompt]'); lines = JSON.parse(p.dataset.lines); out = p.querySelector('[data-prompt-out]'); loops = +p.dataset.loops || 3
CSS: .js .prompt-static ใช้แบบ sr-only (position:absolute; width/height 1px; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0 · ห้าม display:none เพราะ .prompt-line เป็น aria-hidden ต้องมี twin ที่อยู่ใน accessibility tree ตามข้อ 7.2) · .cursor { animation: term-blink 1s steps(2) infinite }
reduced → out.textContent = lines.join(' · '); return
reserve(): วัดทุกบรรทัดจริง (ใส่ทีละบรรทัดใน out แล้วอ่าน p.prompt-line offsetHeight สูงสุด) → p.prompt-line.style.minHeight · เรียกตอน init, document.fonts.ready และ resize (rAF throttle) · กัน .index/.statusbar ขยับตอนบรรทัดพันบน 375px (CLS)
ea2000.observe(p, () => cycle())
cycle(): typewriter(out, [lines[i]], { cps: 30, onDone: () => setTimeout(() => { i = (i+1) % lines.length; if (++count < loops * lines.length) { out.textContent=''; cycle() } }, 4000) })
no-JS: .prompt-static แสดง, .prompt-line ซ่อน (.no-js .prompt-line { display:none })
```
**Spotlight (JS main.js ~0.5 KB)**
```
f = $('.site-footer[data-spotlight]'); if !f || !ea2000.fine || ea2000.reduced: return
pointermove (passive) → rAF → r = f.getBoundingClientRect(); f.style.setProperty('--mx', (e.clientX - r.left) + 'px'); ('--my' เช่นกัน)
pointerleave → ตั้ง --mx/--my เป็น -999px
CSS: .site-footer.console { position:relative; background-color:var(--ink); background-image: var(--dot-grid-ink); background-size: var(--dot-pitch) var(--dot-pitch); overflow:hidden; }
     .site-footer.console::before { content:''; position:absolute; inset:0; pointer-events:none; background: radial-gradient(260px at var(--mx,-999px) var(--my,-999px), rgba(var(--ink-accent-rgb), .10), transparent 70%); }
```
**Watermark (CSS + `.in` fallback)**
```css
.watermark { position:relative; z-index:0; margin:0; padding:0 0 .05em; text-align:center; white-space:nowrap; line-height:.8; font:700 clamp(72px, 22vw, 300px) var(--font-display); color:transparent; -webkit-text-stroke:1px var(--ink-border-strong); opacity:1; }
.wm-ea { -webkit-text-stroke-color: var(--steel); } .wm-num { -webkit-text-stroke-color: var(--ink-accent); }
@supports not (-webkit-text-stroke: 1px #000) { .watermark { color:var(--ink-chip) } }
@supports (animation-timeline: view()) { .js .watermark { animation: wm-on linear both; animation-timeline: view(); animation-range: entry 0% entry 60%; } }
@supports not (animation-timeline: view()) { .js .watermark { opacity:0; transition: opacity .6s steps(4) } .js .watermark.in { opacity:1 } }
@keyframes wm-on { 0% {opacity:0} 30% {opacity:.3} 45% {opacity:0} 70% {opacity:.6} 100% {opacity:1} }
.no-js .watermark, @media (prefers-reduced-motion: reduce) .watermark → opacity:1; animation:none
```
**นาฬิกาไทย (JS main.js ~0.4 KB, graft BENTO แบบตัดทอน)**: `t = $('[data-clock-out]')`; `fmt = new Intl.DateTimeFormat('th-TH', { timeZone: t.dataset.tz, hour:'2-digit', minute:'2-digit', hour12:false })`; `tick(): t.textContent = fmt.format(new Date())`; tick ทันที แล้ว setTimeout ให้ตรงนาทีถัดไป จากนั้น setInterval 60000 · ไม่มีวินาที ไม่มี aria-live ไม่มีข้อความตลาด · PHP พิมพ์เวลาเริ่มต้นไว้แล้ว (แคชอาจค้างเป็นนาที รับได้)

**Switch glyph บนลิงก์ช่องทาง (CSS, graft SWITCHBOARD)**: `.switch { width:30px; height:16px; border-radius:2px; background:var(--ink-chip); position:relative }` `.switch::after { content:''; position:absolute; top:3px; left:3px; width:10px; height:10px; background:var(--ink-muted); transition: translate .2s steps(3), background .2s }` `.switch-row:hover .switch::after, .switch-row:focus-visible .switch::after { translate:14px 0; background:var(--ink-accent); box-shadow:0 0 8px var(--ink-accent) }` · glyph เป็น aria-hidden ลิงก์ยังเป็น `<a>` ธรรมดา

### 4.4 Keys ของ footer
**เดิมที่ยังใช้**: `footer_line_text`, `footer_facebook_text`, `footer_email_text`, `footer_prep_title`, `footer_prep_text`, `footer_prep_items`, `footer_risk_link`, `line_url`, `facebook_url`, `instagram_url`, `tiktok_url`, `youtube_url`, `contact_email`, `links_openchat_url`, `links_openchat_label`, `cta_title`, `cta_subtitle` (fallback), `brand_*` (ไม่แสดงใน footer แล้ว), `show_mobile_nav`, `mobile_nav_*`, `show_float_line`, `float_line_text`, `contact_fallback_text` (ใหม่ในข้อ 2.1)
**เลิกอ่านในเทมเพลตแต่คงใน defaults**: `footer_kicker`, `footer_cta_title`, `footer_cta_text`, `footer_tagline`, `show_cta`, `cta_btn_text`
**ใหม่** (A เพิ่มใน defaults + section 13 ของ customizer เขียนใหม่ทั้ง section):

| key | type | default |
|---|---|---|
| `footer_console_label` | text | `ติดต่อทีมงาน` |
| `footer_headline` | text | `` (ว่าง = ใช้ `cta_title`) |
| `footer_sub` | textarea | `` (ว่าง = ใช้ `cta_subtitle`) |
| `footer_line_qr_img` | image | `` |
| `footer_line_qr_alt` | text | `QR สำหรับเพิ่มเพื่อน LINE Official Account ของ EA2000` |
| `footer_line_qr_note` | text | `อัปโหลด QR ของ LINE OA ขนาด 600x600 px พื้นขาว ที่ ปรับแต่ง : Footer` |
| `footer_qr_toggle_text` | text | `แสดง QR` |
| `footer_hours_title` | text | `เวลาตอบแชท` |
| `footer_hours_text` | textarea | `` (ว่าง = ซ่อนแถว · เจ้าของกรอกเอง ไม่มีสัญญาบริการฝังในโค้ด) |
| `show_footer_console` | checkbox | true |
| `footer_console_prompt` | text | `ea2000@line:~$` |
| `footer_console_lines` | textarea | `ทีมงานตอบแชทด้วยตัวเอง` / `แจ้งเวอร์ชัน MT5 และโบรกเกอร์ที่ใช้ เพื่อให้ช่วยติดตั้งได้เร็วขึ้น` / `อ่านประกาศความเสี่ยงก่อนตัดสินใจทุกครั้ง` |
| `footer_index_title` | text | `ดัชนีหน้า` |
| `footer_channels_title` | text | `ช่องทาง` |
| `footer_docs_title` | text | `เอกสาร` |
| `footer_spec_title` | text | `ข้อมูลระบบ` |
| `footer_spec_items` | textarea | `แพลตฟอร์ม|MetaTrader 5` / `ระบบปฏิบัติการ|Windows หรือ VPS` / `สินทรัพย์|หลายคู่เงิน` / `การส่งมอบ|ไฟล์ EA และคู่มือ` |
| `show_footer_watermark` | checkbox | true |
| `footer_watermark_text` | text | `EA2000` |
| `footer_status_text` | text | `Expert Advisor สำหรับ MetaTrader 5` |
| `footer_copyright_text` | text | `สงวนลิขสิทธิ์` |
| `show_footer_clock` | checkbox | true |
| `footer_clock_label` | text | `เวลาไทย` |
| `footer_backtop_text` | text | `กลับด้านบน` |
| `show_footer_spotlight` | checkbox | true |
| `show_footer_signal` | checkbox | true |

### 4.5 Fixed UI (C ใน footer.php · B ใน section 41)
- **Mobile dock**: **เขียนใหม่ทั้งบล็อก 10 ก.ย. 2026 (v2.3.0) ดู CLAUDE.md ข้อ 29** · สัญญา class ชุดใหม่คือ `.mobile-app-nav.dock` > `.dock-rail` + `.dock-lamp` + `.dock-keys` > `a.dock-key` > `.dock-cap` ( `.dock-glyph` + `.dock-led` ) + `.dock-label` และมี `div.dock-spacer` ต่อท้าย nav · สถานะ `.is-lit` `.is-active` `.is-action` `.is-press` `.is-going` `.is-slim` · **คลาสเดิม `.mobile-app-nav-item` `.mobile-app-nav-icon` `.is-pressing` เลิกใช้แล้ว** · หา active ฝั่ง PHP จึงถูกต้องตั้งแต่ HTML ที่เสิร์ฟ ไม่ต้องรอ JS และมี `aria-current="page"` · เว้นที่ท้ายหน้าด้วย `.dock-spacer` ไม่ใช่ `body.has-mobile-app-nav` แล้ว · `data-line-pos="dock"` บนลิงก์ LINE ยังอยู่
- **Desktop `.line-fab`**: สี่เหลี่ยม 56px radius 4px มุมวงเล็บ (ใช้ `.key` ::before/::after เดียวกัน) พื้น `--primary` ไอคอน LINE ขาว · `data-line-pos="fab"` · `.float-line` (โหมดมีข้อความ) เป็น `.key.key-line` ลอย
- **Cookie bar**: ไม่แตะ

---

## 5) CSS · tokens และรายการ section (B)

### 5.1 Token ใหม่ใน `:root` (section 1)
```css
--font-mono: ui-monospace, "Cascadia Mono", "SF Mono", Consolas, Menlo, "Bai Jamjuree", "Noto Sans Thai", monospace; /* ไทยตกไป Bai Jamjuree */
--hud: var(--primary);            /* สีวงเล็บบนพื้นสว่าง · บนพื้นมืดตั้ง --hud: var(--ink-accent) ที่ scope */
--hud-size: 24px;
--dot-grid: radial-gradient(rgba(11, 18, 16, 0.12) 1px, transparent 1px);
--dot-grid-ink: radial-gradient(rgba(242, 247, 243, 0.08) 1px, transparent 1px);
--dot-pitch: 24px;
--steel-rgb: 138, 145, 153;
--key-radius: 4px;
--ease-mech: steps(4, end);
--band-pad: clamp(64px, 9vw, 112px);
--rail-w: 40px;
```
ห้ามเพิ่ม token สีใหม่ · ใช้คู่ที่ตรวจ contrast แล้ว: `--text`/`--bg` `--muted`/`--bg` `--muted`/`--surface` `--ink-text`/`--ink` (15.6:1) `--ink-muted`/`--ink` (9.7:1) `--ink-deep`/`--ink-accent` (12.6:1) `--warn-text`/`--warn-bg`

### 5.2 Header (แก้เล็กน้อยใน section 39 · เจ้าของสั่งคงโทน)
- แทน `.nav-list a::after` (แถบไล่สี) ด้วย marker วงเล็บ: `.nav-list > li > a::before { content:'[' } ::after { content:']' }` สี `--header-accent` opacity 0 → 1 ตอน hover/focus/current (ไม่ขยับ layout: ใช้ `position:absolute` ซ้ายขวา `-.6em`) · ลบกฎ gradient ที่ 578 และ 6465
- ไม่แตะอย่างอื่นของ header

### 5.3 Section 40 · `หน้าแรก v3 · Control Room` (ต่อท้าย section 39)
เรียงย่อยตามลำดับนี้ ใส่คอมเมนต์หัวข้อทุกก้อน:
1. `40.1` ฐาน: `.home-v3` (`overflow:visible`), `.mono`, `.ch`, `.ch-head` (ซ้ายชิด · `.ch-index` มี `.ch-n` เขียว `--primary`), `.band-surface`, `.ch-actions`, `.ch-foot`, `.notice-row` (hairline บน + ไอคอน warn), `.margin-note`, `.sr-only` มีอยู่แล้ว
2. `40.2` ปุ่ม `.key*`, `.textlink`, `.key-arrow` (เลื่อน 4px ตอน hover)
3. `40.3` HUD: `.hud-frame`, `.hud-c`, `.fig`, `.img-slot`/`.media-frame` v3 (ย้ายกฎ `.img-slot*`, `.media-frame` จาก 38b มาไว้ที่นี่แล้วลบของเดิม · radius เปลี่ยนเป็น 0 ในกรอบ HUD), `.wipe`
4. `40.4` Rail: `.rail` (`position:sticky; top:calc(var(--header-h) + 24px); height:0; overflow:visible; z-index:60; width:var(--rail-w); margin-left:calc((100vw - 1120px) / 2 - 52px)` · แสดงเฉพาะ `@media (min-width:1240px)`), `.rail-counter`, `.rail-track` (2px สูง 160px), `.rail-fill`, `.rail-list`, `.rail-bar` (`position:sticky; top:var(--header-h); height:2px; z-index:60; background:var(--border)` + `.rail-bar-fill` scaleX) แสดงเฉพาะ `< 1240px`
5. `40.5` Boot: `.boot` (`padding: var(--band-pad) 0 0`, `::before` dot grid + mask), `.boot-grid` (12 col), `.boot-copy`, `.boot-prefix`, `.boot-title` (clamp(2.4rem, 5.6vw, 4.4rem)), `.boot-desc`, `.boot-actions`, `.boot-note`, `.hud` (grid auto-fit minmax(150px, 1fr), `dt` `--muted` .7rem, `dd` `--text` .85rem, hairline บน, `position:relative`), `.hud::after` (แถบกวาด · ไม่มี `.hud-sweep` element), `.boot-visual` (`margin-bottom:-48px` ที่ ≥ 960 · `.ch-what` มี `padding-top: calc(var(--band-pad) + 48px)` ที่ ≥ 960)
6. `40.6` Brief: `.brief-grid`, `.sheet`, `.sheet-row` (grid 140px 1fr · hairline), `.principle` (border-left 2px `--primary`, padding-left 16px)
7. `40.7` Diag: `.diag-grid`, `.diag`, `.diag-row` (grid 40px 14px 1fr · hairline), `.led`, `.strike`, `.diag-resolved`
8. `40.8` How: `.how-grid`, `.term*`, `.ledger*`, `.req*`, `.mono-marks .mark` (สี `--primary`)
9. `40.9` Modules: `.modules`, `.module*`, `.scan`
10. `40.10` Tabs: `.tab-radio`, `.tabs`, `.tab`, `.tab-idx`, `.tab-rec`, `.tab-panels`, `.tab-panel`, dossier (`.dossier-media`, `.dossier-body`), tiers (`.tiers`, `.tier-*` · ราคา Chakra Petch 3.2rem · ที่ ≥ 1100 `.tier-panels` ใช้ `grid-auto-flow:column; grid-auto-columns:minmax(0,1fr)` ตามจำนวนแพ็กเกจ และ `.tiers .tab-radio { display:none }`)
11. `40.11` Film: `.film` (`display:flex; gap:16px; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; padding-bottom:4px` + `::-webkit-scrollbar{display:none}` + `:focus-visible` outline), `.frame` (`flex:0 0 min(420px, 84%); scroll-snap-align:start` · รูพรุนฟิล์ม: `border-top:10px solid transparent; background-image: radial-gradient(...)` บน `::before`)
12. `40.12` FAQ: `.qlog` (grid 2 col ≥ 960), `.q`, `.q-sum` (grid 40px 1fr 22px), `.q-idx`, `.q-mark` (สี่เหลี่ยม 22px ขอบ 1px วาด `+` ด้วย ::before/::after · `details[open]` หมุน 45deg), `.q-ans` (padding-left 40px, `p` .95rem)
13. `40.13` Hazard: `.hazard` (`background:var(--warn-bg); color:var(--warn-text); border-top/bottom:12px solid; border-image: repeating-linear-gradient(45deg, var(--warn-border) 0 10px, transparent 10px 20px) 12`), `.hazard-inner` (max-width 860), `.hazard-stamp` (กล่อง hairline `--warn-border` padding 2px 8px), `.hazard-title`, `.hazard-text` (1.125rem), `.key-warn`
14. `40.14` Responsive ของ section 40 ทั้งหมด (1240, 1100, 960, 760, 640) · ไม่กระจายไปไว้ section 24
15. `40.15` `.no-js` และ `prefers-reduced-motion` ของ section 40 รวมไว้ท้าย section

### 5.4 Section 41 · `แถบท้ายเว็บ v2 · Console + dock + fab` (ต่อจาก 40)
`.site-footer.console` และลูกทั้งหมดในข้อ 4, `.mobile-app-nav.dock` (คอลัมน์ใช้ `grid-auto-flow:column; grid-auto-columns:minmax(0,1fr)` ให้ตามจำนวนปุ่มจริง 4 หรือ 5 · ห้าม `repeat(5, ...)`), `.line-fab`, `.float-line` v2, responsive (960: launch 1 col · index 2 col · 640: index 1 col, statusbar wrap, watermark clamp) · `.no-js`/reduced ของ footer ไว้ท้าย section

### 5.5 การถอนกฎเก่า (ทำใน commit เดียวกัน)
ก่อนลบทุก selector ให้ `grep -l` ใน `ea2000/*.php ea2000/inc/*.php` **ยกเว้น** `front-page.php` และ `footer.php` (สองไฟล์นี้ถือว่าใช้เฉพาะคลาสในข้อ 0.2 และข้อ 2/4 เท่านั้น) · พบที่อื่น = เก็บ · ไม่พบ = ลบ
- ลบทั้ง section: **20 Footer**, **17 FAQ** (`.faq-*` ใช้เฉพาะหน้าแรก), **18 Risk warning** (`.risk-box`), **9 Cards** ยกเว้น `.card` ฐานที่ single.php / template-links / template-pricing ใช้ (ลบ `.pain-card`, `.feat-card`, `.review-card`, `.card-icon` ถ้าไม่มีใครใช้), **8 Hero** ยกเว้น `.ember*` + keyframes ที่ `ea2000_page_hero()` / `ea2000_line_cta()` ใช้ (ย้ายไป section 28), **34 / 35 / 37** ส่วนที่รับใช้บล็อกที่ถูกลบ (highlight, live-strip, control, hub, assurance, mid-cta, team, gallery, fit, perf บนหน้าแรก), **38b** ทั้งก้อน (หลังย้าย `.img-slot*` / `.media-frame` ไป 40.3), **39 ครึ่ง footer** (`.site-footer`, `.footer-*`, `--footer-*` alias ใน :root ลบได้), **กฎ dock ใน section "Cookie consent" (บรรทัด 2158 ถึง 2280)** และกฎ `.float-line`/`.line-fab` ใน 21 (แทนด้วย 41)
- section 24 responsive: ลบกฎของ selector ที่หายไป · section 19 CTA เก็บ (ใช้ใน `ea2000_line_cta()`) · section 15 pricing เก็บ (template-pricing.php)
- ยืนยันหลังลบ: `/go/` (template-links.php ไม่เรียก header/footer) และ inner page (single, pricing, install, risk, backtest, forward, 404) render เหมือนเดิม
- ผลลัพธ์ที่คาด: style.css เล็กลงสุทธิแม้เพิ่ม 40 + 41 (ราว 22 KB raw) · **bump `Version:` ใน header ของ style.css** (1.0.3 → 1.1.0)

---

## 6) JavaScript (C)

### 6.1 main.js · เพิ่ม (global ทุกหน้า · วัดจริง 9 ก.ย. 2026: +6.7 KB raw · 12,503 → 19,231 B)
| โมดูล | hook | หมายเหตุ |
|---|---|---|
| `ea2000` namespace | `window.ea2000 = { reduced, fine, observe, typewriter }` ประกาศต้น IIFE | ต้องมีก่อนโค้ด reveal เดิม |
| observer | เปลี่ยน selector reveal เดิมเป็น `'.reveal, .watch'` · `observe(el, cb, { threshold, repeat })` ใช้ IO อีกตัวเมื่อ threshold ต่างจากค่าเริ่มต้น | reduced/no IO → cb ทันที |
| `typewriter(out, lines, opts)` | `lines` = array ของ string หรือ `{t, c}` · `opts.cps` (ตัวอักษร/วินาที default 32), `opts.linePause` ms, `opts.wrap` (`'span.term-line'` → สร้าง span ต่อบรรทัด ใส่ class `c`), `opts.onDone` · ใช้ rAF + timestamp ไม่ใช่ setInterval · `Intl.Segmenter('th', {granularity:'grapheme'})` ถ้ามี ไม่มีก็ per code unit · คืน `{ stop }` · reduced → เขียนทั้งหมดทันทีแล้ว onDone | ~2.4 KB (รวม observe ~1.4 KB) |
| footer prompt | `[data-prompt]` (4.3) · รวม reserve() จองความสูงบรรทัดที่สูงที่สุด | ~2.0 KB |
| footer spotlight | `.site-footer[data-spotlight]` (4.3) | ~0.7 KB |
| clock | `[data-clock-out]` (4.3) | ~0.6 KB |
| LINE event | เพิ่ม `link_pos: link.dataset.linePos || ''` ใน payload `line_click` เดิม | +0.1 KB |
| dock | ไม่แก้ (ยังหา `.mobile-app-nav`) | |

### 6.2 home.js · ไฟล์ใหม่ (หน้าแรกเท่านั้น · วัดจริง 9 ก.ย. 2026: 4,717 B raw ≈ 4.6 KB)
IIFE เดียว guard `if (!document.querySelector('.home-v3')) return;`
| โมดูล | hook | ขนาด |
|---|---|---|
| boot | `[data-hud-frame]`, `[data-hud]`, `.hud-val[data-text]` (3.1) | 1.1 KB |
| terminal | `[data-term]` (3.2) · รวมการจอง min-height ก่อนเล่น | 1.5 KB |
| rail | `.rail`, `[data-rail-n]`, `[data-rail-link]`, `section[data-chapter]`, `--p` fallback (3.4) | 1.7 KB |
| glue | | 0.3 KB |

**A ต้อง enqueue** ใน `ea2000_assets()`:
```php
if ( is_front_page() ) {
	$home_path = get_template_directory() . '/assets/js/home.js';
	wp_enqueue_script( 'ea2000-home', get_template_directory_uri() . '/assets/js/home.js', array( 'ea2000-main' ), file_exists( $home_path ) ? filemtime( $home_path ) : EA2000_VERSION, true );
}
```
งบรวม (วัดจริง 9 ก.ย. 2026 หลังแก้ CLS ข้อ 3.2 และ 4.3): main.js 12,503 → 19,231 B (12.2 → 18.8 KB) · home.js 4,717 B (4.6 KB) · รวมเพิ่ม 11,445 B ≈ 11.2 KB raw (เพดาน +15 KB = 15,360 B เหนือ 12,503 B เดิม) · ตัวเลขประมาณการเดิม (+3.0 / 3.4 / 6.4 KB) ถือเป็นประวัติ · ไม่มี canvas ไม่มี scroll-jacking ไม่มี listener บน `pointermove` นอก footer

---

## 7) Acceptance checklist

### 7.1 กฎเนื้อหา
- [ ] `grep -riE "ทอง|xauusd|gold|martingale|grid|stop loss|\bSL\b|รับประกัน|ทุกออเดอร์" ea2000/functions.php ea2000/front-page.php ea2000/footer.php` = 0 ในค่า default และ template (ยกเว้น `pricing_confirmed` และ risk text ที่บอกว่า "ไม่รับประกัน")
- [ ] ไม่มีตัวเลขในหน้าแรกที่อ่านเป็นผลเทรด: HUD strip, terminal, spec sheet, footer spec ไม่มี `%` หรือสกุลเงินยกเว้นราคาแพ็กเกจ
- [ ] ไม่มี timestamp ใน terminal · ไม่มี canvas · ไม่มี ticker · ไม่มี risk dial
- [ ] `tests_note`, `risk_text`, `hero_note`, `risk_margin_note` แสดงครบทุกคำ ไม่พับ ไม่ตัด
- [ ] ปุ่ม LINE ทุกปุ่มซ่อนหรือ fallback `/go/` เมื่อ `line_url` ว่างหรือ `#` · ไม่มีปุ่มใดชี้ `#`
- [ ] ช่องรูปว่างทุกช่องแสดง note บอกเจ้าของ (สาธารณะ) · QR placeholder เห็นเฉพาะแอดมิน
- [ ] stamp อังกฤษบนหน้ามีแค่ prompt (`ea2000@mt5:~$`, `ea2000@line:~$`) และ `NOTICE`
- [ ] ค่า `footer_hours_text` ว่างเป็นค่าเริ่มต้น และแถวหายเมื่อว่าง

### 7.2 A11y
- [ ] `.term`, `.prompt-line`, `.watermark`, `.signal`, `.rail-counter`, `.hud-c`, `.led`, `.scan`, `.switch` เป็น `aria-hidden` และมี twin ที่อ่านได้ (ledger, prompt-static, sr-only)
- [ ] radio ทุกชุดใช้ label จริง กด Tab แล้วเห็น focus บนแท็บ ลูกศรสลับได้ · details/summary เปิดปิดด้วยคีย์บอร์ด
- [ ] `.film` โฟกัสได้ (`role="group"` + `tabindex=0` + aria-label · ไม่มี role = aria-label ถูกทิ้ง) และเลื่อนด้วยลูกศร · radio ของ tiers ไม่เป็น Tab stop ที่ ≥ 1100 (`display:none`)
- [ ] focus ring บนพื้นมืดเป็น `--ink-accent` · บนพื้นสว่างเป็น `--primary` · ไม่มี `outline:none` ที่ไม่มีของแทน
- [ ] คอนทราสต์ทุกคู่ตามข้อ 5.1 · ข้อความบน `--warn-bg` ใช้ `--warn-text`
- [ ] `prefers-reduced-motion`: ไม่มี decode, ไม่มี typewriter (ข้อความครบทันที), ไม่มี spotlight, ไม่มี sweep/scan, wipe/strike/watermark/signal อยู่สถานะจบ
- [ ] ปิด JS (`html.no-js`): เนื้อหาครบทุกบท วงเล็บ HUD เต็ม รูปไม่ถูก clip ledger เห็น prompt-static เห็น รางแสดง 00/09 นิ่ง

### 7.3 มือถือ / เลย์เอาต์
- [ ] headless Chrome ≥ 500px screenshot + emulation 375px และ 360px: `document.documentElement.scrollWidth === window.innerWidth` ที่ทุกจุด: hero (ไม่มี overhang ต่ำกว่า 960), `.film`, `.modules`, `.tiers` ตาราง 3 คอลัมน์ (≥ 1100 เท่านั้น), footer `.watermark`, `.index` 1 คอลัมน์ · ห้ามใช้ query `?w=`/`?m=`
- [ ] dock ไม่ทับ hazard band และ statusbar (padding-bottom body 92px คงอยู่) · ปุ่ม LINE กลาง dock กดได้ 44px ขึ้นไป
- [ ] header dropdown/drawer มือถือยังทำงาน (section 39 ไม่ถูกแตะนอกจาก hover marker)
- [ ] `/go/` และ inner pages ทั้ง 7 render เหมือนก่อนแก้ (เทียบ screenshot)

### 7.4 ประสิทธิภาพ
- [ ] LCP element (≥ 961px) = `img` ในกล่องสินค้า · มี `<link rel="preload">` 1 แท็ก ตรงกับ `src` และมี `media="(min-width: 961px)"` (เดสก์ท็อปเท่านั้น · บนมือถือกล่องอยู่ใต้ fold LCP คือข้อความ) · `img` ไม่มี `fetchpriority` · ไม่มี opacity/clip บน hero ตอนแรก
- [ ] JS: `main.js ≤ 19.5 KB raw` (วัด 19,231 B), `home.js ≤ 5 KB raw` (วัด 4,717 B) · รวมเพิ่มจาก 12,503 B เดิมไม่เกิน +15 KB (วัด +11,445 B) · home.js โหลดเฉพาะหน้าแรก (ตรวจ view-source หน้า /pricing/)
- [ ] ไม่มี layout shift จาก typewriter: `.term-out` ได้ `min-height` เท่าความสูงข้อความเต็มก่อนเล่นทุกครั้ง (3.2) · `p.prompt-line` ได้ `min-height` เท่าบรรทัดที่สูงที่สุดหลังวัดจริง (4.3) · ตรวจด้วย PerformanceObserver layout-shift ที่ 375px และ 360px: ไม่มี entry ที่ source เป็น `.how-ledger` หรือ `.index`
- [ ] CSS: style.css เล็กกว่าเดิมสุทธิ · ไม่มี `backdrop-filter`, `filter: blur` ในหน้าแรก/footer
- [ ] ไม่มี layout shift จาก: QR flyout (absolute), rail (height 0), watermark (overflow hidden), นาฬิกา (tabular-nums)
- [ ] pointermove listener มีที่ footer เดียว และไม่ bind บน touch/reduced

### 7.5 ไม่เหมือน FENIX
- [ ] `grep -E "class=\"[^\"]*\b(card|sec-head|kicker|badge|price-flag|ember|reveal|cta|risk-box|footer-cta|footer-main|footer-prep|grid-3|grid-4|btn)\b" ea2000/front-page.php ea2000/footer.php` = 0
- [ ] ไม่มี blur blob, candlestick SVG, float loop, pill button, icon tile, centered kicker/H2/underline, CTA band แยก, card-stack footer
- [ ] main.js ไม่ byte-identical กับ FENIX (มี namespace + โมดูลใหม่) · home.js เป็นไฟล์ใหม่
- [ ] ข้อความไม่ซ้ำคำต่อคำ: intersect ค่า string ใน `ea2000_defaults()` กับ `fenix_defaults()` ของ FENIX (อ่านอย่างเดียวจาก `D:\EA VIDEO\fenix-pro-repo`) เฉพาะ key ที่ผู้เข้าชมเห็น (title/desc/note/tag/items/btn ทุกบล็อกและ footer) ความยาว 14 ตัวอักษรขึ้นไป · ต้อง = 0 (ยกเว้น URL, ชื่อไฟล์, และ key ที่ไม่ได้พิมพ์ในเทมเพลตอีกแล้ว) · ตรวจซ้ำบนเว็บสดด้วย curl ทั้งสองโดเมนว่าไม่มี string เดียวกันโผล่ทั้งคู่
- [ ] เปิดหน้าแรก FENIX กับ EA2000 คู่กันที่ 1280px และ 375px: ไม่มีบล็อกใดที่วางเหมือนกันทั้ง layout และ motion

### 7.6 ก่อน push (ทุกคน)
- [ ] `php -l` ทุกไฟล์ PHP ด้วย PHP CLI ใน CLAUDE.md ข้อ 6 · LF ทุกไฟล์ · ไม่มี em/en dash (`grep -P "[\x{2013}\x{2014}]"` = 0)
- [ ] `style.css` `Version: 1.1.0` · push แล้วตรวจ `GET /wp-json/wp/v2/themes?status=active&_fields=version` = 1.1.0
- [ ] หลัง deploy ส่ง REST `ea2000/v1/mods` รีเซ็ต kicker ที่เคยถูกตั้งค่าอังกฤษ: `{"what_kicker":null,"how_kicker":null,"tests_kicker":null,"install_kicker":null,"hero_badge":null}` แล้วเช็กว่าค่าใหม่ภาษาไทยขึ้น · purge Cloudflare หน้าแรก
- [ ] ตรวจ FAQPage + SoftwareApplication ด้วย Rich Results Test ว่ายังออกครบ (ไม่แตะ `ea2000_faq_schema()` / `ea2000_schema_jsonld()`)

---

## 8) Decisions log (สิ่งที่ตัดหรือปรับจากแนวคิดต้นทาง)

- **10 ก.ย. 2026 · กล่องสินค้าใน hero จัดกึ่งกลางแนวตั้งแทนการชิดล่าง** (v2.0.1) · เดิม `.boot-visual` เป็น `align-self: end` บวก `margin-bottom: -48px` ให้กล่องยื่นลงไปทับบล็อกถัดไป · ผลคือบนจอ 1900x950 ขอบบนของกล่องอยู่ต่ำกว่าหัวเรื่องราว 220px และก้นกล่องตกขอบจอ เจ้าของทักว่ากล่องอยู่ต่ำไป · เปลี่ยนเป็น `align-self: center` และ `margin: 0` แล้วคืน `.ch-what` เป็น `padding-top: var(--band-pad)` ตามปกติ · วัดที่ 1440x900 ขอบบนขยับจาก 405 เป็น 270 · ลูกเล่นยื่นทับบล็อกถัดไปถือว่ายกเลิก

| การตัดสิน | เหตุผล |
|---|---|
| ตัด demo candlestick canvas ทิ้งทั้งหมด (ไม่มี key `show_tests_demo`) | judges ทั้ง 3: กราฟที่วิ่งบนหน้าขาย EA ถูกอ่านเป็นผลเทรดแม้ติดป้าย, ซ้ำลาย candlestick ของ FENIX, กิน 2.6 KB |
| จอมืดในเนื้อหาเหลือ 1 วัตถุ (เทอร์มินัลใน #how) ไม่ใช่ 3 แถบเต็มกว้าง | คำสั่งเดิมของเจ้าของ: body สว่างมินิมอล มืดเฉพาะ header/footer |
| ตัด timestamp `[00:00.4]` และ OK marker จาก log | อ่านเป็นคำอ้าง latency/fill |
| pains เป็น ledger ขีดฆ่าบน surface (ไม่ใช่คำ MANUAL outline และไม่ใช่ comparison slider) | strike-through ถูก graft โดย judges ทั้ง 3 · slider ซ่อนครึ่งเนื้อหาไว้หลังการลาก (judge 3) และเพิ่ม DOM/JS |
| stamp อังกฤษเหลือ 2 (prompt, NOTICE) ที่เหลือเป็นไทย/เลข | หน้าไทยที่มี MODULE/ISSUE/FRAME/BACKTEST ตัวใหญ่ดูเป็นเทมเพลต |
| HUD strip ใช้ 4 ข้อเท็จจริง (แพลตฟอร์ม/สินทรัพย์/รูปแบบ/การส่งมอบ) แทน READY/AUTO | judge 3: ให้ moment แรกเป็น moment ทำความเข้าใจ · "หลายคู่เงิน" คือสิ่งที่เจ้าของยืนยัน |
| ไม่มี risk dial, ไม่มี pairs ticker | กับดักเปิดเผยกลยุทธ์ / รายการคู่เงินที่ยังไม่ยืนยัน · ถ้าเจ้าของส่งรายการจริงค่อยพิมพ์เป็นแถว hairline นิ่ง |
| ไม่มี hero ตัวอักษรเบลอ/opacity 0, ไม่มี 400vh driver, ไม่มี sticky terminal ที่ morph, ไม่มี gyroscope/magnetic/auto-advance tabs | LCP, scroll-jacking, jank บนมือถือ, ขัด "คลีน มินิมอล" |
| footer LED "เว็บไซต์เวอร์ชัน 2" ออก · ใส่นาฬิกาไทยแบบไม่มี session/DST | LED เขียวข้างข้อความอ่านเป็น uptime · นาฬิกา Asia/Bangkok ไม่มี DST ไม่มีข้อความตลาด |
| `footer_hours_text` default ว่าง | คำสัญญาเวลาให้บริการต้องมาจากเจ้าของ |
| keycap LINE (SWITCHBOARD), QR reveal (CONTROL ROOM), watermark EA เงิน/2000 เขียว (SIGNAL PATH), wipe รูป + margin note + principle line (ISSUE 01), switch glyph บนลิงก์ (SWITCHBOARD), home.js แยกไฟล์ (BENTO) | grafts ที่ judges เห็นตรงกันอย่างน้อย 2 ใน 3 |
| ไม่ทำ sticky risk ribbon บนมือถือ | เพิ่ม UI เหนือ dock โดยไม่ได้เพิ่มความครบของคำเตือน (hazard band อยู่ในโฟลว์เต็มอยู่แล้ว) |
| คง `.reveal` เดิมไว้ให้ inner pages แต่หน้าแรก/footer ใช้ `.watch` ที่ไม่มี fade-up | audit: fade-up 0.6s คือลายเซ็น FENIX · inner pages ยังไม่ถึงคิว restyle |

---

## ภาคผนวก ก · ตาราง key ใหม่ทั้งหมด (A เพิ่มใน `ea2000_defaults()` + control)

| key | type | Customizer section | default |
|---|---|---|---|
| `show_rail` | checkbox | 2 hero | true |
| `fig_label` | text | 2 hero | `ภาพ` |
| `contact_fallback_text` | text | 1 general | `ติดต่อทีมงาน` |
| `hero_hud_items` | textarea | 2 hero | 4 บรรทัดตาม 2.1 |
| `hero_hud_note` | text | 2 hero | `ป้ายข้อมูลระบบ ไม่ใช่ผลการเทรด` |
| `hero_img_note` | text | 2 hero | `รูปที่ต้องใส่: ภาพกล่องสินค้าพื้นโปร่ง 1000x1000 px` |
| `what_principle_label` | text | 3 what | `หลักการของเรา` |
| `what_principle` | text | 3 what | `เราไม่แสดงตัวเลขที่ยังตรวจสอบไม่ได้ และไม่รับประกันผลกำไร` |
| `pain_kicker` | text | 4 pain | `ปัญหาของการเทรดมือ` |
| `pain_resolved_label` | text | 4 pain | `สิ่งที่ระบบอัตโนมัติเข้ามาแทน` |
| `pain_resolved_text` | textarea | 4 pain | `ระบบทำตามกฎเดิมทุกครั้ง ส่วนทุน ความเสี่ยง และการตัดสินใจเริ่มหรือหยุดยังเป็นของคุณ` |
| `show_how_log` | checkbox | 5 how | true |
| `how_log_title` | text | 5 how | `ภาพจำลองลำดับการทำงาน` |
| `how_log_prompt` | text | 5 how | `ea2000@mt5:~$` |
| `how_log_start` | text | 5 how | `เริ่มลำดับการทำงาน` |
| `how_log_lines` | textarea | 5 how | `` |
| `how_log_ready` | text | 5 how | `พร้อมทำงาน · รอเงื่อนไขตามกฎที่ตั้งไว้` |
| `features_kicker` | text | 6 features | `โมดูลของระบบ` |
| `feat_module_label` | text | 6 features | `โมดูล` |
| `tests_tab_bt_label` | text | 7 tests | `ทดสอบย้อนหลัง` |
| `tests_tab_fw_label` | text | 7 tests | `ทดสอบเดินหน้า` |
| `install_step1_img_note` | text | 8 install | ตาม 2.7 |
| `install_step2_img_note` | text | 8 install | ตาม 2.7 |
| `install_step3_img_note` | text | 8 install | ตาม 2.7 |
| `install_step_label` | text | 8 install | `ขั้น` |
| `pricing_kicker` | text | 9 pricing | `แพ็กเกจ` |
| `pricing_recommended_label` | text | 9 pricing | `แนะนำ` |
| `pricing_more_text` | text | 9 pricing | `ดูรายละเอียดแพ็กเกจทั้งหมด` |
| `pricing_contact_text` | text | 9 pricing | `สอบถามราคา` |
| `faq_kicker` | text | 10 faq | `คำถามที่พบบ่อย` |
| `risk_kicker` | text | 11 risk | `ประกาศความเสี่ยง` |
| `risk_label` | text | 11 risk | `NOTICE` |
| `risk_more_text` | text | 11 risk | `อ่านประกาศความเสี่ยงฉบับเต็ม` |
| `risk_margin_note` | text | 11 risk | `ผลในอดีตไม่รับประกันผลในอนาคต · การเทรดมีความเสี่ยง โปรดอ่านประกาศฉบับเต็มก่อนตัดสินใจ` |
| footer 24 keys | ตาม 4.4 | 13 footer | ตาม 4.4 |

ค่า default ของ key เดิมที่ A ต้องเปลี่ยน: `hero_badge`, `what_kicker`, `what_text`, `what_points`, `what_img_note`, `how_kicker`, `how_step3_desc`, `how_img_note`, `feat4_desc`, `tests_kicker`, `tests_bt_img_note`, `tests_fw_img_note`, `install_kicker` (ค่าใหม่ระบุในข้อ 2) · section 12 (CTA) ใน customizer เปลี่ยน description เป็น `ข้อความชุดนี้เป็นค่าสำรองของหัวข้อใน launch console ของแถบท้ายเว็บ` และลบ control `show_cta`, `cta_btn_text` · REST sanitizer เดิมจัดการ key ใหม่ได้ (ลงท้าย `_img` = URL, bool ตาม default) ไม่ต้องแก้

## ภาคผนวก ข · ลำดับรวมงาน
1. A, B, C ทำงานขนานบน branch เดียวกันแต่คนละไฟล์ (ไม่มีไฟล์ซ้อนกัน)
2. รวมแล้วรัน checklist 7.6 ก่อน push · push = deploy อัตโนมัติ (CLAUDE.md ข้อ 13)
3. หลัง deploy: รีเซ็ต kicker ผ่าน REST, purge Cloudflare, screenshot 1280/375/360, ส่งเจ้าของดู
4. งานถัดไป (นอก spec นี้): ถ่าย `screenshot.png` จริงของธีม, ปรับ inner pages ให้ใช้ภาษา HUD เดียวกัน, รอ QR และรูปหน้าจอจากเจ้าของ
