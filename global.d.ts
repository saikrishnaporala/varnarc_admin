// Declare global to extend Window interface
export {};

declare global {
  interface Window {
    bootstrap: typeof bootstrap;
    axios: typeof axios;
  }
}