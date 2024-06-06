<?php

namespace Seomidia\AssinaturaDigital;

use Illuminate\Support\ServiceProvider;

class AssinaturaDigitalServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registre quaisquer serviços específicos do pacote.
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'assinatura-digital');
    }
}
