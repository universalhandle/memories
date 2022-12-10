<template>
  <NcAppNavigationItem :title="title" :to="to" @click="click" exact>
    <template slot="icon">
      <slot name="icon" />
    </template>
  </NcAppNavigationItem>
</template>

<script lang="ts">
import { Component, Emit, Prop, Vue } from "vue-property-decorator";
import NcAppNavigationItem from "@nextcloud/vue/dist/Components/NcAppNavigationItem";

@Component({
  components: {
    NcAppNavigationItem,
  },
})
export default class UiNavigationItem extends Vue {
  @Prop() title!: string;
  @Prop() to!: { name: string };
  @Emit("click") public click() {}
}
</script>
