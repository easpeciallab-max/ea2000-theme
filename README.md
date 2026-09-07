# EA2000 Theme

ธีม WordPress ของ EA2000 (https://ea2000.co/) หน้า landing ภาษาไทยสำหรับ EA บน MetaTrader 5

- โค้ดธีมอยู่ใน `ea2000/` · WP Pusher ติดตั้งจาก subdirectory นี้
- ไม่มี build step · ต้องการ WordPress 6.0+ และ PHP 8.1+
- ทุกข้อความ รูป ราคา และลิงก์ ตั้งค่าผ่าน Customizer ไม่ hardcode ในเทมเพลต
- ไม่มีไฟล์ EA (.ex5/.zip) ใน repo นี้
- บริบทงานสำหรับผู้ช่วย AI อยู่ใน `CLAUDE.md` · สคริปต์ตรวจอยู่ใน `tests/`

## ตรวจก่อน commit

- `php -l` ทุกไฟล์ใน `ea2000/`
- `grep -ri "fenix\|zaurix\|speccub"` ต้องไม่พบ
- ห้ามใช้ em dash / en dash ในโค้ดและเนื้อหา
