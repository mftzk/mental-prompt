# Mental Prompt

Mental Prompt adalah aplikasi web yang dirancang untuk membantu developer dan tim AI melacak, menganalisis, dan meningkatkan kualitas *prompt* yang digunakan dalam interaksi dengan model bahasa. Dengan sistem ini, Anda dapat memberikan skor kuantitatif terhadap *prompt* berdasarkan efektivitas dan kejelasannya, langsung dari editor kode Anda.

Aplikasi ini dibangun menggunakan **Laravel** sebagai backend API, **MySQL** untuk penyimpanan data, dan dikemas dalam **Docker** untuk kemudahan setup dan deployment.

## Alur Kerja

1.  **Jalankan Server**: Anda menjalankan *backend* Laravel dan *database* menggunakan Docker.
2.  **Buat Client**: Melalui *dashboard* web, Anda membuat sebuah "client" yang akan memberikan Anda sebuah `UUID` unik.
3.  **Pasang MCP Server**: Anda menjalankan server MCP (Model Context Protocol) Python di lokal, yang bertindak sebagai jembatan antara editor Anda dan *backend*.
4.  **Konfigurasi Editor**: Anda menambahkan *rule* di editor (misalnya, Cursor) untuk memanggil *tool* dari MCP server.
5.  **Kirim Skor**: Setiap kali Anda ingin menilai sebuah *prompt*, Anda menjalankan *rule* tersebut, dan skor akan dikirim ke *backend* untuk dianalisis.

---

## 1. Menjalankan API Server (Backend)

Backend aplikasi ini berjalan di dalam kontainer Docker, yang memudahkan proses instalasi.

1.  **Clone Repository**
    ```bash
    git clone https://github.com/ajie-pras/mental-prompt.git
    cd mental-prompt
    ```

2.  **Konfigurasi Environment**
    Salin file `.env.example` untuk membuat file konfigurasi lokal Anda.
    ```bash
    cp .env.example .env
    ```

3.  **Jalankan Services**
    Perintah ini akan membangun *image* dan menjalankan kontainer aplikasi serta *database* di *background*.
    ```bash
    docker-compose up --build -d
    ```

4.  **Jalankan Migrasi Database**
    Setelah kontainer berjalan, siapkan skema *database* dengan menjalankan perintah migrasi.
    ```bash
    docker-compose exec app php artisan migrate
    ```

5.  **Akses Aplikasi**
    Sekarang, aplikasi Anda sudah berjalan dan dapat diakses di [http://localhost](http://localhost).

---

## 2. Membuat Client UUID

Untuk mengirim data ke API, setiap *client* (editor Anda) harus memiliki UUID yang terdaftar.

1.  Buka *dashboard* aplikasi di *browser* Anda: [http://localhost](http://localhost).
2.  Gunakan antarmuka yang tersedia untuk membuat entri "Client" baru.
3.  Setelah dibuat, **salin (copy)** UUID yang dihasilkan. UUID ini akan digunakan pada langkah berikutnya.

---

## 3. Memasang MCP Server

MCP Server adalah skrip Python yang menerima perintah dari editor Anda dan meneruskannya ke API backend.

1.  **Masuk ke Direktori**
    ```bash
    cd mcp-prompt-health
    ```

2.  **Buat Virtual Environment** (Opsional tapi direkomendasikan)
    Ini akan mengisolasi *dependency* Python untuk proyek ini.
    ```bash
    # Untuk macOS/Linux
    python3 -m venv .venv
    source .venv/bin/activate

    # Untuk Windows
    python -m venv .venv
    .venv\Scripts\activate
    ```

3.  **Install Dependencies**
    Install `fastmcp` dan `httpx` yang dibutuhkan oleh server.
    ```bash
    pip install -r requirements.txt
    ```

4.  **Konfigurasi Environment Variable**
    Atur `CLIENT_UUID` yang sudah Anda salin dari *dashboard*.
    ```bash
    # Untuk macOS/Linux
    export CLIENT_UUID="your-uuid-from-dashboard"

    # Untuk Windows (Command Prompt)
    set CLIENT_UUID="your-uuid-from-dashboard"
    ```
    *Pastikan untuk mengganti `your-uuid-from-dashboard` dengan UUID yang sebenarnya.*

5.  **Jalankan Server**
    ```bash
    python prompt_quality_server.py
    ```
    Server sekarang aktif dan siap menerima perintah dari editor Anda.

---

## 4. Contoh Konfigurasi Rule (Cursor)

Langkah terakhir adalah memberitahu editor Anda cara berkomunikasi dengan MCP Server. Berikut adalah contoh konfigurasi untuk editor Cursor menggunakan file `~/.cursor/mcp.json`.

```json
{
  "mcp_server": {
    "command": [
      "python",
      // Ganti dengan path absolut ke skrip di komputer Anda
      "/path/to/your/project/mental-prompt/mcp-prompt-health/prompt_quality_server.py"
    ],
    "env": {
      // Tempel UUID Anda di sini
      "CLIENT_UUID": "your-uuid-from-dashboard",
      // URL backend jika berbeda dari default
      "PROMPT_QUALITY_API": "http://localhost"
    }
  },
  "rules": [
    {
      "scope": "always",
      "action": {
        "type": "mcp",
        "tool": "submit_prompt_quality",
        "args": {
          "project": "{{repo.name}}",
          "efektivitas": "{{eval.eff}}",
          "membingungkan": "{{eval.confusing}}",
          "ambiguous": "{{eval.ambiguous}}",
          "comments": "{{eval.comment}}"
        }
      }
    }
  ]
}
```

### Penjelasan Konfigurasi:

-   **`mcp_server.command`**: Path absolut ke skrip `prompt_quality_server.py`. Pastikan untuk menyesuaikannya.
-   **`mcp_server.env`**: Environment variable yang akan digunakan saat menjalankan server. `CLIENT_UUID` wajib diisi.
-   **`rules`**: Mendefinisikan kapan dan bagaimana *tool* `submit_prompt_quality` akan dipanggil. Dalam contoh ini, *rule* akan selalu aktif (`"scope": "always"`) dan akan mengambil nilai dari evaluasi *prompt* (`eval.*`) untuk dikirim sebagai argumen.
