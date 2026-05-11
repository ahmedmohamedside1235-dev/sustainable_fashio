
AOS.init();
const message = document.getElementById('message');
if (message) {
    setTimeout(() => {
        message.classList.add('show');
    }, 100);

    setTimeout(() => {
        message.classList.remove('show');
    }, 4000);
}

let cardEle = document.getElementById('cardss'),
    sliceItems = (allItems.length <= 5) ? allItems : allItems.slice(0, 5);
loadItems(function () {
    if (!isLoggedIn) {
        cardEle.innerHTML = `<p class='alert alert-warning'> You must log in to view the data </p>`
        document.querySelector('.learn-more').classList.add('d-none');
        return;
    } else {
        document.querySelector('.learn-more').classList.remove('d-none');
        let sliceItems = allItems.length <= 5 ? allItems : allItems.slice(0, 5);
        sliceItems.forEach(function (sliceItem) {
            cardEle.innerHTML += `
        <div class="card" data-aos="fade-up" data-aos-delay="100">
            <img src="${sliceItem.image}" class="card-img" style="cursor:pointer;" onerror="this.src=\'/assets/shared/images/default.png\'">
            <div class="card-body">
                <h3>${sliceItem.owner_name}</h3>
                <p>Category:${sliceItem.category}</p>
                <p>Condition: ${sliceItem.condition}</p>
                <p>Material: ${sliceItem.material}</p>
                <p>Price:${sliceItem.price}</p>
                <a href="${collectionsUrl}"> <button class="swap-btn">View Details</button></a>
                </div>
                </div>
                `;
        });
    }
});