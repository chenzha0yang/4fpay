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
        <el-form-item :label="$t('table.clientID')" prop="userId">
          <el-input
            :placeholder="$t('table.clientID')"
            v-model="form.userId"
            maxlength="12"
            @change="userIdChange"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('table.portName')" prop="clientName">
          <el-input :placeholder="$t('table.input')+$t('table.portName')" v-model="form.clientName">
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('table.certificate')" prop="Secret">
          <el-input :placeholder="$t('table.input')+$t('table.certificate')" v-model="form.Secret">
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('table.whetherOpen')">
          <el-switch
            v-model="form.Revoked"
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
    return {
      rules: demoRules,
      form: {
        Secret: "",
        clientName: "",
        Revoked: 1,
        userId: ""
      },
      Disable: false,
      Loading: false
    };
  },

  methods: {
    ...mapActions(["addClientList"]),

    cleanForm() {
      this.$refs["addForm"].resetFields();
    },

    userIdChange(value) {
      this.form.userId = value.replace(/[^0-9]/g, "");
    },

    onSubmit() {
      this.$refs["addForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;

          const { Secret, clientName, Revoked, userId } = this.form;
          const data = { Secret, clientName, Revoked, userId };

          this.addClientList({
            name: "apiClients",
            type: "add",
            current: this.current,
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
      this.$router.push({ name: "ClientList" });
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
