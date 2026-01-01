import { Controller } from "@hotwired/stimulus";
import * as Turbo from "@hotwired/turbo";

export default class extends Controller {
    static targets = ["menuLink"];

    async navigateTo(event) {
        const url = event.currentTarget.href;

        try {
            Turbo.visit(url);
        } catch (error) {
            console.error("Navigation error:", error);
        }
    }
}
