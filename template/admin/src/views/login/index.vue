<template>
  <div class="login-container">
    <div class="logo"></div>
    <div class="section">
      <div>
        <el-form
          ref="loginForm"
          class="login-form"
          auto-complete="on"
          :model="loginForm"
          :rules="loginRules"
          label-position="left"
        >
          <div class="title-container">
            <h3 class="title">{{ $t('login.title') }}</h3>
            <!-- <lang-select class="set-language"/> -->
          </div>
          <el-form-item prop="username">
            <span class="svg-container svg-container_login">
              <svg-icon icon-class="user"/>
            </span>
            <el-input
              v-model="loginForm.username"
              @change="usernameChange"
              name="username"
              type="text"
              auto-complete="on"
              :placeholder="$t('login.username')"
              @keyup.enter.native="handleLogin"
            />
          </el-form-item>
          <el-form-item prop="password">
            <span class="svg-container">
              <svg-icon icon-class="password"/>
            </span>
            <el-input
              v-model="loginForm.password"
              name="password"
              :type="passwordType"
              auto-complete="on"
              :placeholder="$t('login.password')"
              @keyup.enter.native="handleLogin"
            />
            <span class="show-pwd" @click="showPwd">
              <svg-icon icon-class="eye"/>
            </span>
          </el-form-item>
          <el-form-item prop="verification" class="verification_div">
            <el-input
              @change="vtChange"
              @focus="inputFocus"
              v-model="loginForm.verification"
              name="verification_right"
              type="text"
              :maxlength="4"
              :placeholder="$t('login.correctVerification')"
              @keyup.enter.native="handleLogin"
            />
            <div class="verification" @click="getVerification()">
              <img :src="verificationSrc" :alt="$t('login.verification')">
            </div>
          </el-form-item>
          <el-button
            type="primary"
            style="width:100%;margin-bottom:30px;"
            :disabled="loadingFlag"
            :loading="loading"
            @click.native.prevent="handleLogin"
          >{{ $t('login.logIn') }}</el-button>
        </el-form>
      </div>
    </div>
    <div class="footer"></div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import LangSelect from "@/components/LangSelect";
import { isSpace } from "@/utils/validate";

export default {
  name: "Login",
  components: { LangSelect },
  data() {
    const validateUsername = (rule, value, callback) => {
      const reg = /([a-zA-Z]+)[0-9a-zA-Z_]*$/;
      const regLnegth = /^[0-9A-Za-z_]{5,12}$/;

      reg.test(value) && regLnegth.test(value)
        ? callback()
        : callback(new Error(this.$t("login.sizeError")));
    };

    const validatePassword = (rule, value, callback) => {
      value.length < 6 || isSpace(value)
        ? callback(new Error(this.$t("login.passError")))
        : callback();
    };

    const validateVerification = (rule, value, callback) => {
      const reg = /^[0-9A-Za-z]{4}$/;
      reg.test(value)
        ? callback()
        : callback(new Error(this.$t("login.correctVerification")));
    };

    return {
      verificationSrc: require("../../assets/images/timg_b.png"),
      loginForm: {
        username: "",
        password: "",
        verification: ""
      },
      loginRules: {
        username: [
          { required: true, trigger: "blur", validator: validateUsername }
        ],
        password: [
          { required: true, trigger: "blur", validator: validatePassword }
        ],
        verification: [
          { required: true, trigger: "blur", validator: validateVerification }
        ]
      },
      passwordType: "password",
      loading: false,
      veriFlag: true,
      loadingFlag: true,
      vt: null
    };
  },
  created() {},
  methods: {
    showPwd() {
      if (this.passwordType === "password") {
        this.passwordType = "";
      } else {
        this.passwordType = "password";
      }
    },

    usernameChange(value) {
      this.loginForm.username = value.replace(/[^0-9A-Za-z_]/g, "");
    },

    vtChange(value) {
      this.loginForm.verification = value.replace(/[^0-9A-Za-z]/g, "");
    },

    inputFocus() {
      if (this.vt === null) this.getVerification();
    },

    handleLogin() {
      if (!this.vt) {
        this.operating(this.$t("alertMsg.getCode"), "error");
        return;
      }
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.loading = true;
          this.$store
            .dispatch("Login", this.loginForm)
            .then(response => {
              this.loading = false;
              this.operating(response.msg, "success");
              if (response.code === 200) {
                this.$router.push({ path: "/" });
              }
            })
            .catch(err => {
              this.loading = false;
              if (err.data.code === 1011) {
                this.veriFlag = true;
                this.getVerification();
              }
            });
        }
      });
    },
    getVerification() {
      if (this.veriFlag) {
        this.loadingFlag = false;
        this.veriFlag = false;
        this.vt = Math.random();
        sessionStorage.setItem("vt", this.vt);

        this.verificationSrc = `${
          /* global window */
          window.configs.net_url
        }/index/verification?vt=${this.vt}`;

        setTimeout(() => {
          this.veriFlag = true;
        }, 1000);
      }
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss">
$bg: #2d3a4b;
$light_gray: #eee;

/* reset element-ui css */
.login-container {
  .el-input {
    display: inline-block;
    height: 47px;
    width: 85%;
    input {
      background: transparent;
      border: 0px;
      -webkit-appearance: none;
      border-radius: 0px;
      padding: 12px 5px 12px 15px;
      color: $light_gray;
      height: 47px;
      &:-webkit-autofill {
        -webkit-box-shadow: 0 0 0px 1000px $bg inset !important;
        -webkit-text-fill-color: #fff !important;
      }
    }
  }
  .el-form-item {
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    color: #454545;
  }
}
</style>

<style rel="stylesheet/scss" lang="scss" scoped>
$bg: #2d3a4b;
$bgFFF: #fff;
$dark_gray: #889aa4;
$light_gray: #eee;
$bimg: url(../../assets/images/login_bg.png);
$logo: url(../../assets/images/logo.png);

.logo {
  height: 60px;
  background-image: $logo;
  background-repeat: no-repeat;
  background-size: 180px 40px;
  background-position: 100px 10px;
}
.footer {
  height: 60px;
}
.section {
  height: calc(100vh - 120px);
  min-width: 1000px;
  min-height: 600px;
  background-image: $bimg;
  background-repeat: no-repeat;
  background-size: 100%;
  > div {
    background-color: rgba(0, 0, 0, 0.5);
    height: 100%;
    width: 100%;
  }
}

.login-container {
  position: fixed;
  height: 100%;
  width: 100%;
  background-color: $bgFFF;

  .login-form {
    position: absolute;
    left: 0;
    right: -60%;
    width: 350px;
    padding: 35px 35px 15px 35px;
    border-radius: 5px;
    margin: 120px auto;
    background-color: rgba(45, 58, 75, 0.8);
  }
  .tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 10px;
    span {
      &:first-of-type {
        margin-right: 16px;
      }
    }
  }
  .svg-container {
    padding: 6px 5px 6px 15px;
    color: $dark_gray;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
    &_login {
      font-size: 20px;
    }
  }
  .title-container {
    position: relative;
    .title {
      font-size: 26px;
      font-weight: 400;
      color: $light_gray;
      margin: 0px auto 40px auto;
      text-align: center;
      font-weight: bold;
    }
    .set-language {
      color: #fff;
      position: absolute;
      top: 5px;
      right: 0px;
    }
  }
  .show-pwd {
    position: absolute;
    right: 10px;
    top: 7px;
    font-size: 16px;
    color: $dark_gray;
    cursor: pointer;
    user-select: none;
  }
  .thirdparty-button {
    position: absolute;
    right: 35px;
    bottom: 28px;
  }
  .verification_div {
    position: relative;
  }
  .verification {
    position: absolute;
    top: 1px;
    right: 2px;
    width: 98px;
    height: 43px;
    background-color: #fff;
    img {
      width: 100%;
    }
  }
}
</style>
