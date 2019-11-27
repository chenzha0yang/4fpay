<template>
  <div class="listDiv">
    <ul class="kyeUl">
      <li v-for="(item, index) in arr" :key="index">
        <p>{{index < 9 ? '0' + (index+1) : (index+1)}}.</p>
        <ul>
          <li v-for="(key, k) in item" :key="k">{{key}}</li>
        </ul>
      </li>
    </ul>
    <p class="footer"></p>
  </div>
</template>

<script>
export default {
  props: ["arr"]
};
</script>

<style lang="scss" scoped>
.listDiv {
  background-color: #e2e2e2;
  padding-left: 10px;
  p {
    margin: 0;
    padding: 0;
  }
  .footer {
    height: 10px;
    background-color: #f2f2f2;
  }

  .kyeUl {
    margin: 0;
    padding: 0 20px 0 0;
    background-color: #fff;
    font-size: 14px;
    max-height: 200px;
    overflow-y: scroll;
    overflow-x: auto;
    list-style-type: decimal-leading-zero;
    > li {
      padding: 0;
      background-color: #fff;
      display: flex;
      p {
        width: 60px;
        text-align: center;
        background-color: #f2f2f2;
      }
      ul {
        flex: 1;
        list-style: none;
        margin: 0;
        padding: 0 5px;
        border-left: 1px solid #e2e2e2;
        li {
          word-break: break-all;
          line-height: 25px;
        }
      }
    }
  }
}
</style>
