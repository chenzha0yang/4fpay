<template>
  <div id="cache" class="app-container calendar-list-container">
    <el-row :gutter="20">
      <el-col :span="3">
        <div class="colDiv lineH36 myLabel">选择服务</div>
      </el-col>
      <el-col :span="3">
        <div class="colDiv lineH36">服务类型:</div>
      </el-col>
      <el-col :span="4">
        <el-select v-model="form.server" placeholder="请选择">
          <el-option
            v-for="item in serviceType"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-col>
      <el-col :span="3">
        <div class="colDiv lineH36">服务实例:</div>
      </el-col>
      <el-col :span="4">
        <el-select v-model="form.selectDB" placeholder="请选择">
          <el-option
            v-for="item in instanceType"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-col>
    </el-row>

    <el-row :gutter="20">
      <el-col :span="3">
        <div class="colDiv lineH36 myLabel">获取键名</div>
      </el-col>
      <el-col :span="3">
        <div class="colDiv lineH36">获取方式:</div>
      </el-col>
      <el-col :span="4">
        <el-select v-model="form.getKeyType" placeholder="请选择">
          <el-option
            v-for="item in keyTypeList"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-col>
      <el-col :span="4">
        <div class="lineH36">
          <el-input v-model="form.getKeyName" placeholder="请输入内容" v-if="form.getKeyType!==1"></el-input>
        </div>
      </el-col>
      <el-col :span="8">
        <div class="lineH36 buttonDiv">
          <el-button
            style="width:100px"
            type="primary"
            size="medium"
            :disabled="Disabled"
            :loading="keyLoading"
            @click="mySearchCacheKey"
          >{{$t('table.search')}}</el-button>
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="20">
      <el-col :span="3">
        <div class="colDiv lineH36 myLabel">清除键名</div>
      </el-col>
      <el-col :span="3">
        <div class="colDiv lineH36">选择类型:</div>
      </el-col>
      <el-col :span="4">
        <el-select v-model="form.delKeyType" placeholder="请选择">
          <el-option
            v-for="item in keyTypeList"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-col>
      <el-col :span="4">
        <el-input v-model="form.delKeyName" placeholder="请输入内容"></el-input>
      </el-col>
      <el-col :span="8">
        <div class="lineH36 buttonDiv">
          <el-button
            style="width:100px"
            type="warning"
            size="medium"
            :disabled="Disabled"
            :loading="delLoading"
            @click="myDelCachey"
          >{{$t('table.delete')}}</el-button>
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="20">
      <el-col :span="3">
        <div class="colDiv lineH36 myLabel">获取键值</div>
      </el-col>
      <el-col :span="3">
        <div class="colDiv lineH36">选择类型:</div>
      </el-col>
      <el-col :span="4">
        <el-select v-model="form.getValType" placeholder="请选择">
          <el-option
            v-for="item in keyList"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-col>
      <el-col :span="4">
        <el-input v-model="form.getValName" placeholder="请输入内容"></el-input>
      </el-col>
      <el-col :span="2">
        <div class="colDiv lineH36">反Json:</div>
      </el-col>
      <el-col :span="2">
        <el-select v-model="form.jsonType" placeholder="请选择">
          <el-option
            v-for="item in isJson"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-col>
      <el-col :span="4">
        <div class="lineH36 buttonDiv">
          <el-button
            style="width:100px"
            type="primary"
            size="medium"
            :disabled="Disabled"
            :loading="valLoading"
            @click="mySearchCacheVal"
          >{{$t('table.search')}}</el-button>
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="20">
      <el-col :span="3">
        <div class="colDiv lineH36 myLabel">获取长度</div>
      </el-col>
      <el-col :span="3">
        <div class="colDiv lineH36">选择类型:</div>
      </el-col>
      <el-col :span="4">
        <el-select v-model="form.lenType" placeholder="请选择">
          <el-option
            v-for="item in keyList"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-col>
      <el-col :span="4">
        <el-input v-model="form.lenName" placeholder="请输入内容"></el-input>
      </el-col>
      <el-col :span="8">
        <div class="lineH36 buttonDiv">
          <el-button
            style="width:100px"
            type="primary"
            size="medium"
            :disabled="Disabled"
            :loading="lenLoading"
            @click="mySearchCacheLen"
          >{{$t('table.search')}}</el-button>
        </div>
      </el-col>
    </el-row>

    <div class="myCollapse">
      <el-collapse v-model="activeNames">
        <el-collapse-item name="1" class="collapseItem">
          <template slot="title">
            <div class="myTitle">
              <div>键名列表</div>
            </div>
          </template>
          <!-- <div class="listDiv"> -->
          <!-- <ul class="kyeUl" :class="{'paddBot10':keyArr.length}">
              <li v-for="(item, index) in keyArr" :key="index">
                <span class="liIndex">{{index < 10 ? '0' + (index+1) : (index+1)}}.</span>
                <span>{{item}}</span>
              </li>
          </ul>-->
          <arrTemplate :arr="keyArr"/>
          <!-- </div> -->
        </el-collapse-item>
        <el-collapse-item name="2">
          <template slot="title">
            <div class="myTitle">
              <div>键值列表</div>
            </div>
          </template>
          <!-- <div class="listDiv"> -->
          <!-- <ul class="kyeUl" :class="{'paddBot10':valArr.length}">
              <li v-for="(item, index) in valArr" :key="index">
                <span class="liIndex">{{index < 10 ? '0' + (index+1) : (index+1)}}.</span>
                <span>{{item}}</span>
              </li>
          </ul>-->
          <component :is="isKey" :arr="valArr"></component>
          <!-- </div> -->
        </el-collapse-item>
      </el-collapse>
    </div>

    <el-dialog title="查看长度" :visible.sync="dialogVisible" width="700px">
      <div v-if="dialogVisible" id="g-dialog">
        <p>
          <span class="name">键名:</span>
          <span clasee="content">{{currentRow.name}}</span>
        </p>
        <p>
          <span class="name">长度:</span>
          <span clasee="content">{{currentRow.len}}</span>
        </p>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { mapActions } from "vuex";
import { Message } from "element-ui";
import arrTemplate from "./arrTemplate";
import strTemplate from "./strTemplate";
import codeTemplate from "./codeTemplate";
export default {
  components: { arrTemplate, strTemplate, codeTemplate },
  data() {
    return {
      dialogVisible: false,
      currentRow: {
        name: "",
        len: ""
      },
      activeNames: [],

      serviceType: [
        {
          value: 1,
          label: "redis"
        }
      ],

      instanceType: [
        {
          value: "default",
          label: "默认"
        }
        // {
        //   value: 2,
        //   label: "滚球"
        // },
        // {
        //   value: 3,
        //   label: "会员信息"
        // },
        // {
        //   value: 4,
        //   label: "代理信息"
        // },
        // {
        //   value: 5,
        //   label: "采集队列"
        // },
        // {
        //   value: 6,
        //   label: "SESSION"
        // },
        // {
        //   value: 7,
        //   label: "钱包"
        // }
      ],

      keyTypeList: [
        {
          value: 1,
          label: "全部"
        },
        {
          value: 2,
          label: "前缀"
        },
        {
          value: 3,
          label: "后缀"
        }
      ],

      keyList: [
        {
          value: 0,
          label: "全部(自动选择类型)"
        },
        {
          value: 1,
          label: "字符串(String-get)"
        },
        {
          value: 2,
          label: "集合(Set-sismember)"
        },
        {
          value: 3,
          label: "列表(List-lrange)"
        },
        {
          value: 4,
          label: "哈希表(Hash-hget)"
        }
      ],

      isJson: [
        {
          value: 1,
          label: "是"
        },
        {
          value: 2,
          label: "否"
        }
      ],

      form: {
        server: 1,
        selectDB: "default",

        getKeyType: 1,
        getKeyName: "",

        delKeyType: 1,
        delKeyName: "",

        getValType: 0,
        getValName: "",
        jsonType: 1,

        lenType: 0,
        lenName: ""
      },

      keyLoading: false,
      valLoading: false,
      lenLoading: false,
      delLoading: false,

      Disabled: false,
      isKey: "codeTemplate",
      keyArr: [],
      valArr: []
    };
  },

  // computed: {
  //   ...mapGetters({})
  // },
  methods: {
    ...mapActions([
      "searchCacheKey",
      "searchCacheLen",
      "searchCacheVal",
      "delCache"
    ]),
    // 键名
    mySearchCacheKey() {
      const { server, selectDB, getKeyType, getKeyName } = this.form;

      const data = { server, selectDB, getKeyType, getKeyName };

      if (getKeyType !== 1 && getKeyName === "") {
        Message({
          showClose: true,
          message: "请输入键名",
          type: "error",
          duration: 3 * 1000
        });
        return;
      }

      this.Loading = true;
      this.keyLoading = true;

      this.searchCacheKey(data)
        .then(res => {
          this.keyArr = res.data;
          if (this.activeNames.indexOf("1") === -1) {
            this.activeNames.push("1");
          }
        })
        .catch(err => {})
        .finally(() => {
          this.Loading = false;
          this.keyLoading = false;
        });
    },
    // 长度
    mySearchCacheLen() {
      const { server, selectDB, lenType, lenName } = this.form;

      const data = { server, selectDB, lenType, lenName };

      if (lenName === "") {
        Message({
          showClose: true,
          message: "请输入键名",
          type: "error",
          duration: 3 * 1000
        });
        return;
      }

      this.Loading = true;
      this.lenLoading = true;

      this.searchCacheLen(data)
        .then(res => {
          if (Array.isArray(res.data) && res.data.length) {
            this.currentRow.name = res.data[0];
            this.currentRow.len = res.data[1];
            this.dialogVisible = true;
          } else {
            Message({
              showClose: true,
              message: "获取失败",
              type: "error",
              duration: 3 * 1000
            });
          }
        })
        .catch(err => {})
        .finally(() => {
          this.Loading = false;
          this.lenLoading = false;
        });
    },
    // 键值
    mySearchCacheVal() {
      const { server, selectDB, getValType, getValName, jsonType } = this.form;

      const data = { server, selectDB, getValType, getValName, jsonType };

      if (getValName === "") {
        Message({
          showClose: true,
          message: "请输入键名",
          type: "error",
          duration: 3 * 1000
        });
        return;
      }

      this.Loading = true;
      this.valLoading = true;

      this.searchCacheVal(data)
        .then(res => {
          if (res.data === null) {
            return;
          } else {
            if (this.form.jsonType === 2) {
              const arr = res.data.map(item => {
                return item.split("},").map((item, index, arr) => {
                  var str = item;
                  if (index !== arr.length - 1) {
                    str += "},";
                  }
                  return str;
                });
              });

              this.valArr = arr;
              this.isKey = "strTemplate";
            } else {
              const str = `Array(${res.data.length}) {`;

              const arr = [];
              arr.push(str);

              res.data.forEach((item, index) => {
                const a = Object.keys(item);
                const str = `${index + 1} => Array(${a.length}) {`;
                arr.push(str);
                for (const key in item) {
                  const str = `${key} => ${item[key] === "" ? "''" : item[key]}`;
                  arr.push(str);
                }
                arr.push("}");
              });
              arr.push("}");

              this.valArr = arr;

              this.isKey = "codeTemplate";
            }
            if (this.activeNames.indexOf("2") === -1) {
              this.activeNames.push("2");
            }
          }
        })
        .catch(err => {})
        .finally(() => {
          this.Loading = false;
          this.valLoading = false;
        });
    },
    // 删除
    myDelCachey() {
      const { server, selectDB, delKeyType, delKeyName } = this.form;

      const data = { server, selectDB, delKeyType, delKeyName };

      if (delKeyName === "") {
        Message({
          showClose: true,
          message: "请输入键名",
          type: "error",
          duration: 3 * 1000
        });
        return;
      }

      this.Loading = true;
      this.delLoading = true;

      this.delCache(data)
        .then(res => {
          if (res.res.code === 2001) {
            this.alertMessage(res.res.msg);
            // this.keyArr = res.res.data;
            // if (this.activeNames.indexOf("2") === -1) {
            //   this.activeNames.push("2");
            // }
          } else if (res.res.code === 2010) {
            this.alertMessage(res.res.msg, "error");
          }
        })
        .catch(err => {})
        .finally(() => {
          this.Loading = false;
          this.delLoading = false;
        });
    }
  }
};
</script>
<style scoped  lang="scss">
.el-row {
  margin-bottom: 20px;
}
.el-row:last-child {
  margin-bottom: 0;
}
.colDiv {
  text-align: center;
}

.myLabel {
  font-weight: 700;
}
.lineH36 {
  height: 36px;
  line-height: 36px;
}
.buttonDiv {
  text-align: right;
  margin-right: 40px;
}

.collapseItem {
  margin-bottom: 5px;
}

.myTitle {
  background-color: #e2e2e2;
  padding-left: 10px;
  > div {
    background-color: #f2f2f2;
    padding-left: 20px;
    color: #333;
  }
}
.listDiv {
  background-color: #e2e2e2;
  padding-left: 10px;
}

#g-dialog {
  border: 1px solid #ccc;
  border-radius: 5px;
  p {
    margin: 0;
    padding: 0;
    height: 45px;
    line-height: 45px;
    span {
      display: inline-block;
      width: 150px;
    }
    .name {
      color: #606266;
      text-align: right;
      border-right: 1px solid #ccc;
      font-weight: 700;
      padding-right: 5px;
    }
    .content {
      color: #000;
      padding-left: 5px;
    }
  }
  p:first-child {
    border-bottom: 1px solid #ccc;
  }
}

.currentRow {
  .el-form-item {
    height: 45px;
    line-height: 45px;
    margin-bottom: 0;
    border-bottom: 1px solid #eee;
  }
  .el-form-item:last-child {
    border: none;
  }
}
</style>
