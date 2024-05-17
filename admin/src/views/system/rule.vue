<template>
  <div>
    <div class="toolbar">
      <el-button @click="openCreateOrUpdateDialog(undefined)">
        <el-icon style="vertical-align: middle;">
          <Plus/>
        </el-icon>
        <span style="vertical-align: middle">添加</span>
      </el-button>
      <el-button type="primary" :loading="state.tableLoading" style="margin-right: 10px;" @click="getData">
        <el-icon style="vertical-align: middle;">
          <Refresh/>
        </el-icon>
        <span style="vertical-align: middle">刷新</span>
      </el-button>
    </div>
    <div class="content">
      <el-table
        border
        :data="state.tableData" :row-class-name="state.colorStyle ? tableRowClassName : ''"
        row-key="id" v-loading="state.tableLoading" style="width: 100%;"
        :header-cell-style="{'text-align':'center'}"
      >
        <el-table-column prop="name" label="名称" width="300">
          <template #default="scope">
            <span v-if="scope.row.type === 1 || scope.row.type === 2">{{ scope.row.name }}</span>
            <span v-if="scope.row.type === 3"><el-icon><Pointer/></el-icon> {{ scope.row.name }}</span>
            <span v-if="scope.row.type === 4"><el-icon><Switch/></el-icon> {{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="90" align="center">
          <template #default="scope">
            <el-switch
              @change="(val:number) => handleRoleStatusChange(val, scope.row.id)" v-model="scope.row.status"
              style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949" :active-value="1"
              :inactive-value="0"/>
          </template>
        </el-table-column>
        <el-table-column prop="type" label="类型" width="100" align="center">
          <template #default="scope">
            <el-tag v-if="scope.row.type === 1" type="success">{{ scope.row.typeZh }}</el-tag>
            <el-tag v-if="scope.row.type === 2" type="warning">{{ scope.row.typeZh }}</el-tag>
            <el-tag v-if="scope.row.type === 3" type="info">{{ scope.row.typeZh }}</el-tag>
            <el-tag v-if="scope.row.type === 4" type="danger">{{ scope.row.typeZh }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="route" label="后端路由"/>
        <el-table-column prop="path" label="前端路由" width="180"/>
        <el-table-column prop="sort" label="排序" width="150" align="center"/>
        <el-table-column label="操作" width="200">
          <template #default="scope">
            <el-button type="primary" @click="openCreateOrUpdateDialog(scope.$index)" :disabled="scope.row.type === 4">
              编辑
            </el-button>
            <el-button type="danger" @click="handleDelete([scope.row.id])">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-dialog style="text-align: center;" v-model="state.formDialogVisible"
                 :title="state.isEdit ? '编辑权限' : '新增权限'"
                 width="30%">
        <add-rule v-if="!state.isEdit"></add-rule>
<!--        <el-tabs v-model="state.formActiveName" type="card" @tab-click="handleFormTabsClick">-->
<!--          <el-tab-pane :disabled="state.directoryTabDisabled" label="目录权限" name="directory">-->
<!--            <el-form ref="ruleFormRefDirectory" style="width:80%;margin: 0 auto;" :model="state.ruleForm"-->
<!--                     :rules="state.rules" label-width="83px">-->
<!--              <el-form-item required label="名称:" prop="name">-->
<!--                <el-input v-model="state.ruleForm.name"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item label="前端路由:" prop="path">-->
<!--                <el-input v-model="state.ruleForm.path"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item label="图标:" prop="icon">-->
<!--                <el-input v-model="state.ruleForm.icon"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item label="排序:">-->
<!--                <el-input v-model="state.ruleForm.sort"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item required label="是否启用:">-->
<!--                <el-switch v-model="state.ruleForm.status" active-color="#13ce66" inactive-color="#ff4949"-->
<!--                           :active-value="1" :inactive-value="0">-->
<!--                </el-switch>-->
<!--              </el-form-item>-->
<!--              <el-form-item>-->
<!--                <el-button style="width: 100%;margin: 0 auto;" type="primary" :loading="state.submitLoading"-->
<!--                           @click="ruleSubmit(ruleFormRefDirectory)">提交-->
<!--                </el-button>-->
<!--              </el-form-item>-->
<!--            </el-form>-->
<!--          </el-tab-pane>-->
<!--          <el-tab-pane :disabled="state.menuTabDisabled" label="菜单权限" name="menu">-->
<!--            <el-form ref="ruleFormRefMenu" style="width:80%;margin: 0 auto;" :model="state.ruleForm"-->
<!--                     :rules="state.rules"-->
<!--                     label-width="83px">-->
<!--              <el-form-item required label="父级:" prop="parentId">-->
<!--                <el-select v-model="state.ruleForm.parentId" style="width:100%;" placeholder="请选择父级">-->
<!--                  <el-option :key="0" label="" :value="0"/>-->
<!--                  <el-option v-for="rule in state.topRule" :key="rule.id" :label="rule.name" :value="rule.id"/>-->
<!--                </el-select>-->
<!--              </el-form-item>-->
<!--              <el-form-item required label="名称:" prop="name">-->
<!--                <el-input v-model="state.ruleForm.name"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item label="前端路由:" prop="path">-->
<!--                <el-input v-model="state.ruleForm.path"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item label="图标:" prop="icon">-->
<!--                <el-input v-model="state.ruleForm.icon"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item label="排序:">-->
<!--                <el-input v-model="state.ruleForm.sort"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item required label="是否启用:">-->
<!--                <el-switch v-model="state.ruleForm.status" active-color="#13ce66" inactive-color="#ff4949"-->
<!--                           :active-value="1" :inactive-value="0">-->
<!--                </el-switch>-->
<!--              </el-form-item>-->
<!--              <el-form-item>-->
<!--                <el-button style="width: 100%;margin: 0 auto;" type="primary" :loading="state.submitLoading"-->
<!--                           @click="ruleSubmit(ruleFormRefMenu)">提交-->
<!--                </el-button>-->
<!--              </el-form-item>-->
<!--            </el-form>-->
<!--          </el-tab-pane>-->
<!--          <el-tab-pane :disabled="state.buttonTabDisabled" label="按钮权限" name="button">-->
<!--            <el-form ref="ruleFormRefButton" style="width:80%;margin: 0 auto;" :model="state.ruleForm"-->
<!--                     :rules="state.rules" label-width="83px">-->
<!--              <el-form-item required label="父级:" prop="parentId">-->
<!--                <el-select v-model="state.ruleForm.parentId" style="width:100%;" placeholder="请选择父级">-->
<!--                  <el-option :key="0" label="" :value="0"/>-->
<!--                  <el-option v-for="rule in state.parentMenusTree" :key="rule.id" :label="rule.name" :value="rule.id"/>-->
<!--                </el-select>-->
<!--              </el-form-item>-->
<!--              <el-form-item required label="名称:" prop="name">-->
<!--                <el-input v-model="state.ruleForm.name"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item label="后端路由:" prop="route">-->
<!--                <el-input v-model="state.ruleForm.route" placeholder="/method/xx/xx"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item label="排序:">-->
<!--                <el-input v-model="state.ruleForm.sort"/>-->
<!--              </el-form-item>-->
<!--              <el-form-item required label="是否启用:">-->
<!--                <el-switch v-model="state.ruleForm.status" active-color="#13ce66" inactive-color="#ff4949"-->
<!--                           :active-value="1" :inactive-value="0">-->
<!--                </el-switch>-->
<!--              </el-form-item>-->
<!--              <el-form-item>-->
<!--                <el-button style="width: 100%;margin: 0 auto;" type="primary" :loading="state.submitLoading"-->
<!--                           @click="ruleSubmit(ruleFormRefButton)">提交-->
<!--                </el-button>-->
<!--              </el-form-item>-->
<!--            </el-form>-->
<!--          </el-tab-pane>-->
<!--        </el-tabs>-->
      </el-dialog>
    </div>
  </div>
</template>

<script lang="ts" setup>
import {ruleList, upRuleStatus, createRule, editRule, topRule, parentMenusTree, deleteRule} from '@/api/rule'
import {onMounted, reactive, ref} from 'vue'
import {ElMessageBox, FormInstance, FormRules, TabsPaneContext} from 'element-plus'
import {Plus} from "@element-plus/icons-vue";
import addRule from './component/addRule.vue'
import AddRule from "@/views/system/component/addRule.vue";

interface RuleForm {
  id: number,
  parentId: number | string,
  status: number,
  type: number,
  sort: number,
  name: string,
  icon: string,
  route: string,
  path: string,
}

const ruleFormRefDirectory = ref<FormInstance>()
const ruleFormRefMenu = ref<FormInstance>()
const ruleFormRefButton = ref<FormInstance>()
const ruleFormRefApi = ref<FormInstance>()

const state = reactive({
  total: 0,
  page: 1,
  pageSize: 15,
  tableLoading: false,
  formDialogVisible: false,
  submitLoading: false,
  isEdit: false,
  tableData: <RuleForm[]>([]),
  colorStyle: true,
  formActiveName: 'directory',
  topRule: [],
  parentMenusTree: [],
  directoryTabDisabled: false,
  menuTabDisabled: false,
  buttonTabDisabled: false,
  apiTabDisabled: true,
  ruleForm: <RuleForm>{
    id: 0,
    parentId: '',
    status: 0,
    type: 0,
    sort: 0,
    name: '',
    icon: '',
    route: '',
    path: '',
  },
  rules: <FormRules>{
    name: [
      {required: true, message: '请输入名称', trigger: 'blur'},
    ],
    path: [
      {required: true, message: '请输入后端路由', trigger: 'blur'},
    ],
    route: [
      {required: true, message: '请输入前端路由', trigger: 'blur'},
    ],
    icon: [
      {required: true, message: '请输入图标', trigger: 'blur'},
    ],
  }
})

onMounted(() => {
  getData()
})

const handleDelete = (ids: Array<number>) => {
  ElMessageBox.confirm('你确定要删除吗?', '提示',
    {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning',
    }
  ).then(() => {
    deleteRule({ids: ids}).then(() => {
      getData()
    })
  })
}

const initTopRule = () => {
  topRule().then(resp => {
    state.topRule = resp.data
  })
}

const initParentMenusTree = () => {
  parentMenusTree().then(resp => {
    state.parentMenusTree = resp.data
  })
}

const handleFormTabsClick = (tab: TabsPaneContext) => {
  if (!state.isEdit) {
    resetForm()
  }
  initTabData(tab.paneName)
}

const initTabData = (activeName: string | number | undefined) => {
  switch (activeName) {
    case 'directory':
      state.ruleForm.type = 1
      break
    case 'menu':
      state.ruleForm.type = 2
      initTopRule()
      break
    case 'button':
      state.ruleForm.type = 3
      initParentMenusTree()
      break
    case 'api':
      state.ruleForm.type = 4
      initParentMenusTree()
      break
  }
}

const tableRowClassName = (row: any) => {
  if (row.type === 3) {
    return 'warning-row'
  } else if (row.type === 4) {
    return 'success-row'
  }
  return ''
}

const handleRoleStatusChange = (val: number, id: number) => {
  upRuleStatus({ids: [id], status: val}).then(() => {
    ElNotification({title: '提示', message: '操作成功', type: 'success'})
  })
}

const ruleSubmit = async (formEl: FormInstance | undefined) => {
  if (formEl) {
    await formEl.validate((valid) => {
      if (valid) {
        state.ruleForm.parentId = Number(state.ruleForm.parentId)
        state.submitLoading = true
        if (state.isEdit) {
          editRule(state.ruleForm).then(() => {
            state.formDialogVisible = false
            getData()
          }).finally(() => {
            state.submitLoading = false
          })
        } else {
          createRule(state.ruleForm).then(() => {
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
  ruleList().then(resp => {
    state.tableData = resp.data
  }).finally(() => {
    state.tableLoading = false
  })
}

const resetForm = async () => {
  if (!ruleFormRefDirectory.value) return
  ruleFormRefDirectory.value.resetFields()

  if (!ruleFormRefMenu.value) return
  ruleFormRefMenu.value.resetFields()

  if (!ruleFormRefButton.value) return
  ruleFormRefButton.value.resetFields()

  if (!ruleFormRefApi.value) return
  ruleFormRefApi.value.resetFields()
}

const findTableData = (data, id: number) => {
  for (let i = 0; i < data.length; i++) {
    const item = data[i]
    if (item.id === id) {
      return item
    }

    if (item.children !== undefined) {
      const result = findTableData(item.children, id)
      if (result !== undefined) {
        return result
      }
    }
  }

  return undefined
}

const loadEditData = async (item: RuleForm | undefined) => {
  if (item !== undefined) {
    state.ruleForm = <RuleForm>{
      id: item.id,
      parentId: item.parentId,
      status: item.status,
      type: item.type,
      sort: item.sort,
      name: item.name,
      icon: item.icon,
      route: item.route,
      path: item.path,
    }
  }
}

const openCreateOrUpdateDialog = async (rowIndex: number | undefined) => {
  state.formDialogVisible = true
  state.isEdit = rowIndex !== undefined
  await resetForm()

  if (state.isEdit && rowIndex !== undefined) {
    const rowData = state.tableData[rowIndex]
    await loadEditData(rowData)
    await initTab(rowData.type)
  } else {
    state.directoryTabDisabled = false
    state.menuTabDisabled = false
    state.buttonTabDisabled = false
    state.apiTabDisabled = false
    state.formActiveName = 'directory'
    initTabData(state.formActiveName)
  }
}

const initTab = (type: number) => {
  let activeName = ''
  switch (type) {
    default:
    case 1:
      activeName = 'directory';
      state.menuTabDisabled = true
      state.buttonTabDisabled = true
      state.apiTabDisabled = true
      break
    case 2:
      activeName = 'menu';
      state.directoryTabDisabled = true
      state.buttonTabDisabled = true
      state.apiTabDisabled = true
      break
    case 3:
      activeName = 'button';
      state.directoryTabDisabled = true
      state.menuTabDisabled = true
      state.apiTabDisabled = true
      break
    case 4:
      activeName = 'api';
      state.directoryTabDisabled = true
      state.menuTabDisabled = true
      state.buttonTabDisabled = true
      break
  }
  state.formActiveName = activeName
  initTabData(activeName)
}
</script>

<style lang="scss">
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

.el-table .warning-row {
  --el-table-tr-bg-color: var(--el-color-info-light-9);
}

.el-table .success-row {
  --el-table-tr-bg-color: var(--el-color-success-light-9);
}

.demo-tabs > .el-tabs__content {
  padding: 32px;
  color: #6b778c;
  font-size: 32px;
  font-weight: 600;
}
</style>
