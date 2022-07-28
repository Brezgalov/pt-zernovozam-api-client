<?php

namespace Brezgalov\ZernovozamApiClient\UnitTests;

use Brezgalov\ZernovozamApiClient\ZernovozamApiClient;
use PHPUnit\Framework\TestCase;
use yii\httpclient\Request;

/**
 * Class ZernovozamApiClientTest
 * @package Brezgalov\ZernovozamApiClient\UnitTests
 *
 * @coversDefaultClass \Brezgalov\ZernovozamApiClient\ZernovozamApiClient
 */
class ZernovozamApiClientTest extends TestCase
{
    /**
     * @covers ::getClearPhoneNumber
     */
    public function testClearPhoneNumber()
    {
        $client = \Yii::createObject(ZernovozamApiClient::class);

        $result = $client->getClearPhoneNumber('+7 (909) fgfg авава 34 56 789');

        $this->assertEquals('79093456789', $result);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     *
     * @covers ::prepareRequest
     * @covers ::getClearPhoneNumber
     * @covers ::authByPhoneAndSuperTokenRequest
     */
    public function testAuthByPhoneAndSuper()
    {
        $google = 'https://google.com';

        /** @var ZernovozamApiClient $client */
        $client = \Yii::createObject([
            'class' => ZernovozamApiClient::class,
            'baseUrl' => $google,
            'superToken' => 'super-test',
            'apiToken' => '123-token',
        ]);

        $this->assertEquals('super-test', $client->superToken);
        $this->assertEquals('123-token', $client->apiToken);

        $request = $client->authByPhoneAndSuperTokenRequest('+7 (909) fgfg авава 34 56 789');

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals("{$google}/auth?", $request->getFullUrl());
        $this->assertEquals('post', strtolower($request->getMethod()));

        $data = $request->getData();

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('UserName', $data);
        $this->assertArrayHasKey('AccessToken', $data);

        $this->assertEquals('+79093456789', $data['UserName']);
        $this->assertEquals('super-test', $data['AccessToken']);

        $headers = $request->getHeaders();

        $this->assertTrue($headers->has(ZernovozamApiClient::API_TOKEN_HEADER_NAME));
        $this->assertEquals('123-token', $headers->get(ZernovozamApiClient::API_TOKEN_HEADER_NAME));
    }
}