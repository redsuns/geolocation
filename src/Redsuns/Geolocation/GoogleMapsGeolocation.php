<?php

namespace Redsuns\Geolocation;

use GuzzleHttp\Client as HttpClient;


class GoogleMapsGeolocation
{

    /**
     * @var string
     */
    private $endpoint = 'http://maps.googleapis.com/maps/api/geocode/';

    /**
     * @var string
     */
    private $output = 'json';

    /**
     * @var string
     */
    private $addresses = '';

    /**
     * @var array
     */
    private $latitudeAndLongitude = array();

    /**
     * @var array
     */
    private $lineBreakersFromEditor = array(
        '<br />', '<br>', '<br/>'
    );

    /**
     * Define o(s) endereço(s) para tratamento e obtenção de latitude e longitude
     *
     * <strong>Após este método, chamar o GoogleMapsGeolocation::andGetLatLng()</strong>
     *
     * @param $address
     * @return $this
     */
    public function setAddress($address)
    {
        $address = str_replace($this->lineBreakersFromEditor, '', $address);
        if( strpos($address, PHP_EOL) !==false ) {
            $address = explode(PHP_EOL, $address);
            $address = array_values(array_filter($address, 'strlen'));
        }

        $this->addresses = $address;

        return $this;
    }


    /**
     * Obtém a latitude e longitude dos endereços fornecidos
     *
     * @return array
     */
    public function andGetLatLng()
    {
        $this->parseLatLngFromAddress();

        return $this->latitudeAndLongitude;
    }

    /**
     * Obtém a latitude e longitude conforme cada endereço fornecido
     */
    private function parseLatLngFromAddress()
    {
        if( empty($this->addresses) ) {
            return;
        }

        $client = new HttpClient();

        foreach($this->addresses as $address) {
            $response = $client->get($this->endpoint . $this->output . '?address=' . rawurlencode($address) . '&sensor=false');

            $apiResponse = $response->json();
            $result = $apiResponse['results'][0]['geometry']['location'];

            $this->latitudeAndLongitude[] = array(
                'address' => $address,
                'lat' => $result['lat'],
                'lng' => $result['lng']
            );
        }

    }

} 