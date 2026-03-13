// import './bootstrap';
import {initTaskCompletionHandlers} from "./tasks.js";
import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

initTaskCompletionHandlers();
