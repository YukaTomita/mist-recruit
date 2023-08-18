// アコーディオンメニューの動作を制御するJavaScript
document.addEventListener("DOMContentLoaded", function () {
  var accordionHeaders = document.querySelectorAll(".accordion-header");
  var firstAccordionItem = document.querySelector(".accordion-item");

  // 最初のアコーディオンを開く
  firstAccordionItem.classList.add("active");

  accordionHeaders.forEach(function (header) {
    header.addEventListener("click", function () {
      var accordionItem = this.parentElement;

      if (!accordionItem.classList.contains("active")) {
        // クリックされたアコーディオン以外のコンテンツを閉じる
        accordionHeaders.forEach(function (otherHeader) {
          var otherAccordionItem = otherHeader.parentElement;
          if (otherHeader !== header && otherAccordionItem.classList.contains("active")) {
            otherAccordionItem.classList.remove("active");
          }
        });
      }

      accordionItem.classList.toggle("active");
    });
  });
});

//レベルの〇をつける
function toggleCheckmark(element) {
    var isSelected = element.classList.contains("selected");

    // すでに選択されている場合は「〇」を削除する
    if (isSelected) {
        element.innerHTML = "";
        element.classList.remove("selected");
    } else {
        var selectedCells = document.getElementsByClassName("selected");

        // 同じ行の他のセルが選択されている場合は選択を解除する
        for (var i = 0; i < selectedCells.length; i++) {
            var selectedCell = selectedCells[i];
            if (selectedCell.parentNode === element.parentNode) {
                selectedCell.innerHTML = "";
                selectedCell.classList.remove("selected");
            }
        }

        // 選択されたセルに〇を追加する
        element.innerHTML = "〇";
        element.classList.add("selected");
    }
}


// radio button on/off
const radioButtons = document.querySelectorAll('input[type="radio"]');

const clearRadioButton = (radioButton) => {
  setTimeout(func =()=>{
    radioButton.checked = false;
  },100)
}

radioButtons.forEach(radioButton => {
  let queryStr = 'label[for="' + radioButton.id + '"]'
  let label = document.querySelector(queryStr)

  radioButton.addEventListener("mouseup", func=()=>{
    if(radioButton.checked){
      clearRadioButton(radioButton)
    }
  });

  if(label){
    label.addEventListener("mouseup", func=()=>{
      if(radioButton.checked){
        clearRadioButton(radioButton)
      }
    });
  }
});

// 個人情報同意・エントリーアクティブ


window.addEventListener("DOMContentLoaded", function() {
  var agreeButton = document.getElementById("agreeButton");
  var entryButton = document.getElementById("entryButton");

  agreeButton.addEventListener("click", function(event) {
    if (agreeButton.classList.contains("active")) {
      agreeButton.classList.remove("active");
      entryButton.setAttribute("disabled", "disabled");
      entryButton.classList.remove("active");
      entryButton.style.backgroundColor = "";
    } else {
      agreeButton.classList.add("active");
      entryButton.removeAttribute("disabled");
      entryButton.classList.add("active");
      entryButton.style.backgroundColor = "#8B2022";
    }
    event.preventDefault(); // ページのリロードを防止
  });
});

// 履歴書・職務経歴書アップロード　（サーバー処理要追加）
const uploadButton = document.getElementById('uploadButton');
const fileInput = document.getElementById('fileInput');
const selectedFilesContainer = document.getElementById('selectedFiles');

uploadButton.addEventListener('click', (event) => {
  event.preventDefault();
  fileInput.click();
});

fileInput.addEventListener('change', () => {
  const selectedFiles = fileInput.files;

  if (selectedFiles.length > 0) {
    for (const file of selectedFiles) {
      const fileItem = document.createElement('div');
      fileItem.className = 'file-item';
      fileItem.innerHTML = `
        <span>${file.name}</span>
        <button class="delete-button">×</button>
      `;
      selectedFilesContainer.appendChild(fileItem);
    }
  }
});

selectedFilesContainer.addEventListener('click', (event) => {
  if (event.target.classList.contains('delete-button')) {
    const fileItem = event.target.parentNode;
    selectedFilesContainer.removeChild(fileItem);
  }
});
