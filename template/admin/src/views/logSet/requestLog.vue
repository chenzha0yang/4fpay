<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->

    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        

        <el-form-item class="filter-item">
          <!-- 开始时间-结束时间 -->
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
        <!-- js153 -->
         <el-form-item class="filter-item">
         <el-input v-model="one" placeholder="请求参数" style="width:200px"></el-input>
         <el-input v-model="two" placeholder="路由" style="width:100px"></el-input>
         <el-input v-model="three" placeholder="平台路线" style="width:100px"></el-input>
         </el-form-item>
        <!-- js153 -->
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
      :data="requestLogList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <el-table-column align="center" :label="$t('table.req_clientName')" prop="clientName"/>
      <el-table-column align="center" :label="$t('table.req_aim')" prop="aim"/>
      <el-table-column align="center" :label="$t('table.req_route')" prop="route"/>
      <el-table-column align="center" :label="$t('table.req_data')" prop="data"/>
      <el-table-column align="center" :label="$t('table.req_time')" prop="time"/>
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
        :total="requestLogCount"
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
import HandleSearch from "@/mixins/handleSearch";

export default {
  components: {
    MdInput
  },

  data() {
    return {
      one:'',
      two:'',
      three:'',
      rules: {},
      current: 0,
      pageHeight: 50,
      pickerOptions: pickerOptions,
      form: {
        account: "",
        loginIp: "",
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
    ...mapGetters(["requestLogList", "requestLogCount"])
  },
  methods: {
    ...mapActions(["searchRequestLog"]),

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
          const { page, limit } = this.listQuery;
          const data = { limit };

          var startTime, endTime;

          if (Array.isArray(this.form.dateValue)) {
            startTime = data.startTime = parseTime(this.form.dateValue[0]);
            endTime = data.endTime = parseTime(this.form.dateValue[1]);
          }
          data.request_data=this.one;
          data.router=this.two;
          data.client=this.three;
          this.screenLoading = true;
          this.searchAble = true;
          // var b={
          //     request_data:this.one,
          //   router:this.two,
          //   client:this.three,
          //   data,
          //   oldData: this.oldObj,
          //   page,
          //   searchFn: "searchRequestLog",
          //   callBacks: this.handleSearchCall,
          //   eveCallBacks: this.handleSearchEveCall
          // }
          // console.log(b);
          
          this.mixinsHandleSearch({
            // request_data:this.one,
            // router:this.two,
            // client:this.three,
            data,
            oldData: this.oldObj,
            page,
            searchFn: "searchRequestLog",
            callBacks: this.handleSearchCall,
            eveCallBacks: this.handleSearchEveCall
          });
         console.log(this.requestLogList);
          this.oldObj = {
            startTime,
            endTime,
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
        account: "",
        loginIp: "",
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
</style>
