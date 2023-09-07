<template>
  <div class="dropdown-img">
    <ul v-if="images.length > 0" class="el-upload-list--picture-card el-upload-list__item">
      <li v-for="(item, index) in images" :key="item.path" class="el-upload-list__item">
        <el-image
          class="el-upload-list__item-thumbnail"
          :src="storage + item.path"
        />
        <span class="el-upload-list__item-actions">
          <span class="el-upload-list__item-preview img-preview">
            <i class="el-icon-zoom-in el-icons" />
            <el-image
              class="el-icon-zoom-img"
              :src="storage + item.path"
              :preview-src-list="[storage + item.path]"
            />
          </span>
          <span class="el-upload-list__item-delete" @click="handleRemove(index)">
            <i class="el-icon-delete" />
          </span>
        </span>
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
  </div>
</template>

<script>
import { saveImg } from '@/api/auth'

export default {
  props: {
    images: {
      type: Array,
      default: function() {
        return []
      }
    },
    lengths: {
      type: Number,
      default: 1
    },
    putFile: {
      type: String,
      default: 'houses'
    }
  },
  data() {
    return {
      storage: this.$store.getters.storage,
      dialogImageUrl: '',
      dialogVisibleImg: false
    }
  },
  methods: {
    // 查看大图
    handlePictureCardPreview(url) {
      this.dialogImageUrl = this.storage + url
      this.dialogVisibleImg = true
    },
    // 删除图片
    handleRemove(index) {
      this.images.splice(index, 1)
    },
    // 保存图片 any
    saveImg(data) {
      const file = data.file
      const formData = new FormData()
      formData.append('file', file)
      // 保存文件夹位置
      formData.append('putFile', this.putFile)
      saveImg(formData).then(res => {
        this.$message.success(res.msg)
        const obj = {
          name: data.file.name,
          path: res.data.path
        }
        console.log(this.images.length, this.lengths)
        if (this.images.length + 1 >= this.lengths) {
          this.images.splice(this.images.length - 1, 1)
        }
        this.images.push(obj)
      }).catch(() => {
        this.$refs.upload.clearFiles()
      })
    },
    // 检验图片
    beforeAvatarUpload(file) {
      const isJPG = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/gif'
      const isLt2M = file.size / 1024 / 1024 < 1
      if (!isJPG) {
        this.$message.error('上传图片只能是 JPG、PNG、GIF 格式!')
      }
      if (!isLt2M) {
        this.$message.error('上传图片大小不能超过1MB!')
      }
      return isJPG && isLt2M
    }
  }
}
</script>

<style lang="scss">
.el-upload-list--picture-card {
  padding-left: 20px !important;
}
.img-preview{
  position: relative;
    width: 20px;
    height: 17px;
  .el-icons{
    left: 0;
    top: 0;
    z-index: 1;
    position: absolute;
  }
  .el-icon-zoom-img{
    left: 0;
    top: 0;
    position: absolute;
    z-index: 100;
    width: 20px;
    height: 20px;
    .el-image__inner {
      opacity: 0;
    }
  }
}

</style>
