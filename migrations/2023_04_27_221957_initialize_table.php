<?php

declare(strict_types=1);

use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Model\Post\Post;
use App\AdminRbac\Model\Role\Role;
use Hyperf\Database\Migrations\Migration;

class InitializeTable extends Migration
{
    /**
     * @throws Exception
     */
    public function up(): void
    {
        $this->initAdmin();
        $this->initPost();
        $this->initRole();
        $this->initDept();
    }

    /**
     * @throws Exception
     */
    private function initAdmin()
    {
        $admin = new Admin();

        $admin->name = 'admin';
        $admin->password = encryptPassword('admin123');
        $admin->status = Admin::STATUS_ENABLE;
        $admin->type = Admin::TYPE_SUPER;
        $admin->mobile = '18888888888';
        $admin->email = '18888888888@qq.com';

        $admin->save();
        $admin->setRole([1]);
    }

    private function initRole()
    {
        $role = new Role();
        $role->name = '默认角色';
        $role->status = Role::STATUS_ENABLE;
        $role->desc = '默认角色';
        $role->save();
    }

    private function initDept()
    {
        $dept = new Dept();
        $dept->name = '默认部门';
        $dept->status = Dept::STATUS_ENABLE;
        $dept->remark = '默认部门';
        $dept->save();
    }

    private function initPost()
    {
        $post = new Post();
        $post->name = '默认职位';
        $post->status = Post::STATUS_ENABLE;
        $post->remark = '默认职位';
        $post->save();
    }
}
