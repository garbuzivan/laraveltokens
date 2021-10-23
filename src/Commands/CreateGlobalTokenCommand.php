<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Carbon\Carbon;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class CreateGlobalTokenCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:global-create {title} {day}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать новый токен (tokens:create {title} {количество дней действия или 0 - бессрочно})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:global-create {title} {day}';

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
        $day = intval($arguments['day']);
        $expiration = $day > 0 ? Carbon::now()->addDays($day) : null;
        $token = $this->TokenManager->createGlobalToken($title, $expiration);
        $date = is_null($expiration) ? 'навсегда' : 'до ' . $expiration->format('Y-m-d H:i:s');
        $this->line('Глобальный токен ' . $token->token . ' создан '. $date . '.');
        return 1;
    }
}
