<?php

namespace Perimeterx;


class PerimeterxActivitiesClient
{
    /**
     * @var object - perimeterx configuration object
     */
    private $pxConfig;

    /**
     * @var PerimeterxHttpClient
     */
    private $httpClient;

    /**
     * @param array $pxConfig - perimeterx configurations
     */
    public function __construct($pxConfig)
    {
        $this->pxConfig = $pxConfig;
        $this->httpClient = $pxConfig['http_client'];
    }

    /**
     * @param PerimeterxContext $pxCtx
     * @return array
     */
    private function filterSensitiveHeaders($pxCtx)
    {
        $retval = [];
        foreach ($pxCtx->getHeaders() as $key => $value) {
            if (isset($key, $value) and !in_array($key, $this->pxConfig['sensitive_headers'])) {
                $retval[$key] = $value;
            }
        }
        return $retval;
    }

    /**
     * @param $activityType
     * @param PerimeterxContext $pxCtx
     * @param $details
     */
    public function sendToPerimeterx($activityType, $pxCtx, $details = [])
    {
        if (isset($this->pxConfig['additional_activity_handler'])) {
            $this->pxConfig['additional_activity_handler']($activityType, $pxCtx, $details);
        }

        // Optionally defer sending activity until the shutdown handler to reduce client wait time
        if (!empty($this->pxConfig['defer_page_activity_send'])) {
            register_shutdown_function([$this, 'performSendToPerimeterx'], $activityType, $pxCtx, $details);
        } else {
            $this->performSendToPerimeterx($activityType, $pxCtx, $details);
        }
    }

    /**
     * perform the actual activity send
     *
     * @param $activityType
     * @param PerimeterxContext $pxCtx
     * @param $details
     */
    public function performSendToPerimeterx($activityType, $pxCtx, $details = [])
    {

        $details['cookie_origin'] = $pxCtx->getCookieOrigin();

        $details['module_version'] = $this->pxConfig['sdk_name'];
        $pxData = [];
        $pxData['type'] = $activityType;
        $pxData['headers'] = $this->filterSensitiveHeaders($pxCtx);
        $pxData['timestamp'] = time();
        $pxData['socket_ip'] = $pxCtx->getIp();
        $pxData['px_app_id'] = $this->pxConfig['app_id'];
        $pxData['url'] = $pxCtx->getFullUrl();
        $pxData['details'] = $details;
        $vid = $pxCtx->getVid();

        if (isset($vid)) {
            $pxData['vid'] = $vid;
        }

        $activities = [$pxData];
        $headers = [
            'Authorization' => 'Bearer ' . $this->pxConfig['auth_token'],
            'Content-Type' => 'application/json'
        ];

        $timeout = isset($this->pxConfig['api_page_activity_timeout']) ? $this->pxConfig['api_page_activity_timeout'] : 0;
        $connect_timeout = isset($this->pxConfig['api_page_activity_connect_timeout']) ? $this->pxConfig['api_page_activity_connect_timeout'] : 0;
        $this->httpClient->send('/api/v1/collector/s2s', 'POST', $activities, $headers, $timeout, $connect_timeout);
    }

    /**
     * @param PerimeterxContext $pxCtx
     */
    public function sendBlockActivity($pxCtx)
    {
        if (!$this->pxConfig['send_page_activities']) {
            return;
        }

        $details = [];
        $details['block_uuid'] = $pxCtx->getUuid();
        $details['block_score'] = $pxCtx->getScore();
        $details['block_reason'] = $pxCtx->getBlockReason();
        $details['risk_rtt'] = $pxCtx->getRiskRtt();

        $this->sendToPerimeterx('block', $pxCtx, $details);
    }

    /**
     * @param PerimeterxContext $pxCtx
     */
    public function sendPageRequestedActivity($pxCtx)
    {
        if (!$this->pxConfig['send_page_activities']) {
            return;
        }

        $details = [];
        $details['client_uuid'] = $pxCtx->getUuid();
        $details['module_version'] = $this->pxConfig['sdk_name'];
        $details['http_version'] = $pxCtx->getHttpVersion();
        $details['http_method'] = $pxCtx->getHttpMethod();
        $details['pass_reason'] = $pxCtx->getPassReason();
        $details['risk_rtt'] = $pxCtx->getRiskRtt();

        if ($pxCtx->getDecodedCookie()) {
            $details['px_cookie'] = $pxCtx->getDecodedCookie();
        }

        if ($pxCtx->getCookieHmac()) {
            $details['px_cookie_hmac'] = $pxCtx->getCookieHmac();
        }
        $this->sendToPerimeterx('page_requested', $pxCtx, $details);
    }
}
