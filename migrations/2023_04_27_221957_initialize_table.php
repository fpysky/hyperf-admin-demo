<?php

declare(strict_types=1);

use App\AdminRbac\Model\Admin\Admin;
use Hyperf\Database\Migrations\Migration;
use Hyperf\DbConnection\Db;

class InitializeTable extends Migration
{
    /**
     * @throws Exception
     */
    public function up(): void
    {
        $this->initAdmin();
    }

    /**
     * @throws Exception
     */
    private function initAdmin()
    {
        $admin = new Admin();

        $admin->name = 'admin';
        $admin->password = Admin::encryptPassword('admin123');
        $admin->status = Admin::STATUS_ENABLE;
        $admin->type = Admin::TYPE_SUPER;
        $admin->mobile = '18888888888';
        $admin->email = '18888888888@qq.com';
        $admin->dept_id = 1;
        $admin->post_id = 1;

        $admin->save();
        $admin->setRole([1]);
    }
}
