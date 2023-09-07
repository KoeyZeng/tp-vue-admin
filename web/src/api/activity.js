import request from '@/utils/request'

export function getActivity(query) {
  return request({
    url: '/admin/activity/getActivity',
    method: 'get',
    params: query
  })
}
export function saveActivity(data) {
  return request({
    url: '/admin/activity/saveActivity',
    method: 'post',
    data
  })
}
export function delActivity(data) {
  return request({
    url: '/admin/activity/delActivity',
    method: 'post',
    data
  })
}

export function getApply(query) {
  return request({
    url: '/admin/activity/getApply',
    method: 'get',
    params: query
  })
}
export function saveApply(data) {
  return request({
    url: '/admin/activity/saveApply',
    method: 'post',
    data
  })
}
export function delApply(data) {
  return request({
    url: '/admin/activity/delApply',
    method: 'post',
    data
  })
}
