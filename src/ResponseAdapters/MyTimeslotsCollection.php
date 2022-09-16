<?php

namespace Brezgalov\ZernovozamApiClient\ResponseAdapters;

use Brezgalov\BaseApiClient\ResponseAdapters\BaseResponseAdapter;
use Brezgalov\ZernovozamApiClient\Dto\ITimeslotDto;
use Brezgalov\ZernovozamApiClient\Dto\TimeslotDto;

class MyTimeslotsCollection extends BaseResponseAdapter implements \Iterator
{
    /**
     * @var int
     */
    protected $position = 0;

    /**
     * reset position
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return ITimeslotDto|null
     */
    public function current()
    {
        if (!array_key_exists('Timeslots', $this->responseData)) {
            return null;
        }

        return new TimeslotDto($this->responseData['Timeslots'][$this->position]);
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * change position
     */
    public function next()
    {
        $this->position += 1;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        if (!array_key_exists('Timeslots', $this->responseData)) {
            return false;
        }

        return array_key_exists($this->position, $this->responseData['Timeslots']);
    }
}