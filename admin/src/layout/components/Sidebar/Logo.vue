<template>
  <div
    class="sidebar-logo-container"
    :class="{ 'collapse': collapse }"
    :style="{ backgroundColor: sideTheme === 'theme-dark' ? variables.menuBackground : variables.menuLightBackground }"
  >
    <transition :enter-active-class="proxy?.animate.logoAnimate.enter" mode="out-in">
      <router-link v-if="collapse" key="collapse" class="sidebar-logo-link" to="/">
        <img v-if="logo" :src="logo" class="sidebar-logo" />
        <h1 v-else class="sidebar-title" :style="{ color: sideTheme === 'theme-dark' ? variables.logoTitleColor : variables.logoLightTitleColor }">
          {{ title }}
        </h1>
      </router-link>
      <router-link v-else key="expand" class="sidebar-logo-link" to="/">
        <img v-if="logo" :src="logo" class="sidebar-logo" />
        <h1 class="sidebar-title" :style="{ color: sideTheme === 'theme-dark' ? variables.logoTitleColor : variables.logoLightTitleColor }">
          {{ title }}
        </h1>
      </router-link>
    </transition>
  </div>
</template>

<script setup lang="ts">
import variables from '@/assets/styles/variables.module.scss'
// import logo from '@/assets/logo/logo.png'
import useSettingsStore from '@/store/modules/settings'
import { ComponentInternalInstance } from "vue";
const { proxy } = getCurrentInstance() as ComponentInternalInstance;

defineProps({
    collapse: {
        type: Boolean,
        required: true
    }
})

const logo = ''//ref('data:image/jpeg;base64,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');
const title = ref('后台管理系统');
const settingsStore = useSettingsStore();
const sideTheme = computed(() => settingsStore.sideTheme);
</script>

<style lang="scss" scoped>
.sidebarLogoFade-enter-active {
  transition: opacity 1.5s;
}

.sidebarLogoFade-enter,
.sidebarLogoFade-leave-to {
  opacity: 0;
}

.sidebar-logo-container {
  position: relative;
  width: 100%;
  height: 50px;
  line-height: 50px;
  background: #2b2f3a;
  text-align: center;
  overflow: hidden;

  & .sidebar-logo-link {
    height: 100%;
    width: 100%;

    & .sidebar-logo {
      width: 32px;
      height: 32px;
      vertical-align: middle;
      margin-right: 12px;
    }

    & .sidebar-title {
      display: inline-block;
      margin: 0;
      color: #fff;
      font-weight: 600;
      line-height: 50px;
      font-size: 14px;
      font-family: Avenir, Helvetica Neue, Arial, Helvetica, sans-serif;
      vertical-align: middle;
    }
  }

  &.collapse {
    .sidebar-logo {
      margin-right: 0px;
    }
  }
}
</style>
