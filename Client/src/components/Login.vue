<template>
  <div class="login_container">
    <div class="login_box">
      <div class="avatar_box">
        <img src="../assets/logo.png" alt />
      </div>
      <el-form ref="loginForm" class="login_form" :model="form" :rules="rule">
        <el-form-item prop="name">
          <el-input placeholder="用户名" prefix-icon="el-icon-user-solid" v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item prop="psw">
          <el-input placeholder="密码" prefix-icon="el-icon-lock" v-model="form.psw" type="password"></el-input>
        </el-form-item>
        <el-select v-model="form.rank" placeholder="请选择">
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
        <el-form-item class="btns">
          <el-button type="primary" @click="login">登陆</el-button>
          <el-button type="info" @click="resetForm">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import Cookies from 'js-cookie'
export default {
  data() {
    return {
      form: {
        name: 'teacher',
        psw: '123456789',
        rank: 'teacher'
      },
      options: [
        {
          value: 'student',
          label: '学生'
        },
        {
          value: 'teacher',
          label: '教师'
        },
        {
          value: 'engineer',
          label: '工程师'
        },
        {
          value: 'root',
          label: '管理员'
        }
      ],

      rule: {
        name: [
          { required: true, message: '请输入用户名', trigger: 'blur' },
          { min: 3, max: 10, message: '请输入3到10个字符', trigger: 'blur' }
        ],
        psw: [
          { required: true, message: '请输入密码', trigger: 'blur' },
          { min: 6, max: 16, message: '请输入6到16位字符', trigger: 'blur' }
        ]
      },
      publicKey: ''
    }
  },
  methods: {
    resetForm: function() {
      this.$refs.loginForm.resetFields()
      this.$message.success('已重置')
    },
    login() {
      var _ = this
      this.$refs.loginForm.validate(async valid => {
        if (!valid) return
        // if (!localStorage.getItem('key')) {
        //   await this.$http
        //     .get('/key')
        //     .then(async res => {
        //       const PublicKeyEncode = res.data.key
        //       const PublicKey = window.atob(PublicKeyEncode)
        //       localStorage.setItem('key', PublicKey)
        //     })
        //     .catch(err => {
        //       this.$message.error(err.data.msg)
        //     })
        // }
        // _.$jse.setPublicKey(localStorage.getItem('key'))
        const psw = _.$jse.encrypt(this.form.psw)
        await this.$http
          .post('/token', {
            name: this.form.name,
            psw: psw,
            rank: this.form.rank
          })
          .then(res => {
            this.$message.success('登陆成功！')
            Cookies.set('token', res.data.token, { expires: 3 })
            // window.sessionStorage.setItem('token', res.data.token)
            // window.sessionStorage.setItem(
            //   'exp',
            //   res.data.expire + new Date().getTime()
            // )
            const redirect = decodeURIComponent(
              this.$route.query.redirect || '/home'
            )
            this.$router.push(redirect)
          })
          .catch(err => {
            this.$message.error(err.data.msg)
          })
      })
    }
  }
}
</script>

<style scoped>
.login_container {
  height: 100%;
  background-color: #2b4b6b;
}
.login_box {
  width: 450px;
  height: 350px;
  background-color: #fff;
  border-radius: 3px;
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
}
.avatar_box {
  height: 130px;
  width: 130px;
  border: 1px #2b4b6b solid;
  border-radius: 50%;
  padding: 10px;
  box-shadow: 0 0 10px #ddd;
  position: absolute;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #fff;
}
.avatar_box > img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: #eee;
}
.login_form {
  position: absolute;
  bottom: 0;
  width: 100%;
  padding: 15px;
  box-sizing: border-box;
}
.btns {
  display: flex;
  justify-content: flex-end;
}
</style>
