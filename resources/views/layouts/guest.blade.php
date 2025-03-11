<!DOCTYPE html>
<html data-theme="" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('tanma.png') }}" type="image/png"/>
    <title>Tanma</title>

    <!-- Fonts -->

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="">
    <div class="navbar bg-base-100">
        <div class="navbar-start">
            <div class="dropdown lg:hidden">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a class="text-md" href="{{ route('tanma.merge') }}">PdfMerge</a></li>
                    <li><a class="text-md" href="{{route('tanma.selected')}}" >PdfSelected</a></li>
                    <li><a class="text-md" href="{{route('tanmasplitbill')}}" >Split Bill</a></li>
                </ul>
            </div>
            <a class="text-xl text-red-600 btn btn-ghost" href="{{ route('dashboard') }}">
                Tanma
            </a>
        </div>

        <div class="hidden navbar-center lg:flex">
            <ul class="px-1 menu menu-horizontal">

                <li><a class="text-md" href="{{ route('tanma.merge') }}">Pdf Merge</a></li>
                <li><a class="text-md" href="{{route('tanma.selected')}}" >Pdf Selected</a></li>
                <li><a class="text-md" href="{{route('tanmasplitbill')}}" >Split Bill</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <ul>
                    <label for="theme-selector" class="text-sm font-medium leading-6">Tema:</label>
                    <select id="theme-selector" class="max-w-xs text-sm select select-bordered select-sm">
                        <option value="light">light</option>
                        <option value="cupcake">cupcake</option>
                        <option value="bumblebee">bumblebee</option>
                        <option value="emerald">emerald</option>
                        <option value="corporate">corporate</option>
                        <option value="retro">retro</option>
                        <option value="cyberpunk">cyberpunk</option>
                        <option value="valentine">valentine</option>
                        <option value="halloween">halloween</option>
                        <option value="garden">garden</option>
                        <option value="forest">forest</option>
                        <option value="lofi">lofi</option>
                        <option value="pastel">pastel</option>
                        <option value="fantasy">fantasy</option>
                        <option value="wireframe">wireframe</option>
                        <option value="luxury">luxury</option>
                        <option value="cmyk">cmyk</option>
                        <option value="autumn">autumn</option>
                        <option value="business">business</option>
                        <option value="lemonade">lemonade</option>
                    </select>
                </div>
            </ul>
                <ul class="pl-2">
                    <a href="{{ url('login') }}" class="text-xl">
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </a>
                </ul>
        </div>
    </div>

    <main data-theme="" class="rounded-md border-accent">
        {{ $slot }}
    </main>

</body>
<script>
    const themeSelector = document.getElementById('theme-selector');

// Load theme on page load
document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') ||
    'autumn'); // 'default' is the fallback

themeSelector.addEventListener('change', function() {
    const selectedTheme = this.value;
    document.documentElement.setAttribute('data-theme', selectedTheme);
    localStorage.setItem('theme', selectedTheme);
});
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

// Daftar kutipan yang berbeda dengan <br> untuk pemisahan
const quotes = [
   "Jangan pernah ragu untuk bermimpi besar.",
    "Setiap hari adalah kesempatan baru untuk jadi lebih baik.",
    "Kalo bukan kita yang mulai, siapa lagi?.",
    "Hidup ini tentang perjalanan, bukan hanya tujuan.",
    "Jangan biarkan orang lain mendefinisikan batasanmu.",
    "Kegagalan itu hanya batu loncatan menuju kesuksesan.",
    "Bersyukur itu kunci, meski dalam keadaan sulit.",
    "Jadilah orang yang menginspirasi, bukan yang mengeluh.",
    "Kreativitas itu kekuatan, jangan takut untuk berinovasi.",
    "Temukan passionmu dan kejar tanpa henti.",
    "Sahabat sejati itu yang selalu mendukungmu.",
    "Jangan lupa untuk menikmati prosesnya.",
    "Setiap detik berharga, jangan sia-siakan.",
    "Berani keluar dari zona nyaman, itu langkah awal.",
    "Kita semua punya cerita, tulislah yang terbaik.",
    "Jangan cuma jadi penonton, jadi pemain yang bikin cerita!",
    "Hidup itu kayak kopi, kadang pahit, kadang manis, tapi tetap nikmat!",
    "Jangan takut gagal, karena dari situ kita belajar untuk jadi lebih baik.",
    "Setiap langkah kecil itu berarti, yang penting terus jalan!",
    "Kita semua punya potensi, tinggal gimana kita nge-gali dan maksimalkan!",
    "Jadilah versi terbaik dari dirimu, bukan versi orang lain!",
    "Biar lambat asal selamat, yang penting konsisten!",
    "Sukses itu bukan tentang seberapa cepat, tapi seberapa kuat kita bertahan!",
    "Setiap langkah yang kita ambil adalah bagian dari perjalanan.",
    "Jangan takut untuk bermimpi, karena mimpi adalah awal dari segalanya.",
    "Hidup ini penuh warna, nikmati setiap nuansa yang ada.",
    "Kita semua punya cerita, tuliskan kisah terbaikmu.",
    "Ketika jatuh, bangkitlah lagi, karena setiap kegagalan adalah pelajaran.",
    "Jadilah dirimu sendiri, karena keunikanmu adalah kekuatanmu.",
    "Cinta dan harapan adalah bahan bakar untuk terus melangkah.",
    "Jangan biarkan keraguan menghentikan langkahmu.",
    "Setiap detik berharga, jangan sia-siakan dengan hal yang tidak berarti.",
    "Kita bisa melewati badai, asalkan kita tetap bersatu.",
    "Hidup ini seperti lagu, kadang ada nada tinggi, kadang nada rendah.",
    "Jangan lupa untuk bersyukur, meski dalam keadaan sulit.",
    "Kita adalah penulis cerita hidup kita sendiri.",
    "Jangan pernah berhenti berusaha, karena usaha tidak akan mengkhianati hasil.",
    "Setiap hari adalah kesempatan baru untuk menjadi lebih baik.",
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
document.getElementById("quote").innerHTML = getRandomQuote(greeting);
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let keysPressed = {};

document.addEventListener('keydown', function(event) {
    keysPressed[event.key] = true;

    // Cek apakah semua tombol yang diperlukan ditekan
    if (keysPressed['q'] && keysPressed['w'] && keysPressed['e']) {
        Swal.fire({
            icon: 'info',
            title: 'Selamat datang di Tanma App!',
            text: 'by laradelfa',
            timer: 3000,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    }
});

document.addEventListener('keyup', function(event) {
    // Hapus tombol yang dilepas dari objek keysPressed
    delete keysPressed[event.key];
});
</script>

</html>
