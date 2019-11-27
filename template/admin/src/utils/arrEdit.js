import {
  Message
} from 'element-ui'

export default function arrEdit({state, arrName, type, newInfo, current, arrCount}) {
  switch (type) {
    case 'edit':

      // if (
      //   state[arrName][current].Id !== undefined && (state[arrName][current].Id === newInfo.Id) ||
      //   state[arrName][current].aId !== undefined && (state[arrName][current].aId === newInfo.aId) ||
      //   state[arrName][current].id !== undefined && (state[arrName][current].id === newInfo.id) ||
      //   state[arrName][current].payId !== undefined && (state[arrName][current].payId === newInfo.payId)||
      //   state[arrName][current].typeId !== undefined && (state[arrName][current].typeId === newInfo.typeId)
      // ) {
        const nweData = Object.assign(
          state[arrName][current],
          newInfo
        )

        state[arrName].splice(current, 1, nweData)
      // }
      // else {
      //   Message({
      //     showClose: true,
      //     message: '信息更新出错，请重新搜索',
      //     type: 'error',
      //     duration: 3 * 1000
      //   })
      // }
      break

    case 'add':
      state[arrCount] = state[arrName].unshift(newInfo)
      break

    case 'del':
        state[arrName].splice(current, 1)
        state[arrCount] -= 1
      break

    case 'editQuota':
        var money = Number(state[arrName][current].userMoney)
        if (newInfo.resource === 1) {
          money += newInfo.transaction
        } else if (newInfo.resource === 2) {
          money -= newInfo.transaction
        }
        money = String(money)
        var index = money.indexOf('.')
        if (money.indexOf('.') > -1) {
          money = money.slice(0, index + 3)
          money += '00'
        } else {
          money += '.00'
        }
        index = money.indexOf('.')
        money = money.slice(0, index + 3)
        state[arrName][current].userMoney = money
      break

    case 'stop':
      for (const key of newInfo.ids) {
        for (const item of state[arrName]) {
          if (item.Id === key.Id) {
            item.status = item.status === 2 ? 1 : 2
            break
          }
        }
      }
      break

    case 'kicking':
      if (current === undefined) {
        for (const key of newInfo.ids) {
          for (const item of state[arrName]) {
            if (item.Id === key) {
              item.online = 2
              break
            }
          }
        }
      } else {
        if (state[arrName][current].Id === newInfo.ids[0]) {
          state[arrName][current].online = 2
        } else {
          Message({
            showClose: true,
            message: '信息更新出错，请重新搜索',
            type: 'error',
            duration: 3 * 1000
          })
        }
      }
      break
    default:
      break
  }
}
