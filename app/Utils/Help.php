<?php
namespace App\Utils;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class Help
{
    #[Inject]
    private RequestInterface $request;

    /**
     * 根据ip获取位置信息
     * User: ZhouGongCe
     * Time: 2021/8/13 16:44
     * @param $ip
     * @return mixed
     */
    public function getAreaByIp($ip): mixed
    {
        $ak = config('myconfig.baidu.map_ak');
        return json_decode(file_get_contents('https://api.map.baidu.com/location/ip?ak='. $ak .'&ip=' . $ip), true);
    }

    /**
     * 密码加密
     * User：zhougongce
     * Date：2021/8/3
     * Time：9:38
     * @param $mobile
     * @param $password
     * @param $createTime
     * @return string
     */
    public function encrypPassword($mobile, $password, $createTime): string
    {
        return md5(md5($mobile . $password . $createTime . config('myconfig.salt')));
    }

    public function passwordPreg(): string
    {
        return '/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/';
    }

    public function checkPassword($password): bool|int
    {
        return preg_match($this->passwordPreg(), $password);
    }

    /**
     * ip转成整型
     * User: ZhouGongCe
     * Time: 2021/8/13 16:42
     * @param $ip
     * @return string
     */
    public function ipToInt($ip): string
    {
        return sprintf("%u", ip2long($ip));
    }

    /**
     * 整型转ip
     * User: ZhouGongCe
     * Time: 2021/8/13 16:43
     * @param $ip
     * @return string
     */
    public function intToIp($ip): string
    {
        return long2ip((int)$ip);
    }

    /**
     * 获取ip
     * User: ZhouGongCe
     * Time: 2021/8/13 16:43
     */
    public function getIp(): string
    {
        $headers = $this->request->getHeaders();
        return $headers['x-real-ip'][0] ?? $headers['x-forwarded-for'][0] ?? '';
    }

    /**
     * 管理员id
     * User: ZhouGongCe
     * Time: 2021/8/13 16:43
     * @return int|mixed
     */
    public function getAdminId(): mixed
    {
        $tokenValue = $this->adminTokenInfo();
        return empty($tokenValue['uid']) ? 0 : $tokenValue['uid'];
    }

    /**
     * 管理员类型
     * User: ZhouGongCe
     * Time: 2021/8/13 16:43
     * @return mixed
     */
    public function getAdminType(): mixed
    {
        $tokenValue = $this->adminTokenInfo();
        return $tokenValue['type'];
    }

    /**
     * 管理员token信息
     * User: ZhouGongCe
     * Time: 2021/8/13 16:43
     * @return mixed
     */
    public function adminTokenInfo(): mixed
    {
        $token = $this->request->getHeaderLine(config('myconfig.requireToken.name'));
        return RedisApi::getInstance()->getToken($token);
    }

    public function mobilePreg(): string
    {
        return '/^1[3456789]\d{9}$/';
    }

    public function isMobile($mobile): bool|int
    {
        return preg_match($this->mobilePreg(), $mobile);
    }

    /**
     * 加密 | 解密
     * User: ZhouGongCe
     * Time: 2021/8/13 16:43
     * @param $data 加密或解密数据
     * @param $type encrypt-加密，decrypt-解密
     * @return false|string
     */
    public function opensslData($data, $type): bool|string
    {
        $str = md5(config('myconfig.encryKey'));
        $aesKey = mb_substr($str, 0, 16);
        $aesIV = mb_substr($str, 16);
        if ($type == 'encrypt'){
            return openssl_encrypt($data, 'AES-128-CBC', $aesKey, 0 , $aesIV);
        }else if ($type == 'decrypt'){
            return openssl_decrypt($data, 'AES-128-CBC', $aesKey, 0 , $aesIV);
        }else{
            return false;
        }
    }

    /**
     * 获取省份城市
     * User: ZhouGongCe
     * Time: 2021/8/13 16:44
     * @param $ip
     * @return array|string[]
     */
    public function provinceCity($ip): array
    {
        $res = $this->getAreaByIp($ip);
        if (!empty($res['content']['address_detail'])){
            return [$res['content']['address_detail']['province'], $res['content']['address_detail']['city']];
        }
        return ['', ''];
    }
}


