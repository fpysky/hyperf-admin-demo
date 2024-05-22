<template>
  <el-tabs v-model="state.formActiveName" type="card" @tab-click="handleFormTabsClick">
    <el-tab-pane :disabled="state.directoryTabDisabled" label="目录权限" name="directory">
      <el-form ref="ruleFormRefDirectory" style="width:80%;margin: 0 auto;" :model="state.ruleForm"
               :rules="state.rules" label-width="83px">
        <el-form-item required label="名称:" prop="name">
          <el-input v-model="state.ruleForm.name"/>
        </el-form-item>
        <el-form-item label="前端路由:" prop="path">
          <el-input v-model="state.ruleForm.path"/>
        </el-form-item>
        <el-form-item label="图标:" prop="icon">
          <el-input v-model="state.ruleForm.icon"/>
        </el-form-item>
        <el-form-item label="排序:">
          <el-input v-model="state.ruleForm.sort"/>
        </el-form-item>
        <el-form-item required label="是否启用:">
          <el-switch v-model="state.ruleForm.status" active-color="#13ce66" inactive-color="#ff4949"
                     :active-value="1" :inactive-value="0">
          </el-switch>
        </el-form-item>
        <el-form-item>
          <el-button style="width: 100%;margin: 0 auto;" type="primary" :loading="state.submitLoading"
                     @click="ruleSubmit(ruleFormRefDirectory)">提交
          </el-button>
        </el-form-item>
      </el-form>
    </el-tab-pane>
    <el-tab-pane :disabled="state.menuTabDisabled" label="菜单权限" name="menu">
      <el-form ref="ruleFormRefMenu" style="width:80%;margin: 0 auto;" :model="state.ruleForm"
               :rules="state.rules"
               label-width="83px">
        <el-form-item required label="父级:" prop="parentId">
          <el-select v-model="state.ruleForm.parentId" style="width:100%;" placeholder="请选择父级">
            <el-option :key="0" label="" :value="0"/>
            <el-option v-for="rule in state.topRule" :key="rule.id" :label="rule.name" :value="rule.id"/>
          </el-select>
        </el-form-item>
        <el-form-item required label="名称:" prop="name">
          <el-input v-model="state.ruleForm.name"/>
        </el-form-item>
        <el-form-item label="前端路由:" prop="path">
          <el-input v-model="state.ruleForm.path"/>
        </el-form-item>
        <el-form-item label="图标:" prop="icon">
          <el-input v-model="state.ruleForm.icon"/>
        </el-form-item>
        <el-form-item label="排序:">
          <el-input v-model="state.ruleForm.sort"/>
        </el-form-item>
        <el-form-item required label="是否启用:">
          <el-switch v-model="state.ruleForm.status" active-color="#13ce66" inactive-color="#ff4949"
                     :active-value="1" :inactive-value="0">
          </el-switch>
        </el-form-item>
        <el-form-item>
          <el-button style="width: 100%;margin: 0 auto;" type="primary" :loading="state.submitLoading"
                     @click="ruleSubmit(ruleFormRefMenu)">提交
          </el-button>
        </el-form-item>
      </el-form>
    </el-tab-pane>
    <el-tab-pane :disabled="state.buttonTabDisabled" label="按钮权限" name="button">
      <el-form ref="ruleFormRefButton" style="width:80%;margin: 0 auto;" :model="state.ruleForm"
               :rules="state.rules" label-width="83px">
        <el-form-item required label="父级:" prop="parentId">
          <el-select v-model="state.ruleForm.parentId" style="width:100%;" placeholder="请选择父级">
            <el-option :key="0" label="" :value="0"/>
            <el-option v-for="rule in state.parentMenusTree" :key="rule.id" :label="rule.name" :value="rule.id"/>
          </el-select>
        </el-form-item>
        <el-form-item required label="名称:" prop="name">
          <el-input v-model="state.ruleForm.name"/>
        </el-form-item>
        <el-form-item label="后端路由:" prop="route">
          <el-input v-model="state.ruleForm.route" placeholder="/method/xx/xx"/>
        </el-form-item>
        <el-form-item label="排序:">
          <el-input v-model="state.ruleForm.sort"/>
        </el-form-item>
        <el-form-item required label="是否启用:">
          <el-switch v-model="state.ruleForm.status" active-color="#13ce66" inactive-color="#ff4949"
                     :active-value="1" :inactive-value="0">
          </el-switch>
        </el-form-item>
        <el-form-item>
          <el-button style="width: 100%;margin: 0 auto;" type="primary" :loading="state.submitLoading"
                     @click="ruleSubmit(ruleFormRefButton)">提交
          </el-button>
        </el-form-item>
      </el-form>
    </el-tab-pane>
  </el-tabs>
</template>

<script lang="ts" setup>
import {onMounted, reactive, ref,defineEmits} from "vue";
import {FormInstance, FormRules, TabsPaneContext} from "element-plus";
import {ruleDetail, editRule, parentMenusTree, topRule} from "@/api/rule";
import {propTypes} from "@/utils/propTypes";

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

const props = defineProps({
  id: propTypes.number
})
const emit = defineEmits(['closeDialogAndRefresh'])
const ruleFormRefDirectory = ref<FormInstance>()
const ruleFormRefMenu = ref<FormInstance>()
const ruleFormRefButton = ref<FormInstance>()

const state = reactive({
  formDialogVisible: false,
  directoryTabDisabled: false,
  menuTabDisabled: false,
  buttonTabDisabled: false,
  submitLoading: false,
  formActiveName: 'directory',
  topRule: [],
  parentMenusTree: [],
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
  initData()
})

const initData = () => {
  resetForm()
  getRuleDetail(props.id)
}

const getRuleDetail = async (id: number) => {
  await ruleDetail(id).then((resp: { data: RuleForm }) => {
    const data = resp.data
    state.ruleForm = {
      id: data.id,
      parentId: data.parentId,
      status: data.status,
      type: data.type,
      sort: data.sort,
      name: data.name,
      icon: data.icon,
      route: data.route,
      path: data.path,
    }
    initTab(state.ruleForm.type)
  })
}

const initTab = (type: number) => {
  let activeName = ''
  switch (type) {
    default:
    case 1:
      activeName = 'directory';
      state.menuTabDisabled = true
      state.buttonTabDisabled = true
      break
    case 2:
      activeName = 'menu';
      state.directoryTabDisabled = true
      state.buttonTabDisabled = true
      break
    case 3:
      activeName = 'button';
      state.directoryTabDisabled = true
      state.menuTabDisabled = true
      break
  }
  state.formActiveName = activeName
  initTabData(activeName)
}

const handleFormTabsClick = (tab: TabsPaneContext) => {
  initTabData(tab.paneName)
}

const initTabData = (activeName: string | number | undefined) => {
  if (activeName === undefined) {
    activeName = state.formActiveName
  }

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
  }
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

const resetForm = async () => {
  if (!ruleFormRefDirectory.value) return
  ruleFormRefDirectory.value.resetFields()

  if (!ruleFormRefMenu.value) return
  ruleFormRefMenu.value.resetFields()

  if (!ruleFormRefButton.value) return
  ruleFormRefButton.value.resetFields()
}

const ruleSubmit = async (formEl: FormInstance | undefined) => {
  if (formEl) {
    await formEl.validate((valid) => {
      if (valid) {
        state.ruleForm.parentId = Number(state.ruleForm.parentId)
        state.submitLoading = true
        editRule(state.ruleForm).then(() => {
          state.formDialogVisible = false
          emit('closeDialogAndRefresh')
        }).finally(() => {
          state.submitLoading = false

        })
      }
    })
  }
}

defineExpose({initData})
</script>

<style scoped>

</style>
