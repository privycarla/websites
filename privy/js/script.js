let profile = document.querySelector('.header .flex .profile');
let searchForm = document.querySelector('.header .flex .search-form');

document.querySelector('#user-btn').onclick = () => {
   profile.classList.toggle('active');
   searchForm.classList.remove('active');
};

document.querySelector('#search-btn').onclick = () => {
   searchForm.classList.toggle('active');
   profile.classList.remove('active');
};

window.onscroll = () => {
   profile.classList.remove('active');
   searchForm.classList.remove('active');
};