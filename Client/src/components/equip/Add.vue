<template>
  <div>
    <el-breadcrumb separator-class="el-icon-arrow-right">
      <el-breadcrumb-item :to="{ path: '/home' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>设备管理</el-breadcrumb-item>
      <el-breadcrumb-item :to="{path: '/equip/list'}">设备列表</el-breadcrumb-item>
      <el-breadcrumb-item>添加设备</el-breadcrumb-item>
    </el-breadcrumb>
    <el-card>
      <el-tabs v-model="activeName">
        <el-tab-pane label="逐个添加" name="first">
          <el-form ref="addForm" :model="addForm" :rules="rules" label-width="130px">
            <el-form-item label="设备号" prop="id">
              <el-input v-model.trim="addForm.id" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="设备名" prop="name">
              <el-input v-model.trim="addForm.name" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="等级" prop="level">
              <el-input v-model="addForm.level" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="价格" prop="price">
              <el-input v-model="addForm.price" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="厂家" prop="factory">
              <el-input v-model.trim="addForm.factory" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="寿命" prop="life_span">
              <el-input v-model="addForm.life_span" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="负责人" prop="buyer">
              <el-input v-model.trim="addForm.buyer" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="实验室" prop="lab">
              <el-input v-model.trim="addForm.lab" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item label="详细信息" prop="details">
              <el-input v-model.trim="addForm.details" class="h-40 w-200"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="add()">提交</el-button>
              <el-button @click="$base.goBack()">返回</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
        <el-tab-pane label="批量添加" name="second">
          <div class="help">
            通过excel文件批量添加，
            <a href="http://db.com/equip.xlsx" download="模板.xlsx">点击下载</a>模板文件
          </div>
          <el-upload
            class="upload-demo"
            ref="upload"
            action="http://db.com/api/equip/upload"
            accept=".xlsx"
            :auto-upload="false"
            :on-success="handleSuccess"
            :on-error="handleError"
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
.help {
  font-size: 13px;
  margin-bottom: 5px;
}
</style>
<script>
export default {
  data() {
    var numValidator = (rule, value, callback) => {
      var regx = /^\d+(\.(\d){1,2})?$/
      if (!regx.test(value)) {
        return callback(new Error('必须是数字，且小数点后最多两位'))
      }
      callback()
    }
    return {
      activeName: 'first',
      addForm: {
        id: '',
        name: '',
        level: '',
        price: '',
        factory: '',
        life_span: '',
        buyer: '',
        lab: '',
        details: ''
      },
      rules: {
        id: [{ required: true, message: '请输入设备号', trigger: 'blur' }],
        name: [{ required: true, message: '请输入设备名', trigger: 'blur' }],
        price: [
          { required: true, message: '请输入价格', trigger: 'blur' },
          { validator: numValidator, trigger: 'blur' }
        ],
        factory: [{ required: true, message: '请输入厂家', trigger: 'blur' }],
        buyer: [{ required: true, message: '请输入负责人', trigger: 'blur' }],
        lab: [{ required: true, message: '请输入实验室', trigger: 'blur' }]
      }
    }
  },
  methods: {
    submit() {
      this.$refs.upload.submit()
    },
    handleSuccess(response, file, fileList) {
      this.$message.success('添加设备成功')
      this.$router.push('/equip/list')
    },
    handleError(err, file, fileList) {
      this.$message.error(err.data.msg)
    },
    add() {
      var _ = this
      this.$refs.addForm.validate(valid => {
        if (!valid) return
        this.$http
          .post('equip', this.addForm)
          .then(res => {
            console.log(res.data)
            if (res.data) {
              this.$message.success(`成功添加${res.data.num}条记录`)
              this.$router.push('/equip/list')
            }
          })
          .catch(err => {
            this.$message.error(err.data.msg)
          })
      })
    }
  }
}
</script>
