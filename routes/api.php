<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->group(function () {


    //Route::middleware('api.token')->group(function() {
        // DotMailer - Add Member
        Route::post('/dotmailer/member/{listName}/{emailAddress}', 'DotMailer\AddressbookController@addMember');

        // DotMailer - send campaign
        Route::post('/dotmailer/campaign/{campaignName}/{emailAddress}', 'DotMailer\CampaignController@sendTriggeredCampaign');
    //});
});
