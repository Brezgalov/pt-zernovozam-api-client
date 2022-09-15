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

        return ZernovozamApiClient::ERRORS_DESCRIPTIONS[$status] ?? ZernovozamApiClient::ERROR_MESSAGE_UNKNOWN;
    }

    /**
     * @return WindowDto[]
     * @throws InvalidConfigException
     */
    public function getWidowsArray()
    {
        $windows = $this->responseData['Windows'] ?? [];

        $result = [];
        foreach ($windows as $timeslotId => $windowTime) {
            list($part1) = (explode('-', $windowTime));
            $windowTimeUnix = mb_ereg_replace('[^0-9]', '', $part1);

            $result[] = \Yii::createObject(WindowDto::class, [
                'time' => intval($windowTimeUnix),
                'timeslotId' => intval($timeslotId),
            ]);
        }

        return $result;
    }
}