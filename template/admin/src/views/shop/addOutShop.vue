<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <el-row>
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
    </el-row>

    <div id="form-search-data">
      <el-form ref="addForm" class="clear" :model="form" :rules="rules" label-width="150px">
        <!--平台线路-->
        <el-form-item
          class="filter-item"
          :label="$t('table.clientName')"
          prop="clientUserId"
          v-if="isView.isClient === 1"
        >
          <el-select
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
        </el-form-item>

        <!--代理线路-->
        <el-form-item :label="$t('table.agentName')" prop="agentId" v-if="isView.isAgent === 1">
          <el-input
            :placeholder="$t('table.input')+$t('table.agentName')"
            v-model="form.agentId"
            maxlength="12"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- 商户ID -->
        <el-form-item :label="$t('table.businessNum')" prop="businessNum">
          <el-input
            :placeholder="$t('table.input')+$t('table.businessNum')"
            v-model="form.businessNum"
            maxlength="64"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!--商户类型-->
        <el-form-item class="filter-item" :label="$t('table.confName')" prop="payId">
          <el-select
            filterable
            v-model="form.payId"
            :placeholder="$t('table.confName')"
            size="medium"
          >
            <el-option
              v-for="item in orderConfigLists"
              v-if="item.status === 1"
              :key="item.value"
              :label="item.confName"
              :value="item.payId"
            />
          </el-select>
        </el-form-item>

        <!-- 返回地址 -->
        <el-form-item :label="$t('table.callBackURL')" prop="callbackURL">
          <el-input
            :placeholder="$t('table.input')+$t('table.callBackURL')"
            v-model="form.callbackURL"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- 是否开启 -->
        <el-form-item :label="$t('table.whetherOpen')">
          <el-switch
            v-model="form.status"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-text="$t('tagsView.close')"
            :inactive-text="$t('tagsView.open')"
            :active-value="2"
            :inactive-value="1"
          ></el-switch>
        </el-form-item>

        <el-form-item :label="$t('table.md5Key')" prop="md5Key">
          <el-input :placeholder="$t('table.input')+$t('table.md5Key')" v-model="form.md5Key"></el-input>
        </el-form-item>

        <el-form-item :label="$t('table.rsaKey')" prop="privateKey">
          <el-input
            type="textarea"
            :autosize="{ minRows: 2, maxRows: 4}"
            :placeholder="$t('table.input')+$t('table.rsaKey')"
            v-model="form.privateKey"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('table.publicKey')" prop="publicKey">
          <el-input
            type="textarea"
            :autosize="{ minRows: 2, maxRows: 4}"
            :placeholder="$t('table.input')+$t('table.publicKey')"
            v-model="form.publicKey"
          ></el-input>
        </el-form-item>

        <el-form-item align="center">
          <el-button type="warning" @click="cleanForm">{{$t('table.reset')}}</el-button>
          <el-button
            type="primary"
            @click="onSubmit"
            :disabled="Disable"
            :loading="Loading"
          >{{$t('table.submit')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--   search  end  -->
    <!--  table start  -->
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import demoRules from "@/utils/demoRules";

export default {
  components: {},

  data() {
    return {
      rules: demoRules,
      pageHeight: 50,
      form: {
        agentId: "",
        businessNum: "",
        callbackURL: "",
        clientUserId: 1,
        md5Key: "",
        payId: "",
        privateKey: "",
        publicKey: "",
        status: 1
      },

      Disable: false,
      Loading: false
    };
  },

  created() {
    this.searhOrderConfigLists();
  },
  mounted() {
    if (this.isView.isClient === 1) {
      this.searchOrderClientName();
    }
  },
  computed: {
    ...mapGetters(["orderClientName", "orderConfigLists", "isView", "roles"])
  },
  methods: {
    ...mapActions([
      "searchOrderClientName",
      "addOutShopChild",
      "searhOrderConfigLists"
    ]),

    cleanForm() {
      this.$refs["addForm"].resetFields();
    },

    onSubmit() {
      this.$refs["addForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          var {
            agentId,
            businessNum,
            callbackURL,
            clientUserId,
            md5Key,
            payId,
            privateKey,
            publicKey,
            status
          } = this.form;

          const data = {
            businessNum,
            callbackURL,
            md5Key,
            payId,
            privateKey,
            publicKey,
            status
          };

          this.isView.isClient === 1 ? (data.clientUserId = clientUserId) : "";

          this.isView.isAgent === 1 ? (data.agentId = agentId) : "";

          this.addOutShopChild({
            name: "outShop",
            type: "add",
            data
          })
            .then(data => {
              this.alertMessage(data.rps.msg);
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
      this.$router.push({ name: "outcomeShop" });
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
