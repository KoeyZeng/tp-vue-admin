<template>
  <el-container class="container">
    <el-header class="header-box">
      <el-row :gutter="24" justify="space-between">
        <el-col :span="16">
          <el-input v-model="listQuery.title" class="filter-item w-200" placeholder="请输入活动标题" />
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
        <el-table-column prop="time" label="活动时间" width="200" align="center">
          <template slot-scope="{row}">
            <div v-text="row.start_time + ' 至 ' + row.end_time" />
          </template>
        </el-table-column>
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
              <el-form-item label="活动标题" prop="title">
                <el-input v-model="formData.title" class="w-90" placeholder="请输入活动标题" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="range_date" label="活动时间">
                <el-date-picker v-model="formData.range_date" value-format="yyyy-MM-dd" type="daterange" class="w-90" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" />
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="24" justify="space-between">
            <el-col :span="12">
              <el-form-item prop="path" label="封面图片">
                <upload-img :images="pathArr" :lengths="1" :put-file="'activity'" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="update_time" label="发布时间">
                <el-date-picker v-model="formData.update_time" type="datetime" class="w-90" placeholder="请选择发布时间" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="已报名数" prop="views">
                <el-input-number v-model="formData.views" controls-position="right" class="w-90" :min="0" :max="99999" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="address" label="活动地址">
                <el-input v-model="formData.address" class="w-90" placeholder="请输入address" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="alt" label="图片ALT">
                <el-input v-model="formData.alt" class="w-90" placeholder="请输入图片ALT" />
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item prop="des" label="活动简介">
                <el-input v-model="formData.des" type="textarea" :rows="6" class="w-90" placeholder="请输入活动简介" />
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item prop="content" label="活动内容">
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
import { getActivity, saveActivity, delActivity } from '@/api/activity'
import UploadImg from '@/components/UploadImg'
import Tinymce from '@/components/Tinymce/article'
import Pagination from '@/components/Pagination' // secondary package based on el-pagination

const formData = {
  formTitle: '添加活动',
  id: 0,
  title: '',
  path: '',
  des: '',
  content: '',
  views: 1000,
  address: '',
  range_date: [],
  start_time: new Date(),
  end_time: new Date(),
  update_time: new Date()
}
export default {
  name: 'Activity',
  components: { Pagination, Tinymce, UploadImg },
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
      pathArr: [],
      multipleSelection: [],
      rules: {
        title: [
          { required: true, message: '请输入活动标题！', trigger: 'blur' },
          { min: 1, max: 100, message: '输入活动标题必须在1至100个字符内！', trigger: 'blur' }
        ],
        views: [
          { type: 'number', message: '已报名人数必须为数字值！' }
        ],
        range_date: [
          { type: 'array', required: true, message: '请选择活动时间', trigger: 'blur' }
        ],
        update_time: [
          { required: true, message: '请选择发布时间！', trigger: 'blur' }
        ],
        pathArr: [
          { required: true, message: '请上传封面图片！', trigger: 'change' }
        ],
        alt: [
          { min: 0, max: 250, message: '输入图片ALT必须在0至250个字符内！', trigger: 'blur' }
        ],
        des: [
          { required: true, message: '请输入活动简介！', trigger: 'blur' },
          { min: 1, max: 250, message: '输入文活动简介必须在1至250个字符内！', trigger: 'blur' }
        ],
        address: [
          { required: true, message: '请输入活动地址！', trigger: 'blur' },
          { min: 1, max: 250, message: '输入文活地址介必须在1至250个字符内！', trigger: 'blur' }
        ],
        content: [
          { required: true, message: '请输入活动内容！', trigger: 'blur' }
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
      getActivity(this.listQuery).then(res => {
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
      this.pathArr = []
      this.formData.range_date = [formData.start_time, formData.end_time]
      // 清除表单校验结果
      setTimeout(() => {
        this.$refs.Form.clearValidate()
      }, 0)
    },
    // 编辑活动
    edit(row) {
      this.show(row)
      row.range_date = [new Date(row.start_time), new Date(row.end_time)]
      this.formData = Object.assign({}, row)
      this.pathArr = JSON.parse(this.formData.path)
      delete this.formData.path_url
      this.formData.formTitle = '编辑活动'
    },
    // 删除活动
    del() {
      if (this.multipleSelection.length === 0) {
        this.$message.error('请选择活动数据！')
        return false
      }
      const data = { id: [] }
      this.multipleSelection.forEach((elemnt, index) => {
        data.id[index] = elemnt.id
      })
      this.$confirm('此操作将删除该活动, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delActivity(data).then(res => {
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
          this.formData.start_time = this.formData.range_date[0]
          this.formData.end_time = this.formData.range_date[1]
          delete this.formData.range_date
          this.formData.path = JSON.stringify(this.pathArr)
          saveActivity(this.formData).then(res => {
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
