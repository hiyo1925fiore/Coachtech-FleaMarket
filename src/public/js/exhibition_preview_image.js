document.addEventListener("DOMContentLoaded", function () {
    const imageInput = document.getElementById("img_url");
    const previewContainer = document.getElementById("image-preview-container");

    imageInput.addEventListener("change", function () {
        if (this.files && this.files.length > 0) {
            // 画像のプレビューを表示する
            previewContainer.innerHTML = "";
            const img = document.createElement("img");
            img.classList.add("image-preview");
            img.src = URL.createObjectURL(this.files[0]);
            previewContainer.appendChild(img);
        } else {
            fileNameDisplay.textContent = "";
            previewContainer.innerHTML = "";
        }
    });
});
