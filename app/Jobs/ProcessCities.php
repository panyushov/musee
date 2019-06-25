<?php

namespace App\Jobs;

use App\Services\MusementService;
use App\Services\XMLWriterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var String
     */
    private $locale;

    /**
     * Create a new job instance.
     *
     * @param String $locale
     */
    public function __construct(String $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Fetches cities using Musement services, dispatches activities jobs.
     *
     * @param MusementService $muSe
     * @param XMLWriterService $xmlServ
     * @return void
     */
    public function handle(MusementService $muSe, XMLWriterService $xmlServ)
    {
        $xmlServ->initFileLocale($this->locale);

        $cities = $muSe->getCities($this->locale);

        $cities->each(function ($city) use ($xmlServ) {
            ProcessActivities::dispatch($city->id, $this->locale);

            $xmlServ->addCityNode($city->url, $this->locale);
        });
    }
}