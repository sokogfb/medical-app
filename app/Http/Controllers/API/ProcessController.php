<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ProcessingGate;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
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
     * @return Exception|mixed
     */
    public function token()
    {
        return $this->getAccessToken();
    }

    /**
     * test access symptoms
     * @return Exception|GuzzleException|string
     */
    public function symptoms()
    {
        return $this->processRequest(config('api-medic.url.symptoms'), [
            'token' => $this->getAccessToken(),
        ]);
    }

    /**
     * test access diagnosis
     * @return Exception|GuzzleException|string
     */
    public function diagnosis()
    {
        return $this->processRequest(config('api-medic.url.diagnosis'), [
            'token' => $this->getAccessToken(),
            'symptoms' => [
                233
            ],
            'gender' => 'male',
            'year_of_birth' => 23
        ]);
    }
}
