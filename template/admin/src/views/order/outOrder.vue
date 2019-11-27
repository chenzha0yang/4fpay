<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <!--代理线路-->
        <el-form-item class="filter-item" prop="order" v-if="isView.isAgent === 1">
          <md-input
            v-model="form.agentId"
            icon="search"
            name="name"
            :placeholder="$t('table.input')+$t('table.agentName')"
          >{{ $t('table.agentName') }}</md-input>
        </el-form-item>

        <!-- 商户ID  -->
        <el-form-item class="filter-item" prop="order">
          <md-input
            class="w300"
            v-model="form.businessNum"
            icon="edit"
            name="businessNum"
            :placeholder="$t('table.businessNum')"
          >{{ $t('table.businessNum') }}</md-input>
        </el-form-item>

        <!-- 订单号 -->
        <el-form-item class="filter-item" prop="order">
          <md-input
            class="w300"
            v-model="form.orderNumber"
            icon="search"
            name="orderNumber"
            :placeholder="$t('table.input')+$t('table.orderNumber')"
          >{{ $t('table.orderNumber') }}</md-input>
        </el-form-item>

        <!-- 商户类型 -->
        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.confName')" placement="top">
            <el-select
              class="w120"
              v-model="form.payId"
              filterable
              clearable
              :placeholder="$t('table.confName')"
            >
              <el-option
                v-for="item in orderConfigLists"
                :key="item.payId"
                :label="item.confName"
                :value="item.payId"
              ></el-option>
            </el-select>
          </el-tooltip>
        </el-form-item>

        <!-- 订单状态 -->
        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.orderStatus')" placement="top">
            <el-select
              filterable
              clearable
              v-model="form.isStatus"
              class="w120"
              :placeholder="$t('table.orderStatus') "
              size="medium"
            >
              <el-option
                v-for="item in payStatusOptions"
                :key="item.value"
                :label="$t('table.' + item.label)"
                :value="item.value"
              />
            </el-select>
          </el-tooltip>
        </el-form-item>
        <!-- <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.send') + $t('table.status')" placement="top">
            <el-select
              v-model="form.send_status"
              class="w120"
              :placeholder="$t('table.send') + $t('table.status') "
              size="medium"
            >
              <el-option
                v-for="item in form.sendStatusOptions"
                :key="item.value"
                :label="$t('table.' + item.label)"
                :value="item.value"
              />
            </el-select>
          </el-tooltip>
        </el-form-item>-->
        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.orders') + $t('table.date')" placement="top">
            <el-date-picker
              v-model="form.dateValue"
              :picker-options="pickerOptions"
              type="datetimerange"
              :range-separator="$t('table.to')"
              :start-placeholder="$t('table.start') + $t('table.date')"
              :end-placeholder="$t('table.end') + $t('table.date')"
              align="right"
              :default-time="['00:00:00','23:59:59']"
            />
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
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
    <el-table
      v-loading="screenLoading"
      :data="outOrders"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <el-table-column align="center" :label="$t('table.clientName')" width="150" v-if="isView.isClient === 1">
        <template slot-scope="scope">
          <div slot="clientName" class="name-wrapper">
            <el-tag size="medium">{{scope.row.clientName}}</el-tag>
          </div>
        </template>
      </el-table-column>
      <el-table-column align="center" :label="$t('table.agentId')" width="150" prop="agentId" v-if="isView.isAgent === 1"/>
      <el-table-column
        align="center"
        :label="$t('table.orderNumber')"
        prop="orderNumber"
        width="200"
      />
      <el-table-column
        align="center"
        :label="$t('table.businessNum')"
        prop="businessNum"
        width="200"
      />
      <el-table-column align="center" :label="$t('table.confName')" prop="confName" width="150"/>
      <el-table-column align="center" :label="$t('table.payMoney')" prop="payMoney" width="120"/>
      <el-table-column
        align="center"
        :label="$t('table.orderStatus')"
        width="120"
        prop="tag"
        :filters="[{text:$t('table.pay')+$t('table.success'),value:1},{text:$t('table.pay')+$t('table.success'),value:2},{text:$t('table.pending'),value:0}]"
        :filter-method="filterTag"
      >
        <template slot-scope="scope">
          <el-tag
            disable-transitions
            :type="scope.row.isStatus==1?'success':scope.row.isStatus==2?'danger':'warning'"
          >{{scope.row.isStatus | changeIsStatus}}</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        :label="$t('table.dispatchStatus')"
        width="120"
        prop="tag"
        :filters="[{text:$t('table.send')+$t('table.success'),value:1},{text:$t('table.send')+$t('table.error'),value:2},{text:$t('table.pending'),value:0}]"
        :filter-method="filterTag"
      >
        <template slot-scope="scope">
          <el-tag
            disable-transitions
            :type="scope.row.isSued==1?'success':scope.row.isSued==2?'danger':'warning'"
          >{{scope.row.isSued | changeIsSued}}</el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" :label="$t('table.createdAt')" prop="createdAt" width="200"/>
      <el-table-column align="center" :label="$t('table.updatedAt')" prop="updatedAt" width="200"/>

      <el-table-column align="center" :label="$t('table.actions')" width="100" fixed="right">
        <template slot-scope="scope">
          <!-- <el-button
            size="mini"
            type="primary"
            v-if="scope.row.isSued === 0 && scope.row.isStatus === 1"
            :loading="stopAble && current  == scope.$index"
            @click="handleSend(scope.$index, scope.row)"
          >{{ $t('table.send') }}</el-button>-->
          <el-button
            size="mini"
            type="primary"
            :loading="stopAble && current  == scope.$index"
            v-if="scope.row.isStatus === 1 && scope.row.isSued === 0"
            :disabled="loadDisabled && current  == scope.$index"
            @click="handleSend(scope.$index, scope.row)"
          >{{ $t('table.send') }}</el-button>
          <el-button
            size="mini"
            type="success"
            :disabled="loadDisabled && current  == scope.$index"
            :loading="stopAble && current  == scope.$index"
            v-else-if="scope.row.isStatus === 1 && scope.row.isSued !== 0"
            @click="handleInfo(scope.$index, scope.row)"
          >{{ $t('table.details') }}</el-button>
          <el-button size="mini" type="info" disabled v-else>{{ $t('table.send') }}</el-button>
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
        :total="outOrdersCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <!--   添加／编辑／详情    -->
    <el-dialog :title="$t('table.details')" :visible.sync="dialogVisible" width="700px">
      <div id="g-dialog">
        <el-form :model="temp" class="editForm" ref="dataForm" label-width="150px">
          <el-form-item :label="$t('table.clientOrderNumber')">
            <el-input v-model="temp.orderNumber"></el-input>
          </el-form-item>
          <el-form-item :label="$t('table.autoSend')">
            <el-input :value="temp.isAutoSend | autoSend"></el-input>
          </el-form-item>
          <el-form-item :label="$t('table.callbackUrl')">
            <el-input v-model="temp.callbackUrl"></el-input>
          </el-form-item>
          <el-form-item :label="$t('table.httpCode')">
            <el-input v-model="temp.httpCode"></el-input>
          </el-form-item>
          <el-form-item :label="$t('table.returnMsg')">
            <el-input v-model="temp.returnMsg"></el-input>
          </el-form-item>
          <el-form-item :label="$t('table.sendMsg')">
            <el-input :autosize="{ minRows: 2, maxRows: 4}" type="textarea" v-model="temp.sendMsg"></el-input>
          </el-form-item>
        </el-form>
        <div class="dialog-footer">
          <el-button @click="dialogVisible = false">{{$t('table.cancel')}}</el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import MdInput from "@/components/MDinput";
import pickerOptions from "@/utils/pickerOptions";
import { thatDayAndtomorrow, parseTime } from "@/utils/index";
import i18n from "@/lang/index";
import { resetPage } from "@/utils/sendDataProcess";

export default {
  components: {
    MdInput
  },

  filters: {
    // 订单状态
    changeIsStatus(status) {
      const isStatus = {
        1:
          i18n.messages[i18n.locale].table.pay +
          i18n.messages[i18n.locale].table.success,
        2:
          i18n.messages[i18n.locale].table.pay +
          i18n.messages[i18n.locale].table.error,
        0: i18n.messages[i18n.locale].table.pending
      };
      return isStatus[status];
    },

    // 状态
    changeIsSued(status) {
      const isSued = {
        1:
          i18n.messages[i18n.locale].table.send +
          i18n.messages[i18n.locale].table.success,
        2:
          i18n.messages[i18n.locale].table.send +
          i18n.messages[i18n.locale].table.error,
        0: i18n.messages[i18n.locale].table.pending
      };
      return isSued[status];
    },

    autoSend(status) {
      const isStatus = {
        1: i18n.messages[i18n.locale].table.autoSend,
        2: i18n.messages[i18n.locale].table.manualSend
      };
      return isStatus[status];
    }
  },
  data() {
    return {
      rules: {},
      pageHeight: 50,
      pickerOptions: pickerOptions,
      form: {
        orderNumber: "", // 订单号
        clientUserId: null, // 线路id
        businessNum: "", // 商户号
        payId: null, // 商户类型
        isStatus: "", // 订单状态
        dateValue: "" // 查询时间
      },
      payStatusOptions: [
        // 支付状态选择
        {
          value: "",
          label: "all"
        },
        {
          value: 1,
          label: "success"
        },
        {
          value: 2,
          label: "error"
        },
        {
          value: 0,
          label: "pending"
        }
      ],
      sendStatusOptions: [
        // 下发状态选择
        {
          value: "",
          label: "all"
        },
        {
          value: 1,
          label: "success"
        },
        {
          value: 2,
          label: "error"
        },
        {
          value: 0,
          label: "pending"
        }
      ],
      listQuery: {
        page: 1,
        limit: 50
      },
      screenLoading: false,
      searchAble: false,
      stopAble: false,
      oldObj: {},
      paginationShow: true,
      loadDisabled: false,
      current: null,
      dialogVisible: false,
      temp: {}
    };
  },
  created() {
    // this.searchOrderClientName();
    this.searhOrderConfigLists();
  },

  computed: {
    ...mapGetters([
      "outOrders",
      "outOrdersCount",
      'isView',
      "orderConfigLists" //商户类型
    ])
  },
  methods: {
    ...mapActions([
      "searchOutOrderList",
      "searhOrderConfigLists",
      "sendOutOrder",
      "outOrderFind"
    ]),

    // 分页

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },

    //下发
    handleSend(index, row) {
      this.current = index;
      this.$confirm(this.$t("alertMsg.toSend"), this.$t("alertMsg.prompt"), {
        confirmButtonText: this.$t("alertMsg.confirm"),
        cancelButtonText: this.$t("alertMsg.cancel"),
        type: "warning"
      }).then(() => {
        const outOrderId = row.outOrderId;
        this.stopAble = true;
        this.sendOutOrder({
          outOrderId: outOrderId
        })
          .then(rps => {
            row.isSued = 1;
            this.alertMessage(rps.msg);
            this.stopAble = false;
          })
          .catch(err => {
            this.stopAble = false;
            this.Disable = false;
            this.Loading = false;
          });
      });
    },

    // 查询
    handleSearch() {
      this.$refs["searchForm"].validate(valid => {
        if (valid) {
          this.screenLoading = true;
          this.searchAble = true;

          const {
            orderNumber,
            payId,
            isStatus,
            businessNum,
            clientUserId,
            agentId,
            payWay
          } = this.form;

          var startDate, endDate;
          if (Array.isArray(this.form.dateValue)) {
            startDate = parseTime(this.form.dateValue[0]);
            endDate = parseTime(this.form.dateValue[1]);
          }

          var data = {
            orderNumber,
            payId,
            isStatus,
            businessNum,
            clientUserId,
            agentId,
            payWay,
            startDate,
            endDate
          };

          const flag = resetPage(data, this.oldObj);

          this.listQuery.page = flag ? 1 : this.listQuery.page;

          const { page, limit } = this.listQuery;

          this.oldObj = {
            orderNumber,
            payId,
            isStatus,
            businessNum,
            clientUserId,
            agentId,
            payWay,
            startDate,
            endDate
          };

          data = {
            ...data,
            ...{ page, limit }
          };

          this.searchOutOrderList(data)
            .then(rps => {})
            .catch(() => {})
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

    // 详情
    handleInfo(index, row) {
      this.loadDisabled = true;
      this.stopAble = true;
      this.current = index;
      this.outOrderFind({ outOrderId: row.outOrderId })
        .then(res => {
          this.temp = res.data;
          this.dialogVisible = true;
        })
        .catch(err => {})
        .finally(() => {
          this.loadDisabled = false;
          this.stopAble = false;
        });
    },

    handleClean() {
      this.form = {
        orderNumber: "", // 订单号
        clientUserId: null, // 线路id
        businessNum: "", // 商户号
        payId: null, // 商户类型
        isStatus: "", // 订单状态
        dateValue: "" // 查询时间
      };
    },

    heightFn() {
      const h = document.documentElement.clientHeight - 310;
      return h || 400;
    },

    // 筛选
    filterTag(val, row, col) {
      return row.isStatus == val;
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
.editForm {
  height: 90%;
  padding: 10px 10px 0;
}
.dialog-footer {
  text-align: center;
  padding-bottom: 10px;
}
.btn-export {
  text-align: right;
}

.w300 {
  width: 300px;
}
</style>
