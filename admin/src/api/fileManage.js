import request from '@/utils/request';

export function fileList(data) {
  return request({
    url: '/fileManage/fileList',
    method: 'get',
    params:data
  })
}
