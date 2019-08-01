<?php

namespace App\Jobs;

use App\Services\Dotmailer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class DotMailerSendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @property string $accountName dotmailer account to use
     * value is from our config/dotmailer.php file
     */
    protected $accountName;

    /**
     * @property string $campaign DotMailer internal campaign ID
     */
    protected $campaign;

    /**
     * @property string $emailAddress Email address
     */
    protected $emailAddress;

    /**
     * @property array variables to populate in template
     */
    protected $templateParams;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($accountName, $campaign, $emailAddress, $templateParams)
    {
        $this->accountName = $accountName;
        $this->campaign = $campaign;
        $this->emailAddress = $emailAddress;
        $this->templateParams = $templateParams;
    }

    /**
     * Execute the job. This sends the actual command to dotmailer
     *
     * @return void
     */
    public function handle()
    {
        $dotmailer = new Dotmailer();
        $dotmailer->sendCampaign($this->accountName, $this->campaign, $this->emailAddress, $this->templateParams);
    }
}
