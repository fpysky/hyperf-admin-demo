<template>
  <div>
    <div class="toolbar">
      <el-input v-model="state.searchData.prefix" style="width: 200px;" placeholder="限定返回的文件路径前缀" clearable />
      <el-select v-model="state.searchData.projectName" style="margin-left: 10px" placeholder="项目名称" clearable>
        <el-option label="大薪薪-正式服" value="daxinxin/" />
        <el-option label="大薪薪-测试服" value="daxinxin-test/" />
      </el-select>
      <el-button type="primary" style="margin-left: 10px;" :loading="state.tableLoading" @click="getData()">
        <el-icon size="small" style="vertical-align: middle;">
          <Search />
        </el-icon>
        <span style="vertical-align: middle">搜索</span>
      </el-button>
    </div>
    <div class="content">
      <el-table :data="state.tableData" v-loading="state.tableLoading" style="width: 100%;margin-bottom: 20px;">
        <el-table-column prop="filename" label="文件名" />
        <el-table-column prop="filePath" label="文件路径" />
        <el-table-column prop="lastModified" label="最后修改时间" width="180" />
        <el-table-column prop="sizeStr" label="对象大小" width="180" />
        <el-table-column prop="typeZh" label="对象类型" width="180" />
        <el-table-column prop="projectName" label="项目名称" width="180" />
        <el-table-column label="操作" width="140">
          <template #default="scope">
            <el-button :disabled="scope.row.typeZh !== '图片'" @click="openPreviewImage(scope.row.fileUrl)" type="primary" size="small"
              >预览
            </el-button>
            <el-link type="primary" style="margin-left: 10px;" :href="scope.row.fileUrl" target="_blank">下载</el-link>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <el-dialog style="text-align: center;" v-model="state.openPreviewDialog" title="预览" width="60%">
      <el-image style="width: 100%; height: 100%" :src="state.imagePreviewUrl" fit="fill" />
    </el-dialog>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, reactive } from "vue";
import { fileList } from "@/api/fileManage";
import { Search } from "@element-plus/icons-vue";

const state = reactive({
  total: 0,
  page: 1,
  pageSize: 15,
  tableLoading: false,
  tableData: [],
  searchData: {
    prefix: "",
    projectName:'',
  },
  openPreviewDialog: false,
  imagePreviewUrl: "",
});

const openPreviewImage = (imageUrl: string) => {
  state.openPreviewDialog = true;
  state.imagePreviewUrl = imageUrl;
};

const getData = () => {
  state.tableLoading = true;
  fileList({
    page: state.page,
    pageSize: state.pageSize,
    ...state.searchData
  }).then(resp => {
    state.tableData = resp.data;
  }).finally(() => {
    state.tableLoading = false;
  });
};

onMounted(() => {
  getData();
});
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
