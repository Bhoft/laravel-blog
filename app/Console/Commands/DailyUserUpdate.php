<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DailyUserUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userUpdate:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Selects all Users without a given age and updates is via the agify.io Api';

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
        $users = User::whereNull('age')->get();

        $counter = null;
        if (sizeof($users)) {
            foreach ($users as $user) {
                $age = $this->makeRequest($user->name);
                if ($age) {
                    $user->age = (int) $age;
                    $user->save();
                    $counter++;
                }
            }
        }

        $this->info("Successfully updated {$counter} User entries");
    }


    protected function makeRequest($name)
    {
        $client = new \GuzzleHttp\Client(['headers' => ['Accept' => 'application/json']]);
        $url = 'https://api.agify.io?name=' . $name;

        $res = $client->request('GET', $url, []);

        if ($res->getStatusCode() == 200) {
            if ($res->getBody()) {
                $result = json_decode($res->getBody(), true);

                if (isset($result['age'])) {
                    return $result['age'];
                }
            }
        }
        return false;
    }
}
