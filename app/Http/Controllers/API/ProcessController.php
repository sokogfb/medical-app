<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ProcessingGate;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

class ProcessController extends Controller
{
    use ProcessingGate;

    /**
     * @var Repository|Application|mixed
     */
    private $baseUri;

    /**
     * create controller instance
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->baseUri = config('api-medic.url.endpoint');

    }

    /**
     * test access token
     */
    public function token()
    {
        $this->baseUri = config('api-medic.url.auth_endpoint');

        return $this->getAccessToken();
    }

    /**
     * test access symptoms
     */
    public function symptoms()
    {

    }
}
