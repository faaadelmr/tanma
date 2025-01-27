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
    
    <main data-theme="" class="border-accent rounded-md">
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
    "'Kita semua memiliki kekuatan untuk menciptakan keajaiban, selama kita percaya pada diri kita sendiri dan tidak takut untuk bermimpi lebih besar.' <br> — Frozen II, Elsa —",

    "'Aku tidak ingin menjadi raja, aku hanya ingin menjadi baik dan melakukan yang benar untuk orang-orang di sekitarku, karena itu yang sebenarnya penting dalam hidup.' <br> — The Lion King, Simba —",

    "'Satu-satunya batasan adalah batasan yang kita buat untuk diri kita sendiri; jika kita berani mengambil langkah pertama, tidak ada yang tidak mungkin.' <br> — Big Hero 6, Hiro —",

    "'Cinta adalah hal terkuat di dunia; ia bisa mengalahkan segala rintangan dan membawa kita pada perjalanan yang tak terduga.' <br> — Beauty and the Beast, Belle —",

    "'Jangan takut untuk terbang dan menjelajahi dunia; setiap mimpi dimulai dengan satu langkah berani, dan kamu tidak pernah tahu apa yang bisa kamu capai.' <br> — Peter Pan, Peter —",

    "'Hidup ini seperti kotak cokelat; kamu tidak pernah tahu apa yang akan kamu dapatkan, jadi nikmatilah setiap rasa yang datang kepadamu.' <br> — Forrest Gump, Forrest —",

    "'Tidak ada yang tidak mungkin jika kamu percaya pada dirimu sendiri dan berusaha keras untuk mencapai apa yang kamu inginkan.' <br> — Mulan, Mulan —",

    "'Kamu tidak perlu menjadi sempurna; cukup jadilah dirimu sendiri dan terima segala sesuatu yang membuatmu unik.' <br> — Zootopia, Judy Hopps —",

    "'Jadilah cahaya dalam kegelapan; dengan kebaikanmu, kamu bisa mengubah dunia dan memberi inspirasi kepada orang lain.' <br> — Tangled, Rapunzel —",

    "'Setiap keajaiban dimulai dengan keinginan yang sederhana; jadi jangan pernah meremehkan kekuatan dari harapan dan impianmu.' <br> — Cinderella, Cinderella —",

    "'Hidup adalah petualangan yang harus dijelajahi; jangan takut untuk menghadapi tantangan dan menemukan dirimu dalam prosesnya.' <br> — Moana, Moana —",

    "'Apa pun yang terjadi, kita selalu bersama; persahabatan kita adalah kekuatan yang tidak bisa dipatahkan oleh apapun.' <br> — Toy Story, Woody —",

    "'Kamu adalah pahlawan dalam kisahmu sendiri; jangan pernah meragukan kekuatanmu untuk membuat perbedaan di dunia ini.' <br> — How to Train Your Dragon, Hiccup —",

    "'Jangan biarkan siapapun memberitahumu bahwa kamu tidak bisa melakukan sesuatu; jika kamu memiliki mimpi, kejar hingga akhir.' <br> — Good Will Hunting, Sean —",

    "'Setiap langkah kecil membawa kita lebih dekat ke tujuan besar; jadi teruslah berjalan, meskipun jalannya terjal.' <br> — Ratatouille, Remy —",

    "'Hargai setiap momen yang kamu miliki; mereka adalah kenangan yang akan menuntunmu di masa depan.' <br> — Up, Carl Fredricksen —",

    "'Kita semua berbeda, dan itu yang membuat kita istimewa; keunikan kita adalah kekuatan, bukan kelemahan.' <br> — The Incredibles, Elastigirl —",

    "'Jangan pernah berhenti bermimpi; setiap impian bisa menjadi kenyataan jika kamu berani berjuang untuknya.' <br> — The Princess and the Frog, Tiana —",

    "'Kesuksesan bukan tentang berapa banyak yang kamu miliki, tetapi tentang seberapa banyak yang kamu berikan kepada orang lain.' <br> — Kung Fu Panda, Po —",

    "'Hidup ini penuh dengan keajaiban, jadi nikmati setiap momennya dan lihatlah sekelilingmu dengan mata yang penuh rasa syukur.' <br> — Finding Nemo, Dory —",

    "'Keluarga adalah tempat di mana cinta tidak pernah berakhir; mereka adalah orang-orang yang selalu ada untukmu, tidak peduli apa pun yang terjadi.' <br> — The Croods, Grug —",

    "'Kamu memiliki kekuatan untuk mengubah dunia, jadi gunakan itu dengan bijak dan penuh kasih sayang.' <br> — Wreck-It Ralph, Ralph —",

    "'Keberanian adalah kunci untuk mencapai impian; hadapi ketakutanmu dan terus berjuang meskipun jalannya sulit.' <br> — The Incredibles, Frozone —",

    "'Jangan hanya bermimpi tentang masa depan; buatlah itu menjadi kenyataan dengan tindakanmu hari ini!' <br> — Up, Ellie —",

    "'Setiap orang memiliki potensi untuk menjadi pahlawan, tidak peduli seberapa kecil tindakanmu; setiap kebaikan memiliki dampak.' <br> — The Lego Movie, Emmet —",

    "'Kamu adalah penulis cerita hidupmu sendiri; tulis kisah yang ingin kamu ceritakan kepada dunia dengan semangat dan keberanian.' <br> — Inside Out, Joy —",

    "'Kita semua punya kekuatan untuk membuat perubahan, jadi mulailah hari ini dan jadilah inspirasi bagi orang-orang di sekitarmu.' <br> — Zootopia, Nick Wilde —",

    "'Jadilah yang terbaik dari dirimu; dunia membutuhkan cahaya dari setiap individu untuk membuatnya lebih baik.' <br> — Tangled, Flynn Rider —",

    "'Hidup adalah perjalanan yang penuh warna; nikmati setiap detiknya dan jangan sia-siakan kesempatan untuk bersinar.' <br> — Coco, Miguel —",

    "'Setiap mimpi dimulai dengan seorang pemimpi; jadi beranilah untuk bermimpi dan berusaha keras untuk mewujudkannya.' <br> — The Little Mermaid, Ariel —",

    "'Bersyukurlah atas setiap pengalaman, karena kita belajar dari semuanya dan tumbuh menjadi pribadi yang lebih baik.' <br> — Monsters, Inc., Sulley —",

    "'Cinta yang tulus bisa mengatasi segala rintangan; itu adalah kekuatan terbesar yang kita miliki dalam hidup.' <br> — Aladdin, Aladdin —",

    "'Kamu adalah bagian dari sesuatu yang lebih besar; jangan meremehkan dirimu dan potensi yang ada dalam dirimu.' <br> — The Lion King, Mufasa —",

    "'Keberanian tidak selalu berteriak; kadangkala ia adalah suara kecil yang mengatakan, 'Aku akan mencoba lagi.' <br> — Finding Dory, Dory —",

    "'Bersama kita bisa mengatasi apa pun; kita adalah tim yang tak terpisahkan, dan kekuatan kita terletak pada persahabatan.' <br> — Frozen, Anna —",

    "'Hidup ini terlalu singkat untuk tidak mengejar impianmu; jadi ambil langkah pertama dan mulai perjalananmu sekarang.' <br> — Ratatouille, Linguini —",

    "'Setiap tindakan kecil bisa memberikan dampak besar pada dunia; jadi lakukanlah dengan hati yang penuh kasih.' <br> — Kung Fu Panda, Master Oogway —",

    "'Jadilah diri sendiri, karena keunikanmu adalah kelebihanmu yang membuatmu berharga di mata dunia.' <br> — Zootopia, Judy Hopps —",

    "'Mimpi tidak akan menjadi kenyataan jika kamu tidak berusaha; jadi berjuanglah untuk apa yang kamu inginkan.' <br> — Mulan, Mushu —",

    "'Keluarga bukan hanya tentang darah; itu tentang siapa yang ada di sampingmu dalam setiap langkah perjalanan.' <br> — The Croods, Eep —",

    "'Selalu ada harapan, bahkan dalam kegelapan yang paling dalam; jangan pernah kehilangan keyakinan dalam dirimu sendiri.' <br> — Big Hero 6, Baymax —",

    "'Hargai setiap momen bersama orang-orang yang kamu cintai; itu adalah kenangan yang akan selalu terpatri dalam hati.' <br> — Up, Carl Fredricksen —",

    "'Jadilah perubahan yang ingin kamu lihat di dunia; setiap tindakan baik yang kamu lakukan bisa menginspirasi orang lain.' <br> — Mulan, Mulan —",

    "'Cinta adalah kekuatan yang bisa mengubah segala sesuatu; jangan ragu untuk menunjukkan kasih sayangmu kepada orang lain.' <br> — Beauty and the Beast, Beast —",

    "'Setiap perjalanan dimulai dengan satu langkah; jadi ambillah langkah pertama itu dan nikmati prosesnya.' <br> — Moana, Moana —",

    "'Kita bisa membuat keajaiban jika kita percaya pada diri kita sendiri dan berani mengambil risiko.' <br> — Tangled, Rapunzel —",

    "'Hidup ini adalah sebuah perjalanan, nikmatilah setiap langkah yang kamu ambil dan pelajari dari setiap pengalaman.' <br> — The Incredibles, Dash —",

    "'Kamu memiliki kekuatan untuk membuat dunia menjadi tempat yang lebih baik; jadi gunakan kekuatan itu untuk kebaikan.' <br> — Wreck-It Ralph, Vanellope —",

    "'Hidup adalah petualangan yang indah; jangan sia-siakan kesempatanmu untuk menjelajah dan menemukan keajaiban.' <br> — Coco, Héctor —",

    "'Jangan pernah berhenti berjuang untuk apa yang kamu yakini; keberanianmu akan membawamu jauh.' <br> — Ratatouille, Remy —",

    "'Kita semua memiliki kekuatan untuk menciptakan perubahan dan menginspirasi satu sama lain; jadi mulailah sekarang.' <br> — Zootopia, Chief Bogo —",

    "'Hidup ini penuh dengan tantangan, tetapi kamu lebih kuat dari yang kamu kira; percayalah pada dirimu sendiri.' <br> — Finding Dory, Marlin —",

    "'Cinta dapat mengatasi segala rintangan; itu adalah kekuatan yang tidak bisa dipatahkan oleh apapun.' <br> — Aladdin, Jasmine —",

    "'Kamu adalah pahlawan, tidak peduli seberapa kecil tindakanmu; setiap kebaikan memiliki dampak yang besar.' <br> — How to Train Your Dragon, Toothless —",

    "'Setiap detik adalah kesempatan baru untuk melakukan hal yang baik; jadi manfaatkanlah dengan bijak.' <br> — Inside Out, Sadness —",

    "'Keberanian bukanlah tidak merasa takut, tetapi mampu bertindak meskipun dalam ketakutan; itu adalah kekuatan sejati.' <br> — Big Hero 6, Hiro —",

    "'Keluarga adalah tempat di mana kehidupan dimulai dan cinta tidak pernah berakhir; hargai setiap momen bersamanya.' <br> — The Incredibles, Violet —",
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

</html>
