$(document).ready(function () {
    // CSRFトークンを設定
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // いいねボタンのクリックイベント
    $(".favorite-button").on("click", function (e) {
        e.preventDefault();

        var button = $(this);
        var exhibitionId = button.data("exhibition-id");
        var isFavorited = button.data("favorited") === "true";

        // ボタンを一時的に無効化
        button.prop("disabled", true);

        $.ajax({
            url: "/item/:" + exhibitionId + "/favorite",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                // データ属性を更新
                button.data("favorited", response.isFavorited);

                // 星の色を変更
                var star = button.find(".favorite-star");
                if (response.isFavorited) {
                    star.addClass("favorited");
                } else {
                    star.removeClass("favorited");
                }

                // いいね数を更新
                button
                    .find(".exhibition-actions__count-favorite")
                    .text(response.favoriteCount);

                // 簡単なアニメーション効果
                star.addClass("pulse");
                setTimeout(function () {
                    star.removeClass("pulse");
                }, 300);
            },
            error: function (xhr, status, error) {
                console.error("エラー:", error);
                alert("エラーが発生しました。もう一度お試しください。");
            },
            complete: function () {
                // ボタンを有効化
                button.prop("disabled", false);
            },
        });
    });
});

// パルスアニメーション用のCSS（動的に適用）
$("<style>")
    .text(
        `
    .favorite-star.pulse {
        animation: pulse 0.3s ease-in-out;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
`
    )
    .appendTo("head");
