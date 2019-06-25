<?php

namespace App\Http\Middleware;

use App\Services\DatabaseQueueService;
use Closure;

class Generating
{
    /**
     * @var DatabaseQueueService
     */
    private $qServ;

    public function __construct(DatabaseQueueService $qServ)
    {
        $this->qServ = $qServ;
    }

    /**
     * Checks if there's any generation going on right now and
     * redirects request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->qServ->generationInProgress()) {
            return redirect()->back()->withErrors(["Generation in progress"]);
        }

        return $next($request);
    }
}
