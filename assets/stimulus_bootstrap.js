import { Application } from "@hotwired/stimulus";

const application = Application.start();

// Autoload controllers from the controllers directory
const context = require.context("./controllers", true, /\.js$/);
context.keys().forEach(key => {
    const matches = key.match(/\.\/(\w+)_controller\.js$/);
    if (matches) {
        const name = matches[1].replace(/_/g, '-');
        const module = context(key);
        application.register(name, module.default);
    }
});

export { application };
