<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Commands;

use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class DeleteTokenAllCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tokens:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удалить все токены';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tokens:clear';

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
        $this->TokenManager->deleteAllTokens();
        $this->line('Таблица токенов очищена');
        return 1;
    }
}
