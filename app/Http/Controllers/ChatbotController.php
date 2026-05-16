<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message'  => ['required', 'string', 'max:500'],
            'history'  => ['nullable', 'array'],
        ]);

        $apiKey = env('GROQ_API_KEY', '');

        if (empty($apiKey)) {
            return response()->json([
                'reply' => 'Maaf, layanan chatbot sedang tidak tersedia. Silakan hubungi kami melalui halaman Kontak.',
            ]);
        }

        $kategori = KategoriSampah::where('is_active', true)
            ->get()
            ->map(fn($k) => "{$k->ikon} {$k->nama}: Rp " . number_format($k->harga_per_satuan, 0, ',', '.') . "/kg (jual: Rp " . number_format($k->harga_jual, 0, ',', '.') . "/kg) — {$k->deskripsi}")
            ->join("\n");

        $systemPrompt = <<<PROMPT
Kamu adalah asisten virtual SIMBS (Sistem Informasi Manajemen Bank Sampah). Namamu adalah SIMBS Bot.

Tugasmu adalah membantu menjawab pertanyaan seputar:
- Bank Sampah dan cara kerjanya
- Jenis-jenis sampah yang diterima dan harganya
- Cara mendaftar sebagai nasabah
- Cara menyetor sampah
- Cara menarik saldo tabungan
- Informasi umum tentang daur ulang dan lingkungan

Data kategori sampah yang diterima saat ini:
{$kategori}

Cara mendaftar nasabah: Klik tombol "Daftar" di halaman utama, isi nama, nomor HP, NIK (opsional), alamat, dan buat PIN 6 digit. Nomor rekening akan digenerate otomatis.

Cara login nasabah: Gunakan nomor rekening (contoh: BS-001) dan PIN 6 digit.

Cara setor sampah: Datang ke titik pengumpulan atau hubungi operator. Sampah akan ditimbang dan nilai langsung masuk ke saldo tabungan.

Cara tarik saldo: Login ke portal nasabah → menu Saldo → Ajukan Penarikan. Masukkan jumlah dan konfirmasi PIN. Operator akan memproses dalam 1×24 jam kerja.

Aturan menjawab:
- Jawab dalam Bahasa Indonesia yang ramah dan mudah dipahami
- Jawaban singkat dan to the point (maksimal 3-4 kalimat)
- Utamakan pertanyaan seputar bank sampah, tapi boleh menjawab pertanyaan umum lainnya
- Gunakan emoji secukupnya agar terasa ramah
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        if (!empty($request->history)) {
            foreach (array_slice($request->history, -10) as $h) {
                if (!isset($h['role'], $h['content'])) continue;
                if (!in_array($h['role'], ['user', 'assistant'])) continue;
                $messages[] = [
                    'role'    => $h['role'],
                    'content' => substr($h['content'], 0, 500),
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $request->message];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.1-8b-instant', // gratis & cepat
                'messages'    => $messages,
                'max_tokens'  => 300,
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content');
                return response()->json(['reply' => trim($reply)]);
            }

            $errorCode = $response->json('error.code');

            if ($errorCode === 'rate_limit_exceeded') {
                return response()->json(['reply' => 'Maaf, terlalu banyak permintaan. Coba lagi dalam beberapa detik. 🙏']);
            }

            return response()->json(['reply' => 'Maaf, terjadi kesalahan. Silakan coba lagi. 🙏']);

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Maaf, layanan sedang gangguan. Silakan hubungi kami melalui halaman Kontak. 🙏']);
        }
    }
}