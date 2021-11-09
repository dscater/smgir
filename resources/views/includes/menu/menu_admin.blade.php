<li class="nav-item">
    <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>Usuarios</p>
    </a>
</li>

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


<li class="nav-item">
    <a href="{{ route('bases.index') }}" class="nav-link {{ request()->is('bases*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list"></i>
        <p>Bases</p>
    </a>
</li>

<li class="nav-item @if (request()->is('macro_distritos*') || request()->is('distritos*') || request()->is('zonas*')) menu-is-opening menu-open active @endif">
    <a href="#" class="nav-link">
        <i class="nav-icon fa fa-list"></i>
        <p>Macro Distritos <i class="fas fa-angle-left right"></i></p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('macro_distritos.index') }}"
                class="nav-link {{ request()->is('macro_distritos*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle"></i>
                <p>Macro Distritos</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('distritos.index') }}"
                class="nav-link {{ request()->is('distritos*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle"></i>
                <p>Distritos</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('zonas.index') }}"
                class="nav-link {{ request()->is('zonas*') ? 'active' : '' }}">
                <i class="nav-icon far fa-circle"></i>
                <p>Zonas</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>Reportes</p>
    </a>
</li>
