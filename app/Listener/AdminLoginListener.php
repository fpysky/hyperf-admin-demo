<?php
namespace App\Listener;

use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Model\AdminLoginLog;
use App\Event\AdminLogin;
use App\Utils\Help;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;

class AdminLoginListener implements ListenerInterface
{
    protected RequestInterface $request;
    
    #[Inject]
    protected Help $help;
    
    public function listen(): array
    {
        return [
            AdminLogin::class,
        ];
    }

    /** @param AdminLogin $event */
    public function process(object $event):void
    {
        list($province, $city) = $this->help->provinceCity($event->ip);
        
        Admin::query()
            ->where('id', $event->adminId)->update([
                'last_login_ip' => $this->help->ipToInt($event->ip),
                'last_login_time' => time(),
            ]);

        AdminLoginLog::query()->create([
            'last_login_ip' => $this->help->ipToInt($event->ip),
            'last_login_time' => time(),
            'province' => $province,
            'city' => $city,
            'admin_id' => $event->adminId,
        ]);
    }

}
