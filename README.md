# Artisan Mail Sender API

Mail service API for integration into back office services, 
e-commerce and crm solutions through simple api calls triggering artisan commands to subscribe customer details to
address books and trigger transactional campaigns.

# General Sequence

* Everything is handled via Laravel Queues and can easily be integrated with AWS SQS. So all `dotmailer` commands 
(other than find)  are queued to run asynchronously.
* Separate artisan command (`queue:work`) should always be running, this plucks messages from the queue & sends them.
* Use the [dotmailer](https://dotdigital.com/) portal to send standard campaign emails to address books or send
transactional emails via calls to the send campaign endpoint.


# Installation
```bash
 composer install
 cp .env.example .env
 ```
 configure settings in .env
# Configuration

.env is shipped unconfigured for safety. You will need to add dotmailer & SMTP details, and register
an AWS Queue Service

## Environment Variables Required

    # general config
    APP_NAME=
    APP_ENV=
    APP_DEBUG=
    APP_KEY=
    DB_HOST=
    DB_PORT=
    DB_USERNAME=
    DB_PASSWORD=
    DB_DATABASE=
    DOTMAILER_USERNAME=
    DOTMAILER_PASSWORD=
    
    # AWS connection details
    AWS_ACCESS_KEY_ID=
    AWS_SECRET_ACCESS_KEY=
    
    # SQS config
    SQS_PREFIX=
    SQS_QUEUE=sqs-dev-mail
    SQS_REGION=eu-west-1
    
    # set to sqs for live (to use AWS Queue)
    # or "sync" for local & commands send immediately
    QUEUE_CONNECTION=sqs

## Dotmailer 

### Plan

commands:
    
* `dotmailer:addmember $campaign $emailAddress [customisation params]` add to campaign list
* `dotmailer:sendcampaign $campaign $emailAddress` send campaign email to 1 member

To implement:

* `dotmailer:findmember $campaign $emailAddress ` find member on campaign
* `dotmailer:removemember $campaign $emailAddress`  remove from list

e.g. 
`dotmailer:addmember weekly_mailer ryan.burke@firststepsjs.com first_name=Ryan,last_name=Burke` 


### Authenticating with this API

All API responses are authenticated by 'Authorization' header, which should contain 

`"Bearer $token"`

The token is in config/api/auth.php

##### Add member to list

POST to 
http://localhost/api/dotmailer/member/{listname}/{emailAddress}

e.g.
http://localhost/api/dotmailer/member/precaps/ryan.burke@firststepsjs.com

Any JSON body is passed directly into dotmailer as the *Customisation Parameters*. e.g. when adding a 
customer to an address book, you can specify their name, mobile number etc. 


## AWS Simple Queue Service

You can choose the queuing system of your choice however integration with amazon SQS is simple.

This will hold a queue of events yet to be handled.

Create an AWS SQS Queue, then configure .env as follows

`AWS_ACCESS_KEY_ID` your AWS Access Key ID 
	
`AWS_SECRET_ACCESS_KEY` your AWS Secret Access Key
	
`SQS_PREFIX` SQS URL Prefix, i.e. the URL portion before the sqs queue name

`SQS_QUEUE` SQS queue name

`SQS_REGION` Amazon Queue Region, e.g. eu-west-1

## Logging

You can use local logging to your server if you prefer or use your own solution however provided in the logging
folder is a Cloud watch logger factory.


The log group name is /app/APP_NAME-APP_ENV in your cloud watch logs if you choose this solution.

## Queue Runner

To start the queue runner (which should always be left running), run `php artisan queue:work`

The supervisor.conf file in the .deploy folder will attempt to start this.

## Dependencies

System is built on [Laravel](https://laravel.com/) v5.7, using AWS SQS 

## To do

* Potentially speak to ex data science colleagues about potential uses for call log data metrics.

