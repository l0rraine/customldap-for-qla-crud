<?php

namespace Qla\LdapLogin;

use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\AdldapAuthServiceProvider;
use InvalidArgumentException;

use Adldap\Laravel\Auth\DatabaseUserProvider;
use Adldap\Laravel\Auth\NoDatabaseUserProvider;

use Illuminate\Contracts\Hashing\Hasher;


class LdapLoginServiceProvider extends AdldapAuthServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->mergeConfigFrom(__DIR__ . '/config/qla/adldap_auth.php', 'adldap_auth');
        $this->mergeConfigFrom(__DIR__ . '/config/qla/adldap.php', 'adldap');
        $this->mergeConfigFrom(__DIR__ . '/config/qla/ldap_login.php', 'ldap_login');


        // - first the published views (in case they have any changes)
        $this->loadViewsFrom(resource_path('views/vendor/qla/ldaplogin'), 'ldaplogin');
        // - then the stock views that come with the package, in case a published view might be missing
        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'ldaplogin');

        // Add publishable configuration.
        $this->publishes([
            __DIR__ . '/config/qla' => config_path('qla'),
            __DIR__ . '/resources/views' => resource_path('views/vendor/qla/ldaplogin'),
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

    public static function setupRoutes()
    {


        $group = [
            'namespace' => 'Qla\LdapLogin\app\Http\Controllers',
            'prefix' => config('qla.adminpanel.url_prefix')];


        \Route::group($group, function () {
            \Route::get('login', 'LoginController@showLoginForm')
                ->middleware('admin.referer','web')
                ->name('Crud.Admin.login');

            \Route::post('login', 'LoginController@login');
            \Route::get('logout', 'LoginController@logout');
        });


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        self::setupRoutes();
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
