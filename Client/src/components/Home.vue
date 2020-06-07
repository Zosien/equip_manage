<template>
  <el-container class="home-container">
    <el-header>
      <div class="header-div">
        <img src="../assets/img/buct.jpg" alt="BUCT" class="head-img" />
        <span>实验室设备管理系统</span>
      </div>
      <el-dropdown @command="handleMenu" class="user-menu">
        <span class="el-dropdown-link c-gray" style="cursor: default">
          {{userinfo.username}}&nbsp;&nbsp;
          <i class="fa fa-user" aria-hidden="true"></i>
        </span>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item command="changePsw">修改密码</el-dropdown-item>
          <el-dropdown-item command="logout">退出</el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </el-header>
    <el-container>
      <el-aside width="180px">
        <el-menu
          width="180px"
          background-color="#333744"
          text-color="#fff"
          active-text-color="#ffd04b"
          router
          :default-active="currentUrl"
        >
          <el-menu-item-group v-for="(items, index) in menu" :key="index">
            <template slot="title">{{items.name}}</template>
            <el-menu-item
              :index="item.url"
              v-for="(item, index) in items.child"
              :key="index"
            >{{item.name}}</el-menu-item>
          </el-menu-item-group>
        </el-menu>
      </el-aside>
      <el-main>
        <router-view></router-view>
      </el-main>
      <changePsw :isShow="show" @close="change"></changePsw>
    </el-container>
  </el-container>
</template>
<script>
import changePsw from './common/changePsw.vue'
export default {
  data() {
    return {
      current: '',
      currentUrl: '',
      userinfo: {},
      show: false,
      menu: {}
    }
  },
  components: {
    changePsw: changePsw
  },
  mounted() {
    console.log(this.$route.path)
  },
  created() {
    this.getMyInfo()
    this.getMenu()
    this.getCurrentUrl()
  },
  watch: {
    $router() {
      this.currentUrl = this.$route.path
    }
  },
  methods: {
    getCurrentUrl() {
      this.currentUrl = this.$route.path
    },
    getMenu() {
      this.$http
        .get('/menu')
        .then(res => {
          this.menu = res.data
        })
        .catch(err => {
          console.log(err)
        })
    },
    change(val) {
      this.show = val
    },
    getMyInfo() {
      this.$http
        .get('/my')
        .then(res => {
          this.userinfo = res.data
        })
        .catch(err => {
          console.log(err)
        })
    },
    handleMenu(command) {
      switch (command) {
        case 'changePsw':
          this.change(true)
          break
        case 'logout':
          this.logout()
          break
        default:
          break
      }
    },
    logout() {
      this.$http
        .delete('/token')
        .then(res => {
          Cookie.remove('token')
          this.$message.success('退出成功')
          this.$router.push('/login')
        })
        .catch(err => {
          console.log(err)
          this.$message.error('请检查您的网络')
        })
    }
  }
}
</script>
<style lang="less">
.header-div {
  height: 100%;
  display: flex;
  align-items: center;
}
.head-img {
  height: 100%;
  margin-right: 15px;
}
.home-container {
  height: 100%;
}
.el-header {
  background-color: #373d41;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #fff;
  font-size: 20px;
}
.el-aside {
  background-color: #333744;
  .el-menu {
    border-right: none;
  }
  .el-menu > .el-menu-item-group {
    padding: 15px 0 0 5px;
  }
}
.el-menu-item-group > .el-menu-item-group__title {
  padding-left: 5px !important;
  font-size: 14px;
}
.el-main {
  background-color: #eaedf1;
}
</style>
