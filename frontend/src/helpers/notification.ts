import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export function notify(
  message: string,
  type: "success" | "error" | "warning",
) {
  toast(message, {
    type,
    autoClose: 5000,
    transition: "slide",
  });
}
