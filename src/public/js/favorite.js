document.addEventListener("DOMContentLoaded", function () {
    // CSRFトークンの設定
    const token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // いいねボタンの処理
    const favoriteBtn = document.querySelector(".favorite-button");

    if (favoriteBtn) {
        favoriteBtn.addEventListener("click", function (e) {
            e.preventDefault(); // デフォルトの動作を防ぐ

            const exhibitionId = this.getAttribute("data-exhibition-id");
            const isFavorited = this.getAttribute("data-favorited") === "true";

            // Ajax通信
            fetch(`/item/:{$exhibitionId}/favorite`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                    Accept: "application/json",
                },
                body: JSON.stringify({}),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(
                            `HTTP error! status: ${response.status}`
                        );
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // アイコンの更新
                    const favoriteIcon = this.querySelector(".favorite-icon");
                    const favoriteCount =
                        document.querySelector(".favorite-count");

                    if (data.isFavorited) {
                        // いいね済みの状態
                        favoriteIcon.alt = "いいね済み";
                        this.setAttribute("data-favorited", "true");
                        this.classList.add("favorited");
                    } else {
                        // いいねしていない状態
                        favoriteIcon.alt = "いいね";
                        this.setAttribute("data-favorited", "false");
                        this.classList.remove("favorited");
                    }

                    // いいね数の更新
                    if (favoriteCount) {
                        favoriteCount.textContent = data.favoriteCount;
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("エラーが発生しました。もう一度お試しください。");
                });
        });
    }
});
