<?php

namespace Perimeterx;

class PerimeterxResetClient extends PerimeterxRiskClient
{
    const RESET_API_ENDPOINT = '/api/v1/risk/reset';

    // constants for possible reset reasons
    const RESET_REASON_CAPTCHA_SOLVED = 'captcha_solved';
    const RESET_REASON_CUSTOMER_SUPPORT = 'customer_support';
    const RESET_REASON_OTHER = 'other';

    /**
     * Send a risk reset request
     *
     * @param string $resetReason the reason a score reset is being requested. Should be a self::RESET_REASON_* const
     *
     * @return string the request response
     */
    public function sendResetRequest($resetReason)
    {
        $requestBody = [
            'request' => [
                'ip' => $this->pxCtx->getIp(),
                'headers' => $this->formatHeaders(),
                'uri' => $this->pxCtx->getUri(),
                'url' => $this->pxCtx->getFullUrl()
            ],
            'additional' => [
                'reset_reason' => $resetReason
            ]
        ];

        $vid = $this->pxCtx->getVid();
        if (isset($vid)) {
            $requestBody['vid'] = $vid;
        }

        $headers = [
            'Authorization' => 'Bearer ' . $this->pxConfig['auth_token'],
            'Content-Type' => 'application/json'
        ];

        $timeout = isset($this->pxConfig['api_reset_timeout']) ? $this->pxConfig['api_reset_timeout'] : $this->pxConfig['api_timeout'];
        $connect_timeout = isset($this->pxConfig['api_reset_connect_timeout']) ? $this->pxConfig['api_reset_connect_timeout'] : $this->pxConfig['api_connect_timeout'];
        $response = $this->httpClient->send(self::RESET_API_ENDPOINT, 'POST', $requestBody, $headers, $timeout, $connect_timeout);

        return $response;
    }
}
