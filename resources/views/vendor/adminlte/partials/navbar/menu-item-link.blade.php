<li @isset($item['id']) id="{{ $item['id'] }}" @endisset class="nav-item">

    <a class="nav-link {{ $item['class'] }}" href="{{ $item['href'] }}"
       @isset($item['target']) target="{{ $item['target'] }}" @endisset
       {!! $item['data-compiled'] ?? '' !!}>

        @php
            $notificationUrl = auth()->check() ? route('notifications.index') : null;
            $isNotificationsLink = isset($item['href']) && $notificationUrl && $item['href'] === $notificationUrl;
            $unreadCount = $isNotificationsLink && auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
            $iconColorClass = '';

            if (!empty($item['icon_color'])) {
                $iconColorClass = 'text-' . $item['icon_color'];
            }

            if ($isNotificationsLink && $unreadCount > 0) {
                $iconColorClass = 'text-danger';
            }
        @endphp

        @isset($item['icon'])
            <i class="{{ $item['icon'] }} {{ $iconColorClass }}"></i>
        @endisset

        {{ $item['text'] }}

        @if($isNotificationsLink && $unreadCount > 0)
            <span class="badge badge-danger ml-1">{{ $unreadCount }}</span>
        @endif

        @isset($item['label'])
            <span class="badge badge-{{ $item['label_color'] ?? 'primary' }}">
                {{ $item['label'] }}
            </span>
        @endisset

    </a>

</li>
