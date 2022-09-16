<?php

namespace Brezgalov\ZernovozamApiClient\ResponseAdapters;

use Brezgalov\BaseApiClient\ResponseAdapters\BaseResponseAdapter;
use Brezgalov\ZernovozamApiClient\Dto\SubmitDto;
use yii\base\InvalidConfigException;

class ConfirmTimeslotsResponseAdapter extends BaseResponseAdapter
{
    /**
     * @return SubmitDto[]
     * @throws InvalidConfigException
     */
    public function getSubmits()
    {
        $confirms = $this->responseData['Confirmations'] ?? [];

        $result = [];
        foreach ($confirms as $confirmData) {
            $result[] = \Yii::createObject(SubmitDto::class, [
                'timeslotId' => $confirmData['Id'],
                'status' => $confirmData['Status'],
            ]);
        }

        return $result;
    }
}