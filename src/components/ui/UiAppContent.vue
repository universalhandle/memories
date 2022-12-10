<template>
  <NcAppContent>
    <div class="outer">
      <slot />
    </div>
  </NcAppContent>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";
import NcAppContent from "@nextcloud/vue/dist/Components/NcAppContent";

@Component({
  components: {
    NcAppContent,
  },
})
export default class UiAppContent extends Vue {}
</script>

<style scoped lang="scss">
.outer {
  padding: 0 0 0 44px;
  height: 100%;
  width: 100%;
}

@media (max-width: 768px) {
  .outer {
    padding: 0px;

    // Get rid of padding on img-outer (1px on mobile)
    // Also need to make sure we don't end up with a scrollbar -- see below
    margin-left: -1px;
    width: calc(100% + 3px); // 1px extra here because ... reasons
  }
}
</style>
