<div class="navbar bg-base-100">
    <div class="flex-1">
        <a class="btn btn-ghost text-xl" href="{{ route('dashboard') }}">
            Tanma
        </a>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1">
              <li><a>User Management</a></li>
              <li><a href="">Report Harian</a></li>
              <li>
                <details>
                  <summary>Parent</summary>
                  <ul class="bg-base-100 rounded-t-none p-2">
                    <li><a>Link 1</a></li>
                    <li><a>Link 2</a></li>
                  </ul>
                </details>
              </li>
            </ul>
          </div>
    </div>
    <div class="flex-none gap-2">
      <div class="dropdown dropdown-end">
        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
          <div class="w-10 rounded-full">
            <img
              alt="Null Foto Profil"
              src="" />
          </div>
        </div>
        <ul
          tabindex="0"
          class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">
          <li>
            <a href={{route('profile.edit')}} class="justify-between">
              Profil
              <span class="badge">New</span>
            </a>
          </li>
          //tema
          <li>
            <details> <summary>Tema</summary>
                    <select id="theme-selector" class="w-full max-w-xs select select-bordered">
                      <option value="light">light</option>
                      <option value="dark">dark</option>
                      <option value="cupcake">cupcake</option>
                      <option value="bumblebee">bumblebee</option>
                      <option value="emerald">emerald</option>
                      <option value="corporate">corporate</option>
                      <option value="synthwave">synthwave</option>
                      <option value="retro">retro</option>
                      <option value="cyberpunk">cyberpunk</option>
                      <option value="valentine">valentine</option>
                      <option value="halloween">halloween</option>
                      <option value="garden">garden</option>
                      <option value="forest">forest</option>
                      <option value="aqua">aqua</option>
                      <option value="lofi">lofi</option>
                      <option value="pastel">pastel</option>
                      <option value="fantasy">fantasy</option>
                      <option value="wireframe">wireframe</option>
                      <option value="black">black</option>
                      <option value="luxury">luxury</option>
                      <option value="dracula">dracula</option>
                      <option value="cmyk">cmyk</option>
                      <option value="autumn">autumn</option>
                      <option value="business">business</option>
                      <option value="acid">acid</option>
                      <option value="lemonade">lemonade</option>
                    </select>
            </details>
          </li>
          //Logout
          <li><form method="POST" action="{{ route('logout') }}">
            @csrf
            <button href={{route('logout')}} class="text-red-500 hover:text-red-700">
                Keluar
            </button>
        </form></li>
        </ul>
      </div>
    </div>
  </div>
