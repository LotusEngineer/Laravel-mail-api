<?php

namespace App\Jobs;

use App\Services\Dotmailer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DotMailerAddMember implements ShouldQueue
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
    protected $addressbook;

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
    public function __construct($accountName, $addressbook, $emailAddress, $templateParams)
    {
        $this->accountName = $accountName;
        $this->addressbook = $addressbook;
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
        $dotmailer->addMember($this->accountName, $this->addressbook, $this->emailAddress, $this->templateParams);
    }
}
