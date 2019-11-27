
<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <!-- 查询操作 -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <el-form-item class="filter-item">
          <!-- 三方类型id查询-->
          <el-tooltip :content="$t('table.payId')" placement="top">
            <el-select v-model="form.payId" filterable clearable :placeholder="$t('table.payId')">
              <el-option
                v-for="item in orderConfigLists"
                :key="item.payId"
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
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
    <!-- 结果列表 -->
    <el-table
      v-loading="screenLoading"
      :data="maintainList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <!-- <el-table-column align="center" :label="$t('table.id')" prop="Id"  width="100"/> -->
      <el-table-column align="center" :label="$t('table.maintainProg')" prop="tag">
        <template slot-scope="scope">
          <el-tag type="success">{{$t('maintainProg.' + scope.row.projectType)}}</el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" :label="$t('table.confName')" prop="confName"/>
      <el-table-column align="center" :label="$t('table.maintainInfo')" prop="msg"/>
      <el-table-column align="center" :label="$t('table.createdAt')" prop="createdTime"/>
      <el-table-column align="center" :label="$t('table.updatedAt')" prop="updatedTime"/>
      <el-table-column align="center" :label="$t('table.whetherMaintain')" prop="state">
        <template slot-scope="{row,$index}">
          <el-switch
            v-model="row.state"
            :disabled="$index === current && maintain"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-value="2"
            :inactive-value="1"
            @change="stateChange($index, row,'state','maintain')"
          ></el-switch>
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
          <el-tooltip :content="$t('table.Update')" placement="top">
            <i @click="handleDetail(scope.$index, scope.row)" class="el-icon-edit"></i>
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
        :total="maintainCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <!--   添加／编辑／详情    -->
    <el-dialog :title="$t('table.Update')" :visible.sync="dialogVisible" width="700px">
      <div id="g-dialog">
        <el-form
          class="editForm"
          :rules="demoRules"
          ref="dataForm"
          label-width="150px"
          :model="temp"
        >
          <div class="form-items-hd">
            <el-form-item :label="$t('table.maintainInfo')" prop="msg">
              <el-input
                v-model="temp.msg"
                :placeholder="$t('table.input')+$t('table.maintainInfo')"
                :maxlength="48"
              ></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.whetherMaintain')">
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
          </div>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">{{$t('table.cancel')}}</el-button>
          <el-button
            :disabled="Disable"
            :loading="Loading"
            type="primary"
            @click="upData"
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

import HandleSearch from "@/mixins/handleSearch";
import SendUpdateData from "@/mixins/sendUpdateData";

export default {
  components: {
    MdInput
  },

  data() {
    return {
      demoRules: demoRules,
      rules: {},
      current: 0,
      pageHeight: 50,
      form: {
        Id: null
      },
      listQuery: {
        page: 1,
        limit: 50
      },
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,
      temp: {
        msg: "",
        state: 2
      },
      Loading: false,
      Disable: false,
      oldObj: {},
      paginationShow: true,
      maintain: false
    };
  },
  mixins: [HandleSearch, SendUpdateData],

  created() {
    this.searhOrderConfigLists();
  },

  computed: {
    ...mapGetters([
      "maintainList",
      "maintainCount",
      "orderConfigLists",
      "isView"
    ])
  },
  methods: {
    ...mapActions(["searchMaintain", "putMaintain", "searhOrderConfigLists"]),
    // 分页
    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },
    handleDetail(index, row) {
      this.temp = Object.assign({}, row);
      this.Loading = false;
      this.Disable = false;
      this.current = index;
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
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
        putStoreFn: "putMaintain",
        backtrackFn: "_putMaintain",
        type: "edit",
        dataName: "maintain",
        finallyCallback: () => {
          this[attr] = false;
        }
      });
    },

    // 编辑提交
    upData() {
      this.$refs["dataForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          const { Id, msg, state } = this.temp;

          const data = { Id, msg, state };

          this.mixinsSendUpdateData({
            data,
            index: this.current,
            putStoreFn: "putMaintain",
            backtrackFn: "_putMaintain",
            type: "edit",
            dataName: "maintain",
            thenCallback: this.upDataThenCallback,
            catchCallback: this.upDataCatchCallback
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

    // 查询
    handleSearch() {
      this.$refs["searchForm"].validate(valid => {
        if (valid) {
          this.screenLoading = true;
          this.searchAble = true;

          const { payId } = this.form;
          const { page, limit } = this.listQuery;
          const data = { payId, limit };

          this.mixinsHandleSearch({
            data,
            oldData: this.oldObj,
            page,
            searchFn: "searchMaintain",
            callBacks: this.handleSearchCall,
            eveCallBacks: this.handleSearchEveCall
          });
          this.oldObj = {
            payId,
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
    // 重置
    handleClean() {
      this.form = {
        payId: null
      };
    },

    heightFn() {
      const h = document.documentElement.clientHeight - 260;
      return h || 400;
    }

    // 过滤数据
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
.editForm {
  height: 90%;
  padding: 10px 10px 0;
}
.dialog-footer {
  text-align: center;
  padding-bottom: 10px;
}
</style>
