// 商品購入画面の支払い方法変更機能
document.addEventListener("DOMContentLoaded", function () {
    // セレクトボックスの要素を取得
    const paymentSelect = document.querySelector('select[name="payment"]');
    // 支払い方法表示エリアの要素を取得
    const paymentDisplayText = document.querySelector(
        ".purchase-info__payment"
    );

    // セレクトボックスの変更イベントを監視
    if (paymentSelect && paymentDisplayText) {
        paymentSelect.addEventListener("change", function () {
            // 選択された値を取得
            const selectedValue = this.value;
            // 選択されたオプションのテキストを取得
            const selectedText = this.options[this.selectedIndex].text;

            // 支払い方法表示エリアのテキストを更新
            paymentDisplayText.textContent = selectedText;

            // デバッグ用（本番では削除）
            console.log(
                "選択された支払い方法:",
                selectedText,
                "値:",
                selectedValue
            );
        });

        // ページ読み込み時に初期値を設定
        const initialText =
            paymentSelect.options[paymentSelect.selectedIndex].text;
        paymentDisplayText.textContent = initialText;
    }
});
