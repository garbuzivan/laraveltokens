<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class DeleteTokenCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:delete {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удалить токен (tokens:delete {token})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:delete {token}';

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
        $this->TokenManager->deleteByToken($token);
        $this->line('Токен удален.');
        return 1;
    }
}
