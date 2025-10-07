document.addEventListener("DOMContentLoaded", function () {
    const textarea = document.querySelector(".chat-form__text-area");
    const form = document.querySelector(".chat-form");
    const storageKey = "chat_draft_message_" + window.location.pathname;

    // ページ読み込み時：保存されたメッセージを復元
    if (textarea) {
        const savedMessage = localStorage.getItem(storageKey);
        if (savedMessage && !textarea.value) {
            textarea.value = savedMessage;
        }

        // 入力時：localStorageに自動保存
        textarea.addEventListener("input", function () {
            localStorage.setItem(storageKey, textarea.value);
        });
    }

    // フォーム送信時：保存されたメッセージを削除
    if (form) {
        form.addEventListener("submit", function () {
            localStorage.removeItem(storageKey);
        });
    }
});
