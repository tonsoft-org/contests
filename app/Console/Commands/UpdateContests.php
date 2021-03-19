<?php

namespace App\Console\Commands;

ini_set('max_execution_time', '300');

use App\Classes\FreeTon\Client;
use App\Models\Contest;
use Extraton\TonClient\Entity\Net\Filters;
use Extraton\TonClient\Entity\Net\OrderBy;
use Extraton\TonClient\Entity\Net\ParamsOfQueryCollection;
use Extraton\TonClient\TonClient;
use Illuminate\Console\Command;

class UpdateContests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contests:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contests update';

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
        $contests = Contest::whereRaw('FROM_UNIXTIME(voting_end_at) > created_at')->orWhereNull('created_at')->get();
        $client = new Client();

        foreach ($contests as $contest) {
            $updated_contest = $client->getContests(1, [['id', 'eq', $contest->id]])[0] ?? false;
            if ($updated_contest !== false) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,env('API_URL').'/contests');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['contest' => json_encode($updated_contest)]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close ($ch);

                if ($response != "1") {
                    die("DIE");
                }

                $this->info($updated_contest['title']);
                $this->info($response);
            }
        }

        return 0;
    }
}
