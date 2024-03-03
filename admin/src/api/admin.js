import request from '@/utils/request'

export function adminList(data) {
  return request({
    url: '/admin',
    method: 'get',
    params:data
  })
}

export function createAdmin(data) {
  return request({
    url: '/admin',
    method: 'post',
    data
  })
}

export function editAdmin(data) {
  return request({
    url: '/admin',
    method: 'put',
    data
  })
}

export function upAdminStatus(data) {
  return request({
    url: '/admin/status',
    method: 'patch',
    data
  })
}

export function deleteAdmin(data) {
  return request({
    url: '/admin',
    method: 'delete',
    data
  })
}
