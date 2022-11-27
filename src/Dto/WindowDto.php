<?php

namespace Brezgalov\ZernovozamApiClient\Dto;

use yii\base\Model;

class WindowDto extends Model
{
    /**
     * @var int
     */
    public $cultureId;

    /**
     * @var int
     */
    public $time;

    /**
     * @var int
     */
    public $timeEnd;

    /**
     * @var int
     */
    public $timeStart;

    /**
     * @var int
     */
    public $timeslotId;

    /**
     * @var bool
     */
    public $isArrived;

    /**
     * @var bool
     */
    public $isLate;

    /**
     * @var bool
     */
    public $isOwnWindow;

    /**
     * @var int
     */
    public $receiverId;

    /**
     * @var bool
     */
    public $showPhone;

    /**
     * @var int
     */
    public $traderId;

    /**
     * @var int
     */
    public $providerId;
}
