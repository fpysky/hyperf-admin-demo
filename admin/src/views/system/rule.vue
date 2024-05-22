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
        :header-cell-style="{'text-align':'center'}">
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
            <el-button type="primary" @click="openCreateOrUpdateDialog(scope.row.id)" :disabled="scope.row.type === 4">
              编辑
            </el-button>
            <el-button type="danger" @click="handleDelete([scope.row.id])">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-dialog style="text-align: center;" v-model="state.formDialogVisible"
                 :title="state.isEdit ? '编辑权限' : '新增权限'"
                 width="30%">
        <add-rule v-if="!state.isEdit" ref="addRuleRef" @closeDialogAndRefresh="closeFormDialogAndReload"></add-rule>
        <edit-rule v-if="state.isEdit" ref="editRuleRef" @closeDialogAndRefresh="closeFormDialogAndReload"
                   :id="state.editId"></edit-rule>
      </el-dialog>
    </div>
  </div>
</template>

<script lang="ts" setup>
import {ruleList, upRuleStatus, deleteRule} from '@/api/rule'
import {onMounted, reactive, ref} from 'vue'
import {ElMessageBox} from 'element-plus'
import {Plus} from "@element-plus/icons-vue";
import AddRule from "@/views/system/component/addRule.vue";
import EditRule from "@/views/system/component/editRule.vue";

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

const addRuleRef = ref()
const editRuleRef = ref()

const state = reactive({
  total: 0,
  page: 1,
  pageSize: 15,
  tableLoading: false,
  formDialogVisible: false,
  isEdit: false,
  tableData: <RuleForm[]>([]),
  colorStyle: true,
  editId: 0,
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

const getData = () => {
  state.tableLoading = true
  ruleList().then(resp => {
    state.tableData = resp.data
  }).finally(() => {
    state.tableLoading = false
  })
}

const openCreateOrUpdateDialog = async (id: number | undefined) => {
  state.isEdit = id !== undefined

  if (state.isEdit && id !== undefined) {
    state.editId = id
    editRuleRef.value?.initData()
  } else {
    addRuleRef.value?.initData()
  }

  state.formDialogVisible = true
}

const closeFormDialogAndReload = () => {
  state.formDialogVisible = false
  getData()
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
