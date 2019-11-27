<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <!-- 查询操作 -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <el-form-item class="filter-item">
          <!-- 账户名称 -->
          <md-input
            v-model="form.uName"
            icon="search"
            name="userName"
            :placeholder="$t('table.bankName')"
          >{{$t('table.bankName')}}</md-input>
        </el-form-item>

        <!-- 商户类型 -->
        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.confName')" placement="top">
            <el-select
              v-model="form.payId"
              clearable
              filterable
              :placeholder="$t('table.confName')"
            >
              <el-option
                v-for="item in orderConfigLists"
                :key="item.value"
                :label="item.confName"
                :value="item.payId"
              ></el-option>
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

        <el-form-item
          class="filter-item addBtn"
          v-if="isView.isAgent === 1 && isView.isClient === 1"
        >
          <el-button type="success" class="el-icon-plus" @click="addNew">{{$t('table.add')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
    <!-- 结果列表 -->
    <el-table
      v-loading="screenLoading"
      :data="incomeBankList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <!--<el-table-column align="center" sortable :label="$t('table.id')" prop="Id" width="100"/>-->
      <el-table-column align="center" :label="$t('table.confName')" prop="confName"/>
      <el-table-column align="center" :label="$t('table.bankName')" prop="uName"/>
      <el-table-column align="center" :label="$t('table.bankCode')" prop="bankCode"/>

      <el-table-column align="center" :label="$t('table.createdAt')" prop="createdAt"/>
      <el-table-column align="center" :label="$t('table.updatedAt')" prop="updatedAt"/>

      <el-table-column align="center" :label="$t('table.bankStatus')" prop="tag" width="150">
        <template slot-scope="{row,$index}">
          <el-switch
            v-if="isView.isAgent === 1 && isView.isClient === 1"
            :disabled="$index === current && open"
            v-model="row.state"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-value="2"
            :inactive-value="1"
            @change="stateChange($index, row,'state')"
          ></el-switch>
          <el-tag v-else :type="row.state==1?'success':'danger'">{{$t('maintain.' + row.state)}}</el-tag>
          <!-- <el-tag
            :type="scope.row.state==1?'success':'danger'"
          >{{$t('maintain.' + scope.row.state)}}</el-tag>-->
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        :label="$t('table.actions')"
        class-name="small-padding fixed-width"
        width="100"
        v-if="isView.isAgent === 1 && isView.isClient === 1"
      >
        <template slot-scope="scope">
          <i @click="handleUpdate(scope.$index, scope.row)" class="el-icon-edit"></i>
          <i @click="handleDelete(scope.$index, scope.row)" class="el-icon-delete"></i>
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
        :total="incomeBankCount"
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
          label-width="100px"
        >
          <!-- 商户类型 -->
          <el-form-item :label="$t('table.confName')" prop="payId">
            <el-select v-model="temp.payId" filterable :placeholder="$t('table.confName')">
              <el-option
                v-for="item in orderConfigLists"
                :disabled="item.status === 2"
                :key="item.value"
                :label="item.confName"
                :value="item.payId"
              ></el-option>
            </el-select>
          </el-form-item>

          <el-form-item :label="$t('table.bankName')" prop="uName">
            <el-input
              :placeholder="$t('table.input')+$t('table.bankName')"
              v-model="temp.uName"
              maxlength="24"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.bankCode')" prop="bankCode">
            <el-input
              :placeholder="$t('table.input')+$t('table.bankCode')"
              v-model="temp.bankCode"
              maxlength="24"
              @change="bankCodeChange"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.bankStatus')" prop="state">
            <el-switch
              v-model="temp.state"
              active-color="#ff4949"
              inactive-color="#13ce66"
              :active-text="$t('tagsView.close')"
              :inactive-text="$t('tagsView.open')"
              :active-value="2"
              :inactive-value="1"
            ></el-switch>
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
      rules: {},
      countsOnline: 0,
      current: 0,
      pageHeight: 50,
      form: {
        uName: "",
        payId: ""
      },
      listQuery: {
        page: 1,
        limit: 50
      },
      temp: {
        Id: "",
        bankCode: "",
        confName: "",
        createdAt: "",
        payId: 1,
        state: 1,
        uName: "",
        updatedAt: ""
      },
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,
      stopAble: false,
      enAble: false,
      Disable: true,
      Loading: false,
      oldObj: {},
      paginationShow: true,
      open: false
    };
  },
  created() {
    this.searhOrderConfigLists();
  },
  mounted() {
    if (this.autoPayId !== null) {
      this.form.payId = this.autoPayId;
      this.handleSearch();
      // this.TriparJumpBank(null);
    }
  },

  computed: {
    ...mapGetters([
      "incomeBankList",
      "incomeBankCount",
      "orderConfigLists",
      "autoPayId",
      "isView"
    ])
  },
  methods: {
    ...mapActions([
      "searchIncomeBank",
      "searhOrderConfigLists",
      "editIncomeBank",
      "deleteIncomeBank",
      // "TriparJumpBank"
      // "_putbank"
    ]),

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },

    bankCodeChange(value) {
      this.temp.bankCode = value.replace(/[^a-zA-Z_\-]/g, "");
    },

    handleUpdate(index, row) {
      this.Disable = true;
      this.temp = Object.assign({}, row);
      this.searhOrderConfigLists().then(res => {
        this.Disable = false;
      });

      this.Loading = false;
      this.current = index;
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
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
          // this._putbank({
          //   name: "incomeBankList",
          //   type: "edit",
          //   params: { state: row.state === 1 ? 2 : 1 },
          //   current: this.current
          // });
        });
    },
    sendUpdateData(data = {}, attr = "open", flag) {
      if (!flag) {
        const { Id, bankCode, payId, state, uName } = this.temp;

        const o = this.orderConfigLists.find(v => v.payId === payId);
        const confName = o.confName;
        data = { Id, bankCode, payId, state, uName, confName };
      }

      this.editIncomeBank({
        name: "incomeBankList",
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
    // 查询
    handleSearch() {
      this.$refs["searchForm"].validate(valid => {
        if (valid) {
          this.screenLoading = true;
          this.searchAble = true;

          const { payId, uName } = this.form;

          var data = { payId, uName };

          const flag = resetPage(data, this.oldObj);

          this.listQuery.page = flag ? 1 : this.listQuery.page;

          const { page, limit } = this.listQuery;

          this.oldObj = { payId, uName };

          data = {
            ...data,
            ...{ page, limit }
          };

          this.searchIncomeBank(data)
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

    //      修改
    updateData() {
      this.$refs["dataForm"].validate(valid => {
        if (valid) {
          this.Loading = true;
          this.Disable = true;
          this.sendUpdateData();
        }
      });
    },

    sendUpdateDataCallback(data) {
      modifyStatusCallback("_putbank", "incomeBankList", data, this.current);
    },

    // 删除
    handleDelete(index, row) {
      this.current = index;
      this.enAble = true;
      this.$confirm(this.$t("alertMsg.toDelete"), this.$t("alertMsg.prompt"), {
        confirmButtonText: this.$t("alertMsg.confirm"),
        cancelButtonText: this.$t("alertMsg.cancel"),
        type: "warning"
      }).then(() => {
        const Id = row.Id;
        this.deleteIncomeBank({
          name: "incomeBankList",
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

    // filterConfName(item) {
    //   for (const conf of this.orderConfigLists) {
    //     if (conf.payId === item) {
    //       return conf.confName;
    //     }
    //   }
    // },

    // 重置
    handleClean() {
      this.form = {
        payId: "",
        uName: ""
      };
    },

    addNew() {
      this.$router.push({ name: "addInBank" });
    },

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

.w120 {
  width: 120px;
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

.editForm {
  height: 90%;
  padding: 10px 10px;
}

.addBtn {
  float: right;
}
</style>
