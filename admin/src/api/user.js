import request from '@/utils/request'

/**
 * @method addUserLogin 登录
 * @param {*} data
 * @returns
 */
export function addUserLogin(data) {
  return request({
    url: '/login',
    method: 'post',
    data
  })
}

/**
 * @method addUserInfo 用户信息
 * @param {*} data
 * @returns
 */
export function addUserInfo(data) {
  return request({
    url: '/userInfo',
    method: 'get',
    data
  })
}

/**
 * 退出登陆
 */
export function logout(){
    return request({
        url:'/logout',
        method:'post',
    })
}
