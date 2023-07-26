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
const dotsElement = document.getElementById('dots');
const dots = ['.', '..', '...'];

function animateDots() {
  let i = 0;

  function showDot() {
    dotsElement.textContent = dots[i];
    i = (i + 1) % dots.length;
    setTimeout(showDot, 700);
  }

  showDot();
}

animateDots();


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