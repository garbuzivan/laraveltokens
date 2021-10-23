<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class DeleteGlobalTokenByIDCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:global-delete-by-id {token_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удалить глобальный токен по ID токена (tokens:global-delete-by-id {token_id})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:global-delete-by-id {token_id}';

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
        $token_id = $arguments['token_id'] ? intval($arguments['token_id']) : null;
        if (is_null($token_id) || $token_id < 1) {
            $this->line('ID токена не введен.');
            return 1;
        }
        $this->TokenManager->deleteGlobalTokenById($token_id);
        $this->line('Токен удален.');
        return 1;
    }
}
