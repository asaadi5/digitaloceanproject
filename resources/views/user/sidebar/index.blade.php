<ul class="list-group list-group-flush">
    <li class="list-group-item {{ Request::is('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="list-group-item {{ Request::is('message/*') ? 'active' : '' }}">
        <a href="{{ route('message') }}">Message</a>
    </li>
    <li class="list-group-item {{ Request::is('wishlist') ? 'active' : '' }}">
        <a href="{{ route('wishlist') }}">Wishlist</a>
    </li>
    <li class="list-group-item {{ Request::is('profile') ? 'active' : '' }}">
        <a href="{{ route('profile') }}">Edit Profile</a>
    </li>
    <li class="list-group-item">
        <a href="{{ route('logout') }}">Logout</a>
    </li>
</ul>