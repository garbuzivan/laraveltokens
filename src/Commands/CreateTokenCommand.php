<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use App\Models\User;
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
    protected $name = 'tokens:create {title?} {day?} {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать новый токен (tokens:create {title?} {day?} {user_id?})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:create {title?} {day?} {user_id?}';

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
        $expiration = $arguments['day'] ? Carbon::now()->addDay(intval($arguments['day'])) : null;
        try {
            $token = $this->TokenManager->create($title, $expiration, $user_id);
        } catch (UserNotExistsException $e) {
            $this->line('Пользователь ID ' . $user_id . ' не найден.');
            return 1;
        }
        $prependText = $token->user instanceof User
            ? 'Персональный токен ' . $token->token . ' для ' . $token->user->name
            : 'Глобальный токен ' . $token->token;
        $this->line($prependText . ' создан до ' . $token->expiration . '.');
        return 1;
    }
}
