<template>
  <NcAppNavigation ref="nav">
    <template slot="list">
      <slot name="list" />
    </template>
    <template slot="footer">
      <slot name="footer" />
    </template>
  </NcAppNavigation>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
import NcAppNavigation from "@nextcloud/vue/dist/Components/NcAppNavigation";

@Component({
  components: {
    NcAppNavigation,
  },
})
export default class UiNavigation extends Vue {
  public toggleNavigation() {
    (<any>this.$refs.nav)?.toggleNavigation(false);
  }
}
</script>
