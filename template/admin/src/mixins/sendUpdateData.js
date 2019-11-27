import modifyStatusCallback from "@/utils/modifyStatusCallback";

import {
  MessageBox
} from "element-ui";

export default {
  methods: {

    // switch按钮
    mixinsSwitchChange({
      data,
      index,
      str = 'state',
      attr = "open",
      thenCallback,
      catchCallback,
      finallyCallback,
      putStoreFn,
      backtrackFn,
      type,
      dataName
    }) {
      const msg =
        data[str] === 1 ?
        this.$t(`switchMsg.${attr}[2]`) :
        this.$t(`switchMsg.${attr}[1]`);

      MessageBox.confirm(msg, {
          confirmButtonText: this.$t("alertMsg.confirm"),
          cancelButtonText: this.$t("alertMsg.cencelOperation"),
          type: "warning"
        })
        .then(res => {

          this.mixinsSendUpdateData({
            data,
            index,
            putStoreFn,
            backtrackFn,
            thenCallback,
            catchCallback,
            finallyCallback,
            type,
            dataName,
            flag: true
          })
        })
        .catch(err => {
          modifyStatusCallback(backtrackFn, dataName, data, index);
        }).finally(() => {
          finallyCallback && finallyCallback()
        })
    },

    // 发送
    mixinsSendUpdateData({
      data,
      index,
      putStoreFn,
      backtrackFn,
      thenCallback,
      catchCallback,
      finallyCallback,
      type,
      dataName,
      flag
    }) {
      this[putStoreFn]({
          name: dataName,
          type: type,
          data,
          current: index
        })
        .then(res => {
          this.alertMessage(res.msg);
          thenCallback && thenCallback()
        })
        .catch(err => {
          if (flag) modifyStatusCallback(backtrackFn, dataName, data, index);
          catchCallback && catchCallback()
        })
        .finally(() => {
          finallyCallback && finallyCallback()
        })
    }
  }
}
