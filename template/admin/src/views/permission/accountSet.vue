
<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <el-form-item class="filter-item">
          <!-- 名称 -->
          <md-input
            v-model="form.name"
            icon="search"
            name="userName"
            :placeholder="$t('table.nickname')"
          >{{ $t('table.nickname')}}</md-input>
        </el-form-item>
        <el-form-item class="filter-item">
          <!-- 账户 -->
          <md-input
            v-model="form.account"
            icon="search"
            name="account"
            :placeholder="$t('table.account')"
          >{{ $t('table.account')}}</md-input>
        </el-form-item>
        <el-form-item class="filter-item">
          <el-button
            class="btn"
            type="primary"
            icon="el-icon-search"
            :loading="searchAble"
            size="medium"
            @click="handleSearch"
          >{{ $t('table.search') }}</el-button>

          <el-button
            class="btn"
            icon="el-icon-delete"
            size="medium"
            @click="handleClean"
          >{{ $t('table.reset') }}</el-button>
        </el-form-item>
        <el-form-item class="filter-item addBtn">
          <el-button type="success" class="el-icon-plus" @click="handleCreate">{{$t('table.add')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
    <el-table
      v-loading="screenLoading"
      :data="accountSetList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <el-table-column align="center" :label="$t('table.account')" prop="account"/>
      <el-table-column align="center" :label="$t('table.nickname')" prop="uName"/>
      <el-table-column align="center" :label="$t('table.roles')" prop="roleName"/>

      <el-table-column align="center" :label="$t('table.createdAt')" prop="createdTime"/>
      <el-table-column align="center" :label="$t('table.updatedAt')" prop="updatedTime"/>

      <el-table-column align="center" :label="$t('table.status')" prop="state" width="120">
        <template slot-scope="{row,$index}">
          <el-switch
            v-if="isView.isAgent === 1"
            :disabled="$index === current && stop"
            v-model="row.state"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-value="2"
            :inactive-value="1"
            @change="stateChange($index, row,'state','stop')"
          ></el-switch>
          <el-tag v-else :type="row.state==1?'success':'danger'">{{$t('maintain.' + row.state)}}</el-tag>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        :label="$t('table.actions')"
        class-name="small-padding fixed-width"
        width="100"
      >
        <template slot-scope="scope">
          <el-tooltip :content="$t('table.Update')" placement="top">
            <i @click="handleDetail(scope.$index, scope.row)" class="el-icon-edit"></i>
          </el-tooltip>
          <el-tooltip :content="$t('table.editPassword')" placement="top">
            <i @click="handleEditPsw(scope.$index, scope.row)" class="iconfont icon-mima"></i>
          </el-tooltip>
        </template>
      </el-table-column>
    </el-table>
    <!--  table end  -->
    <!--   page   -->
    <div class="pagination-container" :height="pageHeight">
      <el-pagination
        background
        v-if="paginationShow"
        :current-page.sync="listQuery.page"
        :page-sizes="[50,100,200, 500]"
        :page-size="listQuery.limit"
        layout="total, sizes, prev, pager, next, jumper"
        :total="accountSetCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <!--   添加／编辑／详情    -->
    <el-dialog
      :title="$t('table.'+ textMap[dialogStatus])"
      :visible.sync="dialogVisible"
      width="700px"
    >
      <div id="g-dialog">
        <el-form
          :model="temp"
          class="editForm"
          :rules="demoRules"
          ref="dataForm"
          label-width="100px"
        >
          <div class="form-items-hd">
            <el-form-item
              :label="$t('table.nickname')"
              prop="uName"
              v-if="dialogStatus !== 'EditPwd'"
            >
              <el-input
                v-model="temp.uName"
                :placeholder="$t('table.input')+$t('table.nickname')"
                :maxlength="12"
              ></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.key')" v-if="dialogStatus=='Update'">
              <el-input v-model="temp.rememberToken" :placeholder="$t('table.key')" disabled></el-input>
            </el-form-item>

            <el-form-item
              :label="$t('table.agent')"
              prop="agentId"
              v-if="dialogStatus=='Update'&&temp.agentId !='0'"
            >
              <el-input
                v-model="temp.agentId"
                :placeholder="$t('table.input')+$t('table.agent')"
                :maxlength="12"
              ></el-input>
            </el-form-item>

            <el-form-item
              v-if="dialogStatus === 'EditPwd'"
              :label="$t('table.password')"
              prop="password"
            >
              <el-input
                type="password"
                v-model="temp.password"
                :placeholder="$t('table.input')+$t('table.password')"
                :maxlength="12"
              ></el-input>
            </el-form-item>

            <el-form-item
              v-if="dialogStatus === 'EditPwd'"
              :label="$t('table.repassword')"
              prop="checkPass"
            >
              <el-input
                type="password"
                v-model="temp.checkPass"
                :placeholder="$t('table.input')+$t('table.repassword')"
                :maxlength="12"
              ></el-input>
            </el-form-item>

            <el-form-item
              :label="$t('table.loginIp')"
              v-if="dialogStatus !=='EditPwd'"
              prop="loginIp"
            >
              <el-input v-model="temp.loginIp" type="textarea" :placeholder="$t('table.loginIp')"></el-input>
            </el-form-item>

            <el-form-item
              :label="$t('table.status')"
              v-if="dialogStatus !== 'EditPwd'"
              prop="state"
            >
              <el-switch
                v-model="temp.state"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-text="$t('table.disable')"
                :inactive-text="$t('table.normal')"
                :active-value="2"
                :inactive-value="1"
              ></el-switch>
            </el-form-item>
          </div>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">{{$t('table.cancel')}}</el-button>
          <el-button
            :disabled="Disable"
            v-if="dialogStatus=='Update'"
            :loading="Loading"
            type="primary"
            @click="upData"
          >{{$t('table.confirm')}}</el-button>
          <el-button
            :disabled="Disable"
            v-if="dialogStatus=='EditPwd'"
            :loading="Loading"
            type="primary"
            @click="editPassword"
          >{{$t('table.confirm')}}</el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import MdInput from "@/components/MDinput";
import demoRules from "@/utils/demoRules";
import { isPassword } from "@/utils/validate";
// import { resetPage } from "@/utils/sendDataProcess";
// import { MessageBox } from "element-ui";
// import modifyStatusCallback from "@/utils/modifyStatusCallback";
import HandleSearch from "@/mixins/handleSearch";
import SendUpdateData from "@/mixins/sendUpdateData";

export default {
  components: {
    MdInput
  },

  // 状态信息
  filters: {
    // 账号状态信息
    statusFilter(status) {
      const statusMap = {
        1: "color: #10c14b",
        2: "color: red"
      };
      return statusMap[status];
    }
  },
  data() {
    const validatePass = (rule, value, callback) => {
      switch (isPassword(value)) {
        case "isSpace":
          this.$t("login.spaceMsg");
          callback(new Error(this.$t("login.spaceMsg")));
          break;
        case false:
          callback(new Error(this.$t("login.passWeak")));
          break;
        default:
          if (this.temp.checkPass !== "" && this.temp.checkPass !== undefined) {
            this.$refs.dataForm.validateField("checkPass");
          }
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
      demoRules: {
        ...demoRules,
        password: [
          { required: true, validator: validatePass, trigger: "blur" }
        ],
        checkPass: [
          { required: true, validator: validateCheckPass, trigger: "blur" }
        ]
      },

      rules: {},
      current: 0,
      pageHeight: 50,
      form: {
        name: null,
        account: null
      },
      listQuery: {
        page: 1,
        limit: 50
      },

      dialogStatus: "Update",
      textMap: {
        Update: "Update",
        EditPwd: "editPassword"
      },

      temp: {
        account: "",
        name: "",
        state: 1,
        password: "",
        checkPass: "",
        loginIp: ""
      },
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,

      statusOptions: [
        {
          value: 1,
          label: "normal"
        },
        {
          value: 2,
          label: "disable"
        }
      ],
      clientTree: [],
      // roleList: [],
      Loading: false,
      Disable: false,
      oldObj: {},
      paginationShow: true,
      stop: false
    };
  },

  mixins: [HandleSearch, SendUpdateData],

  computed: {
    ...mapGetters(["accountSetCount", "accountSetList", "isView", "client"])
  },
  methods: {
    ...mapActions([
      "searchAccountSet",
      "putAccountSet",
      // "getRoleName",
      "PeditPassword"
    ]),

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },

    // getclientTree() {
    //   if (this.isView.isClient === 1) {
    //     this.searchOrderClientName().then(rps => {
    //       this.clientTree = rps.data;
    //     });
    //   }
    // },

    // 获取角色
    // getRoles() {
    //   this.getRoleName().then(res => {
    //     this.roleList = res.data;
    //   });
    // },

    // 添加
    handleCreate() {
      this.$router.push({ name: "accountAdd" });
    },

    // 编辑
    handleDetail(index, row) {
      this.temp = Object.assign({}, row);
      this.dialogStatus = "Update";
      this.Loading = false;
      this.Disable = false;
      this.current = index;
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },

    // 修改密码
    handleEditPsw(index, row) {
      this.temp = Object.assign({}, row);
      this.dialogStatus = "EditPwd";
      this.Loading = false;
      this.Disable = false;
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },

    // 查询
    handleSearch() {
      this.$refs["searchForm"].validate(valid => {
        if (valid) {
          const { name, account } = this.form;
          const { page, limit } = this.listQuery;
          const data = { name, account, limit };

          this.screenLoading = true;
          this.searchAble = true;

          this.mixinsHandleSearch({
            data,
            oldData: this.oldObj,
            page,
            searchFn: "searchAccountSet",
            callBacks: this.handleSearchCall,
            eveCallBacks: this.handleSearchEveCall
          });
          this.oldObj = {
            name,
            account,
            limit
          };
        }
      });
    },

    handleSearchEveCall(flag) {
      if (flag) {
        this.paginationShow = false;
        this.listQuery.page = 1;
        this.$nextTick(() => {
          this.paginationShow = true;
        });
      }
    },

    handleSearchCall() {
      this.screenLoading = false;
      this.searchAble = false;
    },

    handleClean() {
      this.form = {
        name: null,
        account: null
      };
    },

    stateChange(index, row, str, attr = "open") {
      this.current = index;
      const { Id } = row;
      const data = { Id };
      data[str] = row[str];
      this[attr] = true;

      this.mixinsSwitchChange({
        data,
        index,
        attr,
        str,
        putStoreFn: "putAccountSet",
        backtrackFn: "_putAccount",
        type: "edit",
        dataName: "accountSet",
        finallyCallback: () => {
          this[attr] = false;
        }
      });

      // const msg =
      //   row[str] === 1
      //     ? this.$t(`switchMsg.${attr}[2]`)
      //     : this.$t(`switchMsg.${attr}[1]`);

      // MessageBox.confirm(msg, {
      //   confirmButtonText: this.$t("alertMsg.confirm"),
      //   cancelButtonText: this.$t("alertMsg.cencelOperation"),
      //   type: "warning"
      // })
      //   .then(res => {
      //     this[attr] = true;
      //     this.sendUpdateData(data, attr, true);
      //   })
      //   .catch(err => {
      //     this.sendUpdateDataCallback(data);
      //   });
    },

    // 编辑提交
    upData() {
      this.$refs["dataForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          const { Id, uName, state, loginIp, agentId } = this.temp;
          const data = { Id, uName, state, loginIp };
          if (agentId != "0") data.agentId = agentId;

          this.mixinsSendUpdateData({
            data,
            index: this.current,
            putStoreFn: "putAccountSet",
            backtrackFn: "_putAccount",
            thenCallback: this.upDataThenCallback,
            catchCallback: this.upDataCatchCallback,
            type: "edit",
            dataName: "accountSet"
          });
        }
      });
    },

    upDataThenCallback() {
      this.dialogVisible = false;
    },
    upDataCatchCallback() {
      this.Disable = false;
      this.Loading = false;
    },

    // processDataFn(data = {}, flag) {
    //   if (!flag) {
    //     const { Id, uName, state, loginIp, agentId } = this.temp;
    //     data = { Id, uName, state, loginIp };
    //     if (agentId != "0") data.agentId = agentId;
    //   }
    //   return data;
    // },

    // sendUpdateData(data = {}, attr = "open", flag) {
    //   // if (!flag) {
    //   //   const { Id, uName, state, loginIp, agentId } = this.temp;
    //   //   data = { Id, uName, state, loginIp };

    //   //   if (agentId != "0") data.agentId = agentId;
    //   // }

    //   this.putAccountSet({
    //     name: "accountSet",
    //     type: "edit",
    //     data,
    //     current: this.current
    //   })
    //     .then(res => {
    //       this.alertMessage(res.msg);
    //       this.dialogVisible = false;
    //     })
    //     .catch(err => {
    //       if (flag) this.sendUpdateDataCallback(data);
    //       else {
    //         this.Disable = false;
    //         this.Loading = false;
    //       }
    //     })
    //     .finally(() => {
    //       this[attr] = false;
    //     });
    // },

    // sendUpdateDataCallback(data) {
    //   modifyStatusCallback("_putAccount", "accountSet", data, this.current);
    // },

    // 修改密码提交
    editPassword() {
      this.$refs["dataForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          var { Id, password } = this.temp;

          const data = { Id, password };

          this.PeditPassword(data)
            .then(res => {
              this.alertMessage(res.msg);
              this.dialogVisible = false;
            })
            .catch(err => {
              this.Disable = false;
              this.Loading = false;
            });
        }
      });
    },

    heightFn() {
      const h = document.documentElement.clientHeight - 260;
      return h || 400;
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
.el-form.clear {
  margin-bottom: 10px;
}

#g-dialog {
  border: 1px solid #ccc;
  border-radius: 5px;
}

#g-dialog .item {
  min-height: 45px;
  line-height: 45px;
  display: flex;
  border-bottom: 1px solid #eee;
}

#g-dialog .item:last-child {
  border-bottom-width: 0;
}

#g-dialog .item .left {
  width: 100px;
  font-weight: 500;
  padding-left: 10px;
}

.btn-export {
  text-align: right;
}
.addBtn {
  float: right;
}
.editForm {
  height: 90%;
  padding: 10px 10px 0;
}
.dialog-footer {
  text-align: center;
  padding-bottom: 10px;
}
</style>
