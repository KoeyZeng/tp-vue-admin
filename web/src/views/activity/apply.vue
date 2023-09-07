<template>
  <el-container class="container">
    <el-header class="header-box">
      <el-row :gutter="24" justify="space-between">
        <el-col :span="18">
          <el-input v-model="listQuery.phone" class="filter-item w-200" placeholder="请输入手机" />
          <el-date-picker v-model="listQuery.rang_date" class="filter-item" type="daterange" :picker-options="picker_pptions" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" />
          <el-button class="filter-item" type="primary" icon="el-icon-search" @click="getList">
            查询
          </el-button>
        </el-col>
        <el-col :span="6">
          <div class="grid-content bg-purple-right">
            <el-button class="filter-item ml-10" type="success" icon="el-icon-s-check" @click="audit">
              审核
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
        <el-table-column prop="name" label="姓名" align="center" />
        <el-table-column prop="wechat" label="微信" align="center" />
        <el-table-column prop="address" label="地址" align="center" />
        <el-table-column prop="audit" label="审核" width="60" align="center">
          <template slot-scope="{row}">
            <template v-if="row.audit === 0">
              <el-tooltip class="pageAuditStatus" content="未审核" placement="top">
                <svg-icon icon-class="unpass" style="font-size:18px" />
              </el-tooltip>
            </template>
            <template v-else-if="row.audit === 1">
              <el-tooltip class="pageAuditStatus" content="已审核" placement="top">
                <svg-icon icon-class="broadcast" style="font-size:18px" />
              </el-tooltip>
            </template>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="提交时间(升序)" width="160" align="center" />
        <el-table-column prop="play" label="操作" width="60" align="center">
          <template slot-scope="{row}">
            <el-tooltip content="查看" placement="top">
              <el-button class="table-action-button" type="text" @click="show(row)">
                <svg-icon class="icon" icon-class="eye-open" />
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
        title="查看参加活动"
        :visible.sync="formShow"
        width="1080px"
      >
        <el-form ref="Form" :model="formData" label-width="130px" label-position="right">
          <el-row :gutter="24" justify="space-between">
            <el-col :span="12">
              <el-form-item prop="name" label="姓名">
                <el-input v-model="formData.name" class="w-90" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="sex" label="性别" class="el-form-height">
                <el-radio v-model="formData.sex" :label="0">女</el-radio>
                <el-radio v-model="formData.sex" :label="1">男</el-radio>
                <el-radio v-model="formData.sex" :label="2">保密</el-radio>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="wechat" label="微信">
                <el-input v-model="formData.wechat" class="w-90" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="phone" label="手机" class="el-form-height">
                <el-input v-model="formData.phone" class="w-90">
                  <template slot="prepend">{{ formData.phone_code }}</template>
                </el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="mail" label="邮箱">
                <el-input v-model="formData.mail" class="w-90" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="school" label="学校">
                <el-input v-model="formData.school" class="w-90" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="book" label="录取通知书">
                <!-- <el-input v-model="formData.book" class="w-70" /> -->
                <el-button style="margin-left: 4%" @click="dowload(formData.book)">下载</el-button>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="screen" label="录屏截图">
                <!-- <el-input v-model="formData.screen" class="w-70" /> -->
                <el-button style="margin-left: 4%" @click="dowload(formData.screen)">下载</el-button>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="referer" label="来源">
                <el-input v-model="formData.referer" class="w-90" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="create_time" label="提交时间">
                <el-input v-model="formData.create_time" class="w-90" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item prop="audit" label="审核状态" class="el-form-height">
                <el-radio v-model="formData.audit" :label="0">未审核</el-radio>
                <el-radio v-model="formData.audit" :label="1">已审核</el-radio>
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="审核内容" prop="audit_content">
                <el-input v-model="formData.audit_content" type="textarea" :rows="5" class="w-90" placeholder="请输入审核内容" />
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
      </el-dialog>
      <el-dialog
        title="审核评论"
        :visible.sync="auditShow"
      >
        <el-form ref="auditForm" :model="auditData" :rules="rules" label-width="100px" label-position="right">
          <el-form-item prop="audit" label="审核状态">
            <el-radio v-model="auditData.audit" :label="0">未审核</el-radio>
            <el-radio v-model="auditData.audit" :label="1">已审核</el-radio>
          </el-form-item>
          <el-form-item label="审核内容" prop="audit_content">
            <el-input v-model="auditData.audit_content" type="textarea" :rows="5" class="w-90" placeholder="请输入审核内容" />
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" icon="el-icon-close" @click="auditShow = false">关闭</el-button>
          <el-button type="primary" icon="el-icon-check" :disabled="auditShow === false" @click="saveAudit">保存</el-button>
        </span>
      </el-dialog>
    </el-main>
  </el-container>
</template>

<script>
import { getApply, saveApply, delApply } from '@/api/activity'
import Pagination from '@/components/Pagination' // secondary package based on el-pagination
import { getToken } from '@/utils/auth'

const auditData = { id: [], audit: 0, audit_content: '' }

export default {
  name: 'ACtivityApply',
  components: { Pagination },
  data() {
    return {
      tableLoading: false,
      auditShow: false,
      formShow: false,
      total: 0,
      listQuery: {
        page: 1,
        limit: this.$store.state.settings.paginationLimit,
        phone: '',
        rang_date: ''
      },
      multipleSelection: [],
      rules: {
        audit: [
          { required: true, message: '请选择审核状态!', trigger: 'blur' }
        ],
        audit_content: [
          { required: true, message: '请输入审核内容!', trigger: 'blur' },
          { min: 1, max: 100, message: '输入审核内容必须在1至100个字符内！', trigger: 'blur' }
        ]
      },
      picker_pptions: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
            picker.$emit('pick', [start, end])
          }
        }]
      },
      tableData: [],
      formData: {},
      auditData: {}
    }
  },
  created() {
    this.getList()
  },
  methods: {
    // 加载表格数据
    getList() {
      this.tableLoading = true
      getApply(this.listQuery).then(res => {
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
    audit() {
      if (this.multipleSelection.length === 0) {
        this.$message.error('请选择评论数据！')
        return false
      }
      this.auditShow = true
      this.auditData = Object.assign({}, auditData)
      this.multipleSelection.forEach((elemnt, index) => {
        this.auditData.id[index] = elemnt.id
      })
      setTimeout(() => {
        this.$refs.auditForm.clearValidate()
      }, 0)
    },
    saveAudit() {
      this.$refs['auditForm'].validate((valid) => {
        if (valid) {
          this.auditShow = false
          saveApply(this.auditData).then(res => {
            this.$message.success(res.msg)
            this.getList()
          }).catch(() => {})
        }
      })
    },
    // 显示表单
    show(row) {
      this.formShow = true
      this.formData = row
      this.pathUrl = ''
      // 清除表单校验结果
      setTimeout(() => {
        this.$refs.Form.clearValidate()
      }, 0)
    },
    // 删除参加活动
    del() {
      if (this.multipleSelection.length === 0) {
        this.$message.error('请选择参加活动数据！')
        return false
      }
      const data = { id: [] }
      this.multipleSelection.forEach((elemnt, index) => {
        data.id[index] = elemnt.id
      })
      this.$confirm('此操作将删除该参加活动, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delApply(data).then(res => {
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
          saveApply(this.formData).then(res => {
            this.$message.success(res.msg)
            this.getList()
          }).catch(() => {})
        }
      })
    },
    //  todo 下载附件
    dowload(url) {
      if (url.length === 0) {
        this.$message.error('下载文件内容为空！')
        return false
      }
      var token = getToken()
      window.open(this.$store.getters.host + '/admin/auth/getFile?' + '&file_url=' + url + '&token=' + token)
    }
  }
}
</script>

<style lang="scss" scoped></style>
