<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Carbon\Carbon;
use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class ProlongationByUserCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:prolongation-by-user {user_id} {day?} {user_type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Продлить срок действия всех токенов по id пользователя ' .
    '(tokens:prolongation-by-user {user_id} {day?} {user_type?})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:prolongation-by-user {user_id} {day?} {user_type?}';

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
        $expiration = intval($arguments['day']) > 0 ? Carbon::now()->addDays(intval($arguments['day'])) : null;
        $this->TokenManager->prolongationAccessTokenByUser($user_id, $user_type, $expiration);
        $date = is_null($expiration) ? 'навсегда' : 'до ' . $expiration->format('Y-m-d H:i:s');
        $this->line('Токен продлен ' . $date . '.');
        return 1;
    }
}
