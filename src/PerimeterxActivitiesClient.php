<?php

namespace Perimeterx;


class PerimeterxActivitiesClient
{
    /**
    * @var int - perimeterx send activities synchronize mode
    */
    const SYNC_MODE = 1;

    /**
    * @var int - perimeterx send activities asynchronize mode
    */
    const ASYNC_MODE = 2;

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
        $this->queueClient = new PerimeterxQueueClient($pxConfig);
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
    public function sendToPerimeterx($activityType, $pxCtx, $details = [], $send_mode = PerimeterxActivitiesClient::SYNC_MODE)
    {
        if (isset($this->pxConfig['additional_activity_handler'])) {
            $this->pxConfig['additional_activity_handler']($activityType, $pxCtx, $details);
        }

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

        
        if(PerimeterxActivitiesClient::ASYNC_MODE == $send_mode) {
            $payload = array(
                "http" => array(
                    "method" => "POST",
                    "path" => "/api/v1/collector/s2s",
                    "headers" => $headers,
                ),
                "activities" => $activities
            );

            $this->queueClient->send($payload);
        }else {
            $this->httpClient->send('/api/v1/collector/s2s', 'POST', $activities, $headers, $this->pxConfig['api_timeout'], $this->pxConfig['api_connect_timeout']);    
        }  
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
        $details['module_version'] = $this->pxConfig['sdk_name'];
        $details['http_version'] = $pxCtx->getHttpVersion();
        $details['http_method'] = $pxCtx->getHttpMethod();
        if ($pxCtx->getDecodedCookie()) {
            $details['px_cookie'] = $pxCtx->getDecodedCookie();
        }

        $this->sendToPerimeterx('page_requested', $pxCtx, $details, PerimeterxActivitiesClient::ASYNC_MODE);
    }
}
