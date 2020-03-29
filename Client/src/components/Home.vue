<template>
  <el-container class="home-container">
    <el-header>
      <div class="header-div">
        <img src="../assets/img/buct.jpg" alt="BUCT" class="head-img" />
        <span>实验室设备管理系统</span>
      </div>
      <el-button type="info" @click="logout">退出</el-button>
    </el-header>
    <el-container>
      <el-aside width="200px">
        <el-menu
          width="200px"
          background-color="#333744"
          text-color="#fff"
          active-text-color="#ffd04b"
        >
          <el-submenu index="1">
            <template slot="title">
              <i class="el-icon-location"></i>
              <span>导航一</span>
            </template>
            <el-menu-item index="1-4-1">
              <template slot="title">
                <i class="el-icon-location"></i>
                <span>导航一</span>
              </template>
            </el-menu-item>
          </el-submenu>
        </el-menu>
      </el-aside>
      <el-main>Main</el-main>
    </el-container>
  </el-container>
</template>
<script>
export default {
  methods: {
    logout() {
      this.$http.delete('/key').then(res => {
        window.sessionStorage.clear()
        this.$message.success('退出成功')
        this.$router.push('/login')
      }).catch(err => {
        console.log(err.response.data)
        this.$message.error('请检查您的网络')
      })
    }
  }
}
</script>
<style>
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
}
.el-main {
  background-color: #eaedf1;
}
</style>
