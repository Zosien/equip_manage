<template>
  <div>
    <el-breadcrumb separator-class="el-icon-arrow-right">
      <el-breadcrumb-item :to="{ path: '/home' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>用户管理</el-breadcrumb-item>
      <el-breadcrumb-item>添加用户</el-breadcrumb-item>
    </el-breadcrumb>
    <el-card>
      <el-tabs v-model="activeName" @tab-click="handleClick">
        <el-tab-pane label="逐个添加" name="first">
          <el-form ref="addForm" :model="form" :rules="rules" label-width="130px">
            <el-form-item label="用户名" prop="username">
              <el-input v-model.trim="form.username" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="psw">
              <el-input v-model.trim="form.psw" class="h-40 w-200" type="password"></el-input>
            </el-form-item>
            <el-form-item label="学院" prop="institute">
              <el-select v-model="form.institute" placeholder="请选择学院" class="w-200">
                <el-option v-for="item in institutes" :key="item.index" :label="item" :value="item"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="班级" prop="class">
              <el-input v-model.trim="form.class" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="姓名" prop="name">
              <el-input v-model.trim="form.name" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="学号" prop="stu_num">
              <el-input v-model.trim="form.stu_num" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="性别" prop="gender">
              <el-select v-model="form.gender" placeholder="请输入性别" class="h-40 w-200">
                <el-option label="男" value="男"></el-option>
                <el-option label="女" value="女"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="年龄" prop="age">
              <el-input v-model.number="form.age" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="add()">提交</el-button>
              <el-button @click="$base.goBack()">返回</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
        <el-tab-pane label="批量添加" name="second">
          <el-upload
            class="upload-demo"
            ref="upload"
            action="http://db.com/api/user/upload"
            accept=".xlsx"
            :auto-upload="false"
            :on-success="handleSuccess"
          >
            <el-button slot="trigger" type="primary">选取文件</el-button>
            <el-button style="margin-left: 10px" type="success" @click="submit">提交</el-button>
          </el-upload>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>
<style lang="less" scoped>
.el-breadcrumb {
  margin-bottom: 15px;
}
.form-checkbox:first-child {
  margin-left: 15px;
}
</style>
<script>
export default {
  data() {
    var validateStuNum = (rule, value, callback) => {
      var regx = /^20((1[4-9])|(20))\d{6}$/
      if (!regx.test(value)) {
        return callback(new Error('请输入正确的学号'))
      }
      callback()
    }
    return {
      isLoading: false,
      activeName: 'first',
      form: {
        username: '2017040353',
        psw: '2017040353',
        institute: '信息学院',
        class: '计科1704',
        name: '李泽祥',
        stu_num: '2017040353',
        gender: '男',
        age: 24
      },
      data: {},
      orgsOptions: [],
      groupOptions: [],
      selectedGroups: [],
      selectedIds: [],
      institutes: ['信息学院', '化工学院', '机械学院', '文法学院'],
      rules: {
        username: [
          { required: true, message: '请输入用户名', trigger: 'blur' },
          { min: 3, max: 10, message: '用户名长度为3-10位', trigger: 'blur' }
        ],
        psw: [
          { required: true, message: '请输入用户密码', trigger: 'blur' },
          { min: 6, max: 16, message: '密码长度为6-16位', trigger: 'blur' }
        ],
        institute: [{ required: true, message: '请输入学院' }],
        class: [{ required: true, message: '请输入班级' }],
        name: [{ required: true, message: '请输入姓名' }],
        stu_num: [
          { required: true, message: '请输入学号' },
          { validator: validateStuNum, trigger: 'blur' }
        ]
        // age: [{ type: 'number', message: '年龄必须为数字值' }]
      }
    }
  },
  methods: {
    submit() {
      this.$refs.upload.submit()
    },
    add() {
      var _ = this
      this.$refs.addForm.validate(valid => {
        if (!valid) return
        this.data = JSON.parse(JSON.stringify(this.form))
        this.data.psw = _.$jse.encrypt(this.form.psw)
        this.$http
          .post('user', this.data)
          .then(res => {
            console.log(res.data)
            if (res.data) {
              this.$message.success(res.data.msg)
              this.$router.push('/user/list')
            }
          })
          .catch(err => {
            this.$message.error(err.data.msg)
          })
      })
    },
    handleSuccess(response, file, fileList) {
      this.$message.success('添加用户成功')
      this.$router.push('/user/list')
    }
    // created() {
    //   this.getAllGroups()
    //   this.getAllOrgs()
    // }
  }
}
</script>
