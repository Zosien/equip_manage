<template>
  <div>
    <el-dialog ref="dialog" title="修改密码" :visible.sync="visible">
      <div>
        <el-form ref="form" :model="form" :rules="rules" label-width="100px">
          <el-form-item label="旧密码" prop="old_psw">
            <el-input type="password" v-model.trim="form.old_psw"></el-input>
          </el-form-item>
          <el-form-item label="新密码" prop="new_psw">
            <el-input type="password" v-model.trim="form.new_psw"></el-input>
          </el-form-item>
          <el-form-item label="确认密码" prop="con_psw">
            <el-input type="password" v-model.trim="form.con_psw"></el-input>
          </el-form-item>
        </el-form>
      </div>
      <div class="p-t-20">
        <el-button type="info" class="fl m-l-20" @click="handleCancle">取消</el-button>
        <el-button type="primary" class="fl m-l-20" :loading="disable" @click="handleSubmit">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<style lang="less" scoped>
</style>
<script>
export default {
  props: {
    isShow: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    visible: function() {
      return this.isShow
    }
  },

  data() {
    var confirmPsw = (rule, value, callback) => {
      if (value !== this.form.new_psw) {
        return callback(new Error('两次密码不一致'))
      }
      callback()
    }
    return {
      form: {
        old_psw: '',
        new_psw: '',
        con_psw: ''
      },
      data: {
        old_psw: '',
        new_psw: ''
      },
      rules: {
        old_psw: [{ required: true, message: '请输入旧密码', trigger: 'blur' }],
        new_psw: [{ required: true, message: '请输入新密码', trigger: 'blur' }],
        con_psw: [
          { required: true, message: '请确认密码', trigger: 'blur' },
          { validator: confirmPsw, trigger: 'blur' }
        ]
      },
      disable: false
    }
  },
  methods: {
    handleSubmit() {
      this.$refs.form.validate(valid => {
        if (!valid) return
        this.disable = true
        this.data.old_psw = this.$jse.encrypt(this.form.old_psw)
        this.data.new_psw = this.$jse.encrypt(this.form.new_psw)
        this.$http
          .patch('/user/psw', this.data)
          .then(res => {
            this.$message.success(res.data.data)
            this.disable = false
            this.handleCancle()
          })
          .catch(err => {
            this.disable = false
            this.$message.error(err.data.msg)
          })
      })
    },
    handleCancle() {
      this.$emit('close', false)
    }
  }
}
</script>
