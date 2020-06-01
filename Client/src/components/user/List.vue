<template>
  <div>
    <el-breadcrumb separator-class="el-icon-arrow-right">
      <el-breadcrumb-item :to="{ path: '/home' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>用户管理</el-breadcrumb-item>
      <el-breadcrumb-item>用户列表</el-breadcrumb-item>
    </el-breadcrumb>
    <el-card>
      <el-row>
        <el-col :span="3">
          <router-link class="btn-link-large add-btn" to="add">
            <i class="el-icon-plus"></i>&nbsp;&nbsp;添加用户
          </router-link>
        </el-col>
        <el-col :span="4">
          <el-input placeholder="请输入用户名" v-model="keyword" @keyup.enter.native="search()">
            <el-button slot="append" icon="el-icon-search" @click="search()"></el-button>
          </el-input>
        </el-col>
      </el-row>
      <el-table
        :data="tableData"
        style="width: 100%"
        @selection-change="selectItem"
        stripe
        v-loading="loading"
      >
        <el-table-column type="selection" width="50"></el-table-column>
        <el-table-column label="id" prop="id" width="50"></el-table-column>
        <el-table-column label="用户名" prop="username"></el-table-column>
        <el-table-column
          label="身份"
          prop="rank"
          :filters="[{ text: 'student', value: 'student' }, { text: 'teacher', value: 'teacher' },{ text: 'engineer', value: 'engineer' }]"
          :filter-method="rankFilter"
        ></el-table-column>
        <el-table-column label="学号" prop="details.stu_num"></el-table-column>
        <el-table-column label="学院" prop="details.institute"></el-table-column>
        <el-table-column label="班级" prop="details.class"></el-table-column>
        <el-table-column label="姓名" prop="details.name"></el-table-column>
        <el-table-column label="性别" prop="details.gender"></el-table-column>
        <el-table-column label="年龄" prop="details.age"></el-table-column>
        <el-table-column
          label="状态"
          prop="status"
          width="100"
          :filters="[{ text: '-1', value: '-1' }, { text: '0', value: '0' },{ text: '1', value: '1' }]"
          :filter-method="statusFilter"
        ></el-table-column>
        <el-table-column label="操作" width="150">
          <template scope="scope">
            <div>
              <el-button size="mini" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
              <el-button
                size="mini"
                type="danger"
                @click="handleDisable(scope.row)"
                v-if="scope.row.status === 1"
              >禁用</el-button>
              <el-button size="mini" type="danger" @click="handleEnable(scope.row)" v-else>启用</el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <!-- <div class="pos-rel p-t-20">
      <btnGroup :selectedData="multipleSelection" :type="'users'"></btnGroup>
      <div class="block pages">
        <el-pagination
          @current-change="handleCurrentChange"
          layout="prev, pager, next"
          :page-size="limit"
          :current-page="currentPage"
          :total="dataCount"
        ></el-pagination>
      </div>
      </div>-->
    </el-card>
  </div>
</template>
<style lang="less" scoped>
.el-breadcrumb {
  margin-bottom: 15px;
}
.add-btn {
  display: inline-block;
  padding: 10px 15px;
  background-color: #4caf50;
  color: #fff;
  border-radius: 5px;
}
.edit-btn {
  box-sizing: border-box !important;
  background: #5bb1fa;
  color: #fff;
  padding: 7px 9px;
  font-size: 12px;
  text-align: center;
  border-radius: 4px;
  margin-right: 10px;
  line-height: 1;
  cursor: pointer;
}
</style>

<script>
export default {
  data() {
    return {
      keyword: '',
      originalData: [],
      tableData: [],
      loading: true
    }
  },
  created() {
    this.getUserList()
  },
  methods: {
    async getUserList() {
      this.loading = true
      await this.$http
        .get('/user')
        .then(res => {
          this.originalData = res.data
          this.tableData = res.data
        })
        .catch(err => {
          this.$message.error(err.data.msg)
        })
      this.loading = false
    },
    search() {
      var _this = this
      this.tableData = this.originalData.filter(function(data) {
        return (
          data.username.toLowerCase().indexOf(_this.keyword.toLowerCase()) > -1
        )
      })
    },
    rankFilter(value, row) {
      return value === row.rank
    },
    statusFilter(value, row) {
      return value === row.status + ''
    },
    handleDisable(row) {
      this.$http
        .patch('/user', {
          id: row.id,
          status: 0
        })
        .then(res => {
          row.status = 0
          this.$message.success('success')
        })
        .catch(err => {
          this.$message.error(err.data.msg)
        })
    },
    handleEnable(row) {
      this.$http
        .patch('/user', {
          id: row.id,
          status: 1
        })
        .then(res => {
          row.status = 1
          this.$message.success('success')
        })
        .catch(err => {
          this.$message.error(err.data.msg)
        })
    }
  }
}
</script>
