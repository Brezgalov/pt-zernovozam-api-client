<?php

namespace Brezgalov\ZernovozamApiClient;

use Brezgalov\BaseApiClient\BaseApiClient;
use Brezgalov\ZernovozamApiClient\RequestBodies\ConfirmWindowsRequestBody;
use Brezgalov\ZernovozamApiClient\RequestBodies\GetWindowRequestBody;
use Brezgalov\ZernovozamApiClient\ResponseAdapters\ConfirmTimeslotsResponseAdapter;
use Brezgalov\ZernovozamApiClient\ResponseAdapters\DeleteTimeslotsResponseAdapter;
use Brezgalov\ZernovozamApiClient\ResponseAdapters\GetWindowsResponseAdapter;
use Brezgalov\ZernovozamApiClient\ResponseAdapters\MyTimeslotsCollection;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
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
    const URL_GET_MY_TIMESLOTS = 'json/reply/GetMyTimeslots';
    const URL_DELETE_TIMESLOTS = 'json/reply/DeleteWindowsRequestV2';

    const RESPONSE_STATUS_SUCCESS = 1;

    const GET_WINDOW_RESPONSE_STATUS_ERROR_UNKNOWN = 0;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_WRONG_TRUCK_COUNT = 2;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_RECEIVING_HALTED = 3;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_LIMITS_EXHAUSTED = 4;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_UNKNOWN_STEVEDORE = 5;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_CONFINE_EXHAUSTED = 6;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_CULTURE_LIMITS_EXHAUSTED = 7;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_STEVEDORE_NOT_TAKE_FOR_THIS_TRADER = 8;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_WRONG_CHECKOUT_DATE = 9;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_BLOCKED = 10;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_QUOTE_IS_ZERO = 11;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_CONNECTION_PROBLEM = 12;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_WRONG_PHONE = 13;
    const GET_WINDOW_RESPONSE_STATUS_ERROR_BALANCE_END = 14;

    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_UNKNOWN = 0;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TIMESLOT_NOT_FOUND = 2;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_PLATE_TRUCK_WRONG = 3;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TIMESLOT_TIME_WRONG = 4;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_BUSY = 5;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_IS_BANNED = 6;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_NO_GLONASS = 7;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_NO_ACCREDITATION = 8;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_GLONASS_INACTIVE = 9;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_IN_TOWN = 10;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_NO_MONEY = 11;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_GLONASS_PROVIDER_BLOCKED_YOUR_DEVICE = 12;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_PHONE_NOT_SET = 13;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_CONFIRM_FORMAT_WRONG = 14;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_DATETIME_FORMAT_WRONG = 15;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_ONLY_TRUCK_OWNER_CAN_SUBMIT = 16;
    const CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_ON_NAT = 17;

    const DELETE_WINDOWS_RESPONSE_STATUS_ERROR_UNKNOWN = 0;
    const DELETE_WINDOWS_RESPONSE_STATUS_ERROR_PLATE_TRUCK_WRONG = 2;
    const DELETE_WINDOWS_RESPONSE_STATUS_ERROR_TOO_EARLY = 3;
    const DELETE_WINDOWS_RESPONSE_STATUS_ERROR_TOO_LATE = 4;
    const DELETE_WINDOWS_RESPONSE_STATUS_ERROR_WINDOW_OWNER_MISMATCH = 5;
    const DELETE_WINDOWS_RESPONSE_STATUS_ERROR_DELETE_QUOTA_PASSED_OUT = 6;

    const ERROR_MESSAGE_UNKNOWN = "Неизвестная ошибка";

    const GET_WINDOW_ERROR_MESSAGE_WRONG_TRUCK_COUNT = "Указано количество автомобилей вне разрешенного предела";
    const GET_WINDOW_ERROR_MESSAGE_RECEIVING_HALTED = "Стивидор приостановил выдачу таймслотов";
    const GET_WINDOW_ERROR_MESSAGE_LIMITS_EXHAUSTED = "Лимиты выбраны на ближайшие доступные даты";
    const GET_WINDOW_ERROR_MESSAGE_UNKNOWN_STEVEDORE = "Указан несуществующий стивидор";
    const GET_WINDOW_ERROR_MESSAGE_CONFINE_EXHAUSTED = "Лимиты выбраны на культуру для данного экспортера на ближайшие доступные даты";
    const GET_WINDOW_ERROR_MESSAGE_CULTURE_LIMITS_EXHAUSTED = "Лимиты выбраны на прием данной культуры на ближайшие доступные даты";
    const GET_WINDOW_ERROR_MESSAGE_STEVEDORE_NOT_TAKE_FOR_THIS_TRADER = "Указанный стивидор не принимает данного экспортера";
    const GET_WINDOW_ERROR_MESSAGE_WRONG_CHECKOUT_DATE = "Указана дата таймслота вне разрешенного предела";
    const GET_WINDOW_ERROR_MESSAGE_BLOCKED = "Телефон клиента заблокирован";
    const GET_WINDOW_ERROR_MESSAGE_QUOTE_IS_ZERO = "Указанный экспортер не имеет доступных квот";
    const GET_WINDOW_ERROR_MESSAGE_CONNECTION_PROBLEM = "Не удалось подключиться к сервису выдачи таймслотов";
    const GET_WINDOW_ERROR_MESSAGE_WRONG_PHONE = "Неверно указан телефон";
    const GET_WINDOW_ERROR_MESSAGE_BALANCE_END = "Баланс пользователя не позволяет получить таймслот";

    const CONFIRM_WINDOWS_ERROR_MESSAGE_TIMESLOT_NOT_FOUND = "Таймслот не найден";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_PLATE_TRUCK_WRONG = "Неверно указан номер автомобиля";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_TIMESLOT_TIME_WRONG = "Неверно указано время таймслота";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_BUSY = "Машина уже в рейсе";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_IS_BANNED = "Машина находится в черном списке";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_NO_GLONASS = "Машина не в глонассе";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_NO_ACCREDITATION = "Машина не аккредитована";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_GLONASS_INACTIVE = "Устройство глонасс не работает";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_IN_TOWN = "Запрещено получать таймслоты машинам находящимся в городе";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_NO_MONEY = "Недостаточно средств на счету";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_GLONASS_PROVIDER_BLOCKED_YOUR_DEVICE = "Услуга заблокирована установщиком Глонасс";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_PHONE_NOT_SET = "Не указан номер";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_CONFIRM_FORMAT_WRONG = "Неверный формат подтверждения";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_DATETIME_FORMAT_WRONG = "Неверный формат даты или времени";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_ONLY_TRUCK_OWNER_CAN_SUBMIT = "Только владелец машины может получать таймслоты";
    const CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_ON_NAT = "Машина находится на НАТ";

    const DELETE_WINDOWS_ERROR_MESSAGE_PLATE_TRUCK_WRONG = 'Неверно указан номер автомобиля';
    const DELETE_WINDOWS_ERROR_MESSAGE_TOO_EARLY = 'Рано удалять окно';
    const DELETE_WINDOWS_ERROR_MESSAGE_TOO_LATE = 'Поздно удалять окно';
    const DELETE_WINDOWS_ERROR_MESSAGE_WINDOW_OWNER_MISMATCH = 'Только создатель окна или владелец машины может его удалить';
    const DELETE_WINDOWS_ERROR_MESSAGE_DELETE_QUOTA_PASSED_OUT = 'Достиг лимита разрешенных удалений';

    const GET_WINDOW_ERRORS_DESCRIPTIONS = [
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_UNKNOWN => self::ERROR_MESSAGE_UNKNOWN,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_WRONG_TRUCK_COUNT => self::GET_WINDOW_ERROR_MESSAGE_WRONG_TRUCK_COUNT,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_RECEIVING_HALTED => self::GET_WINDOW_ERROR_MESSAGE_RECEIVING_HALTED,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_LIMITS_EXHAUSTED => self::GET_WINDOW_ERROR_MESSAGE_LIMITS_EXHAUSTED,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_UNKNOWN_STEVEDORE => self::GET_WINDOW_ERROR_MESSAGE_UNKNOWN_STEVEDORE,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_CONFINE_EXHAUSTED => self::GET_WINDOW_ERROR_MESSAGE_CONFINE_EXHAUSTED,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_CULTURE_LIMITS_EXHAUSTED => self::GET_WINDOW_ERROR_MESSAGE_CULTURE_LIMITS_EXHAUSTED,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_STEVEDORE_NOT_TAKE_FOR_THIS_TRADER => self::GET_WINDOW_ERROR_MESSAGE_STEVEDORE_NOT_TAKE_FOR_THIS_TRADER,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_WRONG_CHECKOUT_DATE => self::GET_WINDOW_ERROR_MESSAGE_WRONG_CHECKOUT_DATE,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_BLOCKED => self::GET_WINDOW_ERROR_MESSAGE_BLOCKED,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_QUOTE_IS_ZERO => self::GET_WINDOW_ERROR_MESSAGE_QUOTE_IS_ZERO,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_CONNECTION_PROBLEM => self::GET_WINDOW_ERROR_MESSAGE_CONNECTION_PROBLEM,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_WRONG_PHONE => self::GET_WINDOW_ERROR_MESSAGE_WRONG_PHONE,
        self::GET_WINDOW_RESPONSE_STATUS_ERROR_BALANCE_END => self::GET_WINDOW_ERROR_MESSAGE_BALANCE_END,
    ];

    const GET_CONFIRM_WINDOWS_ERRORS_DESCRIPTIONS = [
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_UNKNOWN => self::ERROR_MESSAGE_UNKNOWN,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TIMESLOT_NOT_FOUND => self::CONFIRM_WINDOWS_ERROR_MESSAGE_TIMESLOT_NOT_FOUND,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_PLATE_TRUCK_WRONG => self::CONFIRM_WINDOWS_ERROR_MESSAGE_PLATE_TRUCK_WRONG,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TIMESLOT_TIME_WRONG => self::CONFIRM_WINDOWS_ERROR_MESSAGE_TIMESLOT_TIME_WRONG,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_BUSY => self::CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_BUSY,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_IS_BANNED => self::CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_IS_BANNED,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_NO_GLONASS => self::CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_NO_GLONASS,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_NO_ACCREDITATION => self::CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_NO_ACCREDITATION,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_GLONASS_INACTIVE => self::CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_GLONASS_INACTIVE,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_IN_TOWN => self::CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_IN_TOWN,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_NO_MONEY => self::CONFIRM_WINDOWS_ERROR_MESSAGE_NO_MONEY,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_GLONASS_PROVIDER_BLOCKED_YOUR_DEVICE => self::CONFIRM_WINDOWS_ERROR_MESSAGE_GLONASS_PROVIDER_BLOCKED_YOUR_DEVICE,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_PHONE_NOT_SET => self::CONFIRM_WINDOWS_ERROR_MESSAGE_PHONE_NOT_SET,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_CONFIRM_FORMAT_WRONG => self::CONFIRM_WINDOWS_ERROR_MESSAGE_CONFIRM_FORMAT_WRONG,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_DATETIME_FORMAT_WRONG => self::CONFIRM_WINDOWS_ERROR_MESSAGE_DATETIME_FORMAT_WRONG,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_ONLY_TRUCK_OWNER_CAN_SUBMIT => self::CONFIRM_WINDOWS_ERROR_MESSAGE_ONLY_TRUCK_OWNER_CAN_SUBMIT,
        self::CONFIRM_WINDOWS_RESPONSE_STATUS_ERROR_TRUCK_ON_NAT => self::CONFIRM_WINDOWS_ERROR_MESSAGE_TRUCK_ON_NAT,
    ];

    const DELETE_WINDOWS_ERRORS_DESCRIPTIONS = [
        self::DELETE_WINDOWS_RESPONSE_STATUS_ERROR_UNKNOWN => self::ERROR_MESSAGE_UNKNOWN,
        self::DELETE_WINDOWS_RESPONSE_STATUS_ERROR_PLATE_TRUCK_WRONG => self::DELETE_WINDOWS_ERROR_MESSAGE_PLATE_TRUCK_WRONG,
        self::DELETE_WINDOWS_RESPONSE_STATUS_ERROR_TOO_EARLY => self::DELETE_WINDOWS_ERROR_MESSAGE_TOO_EARLY,
        self::DELETE_WINDOWS_RESPONSE_STATUS_ERROR_TOO_LATE => self::DELETE_WINDOWS_ERROR_MESSAGE_TOO_LATE,
        self::DELETE_WINDOWS_RESPONSE_STATUS_ERROR_WINDOW_OWNER_MISMATCH => self::DELETE_WINDOWS_ERROR_MESSAGE_WINDOW_OWNER_MISMATCH,
        self::DELETE_WINDOWS_RESPONSE_STATUS_ERROR_DELETE_QUOTA_PASSED_OUT => self::DELETE_WINDOWS_ERROR_MESSAGE_DELETE_QUOTA_PASSED_OUT,
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
     * @return GetWindowsResponseAdapter
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function requestWindows(GetWindowRequestBody $requestBody)
    {
        $request = $this->prepareRequest(self::URL_GET_WINDOWS)
            ->setMethod('POST')
            ->setFormat(Client::FORMAT_JSON)
            ->setData(
                $requestBody->getBody()
            )
            ->setCookies(
                $this->getAuthCookies()
            );

        return \Yii::createObject(GetWindowsResponseAdapter::class, [
            'request' => $request,
            'response' => $request->send(),
        ]);
    }

    /**
     * @param ConfirmWindowsRequestBody $requestBody
     * @return ConfirmTimeslotsResponseAdapter
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function confirmWindows(ConfirmWindowsRequestBody $requestBody)
    {
        $request = $this->prepareRequest(self::URL_CONFIRM_TIMESLOTS)
            ->setMethod('POST')
            ->setFormat(Client::FORMAT_JSON)
            ->setData(
                $requestBody->getBody()
            )
            ->setCookies(
                $this->getAuthCookies()
            );

        return \Yii::createObject(ConfirmTimeslotsResponseAdapter::class, [
            'request' => $request,
            'response' => $request->send(),
        ]);
    }

    /**
     * @return MyTimeslotsCollection
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function getMyTimeslots()
    {
        $request = $this->prepareRequest(self::URL_GET_MY_TIMESLOTS)
            ->setMethod('POST')
            ->setCookies(
                $this->getAuthCookies()
            );

        return \Yii::createObject(MyTimeslotsCollection::class, [
            'request' => $request,
            'response' => $request->send(),
        ]);
    }

    /**
     * @param array $timeslotsIds
     * @return DeleteTimeslotsResponseAdapter
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function deleteTimeslots(array $timeslotsIds)
    {
        $request = $this->prepareRequest(self::URL_DELETE_TIMESLOTS)
            ->setMethod('POST')
            ->setFormat(Client::FORMAT_JSON)
            ->setCookies(
                $this->getAuthCookies()
            )
            ->setData([
                'WindowIds' => $timeslotsIds
            ]);

        return new DeleteTimeslotsResponseAdapter($request, $request->send());
    }
}
