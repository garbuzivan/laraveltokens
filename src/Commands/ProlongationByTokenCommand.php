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
    protected $description = 'Продлить срок действия токена по id токена (tokens:prolongation {token} {day?})';

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
        }
        $day = intval($arguments['day']) > 0 ? intval($arguments['day']) : 365;
        $expiration = Carbon::now()->addDays($day);
        $this->TokenManager->prolongationByToken($token, $expiration);
        $this->line('Токен продлен до ' . $expiration->format('Y-m-d H:i:s') . '.');
        return 1;
    }
}
