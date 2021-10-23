<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens;

use Garbuzivan\Laraveltokens\Commands\CreateTokenCommand;
use Garbuzivan\Laraveltokens\Commands\DeactivationByIDCommand;
use Garbuzivan\Laraveltokens\Commands\DeactivationByUserCommand;
use Garbuzivan\Laraveltokens\Commands\DeactivationTokenCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenAllCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenByIDCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenByUserCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenCommand;
use Garbuzivan\Laraveltokens\Commands\ProlongationByIDCommand;
use Garbuzivan\Laraveltokens\Commands\ProlongationByTokenCommand;
use Garbuzivan\Laraveltokens\Commands\ProlongationByUserCommand;
use Garbuzivan\Laraveltokens\Interfaces\AccessTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Repositories\AccessTokenRepository;

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
                CreateTokenCommand::class,
                DeactivationTokenCommand::class,
                DeactivationByIDCommand::class,
                DeactivationByUserCommand::class,
                DeleteTokenCommand::class,
                DeleteTokenByIDCommand::class,
                DeleteTokenByUserCommand::class,
                DeleteTokenAllCommand::class,
                ProlongationByIDCommand::class,
                ProlongationByUserCommand::class,
                ProlongationByTokenCommand::class,
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
        $this->app->singleton(AccessTokenRepository::class, function () {
            return new AccessTokenRepository;
        });
        $this->app->bind(AccessTokenRepositoryInterface::class, app(Config::class)->getRepository());
        $this->app->singleton(TokenManager::class, function () {
            return new TokenManager(app(Config::class), app(AccessTokenRepositoryInterface::class));
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
     * @return string
     */
    protected function publishPath(string $configFile): string
    {
        return config_path($configFile);
    }
}
