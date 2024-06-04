let navbar = document.querySelector('.header .flex .navbar');

//active el menu y cierre el menu delusuario
document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   profile.classList.remove('active');
}

let profile = document.querySelector('.header .flex .profile');

//active el menu del usuario y cierrre el otro
document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   navbar.classList.remove('active');
}

//para que al hacer scroll se cierren
window.onscroll = () =>{
   navbar.classList.remove('active');
   profile.classList.remove('active');
}