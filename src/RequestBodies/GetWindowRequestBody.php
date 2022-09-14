<?php

namespace Brezgalov\ZernovozamApiClient\RequestBodies;

use yii\base\Component;

class GetWindowRequestBody extends Component implements IRequestBody
{
    /**
     * @var int
     */
    protected $cultureId;

    /**
     * @var int
     */
    protected $incomingDate;

    /**
     * @var int
     */
    protected $stevedore;

    /**
     * @var int
     */
    protected $traderId;

    /**
     * @var int
     */
    protected $trucksCount;

    /**
     * GetWindowRequestBody constructor.
     * @param int $time
     * @param int $cultureId
     * @param int $stevedoreId
     * @param int $traderId
     * @param int $trucksCount
     */
    public function __construct(int $time, int $cultureId, int $stevedoreId, int $traderId, int $trucksCount)
    {
        $this->incomingDate = $time;
        $this->cultureId = $cultureId;
        $this->stevedore = $stevedoreId;
        $this->traderId = $traderId;
        $this->trucksCount = $trucksCount;

        parent::__construct([]);
    }

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