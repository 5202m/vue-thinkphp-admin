<template lang="pug">
  <div>
    <el-button class="mb-20" type="primary" icon="el-icon-plus" @click="add">添加</el-button>
    <el-table :data="data" border v-loading="loading" :row-class-name="tableRowClassName">
      <el-table-column prop="cat_id" label="ID" align="center" width="100"></el-table-column>
      <el-table-column prop="parent_id" label="PID" align="center" width="100"></el-table-column>
      <el-table-column prop="cat_name" label="分类名称" align="center"></el-table-column>
      <el-table-column prop="is_show" label="是否显示" align="center">
        <template slot-scope="scope">
          <el-tag :type="scope.row.is_show | showFilterType">{{scope.row.is_show | showFilter}}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="show_in_nav" label="导航栏" align="center">
        <template slot-scope="scope">
            <el-tag :type="scope.row.show_in_nav | showFilterType">{{scope.row.show_in_nav | showFilter}}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="sort_order" label="排序" align="center"></el-table-column>
      <el-table-column label="操作" width="300" align="center">
        <template slot-scope="scope">
            <el-button size="mini" type="primary" plain @click="edit(scope.row)">编辑</el-button>
            <el-button size="mini" plain :type="scope.row.is_show | enableShowFilterType" @click="enable(scope.row, 'show')" title="是否显示">{{scope.row.is_show | enableShowFilter}}</el-button>
            <el-button size="mini" plain :type="scope.row.show_in_nav | enableShowFilterType" @click="enable(scope.row, 'nav')" title="是否显示在导航栏">{{scope.row.show_in_nav | enableShowFilter}}</el-button>
            <el-button v-if="scope.row.children && scope.row.expand" size="mini" type="info" plain @click="expand(scope.row,scope.$index)">收起</el-button>
            <el-button v-else-if="scope.row.children && !scope.row.expand" size="mini" type="info" plain @click="expand(scope.row,scope.$index)">展开</el-button>
            <el-button size="mini" type="danger" plain @click="del(scope.row,scope.$index)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog :title="title" :visible.sync="dialogFormVisible" width="600px">
      <el-form v-loading="f_loading" :model="ruleForm" :rules="rules" ref="ruleForm" label-width="130px">
        <el-form-item label="分类名称" prop="cat_name">
          <el-input v-model="ruleForm.cat_name" placeholder="输入分类名称" />
        </el-form-item>
        <el-form-item label="分类别名" prop="cat_alias_name">
          <el-input v-model="ruleForm.cat_alias_name" placeholder="输入分类别名" />
        </el-form-item>
        <el-form-item label="上级分类" prop="parent_id">
          <el-select v-model="ruleForm.parent_id" placeholder="请选择">
            <el-option v-for="item in options" :key="item.cat_id" :label="item.cat_name" :value="item.cat_id" />
          </el-select>
        </el-form-item>
        <!--el-form-item label="头像" prop="status">
          <el-upload class="avatar-uploader" :headers="header" name="file" accept=".jpg,.png,.jpeg" :action="path" :show-file-list="false" :on-success="handleAvatarSuccess" :before-upload="beforeAvatarUpload">
              <img class="avatar" v-if="imageUrl" :src="imageUrl" />
              <i class="el-icon-plus avatar-uploader-icon" v-else />
          </el-upload>
        </el-form-item-->
          <el-form-item label="排序" prop="sort_order">
              <el-input-number v-model="ruleForm.sort_order" :min="0" />
          </el-form-item>
        <!--el-form-item v-if="ruleForm.pid == 0" label="图标">
          <el-input v-model="ruleForm.icon" />
        </el-form-item-->
        <el-form-item label="是否显示">
          <el-switch v-model="ruleForm.is_show" />
        </el-form-item>
        <el-form-item label="是否显示在导航栏">
          <el-switch v-model="ruleForm.show_in_nav" />
        </el-form-item>
        <el-form-item label="关键字" prop="keywords">
          <el-input v-model="ruleForm.keywords" placeholder="输入关键字" />
        </el-form-item>
        <el-form-item label="分类描述" prop="cat_desc">
          <el-input type="textarea" :rows="2" v-model="ruleForm.cat_desc" placeholder="输入分类描述" />
        </el-form-item>
      </el-form>
      <div class="dialog-footer" slot="footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="submit">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import api from '@/api'
import util from '@/utils'
import _ from 'lodash'
export default{
  data () {
    return {
      data: [],
      loading: false,
      f_loading: false,
      dialogFormVisible: false,
      ruleForm: {
        cat_name: '',
        cat_alias_name: '',
        parent_id: null,
        keywords: '',
        cat_desc: '',
        show_in_nav: false,
        is_show: true,
        sort_order: 0
      },
      title: '添加商品分类',
      rules: {
        parent_id: [
          { required: true, message: '请选择上级分类', trigger: 'change' }
        ],
        cat_name: [
          { required: true, message: '请填写分类名称', trigger: 'blur' }
        ]
      },
      options: [{
        cat_id: 0,
        cat_name: '顶级分类'
      }],
      type: 2
    }
  },
  methods: {
    tableRowClassName ({row, rowIndex}) {
      if (row.child) {
        return 'bg'
      } else {
        return ''
      }
    },
    expand (e, index) {
      if (e.children) {
        let children = e.children
        if (e.expand) {
          e.expand = false
          this.data.splice(index + 1, children.length)
        } else {
          let s = this
          _.forEach(children, function (value, key) {
            if (e.parent_id === 0) {
              value.child = true
            } else {
              value.kid = true
            }
            s.data.splice(index + 1 + key, 0, value)
          })
          e.expand = true
        }
      }
    },
    async del (e, index) {
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(async () => {
        let res = await api.category.del(e.cat_id)
        util.response(res, this)
        if (res.code === 200) {
          util.message('操作成功')
          this.data.splice(index, 1)
        } else {
          util.message(res.error, 'error')
        }
      }).catch(() => {
      })
    },
    add () {
      this.dialogFormVisible = true
      this.title = '添加商品分类'
      this.type = 1
      this.$nextTick(() => {
        this.$refs['ruleForm'].resetFields()
        delete this.ruleForm.cat_id
        this.ruleForm.cat_name = ''
        this.ruleForm.parent_id = null
        this.ruleForm.cat_alias_name = ''
        this.ruleForm.keywords = ''
        this.ruleForm.cat_desc = ''
        this.ruleForm.show_in_nav = false
        this.ruleForm.is_show = true
        this.ruleForm.sort_order = 0
      })
    },
    edit (e) {
      this.dialogFormVisible = true
      this.title = '编辑商品分类'
      this.type = 2
      this.ruleForm.cat_id = e.cat_id
      this.ruleForm.cat_name = e.cat_name
      this.ruleForm.parent_id = e.parent_id
      this.ruleForm.cat_alias_name = e.cat_alias_name
      this.ruleForm.keywords = e.keywords
      this.ruleForm.cat_desc = e.cat_desc
      this.ruleForm.show_in_nav = !!e.show_in_nav
      this.ruleForm.is_show = !!e.is_show
      this.ruleForm.sort_order = e.sort_order
    },
    async submit () {
      this.f_loading = true
      this.$refs['ruleForm'].validate(async (valid) => {
        if (valid) {
          let res = []
          if (this.type === 1) {
            res = await api.category.save(this.ruleForm)
          } else {
            res = await api.category.update(this.ruleForm)
          }
          util.response(res, this)
          this.f_loading = false
          if (res.code === 200) {
            this.dialogFormVisible = false
            util.message('操作成功')
            this.getData()
          } else {
            util.message(res.error, 'error')
          }
        } else {
          this.f_loading = false
          return false
        }
      })
    },
    async enable (e, type) {
      let data = {
          'id': e.cat_id,
      }
      if (type == 'show') {
        data.is_show = e.is_show ? 0 : 1
      } else if (type == 'nav') {
        data.show_in_nav = e.show_in_nav ? 0 : 1
      }
      let res = await api.category.enable(data)
      util.response(res, this)
      if (res.code === 200) {
          if (type == 'show') {
            e.is_show = e.is_show ? 0 : 1
          } else if (type == 'nav') {
            e.show_in_nav = e.show_in_nav ? 0 : 1
          }
      } else {
          util.message(res.error, 'error')
      }
    },
    async getData () {
      this.loading = true
      let res = await api.category.index()
      util.response(res, this)
      this.loading = false
      this.data = res.data
      if (this.data == null) {
        this.data = []
      }
      this.getCategory()
    },
    getCategory () {
      this.options = util.cloneDeep(this.data)
      this.options.unshift({cat_id: 0, cat_name: '顶级分类'})
    }
  },
  mounted () {
    this.getData()
  }
}
</script>
<style>
.el-table .bg{
  background: #EDE7F6;
}
</style>
