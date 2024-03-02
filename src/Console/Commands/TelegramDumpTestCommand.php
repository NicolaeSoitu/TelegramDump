<?php

namespace NicolaeSoitu\TelegramDump\Console\Commands;

use Illuminate\Console\Command;
use NicolaeSoitu\TelegramDump\TelegramDump;


class TelegramDumpTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram-dump:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Telegram bot test';

    /**
     * Execute the console command.
     */
    public function handle()
    {

      dump(TelegramDump::send('test'));
    }
}
