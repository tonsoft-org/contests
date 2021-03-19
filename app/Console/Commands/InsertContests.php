<?php

namespace App\Console\Commands;

ini_set('max_execution_time', '45');

use App\Classes\FreeTon\Client;
use App\Models\Contest;
use Illuminate\Console\Command;

class InsertContests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contests:insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'insert contests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $last_contest = Contest::orderBy('last_paid', 'desc')->first();

        $client = new Client();
        $contests_for_insert = $client->getContests(10, [['last_paid', 'gt', ($last_contest->last_paid) ?? 0]], 'last_paid', false);

        foreach ($contests_for_insert as $contest) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,env('API_URL').'/contests');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['contest' => json_encode($contest)]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close ($ch);

            if ($response != "1") {
                die("DIE");
            }

            $this->info($contest['title']);
            $this->info($response);
        }

        return 0;
    }
}
