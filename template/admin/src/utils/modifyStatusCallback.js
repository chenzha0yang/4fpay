import store from '@/store'

export default function modifyStatusCallback(actionsName, dataName, data, index) {
  for (const key in data) {
    if (data.hasOwnProperty(key)) {
      if (!key.includes('Id') && !key.includes('id')) {
        data[key] = data[key] === 1 ? 2 : 1
      }
    }
  }

  store.dispatch(actionsName, {
    type: "edit",
    name: dataName,
    data,
    current: index
  })
}
