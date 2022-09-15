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

    /**
     * WindowConfirmationDto constructor.
     * @param int $id
     * @param string $plate
     * @param string $driverPhone
     */
    public function __construct(int $id, string $plate, string $driverPhone)
    {
        $this->id = $id;
        $this->plate = $plate;
        $this->driverPhone = $driverPhone;

        parent::__construct([]);
    }
}