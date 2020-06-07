<template>
  <div>
    <el-breadcrumb separator-class="el-icon-arrow-right">
      <el-breadcrumb-item :to="{ path: '/home' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item>权限管理</el-breadcrumb-item>
      <el-breadcrumb-item>角色列表</el-breadcrumb-item>
    </el-breadcrumb>
    <el-card>
      <ElTable :data="rules" border>
        <el-table-column type="expand">
          <template slot-scope="scope">
            <el-row
              :class="['bdbottom','vcenter',index1 == 0 ? 'bdtop' : '']"
              v-for="(item1, index1) in scope.row.child"
              :key="index1"
            >
              <el-col :span="5">
                <el-tag type>{{item1.name}}</el-tag>
                <i class="el-icon-caret-right"></i>
              </el-col>
              <el-col :span="19">
                <el-row
                  :class="[index2 === 0 ? '' :'bdtop','vcenter']"
                  v-for="(item2, index2) in item1.child"
                  :key="index2"
                >
                  <el-col :span="6">
                    <el-tag type="success">{{item2.name}}</el-tag>
                    <i class="el-icon-caret-right"></i>
                  </el-col>
                  <el-col :span="18">
                    <el-tag
                      v-for="(item3, index3) in item2.child"
                      :key="index3"
                      type="warning"
                      closable
                      @close="removeRightById(item3.id)"
                    >{{item3.name}}</el-tag>
                  </el-col>
                </el-row>
              </el-col>
            </el-row>
            <!-- <pre>{{scope.row}}</pre> -->
          </template>
        </el-table-column>
        <el-table-column type="index"></el-table-column>
        <el-table-column label="名称" prop="name"></el-table-column>
        <el-table-column label="描述" prop="description"></el-table-column>
        <el-table-column label="操作">
          <template slot-scope="scope">
            <el-radio-button size="mini" label="分配权限" @click="handleClick(scope.row.scope)"></el-radio-button>
          </template>
        </el-table-column>
      </ElTable>
    </el-card>
  </div>
</template>
<style lang="less" scoped>
.el-tag {
  margin: 7px;
}
.bdtop {
  border-top: 1px solid #eee;
}
.bdbottom {
  border-bottom: 1px solid #eee;
}
.vcenter {
  display: flex;
  align-items: center;
}
</style>
<script>
export default {
  data() {
    return {
      rules: []
    }
  },
  created() {
    this.getRules()
  },
  methods: {
    removeRightById(id) {
      this.$confirm('此操作将永久删除该设备, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          // this.$http
          //   .delete('/equip', { data: { id: idArr } })
          //   .then(res => {
          //     this.$message.success(`成功删除${res.data.data}条数据!`)
          //     this.getEquipList()
          //   })
          //   .catch(err => {
          //     this.$message.error(err.data.msg)
          //   })
        })
        .catch(() => {
          this.$message.info('已取消删除')
        })
    },
    getRules() {
      this.$http
        .get('/rules')
        .then(res => {
          this.rules = res.data
        })
        .catch(err => {
          console.log(err)
        })
      console.log(this.rules)
    }
  }
}
</script>
