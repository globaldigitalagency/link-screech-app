import { Toast } from 'bootstrap';

export default function showToasts() {
    document.querySelectorAll(".toast").forEach((toastNode) => {
        const toast = new Toast(toastNode, {
            autohide: true,
        });
        toast.show();
    });
}
