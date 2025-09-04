<ul class="list-group list-group-flush">
    <li class="list-group-item {{ Request::is('agent/dashboard') ? 'active' : '' }}">
        <a href="{{ route('agent_dashboard') }}">Dashboard</a>
    </li>
    <li class="list-group-item {{ Request::is('agent/payment') ? 'active' : '' }}">
        <a href="{{ route('agent_payment') }}">Make Payment</a>
    </li>
    <li class="list-group-item {{ Request::is('agent/order') ? 'active' : '' }}">
        <a href="{{ route('agent_order') }}">Orders</a>
    </li>
    <li class="list-group-item {{ Request::is('agent/property/create') ? 'active' : '' }}">
        <a href="{{ route('agent_property_create') }}">Add Property</a>
    </li>
    <li class="list-group-item {{ Request::is('agent/property/index') || Request::is('agent/property/edit/*') || Request::is('agent/property/update/*') || Request::is('agent/property/photo-gallery/*') || Request::is('agent/property/video-gallery/*') ? 'active' : '' }}">
        <a href="{{ route('agent_property_index') }}">All Properties</a>
    </li>
    <li class="list-group-item {{ Request::is('agent/message/*') ? 'active' : '' }}">
        <a href="{{ route('agent_message') }}">Message</a>
    </li>
    <li class="list-group-item {{ Request::is('agent/profile') ? 'active' : '' }}">
        <a href="{{ route('agent_profile') }}">Edit Profile</a>
    </li>
    <li class="list-group-item">
        <a href="{{ route('agent_logout') }}">Logout</a>
    </li>
</ul>