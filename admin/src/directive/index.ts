import copyText from './common/copyText';
import { hasPermission, hasRoles } from './permission';
import { App } from 'vue';

export default (app: App) => {
  app.directive('copyText', copyText);
  app.directive('hasPermission', hasPermission);
  app.directive('hasRoles', hasRoles);
};
