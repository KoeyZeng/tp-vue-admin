<template>
  <el-container class="container">
    <el-header class="header-box">
      <el-row :gutter="24" justify="space-between">
        <el-col :span="12">
          <div class="grid-content bg-purple">
            <el-input v-model="listQuery.name" style="width: 200px;" placeholder="请输入角色名称" />
            <el-button class="filter-item" type="primary" icon="el-icon-search" @click="getList">
              查询
            </el-button>
          </div>
        </el-col>
        <el-col :span="12">
          <div class="grid-content bg-purple-right">
            <el-button class="filter-item ml-10" type="primary" icon="el-icon-plus" @click="show">
              添加
            </el-button>
          </div>
        </el-col>
      </el-row>
    </el-header>
    <el-main>
      <!-- 表格控件 -->
      <el-table v-loading="tableLoading" :data="tableData" stripe style="width:100%">
        <el-table-column prop="id" label="ID" width="60" align="center" />
        <el-table-column prop="id" label="ID" width="60" align="center" />
        <el-table-column prop="name" label="角色名称" align="center" />
        <el-table-column prop="content" label="描述" align="center" />
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template slot-scope="{row}">
            <el-tooltip class="programPublish" :content="row.status == '0'?'禁用':'启动' " placement="top">
              <el-button class="table-action-button" type="text">
                <svg-icon class="icon" :icon-class="row.status == '0'?'suspend':'broadcast'" />
              </el-button>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column prop="play" label="操作" width="160" align="center">
          <template slot-scope="{row}">
            <el-tooltip content="修改" placement="top">
              <el-button class="table-action-button" type="text" @click="edit(row)">
                <svg-icon class="icon" icon-class="edit" />
              </el-button>
            </el-tooltip>
            <el-tooltip content="删除" placement="top">
              <el-button class="table-action-button" type="text" :disabled="row.id === 1" @click="del(row.id)">
                <svg-icon class="icon" icon-class="delete" />
              </el-button>
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>
      <el-dialog
        :title="formData.title"
        :visible.sync="formShow"
      >
        <el-form ref="Form" :model="formData" :rules="rules" label-width="130px" label-position="right">
          <el-row :gutter="24">
            <el-col :span="14">
              <el-form-item prop="name" label="角色名称">
                <el-input v-model="formData.name" class="w-90" placeholder="请输入角色名称" />
              </el-form-item>
              <el-form-item prop="content" label="描述">
                <el-input v-model="formData.content" type="textarea" :rows="2" class="w-90" placeholder="请输入描述" />
              </el-form-item>
              <el-form-item prop="status" label="状态">
                <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" :disabled="formData.id === 1" />
              </el-form-item>
            </el-col>
            <el-col :span="10">
              <el-form-item label-width="0">
                <el-tree ref="tree" :check-strictly="checkStrictly" :data="menuList" :props="menuTreeProps" :show-checkbox="formData.id !== 1" node-key="id" />
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" icon="el-icon-close" @click="formShow = false">取消</el-button>
          <el-button type="primary" icon="el-icon-check" :disabled="formShow === false" @click="save">保存</el-button>
        </span>
      </el-dialog>
    </el-main>
  </el-container>

</template>

<script>
import { getMenu, getRole, saveRole, delRole } from '@/api/system'

const formData = {
  title: '添加角色',
  id: 0,
  name: '',
  menu: '',
  content: '',
  status: 1
}

export default {
  name: 'Role',
  data() {
    return {
      tableLoading: false,
      formShow: false,
      listQuery: {
        name: ''
      },
      menuList: [],
      menuTreeProps: {
        children: 'children',
        label: 'name'
      },
      checkStrictly: false,
      rules: {
        name: [
          { required: true, message: '请输入角色名称！', trigger: 'blur' },
          { min: 1, max: 50, message: '输入角色名称必须在1至50个字符内！', trigger: 'blur' },
          { pattern: /^[A-Za-z0-9\u4e00-\u9fa5_-]+$/, message: '角色名称只能包含字母、数字、汉字、下划线_及破折号-和@！', trigger: 'blur' }
        ],
        content: [
          { min: 0, max: 250, message: '输入描述必须在0至250个字符内！', trigger: 'blur' }
        ]
      },
      tableData: [],
      formData: Object.assign({}, formData)
    }
  },
  created() {
    this.getList()
    this.getMenuList()
  },
  methods: {
    // 加载表格数据
    getList() {
      this.tableLoading = true
      getRole(this.listQuery).then(res => {
        this.tableLoading = false
        this.tableData = res.data.list
      }).catch(res => {
        this.tableLoading = false
      })
    },
    getMenuList() {
      getMenu().then(res => {
        this.menuList = res.data.list
      })
    },
    // 显示表单
    show() {
      // 清除表单校验结果
      setTimeout(() => { this.$refs.Form.clearValidate() }, 0)
      this.formShow = true
      this.$nextTick(() => {
        this.$refs.tree.setCheckedKeys([])
      })
      this.formData = Object.assign({}, formData)
    },
    // 显示编辑表单
    edit(row) {
      this.show()
      this.formData.title = '编辑角色'
      this.formData = Object.assign({}, row)
      this.checkStrictly = true
      this.$nextTick(() => {
        this.$refs.tree.setCheckedKeys(row.menu.split(','))
        this.checkStrictly = false
      })
    },
    // 删除
    del(id) {
      if (id === 1) {
        this.$message.error('超级管理员角色无法被删除！')
        return
      }
      this.$confirm('此操作将删除该角色, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const data = {
          id: id
        }
        delRole(data).then(res => {
          this.$message.success(res.msg)
          // 重新加载页面数据
          this.getList()
        }).catch(() => {})
      }).catch(() => {})
    },
    // 保存表单
    save() {
      this.$refs['Form'].validate((valid) => {
        if (valid) {
          this.formShow = false
          this.formData.menu = this.$refs.tree.getCheckedKeys().concat(this.$refs.tree.getHalfCheckedKeys()).sort().join(',')

          saveRole(this.formData).then(res => {
            this.$message.success(res.msg)
            this.getList()
          }).catch(() => {})
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
