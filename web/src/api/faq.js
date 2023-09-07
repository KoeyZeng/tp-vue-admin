import request from '@/utils/request'

export function getIndex(query) {
  return request({
    url: '/admin/faq/getIndex',
    method: 'get',
    params: query
  })
}
export function saveIndex(data) {
  return request({
    url: '/admin/faq/saveIndex',
    method: 'post',
    data
  })
}
export function delIndex(data) {
  return request({
    url: '/admin/faq/delIndex',
    method: 'post',
    data
  })
}
