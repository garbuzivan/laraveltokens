<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class DeactivationByUserCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:deactivation-by-user {user_id} {user_type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Деактивировать токены пользователя по ID пользователя ' .
    '(tokens:deactivation-by-user {user_id} {user_type?})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:deactivation-by-user {user_id} {user_type?}';

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
        $user_id = $arguments['user_id'] ? intval($arguments['user_id']) : null;
        $user_type = $arguments['user_type'] ?? $this->TokenManager->getDefaultMorph();
        if (is_null($user_id) || $user_id < 1) {
            $this->line('ID пользователя не введен.');
            return 1;
        }
        $this->TokenManager->deactivationAccessTokenByUser($user_id, $user_type);
        $this->line('Токены пользователя деактивированы.');
        return 1;
    }
}
