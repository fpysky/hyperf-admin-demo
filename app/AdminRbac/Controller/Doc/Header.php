<?php

/**
 * @apiDefine headers
 * @apiHeaderExample {json} Header-App
 * {
 *      "Accept": "application/json"
 * }
 * @apiHeaderExample {json} Header-WeChat
 * {
 *      "Accept": "application/json"
 * }
 * @apiHeaderExample {json} Header-Wap
 * {
 *      "Accept": "application/json"
 * }
 */

/**
 * @apiDefine htmlHeaders
 * @apiHeaderExample {json} HTML-Header-App
 * {
 *      "Accept": "text/html"
 * }
 * @apiHeaderExample {json} HTML-Header-WeChat
 * {
 *      "Accept": "text/html"
 * }
 * @apiHeaderExample {json} HTML-Header-Wap
 * {
 *      "Accept": "text/html"
 * }
 */

/**
 * @apiDefine V1D1D0
 * @apiHeaderExample {json} Header-App
 * {
 *      "Accept": "application/json"
 * }
 * @apiHeaderExample {json} Header-WeChat
 * {
 *      "Accept": "application/json"
 * }
 * @apiHeaderExample {json} Header-Wap
 * {
 *      "Accept": "application/json"
 * }
 */

/**
 * @apiDefine commonSuccessResponse
 * @apiSuccess {int} code 业务状态码
 * @apiSuccess {string} msg 业务返回信息
 * @apiSuccess {object} data 返回内容
 * @apiSuccessExample {json} Success-Response:
 *   {
 *       "code": 200000,
 *       "msg": "",
 *       "data": {}
 *   }
 */

/**
 * @apiDefine commonErrResponse
 * @apiError (成功示例 5xx) {int} code 业务状态码
 * @apiError (成功示例 5xx) {string} msg 业务返回信息
 * @apiError (成功示例 5xx) {object} data 返回内容(空)
 * @apiErrorExample {json} Error-Response:
 *   {
 *       "code": 500000,
 *       "msg": "未知错误，请重试.",
 *       "data": {}
 *   }
 */

/**
 * @apiDefine urlParameter 嵌入式URL传参
 */

/**
 * @apiDefine query URL传参
 */

/**
 * @apiDefine bodyRawJson JSON传参
 */