<?php

return [
    App\Providers\AppServiceProvider::class,
    ...app()->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)
        ? [App\Providers\TelescopeServiceProvider::class]
        : [],
];
