<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <!--平台线路-->
        <el-form-item class="filter-item">
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
      :data="apiLogList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <el-table-column
        align="center"
        :label="$t('table.loginClient')+$t('table.name')"
        prop="clientName"
      />
      <el-table-column align="center" :label="$t('table.APIName')" prop="httpName"/>
      <el-table-column align="center" :label="$t('table.APIPath')" prop="httpPath"/>
      <el-table-column align="center" :label="$t('table.actionsIp')" prop="operationIp"/>
      <el-table-column align="center" :label="$t('table.actionsTime')" prop="operationTime"/>
      <el-table-column
        align="center"
        :label="$t('table.actions')"
        class-name="small-padding fixed-width"
        width="100"
      >
        <template slot-scope="{row}">
          <el-tooltip :content="$t('table.details')" placement="top">
            <i @click="handleDetail(row)" class="el-icon-edit"></i>
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
        :total="apiLogCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <el-dialog :title="$t('table.details')" :visible.sync="dialogVisible" width="700px">
      <div id="g-dialog">
        <el-form :model="temp" class="editForm" ref="dataForm" label-width="100px">
          <div class="form-items-hd">
            <el-form-item :label="$t('table.loginClient')+$t('table.name')">
              <el-input v-model="temp.clientName"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.agentId')" v-if="temp.agentId!==''">
              <el-input v-model="temp.agentId"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.APIName')">
              <el-input v-model="temp.httpName"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.APIPath')">
              <el-input v-model="temp.httpPath"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.getWay')">
              <el-input v-model="temp.method"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.actionsIp')">
              <el-input v-model="temp.operationIp"></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.inputData')">
              <el-input
                v-model="temp.inputData"
                type="textarea"
                :autosize="{ minRows: 4, maxRows: 6}"
              ></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.actionsTime')">
              <el-input v-model="temp.operationTime"></el-input>
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
import { resetPage } from "@/utils/sendDataProcess";

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
        dateValue: "",
        clientUserId: ""
      },
      listQuery: {
        page: 1,
        limit: 50
      },
      screenLoading: false,
      searchAble: false,
      oldObj: {},
      paginationShow: true,
      dialogVisible: false,
      temp: {}
    };
  },

  created() {
    this.searchOrderClientName();
  },

  computed: {
    ...mapGetters(["apiLogList", "apiLogCount", "orderClientName"])
  },
  methods: {
    ...mapActions(["searchApiLogs", "searchOrderClientName"]),

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
          this.screenLoading = true;
          this.searchAble = true;
          const { clientUserId } = this.form;

          var startDate, endDate;
          if (Array.isArray(this.form.dateValue)) {
            startDate = parseTime(this.form.dateValue[0]);
            endDate = parseTime(this.form.dateValue[1]);
          }

          var data = {
            clientUserId,
            startDate,
            endDate
          };

          const flag = resetPage(data, this.oldObj);

          this.listQuery.page = flag ? 1 : this.listQuery.page;

          this.oldObj = {
            clientUserId,
            startDate,
            endDate
          };

          const { page, limit } = this.listQuery;

          data = {
            ...data,
            ...{ page, limit }
          };

          this.searchApiLogs(data)
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

    handleClean() {
      this.form = {
        clientUserId: "",
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
.editForm {
  height: 90%;
  padding: 10px 10px 0;
}
.dialog-footer {
  text-align: center;
  padding-bottom: 10px;
}
</style>
