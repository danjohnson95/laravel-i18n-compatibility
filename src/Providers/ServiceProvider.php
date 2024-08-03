<?php

namespace DanJohnson95\LaravelI18nCompatibility\Providers;

use DanJohnson95\LaravelI18nCompatibility\Translator;
use Illuminate\Translation\TranslationServiceProvider as BaseTranslationServiceProvider;

/**
 * This service provider is used to override the default Laravel translation service provider.
 * We need to do this because we want to use our own syntax for interpolating variables:
 * Instead of using Laravel's ":attribute" syntax, we want to use the i18n "{{ attribute }}" syntax.
 * This means we can share the same syntax between our frontend and backend.
 */
class ServiceProvider extends BaseTranslationServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app->getLocale();

            // We're overrriding this to our own one.
            $trans = new Translator($loader, $locale);

            $trans->setFallback($app->getFallbackLocale());

            return $trans;
        });
    }
}
