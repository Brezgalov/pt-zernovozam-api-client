<?php

namespace Brezgalov\ZernovozamApiClient\ResponseAdapters;

use Brezgalov\BaseApiClient\ResponseAdapters\BaseResponseAdapter;
use Brezgalov\ZernovozamApiClient\Dto\TimeslotDto;
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
     * @return TimeslotDto[]
     * @throws InvalidConfigException
     */
    public function getTimeslotsArray()
    {
        $windows = $this->responseData['Windows'] ?? [];

        $result = [];
        foreach ($windows as $window) {
            // list fixes formats issue
            list($window['Date']) = explode(' ', $window['Date'] ?? '');
            list(,$window['DateEnd']) = explode(' ', $window['DateEnd'] ?? '');
            list(,$window['DateStart']) = explode(' ', $window['DateStart'] ?? '');

            $result[] = \Yii::createObject(TimeslotDto::class, [
                'config' => $window,
            ]);
        }

        return $result;
    }
}
