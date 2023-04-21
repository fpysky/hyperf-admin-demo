<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Hyperf\Utils\Arr;
use HyperfTest\HttpTestCase;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractHttpTest extends HttpTestCase
{
    use Resources;

    public function jsonPrettyPrint($contents)
    {
        if (is_string($contents)) {
            $contents = json_decode($contents);
        }
        echo json_encode($contents, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
    }

    protected function getToken(array $params = [])
    {
        if (empty($params)) {
            $params = [
                'phone' => $this->loginPhone,
                'password' => $this->loginPassword,
            ];
        }
        $client = new Client(['base_uri' => $this->getToken['host']]);
        try {
            $response = $client->request($this->getToken['method'], $this->getToken['path'], [
                RequestOptions::FORM_PARAMS => $params,
            ]);
            $respContent = json_decode($response->getBody()->getContents(), true);
            $this->assertArrayHasKey('token', $respContent['data']);
            $this->assertNotEmpty($respContent['data']['token']);
        } catch (GuzzleException $e) {
            if (method_exists($e, 'getResponse')) {
                $this->jsonPrettyPrint($e->getResponse()->getBody()->getContents());
            } else {
                var_dump($e->getMessage());
            }
            throw new \RuntimeException('登录异常！');
        }
        return $respContent['data']['token'];
    }

    protected function assertBaseStructure(array $resp)
    {
        $this->assertArrayHasKey('msg', $resp);
        $this->assertArrayHasKey('code', $resp);
        $this->assertArrayHasKey('data', $resp);
        $this->assertTrue(is_string($resp['msg']));
        $this->assertTrue(is_numeric($resp['code']));
    }

    protected function assertPaginationStructure(array $respContent)
    {
        $this->assertArrayHasKey('list', $respContent['data']);
        $this->assertArrayHasKey('total', $respContent['data']);
        $this->assertIsArray($respContent['data']['list']);
        $this->assertIsNumeric($respContent['data']['total']);
    }

    protected function assertArrayHasKeyAndIsRightType($key,array $array,string $type)
    {
        $this->assertArrayHasKey($key,$array);
        switch ($type){
            case 'string':
                $this->assertIsString($array[$key]);
                break;
            case 'numeric':
                $this->assertIsNumeric($array[$key]);
                break;
            case 'int':
                $this->assertIsInt($array[$key]);
                break;
            case 'array':
                $this->assertIsArray($array[$key]);
                break;
            case 'assocArray':
                $this->assertIsArray($array[$key]);
                $this->assertEquals(true,Arr::isAssoc($array[$key]));
                break;
            case 'bool':
                $this->assertIsBool($array[$key]);
                break;
            case 'float':
                $this->assertIsFloat($array[$key]);
                break;
        }
    }

    protected function getWithPaginateAssert(string $path, array $options = []): Response
    {
        $resp = $this->getWithOkAssert($path, $options);
        $this->assertArrayHasKey('list', $resp->getData());
        $this->assertArrayHasKey('total',$resp->getData());
        return $resp;
    }

    protected function getWithOkAssert(string $path, array $options = []): Response
    {
        return $this->requestWithOkAssert('get', $path, $options);
    }

    protected function postWithOkAssert(string $path, array $options = []): Response
    {
        return $this->requestWithOkAssert('post', $path, $options);
    }

    protected function putWithOkAssert(string $path, array $options = []): Response
    {
        return $this->requestWithOkAssert('put', $path, $options);
    }

    protected function deleteWithOkAssert(string $path, array $options = []): Response
    {
        return $this->requestWithOkAssert('delete', $path, $options);
    }

    protected function patchWithOkAssert(string $path, array $options = []): Response
    {
        return $this->requestWithOkAssert('patch', $path, $options);
    }

    protected function requestWithOkAssert(string $method, string $path, array $options = []): Response
    {
        return $this->requestWithBaseAssert($method, $path, $options, $this->statusCodeOk, $this->codeOk);
    }

    protected function requestWithBaseAssert(string $method, string $path, array $options, int $expectStatusCode = 200, int $expectCode = 200000): Response
    {
        // todo::$options json 数据传输异常，等待修复
        if ($this->login) {
            $this->putTokenToOptions($this->getToken(), $options);
        }

        /** @var ResponseInterface $response */
        $response = $this->request($method, $path, $options);

        $rawRespContents = $response->getBody()->getContents();

        if ($this->debug) {
            $this->jsonPrettyPrint($rawRespContents);
        }

        $this->assertEquals($expectStatusCode, $response->getStatusCode());

        $respContent = json_decode($rawRespContents, true);
        $this->assertBaseStructure($respContent);
        $this->assertEquals($expectCode, $respContent['code']);

        return new Response(
            (int) $respContent['code'],
            (string) $respContent['msg'],
            $respContent['data'],
            $rawRespContents
        );
    }

    protected function generateRandomPhone(): string
    {
        $header = [
            '130', '131', '132', '133', '134', '135',
            '136', '137', '138', '139', '144', '147',
            '150', '151', '152', '153', '155', '156',
            '157', '158', '159', '176', '177', '178',
            '180', '181', '182', '183', '184', '185',
            '186', '187', '188', '189',
        ];
        $footer = mt_rand(100000, 99999999);
        return $header[mt_rand(0, 33)] . $footer;
    }

    private function putTokenToOptions(string $token, array &$options)
    {
        $tokenHeader = ['token' => $token];
        if (! isset($options[RequestOptions::HEADERS])) {
            $options[RequestOptions::HEADERS] = $tokenHeader;
        } else {
            $options[RequestOptions::HEADERS] = array_merge($options[RequestOptions::HEADERS], $tokenHeader);
        }
    }
}
