<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <el-row>
      <el-col :span="5">
        <h2>{{$t('table.add')}}</h2>
      </el-col>
      <el-col :span="2" :offset="17">
        <el-button
          type="info"
          size="small"
          class="el-icon-back backBtn"
          @click="backBtn"
        >{{$t('tagsView.back')}}</el-button>
      </el-col>
    </el-row>

    <div id="form-search-data">
      <el-form ref="addForm" :model="form" :rules="rules" label-width="150px">
        <el-form-item :label="$t('table.account')" prop="account">
          <el-input
            v-model="form.account"
            :placeholder="$t('table.input')+$t('table.account')"
            :maxlength="12"
          ></el-input>
        </el-form-item>

        <el-form-item :label="$t('table.nickname')" prop="uName">
          <el-input
            v-model="form.uName"
            :placeholder="$t('table.input')+$t('table.nickname')"
            :maxlength="12"
          ></el-input>
        </el-form-item>

        <el-form-item :label="$t('table.roles')" prop="roleId">
          <el-select
            class="filter-item"
            v-model="form.roleId"
            :placeholder="$t('table.Pselect') + $t('table.roles')"
          >
            <el-option
              v-for="status in roleList"
              :key="status.Id"
              :label="status.label"
              :value="status.Id"
            ></el-option>
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('table.password')" prop="password">
          <el-input
            type="password"
            v-model="form.password"
            :placeholder="$t('table.input')+$t('table.password')"
            :maxlength="12"
          ></el-input>
        </el-form-item>

        <el-form-item :label="$t('table.repassword')" prop="checkPass">
          <el-input
            type="password"
            v-model="form.checkPass"
            :placeholder="$t('table.input')+$t('table.repassword')"
            :maxlength="12"
          ></el-input>
        </el-form-item>

        <el-form-item :label="$t('table.loginIp')" prop="loginIp">
          <el-input v-model="form.loginIp" type="textarea" :placeholder="$t('table.loginIp')"></el-input>
        </el-form-item>

        <el-form-item :label="$t('table.loginClient')" prop="clientId" v-if="clientTreeShow">
          <el-select
            class="filter-item"
            v-model="form.clientId"
            :placeholder="$t('table.Pselect') + $t('table.loginClient')"
          >
            <el-option
              v-for="status in clientTree"
              :key="status.value"
              :label="status.label"
              :value="status.value"
              v-if="status.value === 0?form.roleId === 1:true"
            ></el-option>
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('table.agent')" prop="agentId" v-if="agentIdShow">
          <el-input
            v-model="form.agentId"
            :placeholder="$t('table.input')+$t('table.agent')"
            :maxlength="12"
          ></el-input>
        </el-form-item>

        <el-form-item :label="$t('table.status')" prop="state">
          <el-switch
            v-model="form.state"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-text="$t('table.disable')"
            :inactive-text="$t('table.normal')"
            :active-value="2"
            :inactive-value="1"
          ></el-switch>
        </el-form-item>

        <el-form-item align="center">
          <el-button type="warning" @click="cleanForm">{{$t('table.reset')}}</el-button>
          <el-button
            type="primary"
            :disabled="Disable"
            :loading="Loading"
            @click="onSubmit"
          >{{$t('table.submit')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import demoRules from "@/utils/demoRules";
import { isPassword } from "@/utils/validate";

export default {
  components: {},

  data() {
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
          if (this.form.checkPass !== "" && this.form.checkPass !== undefined)
            this.$refs.addForm.validateField("checkPass");
          callback();
          break;
      }
    };
    const validateCheckPass = (rule, value, callback) => {
      if (value) {
        if (value === this.form.password) {
          callback();
        } else {
          callback(new Error(this.$t("login.notSame")));
        }
      } else {
        callback(new Error(this.$t("valiMsg.checkPass")));
      }
    };

    return {
      rules: {
        ...demoRules,
        password: [
          { required: true, validator: validatePass, trigger: "blur" }
        ],
        checkPass: [
          { required: true, validator: validateCheckPass, trigger: "blur" }
        ]
      },
      form: {
        account: "",
        roleId: "",
        uName: "",
        state: 1,
        password: "",
        checkPass: "",
        loginIp: "",
        agentId: "",
        clientId: ""
      },
      Loading: false,
      Disable: false,
      clientTree: [],
      roleList: []
    };
  },
  created() {
    this.getRoles();
    this.getclientTree();
  },

  computed: {
    ...mapGetters(["isView", "client"]),
    clientTreeShow() {
      return this.roleList.some(
        item =>
          item.Id === this.form.roleId &&
          item.isClient === 2 &&
          this.isView.isAgent === this.isView.isClient
      );
    },
    agentIdShow() {
      return this.roleList.some(
        item => item.Id === this.form.roleId && item.isAgent === 2
      );
    }
  },
  methods: {
    ...mapActions(["addAccountSet", "getRoleName", "searchOrderClientName"]),

    // 获取角色
    getRoles() {
      this.getRoleName().then(res => {
        this.roleList = res.data;
      });
    },

    getclientTree() {
      if (this.isView.isClient === 1) {
        this.searchOrderClientName().then(rps => {
          this.clientTree = rps.data;
        });
      }
    },

    cleanForm() {
      this.$refs["addForm"].resetFields();
    },

    onSubmit() {
      this.$refs["addForm"].validate(valid => {
        if (valid) {
          this.Loading = true;
          this.Disable = true;

          var {
            account,
            uName,
            state,
            roleId,
            password,
            loginIp,
            clientId,
            agentId
          } = this.form;

          if (this.isView.isClient === 1 && this.isView.isAgent === 1) {
            const tShow = this.roleList.some(
              item =>
                item.Id === roleId && item.isClient === 1 && item.isAgent === 1
            );
            clientId = tShow ? 0 : clientId;
          } else if (this.isView.isClient === 2 && this.isView.isAgent === 1) {
            clientId = this.client;
          }

          const aShow = this.roleList.some(
            item => item.Id === roleId && item.isAgent === 1
          );

          agentId = aShow ? 0 : agentId;

          const data = {
            account,
            uName,
            state,
            roleId,
            password,
            loginIp,
            clientId,
            agentId
          };

          this.addAccountSet({
            name: "accountSet",
            type: "add",
            data
          })
            .then(rps => {
              this.alertMessage(rps.msg);
              this.backBtn();
            })
            .catch(err => {
              this.Disable = false;
              this.Loading = false;
            });
        }
      });
    },

    backBtn() {
      this.$router.push({ name: "accountSet" });
    }
  }
};
</script>
<style scoped>
.mixin-components-container {
  background-color: #f0f2f5;
  padding: 30px;
  min-height: calc(100vh - 84px);
}

.pay-tag {
  margin: 5px;
}

.component-item {
  min-height: 100px;
}

.material-input__component {
  margin-top: 0 !important;
}

.btn-export {
  text-align: right;
}

#form-search-data {
  width: 80%;
  margin: 0 auto;
}

.backBtn {
  margin-top: 17px;
}
</style>
