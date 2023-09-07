<template>
  <el-container class="container">
    <el-main>
      <el-form ref="Form" :model="formData" :rules="rules" label-width="130px" label-position="right">
        <el-form-item prop="password" label="原密码">
          <el-input v-model="formData.password" type="password" style="width:420px" placeholder="请输入密码" />
        </el-form-item>
        <el-form-item prop="new_password" label="新密码">
          <el-input v-model="formData.new_password" type="password" style="width:420px" placeholder="请输入新密码" />
        </el-form-item>
        <el-form-item prop="confirm_password" label="确认密码">
          <el-input v-model="formData.confirm_password" type="password" style="width:420px" placeholder="请再次输入新密码" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="changePassword">立即修改</el-button>
        </el-form-item>
      </el-form>
    </el-main>
  </el-container>

</template>

<script>

import { changePassword } from '@/api/system'

const formData = {
  password: '',
  new_password: '',
  confirm_password: ''
}

export default {
  name: 'ChangePasswrod',
  data() {
    const validateNewPass = (rule, value, callback) => {
      if (value === this.formData.password) {
        callback(new Error('新密码不能与原密码相同！'))
      } else {
        if (this.formData.confirm_password !== '') {
          this.$refs.Form.validateField('confirm_password')
        }
        callback()
      }
    }
    const validateReNewPass = (rule, value, callback) => {
      if (value !== this.formData.new_password) {
        callback(new Error('两次输入的密码不一致！'))
      } else {
        callback()
      }
    }
    return {
      formData: Object.assign({}, formData),
      rules: {
        password: [
          { required: true, message: '请输入密码！', trigger: 'blur' },
          { min: 1, max: 50, message: '输入密码必须在1至50个字符内！', trigger: 'blur' },
          { pattern: /^[A-Za-z0-9_-]+$/, message: '输入密码只能包含字母、数字、下划线_及破折号-和@！', trigger: 'blur' }
        ],
        new_password: [
          { required: true, message: '请输入新密码！', trigger: 'blur' },
          { min: 6, max: 50, message: '输入新密码必须在6至50个字符内！', trigger: 'blur' },
          { pattern: /^[A-Za-z0-9_-]+$/, message: '输入新密码只能包含字母、数字、下划线_及破折号-和@！', trigger: 'blur' },
          { validator: validateNewPass, trigger: 'change' }
        ],
        confirm_password: [
          { required: true, message: '请再次输入新密码！', trigger: 'blur' },
          { min: 6, max: 50, message: '再次输入新密码必须在6至50个字符内！', trigger: 'blur' },
          { pattern: /^[A-Za-z0-9_-]+$/, message: '再次输入新密码只能包含字母、数字、下划线_及破折号-和@！', trigger: 'blur' },
          { validator: validateReNewPass, trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    changePassword: function() {
      this.$refs.Form.validate(valid => {
        if (valid) {
          changePassword(this.formData).then(res => {
            this.$message.success(res.msg)
            this.logout()
          })
        }
      })
    },
    async logout() {
      await this.$store.dispatch('user/logout')
      this.$router.push(`/login?redirect=${this.$route.fullPath}`)
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
