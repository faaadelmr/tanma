<!DOCTYPE html>
<html data-theme="retro" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('tanma.png') }}" type="image/png"/>
    <title>Tanma</title>

    <!-- Fonts -->

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>
<script>
    // Auto dismiss error alert after 5 seconds
    setTimeout(function() {
        const alert = document.querySelector('.bg-red-50');
        if (alert) {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        }
    }, 5000);
    // Fungsi untuk menentukan salam berdasarkan waktu
    function getGreeting() {
        const hour = new Date().getHours();
        if (hour < 12) {
            return "Selamat Pagi!";
        } else if (hour < 15) {
            return "Selamat Siang!";
        } else if (hour < 18) {
            return "Selamat Sore!";
        } else {
            return "Selamat Malam!";
        }
    }

    // Daftar kutipan yang berbeda
    const quotes = [
        "Hidup adalah anugerah, nikmatilah setiap detiknya.",
        "Jangan pernah berhenti belajar, karena hidup adalah proses belajar.",
        "Hidup adalah perjalanan, jadilah pemandu yang baik bagi diri sendiri.",
        "Kebahagiaan bukanlah tujuan, tetapi cara hidup.",
        "Jangan pernah berhenti berharap, karena harapan adalah sumber kekuatan.",
        "Jadilah perubahan yang ingin kamu lihat di dunia.",
        "Setiap hari adalah kesempatan baru untuk menjadi lebih baik.",
        "Kerja keras seperti diet, semua orang bilang itu baik, tapi sedikit yang bisa menahannya.",
        "Sukses adalah ketika kamu sudah lupa berapa kali kamu gagal dan terus mencoba lagi.",
        "Jika kamu menemukan pekerjaan yang kamu cintai, kamu tidak akan pernah bekerja lagi... tapi kamu mungkin akan terus membawa kerja ke rumah.",
        "Deadline adalah mitos yang diciptakan oleh orang-orang yang tidak punya cukup waktu untuk menunda-nunda.",
        "Kerja adalah tempat di mana kamu pergi untuk tidur dengan mata terbuka.",
        "Kita semua di sini karena seseorang suatu kali berpikir bahwa kita bisa lebih baik daripada duduk di rumah.",
        "Di dalam setiap pekerjaan ada keajaiban, tapi kadang-kadang keajaiban itu hanya sebuah mukjizat jika kita bisa pulang tepat waktu.",
        "Kerja keras memang penting, tapi jangan lupa untuk kerja cerdas agar bisa pulang lebih cepat.",
        "Sukses bukan tentang seberapa banyak kamu bekerja, tapi seberapa pintar kamu menghindari pekerjaan yang tidak perlu.",
        "Kerja itu seperti kue, semua orang ingin mencicipi hasilnya, tapi tidak semua orang ingin menyusun adonannya.",
        "Kerja keras itu penting, tapi jangan lupa untuk bersenang-senang. Setelah semua, siapa yang mau bekerja tanpa sedikit tawa?",
        "Jika pekerjaanmu terasa berat, ingatlah bahwa setiap masalah adalah kesempatan untuk menemukan solusi yang lebih baik... atau setidaknya untuk mencari kopi.",
        "Bekerja itu seperti berlari maraton; terkadang perlu berhenti sejenak untuk menghirup udara segar dan menikmati pemandangan.",
        "Jangan biarkan pekerjaan membuatmu stres. Ingat, bahkan printer pun kadang mengalami 'kertas macet'.",
        "Setiap hari adalah kesempatan baru untuk bersyukur, terutama jika ada kopi gratis di kantor.",
        "Jika hidup memberimu jeruk, buatlah lemonade. Atau lebih baik lagi, buatlah es kopi dan nikmati sambil bekerja!",
        "Kerja itu seperti diet; semua orang bilang itu baik, tapi sedikit yang bisa menahannya. Jadi, nikmati saja!",
        "Ketika pekerjaan terasa berat, ingatlah bahwa setiap proyek memiliki akhir. Jadi, jangan biarkan mereka mengendalikan pikiranmu terlalu lama.",
        "Bekerja dengan cerdas bukan berarti bekerja tanpa henti. Kadang-kadang, itu berarti tahu kapan harus mengambil istirahat.",
        "Jadilah seperti kucing: fleksibel, mandiri, dan terkadang suka bersembunyi di bawah meja saat pekerjaan menumpuk.",
        "Pekerjaan adalah perjalanan. Pastikan kamu membawa bekal yang cukup, terutama kopi.",
        "Jangan khawatir jika rencanamu tidak berjalan sesuai rencana. Itu berarti kamu memiliki waktu untuk membuat rencana yang lebih baik.",
        "Kesuksesan adalah tentang bagaimana kamu menangani kegagalan. Dan tentu saja, seberapa cepat kamu bisa menemukan kembali kantong cemilan rahasia di laci.",
        "Bekerja keras memang penting, tetapi jangan lupakan untuk menikmati setiap detiknya. Bahkan, mungkin sambil menikmati secangkir kopi.",
        "Jangan biarkan pekerjaan membuatmu stres. Ingat, bahkan printer pun kadang mengalami 'kertas macet'.",
        "Jadilah seperti kucing: fleksibel, mandiri, dan terkadang suka bersembunyi di bawah meja saat pekerjaan menumpuk.",
    ];

    // Memilih kutipan secara acak, dengan pengecualian untuk malam
    function getRandomQuote(greeting) {
        if (greeting === "Selamat Malam!") {
            return "Sudah malam, kok belum pulang?";
        }
        return quotes[Math.floor(Math.random() * quotes.length)];
    }

    // Menampilkan salam dan kutipan
    const greeting = getGreeting();
    document.getElementById("greeting").innerText = greeting;
    document.getElementById("quote").innerText = getRandomQuote(greeting);
</script>

</html>
