<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class DeactivationGlobalTokenCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:global-deactivation {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Деактивировать глобальный токен (tokens:global-deactivation {token})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:global-deactivation {token}';

    /**
     * @var Composer
     */
    public Composer $composer;

    /**
     * @var TokenManager
     */
    public TokenManager $TokenManager;

    /**
     * Create a new command instance.
     */
    public function __construct(TokenManager $TokenManager)
    {
        parent::__construct();
        $this->TokenManager = $TokenManager;
    }

    /**
     * Execute the command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->arguments();
        $token = $arguments['token'] ?? null;
        if (is_null($token)) {
            $this->line('Токен не введен.');
            return 1;
        }
        $this->TokenManager->deactivationGlobalToken($token);
        $this->line('Токен деактивирован.');
        return 1;
    }
}
