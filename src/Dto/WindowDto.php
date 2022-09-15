<?php

namespace Brezgalov\ZernovozamApiClient\Dto;

use yii\base\Model;

class WindowDto extends Model
{
    /**
     * @var int
     */
    public $time;

    /**
     * @var int
     */
    public $timeslotId;

    /**
     * WindowDto constructor.
     * @param int $time
     * @param int $timeslotId
     */
    public function __construct(int $time, int $timeslotId)
    {
        $this->time = $time;
        $this->timeslotId = $timeslotId;

        parent::__construct([]);
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'time',
            'timeslot_id' => 'timeslotId',
        ];
    }
}