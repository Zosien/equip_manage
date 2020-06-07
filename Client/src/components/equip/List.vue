<template>
  <div>
    <el-breadcrumb separator-class="el-icon-arrow-right">
      <el-breadcrumb-item :to="{ path: '/home' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>设备管理</el-breadcrumb-item>
      <el-breadcrumb-item>设备列表</el-breadcrumb-item>
    </el-breadcrumb>
    <el-card>
      <el-row>
        <el-col :span="3">
          <router-link class="btn-link-large add-btn" to="add">
            <i class="el-icon-plus"></i>&nbsp;&nbsp;添加设备
          </router-link>
        </el-col>
        <el-col :span="4">
          <el-input
            placeholder="请输入设备名"
            v-model="keyword"
            @keyup.enter.native="search()"
            clearable
            @clear="getEquipList"
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
        <el-table-column label="id" prop="id"></el-table-column>
        <el-table-column label="设备名" prop="name"></el-table-column>
        <el-table-column label="价格" prop="price"></el-table-column>
        <el-table-column label="厂家" prop="factory"></el-table-column>
        <el-table-column label="购买时间" prop="purchase_time"></el-table-column>
        <el-table-column label="负责人" prop="buyer" width="80"></el-table-column>
        <el-table-column label="实验室" prop="lab"></el-table-column>
        <el-table-column
          label="状态"
          prop="useable"
          :filters="[{ text: '可用', value: '1' }, { text: '维修中', value: '0' },{ text: '报废', value: '-1' }]"
          :filter-method="statusFilter"
          width="90"
        >
          <template scope="scope">
            <div>
              <el-tag type="success" v-if="scope.row.useable === 1">可用</el-tag>
              <el-tag type="warning" v-else-if="scope.row.useable === 0">维修中</el-tag>
              <el-tag type="error" v-else>报废</el-tag>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="220">
          <template scope="scope">
            <div>
              <!-- TODO:逻辑实现 -->
              <el-button size="mini" type="primary" @click="handleEdit(scope.row)">报修</el-button>
              <el-button size="mini" @click="handleEdit(scope.row)">编辑</el-button>
              <el-button size="mini" type="danger" @click="handleDel([scope.row])">删除</el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <div class="pos-rel p-t-20">
        <el-button type="success" :disabled="clickAble" @click="handleEdit(selections)">编辑</el-button>
        <ElButton type="warning" :disabled="clickAble" @click="handleDel(selections)">删除</ElButton>
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
      <el-dialog title="编辑设备" :visible.sync="dialogFormVisible" @close="handleClose">
        <el-form
          ref="editForm"
          :model="editForm"
          :rules="batch ? brules : srules"
          label-width="130px"
        >
          <el-form-item label="设备名" prop="name">
            <el-input v-model.trim="editForm.name" class="h-40 w-200"></el-input>
          </el-form-item>
          <el-form-item label="价格" prop="price">
            <el-input v-model="editForm.price" class="h-40 w-200"></el-input>
          </el-form-item>
          <el-form-item label="状态">
            <el-select v-model="editForm.useable" placeholder="请选择状态" class="w-200">
              <el-option
                v-for="item in useable"
                :key="item.index"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="厂家" prop="factory">
            <el-input v-model.trim="editForm.factory" class="h-40 w-200"></el-input>
          </el-form-item>
          <el-form-item label="负责人" prop="buyer">
            <el-input v-model.trim="editForm.buyer" class="h-40 w-200"></el-input>
          </el-form-item>
          <el-form-item label="实验室" prop="lab">
            <el-input v-model.trim="editForm.lab" class="h-40 w-200"></el-input>
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
    </el-card>
  </div>
</template>
<style lang="less" scoped>
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
    var bPriceValidator = (rule, value, callback) => {
      if (value.length) {
        var regx = /^\d+(\.(\d){1,2})?$/
        if (!regx.test(value)) {
          return callback(new Error('必须是数字，且小数点后最多两位'))
        }
      }
      callback()
    }
    return {
      keyword: '',
      batch: false,
      currentPage: 1,
      limit: 20,
      loading: false,
      submitLoading: false,
      clickAble: true,
      dialogFormVisible: false,
      row: {},
      selections: [],
      tableData: [],
      labTable: [],
      dataCount: null,
      editForm: {
        name: '',
        price: '',
        useable: '',
        factory: '',
        buyer: '',
        lab: ''
      },
      backup: {
        name: '',
        price: '',
        useable: '',
        factory: '',
        buyer: '',
        lab: ''
      },
      srules: {
        name: [{ required: true, message: '请输入设备名', trigger: 'blur' }],
        price: [
          { required: true, message: '请输入价格', trigger: 'blur' },
          { validator: numValidator, trigger: 'blur' }
        ],
        factory: [{ required: true, message: '请输入厂家', trigger: 'blur' }],
        buyer: [{ required: true, message: '请输入负责人', trigger: 'blur' }],
        lab: [{ required: true, message: '请输入实验室', trigger: 'blur' }]
      },
      brules: {
        price: [{ validator: bPriceValidator, trigger: 'blur' }]
      },
      useable: [
        {
          value: 1,
          label: '可用'
        },
        {
          value: 0,
          label: '维修中'
        },
        {
          value: -1,
          label: '报废'
        }
      ]
    }
  },
  methods: {
    handleSizeChange(val) {
      this.limit = val
      this.getEquipList()
    },
    handleCurrentChange(page) {
      this.currentPage = page
      this.getEquipList()
    },
    handleDel(rowArr) {
      this.$confirm('此操作将永久删除该设备, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          console.log(rowArr)

          var idArr = this.getID(rowArr)
          this.$http
            .delete('/equip', { data: { id: idArr } })
            .then(res => {
              this.$message.success(`成功删除${res.data.data}条数据!`)
              this.getEquipList()
            })
            .catch(err => {
              this.$message.error(err.data.msg)
            })
        })
        .catch(() => {
          this.$message.info('已取消删除')
        })
    },
    selectItem(item) {
      this.selections = item
    },
    statusFilter(value, row) {
      return value === row.status + ''
    },
    search() {
      this.getEquipList()
    },
    getLabList() {
      this.$http
        .get('/lab')
        .then(res => {
          this.labTable = res.data.data
        })
        .catch(err => {
          console.log(err)
        })
    },
    async getEquipList() {
      this.loading = true
      const data = {
        params: {
          keyword: this.keyword,
          page: this.currentPage,
          limit: this.limit
        }
      }
      await this.$http
        .get('/equip', data)
        .then(res => {
          this.tableData = res.data.data
          this.dataCount = res.data.dataCount
        })
        .catch(err => {
          this.$message.error(err.data.msg)
        })
      this.loading = false
    },
    getID(arr) {
      var idArr = []
      arr.forEach(element => {
        idArr.push(element.id)
      })
      return idArr
    },
    handleEdit(row) {
      if (row instanceof Array) {
        this.batch = true
      } else {
        this.batch = false
        this.row = row
        this.backup.id = this.editForm.id = row.id
        this.backup.name = this.editForm.name = row.name
        this.backup.price = this.editForm.price = row.price
        this.backup.useable = this.editForm.useable = row.useable
        this.backup.factory = this.editForm.factory = row.factory
        this.backup.buyer = this.editForm.buyer = row.buyer
        this.backup.lab = this.editForm.lab = row.lab
      }
      this.dialogFormVisible = true
    },
    handleClose() {
      this.$refs.editForm.resetFields()
      this.backup.id = this.editForm.id = ''
      this.backup.name = this.editForm.name = ''
      this.backup.price = this.editForm.price = ''
      this.backup.useable = this.editForm.useable = ''
      this.backup.factory = this.editForm.factory = ''
      this.backup.buyer = this.editForm.buyer = ''
      this.backup.lab = this.editForm.lab = ''
    },
    handleSubmit() {
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
          .patch('equip', { id: id, options: data })
          .then(res => {
            if (!this.batch) {
              for (var key in data) {
                this.row[key] = data[key]
              }
            } else {
              this.getEquipList()
            }

            this.$message.success(`成功编辑${res.data.data}条数据`)
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
  created() {
    this.getEquipList()
  },
  watch: {
    $route(to, from) {
      this.getEquipList()
    },
    selections() {
      if (this.selections.length === 0) {
        this.clickAble = true
      } else {
        this.clickAble = false
      }
    }
  },
  computed: {
    submit: function() {
      return JSON.stringify(this.editForm) === JSON.stringify(this.backup)
    }
  }
}
</script>
