<template>
  <div>
    <div class="toolbar">
      <el-button @click="openCreateOrUpdateDialog(undefined)">
        <el-icon style="vertical-align: middle;">
          <Plus/>
        </el-icon>
        <span style="vertical-align: middle">添加</span>
      </el-button>
      <el-input v-model="state.searchData.keyword" style="width: 200px;margin-left: 10px" placeholder="请输入关键词"/>
      <el-button type="primary" style="margin-left: 10px;" :loading="state.tableLoading"
                 @click="getData">
        <el-icon style="vertical-align: middle;">
          <Search/>
        </el-icon>
        <span style="vertical-align: middle">搜索</span>
      </el-button>
      <el-button @click="clear()" title="清空搜索条件">
        <el-icon style="vertical-align: middle;">
          <Delete/>
        </el-icon>
      </el-button>
    </div>
    <div class="content">
      <el-table
        :data="state.tableData"
        v-loading="state.tableLoading"
        :header-cell-style="{'text-align':'center'}"
        border
        style="width: 100%;"
      >
        <el-table-column prop="id" label="ID" width="90" align="center"/>
        <el-table-column prop="name" label="名称" width="140" align="center"/>
        <el-table-column prop="status" label="状态" width="80" align="center">
          <template #default="scope">
            <el-switch
              @change="(val: number) => handleStatusChange(val, scope.row.id)"
              v-model="scope.row.status"
              style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949"
              :active-value="1"
              :inactive-value="0"
            />
          </template>
        </el-table-column>
        <el-table-column prop="type" label="类型" width="110" align="center">
          <template #default="scope">
            <el-button type="warning" v-if="scope.row.type === 1">超管</el-button>
            <el-button plain v-else>普通</el-button>
          </template>
        </el-table-column>
        <el-table-column prop="mobile" label="手机号" width="140" align="center"/>
        <el-table-column prop="email" label="电子邮箱" width="200" align="center"/>
        <el-table-column prop="lastLoginTime" label="最后登录时间" width="180" align="center"/>
        <el-table-column prop="createdAt" label="创建时间" width="180" align="center"/>
        <el-table-column prop="updatedAt" label="更新时间" width="180" align="center"/>
        <el-table-column prop="lastLoginIp" label="最后登录IP" width="180" align="center"/>
        <el-table-column prop="logo" label="logo" width="180" align="center"/>
        <el-table-column label="操作" width="200" fixed="right" align="center">
          <template #default="scope">
            <el-button type="primary" @click="openCreateOrUpdateDialog(scope.$index)">编辑</el-button>
            <el-button type="danger" @click="handleDelete([scope.row.id])">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div style="width:100%;margin-top: 20px">
        <el-pagination
          background
          layout="prev, pager, next"
          :current-page="state.page"
          :page-size="state.pageSize"
          :total="state.total"
          @current-change="handlePageChange"
        />
      </div>
      <el-dialog style="text-align: center;" v-model="state.formDialogVisible"
                 :title="state.isEdit ? '编辑管理员' : '新增管理员'" width="30%">
        <el-form
          :loading="state.formDialogLoading"
          ref="ruleFormRef"
          style="width:80%;margin: 0 auto;"
          :model="state.adminForm"
          :rules="state.rules"
          label-width="83px"
        >
          <el-form-item required label="姓名:" prop="name">
            <el-input v-model="state.adminForm.name"/>
          </el-form-item>
          <el-form-item required label="手机号:" prop="mobile">
            <el-input v-model="state.adminForm.mobile"/>
          </el-form-item>
          <el-form-item v-if="!state.isEdit" :required="!state.isEdit" label="密码:" prop="password">
            <el-input v-model="state.adminForm.password" type="password"/>
          </el-form-item>
          <el-form-item v-if="!state.isEdit" :required="!state.isEdit" label="确认密码:" prop="rePassword">
            <el-input v-model="state.adminForm.rePassword" type="password"/>
          </el-form-item>
          <el-form-item required label="电子邮箱:" prop="email">
            <el-input v-model="state.adminForm.email"/>
          </el-form-item>
          <el-form-item required label="角色:" prop="roleIds">
            <el-checkbox-group v-model="state.adminForm.roleIds" v-for="role in state.roles">
              <el-checkbox :label="role.id" style="margin-right: 10px;">{{ role.name }}</el-checkbox>
            </el-checkbox-group>
          </el-form-item>
          <el-form-item required label="是否启用:" prop="status">
            <el-switch v-model="state.adminForm.status" active-color="#13ce66" inactive-color="#ff4949"
                       :active-value="1" :inactive-value="0">
            </el-switch>
          </el-form-item>
          <el-form-item>
            <el-button style="width: 100%;margin: 0 auto;" type="primary" :loading="state.submitLoading"
                       @click="submitForm">提交
            </el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
    </div>
  </div>
</template>

<script lang="ts" setup>
import {adminList, createAdmin, editAdmin, upAdminStatus, deleteAdmin} from "@/api/admin";
import {getRoleSelectData} from "@/api/role";
import {onMounted, reactive, ref} from "vue";
import type {FormInstance, FormRules} from "element-plus";
import {ElMessageBox} from "element-plus";

interface AdminForm {
  id: number,
  name: string,
  mobile: string,
  password: string,
  rePassword: string,
  email: string,
  status: number,
  roleIds: Array<number>,
}

const ruleFormRef = ref<FormInstance>();

const state = reactive({
  total: 0,
  page: 1,
  pageSize: 15,
  tableLoading: false,
  formDialogVisible: false,
  formDialogLoading: false,
  submitLoading: false,
  isEdit: false,
  roles: [],
  tableData: <AdminForm[]>([]),
  searchData: {
    keyword: ""
  },
  adminForm: <AdminForm>({
    id: 0,
    name: "",
    mobile: "",
    password: "",
    rePassword: "",
    email: "",
    status: 0,
    roleIds: []
  }),
  rules: <FormRules>{
    name: [
      {required: true, message: "请输入姓名", trigger: "blur"}
    ],
    mobile: [
      {required: true, message: "请输入手机号", trigger: "blur"}
    ],
    password: [
      {required: true, message: "请输入密码", trigger: "blur"}
    ],
    rePassword: [
      {required: true, message: "请输入确认密码", trigger: "blur"}
    ],
    email: [
      {required: true, message: "请输入电子邮箱", trigger: "blur"}
    ],
    roleIds: [
      {required: true, type: "array", message: "请选择角色", trigger: "change"}
    ]
  }
});

onMounted(() => {
  getData();
});

const handlePageChange = (page:number) => {
  state.page = page;
  getData();
}

const handleDelete = (ids: Array<number>) => {
  ElMessageBox.confirm("你确定要删除吗?", "提示",
    {
      confirmButtonText: "确定",
      cancelButtonText: "取消",
      type: "warning"
    }
  ).then(() => {
    deleteAdmin({ids}).then(() => {
      getData();
    });
  });
};

const handleStatusChange = (val: number, id: number) => {
  upAdminStatus({ids: [id], status: val}).then(() => {
    ElNotification({title: '提示', message: '操作成功',type: 'success'})
  });
};

const submitForm = async () => {
  if (ruleFormRef.value) {
    await ruleFormRef.value.validate((valid, fields) => {
      if (valid) {
        state.submitLoading = true;
        if (state.isEdit) {
          editAdmin(state.adminForm).then(() => {
            state.formDialogVisible = false;
            getData();
          }).finally(() => {
            state.submitLoading = false;
          });
        } else {
          createAdmin(state.adminForm).then(() => {
            state.formDialogVisible = false;
            getData();
          }).finally(() => {
            state.submitLoading = false;
          });
        }
      }
    });
  }
};

const getData = () => {
  state.tableLoading = true;
  adminList({
    page: state.page,
    pageSize: state.pageSize,
    ...state.searchData
  }).then(resp => {
    state.tableData = resp.data.list;
    state.total = resp.data.total;
  }).finally(() => {
    state.tableLoading = false;
  });
};

const clear = () => {
  state.searchData.keyword = "";
};

const resetForm = async () => {
  if (!ruleFormRef.value) return;
  ruleFormRef.value.resetFields();
};

const initRoles = async () => {
  await getRoleSelectData().then(resp => {
    state.roles = resp.data;
  });
};

const loadEditData = async (rowIndex: number | undefined) => {
  if (rowIndex !== undefined) {
    const rowData = state.tableData[rowIndex];
    state.adminForm = <AdminForm>{
      id: rowData.id,
      name: rowData.name,
      mobile: rowData.mobile,
      email: rowData.email,
      status: rowData.status,
      roleIds: rowData.roleIds,
      password: "",
      rePassword: "",
    };
  }
};

const openCreateOrUpdateDialog = async (rowIndex: number | undefined) => {
  state.formDialogVisible = true;
  state.formDialogLoading = true;
  state.isEdit = rowIndex !== undefined;
  await initRoles();
  await resetForm();
  if(state.isEdit){
    await loadEditData(rowIndex);
  }
  state.formDialogLoading = false;
};
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
  padding: 10px 0;
}
</style>
