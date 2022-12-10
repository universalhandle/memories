globalThis.OC ||= {
  webroot: "",
  isUserAdmin: () => true,

  L10N: {
    translate: (app, text, vars, count, options) => text,
    translatePlural: (app, textSingular, textPlural, count, vars, options) =>
      textSingular,
  },

  config: {
    modRewriteWorking: true,
    version: "25.0.0.15",
  },

  coreApps: ["core"],

  appswebroots: {
    memories: "/apps/memories",
  },
} as any;

globalThis.OCA ||= {};
