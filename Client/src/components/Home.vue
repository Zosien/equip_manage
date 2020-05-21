<template>
  <el-container class="home-container">
    <el-header>
      <div class="header-div">
        <img src="../assets/img/buct.jpg" alt="BUCT" class="head-img" />
        <span>实验室设备管理系统</span>
      </div>
      <el-button type="info" @click="logout">退出</el-button>
      {{current}}
    </el-header>
    <el-container>
      <el-aside width="200px">
        <el-menu
          width="200px"
          background-color="#333744"
          text-color="#fff"
          active-text-color="#ffd04b"
          router
          default-active="/user/list"
        >
          <el-menu-item-group>
            <template slot="title">用户管理</template>
            <el-menu-item index="/user/list">用户列表</el-menu-item>
          </el-menu-item-group>
        </el-menu>
      </el-aside>
      <el-main>
        <router-view></router-view>
      </el-main>
    </el-container>
  </el-container>
</template>
<script>
export default {
  data() {
    return {
      current: ''
    }
  },
  mounted() {
    console.log(this.$router.path)
  },
  methods: {
    logout() {
      this.$http
        .delete('/token')
        .then(res => {
          console.log(res)
          window.sessionStorage.clear()
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
