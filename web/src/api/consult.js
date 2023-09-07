import request from '@/utils/request'

export function getIndex(query) {
  return request({
    url: '/admin/consult/getIndex',
    method: 'get',
    params: query
  })
}
export function saveIndex(data) {
  return request({
    url: '/admin/consult/saveIndex',
    method: 'post',
    data
  })
}
export function delIndex(data) {
  return request({
    url: '/admin/consult/delIndex',
    method: 'post',
    data
  })
}
