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
      <el-form ref="addForm" :model="form" :rules="rules" label-width="150px">
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

        <!-- 是否跳转 -->
        <el-form-item :label="$t('table.isApp')">
          <el-switch
            v-model="form.isApp"
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

        <el-form-item class="filter-item" :label="$t('table.confName')" prop="payId">
          <el-select
            v-model="form.payId"
            filterable
            :placeholder="$t('table.confName')"
            size="medium"
            @change="payIdChange"
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

        <!-- 支付方式 -->
        <el-form-item :label="$t('table.payWay')" prop="typeId">
          <el-select
            filterable
            clearable
            class="filter-item"
            v-model="form.typeId"
            :disabled="selectDisabled"
          >
            <el-option
              v-for="item in payTypeList"
              :key="item.value"
              :label="item.typeName"
              :value="item.typeId"
            ></el-option>
          </el-select>
        </el-form-item>

        <!-- 支付网关 -->
        <el-form-item :label="$t('table.merURL')" prop="merURL">
          <el-input :placeholder="$t('table.input')+$t('table.merURL')" v-model="form.bankUrl">
            <template slot="prepend">
              <i class="el-icon-edit-outline"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- 支付code码 -->
        <el-form-item :label="$t('table.payCode')" prop="payCode">
          <el-input :placeholder="$t('table.input')+$t('table.payCode')" v-model="form.payCode">
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <el-form-item :label="extendNameShow" prop="msgOne" v-if="extendNameShow">
          <el-input :placeholder="$t('table.input')+$t('payName.extendName')" v-model="form.msgOne">
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- <el-form-item :label="$t('payName.extendName2')">
          <el-input :placeholder="$t('table.input')+$t('payName.extendName2')"
                    v-model="form.msgTwo">
            <template slot="prepend"><i class="el-icon-edit"></i></template>
          </el-input>
        </el-form-item>
        <el-form-item :label="$t('payName.extendName3')">
          <el-input :placeholder="$t('table.input')+$t('payName.extendName3')"
                    v-model="form.msgThr">
            <template slot="prepend"><i class="el-icon-edit"></i></template>
          </el-input>
        </el-form-item>-->
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
import { isSpace } from "@/utils/validate";

export default {
  components: {},

  data() {
    const payCode = (rule, value, callback) => {
      const reg = /([0-9a-zA-Z]+)[0-9a-zA-Z_@]*$/;
      if (isSpace(value)) {
        callback(new Error(this.$t("valiMsg.spaceMsg")));
      } else {
        if (reg.test(value)) {
          callback();
        } else {
          if (value === "") {
            callback();
          } else {
            callback(new Error(this.$t("valiMsg.payCode_")));
          }
        }
      }
    };

    return {
      rules: {
        ...demoRules,

        payCode: [
          {
            required: false,
            validator: payCode,
            trigger: "blur"
          }
        ]
      },
      form: {
        agentId: "",
        businessNum: "",
        callbackURL: "",
        clientUserId: 1,
        configName: "",
        createdTime: "",
        isApp: 2,
        isQuick: "",
        md5Key: "",
        merURL: "",
        msgOne: "",
        payCode: "",
        payId: "",
        privateKey: "",
        publicKey: "",
        status: 1,
        typeId: "",
        updatedTime: "",
        userLevel: ""
      },

      Disable: false,
      Loading: false,
      payTypeList: [],
      selectDisabled: true
    };
  },

  created() {
    this.searhOrderConfigLists();
    // this.searchPayType();
  },
  mounted() {
    if (this.isView.isClient === 1) {
      this.searchOrderClientName();
    }
  },
  computed: {
    ...mapGetters([
      "orderClientName", //平台线路
      "orderConfigLists", //商户类型
      // "payTypeList", //商户类型
      "inShop",
      "isView"
    ]),
    extendNameShow() {
      const o = this.orderConfigLists.find(
        v => v.payId === this.form.payId && v.extendName !== ""
      );

      return o ? o.extendName : false;

      // return this.orderConfigLists.some(
      //   v => v.payId === this.form.payId && v.extendName !== ""
      // );
    }
  },
  // searchOrderClientName
  methods: {
    ...mapActions([
      "addManageChild",
      "searhOrderConfigLists",
      // "searchPayType",
      "searchOrderClientName",
      "ownType"
    ]),

    cleanForm() {
      this.$refs["addForm"].resetFields();
    },

    payIdChange(value) {
      this.form.typeId = "";
      this.selectDisabled = true;
      this.ownType({ payId: value })
        .then(res => {
          this.payTypeList = res.data;
          this.selectDisabled = false;
        })
        .catch(err => {})
        .finally(() => {});
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
            configName,
            createdTime,
            isApp,
            isQuick,
            md5Key,
            merURL,
            msgOne,
            payCode,
            payId,
            privateKey,
            publicKey,
            status,
            typeId,
            updatedTime,
            userLevel
          } = this.form;

          const data = {
            businessNum,
            callbackURL,
            configName,
            createdTime,
            isApp,
            isQuick,
            md5Key,
            merURL,
            payCode,
            payId,
            privateKey,
            publicKey,
            status,
            typeId,
            updatedTime,
            userLevel
          };

          const msgOneFlag = this.orderConfigLists.some(
            v => v.payId === payId && v.extendName !== ""
          );

          if (msgOneFlag) data.msgOne = msgOne;

          this.isView.isClient === 1 ? (data.clientUserId = clientUserId) : "";

          this.isView.isAgent === 1 ? (data.agentId = agentId) : "";

          this.addManageChild({
            name: "inShop",
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
      this.$router.push({ name: "incomeShop" });
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
