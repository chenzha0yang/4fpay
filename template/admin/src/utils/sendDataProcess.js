// function filterArr(idsArr, reference) {
//   var returnArr = reference.filter(item => {
//     if (idsArr.includes(item.id)) {
//       return true
//     }
//     if (fatherId(item, idsArr)) {
//       item.children = filterChildren(idsArr, item.children || [])
//       return true
//     }
//     return false
//   })
//   return returnArr
// }

// function filterChildren(idsArr, reference) {
//   const arr = reference.filter(item => {
//     if (hasId(item.id, idsArr) || fatherId(item, idsArr)) {
//       if ('children' in item && item.children.length) {
//         item.children = filterChildren(idsArr, item.children)
//       }
//       return true
//     }
//     return false
//   })
//   return arr
// }



// export function getListSort(reference, str, parentId) {
//   str === '' ? 'id' : str

//   const returnArr = reference.map(item => {
//     var obj = {}
//     obj.value = item.value
//     if (parentId === undefined) {
//       obj[str] = item.parentId
//     } else {
//       obj[str] = parentId
//     }
//     if ('children' in item) {
//       obj.children = getListSort(item.children, str, item.value)
//     }
//     return obj
//   })
//   return returnArr
// }

// export function getMenuParentID(value, reference, str = 'id') {

//   for (const item of reference) {
//     if (value === item[str]) {
//       return item[str]
//     } else {
//       if ('children' in item && item.children.length) {
//         const index = getMenuParentID(value, item.children, str)
//         if (index !== false) {
//           return item[str]
//         }
//       }
//     }
//   }

//   return false
// }

// export function getChangeMenu(ids, reference, str = 'id', parentId = 'parentId') {
//   const returnArr = ids.map(item => {
//     var obj = {}
//     obj[str] = item
//     const ParentId = getMenuParentID(item, reference, str)
//     obj[parentId] = ParentId === false ? 0 : ParentId === item ? 0 : ParentId
//     return obj
//   })

//   return returnArr

// }

// export function isParentID(obj, reference) {
//   for (const item of reference) {
//     if (obj.value === item.value) {
//       if (obj.parentId !== item.parentId) {
//         return true
//       }
//     } else {
//       if ('children' in item && item.children.length) {
//         const flag = isParentID(obj, item.children)
//         if (flag) {
//           return true
//         }
//       }
//     }
//   }
// }

// function hasId(id, idsArr) {
//   return idsArr.some(item => item.id === id)
// }

// function fatherId(ref, idsArr) {
//   var mapArr = typeof idsArr[0] === 'number' ? idsArr : idsArr.map(v => v.id)

//   if (mapArr.includes(ref.id)) {
//     return true
//   }
//   if ('children' in ref && ref.children.length) {
//     for (var iterator of ref.children) {
//       if (fatherId(iterator, mapArr)) {
//         return true
//       }
//     }
//   }
//   return false
// }

// 判断搜索条件是否变更
export function resetPage(newObj, oldObj) {

  for (const key in oldObj) {
    if (oldObj.hasOwnProperty(key)) {
      if (oldObj[key] !== newObj[key]) {
        return true
      }
    }
  }
  return false

}

// 判断历史路由标签页是否显示
export function getIsName(name, nameArr, key = 'name', third = true) {

  return nameArr.some(v => {

    var flag = v[key] === name && !v[third]

    if ('children' in v && v.children.length) {
      flag = getIsName(name, v.children, key, third)
    }
    return flag

  })

}

// 获取全部的ID
export function idSProcess(ids, reference) {
  if (!Array.isArray(ids) || !Array.isArray(reference)) return []
  // var returnArr = []
  if (ids.some(v => v === '*')) {
    return getAllIdFunction(reference)
  } else {
    // returnArr = getAllIdFunction(filterArr(ids, reference))
    return ids
  }
  // return returnArr
}

export function getAllIdFunction(reference, str = 'id') {
  var returnArr = []
  getAllId(reference)

  function getAllId(ref) {
    ref.forEach(ele => {
      returnArr.push(ele[str])
      if ('children' in ele && ele.children.length) {
        getAllId(ele.children)
      }
    })
  }
  return returnArr
}
// 获取全部的ID END
