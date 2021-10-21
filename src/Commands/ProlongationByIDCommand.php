<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Carbon\Carbon;
use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class ProlongationByIDCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:prolongation-by-id {token_id} {day?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Продлить срок действия токена по id токена (tokens:prolongation-by-id {token_id} {day?})';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:prolongation-by-id {token_id} {day?}';

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
        }
        $day = intval($arguments['day']) > 0 ? intval($arguments['day']) : 365;
        $expiration = Carbon::now()->addDays($day);
        $this->TokenManager->prolongationById($token_id, $expiration);
        $this->line('Токен продлен до ' . $expiration->format('Y-m-d H:i:s') . '.');
        return 1;
    }
}
