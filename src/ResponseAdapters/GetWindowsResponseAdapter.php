<?php

namespace Brezgalov\ZernovozamApiClient\ResponseAdapters;

use Brezgalov\BaseApiClient\ResponseAdapters\BaseResponseAdapter;
use Brezgalov\ZernovozamApiClient\Dto\ITimeslotDto;
use Brezgalov\ZernovozamApiClient\Dto\TimeslotWindowDto;
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
     * @return ITimeslotDto[]
     * @throws InvalidConfigException
     */
    public function getTimeslotsArray()
    {
        $windows = $this->responseData['Windows'] ?? [];

        $result = [];
        foreach ($windows as $window) {
            $result[] = \Yii::createObject(TimeslotWindowDto::class, [
                'config' => $window,
            ]);
        }

        return $result;
    }
}
