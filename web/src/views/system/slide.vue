<template>
  <el-container class="container">
    <el-header class="header-box">
      <el-row :gutter="24" justify="space-between">
        <el-col :span="24">
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
        <el-table-column prop="path_url" label="图片" align="center">
          <template slot-scope="{row}">
            <el-image class="el-avatar--small" :src="row.path_url" :preview-src-list="[row.path_url]" />
          </template>
        </el-table-column>
        <el-table-column prop="path_url" label="URl" align="center" />
        <el-table-column prop="sort_id" label="排序(升序)" align="center" />
        <el-table-column prop="play" label="操作" width="160" align="center">
          <template slot-scope="{row}">
            <el-tooltip content="修改" placement="top">
              <el-button class="table-action-button" type="text" @click="edit(row)">
                <svg-icon class="icon" icon-class="edit" />
              </el-button>
            </el-tooltip>
            <el-tooltip content="删除" placement="top">
              <el-button class="table-action-button" type="text">
                <svg-icon class="icon" icon-class="delete" @click="del(row.id)" />
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
          <el-form-item prop="path" label="图片">
            <ul v-if="pathUrl.length > 0" class="el-upload-list--picture-card el-upload-list__item">
              <li class="el-upload-list__item">
                <img class="el-upload-list__item-thumbnail" :src="pathUrl">
              </li>
            </ul>
            <el-upload
              ref="upload"
              class="avatar-uploader"
              list-type="picture-card"
              action=""
              :http-request="saveImg"
              :before-upload="beforeAvatarUpload"
              :show-file-list="false"
            >
              <i class="el-icon-plus" />
            </el-upload>
          </el-form-item>
          <el-form-item label="排序(升序)" prop="sort_id">
            <el-input v-model.number="formData.sort_id" controls-position="right" class="w-90" :min="0" :max1="99999" />
          </el-form-item>
          <el-form-item prop="content" label="描述说明">
            <el-input v-model="formData.content" type="textarea" :rows="5" class="w-90" placeholder="请输入描述说明" />
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
import { getSlide, saveSlide, delSlide } from '@/api/system'
import { saveImg } from '@/api/auth'
import Pagination from '@/components/Pagination' // secondary package based on el-pagination

const formData = {
  title: '添加幻灯片',
  id: 0,
  path: '',
  sort_id: 1,
  content: ''
}

export default {
  name: 'Slide',
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
        sort_id: [
          { required: true, message: '请输入排序！', trigger: 'change' },
          { type: 'number', message: '排序为数字值！' }
        ],
        content: [
          { min: 0, max: 500, message: '输入题库说明必须在0至500个字符内！', trigger: 'blur' }
        ]
      },
      pathUrl: '',
      tableData: [],
      formData: Object.assign({}, formData)
    }
  },
  created() {
    this.getList()
  },
  methods: {
    indexMethod(index) {
      return index + 1 + ((this.listQuery.page - 1) * this.listQuery.limit)
    },
    getList() {
      this.tableLoading = true
      getSlide(this.listQuery).then(res => {
        this.tableLoading = false
        this.tableData = res.data.list
        this.total = res.data.total
      }).catch(res => {
        this.tableLoading = false
      })
    },
    saveImg(data) {
      const file = data.file
      const formData = new FormData()
      formData.append('file', file)
      formData.append('putFile', 'admin')
      saveImg(formData).then(res => {
        this.$message.success(res.msg)
        this.formData.path = res.data.savename
        this.pathUrl = res.data.path_url
      }).catch(() => {
        this.$refs.upload.clearFiles()
      })
    },
    beforeAvatarUpload(file) {
      const isJPG = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/gif'
      const isLt2M = file.size / 1024 / 1024 < 20
      if (!isJPG) {
        this.$message.error('上传图片只能是 JPG、PNG、GIF 格式!')
      }
      if (!isLt2M) {
        this.$message.error('上传图片大小不能超过 20MB!')
      }
      return isJPG && isLt2M
    },
    // 显示表单
    show() {
      // 清除表单校验结果
      setTimeout(() => { this.$refs.Form.clearValidate() }, 0)
      this.formShow = true
      this.pathUrl = ''
      this.formData = Object.assign({}, formData)
    },
    edit(row) {
      this.show()
      this.formData.title = '编辑幻灯片'
      this.formData.id = row.id
      this.formData.path = row.path
      this.formData.sort_id = row.sort_id
      this.formData.content = row.content
      this.pathUrl = row.path_url
    },
    del(id) {
      this.$confirm('此操作将删除该幻灯片, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const data = {
          id: id
        }
        delSlide(data).then(res => {
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
          saveSlide(this.formData).then(res => {
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
  .grid-content {
    border-radius: 4px;
    min-height: 36px;
  }
  .row-bg {
    padding: 10px 0;
    background-color: #f9fafc;
  }
  .bg-purple-right{
    text-align: right;
  }
  .filter-item{
    margin-left: 10px;
  }
  .table-action-button{
    padding: 0;
  }
  .avatar-uploader{
    float: left;
  }
</style>
