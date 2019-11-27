export function NowTime(getTime) {
  var date = new Date()

  var len = date.getTime()
  var offset = date.getTimezoneOffset() * 60000

  var utcTime = len + offset

  var dd2 = new Date(utcTime + 3600000 * -4)
  dd2.setSeconds(dd2.getSeconds() + 1)
  var myYears = (dd2.getFullYear() < 1900) ? (1900 + dd2.getFullYear()) : dd2.getFullYear()
  return myYears + '-' + fixNum(dd2.getMonth() + 1) + '-' + fixNum(
    dd2.getDate() + 1) + ' ' + time()
}

function time() {
  var s = ''
  var vtime = new Date()
  s = fixNum(vtime.getHours()) + ':' + fixNum(vtime.getMinutes()) + ':' + fixNum(vtime.getSeconds())
  return (s)
}

function fixNum(num) {
  return parseInt(num) < 10 ? '0' + num : num
}
