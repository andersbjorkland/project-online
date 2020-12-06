(function () {
    let figures = document.getElementsByTagName("figure");
    let caption = "";
    let imgTag;
    for (let i = 0; i < figures.length; i++) {
        imgTag = figures[i].getElementsByTagName("img");
        caption = figures[i].getElementsByTagName("figcaption");

        if (caption.length > 0) {
            if (imgTag.length > 0) {
                imgTag = imgTag[0];
                imgTag.setAttribute("alt", caption[0].textContent);

                caption[0].parentElement.removeChild(caption[0]);
            }
        } else {
            if (imgTag.length > 0) {
                imgTag = imgTag[0];
                imgTag.setAttribute("alt", "")
            }
        }
    }
})();