<template>
  <div class="login-container">
    <div style="width:100%;height:200px;" />
    <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" autocomplete="on" label-position="left">
      <div class="title-container">
        <h3 class="title">欢迎登录-{{ title }}</h3>
      </div>

      <el-form-item prop="username">
        <span class="svg-container">
          <svg-icon icon-class="user" />
        </span>
        <el-input
          ref="username"
          v-model="loginForm.username"
          placeholder="用户账号"
          name="username"
          type="text"
          tabindex="1"
          autocomplete="on"
        />
      </el-form-item>

      <el-tooltip v-model="capsTooltip" content="Caps lock is On" placement="right" manual>
        <el-form-item prop="password">
          <span class="svg-container">
            <svg-icon icon-class="password" />
          </span>
          <el-input
            :key="passwordType"
            ref="password"
            v-model="loginForm.password"
            :type="passwordType"
            placeholder="用户密码"
            name="password"
            tabindex="2"
            autocomplete="on"
            @keyup.native="checkCapslock"
            @blur="capsTooltip = false"
            @keyup.enter.native="handleLogin"
          />
          <span class="show-pwd" @click="showPwd">
            <svg-icon :icon-class="passwordType === 'password' ? 'eye' : 'eye-open'" />
          </span>
        </el-form-item>
      </el-tooltip>
      <div style="text-align:center;">
        <el-button class="form-button" :loading="loading" type="primary" @click.native.prevent="handleLogin">登 录</el-button>
      </div>
    </el-form>
    <div style="text-align:center; color:#ffffff; font-size:18px; margin-top:5%;">
      <div style="width: 70%; border-top: 2px solid white;margin: 0 auto;">
        <p style="font-size: 13px;">建议使用：谷歌、火狐或IE10及以上浏览器</p>
      </div>
    </div>
    <div class="footer">
      <span>{{ company }}有限公司 © 2023</span>
    </div>
  </div>
</template>

<script>

export default {
  name: 'Login',
  data() {
    const validatePassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error('输入的用户密码不能小于6位'))
      } else {
        callback()
      }
    }
    return {
      loginForm: {
        username: 'admin',
        password: 'admin110'
      },
      loginRules: {
        username: [{ required: true, message: '请输入您的账号！', trigger: 'change' }],
        password: [{ required: true, trigger: 'change', validator: validatePassword }]
      },
      title: this.$store.state.settings.title,
      company : this.$store.state.settings.company,
      passwordType: 'password',
      capsTooltip: false,
      loading: false,
      showDialog: false,
      redirect: undefined,
      otherQuery: {}
    }
  },
  watch: {
    $route: {
      handler: function(route) {
        const query = route.query
        if (query) {
          this.redirect = query.redirect
          this.otherQuery = this.getOtherQuery(query)
        }
      },
      immediate: true
    }
  },
  created() {
    // window.addEventListener('storage', this.afterQRScan)
  },
  mounted() {
    if (this.loginForm.username === '') {
      this.$refs.username.focus()
    } else if (this.loginForm.password === '') {
      this.$refs.password.focus()
    }
  },
  destroyed() {
    // window.removeEventListener('storage', this.afterQRScan)
  },
  methods: {
    checkCapslock(e) {
      const { key } = e
      this.capsTooltip = key && key.length === 1 && (key >= 'A' && key <= 'Z')
    },
    showPwd() {
      if (this.passwordType === 'password') {
        this.passwordType = ''
      } else {
        this.passwordType = 'password'
      }
      this.$nextTick(() => {
        this.$refs.password.focus()
      })
    },
    handleLogin() {
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.loading = true
          this.$store.dispatch('user/login', this.loginForm)
            .then(() => {
              this.$router.push({ path: this.redirect || '/', query: this.otherQuery })
              this.loading = false
            })
            .catch(() => {
              this.loading = false
            })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    getOtherQuery(query) {
      return Object.keys(query).reduce((acc, cur) => {
        if (cur !== 'redirect') {
          acc[cur] = query[cur]
        }
        return acc
      }, {})
    }
  }
}
</script>

<style lang="scss">

$bg:#283443;
$light_gray:#999;
$cursor: #fff;

@supports (-webkit-mask: none) and (not (cater-color: $cursor)) {
  .login-container .login-form .el-input input {
    color: $cursor;
  }
}

/* reset element-ui css */
.login-container {
  .login-form {
    .el-input {
      display: inline-block;
      width: 85%;
      input {
        background: transparent;
        border: 0px;
        -webkit-appearance: none;
        border-radius: 10px;
        height: 36px;
        color: $light_gray;
        &:-webkit-autofill {
          box-shadow: 0 0 0px 1000px $cursor inset !important;
          -webkit-text-fill-color: $light_gray !important;
        }
      }
    }

    .el-form-item {
        border: 1px solid #999;
        background: #fff;
        border-radius: 25px;
        color: #141414;
        width: 65%;
        margin: 25px auto;
    }
    .el-form-item__error{
      color: #eeeeee;
      font-size: 12px;
      line-height: 1;
      padding-top: 4px;
      position: absolute;
      top: 100%;
      left: 0;
    }
  }
}
</style>

<style lang="scss" scoped>
$bg:#2d3a4b;
$dark_gray:#889aa4;
$light_gray:#eee;

.login-container {
    background: -webkit-radial-gradient(circle, #0A6AC5 , #23E5BC  50%);
    /* Safari 5.1 - 6.0 */
    background: -o-radial-gradient(circle, #0A6AC5 , #23E5BC  50%);
    /* Opera 11.6 - 12.0 */
    background: -moz-radial-gradient(circle, #0A6AC5 , #23E5BC  50%);
    /* Firefox 3.6 - 15 */
    background: radial-gradient(circle, #0A6AC5 , #23E5BC  50%);
    /*overflow: hidden;*/
    font-family: '微软雅黑',
        serif;
    margin: 0 auto;
    font-size: 9pt;
    height: 100%;
    display: table;
    width: 100%;
  .login-form {
    position: relative;
    width: 500px;
    max-width: 100%;
    padding: 4% 35px 30px;
    margin: 0 auto;
    overflow: hidden;
    // background: url("/eportal/public/static/img/loginbox.png") no-repeat bottom center;
  }
  .img-width{
    width: 550px;
  }
  .tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 10px;

    span {
      &:first-of-type {
        margin-right: 16px;
      }
    }
  }

  .svg-container {
    padding: 0px 5px 0px 15px;
    color: $dark_gray;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
  }

  .title-container {
    position: relative;
    .title {
      font-size: 26px;
      color: $light_gray;
      margin: 0px auto 60px auto;
      text-align: center;
      font-weight: bold;
    }

    .set-language {
      color: #fff;
      position: absolute;
      top: 2px;
      font-size: 18px;
      right: 0px;
      cursor: pointer;
    }
  }
  .el-form-item--medium .el-form-item__content{
    line-height: 28px;
  }
  .show-pwd {
    position: absolute;
    right: 10px;
    top: 1px;
    font-size: 16px;
    color: $dark_gray;
    cursor: pointer;
    user-select: none;
  }

  .thirdparty-button {
    position: absolute;
    right: 0;
    bottom: 6px;
  }

  @media only screen and (max-width: 470px) {
    .thirdparty-button {
      display: none;
    }
  }
  .form-button {
    width: 170px;
    height: 43px;
    border: none;
    background: #74D34B;
    color: #fff;
    font-size: 15px;
    cursor: pointer;
    font-weight: bold;
    border-radius: 30px;
    margin-top: 10px;
  }
  .form-button:hover {
    background: #fff;
    color: #74D34B;
    border: 2px solid #74d348;
  }
  .footer{
    height: 50px;
    line-height: 50px;
    text-align: center;
    position: fixed;
    font-size: 14px;
    bottom: 0;
    width: 100%;
    background-color: #00A98F;
    color: white;
    z-index: 100;
  }
}

</style>
