const getters = {
  sidebar: state => state.app.sidebar,
  language: state => state.app.language,
  device: state => state.app.device,
  // visitedViews: state => state.tagsView.visitedViews,
  // cachedViews: state => state.tagsView.cachedViews,
  token: state => state.user.token,
  avatar: state => state.user.avatar,
  name: state => state.user.name,
  userName: state => state.user.userName,
  nickName: state => state.user.nickName,
  // introduction: state => state.user.introduction,
  shortcutsText: state => state.app.shortcutsText,
  // status: state => state.user.status,
  roles: state => state.user.roles,
  // setting: state => state.user.setting,
  lastLoginIP: state => state.user.lastLoginIP,
  lastLoginTime: state => state.user.lastLoginTime,
  // time: state => state.user.time,
  permission_routers: state => state.permission.routers,
  client: state => state.user.client,
  isView: state => state.user.isView
}
export default getters
