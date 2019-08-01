<?php

namespace App\Http\Controllers\Api\DotMailer;

use App\Exceptions\InvalidEmailException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Exception;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Illuminate\Support\Facades\Log;
use Validator;

class CampaignController extends Controller
{
    /**
     * Send a triggered campaign email to the email address
     *
     * Request (in the URL) will contain the list name & email address
     * If supplied, request body is expected to be JSON, containing dotmailer customisation parameters
     * (such as the customer's name, phone number, etc)
     *
     * @param Request $request
     */
    public function sendTriggeredCampaign(Request $request) {
        try {
            Log::info(sprintf('api/sendTriggeredCampaign called (%s, %s)', $request->campaignName, $request->emailAddress));
            Log::info($request);

            /*$validator = Validator::make($request->all(), config('dotmailer.campaigns.'.$request->campaignName.'.rules'));
            if ($validator->fails()) {
                return response()->json([ 'error' => $validator->errors()->all()], 422);
            }*/

            Artisan::call('dotmailer:sendcampaign', [
                'campaign' => $request->campaignName,
                'email' => $request->emailAddress,
                'params' => $request->json()
            ]);

            // If user is being sent the X email also subscribe them to X list
            if ($request->campaignName == 'campaign_name') {
                Artisan::call('dotmailer:addmember', [
                    'addressbook' => 'Address_book_name',
                    'email' => $request->emailAddress,
                    'params' => $request->json()
                ]);
            }
            //Any additional subscription rules
           // else if

        } catch (NotFoundResourceException $e) {
            Log::error($e->getMessage());
            return response()->json([ 'error' => $e->getMessage()], 404);
        } catch (InvalidEmailException $e) {
            Log::error($e->getMessage());
            return response()->json([ 'error' => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([ 'error' => $e->getMessage()], 500);
        }
    }
}
