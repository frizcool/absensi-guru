<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\GuruPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    GuruPanelProvider::class,
];
