<?php

namespace App\Controller\Permission;

use App\Annotation\Permission;
use App\Controller\AbstractController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\AdminOperationLog;
use App\Resource\AdminOperationLogResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Stringable\Str;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HeaderParameter;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\QueryParameter;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class SystemController extends AbstractController
{
    #[GetMapping(path: 'system/operateLog')]
    #[Get(path: 'system/operateLog', summary: '操作日志列表', tags: ['系统管理/操作日志'])]
    #[Permission(name:'操作日志列表',module: '系统管理/操作日志')]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[QueryParameter(name: 'page', description: '页码', schema: new Schema(type: 'integer'), example: 1)]
    #[QueryParameter(name: 'pageSize', description: '每页显示条数', schema: new Schema(type: 'integer'), example: 15)]
    #[QueryParameter(name: 'module', description: '系统模块', schema: new Schema(type: 'string'), example: '系统模块')]
    #[QueryParameter(name: 'operateType', description: '操作类型 1.新增 2.删除 3.修改 4.查询', schema: new Schema(type: 'integer'), example: 1)]
    #[QueryParameter(name: 'operateAdmin', description: '操作人员', schema: new Schema(type: 'string'), example: '小蜜')]
    #[QueryParameter(name: 'operateStatusStr', description: '操作状态', schema: new Schema(type: 'string'), example: '成功')]
    #[QueryParameter(name: 'operateTimeStart', description: '操作时间开始(YYYY-MM-DD HH:ii:ss)', schema: new Schema(type: 'string'), example: '2023-04-01 22:23:11')]
    #[QueryParameter(name: 'operateTimeEnd', description: '操作时间结束(YYYY-MM-DD HH:ii:ss)', schema: new Schema(type: 'string'), example: '2023-04-01 22:23:11')]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(property: 'data', description: '返回对象', required: ['total', 'list'], properties: [
                new Property(property: 'total', description: '总条数', type: 'integer', example: 200),
                new Property(
                    property: 'list',
                    description: '日志列表',
                    type: 'array',
                    items: new Items(
                        required: [
                            'id', 'module', 'operateTypeZh',
                            'method', 'operateAdmin', 'operateIp',
                            'operateIpAddress','operateStatusZh', 'operatedAt',
                        ],
                        properties: [
                            new Property(property: 'id', description: '日志编号', type: 'integer', example: 1),
                            new Property(property: 'module', description: '系统模块', type: 'string', example: '系统模块'),
                            new Property(property: 'operateTypeZh', description: '操作类型中文', type: 'string', example: '操作类型中文'),
                            new Property(property: 'method', description: '请求方式', type: 'string', example: 'POST'),
                            new Property(property: 'operateAdmin', description: '操作人员', type: 'string', example: 'xxx'),
                            new Property(property: 'operateIp', description: '操作IP', type: 'string', example: '127.0.0.1'),
                            new Property(property: 'operateIpAddress', description: '操作地点', type: 'string', example: '海南省海口市'),
                            new Property(property: 'operateStatusZh', description: '操作状态中文', type: 'string', example: '成功'),
                            new Property(property: 'operatedAt', description: '操作日期(YYYY-MM-DD HH:ii:ss)', type: 'string', example: '2023-04-01 22:23'),
                        ]
                    ),
                ),
            ], type: 'object'),
        ]
    ))]
    public function operateLogList(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize');
        $operateType = (int) $this->request->input('operateType');
        $module = (string) $this->request->input('module');
        $operateAdmin = (string) $this->request->input('operateAdmin');
        $operateStatusStr = (string) $this->request->input('operateStatusStr');
        $operateTimeStart = (string) $this->request->input('operateTimeStart');
        $operateTimeEnd = (string) $this->request->input('operateTimeEnd');

        $builder = AdminOperationLog::query();

        if (Str::length($module) !== 0) {
            $builder->where('module', 'like', "%{$module}%");
        }

        if ($operateType !== 0) {
            $builder->where('operate_type', $operateType);
        }

        if (Str::length($operateAdmin) !== 0) {
            $builder->where('admin_name', 'like', "%{$operateAdmin}%");
        }

        if (Str::length($operateStatusStr) !== 0) {
            if (in_array($operateStatusStr, ['成功', '失败'])) {
                $operateStatus = $operateStatusStr == '成功' ? 1 : 0;
                $builder->where('operate_status', $operateStatus);
            }
        }

        if (Str::length($operateTimeStart) !== 0) {
            $builder->where('operated_at', '>=', $operateTimeStart);
        }

        if (Str::length($operateTimeEnd) !== 0) {
            $builder->where('operated_at', '<=', $operateTimeEnd);
        }

        $paginator = $builder->orderByDesc('created_at')
            ->paginate($pageSize);

        return $this->success([
            'list' => AdminOperationLogResource::collection($paginator->items()),
            'total' => $paginator->total(),
        ]);
    }

    #[GetMapping(path: 'system/operateLog/export')]
    #[Get(path: 'system/operateLog/export', summary: '操作日志列表导出', tags: ['系统管理/操作日志'])]
    #[Permission(name:'操作日志列表导出',module: '系统管理/操作日志')]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[QueryParameter(name: 'module', description: '系统模块', schema: new Schema(type: 'string'), example: '系统模块')]
    #[QueryParameter(name: 'operateType', description: '操作类型 1.新增 2.删除 3.修改 4.查询', schema: new Schema(type: 'integer'), example: 1)]
    #[QueryParameter(name: 'operateAdmin', description: '操作人员', schema: new Schema(type: 'string'), example: '小蜜')]
    #[QueryParameter(name: 'operateStatusStr', description: '操作状态', schema: new Schema(type: 'string'), example: '成功')]
    #[QueryParameter(name: 'operateTimeStart', description: '操作时间开始(YYYY-MM-DD HH:ii:ss)', schema: new Schema(type: 'string'), example: '2023-04-01 22:23:11')]
    #[QueryParameter(name: 'operateTimeEnd', description: '操作时间结束(YYYY-MM-DD HH:ii:ss)', schema: new Schema(type: 'string'), example: '2023-04-01 22:23:11')]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: ['cells', 'list', 'fileName'],
                properties: [
                    new Property(property: 'cells', description: '表头字段', type: 'array', items: new Items(
                        description: '日志编号',
                        type: 'string',
                        example: '日志编号',
                    )),
                    new Property(
                        property: 'list',
                        description: '商圈列表',
                        type: 'array',
                        items: new Items(
                            required: [
                                'id', 'module', 'operateTypeZh',
                                'method', 'operateAdmin', 'operateIp',
                                'operateIpAddress', 'operateStatusZh', 'operatedAt',
                            ],
                            properties: [
                                new Property(property: 'id', description: '日志编号', type: 'integer', example: 1),
                                new Property(property: 'module', description: '系统模块', type: 'string', example: '系统模块'),
                                new Property(property: 'operateTypeZh', description: '操作类型中文', type: 'string', example: '操作类型中文'),
                                new Property(property: 'method', description: '请求方式', type: 'string', example: 'POST'),
                                new Property(property: 'operateAdmin', description: '操作人员', type: 'string', example: 'xxx'),
                                new Property(property: 'operateIp', description: '操作IP', type: 'string', example: '127.0.0.1'),
                                new Property(property: 'operateIpAddress', description: '操作地点', type: 'string', example: '海南省海口市'),
                                new Property(property: 'operateStatusZh', description: '操作状态中文', type: 'string', example: '成功'),
                                new Property(property: 'operatedAt', description: '操作日期(YYYY-MM-DD HH:ii:ss)', type: 'string', example: '2023-04-01 22:23'),
                            ]
                        ),
                    ),
                    new Property(property: 'fileName', description: '导出文件名称', type: 'string', example: 'http://xx/xx/xx.excel'),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function operateLogExport(): ResponseInterface
    {
        $module = (string) $this->request->input('module');
        $operateType = (int) $this->request->input('operateType');
        $operateAdmin = (string) $this->request->input('operateAdmin');
        $operateStatusStr = (string) $this->request->input('operateStatusStr');
        $operateTimeStart = (string) $this->request->input('operateTimeStart');
        $operateTimeEnd = (string) $this->request->input('operateTimeEnd');

        $builder = AdminOperationLog::query();

        if (Str::length($module) !== 0) {
            $builder->where('module', 'like', "%{$module}%");
        }

        if ($operateType !== 0) {
            $builder->where('operate_type', $operateType);
        }

        if (Str::length($operateAdmin) !== 0) {
            $builder->where('admin_name', 'like', "%{$operateAdmin}%");
        }

        if (Str::length($operateStatusStr) !== 0) {
            if (in_array($operateStatusStr, ['成功', '失败'])) {
                $operateStatus = $operateStatusStr == '成功' ? 1 : 0;
                $builder->where('operate_status', $operateStatus);
            }
        }

        if (Str::length($operateTimeStart) !== 0) {
            $builder->where('operated_at', '>=', $operateTimeStart);
        }

        if (Str::length($operateTimeEnd) !== 0) {
            $builder->where('operated_at', '<=', $operateTimeEnd);
        }

        $list = AdminOperationLogResource::collection($builder->orderByDesc('created_at')->get());

        return $this->success([
            'cells' => ['日志编号','系统模块','操作类型','请求方式','操作人员','操作地址','操作地点','操作状态','操作日期'],
            'list' => $list,
            'fileName' => '操作日志列表-'.date('YmdHis'),
        ]);
    }
}