<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens;

use Garbuzivan\Laraveltokens\Commands\CreateGlobalTokenCommand;
use Garbuzivan\Laraveltokens\Commands\CreateTokenCommand;
use Garbuzivan\Laraveltokens\Commands\DeactivationByIDCommand;
use Garbuzivan\Laraveltokens\Commands\DeactivationGlobalByIDCommand;
use Garbuzivan\Laraveltokens\Commands\DeactivationByUserCommand;
use Garbuzivan\Laraveltokens\Commands\DeactivationGlobalTokenCommand;
use Garbuzivan\Laraveltokens\Commands\DeactivationTokenCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenAllCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteGlobalTokenByIDCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenByIDCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenByUserCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteGlobalTokenCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenCommand;
use Garbuzivan\Laraveltokens\Commands\ProlongationByIDCommand;
use Garbuzivan\Laraveltokens\Commands\ProlongationByTokenCommand;
use Garbuzivan\Laraveltokens\Commands\ProlongationGlobalTokenByTokenCommand;
use Garbuzivan\Laraveltokens\Commands\ProlongationByUserCommand;
use Garbuzivan\Laraveltokens\Commands\ProlongationGlobalTokenByIDCommand;
use Garbuzivan\Laraveltokens\Interfaces\AccessTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Interfaces\GlobalTokenRepositoryInterface;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services...
     *
     * @return void
     */
    public function boot()
    {
        $configPath = $this->configPath();

        $this->publishes([
            $configPath . '/config.php' => $this->publishPath('laraveltokens.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateGlobalTokenCommand::class,
                CreateTokenCommand::class,
                DeactivationByIDCommand::class,
                DeactivationByUserCommand::class,
                DeactivationGlobalByIDCommand::class,
                DeactivationGlobalTokenCommand::class,
                DeactivationTokenCommand::class,
                DeleteGlobalTokenByIDCommand::class,
                DeleteGlobalTokenCommand::class,
                DeleteTokenAllCommand::class,
                DeleteTokenByIDCommand::class,
                DeleteTokenByUserCommand::class,
                DeleteTokenCommand::class,
                ProlongationByIDCommand::class,
                ProlongationByTokenCommand::class,
                ProlongationByUserCommand::class,
                ProlongationGlobalTokenByIDCommand::class,
                ProlongationGlobalTokenByTokenCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Config::class, function () {
            return new Config;
        });
        $this->app->bind(
            AccessTokenRepositoryInterface::class,
            app(Config::class)->getRepositoryAccessToken()
        );
        $this->app->bind(
            GlobalTokenRepositoryInterface::class,
            app(Config::class)->getRepositoryGlobalToken()
        );
        $this->app->singleton(TokenManager::class, function () {
            return new TokenManager(
                app(Config::class),
                app(AccessTokenRepositoryInterface::class),
                app(GlobalTokenRepositoryInterface::class),
                app(Coder::class)
            );
        });
    }

    /**
     * @return string
     */
    protected function configPath(): string
    {
        return __DIR__ . '/../config';
    }

    /**
     * @param string $configFile
     *
     * @return string
     */
    protected function publishPath(string $configFile): string
    {
        return config_path($configFile);
    }
}
