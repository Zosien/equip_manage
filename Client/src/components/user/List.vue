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
          <el-input
            placeholder="请输入用户名"
            v-model="keyword"
            @keyup.enter.native="search()"
            clearable
            @clear="getUserList"
          >
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
        <el-table-column label="学号" prop="stu_num"></el-table-column>
        <el-table-column label="学院" prop="institute"></el-table-column>
        <el-table-column label="班级" prop="class"></el-table-column>
        <el-table-column label="姓名" prop="name"></el-table-column>
        <el-table-column label="性别" prop="gender"></el-table-column>
        <el-table-column label="年龄" prop="age"></el-table-column>
        <el-table-column
          label="状态"
          prop="status"
          width="100"
          :filters="[{ text: '-1', value: '-1' }, { text: '0', value: '0' },{ text: '1', value: '1' }]"
          :filter-method="statusFilter"
        >
          <template scope="scope">
            <div>
              <el-switch
                v-model="scope.row.status"
                :active-value="1"
                :inactive-value="0"
                @change="handleChange($event,scope.row)"
              ></el-switch>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150">
          <template scope="scope">
            <div>
              <el-button size="mini" @click="handleEdit(scope.row)">编辑</el-button>
              <el-button size="mini" type="danger" @click="handleDel([scope.row])">删除</el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <div class="pos-rel p-t-20">
        <el-button type="success" :disabled="clickAble" @click="handleEdit(selections)">编辑</el-button>
        <el-button type="primary" :disabled="clickAble" @click="handleEnable(selections)">启用</el-button>
        <ElButton type="warning" :disabled="clickAble" @click="handleDisable(selections)">禁用</ElButton>
        <ElButton type="danger" :disabled="clickAble" @click="handleDel(selections)">删除</ElButton>

        <div class="block">
          <el-pagination
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="currentPage"
            :page-sizes="[5, 10, 20, 30]"
            :page-size="limit"
            layout="->,total, sizes, prev, pager, next, jumper"
            :total="dataCount"
          ></el-pagination>
        </div>
      </div>
    </el-card>
    <el-dialog title="编辑用户" :visible.sync="dialogFormVisible" @close="handleClose">
      <el-form
        ref="editForm"
        :model="editForm"
        :rules="batch ? brules : srules"
        label-width="130px"
      >
        <el-form-item label="用户名" prop="username">
          <el-input v-model.trim="editForm.username" :disabled="batch" class="h-40 w-200"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="psw">
          <el-input
            v-model.trim="editForm.psw"
            :disabled="batch"
            class="h-40 w-200"
            type="password"
          ></el-input>
        </el-form-item>
        <el-form-item label="学院" prop="institute">
          <el-select
            v-model="editForm.institute"
            :disabled="!disable.institute"
            placeholder="请选择学院"
            class="w-200"
          >
            <el-option v-for="item in institutes" :key="item.index" :label="item" :value="item"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="班级" prop="class">
          <el-input v-model.trim="editForm.class" :disabled="!disable.class" class="h-40 w-200"></el-input>
        </el-form-item>
        <el-form-item label="姓名" prop="name">
          <el-input v-model.trim="editForm.name" :disabled="!disable.name" class="h-40 w-200"></el-input>
        </el-form-item>
        <el-form-item label="学号" prop="stu_num">
          <el-input v-model.trim="editForm.stu_num" :disabled="!disable.stu_num" class="h-40 w-200"></el-input>
        </el-form-item>
        <el-form-item label="性别" prop="gender">
          <el-select
            v-model="editForm.gender"
            :disabled="!disable.gender"
            placeholder="请输入性别"
            class="h-40 w-200"
          >
            <el-option label="男" value="男"></el-option>
            <el-option label="女" value="女"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="年龄" prop="age">
          <el-input v-model.number="editForm.age" :disabled="!disable.age" class="h-40 w-200"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">取 消</el-button>
        <el-button
          type="primary"
          @click="handleSubmit"
          :loading="submitLoading"
          :disabled="submit"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<style lang="less" scoped>
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
    var validatePsw = (rule, value, callback) => {
      if ((value.length !== 0 && value.length <= 6) || value.length >= 16) {
        return callback(new Error('密码为6-16位'))
      }
      callback()
    }
    return {
      keyword: '',
      tableData: [],
      dataCount: null,
      loading: true,
      submitLoading: false,
      currentPage: 1,
      limit: 10,
      selections: [],
      clickAble: true,
      institutes: ['信息学院', '化工学院', '机械学院', '文法学院'],
      dialogFormVisible: false,
      batch: false,
      disable: {
        username: true,
        institute: true,
        class: true,
        name: false,
        stu_num: false,
        gender: false,
        age: false
      },
      editForm: {
        id: null,
        username: '',
        psw: '',
        institute: '',
        class: '',
        name: '',
        stu_num: null,
        gender: '',
        age: null
      },
      backup: {
        id: null,
        username: '',
        psw: '',
        institute: '',
        class: '',
        name: '',
        stu_num: null,
        gender: '',
        age: null
      },
      row: {},
      srules: {
        username: [
          { required: true, message: '请输入用户名', trigger: 'blur' },
          { min: 3, max: 10, message: '用户名长度为3-10位', trigger: 'blur' }
        ],
        psw: [{ validator: validatePsw, trigger: 'blur' }],
        institute: [{ required: true, message: '请输入学院' }],
        class: [{ required: true, message: '请输入班级' }]
      },
      brules: {}
    }
  },
  created() {
    this.getUserList()
  },
  methods: {
    handleChange($val, $row) {
      if ($val === 1) {
        this.handleEnable([$row])
      } else {
        this.handleDisable([$row])
      }
    },
    selectItem(item) {
      this.selections = item
    },
    handleSizeChange(val) {
      this.limit = val
      this.getUserList()
    },
    search() {
      this.getUserList()
    },
    handleCurrentChange(page) {
      this.currentPage = page
      this.getUserList()
    },
    async getUserList() {
      this.loading = true
      const data = {
        params: {
          keyword: this.keyword,
          page: this.currentPage,
          limit: this.limit
        }
      }
      await this.$http
        .get('/user', data)
        .then(res => {
          this.tableData = res.data.data
          this.dataCount = res.data.dataCount
        })
        .catch(err => {
          this.$message.error(err.data.msg)
        })
      this.loading = false
    },
    rankFilter(value, row) {
      return value === row.rank
    },
    statusFilter(value, row) {
      return value === row.status + ''
    },
    handleClose() {
      this.$refs.editForm.resetFields()
      this.backup.id = this.editForm.id = ''
      this.backup.username = this.editForm.username = ''
      this.backup.institute = this.editForm.institute = ''
      this.backup.class = this.editForm.class = ''
      this.backup.name = this.editForm.name = ''
      this.backup.stu_num = this.editForm.stu_num = ''
      this.backup.gender = this.editForm.gender = ''
      this.backup.age = this.editForm.age = ''
    },
    handleDisable(rowArr) {
      var idArr = this.getID(rowArr)
      this.$http
        .patch('/user', {
          id: idArr,
          status: 0
        })
        .then(res => {
          rowArr.forEach(element => {
            element.status = 0
          })
          this.$message.success('success')
        })
        .catch(err => {
          this.$message.error(err.data.msg)
        })
    },
    handleDel(rowArr) {
      this.$confirm('此操作将永久删除该设备, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          var idArr = this.getID(rowArr)
          this.$http
            .delete('/user', { data: idArr })
            .then(res => {
              this.$message.success(`成功删除${res.data.data}条数据!`)
              this.getUserList()
            })
            .catch(err => {
              this.$message.error(err.data.msg)
            })
        })
        .catch(() => {
          this.$message.info('已取消删除')
        })
    },
    handleEnable(rowArr) {
      var idArr = this.getID(rowArr)
      this.$http
        .patch('/user', {
          id: idArr,
          status: 1
        })
        .then(res => {
          rowArr.forEach(element => {
            element.status = 1
          })
          this.$message.success('success')
        })
        .catch(err => {
          this.$message.error(err.data.msg)
        })
    },
    handleEdit(row) {
      if (row instanceof Array) {
        this.batch = true
      } else {
        this.batch = false
        this.row = row
        this.backup.id = this.editForm.id = row.id
        this.backup.username = this.editForm.username = row.username
        this.backup.institute = this.editForm.institute = row.institute
        this.backup.class = this.editForm.class = row.class
        this.backup.name = this.editForm.name = row.name
        this.backup.stu_num = this.editForm.stu_num = row.stu_num
        this.backup.gender = this.editForm.gender = row.gender
        this.backup.age = this.editForm.age = row.age
      }
      this.dialogFormVisible = true
    },
    getID(arr) {
      var idArr = []
      arr.forEach(element => {
        idArr.push(element.id)
      })
      return idArr
    },
    async handleSubmit() {
      this.$refs.editForm.validate(valid => {
        if (!valid) return
        this.submitLoading = true
        var data = {}
        var id
        if (this.batch) {
          id = this.getID(this.selections)
        } else {
          id = [this.row.id]
        }
        // 只发送给后端修改过的数据
        for (var key in this.backup) {
          if (this.backup[key] !== this.editForm[key]) {
            data[key] = this.editForm[key]
          }
        }
        this.$http
          .patch('user', { id: id, options: data })
          .then(res => {
            if (this.batch) {
              this.getUserList()
            } else {
              for (var key in data) {
                this.row[key] = data[key]
              }
            }
            this.$message.success(`成功更新${res.data.data}条数据`)
            this.submitLoading = false

            this.dialogFormVisible = false
          })
          .catch(err => {
            this.$message.error(err.data.msg)
            this.submitLoading = false
            this.dialogFormVisible = false
          })
      })
    }
  },
  computed: {
    submit: function() {
      return JSON.stringify(this.editForm) === JSON.stringify(this.backup)
    }
  },
  watch: {
    $route(to, from) {
      this.getUserList()
    },
    selections() {
      if (this.selections.length === 0) {
        this.clickAble = true
      } else {
        this.clickAble = false
      }
    }
  }
}
</script>
