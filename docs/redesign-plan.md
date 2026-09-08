# EA2000 : แผนรีดีไซน์

Header/Footer โทนเขียวเข้ม และโครงหน้ารายเพจให้แต่ละหน้าไม่ซ้ำกัน

> เอกสารนี้สร้างจากการรีเสิร์ชจริงเมื่อ 8 กันยายน 2026 · ไม่มีตัวเลข search volume เพราะไม่ได้ใช้ Keyword Planner · ตัวเลขทุกตัวระบุที่มาไว้แล้ว ห้ามนำไปอ้างเป็นปริมาณการค้นหา

# แผนรีดีไซน์ EA2000 · Header/Footer โทนเขียวเข้มเกือบดำ + โครงหน้ารายเพจ

เอกสารนี้อ้างอิงไฟล์จริงใน `D:\EA VIDEO\ea2000-repo\ea2000\` และตรวจยืนยันกับหน้าเว็บจริง `https://ea2000.co` เมื่อ 8 ก.ย. 2026 แบบอ่านอย่างเดียว · ค่าคอนทราสต์ทุกตัวเป็นค่าที่คำนวณเอง (สูตร WCAG 2.x relative luminance) ไม่ใช่ค่าประมาณ · ภาพประกอบและสคริปต์ทดสอบอยู่ที่ `C:\Users\THANAWUT HR\AppData\Local\Temp\claude\D--EA-VIDEO-ea2000-repo\774f1e8f-695b-41b5-ac49-8c885f22cd55\scratchpad\design\`

---

## 0) สิ่งที่ตรวจพบก่อนเริ่ม (ต้องอ่านก่อน ข้ามไม่ได้)

### 0.1 บั๊กจริงบนเว็บสด: เมนูย่อยบนมือถือหลุดออกนอกจอ

**อาการ**: บนมือถือ กด "ผลทดสอบ" ในลิ้นชักเมนู แล้วเมนูย่อย Backtest / Forward Test จะเลื่อนออกไปทางซ้ายจนมองไม่เห็น ผู้ใช้มือถือเข้าสองหน้านี้จากเมนูไม่ได้เลย

**สาเหตุ** (วัดแล้ว ไม่ใช่การเดา):

| ไฟล์ | บรรทัด | กฎ | Specificity |
|---|---|---|---|
| `style.css` | 586-590 | `.nav-list li.is-open > .sub-menu { transform: translate(-50%, 0); }` | (0,3,1) **ชนะ** |
| `style.css` | 3415-3429 | `@media (max-width:960px) { .nav-list .sub-menu { transform: none; } }` | (0,2,0) แพ้ |

media query ไม่เพิ่ม specificity กฎที่ 3426 จึงแพ้เสมอ · ผลจากการวัด `getBoundingClientRect()` ในเบราว์เซอร์จริง: `sub pos: static | transform=matrix(1,0,0,1,-244,0)` และ `rect submenu: x = -212` บนจอกว้าง 542px คือ **อยู่นอกจอทั้งก้อน**

**ยืนยันว่ากระทบเว็บจริง**: `curl https://ea2000.co/` พบ `menu-item-has-children` 1 ชุด (menu-item-56 "ผลทดสอบ" → Backtest, Forward Test) และ `diff` ระหว่าง `style.css` บนเว็บกับในรีโปได้ผล **byte-identical** แปลว่าไฟล์ที่ deploy อยู่มีบั๊กนี้แน่นอน

**การแก้ (2 บรรทัด)**:
```css
@media (max-width: 960px) {
	.nav-list li.is-open > .sub-menu { transform: none; left: auto; }
}
```
ทดสอบแล้วเมนูย่อยกลับมาแสดงถูกตำแหน่ง (ภาพ `mock-mobile-fixed.png`) · **แก้ข้อนี้ก่อนงานดีไซน์ทั้งหมด เพราะเป็นบั๊กใช้งานไม่ได้ ไม่ใช่เรื่องความสวย**

### 0.2 โลโก้ wordmark ไม่ต้องทำเวอร์ชันใหม่ (ตรงข้ามกับที่กังวล)

โจทย์ตั้งข้อสงสัยว่า wordmark PNG มีเส้นขอบเข้มจึงอาจต้องทำเวอร์ชันสว่างสำหรับพื้นมืด · **ผลตรวจจริงคือตรงกันข้าม**

เรนเดอร์ `assets/img/logo-wordmark.webp` ขยาย 3 เท่าบนพื้นมืดเทียบพื้นขาว (ภาพ `zoom.png`) พบว่า:

- ไฟล์นี้มี **เส้นขอบนอกสีขาว (white keyline)** ล้อมทุกตัวอักษร ไม่ใช่เส้นขอบเข้ม · เส้นเข้มที่เห็นเป็นชั้นใน ถัดเข้ามาจากขาว
- บนพื้นมืด: เส้นขาวทำหน้าที่ตัดขอบให้ตัวอักษรลอยเด่น · "EA" โครเมียมและ "2000" เขียวอ่านชัด · บรรทัดรอง `EXPERT ADVISOR | ALGORITHMIC TRADING SYSTEM` ซึ่งเป็นตัวอักษรสีอ่อนขอบเข้ม **อ่านได้ดีกว่าบนพื้นขาวอย่างชัดเจน**
- บนพื้นขาวปัจจุบัน: ตัวอักษรสีอ่อนจมหายไปกับพื้น เหลือแค่เงาขอบเข้ม ดูเป็นคราบ

**สรุป**: `logo-wordmark.webp` เป็น asset ที่ออกแบบมาสำหรับพื้นมืดตั้งแต่ต้น · การเปลี่ยน header/footer เป็นสีเข้ม **แก้ปัญหาโลโก้ที่มีอยู่เดิม** ไม่ได้สร้างปัญหาใหม่

ตรวจแล้วว่า `brand-wordmark` ถูกใช้แค่ 2 จุดคือ `header.php:25` และ `footer.php:76` ซึ่งจะเป็นพื้นมืดทั้งคู่ · **ไม่ต้องเพิ่ม setting `brand_wordmark_light`** และไม่ควรเพิ่ม เพราะจะกลายเป็น setting ตายที่ไม่มีใครใช้

**ข้อควรทำแทน** (คนละเรื่องกับสีเข้ม แต่ควรทำพร้อมกัน): บรรทัดรองที่ฝังในรูปอ่านไม่ออกที่ความสูง 40px ในทุกพื้นหลัง · ควรตัดรูปใหม่ให้เหลือเฉพาะคำว่า `EA2000` แล้วให้บรรทัดรองเป็นข้อความจริงจาก setting `brand_tagline` ที่มีอยู่แล้ว (`functions.php:190`) · ได้ประโยชน์สามอย่างพร้อมกัน: อ่านออก, screen reader อ่านได้, และไฟล์เล็กลงจาก 101 KB ตามที่ audit เทคนิคชี้ไว้ · **ตอนเปลี่ยนรูปต้องเปลี่ยนชื่อไฟล์เสมอ** ตามบทเรียน Cloudflare ใน CLAUDE.md

### 0.3 โลโก้กลมจะดูดีขึ้นมากบนพื้นมืด

`assets/img/logo.png` เป็นวงกลมพื้นดำมีวงแหวนเขียว · บนพื้นขาวปัจจุบันมันเป็นวงกลมดำทึบแปะอยู่ ต้องใส่ `box-shadow: 0 0 0 1px var(--border)` (`style.css:442`) เพื่อไม่ให้ลอย · บนพื้นเข้ม พื้นดำของโลโก้จะกลืนไปกับ header เหลือเห็นแค่วงแหวนเขียวและตัวอักษร ซึ่งเป็นผลลัพธ์ที่ตั้งใจของโลโก้ (เห็นได้ในภาพ `wordmark-on-dark.png` แถว D)

---

## 1) Header และ Footer โทนเขียวเข้มเกือบดำ

### 1.1 ตัวแปร CSS ใหม่ (เพิ่มใน `:root` ที่ `style.css:20-79`)

เพิ่มต่อท้ายบล็อก `:root` เดิม **โดยไม่แตะ token โทนสว่างเดิมแม้แต่ตัวเดียว** เนื้อหาในหน้ายังสว่างเหมือนเดิมทุกประการ

```css
:root {
	/* ---------- โทนมืด · ใช้กับ header และ footer เท่านั้น ---------- */
	/* สเกลพื้น */
	--ink:         #08150E;   /* พื้นหลักของแถบบนและแถบล่าง */
	--ink-deep:    #05100A;   /* ลึกกว่า: header ตอนเลื่อน, บาร์ล่างมือถือ */
	--ink-raise:   #0F2016;   /* แผงยกระดับ: dropdown, การ์ดใน footer */
	--ink-chip:    #16301F;   /* ชิป/ปุ่มกลม: ตัวสลับภาษา, ปุ่มเมนู, social */
	--ink-rgb:     8, 21, 14;

	/* ตัวอักษรบนพื้นมืด */
	--ink-text:    #F2F7F3;   /* ข้อความหลัก */
	--ink-muted:   #AEBEB2;   /* ข้อความรอง, ลิงก์เมนูตอนพัก */
	--ink-dim:     #8B9C90;   /* ข้อความจาง (copyright) */

	/* สีเน้นบนพื้นมืด · สว่างกว่า --accent เดิมเล็กน้อยเพื่อให้เด้งบนพื้นดำ */
	--ink-accent:     #8CEC4E;
	--ink-accent-rgb: 140, 236, 78;

	/* เส้นขอบบนพื้นมืด (ขาวโปร่ง เพราะเส้นเข้มมองไม่เห็น) */
	--ink-border-soft:   rgba(242, 247, 243, 0.07);
	--ink-border:        rgba(242, 247, 243, 0.12);
	--ink-border-strong: rgba(242, 247, 243, 0.20);

	/* ---------- alias เชิงความหมาย · CSS ของ header/footer เรียกใช้ชื่อกลุ่มนี้ ---------- */
	--header-bg:          var(--ink);
	--header-bg-scrolled: var(--ink-deep);
	--header-text:        var(--ink-text);
	--header-muted:       var(--ink-muted);
	--header-border:      var(--ink-border-soft);
	--header-panel:       var(--ink-raise);
	--header-chip:        var(--ink-chip);
	--header-accent:      var(--ink-accent);

	--footer-bg:      var(--ink);
	--footer-bg-deep: var(--ink-deep);
	--footer-text:    var(--ink-text);
	--footer-muted:   var(--ink-muted);
	--footer-dim:     var(--ink-dim);
	--footer-border:  var(--ink-border);
	--footer-card:    var(--ink-raise);
	--footer-chip:    var(--ink-chip);
	--footer-accent:  var(--ink-accent);
}
```

**เหตุผลที่แยกเป็นสองชั้น** (`--ink-*` = ค่าจริง, `--header-*` / `--footer-*` = alias): ถ้าวันหนึ่งเจ้าของอยากให้ footer เข้มกว่า header หรืออยากให้ header กลับเป็นสว่าง แก้ที่ alias จุดเดียว ไม่ต้องไล่แก้กฎเป็นสิบ · สอดคล้องกับที่ธีมทำ tokenize สีไว้แล้วตาม CLAUDE.md ข้อ 9

**ค่า hue**: `#08150E` อยู่ที่ H148 S45% L6% · ถ้าอยากให้ใกล้เขียวโลโก้ (H115) มากกว่านี้ ใช้ `#09160B` (H129 S42% L6%) แทนได้ ค่าคอนทราสต์แทบไม่ต่าง (คลาดเคลื่อนไม่เกิน 0.2:1 ทุกคู่) · ผมเลือก `#08150E` เพราะเรนเดอร์จริงแล้วเข้ากับโครเมียมของ "EA" ในโลโก้ดีกว่า

### 1.2 ตารางคอนทราสต์ · ตรวจครบทุกคู่ที่ใช้จริง

**คู่ที่ใช้ในดีไซน์ใหม่ (ผ่านหมด)**

| ตัวอักษร | พื้นหลัง | อัตราส่วน | เกณฑ์ | ใช้ที่ไหน |
|---|---|---|---|---|
| `--ink-text` #F2F7F3 | `--ink` #08150E | **17.24:1** | AAA | ชื่อแบรนด์, ลิงก์เมนูตอน hover, หัวข้อ footer |
| `--ink-muted` #AEBEB2 | `--ink` #08150E | **9.62:1** | AAA | ลิงก์เมนูตอนพัก, ข้อความ footer |
| `--ink-dim` #8B9C90 | `--ink` #08150E | **6.46:1** | AA | copyright |
| `--ink-accent` #8CEC4E | `--ink` #08150E | **12.65:1** | AAA | kicker, ลิงก์ hover, ไอคอน check |
| `--accent-2` #C9F5A6 | `--ink` #08150E | **15.21:1** | AAA | ปลาย gradient ขีดใต้เมนู |
| `--ink-text` | `--ink-deep` #05100A | **17.86:1** | AAA | header ตอนเลื่อน, บาร์ล่างมือถือ |
| `--ink-muted` | `--ink-deep` | **9.96:1** | AAA | ป้ายบาร์ล่างมือถือ |
| `--ink-accent` | `--ink-deep` | **13.11:1** | AAA | ปุ่ม active บาร์ล่าง |
| `--ink-text` | `--ink-raise` #0F2016 | **15.64:1** | AAA | ข้อความในการ์ด footer |
| `--ink-muted` | `--ink-raise` | **8.73:1** | AAA | ข้อความรองใน dropdown |
| `--ink-accent` | `--ink-raise` | **11.48:1** | AAA | ลิงก์ dropdown ตอน hover |
| `--ink-text` | `--ink-chip` #16301F | **13.12:1** | AAA | ตัวอักษรบนชิป |
| `--ink-muted` | `--ink-chip` | **7.32:1** | AAA | ไอคอน social ตอนพัก |
| `--ink-accent` | `--ink-chip` | **9.63:1** | AAA | ไอคอน social ตอน hover |
| `#03240F` | `--line-green` #06C755 | **7.37:1** | AAA | ปุ่ม LINE (ค่าเดิม ไม่ต้องแก้) |

**คู่ที่จะพังถ้าไม่แก้ (นี่คือรายการงานที่ต้องทำ)**

| ตัวอักษรเดิม | บนพื้นใหม่ | อัตราส่วน | ผล | กฎที่ต้องแก้ |
|---|---|---|---|---|
| `--text` #0B1210 | `--ink` | **1.01:1** | มองไม่เห็นเลย | `style.css:431` `.brand`, `:435`, `:955` `.nav-toggle span` |
| `--muted` #5B6660 | `--ink` | **3.13:1** | ตก AA | `style.css:509` `.nav-list a`, `:479` `.brand-name small`, `:596` `.sub-menu a`, `:705` lang menu a, `:2262` mobile-app-nav-item |
| `--primary` #1A7F11 | `--ink` | **3.64:1** | ตก AA | `style.css:1986` `.footer-prep-list .icon`, `:5305` `.social-link:hover` |
| `--primary-deep` #166B0F | `--ink` | **2.80:1** | ตก AA | `style.css:1838` `.footer-kicker`, `:2018` `.footer-legal a:hover`, `:2035` |
| `--primary-deep` #166B0F | `--ink-raise` | **2.54:1** | ตก AA หนัก | `style.css:609` `.sub-menu a:hover`, `:717`, `:729` `.language-switcher-menu a .language-switcher-code`, `:931` GTranslate option hover |

ข้อสุดท้ายเห็นชัดในการเรนเดอร์ทดสอบรอบแรก: ชิป "TH" / "EN" ใน dropdown ภาษาเป็นเขียวเข้มบนพื้นเข้ม แทบมองไม่เห็น (เทียบภาพ `mock-desktop.png` กับ `mock-desktop2.png` หลังแก้)

### 1.3 การตัดสินใจสำคัญ: header ต้องทึบ ไม่ใช่โปร่ง

ปัจจุบัน `style.css:408-410`:
```css
background: rgba(var(--bg-rgb), 0.86);
backdrop-filter: blur(14px);
-webkit-backdrop-filter: blur(14px);
```

ถ้าคงความโปร่งไว้แล้วเปลี่ยนเป็นสีเข้ม: `rgba(8,21,14, 0.88)` ทับบนเนื้อหาพื้นขาวจะผสมออกมาเป็น **`#26312B`** ซึ่งเป็นเทาอมเขียวซีด ไม่ใช่ดำเขียวตามที่เจ้าของสั่ง · คอนทราสต์ยังผ่าน (12.45:1) แต่ **สีผิด** และจะเปลี่ยนไปมาตามสีของเนื้อหาที่เลื่อนผ่านข้างหลัง ดูสกปรก

**ข้อสรุป**: บนเว็บที่ body เป็นสีสว่าง header สีเข้มแบบโปร่งจะซีดเสมอ · ให้ใช้พื้นทึบ และตัด `backdrop-filter` ทิ้ง

ผลพลอยได้: ตัด compositing layer ที่ `backdrop-filter` บังคับสร้างทุกเฟรม ซึ่งเป็นภาระเรนเดอร์บนมือถือ · และตัดบรรทัด `-webkit-` ซ้ำซ้อน

**ข้อควรระวังทางเทคนิคที่ต้องรู้**: `backdrop-filter` ที่ไม่ใช่ `none` ทำให้ `.site-header` กลายเป็น containing block ของลูกที่ `position: fixed` · ตอนนี้ `.site-nav` ในโหมดมือถือเป็น `position: fixed` (`style.css:3345`) จึงอิงกับ header อยู่โดยบังเอิญ · **การตัด `backdrop-filter` เปลี่ยน containing block ของ drawer ไปเป็น viewport** · ผมทดสอบแล้วว่าตำแหน่งออกมาเหมือนเดิมทุกพิกเซล เพราะ header เป็น sticky ที่ `top: 0` สูง 72px พอดี ทำให้ `top: var(--header-h)` ให้ผลเท่ากันทั้งสองแบบ (วัดได้ `rect nav: y=72, height = innerHeight - 72`) · แต่ต้องรู้ไว้และตรวจซ้ำหลัง deploy

### 1.4 กฎทุกข้อที่ต้องเปลี่ยน · Header

```css
/* --- แถบหลัก · style.css:401-419 --- */
.site-header {
	background: var(--header-bg);          /* แทน rgba(var(--bg-rgb), 0.86) */
	backdrop-filter: none;                 /* ตัดทิ้งทั้งบรรทัด :409-410 */
	-webkit-backdrop-filter: none;
	border-bottom: 1px solid var(--header-border);
}
.site-header.scrolled {
	background: var(--header-bg-scrolled);
	border-bottom-color: var(--ink-border);
	box-shadow: 0 6px 22px rgba(0, 0, 0, 0.42);   /* เงาเดิม :418 จางเกินบนพื้นดำ */
}

/* --- แบรนด์ · style.css:427-482 --- */
.site-header .brand,
.site-header .brand:hover,
.site-header .brand-name          { color: var(--header-text); }
.site-header .brand-name em       { color: var(--header-accent); }
.site-header .brand-name small    { color: var(--header-muted); }
.site-header .brand-logo          { box-shadow: 0 0 0 1px var(--ink-border-strong); }

/* --- ลิงก์เมนู · style.css:505-532 --- */
.nav-list a          { color: var(--header-muted); }
.nav-list a:hover    { color: var(--header-text); }
.nav-list a::after   { background: linear-gradient(90deg, var(--header-accent), var(--accent-2)); }
/* ลูกศร ::before ที่ :545-546 ใช้ currentColor อยู่แล้ว ไม่ต้องแก้ */

/* --- Dropdown · style.css:556-610 --- */
.nav-list .sub-menu {
	background: var(--header-panel);
	border-color: var(--ink-border);
	box-shadow: 0 22px 46px rgba(0, 0, 0, 0.50);
}
.nav-list .sub-menu a { color: var(--header-muted); }
.nav-list .sub-menu a:hover,
.nav-list .sub-menu a:focus-visible {
	background: rgba(var(--ink-accent-rgb), 0.14);
	color: var(--header-accent);
}

/* --- ตัวสลับภาษา (แบบธีมเอง) · style.css:612-742 --- */
.site-header .language-switcher summary {
	background: var(--header-chip);
	border-color: var(--ink-border);
	color: var(--header-text);
}
.site-header .language-switcher[open] summary {
	background: rgba(var(--ink-accent-rgb), 0.16);
	border-color: rgba(var(--ink-accent-rgb), 0.45);
	color: var(--header-accent);
}
.site-header .language-switcher-menu {
	background: var(--header-panel);
	border-color: var(--ink-border);
	box-shadow: 0 22px 46px rgba(0, 0, 0, 0.50);
	backdrop-filter: none;                 /* :694-695 ตัดทิ้ง ไม่มีประโยชน์บนพื้นทึบ */
}
.site-header .language-switcher-menu a { color: var(--header-muted); }
.site-header .language-switcher-menu a:hover,
.site-header .language-switcher-menu a.is-active {
	background: rgba(var(--ink-accent-rgb), 0.14);
	color: var(--header-accent);
}
.site-header .language-switcher-menu a .language-switcher-code {   /* :721-732 · คู่ที่ตก 2.54:1 */
	border-color: rgba(var(--ink-accent-rgb), 0.42);
	color: var(--ink-accent);
}
.site-header .language-switcher--plugin a {                        /* :755-762 */
	background: var(--header-chip);
	border-color: var(--ink-border);
	color: var(--header-muted);
}

/* --- ปุ่มเปิดเมนู · style.css:935-969 --- */
.nav-toggle      { background: var(--header-chip); border-color: var(--ink-border); }
.nav-toggle span { background: var(--header-text); }

/* --- social ในเมนู · style.css:5286-5309 --- */
.site-header .social-link {
	background: var(--header-chip);
	border-color: var(--ink-border);
	color: var(--header-muted);
}
.site-header .social-link:hover {
	background: rgba(var(--ink-accent-rgb), 0.16);
	border-color: rgba(var(--ink-accent-rgb), 0.45);
	color: var(--header-accent);
}
```

**GTranslate** (`style.css:801-933` · 130 บรรทัดที่ใช้ `!important` ทั้งหมด) · ตรวจ HTML สดแล้วพบว่าธีมพิมพ์ออกมาแค่ `<div class="gtranslate_wrapper" id="gt-wrapper-67467867"></div>` ว่างเปล่า · โครงสร้าง `.gt_float_switcher` ทั้งหมดถูก **JavaScript ของปลั๊กอินฉีดเข้ามาหลังโหลด** และปลั๊กอินตั้ง inline style เอง จึงต้องใช้ `!important` ต่อ:

```css
.site-header .language-switcher--gtranslate .gt_float_switcher {
	background: var(--header-chip) !important;
	border-color: var(--ink-border) !important;
	color: var(--header-text) !important;
}
.site-header .language-switcher--gtranslate .gt_float_switcher .gt-selected .gt-current-lang,
.site-header .language-switcher--gtranslate .gt_float_switcher .gt-selected .gt-current-lang span.gt-lang-code {
	color: var(--header-text) !important;
}
.site-header .language-switcher--gtranslate .gt_float_switcher .gt-selected .gt-current-lang span.gt_float_switcher-arrow {
	filter: invert(1) !important;          /* :875 เดิม filter: none · ลูกศรเป็นภาพสีเข้ม ต้องกลับสี */
	opacity: 0.72 !important;
}
.site-header .language-switcher--gtranslate .gt_float_switcher .gt_options {
	background: var(--header-panel) !important;
	border-color: var(--ink-border) !important;
	box-shadow: 0 22px 46px rgba(0, 0, 0, 0.50) !important;
}
.site-header .language-switcher--gtranslate .gt_float_switcher .gt_options a { color: var(--header-muted) !important; }
.site-header .language-switcher--gtranslate .gt_float_switcher .gt_options a:hover {
	background: rgba(var(--ink-accent-rgb), 0.14) !important;
	color: var(--header-accent) !important;
}
/* กันพลาด: ถ้า GTranslate เปลี่ยนโครงสร้าง ให้ตัวห่อยังมีสีถูก */
.site-header .language-switcher--gtranslate { color-scheme: dark; }
```

`filter: invert(1)` บนลูกศรเป็นจุดที่คนมักลืม เพราะ GTranslate ใช้ภาพลูกศรสีเข้ม ไม่ใช่ตัวอักษร · บรรทัด `:875` ปัจจุบันตั้ง `filter: none !important` ไว้เพราะพื้นสว่าง

**ทางเลือกที่ควรพิจารณาจริง ๆ**: เว็บนี้เป็นภาษาไทยล้วน ไม่มีลูกค้าต่างชาติ และ GTranslate แปลฝั่ง client โดยไม่สร้าง URL ให้ index (ตรงกับข้อสรุปของ audit เทคนิค) · การปิด `show_language_switcher` ใน Customizer แล้วถอดปลั๊กอินออก จะตัดทั้ง 130 บรรทัด `!important` เดิม + 40 บรรทัดใหม่ข้างบน + JS 6.7 KB ทิ้งไปทั้งหมด · **แนะนำให้ถามเจ้าของก่อนลงแรงทำ dark mode ให้ widget ที่อาจถอดทิ้ง**

### 1.5 กฎทุกข้อที่ต้องเปลี่ยน · Footer

```css
/* --- แถบหลัก · style.css:1811-1818 --- */
.site-footer {
	background: var(--footer-bg);
	color: var(--footer-text);
	border-top: 0;                          /* :1813 เส้นเข้มบนพื้นเข้ม มองไม่เห็น เอาออก */
}

/* --- แบรนด์ใน footer · footer.php:73-92 --- */
.site-footer .brand,
.site-footer .brand:hover,
.site-footer .brand-name       { color: var(--footer-text); }
.site-footer .brand-name small { color: var(--footer-muted); }
.site-footer .brand-logo       { box-shadow: 0 0 0 1px var(--ink-border-strong); }

/* --- กล่อง CTA · style.css:1820-1857 --- */
.footer-cta {
	background: var(--footer-card);         /* แทน gradient accent-2 + card-bg ที่ :1830-1832 */
	border-color: var(--ink-border);
	box-shadow: none;                       /* เงาดำบนพื้นดำไม่มีผล */
}
.footer-cta h2                     { color: var(--footer-text); }
.footer-cta p:not(.footer-kicker)  { color: var(--footer-muted); }
.footer-kicker                     { color: var(--footer-accent); }   /* :1838 เดิม 2.80:1 */

/* --- ปุ่ม LINE :1859-1884 --- ไม่ต้องแก้ · เขียว LINE บนพื้นดำเด่นกว่าเดิม 7.37:1 */

/* --- โครงและการ์ด · style.css:1886-1987 --- */
.footer-main         { border-bottom-color: var(--footer-border); }
.footer-tagline      { color: var(--footer-muted); }
.footer-trust li     { background: var(--footer-chip); border-color: var(--ink-border); color: var(--footer-muted); }
.footer-prep         { background: var(--footer-card); border-color: var(--ink-border); box-shadow: none; }
.footer-head         { color: var(--footer-text); }
.footer-prep p       { color: var(--footer-muted); }
.footer-prep-list li { background: var(--footer-chip); border-color: var(--ink-border); color: var(--footer-muted); }
.footer-prep-list .icon { color: var(--footer-accent); }   /* :1986 เดิม 3.64:1 */

/* --- แถวล่าง · style.css:1989-2036 --- */
.footer-copy                { color: var(--footer-dim); }
.footer-legal a,
.footer-links-mini,
.footer-links-mini a        { color: var(--footer-muted); }
.footer-legal a:hover,
.footer-links-mini a:hover  { color: var(--footer-accent); }   /* :2018, :2035 เดิม 2.80:1 */

/* --- social ใน footer · style.css:5286-5309 --- */
.site-footer .social-link {
	background: var(--footer-chip);
	border-color: var(--ink-border);
	color: var(--footer-muted);
}
.site-footer .social-link:hover {
	background: rgba(var(--ink-accent-rgb), 0.16);
	border-color: rgba(var(--ink-accent-rgb), 0.45);
	color: var(--footer-accent);
}
```

**ตัวเลือกเสริมที่แนะนำ**: ทำแถวล่างสุด (`footer.php:112-131` `.footer-bottom`) ให้เข้มกว่าอีกขั้นด้วย `--footer-bg-deep` จะได้ลำดับชั้นสายตาแบบเว็บใหญ่ ๆ ต้องแตะเทมเพลตเล็กน้อย (ย้าย `.footer-bottom` ออกนอก `.container` แล้วห่อด้วย `<div class="footer-bottom-bar">` ใหม่)

### 1.6 ลิ้นชักเมนูมือถือ (`@media max-width: 960px` · `style.css:3334-3445`)

```css
@media (max-width: 960px) {
	/* :3357 เดิม background: var(--bg) = ขาว */
	.site-nav { background: var(--header-bg); border-top: 1px solid var(--ink-border); }

	/* :3401 เดิม color: var(--muted) = 3.13:1 */
	.nav-list a { color: var(--header-muted); }
	.nav-list a:hover,
	.nav-list a:focus-visible {
		background: rgba(var(--ink-accent-rgb), 0.12);   /* :3411 เดิม primary-rgb 0.10 จมบนพื้นดำ */
		color: var(--header-text);
	}

	/* :3422 เส้นซ้ายของเมนูย่อย */
	.nav-list .sub-menu { border-left-color: rgba(var(--ink-accent-rgb), 0.34); }

	/* แก้บั๊กข้อ 0.1 · ต้องมีบรรทัดนี้ */
	.nav-list li.is-open > .sub-menu { transform: none; left: auto; }

	/* :5318 เส้นคั่นเหนือ social ในลิ้นชัก */
	.nav-social { border-top-color: var(--ink-border); }
}
```

**ข้อสังเกตเรื่องความสูง**: drawer ใช้ `height: calc(100dvh - var(--header-h))` (`style.css:3350`) · ตรวจในเบราว์เซอร์จริงแล้วคำนวณถูกต้อง ไม่ต้องแก้

### 1.7 บาร์ล่างมือถือ (`@media max-width: 760px` · `style.css:2229-2334`)

```css
@media (max-width: 760px) {
	.mobile-app-nav {
		background: var(--ink-deep);                    /* :2247 เดิม rgba(--bg-rgb, .94) */
		border-color: var(--ink-border);
		box-shadow: 0 12px 34px rgba(0, 0, 0, 0.55);
		backdrop-filter: blur(18px) saturate(145%);     /* คงไว้ได้ เพราะพื้นหลังคือเนื้อหาสว่าง จะได้ฝ้าจริง */
	}
	.mobile-app-nav-item      { color: var(--ink-muted); }        /* :2262 เดิม --muted 3.13:1 */
	.mobile-app-nav-icon      { background: var(--ink-chip); }    /* :2285 เดิม --surface-2 เทาอ่อน */

	.mobile-app-nav-item.is-active,
	.mobile-app-nav-item:hover,
	.mobile-app-nav-item:focus-visible { color: var(--ink-accent); }

	.mobile-app-nav-item.is-active .mobile-app-nav-icon,
	.mobile-app-nav-item:hover .mobile-app-nav-icon,
	.mobile-app-nav-item:focus-visible .mobile-app-nav-icon {
		background: rgba(var(--ink-accent-rgb), 0.20);   /* :2302, :2314 เดิม --accent-2 สว่างจ้าเกินบนพื้นดำ */
		color: var(--ink-accent);
	}
	.mobile-app-nav-item.is-action { color: var(--ink-accent); }
	/* :2322-2330 ปุ่ม LINE คงเขียว --line-green เดิม เป็นจุดเน้นเดียวในบาร์ ถูกต้องแล้ว */
}
```

**ตัดสินใจสำคัญ**: บาร์ล่างมือถือลอยอยู่เหนือ **เนื้อหาสว่าง** ไม่ใช่เหนือ footer · การทำให้มันเข้มจึงเป็นการเลือกดีไซน์ ไม่ใช่ผลพลอยได้ · แนะนำให้เข้ม เพราะ (ก) จับคู่กับ header เป็นกรอบบน-ล่างชุดเดียวกัน (ข) แยกตัวจากเนื้อหาสว่างชัดกว่าเดิม (ค) ปุ่ม LINE เขียวเด้งขึ้นมาก

### 1.8 องค์ประกอบอื่นที่ต้องคิดด้วย

| องค์ประกอบ | ไฟล์/บรรทัด | เข้มหรือไม่ | เหตุผล |
|---|---|---|---|
| `.reading-progress` | `style.css:3127-3143` | ปรับ gradient | `z-index: 200` อยู่ทับ header · gradient `--primary → --accent` ให้ 3.64:1 บนพื้นดำ · เปลี่ยนเป็น `var(--ink-accent) → var(--accent-2)` ได้ 12.65:1 |
| `.skip-link` | `style.css:377-391` | ไม่ต้องแก้ | พื้น `--primary` ทึบ ตัวอักษรขาว ยังอ่านออกบนทุกพื้น |
| `.float-line` / `.line-fab` | `style.css:2041-2100` · `footer.php:148-159` | ไม่ต้องแก้ | เป็นปุ่มเขียว LINE ทึบ ลอยเหนือเนื้อหาสว่าง |
| `.cookie-consent` | `style.css:2107-2175` · `footer.php:161-178` | **ทำเข้ม** | ลอยติดขอบล่างข้าง ๆ บาร์มือถือ ถ้าเป็นสีขาวจะขัดกัน · ปัจจุบันปิดอยู่ (`show_cookie_consent` = off ยืนยันจาก HTML สด) จึงยังไม่เร่ง แต่ต้องทำก่อนเปิด |
| `.post-card-cat` | `style.css:2450-2462` | ไม่ต้องแก้ | อยู่บนรูปในการ์ดบทความ ไม่เกี่ยวกับ header/footer |
| `template-links.php` (`/go/`) | `style.css:5326+` | **ไม่แตะ** | หน้า standalone ไม่เรียก `get_header()`/`get_footer()` (`template-links.php:64-75`) · token ใหม่ทั้งหมดไม่ถูกอ้างในหน้านี้ |

---

## 2) โครงหน้ารายเพจ · เรียงตามลำดับที่ควรลงมือ

หลักการเดียวที่ทำให้ทุกหน้าต่างกันจริงคือ **จังหวะหน้า (page rhythm)** ไม่ใช่การเปลี่ยนสี · ตอนนี้ธีมมีความกว้างแค่ 2 ค่า (`.container` 1140px `style.css:198-201`, `.container-narrow` 860px `:203-206`) และระยะ section ค่าเดียว (`.section { padding: 104px 0 }` `:209-212`) ทุกหน้าจึงเดินจังหวะเท่ากันหมด

### ขั้น 0 · แก้บั๊กและของเสียก่อน (ครึ่งชั่วโมง)

| งาน | ไฟล์:บรรทัด | หมายเหตุ |
|---|---|---|
| เมนูย่อยมือถือหลุดจอ | `style.css` เพิ่มใน media 960 | ตามข้อ 0.1 · **สำคัญที่สุดในเอกสารนี้** |
| ข้อความสั่งงานแอดมินหลุดสู่สาธารณะ | `front-page.php:387` | ห่อด้วย `if ( current_user_can( 'customize' ) )` เหมือน `template-backtest.php:68` และ `template-forward.php` |
| `#explore` ไม่มีสวิตช์ Customizer | `front-page.php:657` | เพิ่ม `show_explore` ใน `ea2000_defaults()` (`functions.php:177+`) และ `inc/customizer.php` |
| inline style กลางเทมเพลต | `template-forward.php:81` | ย้ายไปเป็นคลาส |

### ขั้น 1 · ชั้นจังหวะหน้า (ครึ่งวัน · ให้ผลมากที่สุดต่อแรงที่ลง)

เพิ่มคลาสบน `<main>` 4 แบบ แล้วให้ตัวแปรคุมระยะและความกว้าง · **ไม่ต้องแตะ component เดิมเลย** แต่ทุกหน้ารู้สึกต่างทันที

```css
/* ความกว้างที่ 3 และ 4 */
.container-doc  { width: min(720px,  calc(100% - 48px)); margin-inline: auto; }
.container-wide { width: min(1240px, calc(100% - 48px)); margin-inline: auto; }

/* จังหวะรายหน้า */
.layout-landing { --section-pad: 104px; --content-w: 1140px; }  /* front-page */
.layout-report  { --section-pad:  64px; --content-w: 1240px; }  /* backtest, forward */
.layout-doc     { --section-pad:  44px; --content-w:  720px; }  /* risk, privacy, page.php */
.layout-tool    { --section-pad:  72px; --content-w:  980px; }  /* pricing, install */
main[class*="layout-"] .section { padding: var(--section-pad) 0; }
```

พร้อมกันนั้นรวมคลาสที่ซ้ำซ้อน 3 คู่ (เก็บชื่อเดิมเป็น alias ไว้ก่อน ไม่ให้เทมเพลตพัง):

| คู่ที่ซ้ำ | บรรทัด | รวมเป็น |
|---|---|---|
| `.phero` / `.page-hero` | `style.css:3670-3678` / `:2340-2348` | `.pagehead` + `--center` `--left` `--split` |
| `.disclaimer` / `.riskdoc-intro` | `:1421-1441` / `:3924-3945` | `.notice` + `.notice--warn` |
| `.guide-num` / `.riskdoc-num` | `:3838-3855` / `:3986-4000` | `.numchip` + `.numchip--lg` |

พร้อมกันให้เปลี่ยนหน้า `risk-disclosure` และ `page.php` ไปใช้ `layout-doc` ทันที เพราะแค่นี้เอกสารกฎหมายก็แยกตระกูลจากหน้าขายแล้ว

### ขั้น 2 · `/backtest/` · โครง "รายงานข้อมูล"

ปัจจุบัน (`template-backtest.php:33-89`): `phero` กลางจอ → `.section` เดียว → `.lead` → `.stats-grid` → `.perf-figure` → `.sec-note` → `.disclaimer` → `ea2000_line_cta()`

โครงใหม่:

| # | Section | คลาสใหม่ | Component ที่ใช้ซ้ำ |
|---|---|---|---|
| 1 | หัวรายงาน 2 คอลัมน์ · ซ้าย h1 + เงื่อนไขทดสอบ · ขวา การ์ดสรุป 3 ค่า | `.report-hero` `.report-meta` `.report-verdict` | `.kicker`, `.stat` |
| 2 | โครง 2 คอลัมน์ `minmax(0,1fr) 300px` | `.report-layout` | `.container-wide` |
| 3 | rail ขวา sticky · สารบัญ + ปุ่ม LINE + กล่องเตือนย่อ | `.report-rail` (`position: sticky; top: calc(var(--header-h) + 16px)`) | `.btn.btn-line`, `.notice--warn` |
| 4 | กราฟ equity | (เดิม) | `.perf-figure` |
| 5 | ตารางเมตริกรายปี/รายเดือน | `.data-table` (แยก base `.tbl` ออกจาก `.compare-table` ที่ `style.css:3760+`) | `.compare-wrap` (overflow-x) |
| 6 | ข้อจำกัดของ backtest เป็น bullet | `.report-caveats` | |
| 7 | คำเตือน | | `.disclaimer` |

ตัด `ea2000_line_cta()` ท้ายหน้าออก เพราะ rail มีปุ่มแล้ว

**Component ใหม่ที่ต้องสร้าง**: `ea2000_data_table( $rows_text )` ใน `functions.php` · ยกตรรกะแปลง `"a|b|c"` เป็นตารางที่ hardcode อยู่ใน `template-pricing.php:89-140` ออกมาใช้ร่วมกัน (ตอนนี้ตรรกะ parse `|`, ตรวจ `✓`/`✗` ฝังอยู่ในเทมเพลต ผิดหลักแยกงาน)

### ขั้น 3 · `/forward-test/` · โครง "บันทึกเดินสด"

ตอนนี้ `/backtest/` กับ `/forward-test/` เรนเดอร์ออกมาเหมือนกันทุกบรรทัด เพราะค่าสถิติยังเป็น placeholder ทั้งหมด · หน้านี้ต้องสื่อว่า "เวลาเดินไปเรื่อย ๆ" ไม่ใช่สแนปช็อต

| # | Section | คลาสใหม่ | ใช้ซ้ำ |
|---|---|---|---|
| 1 | หัวชิดซ้าย + จุดกะพริบ + วันที่อัปเดต + **ปุ่ม verified เป็นปุ่มหลักตรงนี้** | `.live-hero` `.live-badge` | keyframe จุดกะพริบของ `.live-strip-label i` ที่มีอยู่แล้ว |
| 2 | ภาพ/embed เต็มความกว้าง `.container-wide` (ตั้งใจให้กว้างกว่าหน้า backtest) | `.fw-figure` | `.perf-figure` |
| 3 | ตารางผลรายเดือน zebra เรียงล่าสุดขึ้นก่อน | `.fw-months` | `.data-table` |
| 4 | ไทม์ไลน์เหตุการณ์จริง (เริ่มรัน · ปรับพารามิเตอร์ · ช่วง drawdown · ช่วงข่าวแรง) | `.fw-log` `.fw-log-date` (เส้นตั้งด้วย `::before`) | `.step-num` (`style.css:1330+`) |
| 5 | แถบข้อมูลบัญชี (โบรกเกอร์ · เลเวอเรจ · ทุนเริ่มต้น) | `.inline-meta` `.fw-conditions` | |
| 6 | คำเตือน | | `.disclaimer` |

**เงื่อนไขกำกับที่ต้องล็อกในโค้ด**: ให้ `.fw-months` และ `.stats-grid` แสดงได้ก็ต่อเมื่อ `forward_link_url` มีค่าจริง (มีลิงก์ตรวจสอบภายนอก) มิฉะนั้นซ่อนทั้งบล็อก · ธีมมี `ea2000_is_placeholder()` (`functions.php:688`) กันไว้ชั้นหนึ่งแล้ว แต่ต้องเพิ่มชั้นนี้ เพราะโครงหน้าแบบตารางรายเดือนจะดึงให้กรอกตัวเลขโดยไม่มีแหล่งอ้างอิง

### ขั้น 4 · `/pricing/` · ตารางเปรียบเทียบมาก่อน

ตอนนี้ (`template-pricing.php:26-82` แล้ว `:95-142`) วางการ์ดราคา 3 ใบก่อน แล้วค่อยตาราง และตารางถูกยัดใน `.container-narrow` 860px ทั้งที่เป็นตารางหลายคอลัมน์

| # | Section | คลาสใหม่ | ใช้ซ้ำ |
|---|---|---|---|
| 1 | หัวเตี้ยชิดซ้าย + ลิงก์ข้ามไปแต่ละแพ็กเกจ | `.pagehead--left` `.price-anchor-nav` | |
| 2 | **ตารางเปรียบเทียบเต็ม `.container`** · `thead th` เป็น `position: sticky; top: var(--header-h)` และหัวคอลัมน์คือ ชื่อแพ็กเกจ + ราคา + ปุ่ม | `.compare-sticky` | `.compare-wrap`, `.compare-table`, `.cell-yes`, `.cell-no`, `.btn-block` |
| 3 | การ์ด 3 ใบย่อขนาด วางใต้ตารางเป็นสรุป | `.price-cards--summary` `.price-card--sm` | `.price-card`, `.price-flag` |
| 4 | คำถามเรื่องราคาโดยเฉพาะ | `.price-faq` | `details.faq-item`, `.faq-list` |
| 5 | เงื่อนไขชำระเงิน/ต่ออายุ ตัวเล็ก | `.price-terms` | |

**สำคัญ**: `thead th` ที่ sticky ต้องมี **พื้นทึบ** ไม่งั้นเนื้อหาจะทะลุขึ้นมา และต้องเป็นสีที่เข้ากับ header เข้มด้านบน · แนะนำ `background: var(--surface)` ทึบ พร้อม `box-shadow: 0 1px 0 var(--border)`

Mobile fallback: แปลงตารางเป็นการ์ดต่อคอลัมน์ด้วย `data-label` + `::before` ซึ่ง **ต้องเพิ่ม attribute ใน PHP** ไม่ใช่ CSS ล้วน

### ขั้น 5 · `/how-to-install/` · stepper 2 คอลัมน์

ตอนนี้ (`template-install.php:22-79`) สลับความกว้าง container 3 ครั้งโดยไม่มีเหตุผลทางสายตา (`:23` narrow → `:45` container → `:75` narrow) และ `.guide` ยังถูกบีบกลับด้วย `max-width: 860px` ที่ `style.css:3821-3822` อยู่ดี

| # | Section | คลาสใหม่ | ใช้ซ้ำ |
|---|---|---|---|
| 1 | หัว 2 คอลัมน์ · ซ้าย h1 + เวลาโดยประมาณ + `.req-box` · ขวา ภาพผลลัพธ์ปลายทาง | `.install-hero--split` | `.req-box` |
| 2 | โครง grid `220px minmax(0,1fr)` | `.install-layout` | |
| 3 | rail ซ้าย sticky · รายการ 6 ขั้น ไฮไลต์ `.is-current` ตามการเลื่อน + ปุ่ม LINE ปักท้าย | `.install-progress` `.is-current` | ต่อยอด IntersectionObserver เดิมที่ `assets/js/main.js:158-162` |
| 4 | คอลัมน์ขวา `ol.guide` เดิม + เวลาต่อขั้น + กล่องเตือนข้อผิดพลาดที่พบบ่อย | `.guide-note` `.guide-step-head` | `.guide`, `.guide-step`, `.guide-num`, `.guide-img` |
| 5 | ปัญหาที่พบบ่อยท้ายหน้า | `.install-trouble` | `details.faq-item` |

Breakpoint 960px: ยุบ rail เป็นแถบ chip แนวนอน sticky ด้านบน · ลบ `max-width: 860px` ที่ `style.css:3821` เพราะความกว้างจะถูกคุมโดย grid แล้ว

**พร้อมกันให้แก้**: `template-install.php:66` รูป `step-01..06` ไม่มี `width`/`height` (ตรงกับข้อ CLS ใน audit เทคนิค) · ใส่ทั้งสองค่า

### ขั้น 6 · `/risk-disclosure/` + `page.php` · ตระกูลเอกสาร

หน้าคำเตือนตอนนี้หน้าตาเหมือนหน้าขาย: `ea2000_page_hero()` (`template-risk.php:14`) มี `.phero-bg > .ember-a/.ember-b` แสงเรืองสีแบรนด์ (`functions.php:952-956`) เนื้อหาเป็นการ์ดขาวลอย `.riskdoc-block` (`style.css:3968-3974`) และปิดท้ายด้วยแบนเนอร์ขาย (`:66`)

| # | Section | คลาสใหม่ | ใช้ซ้ำ |
|---|---|---|---|
| 1 | หัวเรียบชิดซ้าย ไม่มี gradient: h1 + "ปรับปรุงล่าสุด" + ปุ่มพิมพ์ | `.legal-head` | |
| 2 | grid `240px minmax(0,720px)` | `.legal-layout` | `.container-doc` |
| 3 | สารบัญ sticky สร้างอัตโนมัติจาก `rp_block{n}_title` ที่มีค่าจริง | `.legal-toc` | `.toc` / `.toc-title` / `.toc a` (`style.css:3000-3046` ยกจาก `single.php` มาเป็น component ร่วม) |
| 4 | เนื้อหา: หัวข้อเลข 1. / 1.1 ชิดซ้าย เส้นคั่นบาง ไม่มีกล่อง ไม่มีเงา `line-height: 1.9` | `.legal-body` | typography ของ `.entry-content` |
| 5 | กล่องเตือนบนสุด **ห้ามลดทอน** (CLAUDE.md ข้อ 3.3) | | `.riskdoc-intro` → `.notice--warn` |
| 6 | ปิดท้ายด้วยข้อความติดต่อแบบเรียบ **ไม่ใช่ปุ่มขาย** | `.legal-contact` | |

ใช้ CSS counter จริงแทนการ `str_pad` ตัวเลขใน PHP (`template-risk.php:51`) · `counter-reset: risk` ที่ `style.css:3963` ประกาศไว้แล้วแต่ไม่มีใครใช้ · เพิ่ม `@media print`

`page.php:32-45` ให้ใช้ `.legal-head` + `.legal-layout` ชุดเดียวกัน และแก้ `.container-narrow` เดี่ยว ๆ ที่ `:48` ให้เข้าระบบเดียวกับหน้าอื่น (ที่อื่นทั้งธีมใช้ `.container.container-narrow`)

### ขั้น 7 · `/articles/` + `single.php`

`template-blog.php:26-44` ใช้ `.page-hero.archive-hero` กลางจอเหมือนทุกหน้า แล้วต่อด้วยกริด 3 คอลัมน์เท่ากันหมด (`:50-59`) ไม่มีลำดับความสำคัญ ทั้งที่หน้านี้จะเป็นตัวรับทราฟฟิก SEO หลัก

| # | Section | คลาสใหม่ | ใช้ซ้ำ |
|---|---|---|---|
| 1 | หัวชิดซ้าย + แถบหมวดหมู่แนวนอน (แสดงเมื่อมีหมวดจริง) | `.editorial-head` `.cat-chips` | |
| 2 | บทความล่าสุด 1 ชิ้นเต็มความกว้าง ภาพ 16:9 ซ้าย ข้อความขวา | `.feature-post` | `.post-card-thumb` |
| 3 | กริดที่เหลือ · ใบที่ 1 และ 6 กินกว้าง 2 คอลัมน์ด้วย `:nth-child()` | `.posts-grid--editorial` | `.post-card`, `.posts-grid`, `.load-more` |
| 4 | rail ขวา sticky จอ ≥1100px: บทความอ่านมากที่สุด + ปุ่ม LINE | `.blog-rail` | `ea2000_get_post_views()` (`functions.php:1103`) |

`single.php` เป็นหน้าที่ต่างจากคนอื่นมากที่สุดอยู่แล้ว (reading progress `:19`, TOC `:64-74`, share `:82-87`, related rail `:127-160`) · แก้จุดเดียว: เพิ่ม `.article-layout` = grid `minmax(0,720px) 240px` เฉพาะจอ ≥1200px แล้วย้าย `.toc` ไปเป็น `.toc--rail` sticky ในคอลัมน์ขวา · จอเล็กกว่านั้นคงเป็นกล่องบนสุดเหมือนเดิม

### ขั้น 8 · หน้าแรก (`front-page.php`)

หน้าแรกมี 21 ก้อนในโค้ด เรนเดอร์จริง 14 ก้อน · อีก 7 ปิดหรือไม่มีข้อมูล ทำให้เหลือแต่ข้อความโฆษณาล้วนโดยไม่มีหลักฐานสักชิ้น (ทีมงาน `:218` ปิด, ผลทดสอบ `:467` ไม่มีข้อมูล, รีวิว `:566` ปิด, บทความ `:733` ไม่มีบทความ, ภาพจริง `:350` เป็น placeholder 2 ใบ)

**คำแนะนำ**: ตัดเหลือ 7-8 ก้อน แล้วเพิ่มส่วนหลักฐานกลับทีละก้อนเมื่อเจ้าของส่งข้อมูล
> hero → ปัญหา → EA2000 คืออะไร → ขั้นตอนใช้งาน → เหมาะกับใคร → ราคา → FAQ → คำเตือน

แก้สามอย่างพร้อมกัน:

1. **กริดการ์ดซ้ำ 5 ชุด 21 ใบ** (`grep -c 'grid grid-' front-page.php` = 5) ให้แต่ละก้อนใช้ "รูปทรง" ต่างกัน: `#pain` เป็นแถวรายการแนวนอน `.painline` ไม่มีกล่อง · `#features` เก็บกริด 3 ใบไว้ (ให้เป็นที่เดียวที่มีการ์ดไอคอน) · `#explore` เป็นแถบลิงก์กว้างเต็ม `.hub-strip` · pricing teaser เป็นแถวเดียว 3 คอลัมน์แบบตาราง
2. **`.sec-head` กลางจอซ้ำ 10 ครั้ง** ให้มี modifier `--left` สลับกับ `--center`
3. **ปุ่ม LINE ซ้อน 5 จุด** (`:432` mid-cta, `:778` section.cta, `footer.php:52` footer-cta, `:138` mobile nav, `:148/155` float) โดย 3 จุดท้ายติดกันเป็นพืด · เก็บ `.mid-cta` + `.footer-cta` + ปุ่มลอย รวม 3 จุดพอ แล้วตัด `.section.cta#cta` ของหน้าแรกทิ้ง · เอา `.cta-logo` 96px (`:782`) ออกด้วย เพราะโลโก้ปรากฏที่ header และ footer ในจอเดียวกันอยู่แล้ว

**hero**: `.hero-candles` SVG แท่งเทียน 8 แท่งที่วาดฟิกซ์ไว้ (`:36-51`) และคลาส `.ember-a/-b/-c` เป็นแนวคิด "เชื้อไฟ" ที่สืบทอดมาจากธีมต้นทาง ไม่เข้ากับแบรนด์เขียว-เงิน · เปลี่ยนชื่อคลาสเป็น `.glow*` และแทนแท่งเทียนด้วย `.hero-facts` แถบ 3 ค่าที่พิสูจน์ได้โดยไม่ต้องอ้างผลกำไร (คู่เงินที่รองรับ · แพลตฟอร์ม MT5 · ช่องทางซัพพอร์ต) · **ห้ามใส่ตัวเลขผลตอบแทนหรือ % ชนะใน hero จนกว่าจะมีหลักฐานตรวจสอบได้**

---

## 3) แยกงาน: CSS ล้วน vs แตะเทมเพลต vs ต้องมี Customizer setting

### 3.1 CSS ล้วน · deploy ได้ทันที ไม่กระทบเนื้อหา

- **ทั้งหมดของข้อ 1** (header + footer + drawer + บาร์ล่างมือถือ + reading progress) เป็น CSS ล้วน 100% เพราะ `header.php` และ `footer.php` ใช้ setting และคลาสที่มีอยู่แล้วครบ
- แก้บั๊กเมนูย่อยมือถือ (ข้อ 0.1)
- ชั้นจังหวะหน้า `--section-pad` / `--content-w` / `.container-doc` / `.container-wide`
- รวมคลาสซ้ำ 3 คู่ (ถ้าเก็บชื่อเดิมเป็น alias)
- `.compare-sticky thead th`
- `.article-layout` + `.toc--rail` ของ `single.php` (TOC มีอยู่แล้ว แค่ย้ายด้วย grid)
- `.posts-grid--editorial` ที่ใช้ `:nth-child()`
- ลบ `max-width: 860px` ที่ `style.css:3821`

### 3.2 ต้องแตะเทมเพลต (PHP)

| งาน | ไฟล์ | เหตุผล |
|---|---|---|
| guard ข้อความแอดมิน | `front-page.php:387` | ต้องเรียก `current_user_can()` |
| `class="layout-*"` บน `<main>` | ทุก `template-*.php`, `page.php`, `single.php`, `front-page.php` | เพิ่ม attribute |
| `.report-layout` / `.install-layout` / `.legal-layout` | `template-backtest.php`, `template-install.php`, `template-risk.php`, `page.php` | ต้องเพิ่ม wrapper div |
| `data-label` บน `<td>` สำหรับ mobile table | `template-pricing.php:122` | CSS `::before` อ่านค่าจาก attribute |
| `width`/`height` รูปขั้นตอนติดตั้ง | `template-install.php:66` | แก้ CLS |
| แยก `ea2000_data_table()` | `functions.php` (ใหม่) + `template-pricing.php:89-140` | ยกตรรกะ parse ออกจากเทมเพลต |
| `.footer-bottom-bar` แถบล่างเข้มพิเศษ | `footer.php:112-131` | ต้องมี wrapper นอก `.container` |
| CSS counter แทน `str_pad` | `template-risk.php:51` | |
| ตัด `ea2000_line_cta()` ในหน้าที่มี rail | `template-backtest.php:86`, `template-install.php:82` | |
| ตัดรูป wordmark ใหม่ (ตัดบรรทัดรองออก) | `assets/img/` + `functions.php:191` | เปลี่ยนชื่อไฟล์เสมอ |

### 3.3 ต้องเพิ่ม Customizer setting ใหม่ (CLAUDE.md ข้อ 3.1 ห้าม hardcode)

**พบข้อความ hardcode ที่ผิดหลักอยู่แล้วในโค้ดปัจจุบัน · ต้องแก้ไปพร้อมกัน**

| ข้อความที่ hardcode | ไฟล์:บรรทัด | setting ที่ต้องเพิ่ม |
|---|---|---|
| `'มีคำถาม? ทักมาคุยกับเราได้เลย'` | `functions.php:979` | `line_cta_title_default` |
| `'สอบถามรายละเอียด การติดตั้ง และความเหมาะสมกับทุนของคุณได้ทาง LINE'` | `functions.php:980` | `line_cta_text_default` |
| `'ทัก LINE เพื่อสอบถาม'` | `functions.php:989` | `line_cta_btn_label` |
| `'ความรู้เรื่อง EA, MT5 และการบริหารความเสี่ยง'` | `template-blog.php:36` | `blog_subtitle` |
| kicker `'Backtest'` | `template-backtest.php:17` | `backtest_kicker` |
| kicker `'Risk Disclosure'` | `template-risk.php:14` | `riskpage_kicker` |
| kicker `'How to Install'` | `template-install.php:14` | `install_kicker` |
| kicker `'Pricing'` | `template-pricing.php:14` | `pricing_kicker` |
| `'ติดต่อทีมงาน'` | `footer.php:66` | `footer_contact_fallback_label` |
| `'สิ่งที่ต้องเตรียม'` | `template-install.php:34` | `install_req_title` |

**setting ใหม่สำหรับโครงหน้าใหม่**

| setting | ชนิด | ใช้ที่ |
|---|---|---|
| `show_explore` | checkbox | `front-page.php:657` (ก้อนเดียวที่ยังไม่มีสวิตช์) |
| `bt_conditions` | textarea | `.report-meta` เงื่อนไขทดสอบ (สัญลักษณ์ · ช่วงวันที่ · timeframe · โมเดล spread · ทุนเริ่มต้น) |
| `bt_caveats` | textarea | `.report-caveats` ข้อจำกัดของ backtest |
| `bt_table_rows` | textarea | ตารางเมตริก (รูปแบบ `a|b|c` เหมือน `pricing_compare_rows`) |
| `fw_updated_at` | text | วันที่อัปเดตล่าสุดใน `.live-hero` |
| `fw_months_rows` | textarea | `.fw-months` |
| `fw_log_items` | textarea | `.fw-log` ไทม์ไลน์ |
| `fw_account_meta` | textarea | `.inline-meta` โบรกเกอร์ · เลเวอเรจ · ทุน |
| `inst_step{1..6}_time` | text | เวลาโดยประมาณต่อขั้น |
| `inst_step{1..6}_note` | textarea | `.guide-note` ข้อผิดพลาดที่พบบ่อย |
| `install_trouble_rows` | textarea | `.install-trouble` |
| `price_terms` | textarea | `.price-terms` เงื่อนไขชำระเงิน |
| `legal_updated_label` | text | "ปรับปรุงล่าสุด" |
| `legal_contact_text` | textarea | `.legal-contact` แทนแบนเนอร์ขาย |

**วิธีเพิ่ม**: ทั้งหมดใส่ผ่านโครงเดิม 2 ที่เท่านั้น · ค่า default ใน `ea2000_defaults()` (`functions.php:177+`) และรายการ field ใน array `$sections` ของ `inc/customizer.php` (loop ที่ `:622-702` จัดการ sanitize/control ให้อัตโนมัติแล้ว: `checkbox` → `ea2000_sanitize_checkbox`, `textarea` → `sanitize_textarea_field`, `image` → `esc_url_raw` + `WP_Customize_Image_Control`)

**ไม่ต้องเพิ่ม**: setting สีของ header/footer · สีเป็นระบบดีไซน์ ไม่ใช่เนื้อหา · ถ้าเปิดให้เลือกสีเองจะพัง contrast ทันทีและขัดหลัก "ตรวจ contrast แล้วทุกคู่หลัก" ที่บันทึกไว้ใน CLAUDE.md

---

## 4) Component library · ให้ทุกหน้าเป็นระบบดีไซน์เดียว

### 4.1 Layout primitives

| คลาส | สถานะ | ค่า |
|---|---|---|
| `.container` | มีแล้ว `:198` | 1140px |
| `.container-narrow` | มีแล้ว `:203` | 860px |
| `.container-doc` | **ใหม่** | 720px · เอกสาร |
| `.container-wide` | **ใหม่** | 1240px · รายงาน/ตาราง |
| `.section` | มีแล้ว `:209` | ใช้ `--section-pad` |
| `.layout-landing` / `.layout-report` / `.layout-doc` / `.layout-tool` | **ใหม่** | บน `<main>` |
| `.report-layout` / `.install-layout` / `.legal-layout` / `.article-layout` | **ใหม่** | grid 2 คอลัมน์ ยุบที่ 960-1200px |

### 4.2 Rail (แกนกลางของดีไซน์ใหม่ · ทุกตัวใช้ IntersectionObserver ตัวเดียวกันที่ `main.js:158`)

| คลาส | หน้า | เนื้อหา |
|---|---|---|
| `.report-rail` | backtest | สารบัญ + ปุ่ม LINE + คำเตือนย่อ |
| `.install-progress` | how-to-install | 6 ขั้น + `.is-current` |
| `.legal-toc` | risk, page.php | สารบัญเอกสาร |
| `.blog-rail` | articles | อ่านมากสุด + ปุ่ม LINE |
| `.toc--rail` | single.php | สารบัญบทความ |

ทุกตัวใช้ `position: sticky; top: calc(var(--header-h) + 16px)` เท่ากัน · ค่านี้ผูกกับ `--header-h` (`style.css:78`) จึงปรับความสูง header ที่เดียวได้

### 4.3 Head / Hero

| คลาส | มาจาก |
|---|---|
| `.pagehead` + `--center` `--left` `--split` | รวม `.phero` `:3670` กับ `.page-hero` `:2340` |
| `.report-hero` `.live-hero` `.install-hero--split` `.legal-head` `.editorial-head` | ใหม่ · สืบทอด `.pagehead` |
| `.sec-head` + `--left` `--center` | มีแล้ว `:216` เพิ่ม modifier |
| `.kicker` `.live-badge` | มีแล้ว / ใหม่ |

### 4.4 Data / Content

| คลาส | มาจาก |
|---|---|
| `.tbl` (base) → `.compare-table` + `.data-table` `.fw-months` | แยก base ออกจาก `:3760+` |
| `.compare-wrap` | มีแล้ว · `overflow-x: auto` |
| `.stat` / `.stats-grid` | มีแล้ว |
| `.perf-figure` `.report-figure` `.fw-figure` | มีแล้ว / ใหม่ |
| `.report-meta` `.inline-meta` `.fw-conditions` | ใหม่ · `<dl>` 2 คอลัมน์ |
| `.fw-log` + `.fw-log-date` | ใหม่ · ไทม์ไลน์ |
| `.numchip` + `--lg` | รวม `.guide-num` `:3838` กับ `.riskdoc-num` `:3986` |

### 4.5 Notice / Card / Action

| คลาส | มาจาก |
|---|---|
| `.notice` + `.notice--warn` | รวม `.disclaimer` `:1421` กับ `.riskdoc-intro` `:3924` · ใช้ `--warn-bg` `--warn-text` `--warn-border` |
| `.card` | มีแล้ว `:1199` · **จำกัดให้ใช้เฉพาะ `#features`** |
| `.painline` `.hub-strip` `.price-cards--summary` `.feature-post` | ใหม่ · แทนที่กริดการ์ดที่ซ้ำ |
| `.btn` `.btn-line` `.btn-ghost` `.btn-fire` `.btn-block` `.btn-lg` | มีแล้ว · ครบ ไม่ต้องเพิ่ม |
| `.faq-list` + `details.faq-item` | มีแล้ว · ใช้ซ้ำใน `.price-faq` และ `.install-trouble` |
| `.social-link` | มีแล้ว `:5286` · เพิ่มตัวแปรพื้นมืด |

### 4.6 Token

- โทนสว่าง: `style.css:20-79` เดิม **ไม่แตะ**
- โทนมืด: `--ink-*` + alias `--header-*` / `--footer-*` (ข้อ 1.1)
- จังหวะ: `--section-pad` `--content-w`
- `--header-h: 72px` `:78` ผูกกับ `scroll-margin-top` `:160` และ sticky rail ทุกตัว

---

## 5) ความเสี่ยงและวิธีตรวจ

### 5.1 ตารางความเสี่ยง

| # | ความเสี่ยง | โอกาส | ผลกระทบ | วิธีตรวจ |
|---|---|---|---|---|
| 1 | **GTranslate เปลี่ยนโครงสร้าง** · widget ถูก JS ฉีดหลังโหลด (ยืนยันจาก HTML สด: มีแค่ `<div class="gtranslate_wrapper">` ว่าง) · CSS 130 บรรทัด `!important` ที่ `:801-933` ผูกกับ class ของปลั๊กอิน | **สูง** | ป้ายภาษาเป็นเม็ดสีขาวโดดบนแถบดำ | เปิดหน้าจริงในเบราว์เซอร์ (ไม่ใช่ curl) รอ JS โหลดเสร็จ กด dropdown ตรวจว่าพื้นเป็น `--header-panel` และตัวอักษรเป็น `--header-muted` · ตรวจลูกศรว่า `filter: invert(1)` ทำงาน · **ทางเลี่ยงถาวร: ปิด `show_language_switcher` แล้วถอดปลั๊กอิน** |
| 2 | **ตัด `backdrop-filter` เปลี่ยน containing block** ของ `.site-nav` ที่เป็น `position: fixed` (`:3345`) | ต่ำ | drawer เพี้ยนตำแหน่งบนมือถือ | ทดสอบแล้วว่าเหมือนเดิม (วัด `rect nav: y=72`) แต่ต้องตรวจซ้ำจริงหลัง deploy: เปิดเมนูบนมือถือ เลื่อนหน้าลง ตรวจว่า drawer ยังชิดใต้ header · **ห้ามใส่ `transform` / `filter` / `will-change` / `contain` / `perspective` บน `.site-header` เด็ดขาด** ทุกตัวสร้าง containing block เหมือนกัน |
| 3 | **Elementor Theme Builder** · `header.php:18` และ `footer.php:48` มี guard `elementor_theme_do_location()` · ถ้าเจ้าของติดตั้ง Elementor Pro แล้วสร้าง header/footer template ธีมจะไม่ถูกเรนเดอร์เลย CSS ใหม่จึงไม่ทำงาน | ต่ำ (ตอนนี้เป็น Elementor ฟรี ฟังก์ชันนี้ไม่มี) | header/footer ไม่เปลี่ยนสี | `curl https://ea2000.co/ \| grep -c 'class="site-header"'` ต้องได้ 1 · ถ้าได้ 0 แปลว่า Elementor เข้ามาแทน |
| 4 | **`template-elementor-full-width.php`** เรียก `get_header()`/`get_footer()` จึงได้ header/footer มืดด้วย · ส่วน `template-elementor-canvas.php` ไม่เรียก จึงไม่ได้ | ต่ำ | เพจ Elementor สองแบบหน้าตาไม่ตรงกัน | ตอนนี้ไม่มีเพจไหนใช้ template ทั้งสอง (audit ยืนยันหน้าแรกไม่มี Elementor data) · ถ้าจะใช้ ให้เลือกแบบเดียว · **อย่าเปิด template page ด้วย Elementor** ตาม CLAUDE.md ข้อ 5 |
| 5 | **`/go/` (link hub)** เป็นหน้า standalone ประกาศ `<!DOCTYPE>` เอง ไม่เรียก `get_header()`/`get_footer()` (`template-links.php:64-75`) | ต่ำมาก | ไม่ได้รับ token ใหม่ (ซึ่งถูกแล้ว) | `curl https://ea2000.co/go/ \| grep -c 'site-header'` ต้องได้ 0 (ยืนยันแล้วว่าเป็น 0) · ตรวจสายตาว่าไม่เปลี่ยน · **ห้ามให้ `--ink-*` ไปทับ `:root` ที่หน้านี้ใช้** ตรวจว่ากฎใหม่ทุกข้อ scope ด้วย `.site-header` หรือ `.site-footer` เสมอ |
| 6 | **reveal animation** · `.reveal` เริ่มที่ `opacity: 0` (`:3238`) และรอ IntersectionObserver (`main.js:158-162`) · ถ้า JS พังหรือ observer ไม่ยิง เนื้อหาจะหายทั้งหน้า | กลาง (จะสูงขึ้นเมื่อเพิ่ม `.is-current` ใน observer เดิม) | เนื้อหาหายไปทั้งหน้า | เปิดหน้าโดยปิด JS ต้องยังอ่านได้ (ตอนนี้ยังอ่านไม่ได้ ควรแก้เป็น `.no-js .reveal { opacity: 1 }`) · ตรวจ `prefers-reduced-motion` ว่า `:3578` บังคับ `opacity: 1` ได้จริง · **เพิ่ม `.is-current` เข้า observer เดิม อย่าสร้าง observer ตัวที่สอง** |
| 7 | **แคช Cloudflare ค้างรูปเก่า** · ตามบทเรียนที่บันทึกไว้ใน CLAUDE.md (`link-download-ea2000.png` เคยค้าง) | **สูง** ถ้าเปลี่ยนรูป wordmark | ผู้ใช้เห็นโลโก้เก่า | **เปลี่ยนชื่อไฟล์ทุกครั้ง** ห้ามใช้ชื่อเดิม · ตรวจ `curl -I https://ea2000.co/wp-content/themes/ea2000/assets/img/<ชื่อใหม่>` ต้องได้ 200 · และตรวจว่า `theme_mod` ไม่ค้างชี้ URL เก่า |
| 8 | **CSS ไฟล์เดียว 5844 บรรทัด** · การเพิ่มกฎ dark ~90 ข้อจะทำให้ไฟล์โตขึ้นราว 3 KB (ก่อนบีบ) | ต่ำ | ขนาดโตเล็กน้อย | ยอมรับได้ · ธีมไม่มี build step (CLAUDE.md ข้อ 3.6) · แต่ควรจัดกฎ dark ทั้งหมดไว้เป็นบล็อกเดียวมีคอมเมนต์หัวบล็อก ไม่กระจายแทรกในกฎเดิม เพื่อให้ย้อนกลับได้ |
| 9 | **หัวตาราง sticky ใน `/pricing/`** ชนกับ header sticky | กลาง | หัวตารางซ้อนใต้ header | `top: var(--header-h)` ต้องตรงกับ `.site-header` height เป๊ะ · ถ้าเพิ่ม `.reading-progress` 3px ที่ `z-index: 200` ต้องเผื่อ · ตรวจโดยเลื่อนหน้า pricing ช้า ๆ บนจอ 1280 และ 768 |
| 10 | **ตัวเลขผลทดสอบ** · โครง `.report-*` และ `.fw-*` ที่เสนอ จะดึงให้กรอกตัวเลขผลตอบแทน ซึ่งเป็นเนื้อหา YMYL | **สูง** | ความเสี่ยงด้านกำกับ | ล็อกในเทมเพลต: `.fw-months` และ `.stats-grid` แสดงได้เมื่อ `forward_link_url` มีค่าจริงเท่านั้น · ทุกชุดตัวเลขต้องมี `bt_conditions` / `fw_account_meta` ครบ · **ห้ามคำว่า รับประกัน / การันตี / ผลตอบแทนต่อเดือน ในทุก setting** · `grep -riE "รับประกัน\|การันตี\|กำไรแน่นอน"` ต้องได้ 0 |
| 11 | **คำสัญญาบริการในหน้า pricing** ที่ยกมาจากธีมต้นทาง (ทีมช่วยติดตั้ง/VPS, ไฟล์ Preset, Dashboard, อัปเดตตามรอบ, Support LINE) ยัง "รอเจ้าของยืนยัน" ตาม CLAUDE.md ข้อ 9 | **สูง** | ผิดสัญญาบริการ | ให้เจ้าของกากบาททีละบรรทัดก่อนเปิด index · ลบบรรทัดที่ให้ไม่ได้ออกจาก setting · **อย่าเก็บไว้เพราะกลัวตารางดูโล่ง** |

### 5.2 ลำดับตรวจหลัง deploy (ทำตามลำดับ)

```bash
# 1) ธีมเรนเดอร์จริง ไม่ถูก Elementor แทน
curl -s "https://ea2000.co/?nc=$RANDOM" | grep -c 'class="site-header"'      # ต้อง 1
curl -s "https://ea2000.co/go/?nc=$RANDOM" | grep -c 'site-header'           # ต้อง 0

# 2) CSS ที่ deploy ตรงกับในรีโป
curl -s "https://ea2000.co/wp-content/themes/ea2000/style.css?nc=$RANDOM" -o /tmp/live.css
diff <(tr -d '\r' < /tmp/live.css) "D:/EA VIDEO/ea2000-repo/ea2000/style.css"  # ต้องไม่มี output

# 3) ไม่มีร่องรอยธีมต้นทางกลับเข้ามา
grep -riE "fenix|zaurix|speccub|myfxbook" "D:/EA VIDEO/ea2000-repo/ea2000/"   # เหลือแค่ prefix ea2000_
grep -rn " : \| : " "D:/EA VIDEO/ea2000-repo/ea2000/"                             # ต้อง 0 (ห้าม em/en dash)

# 4) lint ทุกไฟล์ PHP ก่อน commit
"C:/Users/THANAWUT HR/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe/php.exe" -l <ไฟล์>
```

**ตรวจด้วยสายตา** (headless Chrome กว้าง ≥500px เท่านั้น ตาม CLAUDE.md · ห้ามใช้ query `?w=` / `?m=` เพราะเป็น query var ของ WordPress):

| # | สิ่งที่ต้องเห็น |
|---|---|
| 1 | Header ทึบสีเข้ม ไม่ซีดตอนเลื่อนผ่านเนื้อหาขาว |
| 2 | เลื่อนลง 10px แล้ว `.scrolled` เข้มขึ้นและมีเงา |
| 3 | เปิด dropdown "ผลทดสอบ" บนเดสก์ท็อป · พื้น `--ink-raise` ตัวอักษรอ่านออก |
| 4 | **มือถือ: กด "ผลทดสอบ" แล้ว Backtest / Forward Test ต้องปรากฏ** (บั๊กข้อ 0.1) |
| 5 | dropdown ภาษา · ชิป TH / EN ต้องเป็นเขียวสว่าง ไม่ใช่เขียวเข้มจมพื้น |
| 6 | บาร์ล่างมือถือเข้ม ปุ่ม LINE เขียวเด่นเป็นจุดเน้นเดียว |
| 7 | Footer: กล่อง CTA, การ์ด "เตรียมก่อนเริ่ม", social, copyright อ่านออกครบ |
| 8 | รอยต่อ section สว่างสุดท้ายกับ footer เข้ม คมไม่มีเส้นแปลกปลอม |
| 9 | โลโก้ wordmark ใน header และ footer คมชัด ไม่มีขอบขาวเป็นคราบ |
| 10 | `/go/` ยังเป็นโทนเดิมทุกประการ |

---

## 6) สรุปลำดับลงมือ

| ขั้น | งาน | แรงที่ใช้ | ผลที่เห็น |
|---|---|---|---|
| **0** | แก้บั๊กเมนูย่อยมือถือ + guard ข้อความแอดมิน + `show_explore` + ตัด inline style | 30 นาที | ปิดบั๊กใช้งานไม่ได้ |
| **1** | Header + Footer โทนเข้ม (CSS ล้วน ~90 กฎ) | ครึ่งวัน | **เจ้าของเห็นความเปลี่ยนแปลงทันที** |
| **2** | ชั้นจังหวะหน้า + รวมคลาสซ้ำ 3 คู่ + เปลี่ยน risk/page.php เป็น `layout-doc` | ครึ่งวัน | ทุกหน้ารู้สึกต่าง โดยยังไม่แตะ component |
| **3** | `.report-layout` + `.report-rail` ใช้กับ backtest | 1 วัน | หน้าแรกที่มี rail |
| **4** | forward เป็นไทม์ไลน์เดินสด | 1 วัน | สองหน้าผลทดสอบเลิกเหมือนกัน |
| **5** | pricing comparison-first + install stepper (ใช้ rail ตัวเดียวกับขั้น 3) | 1 วัน | |
| **6** | blog editorial grid + TOC rail ของ single.php | ครึ่งวัน | |
| **7** | ตัดรูป wordmark ใหม่ + ย่อรูปทั้งชุดตาม audit เทคนิค | ครึ่งวัน | น้ำหนักหน้าลดลงราว 300 KB |

ทุกขั้นต้อง `php -l` ผ่าน · grep ไม่พบร่องรอยธีมต้นทาง · ไม่มี em/en dash · LF ทั้งหมด · ก่อน `git push` ตรวจ `git remote -v` เป็นรีโป EA2000 และ `git rev-parse --show-toplevel` อยู่ใน `ea2000-repo`

**ยังไม่ได้แก้ไฟล์ใด ไม่ได้รันคำสั่ง git เขียน และไม่ได้ POST ไปเว็บจริง** ทั้งหมดเป็นการอ่านและการทดสอบใน scratchpad เท่านั้น

**ไฟล์อ้างอิงที่สร้างไว้** (scratchpad · ใช้ต่อได้ตอนลงมือ):
- `...\scratchpad\design\contrast.js` · เครื่องคิดเลข WCAG contrast (รับ hex คู่ หรือ `hsl <hex>`)
- `...\scratchpad\design\dark.css` · กฎ dark ทั้งชุดที่ทดสอบแล้ว พร้อมคัดลอกลง `style.css`
- `...\scratchpad\design\mock.html` / `mock-m.html` · mock เดสก์ท็อปและมือถือที่โหลด `style.css` จริง
- `...\scratchpad\design\wordmark-on-dark.png`, `zoom.png` · หลักฐานเรื่องโลโก้
- `...\scratchpad\design\mock-desktop2.png`, `mock-mobile-fixed.png` · ผลลัพธ์หลังแก้ครบ