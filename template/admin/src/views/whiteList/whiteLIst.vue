
<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <!-- 查询操作 -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <!-- ip -->
        <el-form-item class="filter-item" prop="ip">
          <md-input
            icon="search"
            name="ip"
            :placeholder="$t('table.input')+$t('table.ip')"
            :maxlength="50"
            v-model="form.payIp"
          >{{$t('table.ip')}}</md-input>
        </el-form-item>

        <el-form-item class="filter-item">
          <!-- 商户类型-->
          <el-tooltip :content="$t('table.confName')" placement="top">
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

        <el-form-item
          class="filter-item addBtn"
          v-if="isView.isAgent === 1 && isView.isClient === 1"
        >
          <el-button type="success" class="el-icon-plus" @click="handleCreate">{{$t('table.add')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
    <!-- 结果列表 -->
    <el-table
      v-loading="screenLoading"
      :data="whiteList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <el-table-column align="center" :label="$t('table.confName')" prop="confName"/>
      <el-table-column align="center" :label="$t('table.ip')" prop="payIp"/>

      <el-table-column align="center" :label="$t('table.createdAt')" prop="createdAt"/>
      <el-table-column align="center" :label="$t('table.updatedAt')" prop="updatedAt"/>

      <el-table-column align="center" :label="$t('table.whetherOpen')" prop="tag" width="150">
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
        :total="whiteListCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <!--   添加／编辑／详情    -->
    <el-dialog :title="$t('table.Update')" :visible.sync="dialogVisible" width="700px">
      <div id="g-dialog">
        <el-form
          :model="temp"
          class="editForm"
          :rules="demoRules"
          ref="dataForm"
          label-width="150px"
        >
          <el-form-item :label="$t('table.confName')" prop="payId">
            <el-select
              width="300px"
              class="filter-item"
              v-model="temp.payId"
              :placeholder="$t('table.payId')"
            >
              <el-option
                v-for="status in orderConfigLists"
                :key="status.payId"
                :label="status.confName"
                :value="status.payId"
              ></el-option>
            </el-select>
          </el-form-item>

          <el-form-item :label="$t('table.whiteListIp')" prop="payIp">
            <el-input :placeholder="$t('table.input')+$t('table.whiteListIp')" v-model="temp.payIp">
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.whetherOpen')" prop="state">
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
        </el-form>
        <div class="dialog-footer">
          <el-button @click="dialogVisible = false">{{$t('table.cancel')}}</el-button>
          <el-button
            type="primary"
            :disabled="Disable"
            @click="upData"
            :loading="Loading"
          >{{$t('table.submit')}}</el-button>
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
      rules: {},
      demoRules: demoRules,
      current: 0,
      pageHeight: 50,
      form: {
        payIp: null,
        payId: null
      },
      listQuery: {
        page: 1,
        limit: 50
      },
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,

      dialogStatus: "Update",
      temp: {
        payId: null,
        Id: null,
        state: 1
      },
      Disable: true,
      Loading: false,
      oldObj: {},
      paginationShow: true,
      open: false
    };
  },
  mixins: [HandleSearch, SendUpdateData],

  created() {
    this.searhOrderConfigLists();
  },
  computed: {
    ...mapGetters(["whiteList", "whiteListCount", "orderConfigLists", "isView"])
  },
  methods: {
    ...mapActions([
      "searchWhiteList",
      "searhOrderConfigLists",
      "putWhiteList",
      "_putWhiteList"
    ]),

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },
    getConfigLists() {
      this.searhOrderConfigLists().then(rps => {
        this.Disable = false;
      });
    },

    handleDetail(index, row) {
      this.Disable = true;
      this.temp = Object.assign({}, row);
      this.getConfigLists();
      this.Loading = false;
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
        putStoreFn: "putWhiteList",
        backtrackFn: "_putWhiteList",
        type: "edit",
        dataName: "whiteList",
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
          this.sendUpdateData(true);
        }
      });
    },

    sendUpdateData(data = {}, attr = "open", flag) {
      if (!flag) {
        const { Id, payIp, state, payId } = this.temp;
        const o = this.orderConfigLists.find(v => v.payId === payId);
        const confName = o.confName;
        data = { Id, state, payIp, payId, confName };
      }

      this.putWhiteList({
        name: "whiteList",
        type: "edit",
        data,
        current: this.current
      })
        .then(res => {
          this.alertMessage(res.msg);
          this.dialogVisible = false;
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
      modifyStatusCallback("_putWhiteList", "whiteList", data, this.current);
    },

    // 查询
    handleSearch() {
      this.$refs["searchForm"].validate(valid => {
        if (valid) {
          this.screenLoading = true;
          this.searchAble = true;

          const { payIp, payId } = this.form;
          const { page, limit } = this.listQuery;
          const data = { payIp, payId, limit };

          this.mixinsHandleSearch({
            data,
            oldData: this.oldObj,
            page,
            searchFn: "searchWhiteList",
            callBacks: this.handleSearchCall,
            eveCallBacks: this.handleSearchEveCall
          });
          this.oldObj = {
            payIp,
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
        ip: null,
        payId: null
      };
    },
    // 新增
    handleCreate() {
      this.$router.push({ name: "whiteAdd" });
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
.addBtn {
  float: right;
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
