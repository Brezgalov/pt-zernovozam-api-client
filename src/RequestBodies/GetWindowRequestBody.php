<?php

namespace Brezgalov\ZernovozamApiClient\RequestBodies;

use yii\base\Component;

class GetWindowRequestBody extends Component implements IRequestBody
{
    /**
     * @var int
     */
    public $cultureId;

    /**
     * @var int
     */
    public $incomingDate;

    /**
     * @var int
     */
    public $stevedore;

    /**
     * @var int
     */
    public $traderId;

    /**
     * @var int
     */
    public $trucksCount;

    /**
     * @var int
     */
    public $version;

    /**
     * @return string|null
     */
    public function getIncomingDate()
    {
        return $this->incomingDate ? date('d.m.Y H:i', $this->incomingDate) : null;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return [
            "CultureId" => $this->cultureId,
            "IncomingDate" => $this->getIncomingDate(),
            "Phone" => "",
            "ProviderId" => $this->traderId,
            "Stevedore" => $this->stevedore,
            "TraderId" => $this->traderId,
            "TrucksCount" => $this->trucksCount,
            "Version" => 0,
        ];
    }
}