<li class="nav-item dropdown user-menu">
    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
        @php($user = Auth::user())
        @php($role = $user && $user->relationLoaded('role') ? $user->role : ($user->role ?? null))
        @if($user && $user->profile_image)
            <img src="{{ asset('storage/' . $user->profile_image) }}" class="user-image img-circle elevation-2" alt="User Image" style="width:32px;height:32px;object-fit:cover;">
        @else
            <span class="user-image img-circle elevation-2 bg-secondary d-inline-block text-white text-center" style="width:32px;height:32px;line-height:32px;font-size:18px;object-fit:cover;">{{ strtoupper(substr($user->name,0,1)) }}</span>
        @endif
        <span class="ml-2">
            {{ $user->name }}
            @if($role)
                <span>({{ $role->display_name ?? ucfirst(str_replace('_',' ', $role->name)) }})</span>
            @else
                <span class="text-danger" title="No active role found">(No Role)</span>
            @endif
        </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <li class="user-header bg-primary">
            @if($user && $user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" class="img-circle elevation-2" alt="User Image">
            @else
                <span class="user-image img-circle elevation-2 bg-secondary d-inline-block text-white text-center" style="width:64px;height:64px;line-height:64px;font-size:32px;object-fit:cover;">{{ strtoupper(substr($user->name,0,1)) }}</span>
            @endif
            <p>
                {{ $user->name }}
                <small>{{ ucfirst(str_replace('_',' ',optional($user->role)->name)) }}</small>
            </p>
        </li>
        <li class="user-footer">
            <a href="{{ route('profile.edit') }}" class="btn btn-default btn-flat">Profile</a>
            <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-right"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();">Sign out</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</li>
