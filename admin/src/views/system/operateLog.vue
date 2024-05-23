<template>
  <div>
    <div class="toolbar">
      <el-input v-model="state.searchData.keyword" style="width: 200px;" placeholder="搜索"/>
      <el-button type="primary" style="margin-left: 10px;" :loading="state.tableLoading"
                 @click="getData()">
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
      <el-table :data="state.tableData" v-loading="state.tableLoading" style="width: 100%;margin-bottom: 20px;">
        <el-table-column prop="id" label="ID" width="100"/>
        <el-table-column prop="module" label="系统模块" width="300"/>
        <el-table-column prop="operateTypeZh" label="操作类型中文" width="180"/>
        <el-table-column prop="method" label="请求方式" width="180"/>
        <el-table-column prop="operateAdmin" label="操作人员" width="200"/>
        <el-table-column prop="operateIp" label="操作IP" width="180"/>
        <el-table-column prop="operateIpAddress" label="操作地点" width="180"/>
        <el-table-column prop="operateStatusZh" label="操作状态中文" width="180"/>
        <el-table-column prop="operatedAt" label="操作日期" width="180"/>
      </el-table>
      <div style="width:100%;">
        <el-pagination
          style="margin-left: 20px;"
          background
          layout="prev, pager, next"
          :current-page="state.page"
          :page-size="state.pageSize"
          :total="state.total"
          @current-change="handlePageChange"
        />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import {getOperateLog} from "@/api/operateLog";
import {onMounted, reactive} from "vue";

const state = reactive({
  page: 1,
  pageSize: 15,
  total: 0,
  tableLoading: false,
  tableData: [],
  searchData: {
    keyword: ""
  },
});

onMounted(() => {
  getData();
});

const handlePageChange = (page: number) => {
  state.page = page;
  getData();
}

const getData = () => {
  state.tableLoading = true;
  getOperateLog({
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
