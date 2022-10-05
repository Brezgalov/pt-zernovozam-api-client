<?php

namespace Brezgalov\ZernovozamApiClient\ResponseAdapters;

use Brezgalov\BaseApiClient\ResponseAdapters\BaseResponseAdapter;
use Brezgalov\ZernovozamApiClient\ZernovozamApiClient;

class DeleteTimeslotsResponseAdapter extends BaseResponseAdapter
{
    /**
     * @param int $timeslotId
     * @return int|null
     */
    public function checkTimeslotStatus(int $timeslotId)
    {
        if (!is_array($this->responseData)) {
            return null;
        }

        $status = $this->responseData['Status'] ?? [];

        return $status[$timeslotId] ?? null;
    }

    /**
     * @param int $timeslotId
     * @return string|null
     */
    public function getTimeslotError(int $timeslotId)
    {
        $status = $this->checkTimeslotStatus($timeslotId);

        if (is_null($status)) {
            return null;
        }

        if ($status === ZernovozamApiClient::RESPONSE_STATUS_SUCCESS) {
            return null;
        }

        if (array_key_exists($status, ZernovozamApiClient::DELETE_WINDOWS_ERRORS_DESCRIPTIONS)) {
            return ZernovozamApiClient::DELETE_WINDOWS_ERRORS_DESCRIPTIONS[$status];
        }

        return ZernovozamApiClient::ERROR_MESSAGE_UNKNOWN;
    }
}