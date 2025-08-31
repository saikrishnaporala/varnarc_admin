interface ImportMeta {
    readonly glob: (pattern: string) => Record<string, () => Promise<unknown>>;
}