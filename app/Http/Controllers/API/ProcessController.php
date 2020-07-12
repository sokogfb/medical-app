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
        return json_decode($this->processRequest(config('api-medic.url.symptoms') . '?token=' . $this->getAccessToken() . '&language=en-gb'));
    }

    /**
     * test access symptoms
     * @param $symptoms
     * @return Exception|GuzzleException|string
     */
    public function fetchSymptom($symptoms)
    {
        return json_decode($this->processRequest(config('api-medic.url.symptoms') . '?token=' . $this->getAccessToken() . '&symptoms=[' . $symptoms . ']&language=en-gb'));
    }

    /**
     * test access diagnosis
     * @param $symptoms
     * @param string $gender
     * @param int $year_of_birth
     * @return Exception|GuzzleException|string
     */
    public function diagnosis($symptoms, string $gender, int $year_of_birth)
    {
        return $this->processRequest(config('api-medic.url.diagnosis') . '?token=' . $this->getAccessToken() . '&language=en-gb&symptoms=[44]&gender=male&year_of_birth=1993');

        return json_decode($this->processRequest(config('api-medic.url.diagnosis') . '?token=' . $this->getAccessToken() . '&language=en-gb&symptoms=[' . $symptoms . ']&gender=' . $gender . '&year_of_birth=' . $year_of_birth));
    }
}
