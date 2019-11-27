
<template>
  <div
    class="dashboard-container"
    v-loading="screenLoading"
    :element-loading-text="$t('table.searchMsg')"
  >
    <div class="filter-container">
      <el-button
        type="primary"
        :disabled="!permissionList.length"
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
                :data="permissionList"
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
                    <el-button
                      type="text"
                      size="mini"
                      icon="el-icon-edit"
                      @click="() => append(data)"
                    ></el-button>
                    <el-button
                      type="text"
                      size="mini"
                      icon="el-icon-delete"
                      @click="() => deleteData(node, data)"
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
                <!-- 父级菜单 -->
                <el-form-item :label="$t('menu.fatherPerm')">
                  <template>
                    <el-select
                      v-model="temp.parentId"
                      clearable
                      :placeholder="$t('menu.fatherPerm')"
                    >
                      <el-option
                        v-for="item in setPermissionList"
                        v-if="item.Id !== temp.Id"
                        :key="item.Id"
                        :label="item.label"
                        :value="item.Id"
                      ></el-option>
                    </el-select>
                  </template>
                </el-form-item>

                <el-form-item :label="$t('menu.permName')" prop="label">
                  <!-- 名称 -->
                  <template>
                    <el-input
                      :placeholder="$t('table.input')+$t('menu.permName')"
                      v-model="temp.label"
                    >
                      <template slot="prepend">
                        <i class="el-icon-edit"></i>
                      </template>
                    </el-input>
                  </template>
                </el-form-item>

                <el-form-item :label="$t('menu.identify')" prop="slug">
                  <!-- 标识 -->
                  <template>
                    <el-input
                      :placeholder="$t('table.input')+$t('menu.identify')"
                      v-model="temp.slug"
                    >
                      <template slot="prepend">
                        <i class="el-icon-edit"></i>
                      </template>
                    </el-input>
                  </template>
                </el-form-item>

                <el-form-item :label="$t('menu.getType')" prop="method">
                  <!-- 获取方式 -->
                  <template>
                    <el-select v-model="temp.method" multiple :placeholder="$t('menu.getType')">
                      <el-option
                        v-for="item in getTypeList"
                        :key="item"
                        :label="item"
                        :value="item"
                      ></el-option>
                    </el-select>
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
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import { getAllIdFunction } from "@/utils/sendDataProcess";
import demoRules from "@/utils/demoRules";

export default {
  components: {},
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
      defaultExpand: false,

      screenLoading: false,
      searchAble: false,
      temp: {
        parentId: "",
        icon: "",
        label: "",
        path: "",
        method: [],
        Id: "",
        slug: ""
      },
      dialogStatus: "add",
      textMap: {
        Update: "Update",
        add: "add"
      },

      permissionList: [],
      setPermissionList: [],
      getTypeList: ["POST", "DELETE", "PUT", "GET"],
      Archive: {}
    };
  },

  computed: {
    ...mapGetters(["permissionSetList"])
  },
  method() {
    this.permissionList = this.permissionSetList;
  },
  methods: {
    ...mapActions([
      "searchPermissionSet",
      "addPermissionSet",
      "delPermissionSet",
      "putPermissionSet"
    ]),

    //新增提交
    createData() {
      this.$refs["formData"].validate(valid => {
        if (valid) {
          this.screenLoading = true;

          var { parentId, label, slug, path, method } = this.temp;
          parentId = parentId === "" ? 0 : parentId;
          const data = { parentId, label, slug, path, method };

          this.addPermissionSet(data)
            .then(response => {
              this.permissionList = this.permissionSetList;
              this.setPermissionList = [
                {
                  Id: 0,
                  label: this.$t("table.gradeTopPerm")
                },
                ...this.permissionSetList
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
      this.$confirm(this.$t("alertMsg.delPerm"), this.$t("table.prompt"), {
        confirmButtonText: this.$t("table.delete"),
        cancelButtonText: this.$t("table.cancel"),
        type: "warning"
      })
        .then(() => {
          const { Id } = row;
          const data = { Id };
          this.delPermissionSet(data).then(response => {
            this.permissionList = this.permissionSetList;
            this.setPermissionList = [
              {
                Id: 0,
                label: this.$t("table.gradeTopPerm")
              },
              ...this.permissionSetList
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

          var { parentId, label, path, slug, method, Id, children } = this.temp;

          parentId = parentId === "" ? 0 : parentId;
          children = children === undefined ? [] : children;

          const data = { parentId, label, path, slug, method, Id, children };

          this.putPermissionSet(data)
            .then(response => {
              this.permissionList = this.permissionSetList;
              this.setPermissionList = [
                {
                  Id: 0,
                  label: this.$t("table.gradeTopPerm")
                },
                ...this.permissionSetList
              ];
              this.cutover("add");
              this.alertMessage(response.msg);
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
      this.searchPermissionSet()
        .then(() => {
          this.permissionList = this.permissionSetList;
          this.setPermissionList = [
            {
              Id: 0,
              label: this.$t("table.gradeTopPerm")
            },
            ...this.permissionSetList
          ];
          this.cutover();
          this.resetTemp();
        })
        .catch(error => {})
        .finally(() => {
          this.screenLoading = false;
        });
    },
    // 切换
    cutover(str) {
      if (str === this.dialogStatus && this.dialogStatus === "Update") {
        return;
      }
      this.dialogStatus = "add";
      this.resetTemp();
    },
    // 重置表单
    resetTemp() {
      // this.temp = {
      //   parentId: "",
      //   icon: "",
      //   label: "",
      //   path: "",
      //   method: [],
      //   Id: "",
      //   slug: ""
      // };
      this.$nextTick(() => {
        this.$refs.formData.resetFields();
      });
    },
    onReset() {
      if (this.dialogStatus === "add") {
        this.resetTemp();
      } else {
        this.temp = Object.assign({}, this.Archive);
      }
    },
    // 修改
    append(row) {
      this.temp = Object.assign({}, row);
      this.Archive = Object.assign({}, row);
      this.dialogStatus = "Update";
    },
    // 展开/收起
    openList() {
      this.defaultExpand = !this.defaultExpand;
      this.treeShow = false;
      if (this.open === "open") {
        this.open = "shut";
        this.$nextTick(() => {
          this.allId = getAllIdFunction(
            this.permissionSetList,
            "Id",
            "parentId"
          );
          this.treeShow = true;
        });
      } else {
        this.open = "open";
        this.$nextTick(() => {
          this.allId = [];
          this.treeShow = true;
        });
      }
    },
    nodeExpand(data) {
      this.allId.push(data.Id);
      this.allId = Array.from(new Set(this.allId));
    },
    nodeCollapse(data) {
      const index = this.allId.indexOf(data.Id);
      this.allId.splice(index, 1);
    }
  }
  // created() {}
};
</script>
<style lang="scss"  scoped>
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
  text-align: center;
  width: 50px;
  float: left;
  font-size: 30px;
  color: #24292e;
  cursor: pointer;
  span {
    display: block;
    font-size: 16px;
    margin-top: 10px;
  }
}

.disabled {
  pointer-events: none;
}
</style>
