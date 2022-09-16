<?php

namespace Brezgalov\ZernovozamApiClient\Dto;

interface ITimeslotDto
{
    /**
     * @return int
     */
    public function getDate();

    /**
     * @return int
     */
    public function getDateStart();

    /**
     * @return int
     */
    public function getDateEnd();

    /**
     * @return int|null
     */
    public function getDateStartUnix();

    /**
     * @return int|null
     */
    public function getDateEndUnix();

    /**
     * @return int
     */
    public function getCultureId();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return bool
     */
    public function getIsArrived();

    /**
     * @return bool
     */
    public function getIsLate();

    /**
     * @return bool
     */
    public function getIsOwnWindow();

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @return string
     */
    public function getPlate();

    /**
     * @return int
     */
    public function getReceiverId();

    /**
     * @return bool
     */
    public function getShowPhone();

    /**
     * @return int
     */
    public function getTraderId();

    /**
     * @return int
     */
    public function getProviderId();
}