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
      <el-form ref="addForm" class="clear" :model="form" :rules="rules" label-width="150px">
        <el-form-item :label="$t('table.confName')" prop="payId" >
            <el-select filterable width="300px" class="filter-item" v-model="form.payId"
              :placeholder="$t('table.payId')">
              <el-option v-for="status in orderConfigLists" :key="status.payId" :label="status.confName" :value="status.payId">
              </el-option>
            </el-select>
          </el-form-item>

        <el-form-item :label="$t('table.whiteListIp')" prop="payIp">
          <el-input :placeholder="$t('table.input') + $t('table.whiteListIp')" v-model="form.payIp">
              <template slot="prepend"><i class="el-icon-edit-outline"></i></template>
           </el-input>
        </el-form-item>

        <el-form-item :label="$t('table.whetherOpen')" prop="state">
            <el-switch
              v-model="form.state"
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
import demoRules from "@/utils/demoRules";

export default {
  components: {},

  data() {
    return {
      rules: demoRules,
      form: {
        payId: null,
        payIp: null,
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
    ...mapActions(["addWhiteList", "searhOrderConfigLists"]),

    cleanForm() {
      this.$refs["addForm"].resetFields();
    },

    onSubmit() {
      this.$refs["addForm"].validate(valid => {
        if (valid) {
          this.Loading = true;
          this.Disable = true;

          const { payIp, payId, state } = this.form;

          const data = { payIp, payId, state };

          this.addWhiteList({
            name: "whiteList",
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
      this.$router.push({ name: "whiteIndex" });
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
