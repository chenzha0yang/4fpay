<template>
  <div class="app-container calendar-list-container">
    <!--   search  start  -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <el-form-item class="filter-item" prop="order">
          <md-input
            v-model="form.businessNum"
            icon="search"
            name="name"
            :placeholder="$t('table.input')+$t('table.businessNum')"
          >{{ $t('table.businessNum') }}</md-input>
        </el-form-item>

        <!--平台线路-->
        <el-form-item class="filter-item" v-if="isView.isClient === 1">
          <el-tooltip :content="$t('table.clientName')" placement="top">
            <el-select
              filterable
              clearable
              v-model="form.clientUserId"
              :placeholder="$t('table.clientName')"
              size="medium"
            >
              <el-option
                v-for="item in orderClientName"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>
          </el-tooltip>
        </el-form-item>

        <el-form-item class="filter-item" prop="order" v-if="isView.isAgent === 1">
          <md-input
            v-model="form.agentId"
            icon="search"
            name="name"
            :placeholder="$t('table.input')+$t('table.agentName')"
          >{{ $t('table.agentName') }}</md-input>
        </el-form-item>

        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.confName')" placement="top">
            <el-select v-model="form.payId" :placeholder="$t('table.confName')" size="medium">
              <el-option
                v-for="item in orderConfigLists"
                :key="item.value"
                :label="item.confName"
                :value="item.payId"
              />
            </el-select>
          </el-tooltip>
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
          <el-button type="success" class="el-icon-plus" @click="addNew">{{$t('table.add')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
    <div>
      <template>
        <el-table
          v-loading="screenLoading"
          :data="outShop"
          :element-loading-text="$t('table.searchMsg')"
          border
          fit
          highlight-current-row
          style="width: 100%;"
          :height="heightFn()"
          :empty-text="$t('table.searchdata')"
        >
          <el-table-column align="center" :label="$t('table.id')" prop="Id"/>
          <el-table-column
            align="center"
            :label="$t('table.clientName')"
            prop="clientName"
            v-if="isView.isClient === 1"
          />
          <el-table-column
            align="center"
            :label="$t('table.agentId')"
            prop="agentId"
            v-if="isView.isAgent === 1"
          />
          <el-table-column align="center" :label="$t('table.businessNum')" prop="businessNum"/>
          <el-table-column align="center" :label="$t('table.confName')" prop="configName"></el-table-column>
          <el-table-column align="center" :label="$t('table.whetherOpen')" prop="tag" width="150">
            <template slot-scope="{$index,row}">
              <el-switch
                :disabled="$index === current && open"
                v-model="row.status"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-value="2"
                :inactive-value="1"
                @change="stateChange($index, row,'status')"
              ></el-switch>
            </template>
          </el-table-column>

          <el-table-column
            align="center"
            :label="$t('table.actions')"
            class-name="small-padding fixed-width"
            width="100"
          >
            <template slot-scope="scope">
              <i @click="handleUpdate(scope.$index, scope.row)" class="el-icon-edit"></i>
              <i @click="handleDelete(scope.$index, scope.row)" class="el-icon-delete"></i>
            </template>
          </el-table-column>
        </el-table>
      </template>
    </div>
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
        :total="outShopCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <!--   添加／编辑／详情    -->
    <el-dialog :title="$t('table.Update')" :visible.sync="dialogVisible" width="700px">
      <div v-if="dialogVisible" id="g-dialog">
        <el-form
          ref="dataForm"
          :rules="demoRules"
          :model="temp"
          class="editForm"
          label-width="150px"
        >
          <!--平台线路-->
          <el-form-item
            :label="$t('table.clientName')"
            prop="clientUserId"
            v-if="isView.isClient === 1"
          >
            <el-select
              v-model="temp.clientUserId"
              :placeholder="$t('table.clientName')"
              size="medium"
            >
              <el-option
                v-for="item in orderClientName"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>
          </el-form-item>

          <!--代理线-->
          <el-form-item :label="$t('table.agentId')" prop="agentId" v-if="isView.isAgent === 1">
            <el-input
              :placeholder="$t('table.input')+$t('table.agentId')"
              v-model="temp.agentId"
              maxlength="12"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <!--商户ID-->
          <el-form-item :label="$t('table.businessNum')" prop="businessNum">
            <el-input
              :placeholder="$t('table.input')+$t('table.businessNum')"
              v-model="temp.businessNum"
              maxlength="64"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <!--商户类型-->
          <el-form-item class="filter-item" :label="$t('table.confName')" prop="payId">
            <el-select
              filterable
              v-model="temp.payId"
              :placeholder="$t('table.confName')"
            >
              <el-option
                v-for="item in orderConfigLists"
                :disabled="item.status === 2"
                :key="item.value"
                :label="item.confName"
                :value="item.payId"
              />
            </el-select>
          </el-form-item>

          <!--返回地址-->
          <el-form-item :label="$t('table.callBackURL')" prop="callbackURL">
            <el-input
              :placeholder="$t('table.input')+$t('table.callBackURL')"
              v-model="temp.callbackURL"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <!--是否开启-->
          <el-form-item :label="$t('table.whetherOpen')">
            <el-switch
              v-model="temp.status"
              active-color="#ff4949"
              inactive-color="#13ce66"
              :active-text="$t('tagsView.close')"
              :inactive-text="$t('tagsView.open')"
              :active-value="2"
              :inactive-value="1"
            ></el-switch>
          </el-form-item>

          <el-form-item :label="$t('table.md5Key')" prop="md5Key">
            <el-input :placeholder="$t('table.input')+$t('table.md5Key')" v-model="temp.md5Key"></el-input>
          </el-form-item>
          <el-form-item :label="$t('table.rsaKey')" prop="privateKey">
            <el-input
              type="textarea"
              :autosize="{ minRows: 2, maxRows: 4}"
              :placeholder="$t('table.input')+$t('table.rsaKey')"
              v-model="temp.privateKey"
            ></el-input>
          </el-form-item>

          <el-form-item :label="$t('table.publicKey')" prop="publicKey">
            <el-input
              type="textarea"
              :autosize="{ minRows: 2, maxRows: 4}"
              :placeholder="$t('table.input')+$t('table.publicKey')"
              v-model="temp.publicKey"
            ></el-input>
          </el-form-item>

          <el-form-item :label="$t('table.createdAt')">
            <el-input
              class="w300"
              v-model="temp.createdTime"
              disabled
              :placeholder="$t('table.input')+$t('table.createdAt')"
            ></el-input>
          </el-form-item>

          <el-form-item :label="$t('table.updatedAt')">
            <el-input
              class="w300"
              v-model="temp.updatedTime"
              disabled
              :placeholder="$t('table.input')+$t('table.updatedAt')"
            ></el-input>
          </el-form-item>
          <el-form-item align="center">
            <el-button @click="dialogVisible = false">{{$t('table.cancel')}}</el-button>
            <el-button
              type="primary"
              @click="updateData"
              :disabled="Disable"
              :loading="Loading"
            >{{$t('table.submit')}}</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import MdInput from "@/components/MDinput";
import demoRules from "@/utils/demoRules";
import { resetPage } from "@/utils/sendDataProcess";
import { MessageBox } from "element-ui";
import modifyStatusCallback from "@/utils/modifyStatusCallback";

export default {
  components: {
    MdInput
  },

  data() {
    const payId = (rule, value, callback) => {
      const o = this.orderConfigLists.find(v => v.payId === value);
      if (o !== undefined && o.status === 1) {
        callback();
      } else {
        if (o !== undefined && o.status === 2) {
          callback(new Error(this.$t("valiMsg.payStop")));
        } else {
          callback(new Error(this.$t("valiMsg.payIdMsg")));
        }
      }
    };
    return {
      rules: {},
      demoRules: {
        ...demoRules,
        payId: [
          {
            required: true,
            validator: payId,
            trigger: "blur"
          }
        ]
      },
      current: 0,
      pageHeight: 50,
      enterAble: false,
      form: {
        businessNum: "",
        payId: "",
        agentId: "",
        clientUserId: ""
      },
      listQuery: {
        page: 1,
        limit: 50
      },
      temp: {
        clientUserId: "",
        agentId: "",
        businessNum: "",
        payId: "",
        callbackURL: "",
        status: "",
        md5Key: "",
        privateKey: "",
        publicKey: ""
      },
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,

      Disable: false,
      Loading: false,
      oldObj: {},
      paginationShow: true,
      open: false
    };
  },
  created() {
    this.searhOrderConfigLists();
    // this.searchPayType();
  },
  mounted() {
    this.getClientName();
  },
  computed: {
    ...mapGetters([
      "outShop",
      "outShopCount",
      "orderConfigLists", //商户类型
      "orderClientName", //平台线路
      "payTypeList",
      "isView"
    ])
  },
  methods: {
    ...mapActions([
      "searchOutShopList",
      "editOutShopChild",
      "deleteOutShopChild",
      "searhOrderConfigLists",
      "searchOrderClientName",
      "searchPayType"
    ]),
    handleClean() {
      this.form = {
        businessNum: "",
        payId: "",
        agentId: ""
      };
    },

    getClientName() {
      if (this.isView.isClient === 1) this.searchOrderClientName();
    },

    // 分页

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },
    handleUpdate(index, row) {
      this.searhOrderConfigLists();
      this.getClientName();
      this.searchPayType();

      this.temp = Object.assign({}, row);
      this.current = index;
      this.Loading = false;
      this.Disable = false;
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    addNew() {
      this.$router.push({ name: "addOutShop" });
    },

    stateChange(index, row, str, attr = "open") {
      // const msg =
      //   row.status === 1 ? this.$t("table.state1") : this.$t("table.state2");

      // this.current = index;
      // this.temp = Object.assign({}, row);

      const msg =
        row[str] === 1
          ? this.$t(`switchMsg.${attr}[2]`)
          : this.$t(`switchMsg.${attr}[1]`);

      this.current = index;

      const { Id } = row;
      const data = { Id };
      data[str] = row[str];

      MessageBox.confirm(msg, {
        confirmButtonText: this.$t("alertMsg.confirm"),
        cancelButtonText: this.$t("alertMsg.cencelOperation"),
        type: "warning"
      })
        .then(res => {
          this[attr] = true;
          this.sendUpdateData(data, attr, true);
        })
        .catch(err => {
          this.sendUpdateDataCallback(data);
        });
    },

    // 查询
    handleSearch() {
      this.$refs["searchForm"].validate(valid => {
        if (valid) {
          this.screenLoading = true;
          this.searchAble = true;

          const { agentId, businessNum, payId, clientUserId } = this.form;

          var data = {
            agentId,
            businessNum,
            payId,
            clientUserId
          };

          const flag = resetPage(data, this.oldObj);

          this.listQuery.page = flag ? 1 : this.listQuery.page;

          const { page, limit } = this.listQuery;

          this.oldObj = {
            agentId,
            businessNum,
            payId,
            clientUserId
          };

          data = {
            ...data,
            ...{ page, limit }
          };

          this.searchOutShopList(data)
            .then(rps => {})
            .catch(err => {})
            .finally(() => {
              this.screenLoading = false;
              this.searchAble = false;
              if (flag) {
                this.paginationShow = false;
                this.$nextTick(() => {
                  this.paginationShow = true;
                });
              }
            });
        }
      });
    },

    // 修改
    updateData() {
      this.$refs["dataForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;
          this.sendUpdateData(true);
        }
      });
    },
    sendUpdateData(data = {}, attr = "open", flag) {
      if (!flag) {
        var {
          Id,
          clientUserId,
          agentId,
          businessNum,
          payId,
          callbackURL,
          status,
          md5Key,
          privateKey,
          publicKey
        } = this.temp;

        const c = this.orderConfigLists.find(v => v.payId === payId);

        const d = this.orderClientName.find(v => v.value === clientUserId);

        const configName = c.confName;

        const clientName = d.label;

        data = {
          Id,
          status,
          businessNum,
          payId,
          callbackURL,
          md5Key,
          privateKey,
          publicKey,
          configName,
          clientName
        };

        this.isView.isClient === 1 ? (data.clientUserId = clientUserId) : "";

        this.isView.isAgent === 1 ? (data.agentId = agentId) : "";
      }

      this.editOutShopChild({
        name: "outShop",
        type: "edit",
        current: this.current,
        data
      })
        .then(data => {
          this.dialogVisible = false;
          this.alertMessage(data.rps.msg);
        })
        .catch(err => {
          if (flag) this.sendUpdateDataCallback(data);
          else {
            this.Disable = false;
            this.Loading = false;
          }
        })
        .finally(() => {
          this[attr] = false;
        });
    },

    sendUpdateDataCallback(data) {
      modifyStatusCallback("_putShopList", "outShop", data, this.current);
    },

    //删除
    handleDelete(index, row) {
      this.current = index;
      this.$confirm(this.$t("alertMsg.toDelete"), this.$t("alertMsg.prompt"), {
        confirmButtonText: this.$t("alertMsg.confirm"),
        cancelButtonText: this.$t("alertMsg.cancel"),
        type: "warning"
      }).then(() => {
        const Id = row.Id;
        this.deleteOutShopChild({
          name: "outShop",
          type: "del",
          current: this.current,
          data: { Id }
        })
          .then(data => {
            this.alertMessage(data.rps.msg);
          })
          .catch(err => {
            this.Disable = false;
            this.Loading = false;
          });
      });
    },

    heightFn() {
      var h = document.documentElement.clientHeight - 260;
      return h || 400;
    }
  }
};
</script>

<style scoped lang="scss">
.mixin-components-container {
  background-color: #f0f2f5;
  padding: 30px;
  min-height: calc(100vh - 84px);
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

.editForm {
  height: 90%;
  padding: 10px 10px;
}

.addBtn {
  float: right;
}
</style>
