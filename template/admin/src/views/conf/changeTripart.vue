<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <el-row>
      <el-col :span="5">
        <h2>{{$t('table.Update')}}</h2>
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
      <el-form ref="editForm" :model="form" :rules="rules" label-width="150px">
        <!-- 商户名称 -->
        <el-form-item :label="$t('table.tripartConfName')" prop="confName">
          <el-input
            :placeholder="$t('table.input')+$t('table.tripartConfName')"
            v-model="form.confName"
            maxlength="12"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- 模型名称 -->
        <el-form-item :label="$t('table.payMod')" prop="confMod">
          <el-input
            :placeholder="$t('table.input')+$t('table.payMod')"
            v-model="form.confMod"
            maxlength="16"
            @change="confModChange"
          >
            <template slot="prepend">
              <i class="el-icon-edit"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- 开启入款 -->
        <el-form-item :label="$t('table.inState')" prop="inState">
          <el-switch
            v-model="form.inState"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-text="$t('tagsView.close')"
            :inactive-text="$t('tagsView.open')"
            :active-value="2"
            :inactive-value="1"
          ></el-switch>
        </el-form-item>

        <!-- 开启出款 -->
        <el-form-item :label="$t('table.outState')" prop="outState">
          <el-switch
            v-model="form.outState"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-text="$t('tagsView.close')"
            :inactive-text="$t('tagsView.open')"
            :active-value="2"
            :inactive-value="1"
          ></el-switch>
        </el-form-item>
         <!-- 版本 -->
       <!-- 版本 -->
       <el-form-item label="版本" prop="version">
            <el-radio v-model="form.version" :label='1'>V1</el-radio>
            <el-radio v-model="form.version" :label='2'>V2</el-radio>
        </el-form-item>
        <!-- ip白名单 -->
        <el-form-item :label="$t('table.whiteListState')" prop="whiteListState">
          <el-switch
            v-model="form.whiteListState"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-text="$t('tagsView.close')"
            :inactive-text="$t('tagsView.open')"
            :active-value="2"
            :inactive-value="1"
          ></el-switch>
        </el-form-item>

        <!-- 是否开启 -->
        <el-form-item :label="$t('table.whetherOpen')" prop="isStatus">
          <el-switch
            v-model="form.isStatus"
            active-color="#ff4949"
            inactive-color="#13ce66"
            :active-text="$t('tagsView.close')"
            :inactive-text="$t('tagsView.open')"
            :active-value="2"
            :inactive-value="1"
          ></el-switch>
        </el-form-item>

        <!-- 支付方式 -->
        <el-form-item :label="$t('route.payType')" prop="typeId">
          <el-transfer
            style="text-align: left; display: inline-block"
            v-model="form.typeId"
            :titles="[$t('table.source'), $t('table.aims')]"
            :button-texts="[$t('table.goLeft'), $t('table.goRight')]"
            :format="{
                  noChecked: '${total}',
                  hasChecked: '${checked}/${total}'
                }"
            :props="{
                    key:'typeId',
                    label:'typeName'
                }"
            :data="payTypeList"
          >
            <span slot-scope="{ option }">{{ option.typeName }}</span>
          </el-transfer>
        </el-form-item>

        <!-- 支付code码 -->
        <el-form-item :label="$t('table.payCode')" prop="payCode">
          <el-tooltip effect="light" placement="top-start">
            <div slot="content">
              <p class="code">{{$t('table.codeList')}}：</p>
              <p class="code codeList" v-for="key in Math.ceil(payTypeList.length/5)" :key="key">
                <span
                  class="codeSpan"
                  v-for="(item, index) in payTypeList"
                  :key="index"
                  v-if="index >= (key - 1) * 5 + 1 && index < key * 5 + 1"
                >{{item.typeId}}-{{item.englishName}}（{{item.typeName}}）,</span>
              </p>
            </div>
            <el-input
              :placeholder="$t('table.input')+$t('table.payCode')"
              v-model="form.payCode"
              :maxlength="200"
            >
              <template slot="prepend">
                <i class="el-icon-edit"></i>
              </template>
            </el-input>
          </el-tooltip>
        </el-form-item>

        <!-- 支付网关 -->
        <el-form-item
          :label="$t('payName.' + item.englishName)+$t('table.merURL')"
          v-for="(item, index) in payTypeList"
          :key="index"
          v-if="form.typeId.some(id => id === item.typeId)"
          :prop="item.englishName + 'Url'"
          :rules="[
            { required: true, message: $t('valiMsg.url'), trigger: 'blur' },
            { type: 'url', message: $t('valiMsg.url'), trigger: ['blur'] }
          ]"
        >
          <el-input
            :placeholder="$t('table.input')+$t('payName.' + item.englishName)+$t('table.merURL')"
            v-model="form[item.englishName + 'Url']"
          >
            <template slot="prepend">
              <i class="el-icon-edit-outline"></i>
            </template>
          </el-input>
        </el-form-item>

        <!-- 自动出款网关 -->
        <el-form-item :label="$t('payName.dispensing')+$t('table.merURL')" prop="dispensingUrl">
          <el-input
            :placeholder="$t('table.input')+$t('payName.dispensing')+$t('table.merURL')"
            v-model="form.dispensingUrl"
          >
            <template slot="prepend">
              <i class="el-icon-edit-outline"></i>
            </template>
          </el-input>
        </el-form-item>
        <!-- 扩展字段名称 -->
        <el-form-item :label="$t('payName.extendName')" prop="extendName">
          <el-input
            :placeholder="$t('table.input')+$t('payName.extendName')"
            v-model="form.extendName"
          >
            <template slot="prepend">
              <i class="el-icon-edit-outline"></i>
            </template>
          </el-input>
        </el-form-item>

        <el-form-item :label="$t('table.remarkName')" prop="remarkName">
          <el-input
            type="textarea"
            :autosize="{ minRows: 2, maxRows: 4}"
            :placeholder="$t('table.input')+$t('table.remarkName')"
            v-model="form.remarkName"
          >
            <template slot="prepend">
              <i class="el-icon-edit-outline"></i>
            </template>
          </el-input>
        </el-form-item>

        <el-form-item align="center">
          <el-button type="warning" @click="cleanForm">{{$t('table.reset')}}</el-button>
          <el-button
            type="primary"
            @click="onSubmit"
            :loading="Loading"
            :disabled="Disable"
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
import { Message } from "element-ui";
export default {
  components: {},

  data() {
    const isString = (rule, value, callback) => {
      const reg = /^[0-9A-Za-z\u4e00-\u9fa5\.]+$/;
      if (reg.test(value)) {
        callback();
      } else {
        if (value === "") {
          callback(new Error(this.$t(`valiMsg.${[rule.fullField]}`)));
        } else {
          callback(new Error(this.$t("valiMsg.specialSymbol")));
        }
      }
    };

    return {
      rules: {
        ...demoRules,
        confName: [
          {
            required: true,
            validator: isString,
            trigger: "blur"
          }
        ]
      },

      pageHeight: 50,
      form:{
        confName: "",
        confMod: "",
        inState: 1,
        outState: 2,
        version:1,
        whiteListState: 2,
        isStatus: 1,
        typeId: [],
        payCode: "",
        dispensingUrl: "",
        extendName: "",
        remarkName: ""
      },
      Disable: false,
      Loading: false,
      payTypeList: []
    };
  },
  created() {
    this.getPayTypeLis().then(res => {
      this.payTypeList = res.data;
    });

    if ("payId" in this.tripartItem) {
      this.form = Object.assign({}, this.tripartItem);
    } else {
      Message({
        showClose: true,
        message: this.$t("alertMsg.changeTripartMsg"),
        type: "error",
        duration: 2000
      });

      const self = this;
      setTimeout(() => {
        self.$router.push({ name: "TripartiteList" });
      }, 1000);
    }
  },

  computed: {
    ...mapGetters(["tripartItem", "tripartItemIndex"])
  },

  beforeDestroy() {
    this.setTripartItem({ item: {}, index: 0 });
  },

  methods: {
    ...mapActions(["editTripartiteList", "getPayTypeLis", "setTripartItem"]),

    confModChange(value) {
      this.form.confMod = value.replace(/[^a-zA-Z]/g, "");
    },

    cleanForm() {
      this.form = Object.assign({}, this.tripartItem);

      this.$refs["editForm"].clearValidate();
    },

    onSubmit() {
      this.$refs["editForm"].validate(valid => {
        if (valid) {
          this.Disable = true;
          this.Loading = true;
          var {
            version,
            payId,
            confName,
            confMod,
            inState,
            outState,
            whiteListState,
            isStatus,
            typeId,
            payCode,
            dispensingUrl,
            extendName,
            remarkName
          } = this.form;

          const payCodeArr = payCode === "" ? [] : payCode.split(",");

          const payCodeType = payCodeArr.map(item => {
            const a = item.split("-");
            const i = a[0];
            const code = a[1];

            const obj = {
              code,
              id: i
            };

            const o = this.payTypeList.find(v => v.typeId == i);

            if (o) {
              obj.typeName = o.typeName;
            }

            return obj;
          });

          var obj = {};
          const typeName = [];

          typeId.forEach(item => {
            const o = this.payTypeList.find(v => item === v.typeId);
            if (o) {
              typeName.push(o.typeName);
              obj[o.englishName + "Url"] = this.form[o.englishName + "Url"];
            }
          });
         
          const data = {
            payId,
            confName,
            confMod,
            inState,
            version,
            outState,
            whiteListState,
            isStatus,
            typeId,
            payCode,
            dispensingUrl,
            extendName,
            remarkName,
            payCodeType,
            typeName,
            ...obj
          };
          
          this.editTripartiteList({
            name: "tripart",
            type: "edit",
            current: this.tripartItemIndex,
            data
          })
            .then(data => {
              this.alertMessage(data.rps.msg);
              this.backBtn();
            })
            .catch(err => {
              this.Disable = false;
              this.Loading = false;
            })
            .finally(() => {});
        }
      });
    },

    backBtn() {
      this.$router.push({ name: "TripartiteList" });
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
