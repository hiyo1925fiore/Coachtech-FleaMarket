document.addEventListener("DOMContentLoaded", function () {
    const editModal = document.getElementById("edit-modal");
    const editForm = document.getElementById("edit-form");
    const editMessage = document.getElementById("edit-message");
    const editCancel = document.getElementById("edit-cancel");
    const editButtons = document.querySelectorAll(".chat__edit-button");
    const editError = document.getElementById("edit-error");

    if (!editModal || !editForm || !editMessage) {
        return; // 必要な要素がない場合は処理を中断
    }

    // 編集ボタンのクリックイベント
    editButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const chatId = this.getAttribute("data-chat-id");
            const message = this.getAttribute("data-message");

            // フォームのアクションURLを設定
            editForm.action = `/chat/${chatId}`;

            // is_edit_modeとedit_chat_idをhiddenフィールドとして追加
            updateOrCreateHiddenField("is_edit_mode", "1");
            updateOrCreateHiddenField("edit_chat_id", chatId);

            // テキストエリアにメッセージを設定
            editMessage.value = message;

            // エラーメッセージをクリア
            if (editError) {
                editError.innerHTML = "";
            }

            // モーダルを表示
            openModal();
        });
    });

    // キャンセルボタンのクリックイベント
    if (editCancel) {
        editCancel.addEventListener("click", function () {
            closeEditModal();
        });
    }

    // オーバーレイのクリックイベント
    const editOverlay = document.querySelector(".edit-modal-overlay");
    if (editOverlay) {
        editOverlay.addEventListener("click", function () {
            closeEditModal();
        });
    }

    // hiddenフィールドを更新または作成する関数
    function updateOrCreateHiddenField(name, value) {
        let field = editForm.querySelector(`input[name="${name}"]`);
        if (!field) {
            field = document.createElement("input");
            field.type = "hidden";
            field.name = name;
            editForm.appendChild(field);
        }
        field.value = value;
    }

    // モーダルを開く関数
    function openModal() {
        editModal.style.visibility = "visible";
        editModal.style.opacity = "1";
    }

    // モーダルを閉じる関数
    function closeEditModal() {
        editModal.style.visibility = "hidden";
        editModal.style.opacity = "0";
        if (editError) {
            editError.innerHTML = "";
        }
    }

    // エラーがある場合にモーダルを開く関数（Bladeから呼び出される）
    window.openEditModalWithError = function (chatId, message, errors) {
        if (!editForm || !editMessage || !chatId) {
            console.error("編集モーダルの表示に失敗しました");
            return;
        }

        editForm.action = `/chat/${chatId}`;
        editMessage.value = message || "";

        // hiddenフィールドを追加
        updateOrCreateHiddenField("is_edit_mode", "1");
        updateOrCreateHiddenField("edit_chat_id", chatId);

        // エラーメッセージを表示
        if (editError && errors && errors.length > 0) {
            editError.innerHTML = "";
            errors.forEach((error) => {
                const errorP = document.createElement("p");
                errorP.className = "edit-modal__error--text";
                errorP.textContent = error;
                editError.appendChild(errorP);
            });
        }

        // モーダルを表示
        openModal();
    };
});
