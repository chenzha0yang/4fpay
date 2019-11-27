import {
  searchCacheKey,
  searchCacheLen,
  searchCacheVal,
  delCache
} from '@/api/cache'

const cache = {
  actions: {
    searchCacheKey: ({}, params) => {
      return searchCacheKey(params).then(res => {
        const data = res.data
        return {
          success: true,
          data
        }
      })
    },
    searchCacheLen: ({}, params) => {
      return searchCacheLen(params).then(res => {
        const data = res.data
        return {
          success: true,
          data
        }
      })
    },
    searchCacheVal: ({}, params) => {
      return searchCacheVal(params).then(res => {
        const data = res.data
        return {
          success: true,
          data
        }
      })
    },
    delCache: ({}, params) => {
      return delCache(params).then(res => {
        return {
          success: true,
          res
        }
      })
    }
  }
}

export default cache