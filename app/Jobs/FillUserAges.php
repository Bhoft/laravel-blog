<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class FillUserAges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::whereNull('age')->get();

        if (sizeof($users)) {
            foreach ($users as $user) {
                $age = $this->makeRequest($user->name);
                if ($age) {
                    $user->age = (int) $age;
                    $user->save();
                }
            }
        }
    }

    protected function makeRequest($name)
    {
        $client = new \GuzzleHttp\Client(['headers' => ['Accept' => 'application/json']]);
        $url = 'https://api.agify.io?name=' . $name;


        $res = $client->request('GET', $url, []);

        if ($res->getStatusCode() == 200) {
            if ($res->getBody()) {
                $result = json_decode($res->getBody(), true);
            }
            if (isset($result['age'])) {
                return $result['age'];
            }
        }
        return false;
    }
}
