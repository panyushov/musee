<?php

namespace App\Services;

use App\Jobs\ProcessCities;
use App\Mail\SitemapGenerated;
use App\Models\SMTPConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class DatabaseQueueService
{
    /**
     * If there's no generation in progress, this method dispatched
     * cities processing job for specified locale.
     *
     * @param string $locale
     */
    public function dispatchCitiesJobs($locale = MusementService::ITALIAN_LOCALE)
    {
        if (!$this->generationInProgress()) {
            ProcessCities::dispatch($locale);
        }
    }

    /**
     * Binary check if there is a generation going by checking queue size.
     *
     * @return bool
     */
    public function generationInProgress()
    {
        return (Queue::size() > 0) ? true : false;
    }

    /**
     * Called by queue event listener after every job is processed.
     * This method executes post-generation procedures,
     * like in current implementation, sending emails.
     *
     */
    public function queueIsComplete()
    {
        if (!$this->generationInProgress() and SMTPConfig::exists()) {
            $config = SMTPConfig::config();
            $recipients = $config->getRecipients();
            $locale = $config->locale;
            if ($recipients and $locale) {
                $config->setConfig();
                $this->sendEmail($locale, $recipients, $config);
                $config->notify = null;
                $config->locale = null;
                $config->save();
            }
        }
    }

    /**
     * Service method. Tries to send emails to recipients.
     *
     * @param $locale
     * @param $recipients
     * @param $config
     */
    private function sendEmail($locale, $recipients, $config): void
    {
        try {
            MAIL::send(new SitemapGenerated($locale, $recipients));
        } catch (\Exception $e) {
            // Something is wrong with either channel or smtp config, marking config as corrupt
            // by saving exception message.
            $config->markAsCorrupt($e->getMessage());
            $message = sprintf("Error sending email message with exception %s", $e->getMessage());
            Log::error($message);
        }
    }
}