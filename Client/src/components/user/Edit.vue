<template>
  <div>
    <el-breadcrumb separator-class="el-icon-arrow-right">
      <el-breadcrumb-item :to="{ path: '/home' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>用户管理</el-breadcrumb-item>
      <el-breadcrumb-item>修改用户</el-breadcrumb-item>
    </el-breadcrumb>
    <el-card>
      <el-form :ref="form" :model="formData" :rules="rules" label-width="130px">
        <el-form-item label="用户名" prop="username">
          <el-input
            v-model.trim="formData.username"
            v-bind:disabled="!form.username.able"
            class="h-40 w-200"
          ></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="psw">
          <el-input
            v-model.trim="formData.psw"
            :disabled="!form.psw.able"
            class="h-40 w-200"
            type="password"
          ></el-input>
        </el-form-item>
        <el-form-item label="学院" prop="institute">
          <el-select
            v-model="formData.institute"
            :disabled="!form.institute.able"
            placeholder="请选择学院"
            class="w-200"
          >
            <el-option v-for="item in institutes" :key="item.index" :label="item" :value="item"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="班级" prop="class">
          <el-input v-model.trim="formData.class" :disabled="!form.class.able" class="h-40 w-200"></el-input>
        </el-form-item>
        <el-form-item label="姓名" prop="name">
          <el-input v-model.trim="formData.name" :disabled="!form.name.able" class="h-40 w-200"></el-input>
        </el-form-item>
        <el-form-item label="学号" prop="stu_num">
          <el-input
            v-model.trim="formData.stu_num"
            :disabled="!form.stu_num.able"
            class="h-40 w-200"
          ></el-input>
        </el-form-item>
        <el-form-item label="性别" prop="gender">
          <el-select
            v-model="formData.gender"
            :disabled="!form.gender.able"
            placeholder="请输入性别"
            class="h-40 w-200"
          >
            <el-option label="男" value="男"></el-option>
            <el-option label="女" value="女"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="年龄" prop="age">
          <el-input v-model.number="formData.age" :disabled="!form.age.able" class="h-40 w-200"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :disabled="submit" @click="add()">提交</el-button>
          <el-button @click="$base.goBack()">返回</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>
<style lang="less" scoped>
.el-breadcrumb {
  margin-bottom: 15px;
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
      id: null,
      formData: {
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
        username: '',
        psw: '',
        institute: '',
        class: '',
        name: '',
        stu_num: null,
        gender: '',
        age: null
      },
      form: {},
      institutes: ['信息学院', '化工学院', '机械学院', '文法学院'],
      rules: {
        username: [
          { required: true, message: '请输入用户名', trigger: 'blur' },
          { min: 3, max: 10, message: '用户名长度为3-10位', trigger: 'blur' }
        ],
        psw: [{ validator: validatePsw, trigger: 'blur' }],
        institute: [{ required: true, message: '请输入学院' }],
        class: [{ required: true, message: '请输入班级' }]
      }
    }
  },
  methods: {
    add() {
      var {
        name,
        gender,
        // eslint-disable-next-line camelcase
        stu_num,
        age,
        ...data
      } = this.formData
      this.$http
        .patch('user/' + this.id, data)
        .then(res => {
          console.log(res)
        })
        .catch(err => {
          console.log(err)
        })
    },
    async getData() {
      this.$http
        .get('user/' + this.id)
        .then(res => {
          this.form = res.data
          for (var key in this.formData) {
            this.formData[key] = this.form[key].value
            this.backup[key] = this.form[key].value
          }
        })
        .catch(err => {
          console.log(err)
        })
    }
  },
  created() {
    this.id = this.$route.params.id
    this.getData()
  },
  computed: {
    submit: function() {
      return JSON.stringify(this.formData) === JSON.stringify(this.backup)
    }
  },
  watch: {
    change: function(val, oldVal) {
      console.log('y')
    }
    // 'formData.username': function(val, oldVal) {
    //   console.log(val)
    // },
    // 'formData.psw': function(val, oldVal) {
    //   console.log(val)
    // },
    // 'formData.institute': function(val, oldVal) {
    //   console.log(val)
    // },
    // 'formData.class': function(val, oldVal) {
    //   console.log(val)
    // }
  }
}
</script>
