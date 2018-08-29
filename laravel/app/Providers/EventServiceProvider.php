<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\LogSentMail' => [
            'App\Listeners\LogSentMailListener',
        ],
        'App\Events\LogMailDownload' => [
            'App\Listeners\LogMailDownloadListener',
        ],
        'App\Events\LogGuestSoftwareDownload' => [
            'App\Listeners\LogGuestSoftwareDownloadListener',
        ],
        'App\Events\UpdateCustomerVersion' => [
            'App\Listeners\UpdateCustomerVersionDatabase','App\Listeners\LogVersionChange','App\Listeners\UpdateVersionFile'
        ],
        'App\Events\LogCustomerUpdateDownload' => [
            'App\Listeners\LogCustomerUpdateDownloadListener',
        ],
        'App\Events\LogPasswordResetRequest' => [
            'App\Listeners\LogPasswordResetRequestListener',
        ],
        'App\Events\LogUserLogin' => [
            'App\Listeners\LogUserLoginListener',
        ],
        'App\Events\LogDirectDownload' => [
            'App\Listeners\LogDirectDownloadListener',
        ],
        'App\Events\LogInitLogin' => [
            'App\Listeners\LogInitLoginListener',
        ],
        'App\Events\LogTktUploadedFileDownload' => [
            'App\Listeners\LogTktUploadedFileDownloadListener',
        ],
        'App\Events\ConversationInit' => [
            'App\Listeners\ConversationInitListener',
        ],
        'App\Events\ConvUpdateUserActivity' => [
            'App\Listeners\ConvUpdateUserActivityListener',
        ],
        'App\Events\LogSupportPrintObjectDownload' => [
            'App\Listeners\LogSupportPrintObjectDownloadListener',
        ],
        'App\Events\LogPrintObjectDownloadFromMail' => [
            'App\Listeners\LogPrintObjectDownloadFromMailListener',
        ],
        'App\Events\LogDatabaseBackupDownload' => [
            'App\Listeners\LogDatabaseBackupDownloadListener',
        ],
        'App\Events\LogSentSMS' => [
            'App\Listeners\LogSentSMSListener',
        ],
        'App\Events\LogThirdPartyAppDownloads' => [
            'App\Listeners\LogThirdPartyAppDownloadsListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
