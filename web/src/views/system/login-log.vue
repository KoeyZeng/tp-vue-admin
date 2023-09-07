<template>
  <el-container class="container">
    <el-header class="header-box">
      <el-row :gutter="24" justify="space-between">
        <el-col :span="24">
          <div class="grid-content bg-purple">
            <el-input v-model="listQuery.username" style="width: 200px;" placeholder="请输入账号" />
            <el-date-picker v-model="listQuery.rang_date" class="filter-item" type="daterange" :picker-options="picker_pptions" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" />
            <el-button class="filter-item" type="primary" icon="el-icon-search" @click="getList">
              查询
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
        <el-table-column prop="uip" label="IP地址" align="center" />
        <el-table-column prop="type" label="操作" align="center">
          <template slot-scope="{row}">
            <el-tag :type="row.type == '1' ? '': 'info'" effect="plain">
              {{ row.type == '1' ? '登录':'注销' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" align="center">
          <template slot-scope="{row}">
            <el-tag :type="row.status == '1' ? 'success': 'warning'" effect="plain">
              {{ row.status == '1' ? '成功':'失败' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="content" label="描述" align="center" :show-overflow-tooltip="true" />
        <el-table-column prop="create_time" label="时间" align="center" />
      </el-table>
      <!-- 分页控件 -->
      <pagination
        v-show="total>listQuery.limit"
        :total="total"
        :page.sync="listQuery.page"
        :limit.sync="listQuery.limit"
        @pagination="getList"
      />

    </el-main>
  </el-container>
</template>

<script>
import { getLogin } from '@/api/system'
import Pagination from '@/components/Pagination' // secondary package based on el-pagination

export default {
  name: 'LoginLog',
  components: { Pagination },
  data() {
    return {
      tableLoading: false,
      total: 0,
      listQuery: {
        page: 1,
        limit: this.$store.state.settings.paginationLimit,
        username: '',
        rang_date: ''
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
      tableData: []
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
      getLogin(this.listQuery).then(res => {
        this.tableLoading = false
        this.tableData = res.data.list
        this.total = res.data.total
      }).catch(res => {
        this.tableLoading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
