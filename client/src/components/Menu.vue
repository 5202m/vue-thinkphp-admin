<template lang="pug">
  <el-scrollbar class="sbar">
    <el-menu :default-active="$route.path">
      <template v-for="item,key in getPrivateRouter">
        <el-submenu :index="combinePath(item.path)" v-if="item.children && !item.hidden">
          <template slot="title">
            <i :class="fl" v-if="item.meta.icon.indexOf('#') > -1"></i>
            <i v-else :class="item.meta.icon"></i>
              <!--svg class="icon fs-20" aria-hidden="true">
                <use :xlink:href="item.meta.icon" />
              </svg-->
            <span slot="title">{{item.meta.title}}</span>
          </template>
          <template v-for="children in item.children">
            <router-link :to="combinePath(item.path,children.path)" :key="children.name" >
              <el-menu-item v-if="!children.hidden" :index="combinePath(item.path,children.path)">{{children.meta.title}}</el-menu-item>
            </router-link>
          </template>
        </el-submenu>
      </template>
    </el-menu>
  </el-scrollbar>
</template>
<script>
import { mapGetters } from 'vuex'
import path from 'path'
export default {
  name: 'top',
  data () {
    return {
      self: this
    }
  },
  methods: {
    combinePath (a, b = '', c = '') {
      return path.resolve(a, b, c)
    }
  },
  computed: {
    ...mapGetters([
      'getPrivateRouter'
    ])
  }
}
</script>
<style lang="less">
.sbar{
  height: 100%;
  width:200px;
  border-right: 1px solid #e3e3e3;
  // overflow-y: hidden !important;
  // margin-right: -1px;
}
.sbar .el-scrollbar__wrap{
  height: 100%;
  overflow-x: hidden;
}
.sbar .el-scrollbar__view{
    position: relative;
    height: 100%;
}
.sbar .el-scrollbar__view > .el-menu{
  height: 100%;
  font-weight: 500;
  border-right: 0px;

}
</style>
