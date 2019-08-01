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

class AddressbookController extends Controller
{
    /**
     * Add an email address to a dotmailer list
     *
     * Request (in the URL) will contain the list name & email address
     * If supplied, request body is expected to be JSON, containing dotmailer customisation parameters
     * (such as the customer's name, phone number, etc)
     *
     * @param Request $request
     */
    public function addMember(Request $request) {
        try {
            Log::info(sprintf('api/addMember called (%s, %s)', $request->listName, $request->emailAddress));

            /*$validator = Validator::make($request->all(), config('dotmailer.addressbooks.'.$request->listName.'.rules'));
            if ($validator->fails()) {
                return response()->json([ 'error' => $validator->errors()->all()], 422);
            }*/

            Artisan::call('dotmailer:addmember', [
                'addressbook' => $request->listName,
                'email' => $request->emailAddress,
                'params' => $request->json()
            ]);
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
