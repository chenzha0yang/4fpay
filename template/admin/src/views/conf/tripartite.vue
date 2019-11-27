<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <!-- 查询操作 -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <!-- 商户类型 -->
        <el-form-item class="filter-item">
          <el-tooltip :content="$t('table.tripartConfName')" placement="top">
            <el-select
              v-model="form.payId"
              filterable
              clearable
              :placeholder="$t('table.tripartConfName')"
            >
              <el-option
                v-for="item in orderConfigLists"
                :key="item.value"
                :label="item.confName"
                :value="item.payId"
              ></el-option>
            </el-select>
          </el-tooltip>
        </el-form-item>

        <!-- IP白名单  -->
        <el-form-item class="filter-item w150">
          <el-tooltip :content="$t('table.whiteListState')" placement="top">
            <el-select
              v-model="form.whiteListState"
              filterable
              clearable
              :placeholder="$t('table.whiteListState')"
            >
              <el-option
                v-for="(item,index) in selectList"
                :key="index"
                :label="$t('maintain.' + item.label)"
                :value="index"
              ></el-option>
            </el-select>
          </el-tooltip>
        </el-form-item>

        <!--开启通道-->
        <el-form-item class="filter-item w150">
          <el-tooltip :content="$t('table.whetherOpen')" placement="top">
            <el-select
              filterable
              clearable
              v-model="form.isStatus"
              :placeholder="$t('table.whetherOpen')"
            >
              <el-option
                v-for="(item,index) in selectList"
                :key="index"
                :label="$t('maintain.' + item.label)"
                :value="index"
              ></el-option>
            </el-select>
          </el-tooltip>
        </el-form-item>

        <!--开启入款-->
        <el-form-item class="filter-item w150">
          <el-tooltip :content="$t('table.inState')" placement="top">
            <el-select
              filterable
              clearable
              v-model="form.inState"
              :placeholder="$t('table.inState')"
            >
              <el-option
                v-for="(item,index) in selectList"
                :key="index"
                :label="$t('maintain.' + item.label)"
                :value="index"
              ></el-option>
            </el-select>
          </el-tooltip>
        </el-form-item>

        <!-- 开启出款 暂时注释掉-->
        <!-- <el-form-item class="filter-item w150">
          <el-tooltip :content="$t('table.outState')" placement="top">
            <el-select
              filterable
              clearable
              v-model="form.outState"
              :placeholder="$t('table.outState')"
            >
              <el-option
                v-for="(item,index) in selectList"
                :key="index"
                :label="$t('maintain.' + item.label)"
                :value="index"
              ></el-option>
            </el-select>
          </el-tooltip>
        </el-form-item>-->
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
          <el-button type="success" class="el-icon-plus" @click="addNew">{{$t('table.add')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
    <!-- 结果列表 -->
    <div>
      <template>
        <el-table
          v-loading="screenLoading"
          :data="tripartList"
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
            :label="$t('table.tripartConfName')"
            prop="confName"
            width="180"
          />
          <el-table-column align="center" :label="$t('table.payMod')" prop="confMod" width="150"/>
          <el-table-column align="center" :label="$t('table.tripartPayCode')" width="150">
            <template slot-scope="scope">
              <el-popover placement="top" trigger="hover">
                <p v-for="item in scope.row.payCodeType" :key="item.id">{{item.name}}:{{item.code}}</p>
                <div slot="reference" class="name-wrapper">
                  <el-tag class="el-icon-caret-right">{{$t('table.payCode')}}</el-tag>
                </div>
              </el-popover>
            </template>
          </el-table-column>

          <el-table-column align="center" :label="$t('table.typeName')" prop="tag">
            <template slot-scope="scope">
              <el-tag
                disable-transitions
                type="success"
                class="pay-tag"
                v-for="(item,index) in scope.row.typeName"
                :key="index"
              >{{ item }}</el-tag>
            </template>
          </el-table-column>

          <el-table-column align="center" :label="$t('table.whetherOpen')" prop="tag" width="100">
            <template slot-scope="{$index,row}">
              <el-switch
                :disabled="$index === current && open"
                v-if="isView.isAgent === 1 && isView.isClient === 1"
                v-model="row.isStatus"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-value="2"
                :inactive-value="1"
                @change="stateChange($index, row,'isStatus','open')"
              ></el-switch>
              <el-tag
                v-else
                :type="row.isStatus==1?'success':'danger'"
              >{{$t('maintain.' + row.isStatus)}}</el-tag>
            </template>
          </el-table-column>

          <el-table-column align="center" :label="$t('table.inState')" prop="tag" width="100">
            <template slot-scope="{$index,row}">
              <el-switch
                :disabled="$index === current && deposit"
                v-if="isView.isAgent === 1 && isView.isClient === 1"
                v-model="row.inState"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-value="2"
                :inactive-value="1"
                @change="stateChange($index, row,'inState','deposit')"
              ></el-switch>
              <el-tag
                v-else
                :type="row.inState==1?'success':'danger'"
              >{{$t('maintain.' + row.inState)}}</el-tag>
            </template>
          </el-table-column>
            <!-- 版本 -->
             <el-table-column align="center" label="版本" prop='version' width="100">
              <template slot-scope="{$index,row}">
              <p>V{{row.version}}</p>
            </template>
          </el-table-column>
            <!-- 版本结束 -->
          <el-table-column
            align="center"
            :label="$t('table.whiteListState')"
            prop="tag"
            width="100"
          >
            <template slot-scope="{$index,row}">
              <el-switch
                :disabled="$index === current && whiteList"
                v-if="isView.isAgent === 1 && isView.isClient === 1"
                v-model="row.whiteListState"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-value="2"
                :inactive-value="1"
                @change="stateChange($index, row,'whiteListState','whiteList')"
              ></el-switch>
              <el-tag
                v-else
                :type="row.inState==1?'success':'danger'"
              >{{$t('maintain.' + row.inState)}}</el-tag>
            </template>
          </el-table-column>

          <!-- 出款 暂时注释掉 -->
          <!-- <el-table-column align="center" :label="$t('table.outState')" prop="tag" width="100">
            <template slot-scope="{$index,row}">
              <el-switch
                :disabled="$index === current && dispensing"
                v-if="isView.isAgent === 1 && isView.isClient === 1"
                v-model="row.outState"
                active-color="#ff4949"
                inactive-color="#13ce66"
                :active-value="2"
                :inactive-value="1"
                @change="stateChange($index, row,'outState','dispensing')"
              ></el-switch>
              <el-tag
                v-else
                :type="row.outState==1?'success':'danger'"
              >{{$t('maintain.' + row.outState)}}</el-tag>
            </template>
          </el-table-column>-->
          <el-table-column align="center" :label="$t('table.inComeBankList')" width="120">
            <template slot-scope="scope">
              <el-button
                size="mini"
                @click="jumpRouter(scope.row.payId)"
              >{{ $t('table.inComeBankList') }}</el-button>
            </template>
          </el-table-column>

          <el-table-column
            align="center"
            :label="$t('table.actions')"
            class-name="small-padding fixed-width"
            v-if="isView.isAgent === 1 && isView.isClient === 1"
            width="80"
          >
            <template slot-scope="scope">
              <el-tooltip :content="$t('table.Update')" placement="top">
                <i @click="handleUpdate(scope.$index, scope.row)" class="el-icon-edit"></i>
              </el-tooltip>
            </template>
          </el-table-column>
        </el-table>
      </template>
    </div>
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
        :total="tripartListCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import MdInput from "@/components/MDinput";
import { resetPage } from "@/utils/sendDataProcess";
import { MessageBox, version } from "element-ui";
import modifyStatusCallback from "@/utils/modifyStatusCallback";

export default {
  components: {
    MdInput
  },

  data() {
    return {
      rules: {},
      current: 0,

      pageHeight: 50,
      form: {
        payId: "", //商户类型,
        inState: "", //是否开启入款
        whiteListState: "",
        isStatus: "",
        outState: ""
      },
      selectList: [
        { value: "", label: "all" },
        { value: 1, label: 1 },
        { value: 2, label: 2 }
      ],

      listQuery: {
        page: 1,
        limit: 50
      },

      dialogVisible: false,
      screenLoading: false,
      searchAble: false,
      oldObj: {},
      paginationShow: true,
      deposit: false,
      dispensing: false,
      open: false,
      whiteList: false
    };
  },
  created() {
    this.searhOrderConfigLists();

  },

  computed: {
    ...mapGetters([
      "tripartList",
      "tripartListCount",
      "orderConfigLists",
      "payTypeList",
      "isView"
    ])
  },
  methods: {
    ...mapActions([
      "searchTripartList",
      "searchPayType",
      "searhOrderConfigLists",
      "TriparJumpBank",
      "setTripartItem",
      "editTripartiteList"
    ]),

    handleClean() {
      this.form = {
        payId: "", //商户类型,
        inState: "", //是否开启入款
        whiteListState: "",
        isStatus: "",
        outState: ""
      };
    },

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },

    // 编辑
    handleUpdate(index, row) {
      const params = {
        item: Object.assign({}, row),
        index: index
      };
      this.setTripartItem(params).then(() => {
        this.$router.push({ name: "changeTripart" });
      });
    },

    //    查询
    handleSearch() {
      console.log(this.tripartList);
      this.$refs["searchForm"].validate(valid => {
        if (valid) {
          this.screenLoading = true;
          this.searchAble = true;

          const {
            payId,
            inState,
            whiteListState,
            isStatus,
            outState
          } = this.form;

          var data = {
            payId,
            inState,
            whiteListState,
            isStatus,
            outState
          };

          const flag = resetPage(data, this.oldObj);

          this.listQuery.page = flag ? 1 : this.listQuery.page;

          const { page, limit } = this.listQuery;

          this.oldObj = { payId, inState, whiteListState };

          data = {
            ...data,
            ...{ page, limit }
          };

          this.searchTripartList(data)
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
    stateChange(index, row, str, attr = "open") {
        console.log(row[str]);
      // let msg =
      //   row[str] === 1
      //     ? this.$t(`switchMsg.${attr}[2]`)
      //     : this.$t(`switchMsg.${attr}[1]`);
       let msg;
       if(attr=='version'){
           if(row[str]==2){
              msg='是否开启版本v2'
           }else{
              msg='是否开启版本v1'
           }

       }else{
          msg =
        row[str] === 1
          ? this.$t(`switchMsg.${attr}[2]`)
          : this.$t(`switchMsg.${attr}[1]`);
       }
      this.current = index;

      const { payId } = row;
      const data = { payId };
      data[str] = row[str];

      MessageBox.confirm(msg, {
        confirmButtonText: this.$t("alertMsg.confirm"),
        cancelButtonText: this.$t("alertMsg.cencelOperation"),
        type: "warning"
      })
        .then(res => {
          this[attr] = true;
          this.sendUpdateData(data, attr);
        })
        .catch(err => {
          this.sendUpdateDataCallback(data);
        });
    },

    sendUpdateDataCallback(data) {
      modifyStatusCallback("_putConfig", "tripart", data, this.current);
    },

    sendUpdateData(data, attr) {
      this.editTripartiteList({
        name: "tripart",
        type: "edit",
        data,
        current: this.current
      })
        .then(res => {
          this.alertMessage(res.rps.msg);
        })
        .catch(err => {
          this.sendUpdateDataCallback(data);
        })
        .finally(() => {
          this[attr] = false;
        });
    },

    // 新增
    addNew() {
      this.$router.push({ name: "addTripart" });
    },

    jumpRouter(payId) {
      this.TriparJumpBank(payId);
      this.$router.push({ name: "incomeBank" });
    },

    heightFn() {
      const h = document.documentElement.clientHeight - 260;
      return h || 400;
    }
  }
};
</script>

<style scoped>
.pay-tag {
  margin: 5px;
}
.w150 {
  width: 150px;
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
</style>
