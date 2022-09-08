<?php

namespace Brezgalov\ZernovozamApiClient;

use Brezgalov\BaseApiClient\BaseApiClient;
use yii\base\InvalidConfigException;
use yii\httpclient\Message;
use yii\httpclient\Request;

class ZernovozamApiClient extends BaseApiClient
{
    const AUTH_COOKIE_NAME_SS_ID = 'ss-id';
    const AUTH_COOKIE_NAME_SS_PID = 'ss-pid';
    const AUTH_COOKIE_NAME_SS_OPT = 'ss-opt';

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
     * @return Message|Request
     * @throws InvalidConfigException
     */
    public function prepareRequest(string $route, array $queryParams = [], Request $request = null)
    {
        return parent::prepareRequest($route, $queryParams, $request)->setHeaders([
            self::API_TOKEN_HEADER_NAME => $this->apiToken,
        ]);
    }

    /**
     * @param string $phone
     * @return Message|Request
     * @throws InvalidConfigException
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