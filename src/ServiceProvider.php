<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens;

use Garbuzivan\Laraveltokens\Commands\CreateTokenCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenAllCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenByIDCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenByUserCommand;
use Garbuzivan\Laraveltokens\Commands\DeleteTokenCommand;
use Garbuzivan\Laraveltokens\Interfaces\TokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Repositories\TokenRepository;

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
                DeleteTokenCommand::class,
                DeleteTokenByIDCommand::class,
                DeleteTokenByUserCommand::class,
                DeleteTokenAllCommand::class,
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
        $this->app->singleton(TokenRepository::class, function () {
            return new TokenRepository;
        });
        $this->app->bind(TokenRepositoryInterface::class, app(Config::class)->getRepository());
        $this->app->singleton(TokenManager::class, function () {
            return new TokenManager(app(Config::class), app(TokenRepositoryInterface::class));
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
