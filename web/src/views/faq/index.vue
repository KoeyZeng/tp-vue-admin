<template>
  <el-container class="container">
    <el-header class="header-box">
      <el-row :gutter="24" justify="space-between">
        <el-col :span="16">
          <el-input v-model="listQuery.title" class="filter-item w-200" placeholder="请输入问题标题" />
          <el-button class="filter-item" type="primary" icon="el-icon-search" @click="getList">
            查询
          </el-button>
        </el-col>
        <el-col :span="8">
          <div class="grid-content bg-purple-right">
            <el-button class="filter-item ml-10" type="primary" icon="el-icon-plus" @click="show">
              添加
            </el-button>
            <el-button class="filter-item ml-10" type="danger" icon="el-icon-delete" @click="del">
              删除
            </el-button>
          </div>
        </el-col>
      </el-row>
    </el-header>
    <el-main>
      <!-- 表格控件 -->
      <el-table
        v-loading="tableLoading"
        :data="tableData"
        stripe
        class="w-100"
        row-key="id"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="60" align="center" />
        <el-table-column prop="id" label="ID" width="60" align="center" />
        <el-table-column prop="title" label="标题" align="center" />
        <el-table-column prop="update_time" label="发布时间(升序)" width="160" align="center" />
        <el-table-column prop="play" label="操作" width="60" align="center">
          <template slot-scope="{row}">
            <el-tooltip content="修改" placement="top">
              <el-button class="table-action-button" type="text" @click="edit(row)">
                <svg-icon class="icon" icon-class="edit" />
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
        :title="formData.formTitle"
        :visible.sync="formShow"
        width="1080px"
      >
        <el-form ref="Form" :model="formData" :rules="rules" label-width="100px" label-position="right">
          <el-row :gutter="24" justify="space-between">
            <el-col :span="12">
              <el-form-item label="问题标题" prop="title">
                <el-input v-model="formData.title" class="w-90" placeholder="请输入问题标题" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="update_time" label="发布时间">
                <el-date-picker v-model="formData.update_time" type="datetime" class="w-90" placeholder="请选择发布时间" />
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item prop="content" label="问题内容">
                <tinymce v-model="formData.content" :height="280" />
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
import { getIndex, saveIndex, delIndex } from '@/api/faq'
import Tinymce from '@/components/Tinymce/article'
import Pagination from '@/components/Pagination' // secondary package based on el-pagination

const formData = {
  formTitle: '添加问题',
  id: 0,
  title: '',
  des: '',
  content: '',
  views: 1000,
  update_time: new Date()
}
export default {
  name: 'Index',
  components: { Pagination, Tinymce },
  data() {
    return {
      tableLoading: false,
      formShow: false,
      total: 0,
      listQuery: {
        page: 1,
        limit: this.$store.state.settings.paginationLimit,
        title: ''
      },
      multipleSelection: [],
      rules: {
        title: [
          { required: true, message: '请输入问题标题！', trigger: 'blur' },
          { min: 1, max: 100, message: '输入问题标题必须在1至100个字符内！', trigger: 'blur' }
        ],
        views: [
          { type: 'number', message: '浏览次数必须为数字值！' }
        ],
        update_time: [
          { required: true, message: '请输入发布时间！', trigger: 'change' }
        ],
        content: [
          { required: true, message: '请输入问题内容！', trigger: 'blur' }
        ]
      },
      tableData: [],
      formData: Object.assign({}, formData)
    }
  },
  created() {
    this.getList()
  },
  methods: {
    // 加载表格数据
    getList() {
      this.tableLoading = true
      getIndex(this.listQuery).then(res => {
        this.tableLoading = false
        this.tableData = res.data.list
        this.total = res.data.total
      }).catch(res => {
        this.tableLoading = false
      })
    },
    // 选中事件
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    // 显示表单
    show(row = {}) {
      this.formShow = true
      this.formData = Object.assign({}, formData)
      // 清除表单校验结果
      setTimeout(() => {
        this.$refs.Form.clearValidate()
      }, 0)
    },
    // 编辑问题
    edit(row) {
      this.show(row)
      this.formData = Object.assign({}, row)
      this.formData.formTitle = '编辑问题'
    },
    // 删除问题
    del() {
      if (this.multipleSelection.length === 0) {
        this.$message.error('请选择问题数据！')
        return false
      }
      const data = { id: [] }
      this.multipleSelection.forEach((elemnt, index) => {
        data.id[index] = elemnt.id
      })
      this.$confirm('此操作将删除该问题, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delIndex(data).then(res => {
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
          saveIndex(this.formData).then(res => {
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
