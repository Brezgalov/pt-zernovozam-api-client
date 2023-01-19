<?php

namespace Brezgalov\ZernovozamApiClient\Dto;

use yii\base\Component;

/**
 * Class TimeslotDto
 * @package Brezgalov\ZernovozamApiClient\Dto
 */
class TimeslotDto extends Component implements ITimeslotDto
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $cultureId;

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $dateEnd;

    /**
     * @var string
     */
    public $dateStart;

    /**
     * @var bool
     */
    public $isArrived = false;

    /**
     * @var bool
     */
    public $isLate = false;

    /**
     * @var bool
     */
    public $isOwnWindow = false;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $plate;

    /**
     * @var int
     */
    public $receiverId;

    /**
     * @var bool
     */
    public $showPhone = false;

    /**
     * @var int
     */
    public $traderId;

    /**
     * @var int
     */
    public $providerId;

    /**
     * @var string
     */
    public $createdDate;

    /**
     * @var string
     */
    public $deletePossibleAfterCreationHours = 1;

    /**
     * @var string
     */
    public $deletePossibleBeforeTimeslotHours = 2;

    /**
     * TimeslotDto constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct(
            $this->prepareConfig($config)
        );
    }

    /**
     * @return array
     */
    protected function getConfigFieldsMap()
    {
        return [
            "CultureId" => "cultureId",
            "Date" => "date",
            "DateEnd" => "dateEnd",
            "DateStart" => "dateStart",
            "Id" => "id",
            "IsArrived" => "isArrived",
            "IsLate" => "isLate",
            "IsOwnWindow" => "isOwnWindow",
            "Phone" => "phone",
            "Plate" => "plate",
            "ReceiverId" => "receiverId",
            "ShowPhone" => "showPhone",
            "TraderId" => "traderId",
            "ProviderId" => "providerId",
            "DateCreate" => "createdDate",
            "DenyDelAfter" => "deletePossibleAfterCreationHours",
            "DenyDelBefore" => "deletePossibleBeforeTimeslotHours",
        ];
    }

    /**
     * @param array $config
     * @return array
     */
    protected function prepareConfig(array $config)
    {
        $map = $this->getConfigFieldsMap();
        $mapKeys = array_keys($map);

        foreach ($config as $key => $value) {
            if (!array_key_exists($key, $map)) {
                continue;
            }

            if (!in_array($key, $mapKeys)) {
                unset($config[$key]);
                continue;
            }

            $newField = $map[$key];

            $config[$newField] = $value;
            unset($config[$key]);
        }

        return $config;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getDateStart()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getDateEnd()
    {
        return $this->date;
    }

    /**
     * @return int|null
     */
    public function getDateStartUnix()
    {
        if (empty($this->date) || empty($this->dateStart)) {
            return null;
        }

        [$date] = explode(' ', $this->date);

        return strtotime("{$date} {$this->dateStart}");
    }

    /**
     * @return int|null
     */
    public function getDateEndUnix()
    {
        if (empty($this->date) || empty($this->dateEnd)) {
            return null;
        }

        [$date] = explode(' ', $this->date);

        return strtotime("{$date} {$this->dateEnd}");
    }

    /**
     * @return int
     */
    public function getCultureId()
    {
        return $this->cultureId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getIsArrived()
    {
        return $this->isArrived;
    }

    /**
     * @return bool
     */
    public function getIsLate()
    {
        return $this->isLate;
    }

    /**
     * @return bool
     */
    public function getIsOwnWindow()
    {
        return $this->isOwnWindow;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getPlate()
    {
        return $this->plate;
    }

    /**
     * @return int
     */
    public function getReceiverId()
    {
        return $this->receiverId;
    }

    /**
     * @return bool
     */
    public function getShowPhone()
    {
        return $this->showPhone;
    }

    /**
     * @return int
     */
    public function getTraderId()
    {
        return $this->traderId;
    }

    /**
     * @return int
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * @return string
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @return int
     */
    public function getDeletePossibleAfterCreationHours()
    {
        return $this->deletePossibleAfterCreationHours;
    }

    /**
     * @return int
     */
    public function getDeletePossibleBeforeTimeslotHours()
    {
        return $this->deletePossibleBeforeTimeslotHours;
    }
}
