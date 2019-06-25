<?php

namespace App\Mail;

use App\Services\XMLWriterService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SitemapGenerated extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @param String $locale
     * @param  object|array|string $recipients
     */
    const MAIL_SUBJECT_TEMPLATE = "MUSEMENT.COM sitemap for %s";
    private $recipients;

    public function __construct(String $locale, $recipients)
    {
        $this->locale = $locale;
        $this->recipients = $recipients;
    }

    /**
     * Composes 'end of the sitemap generation' email
     * notification.
     *
     * @param XMLWriterService $xmlServ
     * @return $this
     */
    public function build(XMLWriterService $xmlServ)
    {
        $fileName = $xmlServ->localeFileName($this->locale);
        $subject = sprintf(self::MAIL_SUBJECT_TEMPLATE, $this->locale);

        return $this->subject($subject)
            ->to($this->recipients)
            ->attachFromStorageDisk("local", $fileName)
            ->text("mail");
    }
}
