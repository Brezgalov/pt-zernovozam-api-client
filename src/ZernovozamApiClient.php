<?php

namespace Brezgalov\ZernovozamApiClient;

use Brezgalov\BaseApiClient\BaseApiClient;
use Brezgalov\ZernovozamApiClient\RequestBodies\ConfirmWindowsRequestBody;
use Brezgalov\ZernovozamApiClient\RequestBodies\GetWindowRequestBody;
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

    const URL_AUTH = 'auth';
    const URL_ACTUAL_STEVEDORE = 'json/reply/ActualStevedore';
    const URL_TRADER_REQUEST = 'json/reply/TraderV2Request';
    const URL_GET_STEVEDORE_CULTURES = 'json/reply/GetStevedoreCultures';
    const URL_GET_WINDOWS = 'json/reply/GetWindows';
    const URL_CONFIRM_TIMESLOTS = 'json/reply/ConfirmTimeslots';

    const RESPONSE_STATUS_ERROR_UNKNOWN = 0;
    const RESPONSE_STATUS_SUCCESS = 1;
    const RESPONSE_STATUS_ERROR_WRONG_TRUCK_COUNT = 2;
    const RESPONSE_STATUS_ERROR_RECEIVING_HALTED = 3;
    const RESPONSE_STATUS_ERROR_LIMITS_EXHAUSTED = 4;
    const RESPONSE_STATUS_ERROR_UNKNOWN_STEVEDORE = 5;
    const RESPONSE_STATUS_ERROR_CONFINE_EXHAUSTED = 6;
    const RESPONSE_STATUS_ERROR_CULTURE_LIMITS_EXHAUSTED = 7;
    const RESPONSE_STATUS_ERROR_STEVEDORE_NOT_TAKE_FOR_THIS_TRADER = 8;
    const RESPONSE_STATUS_ERROR_WRONG_CHECKOUT_DATE = 9;
    const RESPONSE_STATUS_ERROR_BLOCKED = 10;
    const RESPONSE_STATUS_ERROR_QUOTE_IS_ZERO = 11;
    const RESPONSE_STATUS_ERROR_CONNECTION_PROBLEM = 12;
    const RESPONSE_STATUS_ERROR_WRONG_PHONE = 13;
    const RESPONSE_STATUS_ERROR_BALANCE_END = 14;

    const ERRORS_DESCRIPTIONS = [
        self::RESPONSE_STATUS_ERROR_UNKNOWN => "Неопределённый статус",
        self::RESPONSE_STATUS_ERROR_WRONG_TRUCK_COUNT => "Указано количество автомобилей вне разрешенного предела",
        self::RESPONSE_STATUS_ERROR_RECEIVING_HALTED => "Стивидор приостановил выдачу таймслотов",
        self::RESPONSE_STATUS_ERROR_LIMITS_EXHAUSTED => "Лимиты выбраны на ближайшие доступные даты",
        self::RESPONSE_STATUS_ERROR_UNKNOWN_STEVEDORE => "Указан несуществующий стивидор",
        self::RESPONSE_STATUS_ERROR_CONFINE_EXHAUSTED => "Лимиты выбраны на культуру для данного экспортера на ближайшие доступные даты",
        self::RESPONSE_STATUS_ERROR_CULTURE_LIMITS_EXHAUSTED => "Лимиты выбраны на прием данной культуры на ближайшие доступные даты",
        self::RESPONSE_STATUS_ERROR_STEVEDORE_NOT_TAKE_FOR_THIS_TRADER => "Указанный стивидор не принимает данного экспортера",
        self::RESPONSE_STATUS_ERROR_WRONG_CHECKOUT_DATE => "Указана дата таймслота вне разрешенного предела",
        self::RESPONSE_STATUS_ERROR_BLOCKED => "Телефон клиента заблокирован",
        self::RESPONSE_STATUS_ERROR_QUOTE_IS_ZERO => "Указанный экспортер не имеет доступных квот",
        self::RESPONSE_STATUS_ERROR_CONNECTION_PROBLEM => "Не удалось подключиться к сервису выдачи таймслотов",
        self::RESPONSE_STATUS_ERROR_WRONG_PHONE => "Неверно указан телефон",
        self::RESPONSE_STATUS_ERROR_BALANCE_END => "Балланс пользователя не позволяет получить таймслот",
    ];

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
     * @param bool $rememberMe
     * @return Message|Request
     * @throws InvalidConfigException
     */
    public function authByPhoneAndSuperTokenRequest(string $phone, bool $rememberMe = false)
    {
        $phone = $this->getClearPhoneNumber($phone);

        return $this
            ->prepareRequest(self::URL_AUTH)
            ->setMethod('POST')
            ->setData([
                "provider" => "credentials",
                "UserName" => "+{$phone}",
                "Password" => "1",
                "AccessToken" => $this->superToken,
                "RememberMe" => $rememberMe,
            ]);
    }

    /**
     * @return Request
     * @throws InvalidConfigException
     */
    public function getTerminalsRequest()
    {
        return $this
            ->prepareRequest(self::URL_ACTUAL_STEVEDORE)
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
            ->prepareRequest(self::URL_TRADER_REQUEST)
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
        return $this->prepareRequest(self::URL_GET_STEVEDORE_CULTURES)
            ->setMethod('POST')
            ->setCookies(
                $this->getAuthCookies()
            );
    }

    /**
     * @param GetWindowRequestBody $requestBody
     * @return Message|Request
     * @throws InvalidConfigException
     */
    public function getWindowsRequest(GetWindowRequestBody $requestBody)
    {
        return $this->prepareRequest(self::URL_GET_WINDOWS)
            ->setMethod('POST')
            ->setData(
                $requestBody->getBody()
            )
            ->setCookies(
                $this->getAuthCookies()
            );
    }

    /**
     * @param ConfirmWindowsRequestBody $requestBody
     * @return Message|Request
     * @throws InvalidConfigException
     */
    public function getConfirmWindowRequest(ConfirmWindowsRequestBody $requestBody)
    {
        return $this->prepareRequest(self::URL_CONFIRM_TIMESLOTS)
            ->setMethod('POST')
            ->setData(
                $requestBody->getBody()
            )
            ->setCookies(
                $this->getAuthCookies()
            );
    }
}