<template>
  <div
    class="dashboard-container"
    v-loading="screenLoading"
    :element-loading-text="$t('table.searchMsg')"
  >
    <div class="info clear">
      <div class="item fl">
        <div class="item-key">{{$t('index.lastLoginTime')}}:</div>
        <div class="item-value">{{lastLoginTime}}</div>
      </div>
      <div class="item fl">
        <div class="item-key">{{$t('index.lastLoginIp')}}:</div>
        <div class="item-value">{{lastLoginIP}}</div>
      </div>
    </div>
    <div class="clear">
      <div class="chartDiv">
        <div class="chart" id="Chart" :style="{height:'560px',width:'100%'}"></div>
      </div>
      <transition-group name="list-complete" tag="div" class="listDiv">
        <div v-if="top20Show" :key="1" class="list-complete-item">
          <el-card :body-style="{ padding: '0px' }">
            <div class="cardHeader">
              <span>
                <svg-icon class-name="svgIcon" icon-class="medal"/>
              </span>
              <span class="title">热门三方&nbsp;TOP20</span>
              <span class="operating">
                <i class="icon iconfont icon-jianhao" @click="foldClick"></i>&nbsp;
                <i class="icon iconfont icon-chenghao" @click="offClick()"></i>
              </span>
            </div>
            <transition-group
              name="card-complete"
              tag="ul"
              class="cardUl"
              :class="{'cardUlH':!cardliShow}"
            >
              <li v-for="(item, index) in top20" :key="index" class="card-complete-item">
                <p class="tripart clear">
                  <span class="icon">
                    <i class="icon iconfont icon-huo"></i>
                  </span>
                  <span class="name">{{item.confName}}</span>
                  <span class="countUse">用户数:&nbsp;{{item.countUse}}</span>
                </p>
              </li>
            </transition-group>
            <p class="footer"></p>
          </el-card>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import echarts from "echarts";
import resize from "./resize";

export default {
  name: "Index",
  components: {},
  computed: {
    ...mapGetters([
      "tripartList",
      "echartsData",
      "lastLoginIP",
      "lastLoginTime"
    ])
  },
  mixins: [resize],

  data() {
    return {
      chart: null,
      top20: [],
      top20Show: true,
      cardliShow: false,
      links: [
        {
          name: "alipay",
          url: "https://www.alipay.com/"
        },
        {
          name: "WeChat",
          url: "https://pay.weixin.qq.com/"
        },
        {
          name: "JD",
          url: "https://jr.jd.com/"
        },
        {
          name: "qq",
          url: "https://qpay.qq.com/"
        },
        {
          name: "PayPal",
          url: "https://www.paypal.com/"
        },
        {
          name: "baidu",
          url: "https://www.baifubao.com/"
        },
        {
          name: "unionpay",
          url: "https://cn.unionpay.com/"
        }
      ],
      screenLoading: false
    };
  },
  created() {
    this.drawing();
    this.searchTopTwenty().then(rps => {
      this.top20 = rps.data;
      this.$nextTick(() => {
        this.cardliShow = true;
      });
    });
  },
  methods: {
    ...mapActions(["searchTopTwenty", "getEcharts"]),

    foldClick() {
      this.cardliShow = !this.cardliShow;
    },

    offClick() {
      this.top20Show = false;
    },
    drawing() {
      this.screenLoading = true;
      this.getEcharts()
        .then(res => {
          this.initChart();
        })
        .catch(err => {})
        .finally(() => {
          this.screenLoading = false;
        });
    },
    initChart() {
      this.destroyDrawing();
      this.chart = echarts.init(document.getElementById("Chart"));

      var obj = {};
      this.echartsData.forEach((ele, index) => {
        for (const k in ele) !index ? (obj[k] = [ele[k]]) : obj[k].push(ele[k]);
      });

      const keyArr = [];
      const chartArr = [];

      const barColorArr = [
        "rgba(0,191,183,1)",
        "rgba(255,144,128,1)",
        "#5AB1EF",
        "#B6A2DE",
        "rgb(137,189,27)",
        "rgb(0,136,212)",
        "rgb(219,50,51)"
      ];

      const lineColorArr = [
        {
          color: "rgb(137,189,27)",
          borderColor: "rgba(137,189,2,0.27)",
          areaStyleColor: "rgba(137, 189, 27, 0.3)",
          areaStyleColor1: "rgba(137, 189, 27, 0)"
        },
        {
          color: "rgb(0,136,212)",
          borderColor: "rgba(0,136,212,0.2)",
          areaStyleColor: "rgba(0, 136, 212, 0.3)",
          areaStyleColor1: "rgba(0, 136, 212, 0)"
        },
        {
          color: "rgb(219,50,51)",
          borderColor: "rgba(219,50,51,0.2)",
          areaStyleColor: "rgba(219, 50, 51, 0.3)",
          areaStyleColor1: "rgba(219, 50, 51, 0)"
        }
      ];

      for (const key in obj) {
        if (key !== "date") {
          const k = this.$t(`table.${key}`);

          keyArr.push(k);

          // const r = parseInt(255 - Math.random() * 255 + 50);
          // const b = parseInt(255 - Math.random() * 255);
          // const g = parseInt(255 - Math.random() * 255);
          // console.log(r);
          // console.log(b);
          // console.log(g);
          // console.log("------------");

          if (key.indexOf("Fee") > -1) {
            const colorObj = lineColorArr[0];
            lineColorArr.splice(0, 1);

            const o = {
              name: k,
              type: "line",
              smooth: true,
              symbol: "circle",
              symbolSize: 5,
              showSymbol: false,
              lineStyle: {
                normal: {
                  width: 1
                }
              },
              areaStyle: {
                normal: {
                  color: new echarts.graphic.LinearGradient(
                    0,
                    0,
                    0,
                    1,
                    [
                      {
                        offset: 0,
                        color: colorObj.areaStyleColor
                      },
                      {
                        offset: 0.8,
                        color: colorObj.areaStyleColor1
                      }
                    ],
                    false
                  ),
                  shadowColor: "rgba(0, 0, 0, 0.1)",
                  shadowBlur: 10
                }
              },
              itemStyle: {
                normal: {
                  color: colorObj.color,
                  borderColor: colorObj.borderColor,
                  borderWidth: 12
                }
              },
              data: obj[key]
            };
            chartArr.push(o);
          } else if (key.indexOf("Num") > -1) {
            const color = barColorArr[0];
            barColorArr.splice(0, 1);

            const o = {
              name: k,
              type: "bar",
              // stack: "total",
              itemStyle: {
                normal: {
                  color: color,
                  barBorderRadius: 0,
                  label: {
                    show: true,
                    position: "top",
                    formatter(p) {
                      return p.value > 0 ? p.value : "";
                    }
                  }
                }
              },
              data: obj[key]
            };
            chartArr.push(o);
          }
        }
      }

      this.chart.setOption({
        backgroundColor: "#344b58",
        title: {
          top: 20,
          text: this.$t("table.orderStatistics"),
          textStyle: {
            fontWeight: "normal",
            fontSize: 24,
            color: "#F1F1F3"
          },
          left: "5%"
        },
        tooltip: {
          trigger: "axis",
          axisPointer: {
            lineStyle: {
              color: "#57617B"
            }
          }
        },
        grid: {
          top: 100,
          left: "3%",
          right: "4%",
          bottom: "2%",
          containLabel: true
        },
        legend: {
          data: keyArr,

          top: 20,
          right: "4%",
          icon: "rect",
          itemWidth: 14,
          itemHeight: 5,
          itemGap: 13,
          textStyle: {
            fontSize: 12,
            color: "#F1F1F3"
          }
        },
        calculable: true,
        xAxis: [
          {
            type: "category",
            axisLine: {
              lineStyle: {
                color: "#90979c"
              }
            },
            splitLine: {
              show: false
            },
            axisTick: {
              show: false
            },
            splitArea: {
              show: false
            },
            axisLabel: {
              interval: 0
            },
            data: obj.date
          }
        ],
        yAxis: [
          {
            type: "value",
            splitLine: {
              show: false
            },
            axisLine: {
              lineStyle: {
                color: "#90979c"
              }
            },
            axisTick: {
              show: false
            },
            axisLabel: {
              interval: 0
            },
            splitArea: {
              show: false
            }
          }
        ],
        series: chartArr
      });
    },

    destroyDrawing() {
      this.chart && this.chart.dispose();
    }
  },
  beforeDestroy() {
    this.destroyDrawing();
  }
};
</script>
<style lang="scss" scoped>
@import url("../../styles/global.scss");

.dashboard-container {
  padding: 20px;
  .h3 {
    margin: 0 0 20px;
  }

  .chartDiv {
    float: left;
    width: 75%;
    height: 500px;
  }

  .listDiv {
    width: 24%;
    height: 500px;
    float: right;
    .list-complete-item {
      width: 100%;
      box-sizing: content-box;
    }
    .cardHeader {
      font-size: 20px;
      border-bottom: 1px solid #ebeef5;
      padding: 10px 20px 10px 5px;
      height: 28px;
      box-sizing: content-box;
      .svgIcon {
        font-size: 30px;
        position: absolute;
      }
      .title {
        margin-left: 40px;
        line-height: 30px;
      }
      i {
        font-size: 24px;
      }
      .operating {
        float: right;
        list-style: 28px;
        i {
          font-size: 16px;
          margin-left: 10px;
        }
      }
    }
    .cardUl {
      list-style: none;
      padding: 0 10px;
      margin: 0;
      height: 500px;
      overflow-y: auto;
      transition: height 0.5s;
      li {
        margin: 5px 0;
        width: 100%;
        .tripart {
          height: 26px;
          margin: 0;
          line-height: 26px;
          .name {
            color: #555;
            width: 50%;
            float: left;
          }
          .countUse {
            float: right;
            text-align: left;
          }
          .icon {
            width: 40px;
            margin-right: 5px;
            float: left;
            i {
              font-size: 20px;
            }
          }
        }
      }
    }
    .cardUlH {
      height: 0;
    }
    .cardUl::-webkit-scrollbar {
      display: none;
    }
    .footer {
      height: 10px;
      margin: 0;
      padding: 0;
    }
  }
}

.card-complete-item {
  transition: all 1s;
  display: inline-block;
}
.card-complete-enter,
.card-complete-leave-to {
  opacity: 0;
  transform: translateY(30px);
}
.card-complete-leave-active {
  position: absolute;
}

.list-complete-item {
  transition: all 1s;
  display: inline-block;
}
.list-complete-enter,
.list-complete-leave-to {
  opacity: 0;
  transform: translateY(300px);
}
.list-complete-leave-active {
  position: absolute;
}

.info {
  min-width: 500px;
  height: auto;
  border-radius: 5px;
  box-shadow: #ccc 0px 0px 1px;
  margin: 0px auto 10px;

  .item {
    display: flex;
    flex-direction: row;
    height: 50px;
    line-height: 50px;
    border-bottom: 1px solid #eee;
  }

  .item-key {
    width: 150px;
    text-align: right;
  }

  .item-value {
    padding-left: 10px;
  }
}
</style>
