<?php

namespace Brezgalov\ZernovozamApiClient\ResponseAdapters;

use Brezgalov\BaseApiClient\ResponseAdapters\BaseResponseAdapter;
use Brezgalov\ZernovozamApiClient\Dto\WindowDto;
use Brezgalov\ZernovozamApiClient\ZernovozamApiClient;
use yii\base\InvalidConfigException;

class GetWindowsResponseAdapter extends BaseResponseAdapter
{
    /**
     * @return int
     */
    public function getApiResponseStatus()
    {
        return intval($this->responseData['Status'] ?? 0);
    }

    /**
     * @return string|null
     */
    public function getApiErrorMessage()
    {
        $status = $this->getApiResponseStatus();

        if ($status === ZernovozamApiClient::RESPONSE_STATUS_SUCCESS) {
            return null;
        }

        return ZernovozamApiClient::GET_WINDOW_ERRORS_DESCRIPTIONS[$status] ?? ZernovozamApiClient::ERROR_MESSAGE_UNKNOWN;
    }

    /**
     * @return WindowDto[]
     * @throws InvalidConfigException
     */
    public function getWidowsArray()
    {
        $windows = $this->responseData['Windows'] ?? [];

        $result = [];
        foreach ($windows as $window) {
            $result[] = \Yii::createObject(WindowDto::class, [
                'cultureId' => $window['CultureId'] ?? null,
                'time' => array_key_exists('Date', $window) ? strtotime($window['Date']) : null,
                'timeEnd' => array_key_exists('DateEnd', $window) ? strtotime($window['DateEnd']) : null,
                'timeStart' => array_key_exists('DateStart', $window) ? strtotime($window['DateStart']) : null,
                'timeslotId' => $window['Id'] ??  null,
                'IsArrived' => $window['IsArrived'] ?? null,
                'IsLate' => $window['IsLate'] ?? null,
                'IsOwnWindow' => $window['IsOwnWindow'] ?? null,
                'ReceiverId' => $window['ReceiverId'] ?? null,
                'ShowPhone' => $window['ShowPhone'] ?? null,
                'TraderId' => $window['TraderId'] ?? null,
                'ProviderId' => $window['ProviderId'] ?? null,
            ]);
        }

        return $result;
    }
}
