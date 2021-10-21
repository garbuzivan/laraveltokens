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
    protected $name = 'tokens:prolongation-by-user {token_id} {day?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Продлить срок действия всех токенов по id пользователя ' .
    '(tokens:prolongation-by-user {token_id} {day?})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:prolongation-by-user {token_id} {day?}';

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
        if (is_null($user_id) || $user_id < 1) {
            $this->line('ID пользователя не введен.');
        }
        $day = intval($arguments['day']) > 0 ? intval($arguments['day']) : 365;
        $expiration = Carbon::now()->addDays($day);
        $this->TokenManager->prolongationByUser($user_id, $expiration);
        $this->line('Токены пользователя продлены до ' . $expiration->format('Y-m-d H:i:s') . '.');
        return 1;
    }
}
