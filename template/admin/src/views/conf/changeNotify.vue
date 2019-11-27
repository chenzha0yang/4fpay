<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!-- <div class="filter-container">
      <el-button
        type="primary"
        class="el-icon-refresh"
        :disabled="searchAble"
        :loading="screenLoading"
        @click="handleSearch"
      >{{$t('table.refresh')}}</el-button>
    </div> -->
    <!-- <el-row>
      <el-col :span="5">
        <h2>{{$t('table.add')}}</h2>
      </el-col>
      <el-col :span="2" :offset="17">
        <el-button
          type="info"
          size="small"
          class="el-icon-back backBtn"
          @click="backBtn"
        >{{$t('tagsView.back')}}</el-button>
      </el-col>
    </el-row>-->
    <div
      id="form-search-data"
      :element-loading-text="$t('table.searchMsg')"
      v-loading="screenLoading"
    >
      <el-form ref="addForm" :model="form" :rules="rules" label-width="150px">
        <!--平台线路-->
        <!-- <el-form-item
          :label="$t('table.loginClient')"
          prop="clientUserId"
          v-if="isView.isClient === 1"
        >
          <el-select
            v-model="form.clientUserId"
            :placeholder="$t('table.Pselect') + $t('table.loginClient')"
            size="medium"
          >
            <el-option
              v-for="item in orderClientName"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
        </el-form-item> -->
         <el-form-item :label="$t('table.loginClient')" prop="clientUserName" v-if="isView.isClient === 1">
          <el-input :placeholder="$t('table.loginClient')" v-model="form.clientUserName" :disabled="true">
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- 代理线 -->
        <el-form-item :label="$t('table.agent')" prop="agentId" v-if="isView.isAgent === 1">
          <el-input :placeholder="$t('table.input')+$t('table.agent')" v-model="form.agentId" :disabled="true">
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- <el-form-item :label="$t('table.whetherOpen')">
          <el-switch
            v-model="form.state"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-text="$t('tagsView.close')"
            :inactive-text="$t('tagsView.open')"
            :active-value="2"
            :inactive-value="1"
          ></el-switch>
        </el-form-item> -->
        <!-- IP -->
        <el-form-item :label="$t('table.Ip')" prop="agentIp">
          <el-input :placeholder="$t('table.input')+$t('table.Ip')" v-model="form.agentIp">
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- 端口 -->
        <el-form-item :label="$t('table.port')" prop="agentPort">
          <el-input :placeholder="$t('table.input')+$t('table.port')" v-model="form.agentPort">
            <template slot="prepend">
              <i class="el-icon-edit-outline"></i>
            </template>
          </el-input>
        </el-form-item>

        <!--站点域名-->
        <el-form-item :label="$t('table.siteUrl')" prop="siteUrl">
          <el-input :placeholder="$t('table.input')+$t('table.siteUrl')" v-model="form.siteUrl">
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!--入款异步回调路由-->
        <el-form-item :label="$t('table.incomeCallbackUrl')" prop="callBackUrl">
          <el-input
            :placeholder="$t('table.input')+$t('table.incomeCallbackUrl')"
            v-model="form.callBackUrl"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- 出款异步回调路由 -->
        <el-form-item :label="$t('table.outcomeCallbackUrl')" prop="outCallBackUrl" v-if="true">
          <el-input
            :placeholder="$t('table.input')+$t('table.outcomeCallbackUrl')"
            v-model="form.outCallBackUrl"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <el-form-item align="center">
          <!-- <el-button type="warning" @click="cleanForm">{{$t('table.reset')}}</el-button> -->
          <el-button
            type="primary"
            @click="onSubmit"
            :disabled="Disable"
            :loading="Loading"
          >{{$t('table.submit')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import demoRules from "@/utils/demoRules";
import { validateIp, validateURL } from "@/utils/validate";

export default {
  props:{
    editContent:{
      type:Object
    },
     editDialog:{
      type:Boolean
    },
    current:{
      type:Number
    }
  },
  data() {
    const agentIp = (rule, value, callback) => {
      if (validateIp(value)) {
        callback();
      } else {
        if (value === "") {
          if (validateURL(this.form.siteUrl)) {
            callback();
          } else {
            if(this.form.siteUrl!==''){
                callback()
            }
            callback(new Error(this.$t("valiMsg.agentIp")));
          }
        } else {
          callback(new Error(this.$t("valiMsg.IpMsg")));
        }
      }
    };

    const agentPort = (rule, value, callback) => {
      if (value !== "" && value >= 0 && value <= 65535) {
        callback();
      } else {
        if (value === "") {
          if (validateURL(this.form.siteUrl)) {
            callback();
          } else {
             if(this.form.siteUrl!==""){
              callback()
            }else{
               callback(new Error(this.$t("valiMsg.agentPort")));
            }

          }
        } else {
          callback(new Error(this.$t("valiMsg.port")));
        }
      }
    };

    const siteUrl = (rule, value, callback) => {
      if (validateURL(value)) {
        callback();
      } else {
        if (value === "") {
          if (
            validateIp(this.form.agentIp) &&
            this.form.agentPort !== "" &&
            this.form.agentPort >= 0 &&
            this.form.agentPort <= 65535
          ) {
            callback();
          } else {
            callback(new Error(this.$t("valiMsg.siteUrl")));
          }
        } else {
          callback(new Error(this.$t("valiMsg.url")));
        }
      }
    };

    const callBackPath = (rule, value, callback) => {
      const reg = /^\w+(\.\w+)?((\/\w+)?)*$/g;
      if (reg.test(value)) {
        callback();
      } else {
        callback(new Error(this.$t("valiMsg.pathMsg")));
      }
    };

    const outCallBackPath = (rule, value, callback) => {
      const reg = /^\w+(\.\w+)?((\/\w+)?)*$/g;
      if (reg.test(value) || value === "") {
        callback();
      } else {
        callback(new Error(this.$t("valiMsg.pathMsg")));
      }
    };

    return {
      rules: {
        // ...demoRules,
        agentIp: [
          {
            required: false,
            validator: agentIp,
            trigger: "blur"
          }
        ],

        agentPort: [
          {
            required: false,
            validator: agentPort,
            trigger: "blur"
          }
        ],

        siteUrl: [
          {
            required: false,
            validator: siteUrl,
            trigger: "blur"
          }
        ],

        callBackUrl: [
          {
            required: true,
            validator: callBackPath,
            trigger: "blur"
          }
        ],
        outCallBackUrl: [
          {
            required: false,
            validator: outCallBackPath,
            trigger: "blur"
          }
        ]
      },
      form: {
        clientUserId: "",
        agentId: "",
        siteUrl: "",
        callBackUrl: "",
        outCallBackUrl: "",
        agentIp: "",
        agentPort: ""
        // state: 1
      },
      Loading: false,
      Disable: false,
      // screenLoading: true,
      screenLoading:false,
      searchAble: false
    };
  },

  created() {
    // console.log(222,this.editContent);
    // this.handleSearch();
      Object.assign(this.form,this.editContent);
      //  console.log(111, this.form);

  },
  watch:{
   editContent:function(newVal,oldVal){
      Object.assign(this.form,newVal);
   }

  },
  computed: {
    // ...mapGetters(["orderClientName", "isView"])
      ...mapGetters(["orderClientName", "isView"])
  },
  methods: {
    ...mapActions(["editNotifyAgent", "searchNotifyAgent"]),

    handleSearch() {
      this.searchAble = true;
      this.screenLoading = true;
      this.searchNotifyAgent()
        .then(rps => {
          if (!Array.isArray(rps.data)) {
            this.form = Object.assign(this.form, rps.data);
          }
        })
        .catch(err => {})
        .finally(() => {
          this.screenLoading = false;
          this.searchAble = false;
        });
    },

    onSubmit() {

      this.$refs["addForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          const {
            Id,
            siteUrl,
            callBackUrl,
            outCallBackUrl,
            agentIp,
            agentPort
          } = this.form;


          const data = {
            Id,
            siteUrl,
            callBackUrl,
            outCallBackUrl,
            agentIp,
            agentPort
          };


          this.editNotifyAgent({
            name: "notify",
            type: "edit",
            current: this.current,
            data})
            .then(rps => {
              this.alertMessage(rps.rps.msg);
              this.clearForm();
              this.$emit('update:editDialog',false);
              this.Disable = false;
              this.Loading = false;
            })
            .catch(err => {})
            .finally(() => {
              this.Disable = false;
              this.Loading = false;
            });
        }
      });
    },
     clearForm(){
      this.form={
        clientUserId: "",
        agentId: "",
        siteUrl: "",
        callBackUrl: "",
        outCallBackUrl: "",
        agentIp: "",
        agentPort: "",
        // state: 1
      }
    }
  }
};
</script>
<style scoped>
#form-search-data {
  width: 80%;
  margin: 0px auto 0;
}

.backBtn {
  margin-top: 17px;
}
</style>
