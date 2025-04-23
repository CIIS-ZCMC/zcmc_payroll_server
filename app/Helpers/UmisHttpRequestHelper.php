<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class UmisHttpRequestHelper
{
    private static function __clientInstance()
    {
        return new Client(['base_uri' => env('UMIS'), 'timeout' => 30]);
    }

    private static function __constructHeader()
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.env('UMIS_API_KEY')
        ];
    }

    private static function __processRequestResponse($response)
    {
        $status =  $response->getStatusCode();
        $data = json_decode($response->getBody(), true);

        if(!($status >= 200 & $status < 300)){
            return [
                'is_failed' => true,
                'message' => $data['message']
            ];
        }

        return [
            'is_failed' => false,
            'data' => $data,
        ];
    }

    public static function post($end_point, $data)
    {
        $client = self::__clientInstance();

        $response = $client->post('/' . $end_point,[
            'json' => json_encode($data),
            'headers' => self::__constructHeader()
        ]);

        return self::__processRequestResponse($response);
    }

    public static function get($api)
    {
        $client = self::__clientInstance();

        $response = $client->get('/' . $api, [
            'headers' => self::__constructHeader()
        ]);

        return self::__processRequestResponse($response);
    }
}
