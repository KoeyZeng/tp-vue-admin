import request from '@/utils/request'

export function getAdmin(query) {
  return request({
    url: '/admin/system/getAdmin',
    method: 'get',
    params: query
  })
}

export function saveAdmin(data) {
  return request({
    url: '/admin/system/saveAdmin',
    method: 'post',
    data
  })
}

export function delAdmin(query) {
  return request({
    url: '/admin/system/delAdmin',
    method: 'get',
    params: query
  })
}

export function getSlide(query) {
  return request({
    url: '/admin/system/getSlide',
    method: 'get',
    params: query
  })
}

export function saveSlide(data) {
  return request({
    url: '/admin/system/saveSlide',
    method: 'post',
    data
  })
}

export function delSlide(query) {
  return request({
    url: '/admin/system/delSlide',
    method: 'get',
    params: query
  })
}

export function getLogin(query) {
  return request({
    url: '/admin/system/getLogin',
    method: 'get',
    params: query
  })
}

export function getPlay(query) {
  return request({
    url: '/admin/system/getPlay',
    method: 'get',
    params: query
  })
}

export function changePassword(data) {
  return request({
    url: '/admin/system/changePassword',
    method: 'post',
    data
  })
}

export function getMenu() {
  return request({
    url: '/admin/system/getMenu',
    method: 'get'
  })
}
export function saveMenu(data) {
  return request({
    url: '/admin/system/saveMenu',
    method: 'post',
    data
  })
}
export function delMenu(data) {
  return request({
    url: '/admin/system/delMenu',
    method: 'post',
    data
  })
}

export function getRole(query) {
  return request({
    url: '/admin/system/getRole',
    method: 'get',
    params: query
  })
}
export function saveRole(data) {
  return request({
    url: '/admin/system/saveRole',
    method: 'post',
    data
  })
}
export function delRole(data) {
  return request({
    url: '/admin/system/delRole',
    method: 'post',
    data
  })
}
// 清理缓存
export function getClear(data) {
  return request({
    url: '/admin/system/getClear',
    method: 'get'
  })
}

