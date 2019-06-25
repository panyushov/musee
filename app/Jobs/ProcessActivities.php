<?php

namespace App\Jobs;

use App\Services\MusementService;
use App\Services\XMLWriterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessActivities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var String
     */
    private $cityID;
    /**
     * @var String
     */
    private $locale;

    /**
     * Create a new job instance.
     *
     * @param String $cityID
     * @param string $locale
     */
    public function __construct(string $cityID, string $locale)
    {
        $this->cityID = $cityID;
        $this->locale = $locale;
    }

    /**
     * Fetches activities using Musement service and writes
     * each one in sitemap as xml node.
     *
     * @param MusementService $muSe
     * @param XMLWriterService $xmlServ
     * @return void
     */
    public function handle(MusementService $muSe, XMLWriterService $xmlServ)
    {
        $activities = $muSe->getActivities($this->cityID, $this->locale);

        $activities->each(function ($activity) use ($xmlServ) {
            $xmlServ->addActivityNode($activity->url, $this->locale);
        });
    }
}
