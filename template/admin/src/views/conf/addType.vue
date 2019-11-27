<template>
  <div id="memberlist" class="app-container calendar-list-container">
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
        <el-form-item :label="$t('route.payType')" prop="typeName">
          <el-input :placeholder="$t('table.input')+$t('route.payType')" v-model="form.typeName"
            maxlength="12"
            >
            <template slot="prepend"><i class="el-icon-edit"></i></template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('table.englishName')" prop="englishName">
          <el-input :placeholder="$t('table.input')+$t('table.englishName')" v-model="form.englishName"
            @change="englishNameChange"
            maxlength="12"
          >
            <template slot="prepend"><i class="el-icon-edit"></i></template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('table.ifStatus')">
          <el-switch
            v-model="form.isStatus"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-text="$t('tagsView.close')"
            :inactive-text="$t('tagsView.open')"
            :active-value=2
            :inactive-value=1
          >
          </el-switch>
        </el-form-item>

        <el-form-item align='center'>
          <el-button type="warning" @click="cleanForm">{{$t('table.reset')}}</el-button>
          <el-button type="primary" @click="onSubmit" :disabled="Disable" :loading="Loading">{{$t('table.submit')}}</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>
<script>
import { mapActions } from "vuex";
import demoRules from "@/utils/demoRules";

export default {
  data() {
    return {
      rules: demoRules,
      form: {
        englishName: "",
        isStatus: 1,
        typeName: ""
      },
      Disable: false,
      Loading: false
    };
  },

  methods: {
    ...mapActions(["addPayTypeList"]),

    cleanForm() {
      this.$refs["addForm"].resetFields();
    },

    englishNameChange(value) {
      this.form.englishName = value.replace(/[^a-zA-Z]/g, "");
    },

    onSubmit() {
      this.$refs["addForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          const { englishName, isStatus, typeName } = this.form;

          const data = { englishName, isStatus, typeName };

          this.addPayTypeList({
            name: "payType",
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
      this.$router.push({ name: "payType" });
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
