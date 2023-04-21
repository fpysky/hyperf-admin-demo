<?php

/**
 * @api {get} / 0 接口说明
 * @apiDescription 对接口中的一些公共特性进行说明
 * @apiName basedocs
 * @apiGroup A
 * @apiVersion 1.0.0
 *
 * @apiSuccess {Number} code    状态码，0为成功，其他为错误
 * @apiSuccess {String} msg     出错时提示的信息
 * @apiSuccess {String} data    数据
 *
 * @apiSuccessExample [json-app]-操作成功
 * HTTP/2.0 200 OK
 * {
 *      [
 *          {},
 *          ...
 *      ]
 * }
 * @apiSuccessExample [json-cmd]-操作成功
 * HTTP/2.0 200 OK
 * {
 *      "code": 0,
 *      "msg": "",
 *      "data": [
 *          {},
 *          ...
 *      ]
 * }
 * @apiErrorExample [json-app]-资源不存在
 * HTTP/2.0 404 Not Found
 * {
 *      "msg": "资源不存在"
 * }
 * @apiErrorExample [json-cmd]-资源不存在
 * HTTP/2.0 404 Not Found
 * {
 *      "code": 404,
 *      "msg": "资源不存在",
 *      "data": []
 * }
 * @apiErrorExample [json-app]-数据不完整
 * HTTP/2.0 400
 * {
 *      "msg": "数据不完整"
 * }
 * @apiErrorExample [json-cmd]-数据不完整
 * HTTP/2.0 400
 * {
 *      "code": 400,
 *      "msg": "数据不完整",
 *      "data": []
 * }
 * 提交的数据会进行个数验证，所以每个接口的参数都必须要提交
 * 没有值的参数一律提交0
 * @apiErrorExample [json-app]-处理失败
 * HTTP/2.0 500
 * {
 *      "msg": "服务器出问题了，请等待修复"
 * }
 * @apiErrorExample [json-cmd]-处理失败
 * HTTP/2.0 500
 * {
 *      "code": 500,
 *      "msg": "服务器出问题了，请等待修复",
 *      "data": []
 * }
 * @apiErrorExample [json-app]-处理失败-稍后尝试
 * HTTP/2.0 503
 * {
 *      "msg": "服务器暂时无法处理，请稍后尝试"
 * }
 * @apiErrorExample [json-cmd]-处理失败-稍后尝试
 * HTTP/2.0 503
 * {
 *      "code": 503,
 *      "msg": "服务器暂时无法处理，请稍后尝试",
 *      "data": []
 * }
 * @apiErrorExample Error 0:
 * Error 0:
 * 检查一下是不是没有传参数？
 * 域名是否已经解析到相关ip？
 **/

/**
 * @api {put} / 1 PUT说明
 * @apiDescription 文档提供的接口测试工具可能无法完成put请求
 * @apiName put
 * @apiGroup A
 * @apiVersion 1.0.0
 *
 **/