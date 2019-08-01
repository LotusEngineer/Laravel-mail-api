<?php

namespace App\Services;

use App\Jobs\DotMailerAddMember;
use App\Jobs\DotMailerSendCampaign;
use Illuminate\Support\Facades\Log;

class Dotmailer {
    /**
     * @property curl
     */
    protected $client;

    protected $baseUri;

    public function __construct()
    {
        $this->baseUri = 'https://r1-api.dotmailer.com/' . config('dotmailer.api_version') . '/';

        $this->client = curl_init();
        curl_setopt($this->client, CURLAUTH_BASIC, CURLAUTH_DIGEST);
        curl_setopt($this->client, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->client, CURLOPT_HTTPHEADER, array('Accept: application/json',
                'Content-Type: application/json'));
    }

    protected function setAccount($accountName) {
        curl_setopt(
            $this->client, CURLOPT_USERPWD,
            config('dotmailer.auth.'.$accountName.'.username'). ':' . config('dotmailer.auth.'.$accountName.'.password')
        );

    }

    /**
     * Queue a member to be added onto a dotmailer addressbook. addMember() actually performs the add
     *
     * @param $accountName string Your dotmailer account name (from config/dotmailer.php)
     * @param $addressbookName string Your addressbook name (config/dotmailer.php)
     * @param $emailAddress string Email Address
     * @param null $params string dotmailer personalisation values (key=value, comma separated)
     */
    public function queueAddMember($accountName, $addressbookName, $emailAddress, $params=null) {
        Log::info(sprintf('queueAddMember (%s, %s)', $addressbookName, $emailAddress));
        Log::info(print_r(func_get_args(), true));
        // get dotmailer campaign ID from
        $addressbookId = config('dotmailer.addressbooks')[$addressbookName]['id'];
        $params = $this->personalizationValues($this->paramsToArray($params));

        // register an 'add member' request in queue
        // this does not send immedately, artisan queue process does that
        Log::info("dispatching $emailAddress onto queue");
        DotMailerAddMember::dispatch($accountName, $addressbookId, $emailAddress, $params);
    }

    /**
     * Add a member, called from artisan queue, really add member on dotmailer
     *
     * @param $accountName string our dotmailer account name (from config/dotmailer.php)
     * @param $addressbookId numeric dotmailer addressbook id
     * @param $emailAddress customer email address
     * @param null $templateParams
     */
    public function addMember($accountName, $addressbookId, $emailAddress, $templateParams=null) {
        Log::info(sprintf('adding %s to addressbook %s', $emailAddress, $addressbookId));
        $this->setAccount($accountName);

        curl_setopt($this->client, CURLOPT_URL, $this->baseUri . sprintf('address-books/%d/contacts', $addressbookId));
        curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->client, CURLOPT_POSTFIELDS,json_encode([
            'email' => $emailAddress,
            'DataFields' => $templateParams
        ]));
        $res = curl_exec($this->client);

        $responseCode = curl_getinfo($this->client,  CURLINFO_RESPONSE_CODE);
        if ($responseCode >= 400) {
            Log::error(sprintf('could not add member (%s) to list %s', $emailAddress, $addressbookId));
            Log::error($res);
        }
    }

    /**
     * Queue a member to be sent a triggered campaign email. sendCampaign() actually performs the send
     *
     * @param $accountName string our dotmailer account name (from config/dotmailer.php)
     * @param $campaignName string Our campaign name (config/dotmailer.php)
     * @param $emailAddress string Email Address
     * @param null $params string dotmailer personalisation values (key=value, comma separated)
     */
    public function queueSendCampaign($accountName, $campaignName, $emailAddress, $params=null) {
        Log::info(sprintf('queueSendCampaign (%s, %s)', $campaignName, $emailAddress));
        Log::info(print_r(func_get_args(), true));
        // get dotmailer campaign ID from
        $campaignId = config('dotmailer.campaigns')[$campaignName]['id'];
        Log::info("Processing \$params:" .print_r($params, true));
        $params = $this->personalizationValues($this->paramsToArray($params), 'Name');
        Log::info("Sending \$params to DotMailerSendCampaign:" .print_r($params, true));
        // register a 'send campaign' request in queue
        // this does not send immedately, artisan queue process does that
        Log::info("dispatching SendCampaign $emailAddress onto queue");
        DotMailerSendCampaign::dispatch($accountName, $campaignId, $emailAddress, $params);
    }

    /**
     * Send triggered campaign email
     *
     * @param $accountName string our dotmailer account name (from config/dotmailer.php)
     * @param $addressbookId numeric dotmailer addressbook id
     * @param $emailAddress customer email address
     * @param null $templateParams
     */
    public function sendCampaign($accountName, $campaignId, $emailAddress, $templateParams=null) {
        Log::info(sprintf('sending campaign %s on account %s to %s', $campaignId, $accountName, $emailAddress));
        $this->setAccount($accountName);
        Log::info(print_r($templateParams, true));
        curl_setopt($this->client, CURLOPT_URL, $this->baseUri . 'email/triggered-campaign');
        curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->client, CURLOPT_POSTFIELDS,json_encode([
            'toAddresses' => [ $emailAddress ],
            'campaignID' => $campaignId,
            'personalizationValues' => $templateParams
        ]));
        $res = curl_exec($this->client);

        $responseCode = curl_getinfo($this->client,  CURLINFO_RESPONSE_CODE);
        if ($responseCode >= 400) {
            Log::error(sprintf('could not send campaign (%s) on account %s to address %s', $campaignId, $accountName, $emailAddress));
            Log::error($res);
        }
    }


    /**
     * Convert comma-separated params to array of key-value pairs
     *
     * @param $params string
     * @return array
     */
    protected function paramsToArray($params) {
        if (! $params) {
            return false;
        }
        $rtn = [];
        if (is_string($params)) {
            foreach (explode(',', $params) as $pair) {
                list($k, $v) = explode('=', $pair);
                $rtn[$k] = $v;
            }
        } elseif (is_object($params)) {
            foreach ($params as $k => $v) {
                $rtn[$k] = $v;
            }
        }
        return $rtn;
    }

    /**
     * Translate an array of key/value pairs to dotmailer format
     *
     * Some dotmailer APIs use 'key & value', some use 'name & value'
     *
     * @param $params array
     * @return array
     */
    protected function personalizationValues($params, $type = 'Key') {
        if (! $params) {
            return false;
        }
        $rtn = [];
        foreach ($params as $k => $v) {
            $rtn[] = [
                $type => $k,
                'Value' => $v
            ];
        }
        return $rtn;
    }

}