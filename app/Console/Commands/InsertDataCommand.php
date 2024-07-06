<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;

class InsertDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:insert-data-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $arraySave = [
            'name' => fake()->name(),
            'age' => random_int(18,40),
        ];
        Employee::create($arraySave);
        file_put_contents('/var/log/laravel-command.log', now() . " - Command ran successfully\n".$arraySave, FILE_APPEND);

    }
}
