<template>
  <div id="memberlist" class="app-container calendar-list-container">
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
        <!-- 商户类型 -->
        <el-form-item :label="$t('table.confName')" prop="payId">
          <el-select v-model="form.payId" filterable :placeholder="$t('table.confName')">
            <el-option
              v-for="item in orderConfigLists"
              v-if="item.status === 1"
              :key="item.value"
              :label="item.confName"
              :value="item.payId"
            ></el-option>
          </el-select>
        </el-form-item>

        <el-form-item :label="$t('table.bankName')" prop="uName">
          <el-input
            :placeholder="$t('table.input')+$t('table.bankName')"
            v-model="form.uName"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('table.bankCode')" prop="bankCode">
          <el-input
            :placeholder="$t('table.input')+$t('table.bankCode')"
            v-model="form.bankCode"
            @change="bankCodeChange"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('table.bankStatus')" prop="state">
          <el-switch
            v-model="form.state"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-text="$t('tagsView.close')"
            :inactive-text="$t('tagsView.open')"
            :active-value="2"
            :inactive-value="1"
          ></el-switch>
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
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import demoRules from "@/utils/demoRules";

export default {
  data() {
    function bankverify(value, str) {
      value = value.replace(/，/g, ",");

      if (value) {
        if (value[value.length - 1] === ",")
          value = value.slice(0, value.length - 1);

        const arr = value.split(",");
        const reg = /^[0-9A-Za-z\u4e00-\u9fa5_\-]+$/;
        for (const iterator of arr) {
          if (!reg.test(iterator)) {
            return `valiMsg.${str}`;
          }
        }
      } else {
        return `valiMsg.${str}`;
      }
      return false;
    }

    const bankName = (rule, value, callback) => {
      const s = bankverify(value, "bankName");
      if (s) {
        callback(new Error(this.$t(s)));
      } else {
        callback();
      }
    };

    const bankCode = (rule, value, callback) => {
      const s = bankverify(value, "bankCode");
      if (s) {
        callback(new Error(this.$t(s)));
      } else {
        callback();
      }
    };

    return {
      rules: {
        ...demoRules,
        uName: [
          {
            required: true,
            validator: bankName,
            trigger: "blur"
          }
        ],
        bankCode: [
          {
            required: true,
            validator: bankCode,
            trigger: "blur"
          }
        ]
      },
      form: {
        uName: "",
        payId: "",
        bankCode: "",
        state: 1
      },
      Loading: false,
      Disable: false
    };
  },

  created() {
    this.searhOrderConfigLists();
  },
  computed: {
    ...mapGetters(["orderConfigLists"])
  },
  methods: {
    ...mapActions(["addOutcomeBank", "searhOrderConfigLists"]),

    bankCodeChange(value) {
      this.form.bankCode = value.replace(/[^0-9a-zA-Z,，_\-]/g, "");
    },

    onSubmit() {
      this.$refs["addForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          const { uName, payId, bankCode, state } = this.form;

          const data = { uName, payId, bankCode, state };

          this.addOutcomeBank({
            name: "outcomeBankList",
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

    cleanForm() {
      this.$refs["addForm"].resetFields();
    },

    backBtn() {
      this.$router.push({ name: "outcomeBank" });
    }
  }
};
</script>
<style scoped>
#form-search-data {
  width: 80%;
  margin: 0 auto;
}

.backBtn {
  margin-top: 17px;
}
</style>
