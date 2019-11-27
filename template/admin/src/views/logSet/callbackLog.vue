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

        <el-form-item class="filter-item">
          <md-input
            v-model="form.callbackIp"
            icon="search"
            name="userName"
            :placeholder="$t('table.callback')+$t('table.source')+$t('table.ip')"
          >{{ $t('table.callback') + $t('table.source') + $t('table.ip')}}</md-input>
        </el-form-item>

        <!--支付方式-->
        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.payWay')" placement="top">
            <el-select
              filterable
              clearable
              v-model="form.payWay"
              :placeholder="$t('table.payWay')"
              size="medium"
            >
              <el-option
                v-for="item in payTypeList"
                :key="item.value"
                :label="item.typeName"
                :value="item.typeId"
              />
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
      :data="callbackLogList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <!-- <el-table-column align="center" :label="$t('route.callbackLog')+$t('table.id')" prop="operationId" width="80"/> -->
      <el-table-column align="center" :label="$t('table.orderNumber')" prop="orderNumber"/>
      <el-table-column align="center" :label="$t('callback.clientName')" prop="clientName"/>
      <el-table-column align="center" :label="$t('table.tripartConfName')" prop="confName"/>
      <!-- <el-table-column align="center" :label="$t('table.callback')+$t('table.source')+$t('table.ip')" prop="callbackIp"/> -->
      <!-- <el-table-column align="center" :label="$t('table.callback')+$t('table.message')" prop="callbackMsg"/> -->
      <!-- <el-table-column align="center" :label="$t('table.callBackURL')" prop="callbackUrl" /> -->
      <el-table-column align="center" :label="$t('table.errorCode')" prop="tag">
        <template slot-scope="scope">
          <el-tag
            disable-transitions
            :type="scope.row.errorCode==1?'info':scope.row.errorCode==2?'warning':'danger'"
          >{{ scope.row.errorCode | changeIsSuedFilters }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" :label="$t('table.typeName')" prop="typeName"/>
      <el-table-column align="center" :label="$t('table.createdAt')" prop="createdAt"/>
      <!-- <el-table-column align="center" :label="$t('table.updatedAt')" prop="updatedAt"/> -->
      <el-table-column
        align="center"
        :label="$t('table.actions')"
        class-name="small-padding fixed-width"
        width="100"
      >
        <template slot-scope="scope">
          <el-tooltip :content="$t('table.details')" placement="top">
            <i @click="handleDetail(scope.row)" class="el-icon-edit"></i>
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
        :total="callbackLogCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>

    <el-dialog :title="$t('table.details')" :visible.sync="dialogVisible" width="700px">
      <div id="g-dialog">
        <el-form :model="temp" class="editForm" ref="dataForm" label-width="100px">
          <div class="form-items-hd">
            <el-form-item :label="$t('table.orderNumber')" prop="orderNumber">
              <el-input v-model="temp.orderNumber"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.clientName')">
              <el-input v-model="temp.clientName"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.clientName')">
              <el-input v-model="temp.clientName"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.confName')">
              <el-input v-model="temp.confName"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.callBackURL')">
              <el-input v-model="temp.callbackUrl"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.callback')+$t('table.source')+$t('table.ip')">
              <el-input v-model="temp.callbackIp"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.callback')+$t('table.message')">
              <el-input
                type="textarea"
                :autosize="{ minRows: 4, maxRows: 20}"
                v-model="temp.callbackMsg"
              ></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.errorCode')">
              <el-input :value="temp.errorCode | changeIsSuedFilters"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.typeName')">
              <el-input v-model="temp.typeName"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.createdAt')">
              <el-input v-model="temp.createdAt"></el-input>
            </el-form-item>
          </div>
        </el-form>
        <div slot="footer" class="dialog-footer">
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
import { parseTime } from "@/utils/index";
import HandleSearch from "@/mixins/handleSearch";
import i18n from "@/lang/index";

export default {
  components: {
    MdInput
  },

  data() {
    return {
      rules: {},
      current: 0,
      pageHeight: 50,
      pickerOptions: pickerOptions,
      form: {
        orderNumber: "",
        callbackIp: "",
        payWay: "",
        dateValue: "" // 查询时间
      },
      listQuery: {
        page: 1,
        limit: 50
      },
      temp: {},
      screenLoading: false,
      searchAble: false,
      dialogVisible: false,
      oldObj: {},
      paginationShow: true
    };
  },
  filters: {
    changeIsSuedFilters(status) {
      const changeMap = {
        1: i18n.messages[i18n.locale].changeIsSued[1],
        2: i18n.messages[i18n.locale].changeIsSued[2],
        3: i18n.messages[i18n.locale].changeIsSued[3],
        4: i18n.messages[i18n.locale].changeIsSued[4],
        5: i18n.messages[i18n.locale].changeIsSued[5],
        6: i18n.messages[i18n.locale].changeIsSued[6],
        7: i18n.messages[i18n.locale].changeIsSued[7]
      };

      return changeMap[status];
    }
  },
  mixins: [HandleSearch],
  mounted() {
    this.searchPayType();
  },
  computed: {
    ...mapGetters(["callbackLogList", "callbackLogCount", "payTypeList"])
  },
  methods: {
    ...mapActions(["searchCallbackLog", "searchPayType"]),

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },
    handleDetail(row) {
      this.temp = Object.assign({}, row);
      this.dialogVisible = true;
    },

    // 查询
    handleSearch() {
      this.$refs["searchForm"].validate(valid => {
        if (valid) {
          const { orderNumber, payWay, callbackIp } = this.form;
          const { page, limit } = this.listQuery;
          const data = { orderNumber, payWay, callbackIp, limit };

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
            searchFn: "searchCallbackLog",
            callBacks: this.handleSearchCall,
            eveCallBacks: this.handleSearchEveCall
          });

          this.oldObj = {
            orderNumber,
            payWay,
            callbackIp,
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
        callbackIp: "",
        payWay: "",
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

.editForm {
  height: 90%;
  padding: 10px 10px 0;
}
.dialog-footer {
  text-align: center;
  padding-bottom: 10px;
}
</style>
