export function delList(data, MenuList, Id = 'Id') {
  const returnArr = MenuList.filter(item => {

    if (data[Id] === item[Id]) {
      return false
    }
    console.log(item)

    if ('children' in item && item.children.length) {
      item.children = delList(data, item.children, Id)
    }

    return true
  })

  return returnArr
}

export function putList(data, MenuList, parentId = 'Id', liId = 'Id') {

  console.log(data)
  console.log(MenuList)
  var pushFlag = true

  var returnArr = putList_(data, MenuList, parentId, liId)


  if (pushFlag) {
    if (data[parentId] === 0) {
      returnArr.push(data)
    } else {
      returnArr = addList(data, returnArr, parentId, liId)
    }
  }

  console.log(returnArr)
  return returnArr

  function putList_(obj, list, parentId = 'Id', liId = 'Id') {
    const arr = list.filter(item => {

      if (item[liId] === obj[liId]) {
        if (item[parentId] === obj[parentId]) {
          console.log(item)
          console.log(obj)
          item = Object.assign(item, obj)
          pushFlag = false
          return true
        } else {
          return false
        }
      }

      if ('children' in item && item.children.length) {
        item.children = putList_(obj, item.children, parentId, liId)
      }

      return true
    })

    return arr
  }

}

export function addList(data, MenuList, parentId = 'Id', liId = 'Id') {
  const returnArr = MenuList.filter(item => {
    if (data[parentId] === item[liId]) {
      if ('children' in item) {
        item.children.push(data)
      } else {
        item.children = [data]
      }
    }

    return true
  })
  return returnArr
}
