<template>
  <div id="memberlist" class="app-container calendar-list-container">
    <!--   search  start  -->
    <div id="form-search-data" class="filter-container">
      <el-form ref="searchForm" class="clear" :model="form" :rules="rules" :show-message="false">
        <el-form-item class="filter-item" style="width:300px">
          <!-- 订单号 -->
          <md-input
            v-model="form.orderNumber"
            icon="search"
            name="orderNumber"
            :placeholder="$t('table.orderNumber')"
          >{{ $t('table.orderNumber') }}</md-input>
        </el-form-item>

        <el-form-item class="filter-item">
          <!-- 开始时间-结束时间 -->
          <el-tooltip :content="$t('table.date')" placement="top">
            <el-date-picker
              v-model="form.date"
              align="right"
              :picker-options="pickerOptions"
              value-format="yyyy-MM-dd"
            />
          </el-tooltip>
        </el-form-item>
        <el-form-item class="filter-item">
          <el-button
            class="btn"
            type="primary"
            icon="el-icon-search"
            :loading="searchAble"
            size="medium"
            @click="handleSearch"
          >{{ $t('table.search') }}</el-button>

          <el-button
            class="btn"
            icon="el-icon-delete"
            size="medium"
            @click="handleClean"
          >{{ $t('table.reset') }}</el-button>
        </el-form-item>
      </el-form>
    </div>
    <div class="content">{{orderList}}</div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import MdInput from "@/components/MDinput";
import { resetPage } from "@/utils/sendDataProcess";
import { Message } from "element-ui";

export default {
  components: {
    MdInput
  },

  data() {
    return {
      rules: {},
      current: 0,
      pageHeight: 50,
      pickerOptions: {
        disabledDate(time) {
          return time.getTime() > Date.now();
        }
      },
      form: {
        orderNumber: "",
        date: ""
      },

      screenLoading: false,
      searchAble: false,
      oldObj: {},
      paginationShow: true
    };
  },

  computed: {
    ...mapGetters(["orderList", "orderCount"])
  },
  methods: {
    ...mapActions(["searchOrderLog"]),

    // 查询
    handleSearch() {
      this.$refs["searchForm"].validate(valid => {
        var { orderNumber, date } = this.form;

        if (orderNumber === "" || date === "") {
          Message({
            showClose: true,
            message: this.$t("alertMsg.orderLogMsg"),
            type: "error",
            duration: 3 * 1000
          });
        } else {
          this.screenLoading = true;
          this.searchAble = true;

          var data = {
            orderNumber,
            date
          };

          this.searchOrderLog(data)
            .then(rps => {})
            .catch(err => {})
            .finally(() => {
              this.screenLoading = false;
              this.searchAble = false;
            });
        }
      });
    },

    handleClean() {
      this.form = {
        orderNumber: "",
        dateValue: ""
      };
    },

    heightFn() {
      const h = document.documentElement.clientHeight - 260;
      return h || 400;
    }
  }
};
</script>

<style scoped>
.content {
  border: 1px solid #ccc;
  border-radius: 5px;
  background-color: #fff;
  padding: 30px;
  min-height: calc(100vh - 200px);
  max-height: calc(100vh - 200px);
  overflow-wrap: break-word;
  overflow: auto;
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

/* #g-dialog {
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
} */
</style>
