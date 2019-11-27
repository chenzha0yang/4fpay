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
            <el-select
              filterable
              clearable
              v-model="form.payId"
              :placeholder="$t('table.confName')"
              size="medium"
            >
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
          :data="inShop"
          :element-loading-text="$t('table.searchMsg')"
          border
          fit
          highlight-current-row
          style="width: 100%;"
          :height="heightFn()"
          :empty-text="$t('table.searchdata')"
        >
          <el-table-column align="center" :label="$t('table.id')" prop="Id" width="80"/>
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
          <el-table-column align="center" :label="$t('table.confName')" prop="configName"/>

          <el-table-column align="center" :label="$t('table.payWay')" prop="typeName">
            <template slot-scope="scope">
              <el-tag disable-transitions type="success" class="pay-tag">{{ scope.row.typeName }}</el-tag>
            </template>
          </el-table-column>

          <el-table-column align="center" :label="$t('table.whetherOpen')" prop="tag" width="120">
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

          <el-table-column align="center" :label="$t('table.isApp')" prop="tag" width="120">
            <template slot-scope="{$index,row}">
              <el-switch
                v-model="row.isApp"
                :disabled="$index === current && jump"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-value="2"
                :inactive-value="1"
                @change="stateChange($index, row,'isApp','jump')"
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
        :total="inShopCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <!--   添加／编辑／详情    -->
    <el-dialog :title="$t('table.Update')" :visible.sync="dialogVisible" width="700px">
      <div v-if="dialogVisible" id="g-dialog">
        <el-form
          ref="dataForm"
          :model="temp"
          class="editForm"
          :rules="demoRules"
          label-width="150px"
        >
          <!--平台线路-->
          <el-form-item
            :label="$t('table.clientName')"
            prop="clientUserId"
            v-if="isView.isClient === 1"
          >
            <label >{{orderClientName[0].label}}</label>
<!--            <el-select-->
<!--              v-model="temp.clientUserId"-->
<!--              :placeholder="$t('table.clientName')"-->
<!--              size="medium"-->
<!--            >-->
<!--              <el-option-->
<!--                v-for="item in orderClientName"-->
<!--                :key="item.value"-->
<!--                :label="item.label"-->
<!--                :value="item.value"-->
<!--              />-->
<!--            </el-select>-->
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

          <!-- 是否跳转 -->
          <el-form-item :label="$t('table.isApp')">
            <el-switch
              v-model="temp.isApp"
              active-color="#ff4949"
              inactive-color="#13ce66"
              :active-text="$t('tagsView.close')"
              :inactive-text="$t('tagsView.open')"
              :active-value="2"
              :inactive-value="1"
            ></el-switch>
          </el-form-item>

          <el-form-item :label="$t('table.md5Key')">
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

          <!--商户类型-->
          <el-form-item class="filter-item" :label="$t('table.confName')" prop="payId">
            <el-select
              filterable
              v-model="temp.payId"
              :placeholder="$t('table.confName')"
              @change="payIdChange"
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

          <!--支付方式-->
          <el-form-item :label="$t('table.payWay')" prop="typeId">
            <el-select
              class="filter-item"
              v-model="temp.typeId"
              filterable
              :disabled="selectDisabled"
            >
              <el-option
                v-for="item in payTypeList"
                :key="item.value"
                :label="(item.typeName)"
                :value="item.typeId"
              ></el-option>
            </el-select>
          </el-form-item>

          <el-form-item :label="$t('table.merURL')" prop="merURL">
            <el-input :placeholder="$t('table.input')+$t('table.merURL')" v-model="temp.merURL">
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.payCode')" prop="payCode">
            <el-input :placeholder="$t('table.input')+$t('table.payCode')" v-model="temp.payCode">
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('payName.extendName')" prop="msgOne" v-if="extendNameShow">
            <el-input
              :placeholder="$t('table.input')+$t('payName.extendName')"
              v-model="temp.msgOne"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.createdAt')">
            <el-input
              v-model="temp.createdTime"
              disabled
              :placeholder="$t('table.input')+$t('table.createdAt')"
            ></el-input>
          </el-form-item>
          <el-form-item :label="$t('table.updatedAt')">
            <el-input
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
import { isSpace } from "@/utils/validate";
import { MessageBox } from "element-ui";
import modifyStatusCallback from "@/utils/modifyStatusCallback";

export default {
  components: {
    MdInput
  },

  data() {
    const payCode = (rule, value, callback) => {
      const reg = /([0-9a-zA-Z]+)[0-9a-zA-Z_@]*$/;
      if (isSpace(value)) {
        callback(new Error(this.$t("valiMsg.spaceMsg")));
      } else {
        if (reg.test(value)) {
          callback();
        } else {
          if (value === "") {
            callback();
          } else {
            callback(new Error(this.$t("valiMsg.payCode_")));
          }
        }
      }
    };
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

    const typeId = (rule, value, callback) => {
      // const o = this.payTypeList.some(v => v.typeId === value);
      // console.log(o)
      // console.log(value)
      // console.log(this.payTypeList)
      // if (o === undefined) {
      //   callback(new Error(this.$t("valiMsg.typeId")));
      // } else {
      //   callback();
      // }
      if (this.payTypeList.some(v => v.typeId === value)) {
        callback();
      } else {
        callback(new Error(this.$t("valiMsg.typeId")));
      }
    };

    return {
      rules: {},
      demoRules: {
        ...demoRules,

        payCode: [
          {
            required: false,
            validator: payCode,
            trigger: "blur"
          }
        ],
        payId: [
          {
            required: true,
            validator: payId,
            trigger: "blur"
          }
        ],
        typeId: [
          {
            required: true,
            validator: typeId,
            trigger: "blur"
          }
        ]
      },
      current: 0,
      pageHeight: 50,
      form: {
        businessNum: "",
        clientUserId: "",
        payId: "",
        agentId: ""
      },
      listQuery: {
        page: 1,
        limit: 50
      },
      temp: {
        Id: "",
        clientUserId: "",
        businessNum: "",
        agentId: "",
        callbackURL: "",
        status: "",
        md5Key: "",
        msgOne: "",
        payCode: "",
        merURL: "",
        typeId: "",
        isApp: "",
        privateKey: "",
        publicKey: "",
        payId: ""
      },
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,
      Disable: true,
      Loading: false,
      oldObj: {},
      paginationShow: true,
      payTypeList: [],
      selectDisabled: true,
      open: false,
      jump: false
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
      "inShop",
      "inShopCount",
      "orderConfigLists", //商户类型
      "orderClientName", //平台线路
      // "payTypeList",
      "isView"
    ]),
    extendNameShow() {
      return this.orderConfigLists.some(
        v => v.payId === this.temp.payId && v.extendName !== ""
      );
    }
  },
  methods: {
    ...mapActions([
      "searchInShopList",
      "editManageChild",
      "deleteManageChild",
      "searhOrderConfigLists",
      "searchOrderClientName",
      "ownType"
      // "_putShopList"
    ]),
    handleClean() {
      this.form = {
        businessNum: "",
        clientUserId: "",
        payId: "",
        agentId: ""
      };
    },

    getClientName() {
      if (this.isView.isClient === 1) this.searchOrderClientName();
    },

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },

    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },

    payIdChange(value) {
      this.selectDisabled = true;
      this.temp.typeId = "";
      this.ownType({ payId: value })
        .then(res => {
          this.payTypeList = res.data;
          this.selectDisabled = false;
          this.Disable = false;
        })
        .catch(err => {})
        .finally(() => {});
    },

    handleUpdate(index, row) {
      this.Disable = true;
      this.searhOrderConfigLists();
      this.getClientName();
      this.payIdChange(row.payId);
      this.temp = Object.assign({}, row);
      this.current = index;
      this.Loading = false;
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    addNew() {
      this.$router.push({ name: "changeShop" });
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

          this.searchInShopList(data)
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

    stateChange(index, row, str, attr = "open") {
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

    // 修改
    updateData() {
      this.$refs["dataForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;
          this.sendUpdateData();
        }
      });
    },

    sendUpdateData(data = {}, attr = "open", flag) {
      if (!flag) {
        var {
          Id,
          clientUserId,
          businessNum,
          agentId,
          callbackURL,
          status,
          md5Key,
          msgOne,
          payCode,
          merURL,
          typeId,
          isApp,
          privateKey,
          publicKey,
          payId
        } = this.temp;

        const o = this.payTypeList.find(v => v.typeId === typeId);
        const typeName = o.typeName;

        const c = this.orderConfigLists.find(v => v.payId === payId);
        const configName = c.confName;

        const d = this.orderClientName.find(v => v.value === clientUserId);
        const clientName = d.label;

        data = {
          Id,
          status,
          isApp,
          businessNum,
          callbackURL,
          md5Key,
          payCode,
          merURL,
          typeId,
          privateKey,
          publicKey,
          payId,
          typeName,
          configName,
          clientName
        };

        const msgOneFlag = this.orderConfigLists.some(
          v => v.payId === payId && v.extendName !== ""
        );

        if (msgOneFlag) data.msgOndata = msgOne;
        this.isView.isClient === 1 ? (data.clientUserId = clientUserId) : "";

        this.isView.isAgent === 1 ? (data.agentId = agentId) : "";
      }

      this.editManageChild({
        name: "inShop",
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
      modifyStatusCallback("_putShopList", "inShop", data, this.current);
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
        this.deleteManageChild({
          name: "inShop",
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

    // filterClient(item) {
    //   for (const conf of this.orderClientName) {
    //     if (conf.value === item) {
    //       return conf.label;
    //     }
    //   }
    // },

    // filterPay(item) {
    //   for (const pay of this.payTypeList) {
    //     if (pay.typeId === item) {
    //       return pay.typeName;
    //     }
    //   }
    // },

    // filterConfName(item) {
    //   for (const conf of this.orderConfigLists) {
    //     if (conf.payId === item) {
    //       return conf.confName;
    //     }
    //   }
    // },

    heightFn() {
      var h = document.documentElement.clientHeight - 260;
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
