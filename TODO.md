# TODO - Perbaikan SIMBS Bot tidak bisa dikirim

- [x] Ubah `resources/views/partials/chat-widget.blade.php`:
  - [x] Ganti kirim request dari `XMLHttpRequest` ke `fetch`
  - [x] Kirim CSRF header hanya jika token tersedia
  - [x] Tambahkan handling error yang menampilkan info HTTP/status + isi error backend
  - [x] Pastikan parsing JSON tidak gagal dan tetap menampilkan `raw` response jika bukan JSON
- [ ] (Setelah edit) instruksikan user cek DevTools Network untuk memastikan `POST /chatbot` sukses dan reply muncul

