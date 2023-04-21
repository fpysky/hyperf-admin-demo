<?php

declare(strict_types=1);

namespace HyperfTest\Cases\StdRespTest;

use GuzzleHttp\RequestOptions;
use Hyperf\Utils\Arr;
use HyperfTest\Cases\AbstractHttpTest;
use HyperfTest\Cases\Response;

/**
 * @internal
 * @coversNothing
 */
class StdRespTest extends AbstractHttpTest
{
    public function testAll()
    {
        $this->testMsgResp();
        $this->testArrayResp();
        $this->testMepResp();
        $this->testBoolResp();
    }

    public function testBoolResp()
    {
        $resp = $this->doReq(true);

        $this->assertIsBool($resp->getData());
        $this->printResp(__FUNCTION__,$resp->getRawResp());
    }

    public function testMsgResp()
    {
        $data = 'this is a message.';
        $resp = $this->doReq($data);

        $this->assertIsString($resp->getMsg());
        $this->printResp(__FUNCTION__,$resp->getRawResp());
    }

    public function testMepResp()
    {
        $data = ['key1' => 'value1'];
        $resp = $this->doReq($data);

        $this->assertTrue(Arr::isAssoc($resp->getData()));
        $this->printResp(__FUNCTION__,$resp->getRawResp());
    }

    public function testArrayResp()
    {
        $data = [
            ['key1' => 'value1'],
            ['key2' => 'value2']
        ];
        $resp = $this->doReq($data);

        $this->assertTrue(!Arr::isAssoc($resp->getData()));
        $this->printResp(__FUNCTION__,$resp->getRawResp());
    }

    private function printResp(string $title, mixed $contents):void
    {
        echo "{$title}: \n";
        jsonPrettyPrint($contents);
        echo "\n";
    }

    private function doReq(mixed $reqData): Response
    {
        return $this->postWithOkAssert('/stdRespTest',[
            RequestOptions::FORM_PARAMS => ['data' => $reqData],
        ]);
    }
}
