<?php

namespace Brezgalov\ZernovozamApiClient\RequestBodies;

interface IRequestBody
{
    /**
     * @return array
     */
    public function getBody();
}