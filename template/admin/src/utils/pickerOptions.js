import store from '@/store'
const pickerOptions = {
  // disabledDate(time) {
  //   return time.getTime() > Date.now();
  // },
  shortcuts: [{
      text: store.getters.shortcutsText.latestWeek,
      onClick(picker) {
        const end = new Date()
        const start = new Date()
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
        picker.$emit('pick', [start, end])
      }
    },
    {
      text: store.getters.shortcutsText.lastMonth,
      onClick(picker) {
        const end = new Date()
        const start = new Date()
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
        picker.$emit('pick', [start, end])
      }
    },
    {
      text: store.getters.shortcutsText.lastThreeMonths,
      onClick(picker) {
        const end = new Date()
        const start = new Date()
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
        picker.$emit('pick', [start, end])
      }
    }
  ]
}
export default pickerOptions
