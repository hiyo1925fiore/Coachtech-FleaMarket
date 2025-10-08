document.addEventListener("DOMContentLoaded", function () {
    const tradeCloseButton = document.querySelector(".trade-close-button");
    const modal = document.querySelector(".modal");
    const stars = document.querySelectorAll(".star");
    const ratingInput = document.querySelector('input[name="rating"]');
    const starsContainer = document.querySelector(".modal-form__stars");

    // 初期表示で3つ目の星を選択状態にする
    function initializeStars() {
        if (stars.length >= 3 && ratingInput) {
            ratingInput.value = 3;
            stars.forEach((star, i) => {
                if (i < 3) {
                    star.classList.add("active");
                } else {
                    star.classList.remove("active");
                }
            });
        }
    }

    // 星評価のイベントリスナーを設定
    function setupStarListeners() {
        stars.forEach((star, index) => {
            // クリックイベント
            star.addEventListener("click", function (e) {
                e.preventDefault();
                const rating = index + 1;
                ratingInput.value = rating;

                // すべての星をリセット
                stars.forEach((s) => {
                    s.classList.remove("active");
                });

                // クリックした星までをアクティブに
                for (let i = 0; i <= index; i++) {
                    stars[i].classList.add("active");
                }
            });

            // ホバー効果
            star.addEventListener("mouseenter", function () {
                stars.forEach((s) => {
                    s.classList.remove("hover");
                });

                for (let i = 0; i <= index; i++) {
                    stars[i].classList.add("hover");
                }
            });
        });

        // マウスが星エリアから離れたらホバー効果をリセット
        if (starsContainer) {
            starsContainer.addEventListener("mouseleave", function () {
                stars.forEach((s) => {
                    s.classList.remove("hover");
                });
            });
        }
    }

    // 星を初期化
    initializeStars();
    // イベントリスナーを設定
    setupStarListeners();

    // 「取引を完了する」ボタンがある場合の処理
    if (tradeCloseButton && modal) {
        tradeCloseButton.addEventListener("click", function (e) {
            e.preventDefault();

            // モーダルを表示（外クリックで閉じないようにoverlayのhrefを無効化）
            modal.style.visibility = "visible";
            modal.style.opacity = "1";

            const modalOverlay = modal.querySelector(".modal-overlay");
            if (modalOverlay) {
                modalOverlay.style.pointerEvents = "none";
            }

            // モーダルを表示した後に星を再初期化
            setTimeout(() => {
                initializeStars();
            }, 100);
        });
    }
});
