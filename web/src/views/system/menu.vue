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
      <el-table
        v-loading="tableLoading"
        :data="tableData"
        row-key="name"
        stripe
        :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
        style="width:100%"
      >
        <el-table-column prop="id" label="ID" width="100" align="center" />
        <el-table-column prop="name" label="菜单名称" align="left" />
        <el-table-column prop="label" label="标签名称" align="center" />
        <el-table-column prop="path" label="路由" align="center" />
        <el-table-column prop="component" label="组件路径" align="center" />
        <el-table-column prop="sort_id" label="排序(升序)" width="100" align="center" />
        <el-table-column prop="icon" label="图标" width="60" align="center">
          <template slot-scope="{row}">
            <i :class="'el-icon-' + row.icon" style="font-size:20px" />
          </template>
        </el-table-column>
        <el-table-column prop="hidden" label="显示" width="60" align="center">
          <template slot-scope="{row}">
            <el-tooltip class="programPublish" :content="row.hidden === 1?'隐藏':'显示' " placement="top">
              <el-button class="table-action-button" type="text">
                <svg-icon class="icon" :icon-class="row.hidden === 1?'suspend':'broadcast'" />
              </el-button>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column prop="play" label="操作" width="100" align="center">
          <template slot-scope="{row}">
            <el-tooltip content="修改" placement="top">
              <el-button class="table-action-button" type="text" @click="edit(row)">
                <svg-icon class="icon" icon-class="edit" />
              </el-button>
            </el-tooltip>
            <el-tooltip content="删除" placement="top">
              <el-button class="table-action-button" type="text" @click="del(row.id)">
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
          <el-form-item prop="pid" label="上级菜单">
            <el-cascader v-model="formData.pid_arr" :options="superiorMenus" :props="menuListProps" class="w-90" />
          </el-form-item>
          <el-form-item prop="name" label="菜单名称">
            <el-input v-model="formData.name" class="w-90" placeholder="请输入菜单名称" />
          </el-form-item>
          <el-form-item prop="label" label="标签名称">
            <el-input v-model="formData.label" class="w-90" placeholder="请输入标签名称" />
          </el-form-item>
          <el-form-item prop="path" label="路由">
            <el-input v-model="formData.path" class="w-90" placeholder="请输入路由" />
          </el-form-item>
          <el-form-item prop="component" label="组件路径">
            <el-input v-model="formData.component" class="w-90" placeholder="请输入组件路径">
              <template slot="prepend">/ src / views /</template>
            </el-input>
          </el-form-item>
          <el-form-item prop="icon" label="图标">
            <el-row class="w-90">
              <el-col :span="6"><div><i :class="'el-icon-' + formData.icon" /></div></el-col>
              <el-col :span="18">
                <el-select v-model="formData.icon" filterable placeholder="请选择图标" style="width:100%">
                  <el-option
                    v-for="(item, i) in iconList"
                    :key="i"
                    :value="item"
                  >
                    <i style="float: left" :class="'el-icon-' + item" />
                    <span style="float: right; color: #8492a6; font-size: 13px">{{ item }}</span>
                  </el-option>
                </el-select>
              </el-col>
            </el-row>
          </el-form-item>
          <el-form-item label="排序(升序)" prop="sort_id">
            <el-input-number v-model="formData.sort_id" controls-position="right" class="w-90" :min="0" :max="99999" />
          </el-form-item>
          <el-form-item prop="hidden" label="隐藏菜单">
            <el-switch v-model="formData.hidden" :active-value="1" :inactive-value="0" />
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
import { getMenu, saveMenu, delMenu } from '@/api/system'
import elementIcons from '@/utils/element-icons'

const formData = {
  title: '添加菜单',
  id: 0,
  name: '',
  label: '',
  pid: [0],
  path: '',
  component: '',
  icon: '',
  sort_id: 1000,
  hidden: 0
}
const superiorMenus = [{ id: 0, name: '顶级菜单' }]

export default {
  name: 'Menu',
  data() {
    return {
      tableLoading: false,
      formShow: false,
      menuListProps: {
        value: 'id',
        label: 'name',
        children: 'children',
        checkStrictly: true,
        expandTrigger: 'hover'
      },
      superiorMenus: [],
      rules: {
        pid: [
          { required: true, message: '请选择上级菜单', trigger: 'blur' }
        ],
        name: [
          { required: true, message: '请输入菜单名称！', trigger: 'blur' },
          { min: 1, max: 50, message: '输入菜单名称必须在1至50个字符内！', trigger: 'blur' }
        ],
        label: [
          { required: true, message: '请输入标签名称！', trigger: 'blur' },
          { min: 1, max: 50, message: '输入标签名称必须在1至50个字符内！', trigger: 'blur' }
        ],
        path: [
          { required: true, message: '请输入路由！', trigger: 'blur' },
          { min: 1, max: 50, message: '输入路由必须在1至50个字符内！', trigger: 'blur' }
        ],
        component: [
          { min: 1, max: 50, message: '输入组件路径必须在1至50个字符内！', trigger: 'blur' }
        ]
      },
      iconList: elementIcons,
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
      getMenu().then(res => {
        this.superiorMenus = []
        this.tableLoading = false
        this.tableData = res.data.list
        this.superiorMenus = Object.assign([], superiorMenus).concat(this.tableData)
      }).catch(res => {
        this.tableLoading = false
      })
    },
    // 显示表单
    show() {
      // 清除表单校验结果
      setTimeout(() => { this.$refs.Form.clearValidate() }, 0)
      this.formShow = true
      this.formData = Object.assign({}, formData)
      this.formData.pid_arr = this.formData.pid
    },
    edit(row) {
      this.show()
      this.formData.title = '编辑菜单'
      this.formData = Object.assign({}, row)
      this.formData.pid_arr = row.pid_arr
      delete this.formData.children
    },
    del(id) {
      this.$confirm('此操作将删除该菜单, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const data = {
          id: id
        }
        delMenu(data).then(res => {
          this.$message.success(res.msg)
          // 重新加载页面数据
          this.getList()
        }).catch(() => {})
      }).catch(() => {})
    },
    // 保存表单
    save() {
      typeof this.formData.pid_arr === 'object' &&
            (this.formData.pid = this.formData.pid_arr.length > 0 ? this.formData.pid_arr[this.formData.pid_arr.length - 1] : 0)
      delete this.formData.pid_arr
      this.$refs['Form'].validate((valid) => {
        if (valid) {
          this.formShow = false
          saveMenu(this.formData).then(res => {
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
