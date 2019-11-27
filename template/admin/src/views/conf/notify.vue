<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <!-- 查询操作 -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <!--平台线路-->
        <el-form-item class="filter-item" v-if="isView.isClient === 1">
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
          <md-input
            v-model="form.agentId"
            icon="search"
            name="name"
            :placeholder="$t('table.input')+$t('table.agentName')"
          >{{ $t('table.agentName') }}</md-input>
        </el-form-item>

        <el-form-item class="filter-item" v-if="isView.isAgent === 1">
          <md-input
            v-model="form.agentIp"
            icon="search"
            name="name"
            :placeholder="$t('table.input')+$t('table.ip')"
          >{{ $t('table.ip') }}</md-input>
        </el-form-item>

        <el-form-item class="filter-item">
          <md-input
            v-model="form.agentPort"
            icon="search"
            name="name"
            :placeholder="$t('table.input')+$t('table.port')"
          >{{ $t('table.port') }}</md-input>
        </el-form-item>

        <el-form-item class="filter-item">
          <md-input
            v-model="form.siteUrl"
            icon="search"
            name="name"
            :placeholder="$t('table.input')+$t('table.siteUrl')"
          >{{ $t('table.siteUrl') }}</md-input>
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

        <!-- <el-form-item class="filter-item addBtn">
          <el-button type="success" class="el-icon-plus" @click="addNew">{{$t('table.add')}}</el-button>
        </el-form-item> -->

         <el-form-item class="filter-item addBtn">
          <el-button type="success" class="el-icon-plus" @click="dialogAddNotify=true">{{$t('table.add')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
    <!-- 结果列表 -->
    <el-table
      v-loading="screenLoading"
      :data="notifyList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <!-- <el-table-column align="center" :label="$t('table.agentId')" prop="agentId"/> -->
      <!-- <el-table-column align="center" :label="$t('table.client')" prop="clientUserId" :formatter="changeClientUserName"/> -->
       <el-table-column align="center" :label="$t('table.client')" prop="clientUserName" v-if="isView.isClient === 1"/>
      <el-table-column align="center" :label="$t('table.agentId')" prop="agentId"/>
      <el-table-column align="center" :label="$t('table.ip')" prop="agentIp"/>
      <el-table-column align="center" :label="$t('table.port')" prop="agentPort" width="80"/>
      <el-table-column align="center" :label="$t('table.siteUrl')" prop="siteUrl"/>
      <el-table-column align="center" :label="$t('table.incomeCallbackUrl')" prop="callBackUrl"/>
      <el-table-column
        align="center"
        :label="$t('table.outcomeCallbackUrl')"
        prop="outCallBackUrl"
      />
      <!-- <el-table-column align="center" :label="$t('table.status')" prop="state" width="150">
        <template slot-scope="scope">
          <el-tag
            :type="scope.row.state==1?'success':'danger'"
          >{{$t('maintain.' + scope.row.state)}}</el-tag>
        </template>
      </el-table-column> -->
      <el-table-column
        align="center"
        :label="$t('table.actions')"
        class-name="small-padding fixed-width"
        width="80"
      >
        <template slot-scope="scope">
          <i @click="handleUpdate(scope.$index, scope.row)" class="el-icon-edit"></i>
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
        :total="notifyListCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <!--   添加／编辑／详情    -->
    <!-- <el-dialog :title="$t('table.Update')" :visible.sync="dialogVisible" width="700px">
      <div v-if="dialogVisible" id="g-dialog">
        <el-form
          ref="dataForm"
          :rules="demoRules"
          :model="temp"
          class="editForm"
          label-width="150px"
        >
          <el-form-item :label="$t('table.agentId')" prop="agentId">
            <el-input
              :placeholder="$t('table.input')+$t('table.agentId')"
              v-model="temp.agentId"
              maxlength="12"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.whetherOpen')">
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

          <el-form-item :label="$t('table.agentIp')" prop="agentIp">
            <el-input
              :placeholder="$t('table.input')+$t('table.agentIp')"
              v-model="temp.agentIp"
              maxlength="15"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.agentPort')" prop="agentPort">
            <el-input
              :placeholder="$t('table.input')+$t('table.agentPort')"
              v-model="temp.agentPort"
              maxlength="5"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.siteUrl')" prop="siteUrl">
            <el-input :placeholder="$t('table.input')+$t('table.siteUrl')" v-model="temp.siteUrl">
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.incomeCallbackUrl')" prop="callBackUrl">
            <el-input
              :placeholder="$t('table.input')+$t('table.incomeCallbackUrl')"
              v-model="temp.callBackUrl"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.outcomeCallbackUrl')" prop="outCallBackUrl">
            <el-input
              :placeholder="$t('table.input')+$t('table.outcomeCallbackUrl')"
              v-model="temp.outCallBackUrl"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.createdAt')">
            <el-input
              :placeholder="$t('table.input')+$t('table.createdAt')"
              disabled
              v-model="temp.createdAt"
            ></el-input>
          </el-form-item>

          <el-form-item :label="$t('table.updatedAt')">
            <el-input
              :placeholder="$t('table.input')+$t('table.updatedAt')"
              disabled
              v-model="temp.updatedAt"
            ></el-input>
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
    </el-dialog>-->

    <!-- 添加 -->
    <el-dialog :visible.sync="dialogAddNotify">
       <AddNotify :showDialog.sync="dialogAddNotify"/>
    </el-dialog>


    <!-- 修改 -->
   <el-dialog :visible.sync="dialogEditNotify">
       <ChangeNotify :editDialog.sync="dialogEditNotify" :editContent='temp' :current='current'/>
    </el-dialog>


  </div>

</template>
<script>
import { mapActions, mapGetters } from "vuex";
import MdInput from "@/components/MDinput";
import { resetPage } from "@/utils/sendDataProcess";
import AddNotify from "@/views/conf/addNotify";
import ChangeNotify from "@/views/conf/changeNotify";
import clientVue from './client.vue';

export default {
  components: {
    MdInput,
    AddNotify,
    ChangeNotify
  },
  data() {
    return {
      rules: {},
      current: 0,
      pageHeight: 50,
      form: {
        agentId: "",
        agentIp: "",
        agentPort: "",
        siteUrl: "",
        clientUserId: ""
      },
      listQuery: {
        page: 1,
        limit: 50
      },
      dialogAddNotify:false,
      dialogEditNotify:false,
      // temp: {
      //   Id: "",
      //   agentId: "",
      //   agentIp: "",
      //   agentPort: "",
      //   callBackUrl: "",
      //   clientUserId: "",
      //   outCallBackUrl: "",
      //   siteUrl: "",
      //   state: 1
      // },
      temp:{},
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,
      oldObj: {},
      paginationShow: true
    };
  },
  mounted() {
    if (this.isView.isClient === 1) this.searchOrderClientName();

  },
  computed: {
    ...mapGetters([
      "notifyList",
      "notifyListCount",
      "isView",
      "orderClientName"
    ]),

  },
  methods: {
    ...mapActions(["searchNotifyList", "searchOrderClientName"]),

    handleClean() {
      this.form = {
        agentId: "",
        agentIp: "",
        agentPort: "",
        siteUrl: ""
      };
    },
    //  changeClientUserName(row,column){
    //    if(this.orderClientName.length!==0){
    //     let clientItem= this.orderClientName.find(item=>item.value==row.clientUserId)
    //      return  clientItem.label;
    //    }

    // },

    // 分页'
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
          this.screenLoading = true;
          this.searchAble = true;

          const {
            agentId,
            agentIp,
            agentPort,
            siteUrl,
            clientUserId
          } = this.form;

          var data = {
            agentId,
            agentIp,
            agentPort,
            siteUrl,
            page,
            limit,
            clientUserId
          };



          const flag = resetPage(data, this.oldObj);


          this.listQuery.page = flag ? 1 : this.listQuery.page;

          const { page, limit } = this.listQuery;

          // this.oldObj = { agentId, agentIp, agentPort, siteUrl, page, limit };
          this.oldObj = { agentId, agentIp, agentPort, siteUrl};

          data = {
            ...data,
            ...{ page, limit }
          };

          this.searchNotifyList(data)
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

    heightFn() {
      const h = document.documentElement.clientHeight - 260;
      return h || 400;
    },
    handleUpdate(index, row) {


      this.dialogEditNotify=true;

      this.temp = Object.assign({}, row);
      this.current = index;


      // this.Loading = false;
      // this.Disable = false;
      // this.dialogVisible = true;
      // this.$nextTick(() => {
      //   this.$refs["dataForm"].clearValidate();
      // });
    },
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

/* #g-dialog {
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
} */

/* .editForm {
  height: 90%;
  padding: 10px 10px;
} */

/* .addBtn {
  float: right;
} */
</style>
