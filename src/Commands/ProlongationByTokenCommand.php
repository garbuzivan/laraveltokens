<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Carbon\Carbon;
use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class ProlongationByTokenCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:prolongation {token} {day?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Продлить срок действия токена (tokens:prolongation {token} {day?})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:prolongation {token} {day?}';

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
        $expiration = intval($arguments['day']) > 0 ? Carbon::now()->addDays(intval($arguments['day'])) : null;
        $this->TokenManager->prolongationAccessToken($token, $expiration);
        $date = is_null($expiration) ? 'навсегда' : 'до ' . $expiration->format('Y-m-d H:i:s');
        $this->line('Токен продлен ' . $date . '.');
        return 1;
    }
}
