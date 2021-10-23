<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Carbon\Carbon;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class CreateTokenCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:create {title} {day} {user_id} {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать новый токен ' .
    '(tokens:create {title} {количество дней действия или 0 == бессрочно} {user_id} {type?})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:create {title} {day} {user_id} {type?}';

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
        $title = $arguments['title'] ?? date('Y-m-d H:i:s');
        $user_id = $arguments['user_id'] ? intval($arguments['user_id']) : null;
        $type = $arguments['type'] ?? $this->TokenManager->getDefaultMorph();
        $day = intval($arguments['day']);
        $expiration = $day > 0 ? Carbon::now()->addDays($day) : null;
        try {
            $token = $this->TokenManager->createAccessToken($title, $expiration, $user_id, $type);
        } catch (UserNotExistsException $e) {
            $this->line('Пользователь ID ' . $user_id . ' не найден.');
            return 1;
        }
        $this->line('Персональный токен ' . $token->token . ' создан до ' . $token->expiration . '.');
        return 1;
    }
}
