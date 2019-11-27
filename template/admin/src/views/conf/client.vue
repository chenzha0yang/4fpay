<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <el-button
      type="primary"
      class="el-icon-refresh"
      :disabled="searchAble"
      @click="handleSearch()"
    >{{$t('table.refresh')}}</el-button>
    <el-button
      type="success"
      class="el-icon-plus addBtn"
      @click="addNew()"
      v-if="isView.isAgent === 1 && isView.isClient === 1"
    >{{$t('table.add')}}</el-button>
    <!-- 结果列表 -->
    <el-table
      v-loading="screenLoading"
      :data="apiClientsList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <el-table-column align="center" :label="$t('table.clientID')" prop="userId" width="80"/>
      <el-table-column align="center" :label="$t('table.portName')" prop="clientName"/>
      <el-table-column align="center" :label="$t('table.certificate')" prop="Secret"/>
      <el-table-column align="center" :label="$t('table.createdAt')" prop="createdAt"></el-table-column>
      <el-table-column align="center" :label="$t('table.updatedAt')" prop="updatedAt"></el-table-column>
      <el-table-column align="center" :label="$t('table.whetherOpen')" prop="tag" width="150">
        <template slot-scope="{$index,row}">
          <el-switch
            v-if="isView.isAgent === 1 && isView.isClient === 1"
            :disabled="$index === current && open"
            v-model="row.Revoked"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-value="2"
            :inactive-value="1"
            @change="stateChange($index, row,'Revoked')"
          ></el-switch>
          <el-tag v-else :type="row.Revoked==1?'success':'danger'">{{$t('maintain.' + row.Revoked)}}</el-tag>
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
          <i @click="handleUpdate(scope.$index, scope.row)" class="el-icon-edit"></i>
          <i @click="handleDelete(scope.$index, scope.row)" class="el-icon-delete"></i>
        </template>
      </el-table-column>
    </el-table>
    <!--  table end  -->
    <!--   page   -->
    <div class="pagination-container" :height="pageHeight">
      <el-pagination
        background
        :current-page.sync="listQuery.page"
        :page-sizes="[50,100,200, 500]"
        :page-size="listQuery.limit"
        layout="total, sizes, prev, pager, next, jumper"
        :total="apiClientsCount"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
    <!--   添加／编辑／详情    -->
    <el-dialog :title="$t('table.Update')" :visible.sync="dialogVisible" width="700px">
      <div v-if="dialogVisible" id="g-dialog">
        <el-form
          ref="dataForm"
          :rules="demoRules"
          :model="temp"
          class="editForm"
          label-width="150px"
        >
          <!-- <el-form-item :label="$t('table.client')+$t('table.id')" prop="userId">
          <el-input :placeholder="$t('table.client')+$t('table.id')" v-model="temp.userId"
            @change="userIdChange"
            maxlength="12"
            >
            <template slot="prepend"><i class="el-icon-edit"></i></template>
          </el-input>
          </el-form-item>-->
          <el-form-item :label="$t('table.portName')" prop="clientName">
            <el-input
              :placeholder="$t('table.input')+$t('table.portName')"
              v-model="temp.clientName"
              maxlength="12"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.certificate')" prop="Secret">
            <el-input
              :placeholder="$t('table.input')+$t('table.certificate')"
              v-model="temp.Secret"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.whetherOpen')">
            <el-switch
              v-model="temp.Revoked"
              active-color="#ff4949"
              inactive-color="#13ce66"
              :active-text="$t('tagsView.close')"
              :inactive-text="$t('tagsView.open')"
              :active-value="2"
              :inactive-value="1"
            ></el-switch>
          </el-form-item>

          <el-form-item align="center">
            <el-button @click="dialogVisible = false">{{$t('table.cancel')}}</el-button>
            <el-button type="primary" @click="updateData" :loading="Loading">{{$t('table.submit')}}</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import demoRules from "@/utils/demoRules";
import { MessageBox } from "element-ui";
import modifyStatusCallback from "@/utils/modifyStatusCallback";

export default {
  data() {
    return {
      demoRules: demoRules,
      current: 0,
      pageHeight: 50,

      listQuery: {
        page: 1,
        limit: 50
      },
      temp: {
        Id: "",
        Secret: "",
        clientName: "",
        Revoked: 1,
        userId: 1
      },
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,
      Disable: true,
      Loading: false,
      open: false
    };
  },

  computed: {
    ...mapGetters(["isView", "apiClientsList", "apiClientsCount"])
  },
  methods: {
    ...mapActions(["searchClientList", "editClientList", "deleteClientList"]),

    // 分页

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },

    userIdChange(value) {
      this.temp.userId = value.replace(/[^0-9]/g, "");
    },

    handleUpdate(index, row) {
      this.temp = Object.assign({}, row);
      this.current = index;
      this.Loading = false;
      this.Disable = false;
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    stateChange(index, row, str, attr = "open") {
      const msg =
        row[str] === 1
          ? this.$t(`switchMsg.${attr}[2]`)
          : this.$t(`switchMsg.${attr}[1]`);

      this.current = index;

      const { Id } = row;
      const data = { Id };
      data[str] = row[str];

      MessageBox.confirm(msg, {
        confirmButtonText: this.$t("alertMsg.confirm"),
        cancelButtonText: this.$t("alertMsg.cencelOperation"),
        type: "warning"
      })
        .then(res => {
          this[attr] = true;
          this.sendUpdateData(data, attr, true);
        })
        .catch(err => {
          this.sendUpdateDataCallback(data);
          // this._putConfig({
          //   name: "apiClientsList",
          //   type: "edit",
          //   params: { Revoked: row.Revoked === 1 ? 2 : 1 },
          //   current: this.current
          // });
        });
    },
    // 查询
    handleSearch() {
      this.screenLoading = true;
      this.searchAble = true;
      const { page, limit } = this.listQuery;
      this.searchClientList({ page, limit })
        .then(rps => {})
        .catch(err => {})
        .finally(() => {
          this.screenLoading = false;
          this.searchAble = false;
        });
    },

    // 修改提交
    updateData() {
      this.$refs["dataForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;
          this.sendUpdateData();
        }
      });
    },

    sendUpdateData(data = {}, attr = "open", flag) {
      if (!flag) {
        const { Id, Secret, clientName, Revoked } = this.temp;
        data = { Id, Secret, clientName, Revoked };
      }

      this.editClientList({
        name: "apiClients",
        type: "edit",
        current: this.current,
        data
      })
        .then(data => {
          this.dialogVisible = false;
          this.alertMessage(data.rps.msg);
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
      modifyStatusCallback("_putConfig", "apiClients", data, this.current);
    },

    // 删除
    handleDelete(index, row) {
      this.current = index;
      this.$confirm(this.$t("alertMsg.toDelete"), this.$t("alertMsg.prompt"), {
        confirmButtonText: this.$t("alertMsg.confirm"),
        cancelButtonText: this.$t("alertMsg.cancel"),
        type: "warning"
      }).then(() => {
        const Id = row.Id;
        this.deleteClientList({
          name: "apiClients",
          type: "del",
          current: this.current,
          data: { Id }
        })
          .then(data => {
            this.alertMessage(data.rps.msg);
          })
          .catch(err => {});
      });
    },

    addNew() {
      this.$router.push({ name: "addClientList" });
    },

    heightFn() {
      var h = document.documentElement.clientHeight - 260;
      return h || 400;
    }
  }
};
</script>

<style scoped lang="scss">
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

.el-icon-refresh {
  margin-bottom: 10px;
}

.editForm {
  height: 90%;
  padding: 10px 10px;
}

.addBtn {
  float: right;
}
</style>
