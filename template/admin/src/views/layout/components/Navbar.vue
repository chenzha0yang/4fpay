<template>
  <el-menu class="navbar" mode="horizontal">
    <hamburger
      class="hamburger-container"
      :toggle-click="toggleSideBar"
      :is-active="sidebar.opened"
    />

    <breadcrumb class="breadcrumb-container"/>

    <div class="right-menu">
      <!-- <error-log class="errLog-container right-menu-item"></error-log> -->
      <el-tooltip effect="dark" placement="bottom" disabled>
        <div class="right-menu-item divSpan">{{ $t('navbar.Time') }}: {{ NowTime }}</div>
      </el-tooltip>

      <el-tooltip effect="dark" placement="bottom" disabled>
        <div class="right-menu-item divSpan line">
          <span/>
        </div>
      </el-tooltip>

      <el-tooltip effect="dark" :content="$t('index.account')" placement="bottom">
        <div class="right-menu-item divSpan">{{ userName }}</div>
      </el-tooltip>

      <el-tooltip effect="dark" :content="$t('navbar.screenfull')" placement="bottom">
        <screenfull class="screenfull right-menu-item"/>
      </el-tooltip>

      <!-- <lang-select class="international right-menu-item" /> -->
      <el-dropdown class="avatar-container right-menu-item" trigger="click">
        <div class="avatar-wrapper">
          <img class="user-avatar" :src="avatar+'?imageView2/1/w/80/h/80'">
          <i class="el-icon-caret-bottom"/>
        </div>
        <el-dropdown-menu slot="dropdown">
          <router-link to="/">
            <el-dropdown-item>{{ $t('navbar.index') }}</el-dropdown-item>
          </router-link>
          <a @click="handleEditPsw()">
            <el-dropdown-item>{{ $t('table.editPassword') }}</el-dropdown-item>
          </a>
          <el-dropdown-item divided>
            <span style="display:block;" @click="logout">{{ $t('navbar.logOut') }}</span>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </div>
    <el-dialog
      id="dialog-form-header"
      :title="$t('table.editPassword')"
      :visible.sync="dialogFormVisible"
    >
      <el-form
        ref="dataForm"
        :rules="demoRules"
        :model="temp"
        label-position="right"
        label-width="160px"
        style="width: 480px; margin-left:50px;"
      >
        <div class="form-items-hd">
          <el-form-item :label="$t('table.oldPass')" prop="oldPassword">
            <el-input
              v-model="temp.oldPassword"
              type="password"
              width="300px"
              :placeholder="$t('table.input')+$t('table.oldPass')"
              :maxlength="12"
            />
          </el-form-item>
          <el-form-item :label="$t('table.password')" prop="password">
            <el-input
              v-model="temp.password"
              type="password"
              width="300px"
              :placeholder="$t('table.input')+$t('table.password')"
              :maxlength="12"
            />
          </el-form-item>
          <el-form-item :label="$t('table.repassword')" prop="checkPass">
            <el-input
              v-model="temp.checkPass"
              type="password"
              width="300px"
              :placeholder="$t('table.input')+$t('table.repassword')"
              :maxlength="12"
            />
          </el-form-item>
        </div>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">{{ $t('table.cancel') }}</el-button>
        <el-button
          :disabled="Disable"
          :loading="Loading"
          type="primary"
          @click="editPassword"
        >{{ $t('table.confirm') }}</el-button>
      </div>
    </el-dialog>
  </el-menu>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import Breadcrumb from "@/components/Breadcrumb";
import Hamburger from "@/components/Hamburger";
import Screenfull from "@/components/Screenfull";
// import LangSelect from "@/components/LangSelect";
import { MessageBox } from "element-ui";
import { isSpace, isPassword } from "@/utils/validate";

export default {
  components: {
    Breadcrumb,
    Hamburger,
    Screenfull
    // LangSelect
  },
  data() {
    const oldPass = (rule, value, callback) => {
      value.length < 6 || isSpace(value)
        ? callback(new Error(this.$t("valiMsg.oldPass")))
        : callback();
    };

    const validatePass = (rule, value, callback) => {
      switch (isPassword(value)) {
        case "isSpace":
          this.$t("login.spaceMsg");
          callback(new Error(this.$t("login.spaceMsg")));
          break;
        case false:
          if (value === "") {
            callback(new Error(this.$t("valiMsg.mewPass")));
          } else {
            callback(new Error(this.$t("login.passWeak")));
          }
          break;
        default:
          if (this.temp.checkPass !== "" && this.temp.checkPass !== undefined)
            this.$refs.dataForm.validateField("checkPass");
          callback();
          break;
      }
    };

    const validateCheckPass = (rule, value, callback) => {
      if (value) {
        if (value === this.temp.password) {
          callback();
        } else {
          callback(new Error(this.$t("login.notSame")));
        }
      } else {
        callback(new Error(this.$t("valiMsg.checkPass")));
      }
    };

    return {
      NowTime: null,
      timer: null,
      temp: {
        oldPassword: "",
        password: "",
        checkPass: ""
      },
      demoRules: {
        oldPassword: [
          { required: true, validator: oldPass, trigger: "change" }
        ],
        password: [
          { required: true, validator: validatePass, trigger: "change" }
        ],
        checkPass: [
          { required: true, validator: validateCheckPass, trigger: "change" }
        ]
      },
      Disable: true,
      Loading: false,
      dialogFormVisible: false
    };
  },
  computed: {
    ...mapGetters(["sidebar", "avatar", "money", "userName"])
  },
  created() {
  },

  mounted() {
    const _this = this;
    this.timer = setInterval(() => {
      _this.setTime();
    }, 1000);
  },

  destroyed() {
    clearInterval(this.timer);
  },
  methods: {
    ...mapActions(["UidEditUserPassword", "LogOut"]),

    toggleSideBar() {
      this.$store.dispatch("toggleSideBar");
    },

    logout() {
      this.$store.dispatch("LogOut");
    },

    setTime() {
      const date = new Date();

      const yyyy = date.getFullYear();
      var m = date.getMonth() + 1;
      var d = date.getDate();
      var hh = date.getHours();
      var mm = date.getMinutes();
      var ss = date.getSeconds();

      m = m < 9 ? "0" + m : m;
      d = d < 9 ? "0" + d : d;
      hh = hh < 9 ? "0" + hh : hh;
      mm = mm < 9 ? "0" + mm : mm;
      ss = ss < 9 ? "0" + ss : ss;

      this.NowTime = `${yyyy}-${m}-${d} ${hh}:${mm}:${ss}`;
    },

    handleEditPsw(index, row) {
      this.temp = {
        oldPassword: "",
        password: "",
        checkPass: ""
      };
      this.Loading = false;
      this.Disable = false;
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    // 修改密码
    editPassword() {
      this.$refs["dataForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          const { password, oldPassword } = this.temp;
          this.UidEditUserPassword({ password, oldPassword })
            .then(data => {
              if (data.data.code === 2009) {
                this.dialogFormVisible = false;
                MessageBox.alert(
                  this.$t("alertMsg.reLoginSuccess"),
                  this.$t("alertMsg.prompt"),
                  {
                    confirmButtonText: this.$t("alertMsg.reLogin")
                  }
                ).then(() => {
                  this.LogOut();
                });
              }
              this.alertMessage(data.rps.msg);
            })
            .catch(() => {
              this.Disable = false;
              this.Loading = false;
            });
        }
      });
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.navbar {
  height: 50px;
  line-height: 50px;
  border-radius: 0px !important;
  .hamburger-container {
    line-height: 58px;
    height: 50px;
    float: left;
    padding: 0 10px;
  }
  .breadcrumb-container {
    float: left;
  }
  .errLog-container {
    display: inline-block;
    vertical-align: top;
  }
  .right-menu {
    float: right;
    height: 100%;
    &:focus {
      outline: none;
    }
    .right-menu-item {
      display: inline-block;
      margin: 0 8px;
    }
    // .screenfull {
    //   height: 50px;
    // }
    .international {
      vertical-align: top;
    }
    .theme-switch {
      vertical-align: 15px;
    }
    .divSpan {
      vertical-align: top;
      cursor: default;
    }
    .divSpan.line {
      padding-top: 2px;
      span {
        border-right: 1px solid #666;
        display: inline-block;
        height: 16px;
      }
    }
    .moneyClass {
      background: transparent;
      border: none;
      color: #000;
      padding-right: 0;
      padding-left: 0;
    }

    .avatar-container {
      height: 50px;
      margin-right: 30px;
      .avatar-wrapper {
        cursor: pointer;
        margin-top: 5px;
        position: relative;
        .user-avatar {
          width: 40px;
          height: 40px;
          border-radius: 10px;
        }
        .el-icon-caret-bottom {
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }
  }
}
</style>
