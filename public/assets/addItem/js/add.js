function previewFile(e) {
    let file = e.target.files[0];
    if (!file) return;
    let reader = new FileReader();
    reader.onload = function () {
        document.getElementById('previewImg').src = reader.result;
        document.getElementById('previewImg').style.display = 'block';
        document.getElementById('imgPlaceholder').style.display = 'none';
    };
    reader.readAsDataURL(file);
}