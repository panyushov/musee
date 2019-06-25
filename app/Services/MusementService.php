<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class MusementService
{
    const LIMIT = 20;
    const LIMIT_PARAM_STRING = "?limit=%u";

    const SPANISH_LOCALE = "es-ES";
    const FRENCH_LOCALE = "fr-FR";
    const ITALIAN_LOCALE = "it-IT";

    protected $citiesEndpoint = "https://api.musement.com/api/v3/cities";
    protected $activitiesEndpoint = "https://api.musement.com/api/v3/cities/%u/activities";

    private $httpClient;

    /**
     * Constructor injects client http library.
     *
     * MusementService constructor.
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Returns array with all available locales.
     *
     * @return array
     */
    public static function allSupportedLocales()
    {
        return [
            self::SPANISH_LOCALE,
            self::FRENCH_LOCALE,
            self::ITALIAN_LOCALE,
        ];
    }

    /**
     * Method executes http request to the cities endpoints,
     * processes response and returns a collection.
     *
     * @param string $locale
     * @return \Illuminate\Support\Collection
     */
    public function getCities($locale = self::ITALIAN_LOCALE)
    {
        try {
            $citiesURL = sprintf($this->citiesEndpoint . self::LIMIT_PARAM_STRING, self::LIMIT);

            $response = $this->httpClient->request("GET", $citiesURL, [
                'headers' => [
                    'Accept-Language' => $locale,
                ],
            ]);
            return $this->decodeCities($response);
        } catch (GuzzleException $e) {
            // Service or network is down. Nothing much we can do here.
            // Returning an empty collection.
            $errorMessage = sprintf("Cities endpoint for %s locale failed to answer", $locale);
            Log::error($errorMessage);
            return collect([]);
        }
    }

    /**
     * Service method. Decodes cities api response.
     *
     * @param $response
     * @return \Illuminate\Support\Collection
     */
    private function decodeCities($response)
    {
        return collect(json_decode($response->getBody()));
    }

    /**
     * Method executes http request to the activities endpoints,
     * processes response and returns a collection.
     *
     * @param $cityID
     * @param string $locale
     * @return \Illuminate\Support\Collection
     */
    public function getActivities($cityID, $locale = self::ITALIAN_LOCALE)
    {
        $url = sprintf($this->activitiesEndpoint . self::LIMIT_PARAM_STRING, $cityID, self::LIMIT);

        try {
            $response = $this->httpClient->request("GET", $url, [
                'headers' => [
                    'Accept-Language' => $locale,
                ],
            ]);

            return $this->decodeActivities($response);

        } catch (GuzzleException $e) {
            // Service or network is down. Nothing much we can do here.
            // Returning an empty collection.
            $errorMessage = sprintf("Activites endpoint for %u city id and %s locale failed to answer", $cityID, $locale);
            Log::error($errorMessage);
            return collect([]);
        }
    }

    /**
     * Service method. Decodes activities api response.
     *
     * @param $response
     * @return \Illuminate\Support\Collection
     */
    private function decodeActivities($response)
    {
        return collect(json_decode($response->getBody())->data);
    }


}