function showPopup(popupNumber) {
    // すべてのポップアップを非表示にする
    const popups = document.querySelectorAll('.popup');
    popups.forEach(popup => popup.style.display = 'none');

    // 対応するポップアップを表示する
    const popup = document.getElementById(`popup${popupNumber}`);
    popup.style.display = 'block';
}

function hidePopup(popupId) {
    var popup = document.getElementById("popup" + popupId);
    popup.style.display = "none";
}



function toggleDots() {
    const dotsElement = document.getElementById("dots");
    dotsElement.style.visibility = dotsElement.style.visibility === "hidden" ? "visible" : "hidden";
}

function startBlinking() {
    const intervalId = setInterval(toggleDots, 500); // 点滅間隔（ミリ秒）
    setTimeout(() => clearInterval(intervalId), 5000); // 5秒後に点滅停止
}

document.addEventListener("DOMContentLoaded", () => {
    startBlinking();
});


