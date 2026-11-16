<header class="flex items-center justify-between bg-blue-700 px-10 py-4 text-white">
    <a href="{{ route('welcome') }}" class="text-lg font-bold tracking-widest text-white">BLAUG</a>

    <nav class="flex gap-8">
        @foreach($menu as $label => $link)
            <a href="{{$link}}" class="text-sm font-medium capitalize text-blue-100 hover:text-white">{{$label}}</a>
        @endforeach
    </nav>

    <div>
        @auth
            {{ auth()->user()->name }}:
            <a href="{{route('dashboard')}}" class="text-sm font-medium capitalize text-blue-100 hover:text-white">Dashboard</a>
        @else
            <a href="{{route('login')}}" class="text-sm font-medium capitalize text-blue-100 hover:text-white">Login</a>
             -
            <a href="{{route('register')}}" class="text-sm font-medium capitalize text-blue-100 hover:text-white">Register</a>

        @endauth
    </div>

</header>
