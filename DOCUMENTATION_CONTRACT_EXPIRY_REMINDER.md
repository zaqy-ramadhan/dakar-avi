# Contract Expiry Reminder System - Dokumentasi

## 📋 Overview

Sistem ini mengirimkan email otomatis setiap awal bulan (tanggal 1 pukul 08:00 WIB) untuk memberikan reminder tentang karyawan yang kontraknya akan berakhir dalam 3 bulan ke depan.

## 🎯 Fitur Utama

✅ **Email Otomatis** - Dikirim otomatis setiap tanggal 1 bulan  
✅ **Excel Attachment** - Laporan detail dalam format Excel yang rapi  
✅ **Multiple Recipients** - Dukungan untuk mengirim ke banyak email  
✅ **Professional Template** - Email template yang modern dan informatif  
✅ **Scheduling** - Terintegrasi dengan Laravel scheduler  

## 📁 Struktur File yang Dibuat

```
app/
├── Console/
│   └── Commands/
│       └── SendContractExpiryReminder.php  (Command untuk mengirim email)
├── Exports/
│   └── ContractExpiryReminderExport.php   (Export Excel)
└── Mail/
    └── ContractExpiryReminder.php         (Mailable class)

resources/views/emails/
├── contract-expiry-reminder.blade.php     (Email template)
└── layout.blade.php                       (Email layout)

config/
└── mail.php                               (Updated dengan recipients config)

routes/
└── console.php                            (Updated dengan schedule)
```

## ⚙️ Konfigurasi

### 1. Konfigurasi Email (.env)

Pastikan konfigurasi SMTP sudah benar di file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=astra-visteon.com
MAIL_PORT=587
MAIL_USERNAME=buddy.service@astra-visteon.com
MAIL_PASSWORD='M1keda2024#'
MAIL_ENCRYPTION=null
MAIL_SMTP_AUTH=true
MAIL_AUTH_MODE=LOGIN
MAIL_FROM_ADDRESS="buddy.service@astra-visteon.com"
MAIL_FROM_NAME="Astra Visteon Indonesia"
```

### 2. Konfigurasi Email Recipients

Edit file `config/mail.php` untuk mengatur email penerima:

```php
'contract_expiry_recipients' => [
    'buddy.service@astra-visteon.com',
    'hr@astra-visteon.com',
    'manager@astra-visteon.com',  // Tambahkan email lain jika diperlukan
],
```

## 🚀 Cara Menggunakan

### Menjalankan Command Secara Manual

Jalankan command berikut dari terminal:

```bash
php artisan email:contract-expiry-reminder
```

### Menjalankan dengan Email Recipients Tertentu

```bash
php artisan email:contract-expiry-reminder --recipients="email1@example.com,email2@example.com"
```

### Penjadwalan Otomatis

Sistem sudah dikonfigurasi untuk berjalan otomatis setiap tanggal 1 bulan pukul 08:00 WIB.

#### Setup Cron Job

Tambahkan cron job pada server untuk menjalankan laravel scheduler:

**Linux/Mac:**
```bash
* * * * * cd /path/to/dakar && php artisan schedule:run >> /dev/null 2>&1
```

**Windows (jika menggunakan sistem yang mendukung scheduled tasks):**
- Buat scheduled task yang menjalankan: `php artisan schedule:run`
- Jalankan setiap 1 menit

#### Verifikasi Penjadwalan

Untuk memverifikasi bahwa scheduler berjalan dengan baik:

```bash
php artisan schedule:list
```

## 📧 Email Template

Email yang dikirim mencakup:

- ✅ Header dengan judul dan tanggal
- ✅ Ringkasan jumlah karyawan
- ✅ Tabel yang menampilkan 5 karyawan pertama (preview)
- ✅ Catatan penting
- ✅ Call to action
- ✅ Excel attachment dengan data lengkap

**Lokasi Template:**
- Konten: `resources/views/emails/contract-expiry-reminder.blade.php`
- Layout: `resources/views/emails/layout.blade.php`

Anda dapat mengedit template ini sesuai kebutuhan perusahaan.

## 📊 File Excel yang Dihasilkan

File Excel akan berisi kolom-kolom berikut:

| Kolom | Deskripsi |
|-------|-----------|
| NPK | Nomor Pegawai Karyawan |
| Nama | Nama Lengkap Karyawan |
| Posisi | Jabatan/Posisi Karyawan |
| Departemen | Nama Departemen |
| Divisi | Nama Divisi |
| Tanggal Mulai Kontrak | Tanggal awal kontrak |
| Tanggal Akhir Kontrak | Tanggal akhir kontrak |
| Sisa Hari | Jumlah hari tersisa |
| Status | Status Kepegawaian |

### Styling Excel:
- ✅ Header dengan warna biru latar dan teks putih
- ✅ Alternating row colors untuk kemudahan pembacaan
- ✅ Border pada semua cell
- ✅ Auto-width kolom untuk optimal display

## 🔍 Query yang Digunakan

Command akan mencari employee dengan kriteria:

```
1. Memiliki employee job record
2. Job status = "kontrak"
3. End date antara hari ini dan 3 bulan ke depan
4. Dengan relasi position, department, dan division
```

## 📝 Customization

### 1. Mengubah Waktu Pengiriman

Edit `routes/console.php`:

```php
// Current: Setiap tanggal 1 jam 08:00
Schedule::command(SendContractExpiryReminder::class)
    ->monthlyOn(1, '08:00')
    
// Contoh: Setiap hari Senin jam 09:00
// Schedule::command(SendContractExpiryReminder::class)
//     ->weeklyOn(1, '09:00')  // 1 = Monday
```

### 2. Mengubah Periode Kontrak (dari 3 bulan)

Edit `app/Console/Commands/SendContractExpiryReminder.php`:

```php
// Ubah ini:
$threeMonthsLater = $now->clone()->addMonths(3);

// Menjadi misalnya 6 bulan:
$threeMonthsLater = $now->clone()->addMonths(6);
```

### 3. Mengubah Template Email

Edit `resources/views/emails/contract-expiry-reminder.blade.php`

Variabel yang tersedia:
- `$employees` - Collection dari employee objects
- `$totalEmployees` - Jumlah total karyawan
- `$monthInfo` - Informasi bulan (jika ada)

## 🧪 Testing

### Test Command Secara Manual

```bash
php artisan email:contract-expiry-reminder
```

### Test dengan Email Penerima Dummy

```bash
php artisan email:contract-expiry-reminder --recipients="test@example.com"
```

### Check Log

Cek file log untuk debugging:
```bash
tail -f storage/logs/laravel.log
```

## ❗ Troubleshooting

### Email tidak terkirim

1. **Verifikasi SMTP Configuration:**
   ```bash
   php artisan tinker
   >>> Mail::to('test@example.com')->send(new \App\Mail\ContractExpiryReminder(collect([]), 'test.xlsx'))
   ```

2. **Check Laravel Log:**
   ```bash
   tail storage/logs/laravel.log
   ```

3. **Pastikan temp folder writable:**
   ```bash
   chmod 775 storage/
   ```

### Command tidak jalan otomatis

1. **Check cron job:**
   ```bash
   crontab -l
   ```

2. **Verifikasi scheduler:**
   ```bash
   php artisan schedule:list
   ```

3. **Test scheduler manual:**
   ```bash
   php artisan schedule:run
   ```

## 🔐 Security Notes

- Email credentials disimpan di `.env` - **jangan commit ke git**
- Temporary Excel files dihapus otomatis setelah email terkirim
- Pastikan `storage/` folder tidak accessible dari public

## 📞 Support

Jika ada pertanyaan atau perlu customization lebih lanjut, hubungi tim development.

---

**Last Updated:** {{ date('Y-m-d') }}  
**Version:** 1.0
