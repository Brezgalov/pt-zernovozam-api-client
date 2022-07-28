<?php

namespace Brezgalov\ZernovozamApiClient;

use Brezgalov\BaseApiClient\BaseApiClient;
use yii\httpclient\Request;

class ZernovozamApiClient extends BaseApiClient
{
    const API_TOKEN_HEADER_NAME = 'AuthKey';

    /**
     * @var string
     */
    public $apiToken;

    /**
     * @var string
     */
    public $superToken;

    /**
     * @param string $phone
     * @return string|string[]|null
     */
    public function getClearPhoneNumber(string $phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * @param string $route
     * @param array $queryParams
     * @param Request|null $request
     * @return \yii\httpclient\Message|Request
     * @throws \yii\base\InvalidConfigException
     */
    public function prepareRequest(string $route, array $queryParams = [], Request $request = null)
    {
        return parent::prepareRequest($route, $queryParams, $request)->setHeaders([
            self::API_TOKEN_HEADER_NAME => $this->apiToken,
        ]);
    }

    /**
     * @param string $phone
     * @return \yii\httpclient\Message|\yii\httpclient\Request
     * @throws \yii\base\InvalidConfigException
     */
    public function authByPhoneAndSuperTokenRequest(string $phone)
    {
        $phone = $this->getClearPhoneNumber($phone);

        return $this->prepareRequest('auth')
            ->setMethod('POST')
            ->setData([
                "provider" => "credentials",
                "UserName" => "+{$phone}",
                "Password" => "1",
                "AccessToken" => $this->superToken,
            ]);
    }
}