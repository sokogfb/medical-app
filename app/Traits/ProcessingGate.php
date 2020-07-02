<?php


namespace App\Traits;


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
        $options = [
            'header' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . config('api-medic.keys.live.username') . ':' . hash_hmac('md5', config('api-medic.keys.live.password'), 'secret')
            ]
        ];

        $response = $this->processRequest(config('api-medic.url.token'), $options, 'POST', true);
        dd($response);
    }

    /**
     * ---------------------------
     * cache access token here
     * @return mixed
     * --------------------------
     */
    private function cacheAccessToken()
    {
        if (Cache::has('sms_access_token'))
            return Cache::get('sms_access_token');
        return Cache::rememberForever('sms_access_token', function () {
            return config('sms.keys.token');
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
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->cacheAccessToken(),
            ],
            'json' => $data,
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
