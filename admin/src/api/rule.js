import request from '@/utils/request'

export function ruleList() {
  return request({
    url: '/rule',
    method: 'get',
  })
}

export function upRuleStatus(data) {
  return request({
    url: '/rule/status',
    method: 'patch',
    data
  })
}

export function createRule(data) {
  return request({
    url: '/rule',
    method: 'post',
    data
  })
}

export function editRule(data) {
  return request({
    url: '/rule',
    method: 'put',
    data
  })
}

export function topRule() {
  return request({
    url: '/rule/topRule',
    method: 'get',
  })
}

export function parentMenusTree() {
  return request({
    url: '/rule/parentMenusTree',
    method: 'get',
  })
}

export function deleteRule(data) {
  return request({
    url: '/rule',
    method: 'delete',
    data
  })
}

export function roleRuleTree(roleId) {
  return request({
    url: '/rule/roleRuleTree/' + roleId,
    method: 'get'
  })
}

export function ruleDetail(id) {
  return request({
    url: '/rule/' + id,
    method: 'get'
  })
}


export function selectRuleTree() {
  return request({
    url: '/rule/selectRuleTree',
    method: 'get'
  })
}
