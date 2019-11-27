<template>
  <div
    class="dashboard-container"
    v-loading="screenLoading"
    :element-loading-text="$t('table.searchMsg')"
  >
    <div class="filter-container">
      <el-button
        type="primary"
        :disabled="!menuList.length"
        @click="openList()"
      >{{$t('table.' + open)}}</el-button>
      <el-button type="primary" :loading="searchAble" @click="handleSearch">{{$t('table.refresh')}}</el-button>
    </div>
    <div id="g-account">
      <div class="content">
        <el-row :gutter="20" type="flex" justify="space-between">
          <!-- 左边目录结构 -->
          <el-col :span="13" class="menu-left">
            <div class="grid-content bg-purple-dark">
              <el-tree
                v-if="treeShow"
                :data="menuList"
                node-key="Id"
                :default-expand-all="defaultExpand"
                highlight-current
                :expand-on-click-node="false"
                :default-expanded-keys="allId"
                @node-expand="nodeExpand"
                @node-collapse="nodeCollapse"
              >
                <span class="custom-tree-node tree-width" slot-scope="{ node, data }">
                  <span class="dataLabel">{{ node.label }}</span>
                  <span class="dataPath">{{ data.path }}</span>
                  <span class="select-methods">
                    <el-button type="text" size="mini" icon="el-icon-edit" @click="append(data)"></el-button>
                    <el-button
                      type="text"
                      size="mini"
                      icon="el-icon-delete"
                      @click="deleteData(node, data)"
                    ></el-button>
                  </span>
                </span>
              </el-tree>
            </div>
          </el-col>

          <!-- 右边新增结构 -->
          <el-col :span="10" class="menu-right">
            <div class="grid-content">
              <div class="menu-right-title">
                <span
                  :class="{ 'no-redirect':dialogStatus === 'Update' }"
                  @click="cutover('add')"
                >{{$t('table.add')}}</span>
                <b v-if="dialogStatus === 'Update'">&nbsp;/&nbsp;</b>
                <span
                  class="Update"
                  :class="{ 'no-redirect':dialogStatus === 'add' }"
                  v-if="dialogStatus === 'Update'"
                >{{$t('table.Update')}}</span>
              </div>

              <el-form :model="temp" :rules="demoRules" ref="formData" label-width="80px">
                <el-form-item :label="$t('menu.root')">
                  <!-- 父级菜单 -->
                  <template>
                    <el-select v-model="temp.parentId" clearable :placeholder="$t('menu.root')">
                      <el-option
                        v-for="item in setMenuList"
                        :key="item.Id"
                        :label="item.label"
                        :value="item.Id"
                      ></el-option>
                    </el-select>
                  </template>
                </el-form-item>

                <el-form-item :label="$t('menu.menuTitle')" prop="label">
                  <!-- 标题 -->
                  <template>
                    <el-input
                      :placeholder="$t('table.input') + $t('menu.menuTitle')"
                      maxlength="12"
                      v-model="temp.label"
                    >
                      <template slot="prepend">
                        <i class="el-icon-edit"></i>
                      </template>
                    </el-input>
                  </template>
                </el-form-item>

                <el-form-item :label="$t('menu.icon')" prop="icon">
                  <!-- 图标 -->
                  <template>
                    <el-input v-model="temp.icon" @focus="dialogFormVisible =true" maxlength="12">
                      <template slot="prepend">
                        <i class="el-icon-edit"></i>
                      </template>
                    </el-input>
                  </template>
                </el-form-item>

                <el-form-item :label="$t('menu.path')" prop="path">
                  <!-- 路径 -->
                  <template>
                    <el-input :placeholder="$t('table.input')+$t('menu.path')" v-model="temp.path">
                      <template slot="prepend">
                        <i class="el-icon-edit"></i>
                      </template>
                    </el-input>
                  </template>
                </el-form-item>

                <el-form-item>
                  <el-button
                    type="primary"
                    size="medium"
                    @click="createData"
                    v-if="dialogStatus === 'add'"
                  >{{$t('table.submit')}}</el-button>
                  <el-button
                    type="primary"
                    size="medium"
                    @click="updateData"
                    v-if="dialogStatus === 'Update'"
                  >{{$t('table.submit')}}</el-button>
                  <el-button type="warning" size="medium" @click="onReset">{{$t('table.reset')}}</el-button>
                </el-form-item>
              </el-form>
            </div>
          </el-col>
        </el-row>
      </div>
    </div>
    <el-dialog id="dialog-form-header" :title="$t('table.icons')" :visible.sync="dialogFormVisible">
      <div class="clear">
        <div
          class="icon-item"
          :class="{
          'is-selected':item === temp.icon
        }"
          v-for="item of iconsMap"
          :key="item"
          @click="handleClipboard(item)"
        >
          <svg-icon class-name="disabled" :icon-class="item"/>
        </div>
      </div>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">{{$t('table.cancel')}}</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import icons from "@/icons/generateIconsView";
import { getAllIdFunction } from "@/utils/sendDataProcess";
import demoRules from "@/utils/demoRules";

export default {
  name: "menuSet",
  data() {
    return {
      demoRules: demoRules,
      treeShow: true,
      allId: [],
      open: "open",
      openMap: {
        open: "open",
        shut: "shut"
      },
      dialogFormVisible: false,
      iconsMap: [],
      defaultExpand: false,

      screenLoading: false,
      searchAble: false,
      temp: {
        parentId: "",
        icon: "",
        label: "",
        path: ""
      },
      dialogStatus: "add",
      textMap: {
        Update: "Update",
        add: "add"
      },
      menuList: [],
      setMenuList: [],
      Archive: {}
    };
  },

  mounted() {
    this.menuList = this.menuSetList;
    this.iconsMap = icons.state.iconsMap.map(i => {
      return i.default.id.split("-")[1];
    });
  },
  computed: {
    ...mapGetters(["menuSetList"])
  },
  methods: {
    ...mapActions(["searchMenuSet", "addMenuSet", "delMenuSet", "putMenuSet"]),

    //新增提交
    createData() {
      this.$refs["formData"].validate(valid => {
        if (valid) {
          this.screenLoading = true;

          var { parentId, label, icon, path } = this.temp;
          parentId = parentId === "" ? 0 : parentId;
          const data = { parentId, label, icon, path };

          this.addMenuSet(data)
            .then(response => {
              this.menuList = this.menuSetList;
              this.setMenuList = [
                {
                  Id: 0,
                  label: this.$t("table.gradeTopMenu")
                },
                ...this.menuSetList
              ];
              this.alertMessage(response.msg);
              this.resetTemp();
            })
            .catch(error => {})
            .finally(() => {
              this.$nextTick(() => {
                this.screenLoading = false;
              });
            });
        }
      });
    },
    // 删除提交
    deleteData(node, row) {
      this.$confirm(this.$t("alertMsg.delMenu"), this.$t("table.prompt"), {
        confirmButtonText: this.$t("table.delete"),
        cancelButtonText: this.$t("table.cancel"),
        type: "warning"
      })
        .then(() => {
          const { Id } = row;
          const data = { Id };
          this.delMenuSet(data).then(response => {
            this.menuList = this.menuSetList;
            this.setMenuList = [
              {
                Id: 0,
                label: this.$t("table.gradeTopMenu")
              },
              ...this.menuSetList
            ];
            this.alertMessage(response.msg);
          });
        })
        .catch(() => {});
    },
    // 修改提交
    updateData() {
      this.$refs["formData"].validate(valid => {
        if (valid) {
          this.screenLoading = true;
          var { parentId, label, icon, path, Id, children } = this.temp;
          parentId = parentId === "" ? 0 : parentId;
          children = children === undefined ? [] : children;
          const data = { parentId, label, icon, path, Id, children };

          this.putMenuSet(data)
            .then(response => {
              this.alertMessage(response.msg);
              this.menuList = this.menuSetList;
              this.setMenuList = [
                {
                  Id: 0,
                  label: this.$t("table.gradeTopMenu")
                },
                ...this.menuSetList
              ];
              this.cutover("add");
            })
            .catch(error => {})
            .finally(() => {
              this.$nextTick(() => {
                this.screenLoading = false;
              });
            });
        }
      });
    },

    // 刷新
    handleSearch() {
      this.screenLoading = true;
      this.searchMenuSet()
        .then(() => {
          this.menuList = this.menuSetList;
          this.setMenuList = [
            {
              Id: 0,
              label: this.$t("table.gradeTopMenu")
            },
            ...this.menuSetList
          ];
          this.cutover("add");
          this.resetTemp();
        })
        .catch(error => {})
        .finally(() => {
          this.screenLoading = false;
        });
    },
    // 重置
    onReset() {
      if (this.dialogStatus === "add") {
        this.resetTemp();
      } else {
        this.temp = Object.assign({}, this.Archive);
      }
    },
    // 切换
    cutover(str) {
      if (str === this.dialogStatus && this.dialogStatus === "Update") {
        return;
      }
      this.dialogStatus = "add";
      this.resetTemp();
      // this.$nextTick(() => {
      //   this.$refs.formData.clearValidate();
      // });
    },
    // 重置表单
    resetTemp() {
      this.$nextTick(() => {
        this.$refs.formData.resetFields();
      });
    },

    // 修改
    append(row) {
      this.temp = Object.assign({}, row);
      this.Archive = Object.assign({}, row);
      this.dialogStatus = "Update";
    },

    // 图标选择
    handleClipboard(item) {
      this.temp.icon = item;
      this.dialogFormVisible = false;
    },
    nodeExpand(data) {
      this.allId.push(data.Id);
      this.allId = Array.from(new Set(this.allId));
    },
    nodeCollapse(data) {
      const index = this.allId.indexOf(data.Id);
      this.allId.splice(index, 1);
    },

    // 展开/收起
    openList() {
      this.defaultExpand = !this.defaultExpand;
      this.treeShow = false;
      if (this.open === "open") {
        this.open = "shut";
        this.$nextTick(() => {
          this.allId = getAllIdFunction(this.menuSetList, "Id", "parentId");
          this.treeShow = true;
        });
      } else {
        this.open = "open";
        this.$nextTick(() => {
          this.allId = [];
          this.treeShow = true;
        });
      }
    }
  }
};
</script>
<style lang="scss" scoped>
// @import url("../../styles/global.scss");

.filter-container {
  margin: 15px 30px 0;
}
#g-account {
  min-width: 500px;
  min-height: 400px;
  width: 80%;
  height: calc(100vh - 250px);
  overflow: auto;
  border-radius: 5px;
  box-shadow: #ccc 0px 0px 1px;
  margin: 15px auto;
}

#g-account .header {
  font-size: 24px;
  text-align: center;
  line-height: 50px;
  border-bottom: 1px solid #ccc;
}

#g-account .content {
  padding: 20px;
}

#g-account .content .item {
  display: flex;
  flex-direction: row;
  height: 50px;
  line-height: 50px;
  border-bottom: 1px solid #eee;
}

#g-account .content .item-key {
  width: 150px;
  text-align: right;
}

#g-account .content .item-value {
  padding-left: 10px;
}

.grid-content {
  border-radius: 4px;
  min-height: 36px;
  padding-top: 10px;
}
.select-methods {
  float: right;
  margin-right: 10px;
}
.dataLabel,
.dataPath {
  line-height: 28px;
}

.tree-width {
  width: 100%;
}
.menu-right {
  background: #e8e8e8;
}
.menu-left {
  height: 450px;
  overflow: auto;
  border: 1px solid #dcdfe6;
  border-radius: 5px;
}
.menu-right-title {
  border-bottom: 1px solid #bdb5b5;
  padding-bottom: 5px;
  margin-bottom: 10px;
  span {
    font-weight: 700;
    cursor: text;
  }
  span.no-redirect {
    font-weight: 400;
    color: #97a8be;
    cursor: default;
  }
  .span.Update {
    cursor: text;
  }
}
.dataPath {
  padding-left: 40px;
  color: #409eff;
}
.icons-wrapper {
  margin: 0 auto;
}
.clear {
  max-height: 400px;
  overflow-y: auto;
}
.icon-item {
  margin: 10px;
  height: 50px;
  line-height: 50px;
  text-align: center;
  width: 50px;
  float: left;
  font-size: 30px;
  color: #24292e;
  border: 1px solid transparent;
  cursor: pointer;
  span {
    display: block;
    font-size: 16px;
    margin-top: 10px;
  }
}
.icon-item.is-selected{
  border-color: #666;
}

.disabled {
  pointer-events: none;
}
</style>
