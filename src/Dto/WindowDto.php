<?php

namespace Brezgalov\ZernovozamApiClient\Dto;

class WindowDto
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
    }
}