<?php

namespace App\Providers;

use App\Services\DatabaseQueueService;
use App\Services\MusementService;
use App\Services\XMLWriterService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Services\DatabaseQueueService', function () {
            return new DatabaseQueueService();
        });

        $this->app->singleton('App\Services\MusementService', function ($app) {
            return new MusementService($app->make('GuzzleHttp\Client'));
        });

        $this->app->singleton('App\Services\XMLWriterService', function ($app) {
            return new XMLWriterService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @param DatabaseQueueService $qServ
     * @return void
     */
    public function boot(DatabaseQueueService $qServ)
    {
        // Extending validation
        Validator::extend("emails", function ($attribute, $value, $parameters) {
            $rules = [
                'email' => 'sometimes|email',
            ];
            $emails = explode(",", $value);

            foreach ($emails as $email) {
                $email = trim($email);
                $data = [
                    'email' => $email
                ];
                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    return false;
                }
            }
            return true;
        }, "Every comma separated recipient should be a valid email address");

        // Queue listener
        Queue::after(function () use ($qServ) {
            $qServ->queueIsComplete();
        });
    }
}