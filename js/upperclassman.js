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


/*更新中・・・*/
function toggleDots() {
    const dotsElement = document.getElementById("dots");
    dotsElement.style.visibility = dotsElement.style.visibility === "hidden" ? "visible" : "hidden";
}

function startBlinking() {
    const intervalId = setInterval(toggleDots, 700); // 点滅間隔（ミリ秒）
    setTimeout(() => clearInterval(intervalId), 10000); // 5秒後に点滅停止
}

document.addEventListener("DOMContentLoaded", () => {
    startBlinking();
});

/*日付*/
function updateDate() {
    const currentDateElement = document.getElementById("currentDate");
    const currentDate = new Date();
    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const day = String(currentDate.getDate()).padStart(2, '0');
    const formattedDate = `${year}.${month}.${day}`;
    currentDateElement.textContent = "更新：" + formattedDate;
}

document.addEventListener("DOMContentLoaded", function() {
    updateDate();
    setInterval(updateDate, 1000); // 1秒ごとに日付を更新
});

//
