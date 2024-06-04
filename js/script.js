//activar el menu
let navbar = document.querySelector(".header .flex .navbar");

document.querySelector("#menu-btn").onclick = () => {
    navbar.classList.toggle("active");
};

// activar la cuenta del usuario, icono de user
let account = document.querySelector('.user-account');

document.querySelector('#user-btn').onclick = () => {
    account.classList.add('active');
}

//se cierre el menu se usuario al dar clic en close
document.querySelector('#close-account').onclick = () => {
    account.classList.remove('active');
}

//activar el menu de ordenes
let myOrders = document.querySelector('.my-orders');

document.querySelector('#order-btn').onclick = () => {
    myOrders.classList.add('active');
}


//cerra el menu de ordenes
document.querySelector('#close-orders').onclick = () => {
    myOrders.classList.remove('active');
}

//active y muestre el menu de carrito
let cart = document.querySelector('.shopping-cart');

document.querySelector('#cart-btn').onclick = () => {
    cart.classList.add('active');
}

//cierre el menu con la palabra close
document.querySelector('#close-cart').onclick = () => {
    cart.classList.remove('active');
}






//slider 1
var swiper = new Swiper(".mySwiper-1", {
    slidesPerView: 1,
    spaceBetween: 30,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
});










//para quitar al hacer scroll
window.onscroll = () => {
    navbar.classList.remove("active");
    myOrders.classList.remove("active");
    cart.classList.remove("active");
};
