<template>
  <el-container class="container">
    <el-header class="header-box">
      <el-row :gutter="24" justify="space-between">
        <el-col :span="12">
          <div class="grid-content bg-purple">
            <el-input v-model="listQuery.username" style="width: 200px;" placeholder="请输入账号" />
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
        <el-table-column type="index" label="序号" width="60" align="center" :index="indexMethod" />
        <el-table-column prop="username" label="账号" align="center" />
        <el-table-column prop="nickname" label="昵称" align="center" />
        <el-table-column prop="phone" label="手机" align="center" />
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template slot-scope="{row}">
            <el-tooltip class="programPublish" :content="row.status === 0?'禁用':'启动' " placement="top">
              <el-button class="table-action-button" type="text">
                <svg-icon class="icon" :icon-class="row.status === 0?'suspend':'broadcast'" />
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
      <!-- 分页控件 -->
      <pagination
        v-show="total>listQuery.limit"
        :total="total"
        :page.sync="listQuery.page"
        :limit.sync="listQuery.limit"
        @pagination="getList"
      />
      <!-- 表单控件 -->
      <el-dialog
        :title="formData.title"
        :visible.sync="formShow"
        width="700px"
      >
        <el-form ref="Form" :model="formData" :rules="rules" label-width="130px" label-position="right">
          <el-form-item prop="role" label="角色">
            <el-select v-model="formData.role" class="w-90" placeholder="请选择角色" :disabled="formData.id === 1">
              <el-option
                v-for="item in roleList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item prop="username" label="账号">
            <el-input v-model="formData.username" class="w-90" placeholder="请输入账号" />
          </el-form-item>
          <el-form-item prop="password" label="密码">
            <el-input v-model="formData.password" type="password" class="w-90" placeholder="请输入密码" />
          </el-form-item>
          <el-form-item prop="nickname" label="昵称">
            <el-input v-model="formData.nickname" class="w-90" placeholder="请输入昵称" />
          </el-form-item>
          <el-form-item prop="phone" label="手机">
            <el-input v-model="formData.phone" class="w-90" placeholder="请输入手机" />
          </el-form-item>
          <el-form-item prop="status" label="状态">
            <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" :disabled="formData.id === 1" />
          </el-form-item>
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
import { getAdmin, getRole, saveAdmin, delAdmin } from '@/api/system'
import Pagination from '@/components/Pagination' // secondary package based on el-pagination

const formData = {
  title: '添加管理员',
  id: 0,
  username: '',
  password: '',
  nickname: '',
  phone: '',
  role: '',
  status: 1
}

export default {
  name: 'Admin',
  components: { Pagination },
  data() {
    return {
      tableLoading: false,
      formShow: false,
      total: 0,
      listQuery: {
        page: 1,
        limit: this.$store.state.settings.paginationLimit,
        username: ''
      },
      rules: {
        username: [
          { required: true, message: '请输入账号！', trigger: 'blur' },
          { min: 1, max: 50, message: '输入账号必须在1至50个字符内！', trigger: 'blur' },
          { pattern: /^[A-Za-z0-9\u4e00-\u9fa5_-]+$/, message: '账号只能包含字母、数字、汉字、下划线_及破折号-和@！', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '请输入密码！', trigger: 'blur' },
          { min: 6, max: 50, message: '输入密码必须在6至50个字符内！', trigger: 'blur' },
          { pattern: /^[A-Za-z0-9_-]+$/, message: '输入密码只能包含字母、数字、下划线_及破折号-和@！', trigger: 'blur' }
        ],
        nickname: [
          { required: true, message: '请输入昵称！', trigger: 'blur' },
          { min: 1, max: 50, message: '输入昵称必须在1至50个字符内！', trigger: 'blur' },
          { pattern: /^[A-Za-z0-9\u4e00-\u9fa5_-]+$/, message: '昵称只能包含字母、数字、汉字、下划线_及破折号-和@！', trigger: 'blur' }
        ],
        phone: [
          // { required: true, message: '请输入手机！', trigger: 'change' },
          { pattern: /^1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}$/, message: '请输入正确的手机号码！', trigger: 'blur' }
        ],
        role: [
          { required: true, message: '请选择角色！', trigger: 'change' }
        ]
      },
      roleList: [],
      tableData: [],
      formData: Object.assign({}, formData)
    }
  },
  created() {
    this.getList()
    this.getRoleList()
  },
  methods: {
    indexMethod(index) {
      return index + 1 + ((this.listQuery.page - 1) * this.listQuery.limit)
    },
    getList() {
      this.tableLoading = true
      getAdmin(this.listQuery).then(res => {
        this.tableLoading = false
        this.tableData = res.data.list
        this.total = res.data.total
      }).catch(res => {
        this.tableLoading = false
      })
    },
    getRoleList() {
      getRole().then(res => {
        this.roleList = res.data.list
      }).catch(res => {})
    },
    // 显示表单
    show() {
      // 清除表单校验结果
      setTimeout(() => { this.$refs.Form.clearValidate() }, 0)
      this.formShow = true
      this.rules.password[0].required = true
      this.formData = Object.assign({}, formData)
    },
    edit(row) {
      this.show()
      this.rules.password[0].required = false
      this.formData.title = '编辑管理员'
      this.formData.id = row.id
      this.formData.username = row.username
      this.formData.password = ''
      this.formData.nickname = row.nickname
      this.formData.phone = row.phone
      this.formData.status = row.status
      this.formData.role = parseInt(row.role)
    },
    del(id) {
      if (id === 1) {
        this.$message.error('超级管理员无法被删除！')
        return
      }
      this.$confirm('此操作将删除该管理员, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const data = {
          id: id
        }
        delAdmin(data).then(res => {
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
          saveAdmin(this.formData).then(res => {
            this.$message.success(res.msg)
            this.getList()
          }).catch(() => {})
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped></style>
