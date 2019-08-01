<?php

namespace App\Console\Commands;

use App\Services\Dotmailer;
use Illuminate\Console\Command;
use App\Exceptions\InvalidEmailException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;


class DotMailerAddMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dotmailer:addmember 
        {addressbook : Our addressbook reference (see config/dotmailer.php) } 
        {email : User\'s email address} 
        {params? : (optional) comma-separated list of key-value pairs to save against user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add user to DotMailer campaign';

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
     * @return mixed
     */
    public function handle()
    {
        // read input parameters
        $addressbookName = $this->argument('addressbook');
        $emailAddress = $this->argument('email');
        $params = $this->argument('params');

        // parameter validation
        if (! array_key_exists($addressbookName, config('dotmailer.addressbooks'))) {
            throw new NotFoundResourceException('Addressbook not found in configuration');
        }

        if (! filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException('Invalid email address');
        }

        $accountName = config('dotmailer.addressbooks.'.$addressbookName.'.account');

        // queue the add member command
        $dotmailer = new Dotmailer();
        $dotmailer->queueAddMember($accountName, $addressbookName, $emailAddress, $params);
    }
}
