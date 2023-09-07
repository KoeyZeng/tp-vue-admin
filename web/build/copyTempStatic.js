require('shelljs/global')

// 将打包后的文件复制到 php server 资源目录

// 强制递归删除`/server/public/admin/目录`
rm('-rf', '../server/public/admin')

// 复制” server/public/admin“ 文件夹下的内容
cp('-R', './dist/', '../server/public/admin')
