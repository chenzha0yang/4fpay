
<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <el-form-item class="filter-item">
          <!-- ID  -->
          <el-tooltip :content="$t('table.id')" placement="top">
            <el-input v-model="form.Id" :placeholder="$t('table.id')"></el-input>
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
          <el-button type="success" class="el-icon-plus" @click="handleCreate">{{$t('table.add')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
    <el-table
      v-loading="screenLoading"
      :data="roleSetList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <el-table-column align="center" :label="$t('table.id')" prop="Id" width="150"/>
      <el-table-column align="center" :label="$t('table.rolesName')" prop="uName"/>
      <el-table-column align="center" :label="$t('table.identify')" prop="slug"/>
      <!-- <el-table-column align="center" :label="$t('table.level')" prop="permissionLevel">
        <template slot-scope="scope">
          <el-tag :type="scope.row.permissionLevel==1?'success':scope.row.permissionLevel==2?'warning':'danger'">{{scope.row.permissionLevel|changeIsStatus}}</el-tag>
        </template>
      </el-table-column>-->
      <!-- <el-table-column align="center" :label="$t('table.permission')" prop="tag">
       <template slot-scope="scope">
            <el-tag disable-transitions type="success" v-for='(item,index) in scope.row.permissions' :key='index' class='pay-tag'>{{item.name}}</el-tag>
        </template>
      </el-table-column>-->
      <el-table-column align="center" :label="$t('table.createdAt')" prop="createdTime"/>
      <el-table-column align="center" :label="$t('table.updatedAt')" prop="updatedTime"/>

      <el-table-column align="center" :label="$t('table.status')" prop="state" width="150">
        <template slot-scope="{row,$index}">
          <el-switch
            v-model="row.state"
            :disabled="$index === current && stop"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-value="2"
            :inactive-value="1"
            @change="stateChange($index, row,'state','stop')"
          ></el-switch>
          <!-- <span :style="scope.row.state | statusFilter">{{$t('statusMsg.' + scope.row.state)}}</span> -->
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        :label="$t('table.actions')"
        class-name="small-padding fixed-width"
        width="100"
      >
        <template slot-scope="scope">
          <el-tooltip :content="$t('table.Update')" placement="top">
            <i @click="handleDetail(scope.$index, scope.row)" class="el-icon-edit"></i>
          </el-tooltip>
          <!-- <i  @click="handleDel(scope.$index, scope.row)" class="el-icon-delete"  ></i> -->
        </template>
      </el-table-column>
    </el-table>
    <!--  table end  -->
    <!--   page   -->
    <div class="pagination-container" :height="pageHeight">
      <el-pagination
        background
        v-if="paginationShow && listQuery.light<roleSetCount"
        :current-page.sync="listQuery.page"
        :page-sizes="[50,100,200, 500]"
        :page-size="listQuery.limit"
        layout="total, sizes, prev, pager, next, jumper"
        :total="roleSetCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <!--   添加／编辑／详情    -->
    <el-dialog :title="$t('table.Update')" :visible.sync="dialogVisible" width="700px">
      <div
        id="g-dialog"
        :element-loading-text="$t('table.searchMsg')"
        v-loading="!menuSetShow && !permSetShow"
      >
        <el-form
          :model="temp"
          class="editForm"
          :rules="demoRules"
          ref="dataForm"
          label-width="100px"
        >
          <div class="form-items-hd">
            <el-form-item :label="$t('table.rolesName')" prop="uName">
              <el-input
                class="w300"
                v-model="temp.uName"
                :placeholder="$t('table.input')+$t('table.nickname')"
                :maxlength="12"
              ></el-input>
            </el-form-item>

            <el-form-item :label="$t('table.identify')" prop="slug">
              <el-input
                class="w300"
                v-model="temp.slug"
                :placeholder="$t('table.input')+$t('table.identify')"
                :maxlength="12"
              ></el-input>
            </el-form-item>

            <!-- <el-form-item :label="$t('table.permLv')" prop="permLv">
            <el-input class="w300" v-model="temp.permissionLevel" :placeholder="$t('table.input')+$t('table.identify')"
              :maxlength="12">
            </el-input>
            </el-form-item>-->
            <el-form-item :label="$t('table.setMenu')" prop="menuIds">
              <treeselect
                v-if="menuSetShow"
                width="300px"
                :multiple="true"
                :options="menuSetList"
                :placeholder="$t('table.selectPermission')"
                :defaultExpandLevel="Level"
                v-model="temp.menuIds"
                :max-height="300"
              />
            </el-form-item>

            <el-form-item :label="$t('table.setPerm')" prop="permissionIds">
              <treeselect
                v-if="permSetShow"
                width="300px"
                :multiple="true"
                :options="permissionSetList"
                :placeholder="$t('table.selectPermission')"
                :defaultExpandLevel="Level"
                v-model="temp.permissionIds"
                :max-height="300"
              />
            </el-form-item>

            <el-form-item :label="$t('table.client')">
              <el-switch
                v-model="temp.isClient"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-text="$t('table.disable')"
                :inactive-text="$t('table.normal')"
                :active-value="2"
                :inactive-value="1"
              ></el-switch>
            </el-form-item>

            <el-form-item :label="$t('table.agent')">
              <el-switch
                v-model="temp.isAgent"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-text="$t('table.disable')"
                :inactive-text="$t('table.normal')"
                :active-value="2"
                :inactive-value="1"
              ></el-switch>
            </el-form-item>

            <el-form-item :label="$t('table.status')">
              <el-switch
                v-model="temp.state"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-text="$t('table.disable')"
                :inactive-text="$t('table.normal')"
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
import Treeselect from "@riophae/vue-treeselect";
import demoRules from "@/utils/demoRules";
import { idSProcess } from "@/utils/sendDataProcess";

import HandleSearch from "@/mixins/handleSearch";
import SendUpdateData from "@/mixins/sendUpdateData";

export default {
  components: {
    MdInput,
    Treeselect
  },

  // 状态信息
  filters: {
    // 账号状态信息
    statusFilter(status) {
      const statusMap = {
        1: "color: #10c14b",
        2: "color: red"
      };
      return statusMap[status];
    }
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

      temp: {
        uName: "",
        menuIds: [],
        permissionLevel: 1,
        slug: "",
        state: 1,
        permissionIds: [],
        Id: 1,
        isAgent: 1,
        isClient: 1
      },

      listQuery: {
        page: 1,
        limit: 50
      },

      Level: Infinity,

      dialogVisible: false,
      screenLoading: false,
      searchAble: false,

      menuSetList: [],
      permissionSetList: [],

      Disable: true,
      Loading: false,
      oldObj: {},
      paginationShow: true,
      permSetShow: false,
      menuSetShow: false,
      stop: false
    };
  },
  mixins: [HandleSearch, SendUpdateData],

  computed: {
    ...mapGetters(["roleSetList", "roleSetCount"])
  },
  methods: {
    ...mapActions([
      "searchRoleSet",
      "putRoleSet",
      "PgetPermissions",
      "PgetMenus"
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

    // 添加
    handleCreate() {
      this.$router.push({ name: "roleAdd" });
    },
    // 编辑
    handleDetail(index, row) {
      this.temp = Object.assign({}, row);
      this.getMenus();
      this.getPermissions();
      this.Loading = false;
      this.Disable = false;
      this.current = index;
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    // 开关
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
        putStoreFn: "putRoleSet",
        backtrackFn: "_putAccount",
        type: "edit",
        dataName: "roleSet",
        finallyCallback: () => {
          this[attr] = false;
        }
      });
    },

    // 获取菜单选项
    getMenus() {
      this.menuSetShow = false;
      this.PgetMenus().then(rps => {
        this.menuSetList = rps.data;
        this.menuSetShow = true;
      });
    },

    // 获取权限选项
    getPermissions() {
      this.permSetShow = false;
      this.PgetPermissions().then(rps => {
        this.permissionSetList = rps.data;
        this.permSetShow = true;
      });
    },

    // 修改 提交
    upData() {
      this.$refs["dataForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          const {
            Id,
            slug,
            state,
            uName,
            isAgent,
            isClient,
            permissionIds,
            menuIds
          } = this.temp;

          const newPermissionIds = idSProcess(
            permissionIds,
            this.permissionSetList[0].children
          );

          const newMenuIds = idSProcess(menuIds, this.menuSetList[0].children);

          const data = {
            Id,
            slug,
            state,
            uName,
            isAgent,
            isClient,
            permissionIds: newPermissionIds,
            menuIds: newMenuIds
          };

          this.mixinsSendUpdateData({
            data,
            index: this.current,
            putStoreFn: "putRoleSet",
            backtrackFn: "_putAccount",
            thenCallback: this.upDataThenCallback,
            catchCallback: this.upDataCatchCallback,
            type: "edit",
            dataName: "roleSet"
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

          const { Id } = this.form;
          const { page, limit } = this.listQuery;
          const data = { Id, limit };

          this.mixinsHandleSearch({
            data,
            oldData: this.oldObj,
            page,
            searchFn: "searchRoleSet",
            callBacks: this.handleSearchCall,
            eveCallBacks: this.handleSearchEveCall
          });
          this.oldObj = {
            Id,
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
        Id: null
      };
    },

    heightFn() {
      const h = document.documentElement.clientHeight - 260;
      return h || 400;
    }
  }
};
</script>
<style src="@riophae/vue-treeselect/dist/vue-treeselect.min.css"></style>

<style scoped lang="scss">
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
