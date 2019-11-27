<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <!-- 用户ID  -->
        <el-form-item class="filter-item">
          <md-input
            v-model="form.account"
            icon="search"
            name="loginIp"
            :placeholder="$t('table.account')"
          >{{ $t('table.account') }}</md-input>
        </el-form-item>

        <!-- path  -->
        <el-form-item class="filter-item">
          <md-input
            v-model="form.path"
            icon="search"
            name="loginIp"
            :placeholder="$t('table.path')"
          >{{ $t('table.path') }}</md-input>
        </el-form-item>

        <!-- 登陆IP  -->
        <el-form-item class="filter-item">
          <md-input
            v-model="form.operationIp"
            icon="search"
            name="loginIp"
            :placeholder="$t('table.loginIp')"
          >{{ $t('table.loginIp') }}</md-input>
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
      :data="controlLogList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <!-- <el-table-column align="center" :label="$t('route.operateLog')+$t('table.id')" prop="operationId" /> -->
      <!-- <el-table-column align="center" :label="$t('table.username')+$t('table.id')" prop="userId"/> -->
      <el-table-column align="center" :label="$t('table.account')" prop="account"/>
      <el-table-column align="center" :label="$t('table.path')" prop="Path"/>
      <el-table-column align="center" :label="$t('table.interaction')" prop="Method"/>
      <el-table-column align="center" :label="$t('table.loginIp')" prop="operationIp"/>
      <!-- <el-table-column align="center" :label="$t('table.actions')+$t('table.content')" prop="Input"/> -->
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
        :total="controlLogCount"
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
// import { resetPage } from "@/utils/sendDataProcess";
import HandleSearch from "@/mixins/handleSearch";

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
        account: "",
        path: "",
        operationIp: "",
        dateValue: ""
      },
      listQuery: {
        page: 1,
        limit: 50
      },
      screenLoading: false,
      searchAble: false,
      oldObj: {},
      paginationShow: true
    };
  },

  mixins: [HandleSearch],

  computed: {
    ...mapGetters(["controlLogList", "controlLogCount"])
  },
  methods: {
    ...mapActions(["searchControlLog"]),

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
          const { account, path, operationIp } = this.form;
          const { page, limit } = this.listQuery;
          const data = { account, path, operationIp, limit };

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
            searchFn: "searchControlLog",
            callBacks: this.handleSearchCall,
            eveCallBacks: this.handleSearchEveCall
          });

          this.oldObj = {
            account,
            path,
            operationIp,
            startDate,
            endDate,
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
        path: "",
        operationIp: "",
        dateValue: ""
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
