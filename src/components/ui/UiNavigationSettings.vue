<template>
  <NcAppNavigationSettings :title="title">
    <slot />
  </NcAppNavigationSettings>
</template>

<script lang="ts">
import { Component, Prop, Vue } from "vue-property-decorator";
import NcAppNavigationSettings from "@nextcloud/vue/dist/Components/NcAppNavigationSettings";

@Component({
  components: {
    NcAppNavigationSettings,
  },
})
export default class UiNavigationSettings extends Vue {
  @Prop() title!: string;
}
</script>
