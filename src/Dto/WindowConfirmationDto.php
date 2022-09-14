<?php

namespace Brezgalov\ZernovozamApiClient\Dto;

use yii\base\Component;

class WindowConfirmationDto extends Component
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $plate;

    /**
     * @var string
     */
    public $driverPhone;
}