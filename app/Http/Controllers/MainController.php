<?php

namespace App\Http\Controllers;

use App\Models\SMTPConfig;
use App\Services\DatabaseQueueService;
use App\Services\MusementService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MainController extends Controller
{
    /**
     * Processes SMTP config alteration request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processConfig(Request $request)
    {
        $request->validate([
            "host" => "required|max:255",
            "port" => "required|numeric|between:0,65535",
            "username" => "required|max:255",
            "password" => "required|max:255",
            "encryption" => "sometimes|max:255",
            "from" => "required|email",
        ]);

        $input = $request->input();
        SMTPConfig::cleanCorruption();
        SMTPConfig::saveConfig($input);

        return redirect()->route('musee.config');
    }

    /**
     * Receives and validates request for sitemap generation.
     *
     * @param Request $request
     * @param DatabaseQueueService $qServ
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startGeneration(Request $request, DatabaseQueueService $qServ)
    {
        $request->validate([
            "emails" => 'sometimes|emails',
            "locale" => ['required', Rule::in(MusementService::allSupportedLocales())],
        ]);

        $recipients = $request->input("emails");
        $locale = $request->input("locale");
        SMTPConfig::setRecipients($recipients);
        SMTPConfig::setLocale($locale);
        SMTPConfig::cleanCorruption();


        $qServ->dispatchCitiesJobs($locale);

        return redirect()->route('musee.generator');
    }

    /**
     * Renders Sitemap Generator page.
     *
     * @param DatabaseQueueService $qServ
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function generator(DatabaseQueueService $qServ)
    {
        $warnings = [];
        $config = SMTPConfig::config();
        $this->checkConfWarnings($config, $warnings);

        $vars = [
            "warnings" => $warnings,
            "locales" => MusementService::allSupportedLocales(),
            "flgGenerating" => $qServ->generationInProgress()
        ];
        return view('generator', $vars);
    }

    /**
     * Service method. Checks smtp for error messages, puts them in the warnings
     * array and returns them in the main controller methods.
     *
     * @param $config
     * @param $warnings
     * @return void
     */
    private function checkConfWarnings($config, &$warnings)
    {
        if (SMTPConfig::exists() and $config->corrupt) {
            $warnings[] = sprintf("During the previous generation there were some problems with email notifications. 
            Please fix configuration if necessary. Exception message: %s", $config->corrupt);
        }
    }

    /**
     * Composes and renders SMTP config page.
     *
     * @param DatabaseQueueService $qServ
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function config(DatabaseQueueService $qServ)
    {
        $warnings = [];
        $config = SMTPConfig::config();
        $this->checkConfWarnings($config, $warnings);

        $vars = [
            "warnings" => $warnings,
            "config" => $config,
            "flgGenerating" => $qServ->generationInProgress(),
        ];

        return view('config', $vars);
    }

    /**
     * Root route. Based on the config existence condition
     * redirects users either directly on the sitemap generation
     * page or on the SMTP configuration page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (!SMTPConfig::exists()) {
            return redirect()->route('musee.config');
        } else {
            return redirect()->route('musee.generator');
        }
    }
}