import {
  resetPage
} from "@/utils/sendDataProcess";


export default {
  methods: {
    // 查询
    mixinsHandleSearch({
      data = {},
      oldData = {},
      page = 1,
      searchFn,
      callBacks,
      eveCallBacks
    }) {
      if (searchFn === undefined || typeof searchFn !== 'string') {
        return
      }
      const flag = resetPage(data, oldData);
      data.page = flag ? 1 : page

      eveCallBacks && eveCallBacks(flag)

      this[searchFn](data).then(rps => {})
        .catch(() => {})
        .finally(() => {
          callBacks && callBacks(flag)
        })
    },
  }
}
