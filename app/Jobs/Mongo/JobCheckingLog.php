<?php
namespace App\Jobs\Mongo;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Mongo\CheckingLog;
use Illuminate\Support\Facades\Log;

class JobCheckingLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
{
    $agProductClient = \App\Models\Product::where('id', $this->data['agProductClient_id'])->first();
    $agCodes = \App\Models\Code::where('id', $this->data['agCodes_id'])->first();
    $agUser = \App\Models\User::where('id', $this->data['agUser_id'])->first();
    $appid = \App\Models\Appid::where('appid', $this->data['appid'])->first();

    // Check if each model exists before calling toArray
    $agProductClientArray = $agProductClient ? $agProductClient->toArray() : null;
    $agCodesArray = $agCodes ? $agCodes->toArray() : null;
    $agUserArray = $agUser ? $agUser->toArray() : null;
    $appidArray = $appid ? $appid->toArray() : null;

    // Log any null values to help with debugging
    if (!$agProductClient) {
        \Log::error('agProductClient not found', ['id' => $this->data['agProductClient_id']]);
    }
    if (!$agCodes) {
        \Log::error('agCodes not found', ['id' => $this->data['agCodes_id']]);
    }
    if (!$appid) {
        \Log::error('appid not found', ['appid' => $this->data['appid']]);
    }

    // Insert data into CheckingLog
    CheckingLog::create([
        'agProductClient' => $agProductClientArray,
        'agCodes' => $agCodesArray,
        'agUser' => $agUserArray,
        'appid' => $appidArray,
        'geoLocLatitude' => $this->data['geoLocLatitude'],
        'geoLocLongitude' => $this->data['geoLocLongitude'],
    ]);
}

}

?>
