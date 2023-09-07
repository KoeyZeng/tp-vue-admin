<template>
  <tinymce :id="id" v-model="data" :other_options="config" />
</template>

<script>
import tinymce from 'vue-tinymce-editor'

export default {
  components: {
    tinymce: tinymce
  },
  props: {
    value: {
      type: String,
      default: ''
    },
    height: {
      type: [Number, String],
      required: false,
      default: 150
    }
  },
  data() {
    return {
      data: this.value,
      id: 'tinymce' + Math.ceil(Math.random() * 100),
      config: {
        height: this.height + 'px',
        width: '95%',
        language: 'zh_CN',
        convert_urls: false,
        language_url: this.$store.getters.host + '/static/admin/js/tinymce/lang/zh_CN.js',
        automatic_uploads: true,
        images_upload_handler: (blobInfo, success, failure) => {
          // 图片异步上传处理函数
          var xhr, formData
          xhr = new XMLHttpRequest()
          xhr.withCredentials = false
          xhr.open('POST', this.$store.getters.host + '/admin/auth/saveFile')
          xhr.setRequestHeader('Authorization', 'Bearer' + this.$store.getters.token)
          xhr.onload = function() {
            var json = ''
            if (xhr.status !== 200) {
              failure('HTTP Error: ' + xhr.status)
              return
            }

            json = JSON.parse(xhr.responseText)
            console.log(json)
            if (!json || typeof json.data.path_url !== 'string') {
              failure('Invalid JSON: ' + xhr.responseText.msg)
              return
            }
            success(json.data.path_url)
          }

          formData = new FormData()
          formData.append('file', blobInfo.blob(), blobInfo.filename())
          formData.append('putFile', 'tinymce')

          xhr.send(formData)
        }
      }
    }
  },
  watch: {
    value(newVal, oldVal) {
      this.data = newVal
    },
    data(newVal, oldVal) {
      this.$emit('input', newVal)
    }
  }
}
</script>
