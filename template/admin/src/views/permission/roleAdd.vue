<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <el-row>
      <el-col :span='5'>
        <h2>{{$t('table.add')}}</h2>
      </el-col>
      <el-col :span='2' :offset="17">
        <el-button type="info" size="small" class='el-icon-back backBtn' @click='backBtn'>{{$t('tagsView.back')}}
        </el-button>
      </el-col>
    </el-row>

    <div id="form-search-data">
      <el-form ref="addForm" :model="form" :rules="rules" label-width="150px">


          <el-form-item :label="$t('table.rolesName')" prop="uName">
            <el-input class="w300" v-model="form.uName" :placeholder="$t('table.input')+$t('table.nickname')"
              :maxlength="12">
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.identify')" prop="slug">
            <el-input class="w300" v-model="form.slug" :placeholder="$t('table.input')+$t('table.identify')"
              :maxlength="12">
            </el-input>
          </el-form-item>

          <el-form-item :label="$t('table.setMenu')" prop="menuIds">
             <div>
               <treeselect
                 width="300px"
                 :multiple="true"
                 :options="menuSetList"
                 :placeholder="$t('table.selectPermission')"
                 :defaultExpandLevel="Level"
                 v-model="form.menuIds"
                 :max-height="400"/>
             </div>
           </el-form-item>
            <el-form-item :label="$t('table.setPerm')" prop="permissionIds">
             <div>
               <treeselect
                 width="300px"
                 :multiple="true"
                 :options="permissionSetList"
                 :placeholder="$t('table.selectPermission')"
                 :defaultExpandLevel="Level"
                 v-model="form.permissionIds"
                 :max-height="400"/>
             </div>
           </el-form-item>

          <el-form-item :label="$t('table.isAgent')">
            <el-switch
              v-model="form.isAgent"
              active-color="#ff4949"
              inactive-color="#13ce66"
              :active-text="$t('table.disable')"
              :inactive-text="$t('table.normal')"
              :active-value=2
              :inactive-value=1
            >
            </el-switch>
          </el-form-item>

          <el-form-item :label="$t('table.isClient')">
            <el-switch
              v-model="form.isClient"
              active-color="#ff4949"
              inactive-color="#13ce66"
              :active-text="$t('table.disable')"
              :inactive-text="$t('table.normal')"
              :active-value=2
              :inactive-value=1
            >
            </el-switch>
          </el-form-item>

          <el-form-item :label="$t('table.status')" prop="state">
            <el-switch
              v-model="form.state"
              active-color="#ff4949"
              inactive-color="#13ce66"
              :active-text="$t('table.disable')"
              :inactive-text="$t('table.normal')"
              :active-value=2
              :inactive-value=1
            >
            </el-switch>
          </el-form-item>
        <el-form-item align='center'>
          <el-button type="warning" @click="cleanForm">{{$t('table.reset')}}</el-button>
          <el-button type="primary" :disabled="Disable" :loading="Loading" @click="onSubmit">{{$t('table.submit')}}</el-button>
        </el-form-item>

      </el-form>

    </div>
    <!--   search  end  -->
    <!--  table start  -->
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import Treeselect from "@riophae/vue-treeselect";
import { idSProcess } from "@/utils/sendDataProcess";
import demoRules from "@/utils/demoRules";

export default {
  components: { Treeselect },

  data() {
    return {
      rules: demoRules,
      form: {
        permissionIds: [],
        menuIds: [],
        slug: "",
        state: 1,
        uName: "",
        isClient: 1,
        isAgent: 1
      },
      Level: Infinity,
      Loading: false,
      Disable: false,
      menuSetList: [],
      permissionSetList: []
    };
  },
  // created() {},
  mounted() {
    this.getMenus();
    this.getPermissions();
  },
  computed: {
    // ...mapGetters(["orderConfigLists"])
  },
  methods: {
    ...mapActions(["PgetPermissions", "PgetMenus", "addRoleSet"]),

    cleanForm() {
      this.$refs["addForm"].resetFields();
    },

    getMenus() {
      this.PgetMenus().then(rps => {
        this.menuSetList = rps.data;
      });
    },
    // 获取权限选项
    getPermissions() {
      this.PgetPermissions().then(rps => {
        this.permissionSetList = rps.data;
      });
    },

    onSubmit() {
      this.$refs["addForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          const {
            permissionIds,
            menuIds,
            slug,
            state,
            uName,
            isClient,
            isAgent
          } = this.form;

          const newPermissionIds = idSProcess(
            permissionIds,
            this.permissionSetList[0].children
          );

          const newMenuIds = idSProcess(menuIds, this.menuSetList[0].children);

          const data = {
            permissionIds: newPermissionIds,
            menuIds: newMenuIds,
            slug,
            state,
            uName,
            isClient,
            isAgent
          };

          this.addRoleSet({
            name: "roleSet",
            type: "add",
            data
          })
            .then(rps => {
              this.alertMessage(rps.msg);
              this.backBtn();
            })
            .catch(err => {
              this.Disable = false;
              this.Loading = false;
            });
        }
      });
    },

    backBtn() {
      this.$router.push({ name: "roleSet" });
    }
  }
};
</script>
<style src="@riophae/vue-treeselect/dist/vue-treeselect.min.css"></style>
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

.btn-export {
  text-align: right;
}

#form-search-data {
  width: 80%;
  margin: 0 auto;
}

.backBtn {
  margin-top: 17px;
}
</style>
