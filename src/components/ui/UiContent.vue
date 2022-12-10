<template>
  <NcContent
    app-name="memories"
    :class="{
      'remove-gap': removeOuterGap,
    }"
  >
    <slot />
  </NcContent>
</template>

<script lang="ts">
import { Component, Prop, Vue } from "vue-property-decorator";
import NcContent from "@nextcloud/vue/dist/Components/NcContent";

@Component({
  components: {
    NcContent,
  },
})
export default class UiContent extends Vue {
  @Prop() public appName!: string;

  get removeOuterGap() {
    return this.ncVersion >= 25;
  }

  get ncVersion() {
    const version = (<any>window.OC).config.version.split(".");
    return Number(version[0]);
  }
}
</script>

<style lang="scss">
// Nextcloud 25+: get rid of gap and border radius at right
#content-vue.remove-gap {
  // was var(--body-container-radius)
  // now set on #app-navigation-vue
  border-radius: 0;
  width: calc(100% - var(--body-container-margin) * 1); // was *2

  // Reduce size of navigation. NC <25 doesn't like this on mobile.
  #app-navigation-vue {
    max-width: 250px;
  }
}

// Prevent content overflow on NC <25
#content-vue {
  max-height: 100vh;

  // https://bugs.webkit.org/show_bug.cgi?id=160953
  overflow: visible;
  #app-navigation-vue {
    border-top-left-radius: var(--body-container-radius);
    border-bottom-left-radius: var(--body-container-radius);
  }
}
</style>
