import request from '@/utils/request'

export function ruleList() {
    return request({
        url: '/admin/rule',
        method: 'get',
    })
}

export function upRuleStatus(data) {
    return request({
        url: '/admin/rule/status',
        method: 'patch',
        data
    })
}

export function createRule(data) {
    return request({
        url: '/admin/rule',
        method: 'post',
        data
    })
}

export function editRule(data) {
    return request({
        url: '/admin/rule',
        method: 'put',
        data
    })
}

export function topRule() {
    return request({
        url: '/admin/rule/topRule',
        method: 'get',
    })
}

export function parentMenusTree() {
    return request({
        url: '/admin/rule/parentMenusTree',
        method: 'get',
    })
}

export function deleteRule(data) {
    return request({
        url: '/admin/rule',
        method: 'delete',
        data
    })
}

export function roleRuleTree(roleId) {
    return request({
        url: '/admin/rule/roleRuleTree/' + roleId,
        method: 'get'
    })
}
