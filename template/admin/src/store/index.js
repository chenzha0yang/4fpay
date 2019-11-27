import Vue from 'vue'
import Vuex from 'vuex'
import app from './modules/app'
import errorLog from './modules/errorLog'
import permission from './modules/permission'
import tagsView from './modules/tagsView'
import user from './modules/user'
import order from './modules/order'
import shop from './modules/shop'
import config from './modules/config'
import whiteList from './modules/whiteList'
import bank from './modules/bank'
import maintain from './modules/maintain'
import logSet from './modules/logSet'
import account from './modules/account'
import home from './modules/home'
import cache from './modules/cache'
import getters from './getters'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    app,
    errorLog,
    permission,
    tagsView,
    user,
    order,
    shop,
    config,
    whiteList,
    bank,
    maintain,
    logSet,
    account,
    home,
    cache
  },
  getters
})

export default store
