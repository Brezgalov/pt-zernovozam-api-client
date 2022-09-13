<?php

namespace Brezgalov\ZernovozamApiClient;

use Brezgalov\BaseApiClient\BaseApiClient;
use yii\base\InvalidConfigException;
use yii\httpclient\Message;
use yii\httpclient\Request;
use yii\web\Cookie;
use yii\web\CookieCollection;

class ZernovozamApiClient extends BaseApiClient
{
    const AUTH_COOKIE_NAME_SS_ID = 'ss-id';
    const AUTH_COOKIE_NAME_SS_PID = 'ss-pid';
    const AUTH_COOKIE_NAME_SS_OPT = 'ss-opt';

    const API_TOKEN_HEADER_NAME = 'AuthKey';

    /**
     * @var string
     */
    public $ssId;

    /**
     * @var string
     */
    public $ssPid;

    /**
     * @var string
     */
    public $ssOpt;

    /**
     * @var string
     */
    public $apiToken;

    /**
     * @var string
     */
    public $superToken;

    /**
     * @param string $value
     * @return ZernovozamApiClient
     */
    public function setSsId(string $value)
    {
        $this->ssId = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return ZernovozamApiClient
     */
    public function setSsPid(string $value)
    {
        $this->ssPid = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return ZernovozamApiClient
     */
    public function setSsOpt(string $value)
    {
        $this->ssOpt = $value;

        return $this;
    }

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
        return parent::prepareRequest($route, $queryParams, $request)
            ->setHeaders([
                self::API_TOKEN_HEADER_NAME => $this->apiToken,
            ]);
    }

    /**
     * @return CookieCollection
     */
    public function getAuthCookies()
    {
        return new CookieCollection([
            self::AUTH_COOKIE_NAME_SS_ID => \Yii::createObject([
                'class' => Cookie::class,
                'name' => self::AUTH_COOKIE_NAME_SS_ID,
                'value' => $this->ssId,
            ]),
            self::AUTH_COOKIE_NAME_SS_PID => \Yii::createObject([
                'class' => Cookie::class,
                'name' => self::AUTH_COOKIE_NAME_SS_PID,
                'value' => $this->ssPid,
            ]),
            self::AUTH_COOKIE_NAME_SS_OPT => \Yii::createObject([
                'class' => Cookie::class,
                'name' => self::AUTH_COOKIE_NAME_SS_OPT,
                'value' => $this->ssOpt,
            ]),
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

        return $this
            ->prepareRequest('auth')
            ->setMethod('POST')
            ->setData([
                "provider" => "credentials",
                "UserName" => "+{$phone}",
                "Password" => "1",
                "AccessToken" => $this->superToken,
            ]);
    }

    /**
     * @return Request
     * @throws InvalidConfigException
     */
    public function getTerminalsRequest()
    {
        return $this
            ->prepareRequest('json/reply/ActualStevedore')
            ->setMethod('GET')
            ->setCookies(
                $this->getAuthCookies()
            );
    }

    /**
     * @return Request
     * @throws InvalidConfigException
     */
    public function getTradersRequest()
    {
        return $this
            ->prepareRequest('json/reply/TraderV2Request')
            ->setMethod('POST')
            ->setCookies(
                $this->getAuthCookies()
            );
    }

    /**
     * @return Request
     * @throws InvalidConfigException
     */
    public function getCulturesRequest()
    {
        return $this->prepareRequest('json/reply/GetStevedoreCultures')
            ->setMethod('POST')
            ->setCookies(
                $this->getAuthCookies()
            );
    }
}