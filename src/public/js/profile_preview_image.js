document.addEventListener("DOMContentLoaded", function () {
    const imageInput = document.getElementById("img_url");
    const previewContainer = document.getElementById("image-preview-container");
    const placeholderCircle = document.getElementById("placeholder-circle");
    const existingImage = document.getElementById("preview-image");

    imageInput.addEventListener("change", function () {
        if (this.files && this.files.length > 0) {
            const file = this.files[0];

            // ファイルタイプの確認
            if (!file.type.startsWith("image/")) {
                alert("画像ファイルを選択してください。");
                return;
            }

            // 既存のプレビュー画像やプレースホルダーを削除
            previewContainer.innerHTML = "";

            // 新しい画像要素を作成
            const img = document.createElement("img");
            img.classList.add("profile-preview-image");
            img.id = "preview-image";

            // FileReaderを使用してファイルを読み込み
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else {
            // ファイルが選択されていない場合はプレースホルダーを表示
            previewContainer.innerHTML =
                '<div class="profile-placeholder-circle" id="placeholder-circle"></div>';
        }
    });
});
