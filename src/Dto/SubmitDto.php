<?php

namespace Brezgalov\ZernovozamApiClient\Dto;

use Brezgalov\ZernovozamApiClient\ZernovozamApiClient;
use yii\base\Model;

class SubmitDto extends Model
{
    /**
     * @var int
     */
    public $timeslotId;

    /**
     * @var int
     */
    public $status;

    /**
     * SubmitDto constructor.
     * @param int $timeslotId
     * @param int $status
     */
    public function __construct(int $timeslotId, int $status)
    {
        $this->timeslotId = $timeslotId;
        $this->status = $status;

        parent::__construct([]);
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'timeslot_id' => 'timeslotId',
            'api_error' => 'apiError',
        ];
    }

    /**
     * @return string|null
     */
    public function getApiError()
    {
        if ($this->status === ZernovozamApiClient::RESPONSE_STATUS_SUCCESS) {
            return null;
        }

        return ZernovozamApiClient::ERRORS_DESCRIPTIONS[$this->status] ?? ZernovozamApiClient::ERROR_MESSAGE_UNKNOWN;
    }
}