<template>
  <div>
    <div class="toolbar">
      <el-button size="small" style="margin-right: 10px;" @click="openCreateOrUpdate(undefined)">
        <el-icon size="small" style="vertical-align: middle;">
          <Plus/>
        </el-icon>
        <span style="vertical-align: middle">添加</span>
      </el-button>
      <el-button type="primary" size="small" :loading="state.tableLoading" style="margin-right: 10px;" @click="getData">
        <el-icon size="small" style="vertical-align: middle;">
          <Refresh/>
        </el-icon>
        <span style="vertical-align: middle">刷新</span>
        刷新
      </el-button>
    </div>
    <div class="content">
      <el-table :data="state.tableData" v-loading="state.tableLoading" style="width: 100%;margin-bottom: 20px;">
        <el-table-column prop="id" label="ID" width="180"/>
        <el-table-column prop="name" label="名称" width="180"/>
        <el-table-column prop="status" label="状态" width="180">
          <template #default="scope">
            <el-switch @change="(val) => handleRoleStatusChange(val, scope.row.id)" v-model="scope.row.status"
                       style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949" :active-value="1"
                       :inactive-value="0"/>
          </template>
        </el-table-column>
        <el-table-column prop="sort" label="排序" width="180"/>
        <el-table-column prop="desc" label="描述" width="180"/>
        <el-table-column prop="createdAt" label="创建时间" width="180"/>
        <el-table-column prop="updatedAt" label="更新时间" width="180"/>
        <el-table-column label="操作" width="230">
          <template #default="scope">
            <el-button size="small" @click="openCreateOrUpdate(scope.$index)">编辑</el-button>
            <el-button size="small" type="success" @click="openSetRule(scope.$index)">设置权限</el-button>
            <el-button size="small" type="danger" @click="handleDelete([scope.row.id])">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div style="width:100%;">
        <el-pagination style="margin-left: 20px;" background layout="prev, pager, next" :total="state.total"/>
      </div>
      <el-dialog v-model="state.formDialogVisible" :title="state.isEdit ? '编辑角色' : '新增角色'"
                 width="800px">
        <el-form ref="ruleFormRef" style="width:80%;margin: 0 auto;" :model="state.roleForm" :rules="state.rules"
                 label-width="83px">
          <el-form-item required label="名称:" prop="name">
            <el-input v-model="state.roleForm.name"/>
          </el-form-item>
          <el-form-item label="描述:">
            <el-input type="textarea" v-model="state.roleForm.desc"/>
          </el-form-item>
          <el-form-item label="排序:">
            <el-input v-model="state.roleForm.sort"/>
          </el-form-item>
          <el-form-item required label="是否启用:">
            <el-switch v-model="state.roleForm.status" active-color="#13ce66" inactive-color="#ff4949" :active-value="1"
                       :inactive-value="0">
            </el-switch>
          </el-form-item>
          <el-form-item>
            <el-button style="width: 100%;margin: 0 auto;" type="primary" :loading="state.submitLoading"
                       @click="roleSubmit">提交
            </el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
      <el-dialog v-model="state.setRuleDialogVisible" title="设置权限" width="800px">
        <el-form v-loading="state.setRuleDialogLoading" ref="setRuleFormRef"
                 style="width:80%;margin: 0 auto;min-height: 600px;" label-width="83px">
          <el-form-item label="设置权限:">
            <el-tree
                ref="treeRef"
                :data="state.ruleTree"
                show-checkbox
                node-key="id"
                highlight-current
                :props="defaultProps"
                :render-after-expand=false
                :default-expanded-keys="state.checkedNode"
                @check-change="handleTreeCheckChange"
            />
          </el-form-item>
        </el-form>
        <template #footer>
            <span class="dialog-footer">
              <el-button style="width: 100%;margin: 0 auto;" type="primary" :loading="state.setRuleLoading"
                         @click="setRuleSubmit">提交
            </el-button>
            </span>
        </template>
      </el-dialog>
    </div>
  </div>
</template>

<script lang="ts" setup>
import {roleList, upRoleStatus, editRole, createRole, deleteRole, setRule} from '@/api/role'
import {ruleList, roleRuleTree} from '@/api/rule'
import {nextTick, onMounted, reactive, ref, toRefs} from 'vue'
import {ElMessage, ElMessageBox, FormInstance, FormRules} from 'element-plus'
import type Node from 'element-plus/es/components/tree/src/model/node'

interface Tree {
  id: number
  label: string
  children?: Tree[]
}

interface RoleForm {
  id: number,
  name: string,
  desc: string,
  sort: number,
  status: number,
}

const defaultProps = {
  children: 'children',
  label: 'name',
}

const ruleFormRef = ref<FormInstance>()
const treeRef = ref()

const state = reactive({
  total: 0,
  page: 1,
  pageSize: 15,
  tableLoading: false,
  formDialogVisible: false,
  submitLoading: false,
  isEdit: false,
  tableData: [],
  roleForm: <RoleForm>{
    id: 0,
    name: '',
    desc: '',
    sort: 1,
    status: 0,
  },
  rules: <FormRules>{
    name: [
      {required: true, message: '请输入角色名称', trigger: 'blur'},
    ],
  },
  ruleTree: <Tree>[],
  roleId: 0,
  checkedNode: [],
  setRuleDialogVisible: false,
  setRuleDialogLoading: false,
  setRuleLoading: false,
})

const handleTreeCheckChange = (
    data: Tree,
    checked: boolean,
    indeterminate: boolean
) => {
  state.checkedNode = treeRef.value!.getCheckedNodes(false, true)
}

const setRuleSubmit = () => {
  state.setRuleDialogVisible = true
  const checkedNodes = treeRef.value!.getCheckedNodes(false, true)
  let ruleIds = []
  checkedNodes.forEach(node => {
    ruleIds.push(node.id)
  })
  setRule({
    ruleIds: ruleIds,
    roleId: state.roleId
  }).then(resp => {
    ElMessage({
      message: resp.msg || '',
      type: 'success',
    })
    state.setRuleDialogVisible = false
  }).catch(() => {
    state.setRuleDialogVisible = false
  })
}

const initRuleTree = async () => {
  await ruleList().then(async resp => {
    state.ruleTree = resp.data
  })
}

const handleDelete = (ids: Array<number>) => {
  ElMessageBox.confirm('你确定要删除吗?', '提示',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }
  ).then(() => {
    deleteRole({ids: ids}).then(() => {
      getData()
    })
  })
}

const handleRoleStatusChange = (val, id) => {
  upRoleStatus({ids: [id], status: val})
}

const roleSubmit = async () => {
  if (ruleFormRef.value) {
    await ruleFormRef.value.validate((valid, fields) => {
      if (valid) {
        state.submitLoading = true
        if (state.isEdit) {
          editRole(state.roleForm).then(() => {
            state.formDialogVisible = false
            getData()
          }).finally(() => {
            state.submitLoading = false
          })
        } else {
          createRole(state.roleForm).then(() => {
            state.formDialogVisible = false
            getData()
          }).finally(() => {
            state.submitLoading = false
          })
        }
      }
    })
  }
}

const getData = () => {
  state.tableLoading = true
  roleList({
    page: state.page,
    pageSize: state.pageSize,
  }).then(resp => {
    state.tableData = resp.data.list
    state.total = resp.data.total
  }).finally(() => {
    state.tableLoading = false
  })
}

const resetForm = async () => {
  if (!ruleFormRef.value) return
  ruleFormRef.value.resetFields()
}

const initingAdminForm = async (index: number | undefined) => {
  if (index !== undefined) {
    const data = state.tableData[index]
    state.roleForm = <RoleForm>{
      id: data.id,
      name: data.name,
      desc: data.desc,
      sort: data.sort,
      status: data.status,
    }
  } else {
    state.roleForm = <RoleForm>{
      id: 0,
      name: '',
      desc: '',
      sort: 1,
      status: 0,
    }
  }
}

const openCreateOrUpdate = async (index: number | undefined) => {
  state.isEdit = index !== undefined
  state.formDialogVisible = true
  await initingAdminForm(index)
  if (index === undefined) await resetForm()
}

const openSetRule = async (index: number | undefined) => {
  state.setRuleDialogVisible = true
  state.setRuleDialogLoading = true
  const data = state.tableData[index]
  state.roleId = data.id
  await initRuleTree()
  await initRoleRuleTree(data.id)
  state.setRuleDialogLoading = false
}

//递归的方式设置选中的节点
const recursiveSetNodeCheck = async (nodeList: Node[]) => {
  for (const node of nodeList) {
    treeRef.value!.setChecked(node, true, false)
    if (node.children !== undefined) {
      await recursiveSetNodeCheck(node.children)
    }
  }
}

const initRoleRuleTree = async (roleId: number | undefined) => {
  await roleRuleTree(roleId).then(async resp => {
    await nextTick()
    const data = resp.data as Node[]
    await recursiveSetNodeCheck(data)
  })
}

onMounted(() => {
  getData()
})

</script>

<style lang="scss" scoped>
.role-container {
  margin: 20px;
  padding: 20px;
  background-color: #fff;
}

.toolbar {
  padding: 10px;
  width: 100%;
  background-color: #fff;
  border-radius: 5px;
}

.content {
  width: 100%;
  background-color: #fff;
  margin-top: 20px;
  padding: 10px 0;
}
</style>
