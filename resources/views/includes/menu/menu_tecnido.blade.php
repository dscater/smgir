
<li class="nav-item">
    <a href="{{ route('mantenimientos.index') }}" class="nav-link {{ request()->is('mantenimientos*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Mantenimientos</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('obras.index') }}" class="nav-link {{ request()->is('obras*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Obras</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('gaems.index') }}" class="nav-link {{ request()->is('gaems*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>GAEM</p>
    </a>
</li>
