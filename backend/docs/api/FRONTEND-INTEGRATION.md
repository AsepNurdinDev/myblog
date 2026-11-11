# MyBlog API: frontend integration notes

Dokumen ini mencatat kontrak dan konfigurasi yang perlu diperhatikan saat Next.js mengonsumsi Laravel API.

## Base URL

- Development: `http://127.0.0.1:8000/api/v1`
- Production: `https://api.asepblog.online/api/v1`

Atur `APP_URL` pada backend ke origin API yang benar. URL media dari disk `public` mengikuti konfigurasi `filesystems.disks.public.url`, yang secara default dibangun dari `APP_URL`.

## Media

Field media (`cover_image`, `thumbnail`, `avatar`, dan URL image block) memiliki kontrak:

- `null` jika path kosong atau file tidak ditemukan di disk `public`.
- URL absolut jika file tersedia.
- URL absolut yang sudah tersimpan sebagai URL eksternal tetap dikembalikan apa adanya.

Frontend harus menangani nilai `null` dengan placeholder/fallback image. Jangan mengasumsikan semua record lama memiliki file media yang masih tersedia.

## Komentar pada detail artikel

`GET /posts/{slug}` mengembalikan maksimal 50 komentar berstatus approved terbaru pada field `comments`. Field `comments_count` berisi total komentar approved, termasuk komentar di luar 50 item preview. Email pengomentar tidak pernah dikirim ke publik.

Jika frontend nantinya membutuhkan seluruh komentar, buat endpoint komentar terpisah dengan pagination; jangan menaikkan batas preview tanpa batas.

## HTML dari konten

Beberapa field memang berisi HTML yang dibuat melalui Filament, misalnya blok teks artikel, konten project, dan bio profil. Laravel mengirimkan HTML tersebut sebagai konten, bukan sebagai HTML yang sudah disanitasi untuk browser.

Sebelum merender HTML dari API di browser, sanitasi menggunakan allowlist tag/atribut yang diperlukan (contoh: DOMPurify dengan konfigurasi allowlist). Jangan langsung memasukkan konten API ke `dangerouslySetInnerHTML` tanpa sanitasi. Hindari mengizinkan script, event-handler seperti `onerror`, iframe yang tidak dibutuhkan, dan URL `javascript:`.

## CORS

Konfigurasi CORS saat ini mengizinkan origin production `https://asepblog.online` dan development `http://localhost:3000`. Jika domain frontend berubah, perbarui `config/cors.php` secara eksplisit. `supports_credentials` tetap `false` karena endpoint publik tidak menggunakan cookie/session authentication.

## Reverse proxy

Wildcard trusted proxy sudah dihilangkan. Secara default, Laravel tidak mempercayai forwarded headers dari proxy mana pun.

Jika backend memang menerima request dari reverse proxy dan perlu membaca IP/protocol asli, isi `TRUSTED_PROXIES` di environment server dengan alamat IP atau CIDR proxy yang benar-benar menjadi peer langsung PHP, dipisahkan koma. Contoh untuk proses proxy lokal:

```dotenv
TRUSTED_PROXIES=127.0.0.1,::1
```

Untuk Docker, gunakan IP atau subnet private dari container/network proxy yang benar-benar terhubung ke backend. Jangan menyalin daftar IP Cloudflare edge secara membabi buta untuk Cloudflare Tunnel; tentukan alamat peer yang terlihat oleh PHP. Jangan mengatur `TRUSTED_PROXIES=*` atau mempercayai seluruh internet.

Setelah mengubah environment pada deployment dengan config cache, jalankan:

```bash
php artisan config:clear
php artisan config:cache
```

Pastikan origin backend tidak dapat diakses langsung dari internet jika arsitektur mengandalkan proxy sebagai satu-satunya pintu masuk.

## Verifikasi lokal

```bash
php artisan test --compact tests/Feature/Api
php artisan route:list --path=api -v
```

Jalankan juga smoke test endpoint dari browser/Next.js setelah backend aktif. Jangan pernah membagikan isi `.env` atau secret ketika meminta bantuan debugging.
