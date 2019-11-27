<template>
  <div class="app-container calendar-list-container">
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
      :data="payTypeList"
      :element-loading-text="$t('table.searchMsg')"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :height="heightFn()"
      :empty-text="$t('table.searchdata')"
    >
      <el-table-column align="center" :label="$t('table.id')" prop="typeId" width="80"/>
      <el-table-column align="center" :label="$t('route.payType')" prop="typeName"/>
      <el-table-column align="center" :label="$t('table.whetherOpen')" prop="tag">
        <template slot-scope="{$index,row}">
          <el-switch
            v-if="isView.isAgent === 1 && isView.isClient === 1"
            :disabled="$index === current && open"
            v-model="row.isStatus"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-value="2"
            :inactive-value="1"
            @change="stateChange($index, row, 'isStatus')"
          ></el-switch>
          <el-tag
            v-else
            :type="row.isStatus==1?'success':'danger'"
          >{{$t('maintain.' + row.isStatus)}}</el-tag>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        :label="$t('table.actions')"
        class-name="small-padding fixed-width"
        v-if="isView.isAgent === 1 && isView.isClient === 1"
      >
        <template slot-scope="scope">
          <el-tooltip :content="$t('table.Update')" placement="top">
            <i @click="handleUpdate(scope.$index, scope.row)" class="el-icon-edit"></i>
          </el-tooltip>
          <!-- <el-tooltip :content="$t('table.disable')" placement="top">
            <i @click="handleDisable(scope.$index, scope.row)" class="el-icon-setting"></i>
          </el-tooltip>-->
        </template>
      </el-table-column>
    </el-table>

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
          <el-form-item :label="$t('route.payType')" prop="typeName">
            <el-input
              :placeholder="$t('table.input')+$t('route.payType')"
              v-model="temp.typeName"
              maxlength="12"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.englishName')" prop="englishName">
            <el-input
              :placeholder="$t('table.input')+$t('table.englishName')"
              v-model="temp.englishName"
              @change="englishNameChange"
              maxlength="12"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.ifStatus')">
            <el-switch
              v-model="temp.isStatus"
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
      rules: {},
      current: 0,
      pageHeight: 50,
      listQuery: {
        page: 1,
        limit: 50
      },
      temp: {
        englishName: "",
        isStatus: 1,
        typeName: "",
        typeId: ""
      },
      dialogVisible: false,
      screenLoading: false,
      searchAble: false,
      Loading: false,
      Disable: true,
      open: false
    };
  },

  computed: {
    ...mapGetters(["payTypeList", "isView"])
  },
  methods: {
    ...mapActions(["searchPayType", "editPayTypeList"]),

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.handleSearch();
    },
    handleCurrentChange(val) {
      this.listQuery.page = val;
      this.handleSearch();
    },

    englishNameChange(value) {
      this.temp.englishName = value.replace(/[^a-zA-Z]/g, "");
    },
    stateChange(index, row, str, attr = "open") {
      const msg =
        row[str] === 1
          ? this.$t(`switchMsg.${attr}[2]`)
          : this.$t(`switchMsg.${attr}[1]`);

      this.current = index;

      const { typeId } = row;
      const data = { typeId };
      data[str] = row[str];

      // this.current = index;
      // this.temp = Object.assign({}, row);

      MessageBox.confirm(msg, {
        confirmButtonText: this.$t("alertMsg.confirm"),
        cancelButtonText: this.$t("alertMsg.cencelOperation"),
        type: "warning"
      })
        .then(res => {
          // this.screenLoading = true;
          // this.sendUpdateData(false);
          this[attr] = true;
          this.sendUpdateData(data, attr, true);
        })
        .catch(err => {
          this.sendUpdateDataCallback(data);
          // this._putConfig({
          //   name: "payTypeList",
          //   type: "edit",
          //   params: { isStatus: row.isStatus === 1 ? 2 : 1 },
          //   current: this.current
          // });
        });
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
    // 查询
    handleSearch() {
      this.screenLoading = true;
      this.searchAble = true;
      const { page, limit } = this.listQuery;
      this.searchPayType({ page, limit })
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
        const { englishName, isStatus, typeId, typeName } = this.temp;
        data = { englishName, isStatus, typeId, typeName };
      }

      this.editPayTypeList({
        name: "payType",
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
      modifyStatusCallback("_putConfig", "payType", data, this.current);
    },

    // 删除
    // handleDelete(index, row) {
    //   this.current = index;
    //   this.$confirm(this.$t("alertMsg.toDelete"), this.$t("alertMsg.prompt"), {
    //     confirmButtonText: this.$t("alertMsg.confirm"),
    //     cancelButtonText: this.$t("alertMsg.cancel"),
    //     type: "warning"
    //   }).then(() => {
    //     const typeId = row.typeId;
    //     this.deletePayTypeList({
    //       name: "payTypeList",
    //       type: "del",
    //       current: this.current,
    //       params: { typeId }
    //     }).then(data => {
    //       this.alertMessage(data.rps.msg);
    //     });
    //   });
    // },

    addNew() {
      this.$router.push({ name: "addPayType" });
    },

    heightFn() {
      var h = document.documentElement.clientHeight - 260;
      return h || 400;
    }
  }
};
</script>

<style scoped>
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
  padding: 10px 10px;
}

.el-icon-refresh {
  margin-bottom: 10px;
}

.addBtn {
  float: right;
}
</style>
