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
        <el-form-item class="btns">
          <el-button type="primary" @click="login()">登陆</el-button>
          <el-button type="info" @click="resetForm()">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        name: 'lzx',
        psw: '123456789'
      },
      rule: {
        name: [
          { required: true, message: '请输入用户名', trigger: 'blur' },
          { required: true, message: '请输入3到10个字符', trigger: 'blur' }
        ],
        psw: [
          { required: true, message: '请输入密码', trigger: 'blur' },
          { required: true, message: '请输入6到16位字符', trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    resetForm: function() {
      this.$refs.loginForm.resetFields()
      this.$message.success('已重置')
    },
    login() {
      this.$refs.loginForm.validate(async valid => {
        if (!valid) return
        const result = await this.$http.post('/token', this.form)
        console.log(result)
        console.log(result.data.token)
        if (result.status !== 200) {
          return this.$message.error('登陆失败！')
        } else {
          this.$message.success('登陆成功！')
          window.sessionStorage.setItem('token', result.data)
          // this.$router.push('/home')
        }
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
  height: 300px;
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
