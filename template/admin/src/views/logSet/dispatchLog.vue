<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <!-- 订单号 -->
        <el-form-item class="filter-item">
          <md-input
            v-model="form.orderNumber"
            icon="search"
            name="userName"
            :placeholder="$t('table.orderNumber')"
          >{{ $t('table.orderNumber') }}</md-input>
        </el-form-item>

        <!-- 是否自动下发  -->
        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.whetherSend')" placement="top">
            <el-select v-model="form.isAutoSend" :placeholder="$t('table.whetherSend')">
              <el-option
                v-for="item in sendStatusList"
                :key="item.value"
                :label="$t('table.' + item.label)"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-tooltip>
        </el-form-item>

        <!-- 入款出款下发  -->
        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.inOutcomeSend')" placement="top">
            <el-select v-model="form.way" :placeholder="$t('table.inOutcomeSend')">
              <el-option
                v-for="item in inOutcomeSend"
                :key="item.value"
                :label="$t('table.' + item.label)"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-tooltip>
        </el-form-item>

        <!-- 开始时间-结束时间 -->
        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.date')" placement="top">
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
      :data="dispatchLogList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <!-- <el-table-column align="center" :label="$t('route.dispatchLog')+$t('table.id')" prop="sendCallbackId" width="80"/> -->
      <el-table-column align="center" :label="$t('table.orderNumber')" prop="orderNum"/>
      <el-table-column
        align="center"
        :label="$t('table.send')+$t('table.address')"
        prop="callbackUrl"
      />
      <!-- <el-table-column align="center" :label="$t('table.send')+$t('table.data')" width="120">
        <template slot-scope="scope">
            <el-popover
              placement="top-start"
              :title="$t('table.data')"
              trigger="hover"
              :content="scope.row.sendMsg">
              <el-button type='warning'  slot="reference">{{$t('table.check')+$t('table.data')}}</el-button>
            </el-popover>
      </template>-->
      <!-- </el-table-column> -->
      <el-table-column align="center" :label="$t('table.httpCode')" prop="httpCode"/>
      <el-table-column align="center" :label="$t('table.reponseMessage')" prop="returnMsg"/>
      <el-table-column align="center" :label="$t('table.whetherSend')" prop="tag">
        <template slot-scope="scope">
          <el-tag
            disable-transitions
            :type="scope.row.isAutoSend==1?'success':'warning'"
          >{{scope.row.isAutoSend | changeIsSued}}</el-tag>
        </template>
      </el-table-column>

      <el-table-column align="center" :label="$t('table.inOutcomeSend')" prop="tag">
        <template slot-scope="scope">
          <el-tag
            disable-transitions
            :type="scope.row.way==1?'success':'warning'"
          >{{scope.row.way | inOutcomeStatus}}</el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" :label="$t('table.createdAt')" prop="createdAt"/>
      <!-- <el-table-column align="center" :label="$t('table.updatedAt')" prop="updatedAt" /> -->
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
        :total="dispatchLogCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import MdInput from "@/components/MDinput";
import pickerOptions from "@/utils/pickerOptions";
import { parseTime } from "@/utils/index";
import i18n from "@/lang/index";
import HandleSearch from "@/mixins/handleSearch";

export default {
  components: {
    MdInput
  },
  filters: {
    //出入款下发
    inOutcomeStatus(status) {
      const isStatus = {
        1:
          i18n.messages[i18n.locale].table.income +
          i18n.messages[i18n.locale].table.send,
        2:
          i18n.messages[i18n.locale].table.outcome +
          i18n.messages[i18n.locale].table.send
      };
      return isStatus[status];
    },
    // 是否自动下发
    changeIsSued(status) {
      const isSued = {
        1:
          i18n.messages[i18n.locale].table.auto +
          i18n.messages[i18n.locale].table.send,
        2:
          i18n.messages[i18n.locale].table.manual +
          i18n.messages[i18n.locale].table.send
      };
      return isSued[status];
    }
  },
  data() {
    return {
      rules: {},
      pageHeight: 50,
      pickerOptions: pickerOptions,
      form: {
        orderNumber: "",
        isAutoSend: "",
        way: "",
        dateValue: "" // 查询时间
      },
      sendStatusList: [
        {
          value: "",
          label: "all"
        },
        {
          value: 1,
          label: "auto"
        },
        {
          value: 2,
          label: "manual"
        }
      ],
      inOutcomeSend: [
        {
          value: "",
          label: "all"
        },
        {
          value: 1,
          label: "income"
        },
        {
          value: 2,
          label: "outcome"
        }
      ],
      listQuery: {
        page: 1,
        limit: 50
      },
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,
      oldObj: {},
      paginationShow: true
    };
  },
  mixins: [HandleSearch],
  computed: {
    ...mapGetters(["dispatchLogList", "dispatchLogCount"])
  },
  methods: {
    ...mapActions(["searchDispatchLog"]),

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },
    // 查询
    handleSearch() {
      this.$refs["searchForm"].validate(valid => {
        if (valid) {
          const { orderNumber, isAutoSend, way } = this.form;
          const { page, limit } = this.listQuery;
          const data = { orderNumber, isAutoSend, way, limit };

          var startDate, endDate;
          if (Array.isArray(this.form.dateValue)) {
            startDate = data.startDate = parseTime(this.form.dateValue[0]);
            endDate = data.endDate = parseTime(this.form.dateValue[1]);
          }

          this.screenLoading = true;
          this.searchAble = true;

          this.mixinsHandleSearch({
            data,
            oldData: this.oldObj,
            page,
            searchFn: "searchDispatchLog",
            callBacks: this.handleSearchCall,
            eveCallBacks: this.handleSearchEveCall
          });

          this.oldObj = {
            orderNumber,
            isAutoSend,
            way,
            limit,
            startDate,
            endDate
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
        orderNumber: "",
        isAutoSend: "",
        way: "",
        dateValue: "" // 查询时间
      };
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
</style>
