import request from '@/utils/request'

export function getArticle(query) {
  return request({
    url: '/admin/news/getArticle',
    method: 'get',
    params: query
  })
}
export function saveArticle(data) {
  return request({
    url: '/admin/news/saveArticle',
    method: 'post',
    data
  })
}
export function delArticle(data) {
  return request({
    url: '/admin/news/delArticle',
    method: 'post',
    data
  })
}
