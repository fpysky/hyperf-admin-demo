import request from '@/utils/request'

export function getRoleSelectData() {
  return request({
    url: '/role/selectData',
    method: 'get',
  })
}

export function roleList(data) {
  return request({
    url: '/role',
    method: 'get',
    params:data
  })
}

export function upRoleStatus(data) {
  return request({
    url: '/role/status',
    method: 'patch',
    data
  })
}

export function createRole(data) {
  return request({
    url: '/role',
    method: 'post',
    data
  })
}

export function editRole(data) {
  return request({
    url: '/role',
    method: 'put',
    data
  })
}

export function deleteRole(data) {
  return request({
    url: '/role',
    method: 'delete',
    data
  })
}

export function setRule(data){
  return request({
    url:'/role/setRule',
    method:'post',
    data
  })
}
