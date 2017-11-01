<?php

namespace Qla\CustomLdap;

use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\AdldapAuthServiceProvider;
use InvalidArgumentException;

use Adldap\Laravel\Auth\DatabaseUserProvider;
use Adldap\Laravel\Auth\NoDatabaseUserProvider;

use Illuminate\Contracts\Hashing\Hasher;


class CustomLdapServiceProvider extends AdldapAuthServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {


        $this->mergeConfigFrom(__DIR__ . '/config/qla/adldap_auth.php', 'adldap_auth');


        // - first the published views (in case they have any changes)
        $this->loadViewsFrom(resource_path('views/vendor/qla/customldap'), 'customldap');
        // - then the stock views that come with the package, in case a published view might be missing
        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'customldap');

        // Add publishable configuration.
        $this->publishes([
            __DIR__ . '/config/qla' => config_path('qla'),
            __DIR__ . '/resources/views' => resource_path('views/vendor/qla/customldap'),
        ], 'qla');


        $auth = Auth::getFacadeRoot();

        if (method_exists($auth, 'provider')) {
            // If the provider method exists, we're running Laravel 5.2.
            // Register the adldap auth user provider.
            $auth->provider('adldap', function ($app, array $config) {
                return $this->newUserProvider($app['hash'], $config);
            });
        } else {
            // Otherwise we're using 5.0 || 5.1
            // Extend Laravel authentication with Adldap driver.
            $auth->extend('adldap', function ($app) {
                return $this->newUserProvider($app['hash'], $app['config']['auth']);
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Adldap\Laravel\AdldapServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['auth'];
    }


    /**
     * Returns a new Adldap user provider.
     *
     * @param Hasher $hasher
     * @param array $config
     *
     * @return \Illuminate\Contracts\Auth\UserProvider
     *
     * @throws InvalidArgumentException
     */
    protected function newUserProvider(Hasher $hasher, array $config)
    {
        $provider = $this->getUserProvider();

        switch ($provider) {
            case CustomAdldapUserProvider::class:
                if (array_key_exists('model', $config)) {
                    return new $provider($hasher, $config['model']);
                }

                throw new InvalidArgumentException(
                    "No model is configured. You must configure a model to use with the [{$provider}]."
                );
            case DatabaseUserProvider::class:
                if (array_key_exists('model', $config)) {
                    return new $provider($hasher, $config['model']);
                }

                throw new InvalidArgumentException(
                    "No model is configured. You must configure a model to use with the [{$provider}]."
                );
            case NoDatabaseUserProvider::class:
                return new $provider;
        }

        throw new InvalidArgumentException(
            "The configured Adldap provider [{$provider}] is not supported or does not exist."
        );
    }


}
