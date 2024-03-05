import request from '@/utils/request'

export function getOperateLog(data) {
  return request({
    url: '/system/operateLog',
    method: 'get',
    params:data
  })
}
