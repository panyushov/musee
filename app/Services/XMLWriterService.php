<?php

namespace App\Services;

use DOMDocument;
use Illuminate\Support\Facades\Storage;

class XMLWriterService
{
    const CITY_PRIORITY = "0.7";
    const ACTIVITY_PRIORITY = "0.5";

    const PATH_PREFIX = "app/";
    const FILE_NAME_PATTERN = 'sitemap_%s.xml';

    /**
     * Removes and recreates xml sitemap file for the specified locale.
     *
     * @param string $locale
     */
    public function initFileLocale(string $locale): void
    {
        $this->removeLocaleDoc($locale);
        $this->createLocaleDoc($locale);
    }

    /**
     * Service method. Removes xml file for the specified locale.
     *
     * @param string $locale
     */
    public function removeLocaleDoc(string $locale): void
    {
        Storage::delete($this->localeFilePath($locale));
    }

    /**
     * Returns absolute file path for the sitemap file for
     * specified locale.
     *
     * @param $locale
     * @return string
     */
    public function localeFilePath($locale): string
    {
        $path = self::PATH_PREFIX . $this->localeFileName($locale);
        return storage_path($path);
    }

    /**
     * Returns only file name for sitemap file.
     *
     * @param $locale
     * @return string
     */
    public function localeFileName($locale): string
    {
        return sprintf(self::FILE_NAME_PATTERN, $locale);
    }

    /**
     * Creates xml sitemap document for the specified locale.
     *
     * @param $locale
     */
    public function createLocaleDoc(string $locale): void
    {
        $xml = new DOMDocument("1.0", "UTF-8");
        $xml_urlset = $xml->createElement('urlset');
        $xml_urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->appendChild($xml_urlset);
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;
        Storage::put($this->localeFileName($locale), $xml->saveXML());
    }

    /**
     * Adds city node in the sitemap locale document.
     *
     * @param string $cityURL
     * @param string $locale
     */
    public function addCityNode(string $cityURL, string $locale): void
    {
        $this->addXMLNode($cityURL, self::CITY_PRIORITY, $locale);
    }

    /**
     * Adds loc and priority nodes in the locale xml document.
     *
     * @param string $loc
     * @param string $priority
     * @param string $locale
     */
    public function addXMLNode(string $loc, string $priority, string $locale): void
    {
        $xml = new DOMDocument();

        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;
        $xml->loadXML(Storage::get($this->localeFileName($locale)));

        $xml_urlset_list = $xml->getElementsByTagName("urlset");
        if ($xml_urlset_list->length > 0) {
            $xml_urlset = $xml_urlset_list->item(0);

            $xml_url = $xml->createElement("url");
            $xml_url_loc = $xml->createElement("loc", $loc);
            $xml_url_priority = $xml->createElement("priority", $priority);
            $xml_url->appendChild($xml_url_loc);
            $xml_url->appendChild($xml_url_priority);
            $xml_urlset->appendChild($xml_url);

            Storage::put($this->localeFileName($locale), $xml->saveXML());
        }
    }

    /**
     * Adds activity node in the sitemap locale document.
     *
     * @param string $activityURL
     * @param string $locale
     */
    public function addActivityNode(string $activityURL, string $locale): void
    {
        $this->addXMLNode($activityURL, self::ACTIVITY_PRIORITY, $locale);
    }
}