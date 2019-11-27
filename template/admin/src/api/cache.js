import request from '@/utils/request'
import url from './url'


export function searchCacheKey(params) {
  return request({
    url: url.cacheKey,
    method: 'post',
    data: {
      ...params
    }
  })
}

export function searchCacheLen(params) {
  return request({
    url: url.cacheLen,
    method: 'post',
    data: {
      ...params
    }
  })
}

export function searchCacheVal(params) {
  return request({
    url: url.cacheVal,
    method: 'post',
    data: {
      ...params
    }
  })
}

export function delCache(params) {
  return request({
    url: url.cacheDel,
    method: 'post',
    data: {
      ...params
    }
  })
}
