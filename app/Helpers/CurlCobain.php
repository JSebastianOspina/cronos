<?php

namespace App\Helpers;

class CurlCobain
{
    public $url;
    public $finalUrl;
    public $method;
    public $data;
    public $queryParams;
    public $headers;
    public $cookies;
    public $requireSSL = false;
    private $ch;

    /**
     * CurlCobain constructor.
     * @param $url
     * @param $method
     */
    public function __construct($url, $method = 'GET')
    {
        $this->ch = curl_init();
        $this->url = $url;
        $this->method = $method;
        $this->basicSetUp();

    }


    private function basicSetUp(): void
    {
        $this->setCurlOption(CURLOPT_URL, $this->url);
        $this->setCurlOption(CURLOPT_POST, $this->method === 'POST');
        $this->setCurlOption(CURLOPT_RETURNTRANSFER, true); //Get text instead of void
        $this->setCurlOption(CURLOPT_SSL_VERIFYHOST, $this->requireSSL);
        $this->setCurlOption(CURLOPT_SSL_VERIFYPEER, $this->requireSSL);

    }

    public function setCurlOption($option, $value): void
    {
        curl_setopt($this->ch, $option, $value);
    }

    public function setQueryParam(string $fieldName, string $value)
    {
        $this->queryParams[$fieldName] = $value;

        $this->buildUrl();
    }

    private function buildUrl()
    {
        if (count($this->queryParams) === 0) {
            $this->finalUrl = $this->url;
        } else {
            $this->finalUrl = $this->url . '?' . http_build_query($this->queryParams);
        }
        $this->setCurlOption(CURLOPT_URL, $this->finalUrl);
    }

    public function setHeadersAsArray(array $headers): void
    {
        $this->headers[] = $headers;
        $this->setCurlOption(CURLOPT_HTTPHEADER, $this->headers);
    }

    public function makeRequest()
    {
        $resp = curl_exec($this->ch);
        curl_close($this->ch);
        return $resp;
    }

    public function enableSSL()
    {
        $this->requireSSL = true;
        $this->setCurlOption(CURLOPT_SSL_VERIFYPEER, $this->requireSSL);
    }

    public function disableSSL()
    {
        $this->requireSSL = false;
        $this->setCurlOption(CURLOPT_SSL_VERIFYPEER, $this->requireSSL);
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
        $this->setCurlOption(CURLOPT_POST, $this->method === 'POST');
    }

    public function setDataAsJson(array $data)
    {
        $this->setCurlOption(CURLOPT_POSTFIELDS, json_encode($data));
        $this->setHeader('content-type', 'application/json');
    }

    public function setHeader(string $headerName, string $headerValue): void
    {
        $this->headers[] = $headerName . ': ' . $headerValue;
        $this->setCurlOption(CURLOPT_HTTPHEADER, $this->headers);
    }


}
