<?php


namespace App\Traits;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait ProcessingGate
{
    /**
     * get the access token
     */
    public function getAccessToken()
    {
        try {
            $options = [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . config('api-medic.keys.live.username') . ':' . base64_encode(hash_hmac('md5', config('api-medic.url.auth_endpoint'), config('api-medic.keys.live.password'), true))
                ]
            ];

            if (Cache::has('api_access_token'))
                return Cache::get('api_access_token');

            // send request and cache token
            $response = json_decode($this->processRequest(config('api-medic.url.auth_endpoint'), $options, 'POST', true));
            return $this->cacheAccessToken($response->Token, $response->ValidThrough);
        } catch (Exception $exception) {
            Log::critical('token-exception' . $exception->getMessage());
            return $exception;
        }
    }

    /**
     * ---------------------------
     * cache access token here
     * @param string $token
     * @param int $time_to_live
     * @return mixed
     * --------------------------
     */
    private function cacheAccessToken(string $token, int $time_to_live)
    {
        return Cache::remember('api_access_token', now()->addSeconds(($time_to_live - 10)), function () use ($token) {
            return $token;
        });
    }


    /**
     * -------------------------
     * create header details
     * for any request
     * -------------------------
     * @param array $data
     * @return array[]
     */
    private function setRequestOptions(array $data)
    {
        return [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => $data,
        ];
    }

    /**
     * ---------------------------------
     * process the request
     * @param string $requestUrl
     * @param array $data
     * @param string $method
     * @param bool $token
     * @return string
     * ---------------------------------
     */
    public function processRequest(string $requestUrl, $data = [], string $method = 'GET', bool $token = false)
    {
        try {
            // define the guzzle client
            $client = new Client([
                'base_uri' => $this->baseUri,
                'timeout' => config('api-medic.timeout'),
                'connect_timeout' => config('api-medic.connect_timeout'),
                'protocols' => ['http', 'https'],
            ]);

            if ($token) {
                $response = $client->request($method, $requestUrl, $data);
            } else {
                $response = $client->request($method, $requestUrl, $this->setRequestOptions($data));
            }
            return ($response->getBody()->getContents());

        } catch (ClientException $clientException) {
            $exception = $clientException->getResponse()->getBody()->getContents();
            Log::critical('client-exception' . $clientException->getMessage());
            return $exception;
        } catch (ServerException $serverException) {
            $exception = $serverException->getResponse()->getBody()->getContents();
            Log::critical('server-exception' . $serverException->getMessage());
            return $exception;
        } catch (GuzzleException $guzzleException) {
            Log::critical('guzzle-exception' . $guzzleException->getMessage());
            return $guzzleException;
        }
    }

}
