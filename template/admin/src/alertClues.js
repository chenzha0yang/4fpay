export default {
  install(Vue, options) {
    Vue.prototype.alertMessage = function (Message, type = 'success') {
      // 提示框 ErrorMessage 错误信息； ErrorTitle: 提示类型 success/error/info...
      this.$notify({
        showClose: true,
        title: '',
        message: Message,
        type: type,
        duration: 2000
      })
    }
    // 提示框
    Vue.prototype.operating = function (Message, type) {
      this.$message({
        type: type,
        message: Message
      })
    }
  }
}
