<?php

namespace Brezgalov\ZernovozamApiClient\RequestBodies;

use Brezgalov\ZernovozamApiClient\Dto\WindowConfirmationDto;
use yii\base\Component;

class ConfirmWindowsRequestBody extends Component implements IRequestBody
{
    /**
     * @var WindowConfirmationDto[]
     */
    protected $confirmations = [];

    /**
     * @param WindowConfirmationDto[] $confirmations
     */
    public function setConfirmations(array $confirmations)
    {
        $this->confirmations = [];

        foreach ($confirmations as $confirmation) {
            $this->addConfirmation($confirmation);
        }
    }

    /**
     * @param WindowConfirmationDto $confirmation
     */
    public function addConfirmation(WindowConfirmationDto $confirmation)
    {
        $this->confirmations[] = $confirmation;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        $result = [
            'Confirmations' => []
        ];

        foreach ($this->confirmations as $confirmation) {
            $result['Confirmations'][] = [
                "Id" => $confirmation->id,
                "Plate" => $confirmation->plate,
                "DriverPhone" => $confirmation->driverPhone,
            ];
        }

        return $result;
    }
}