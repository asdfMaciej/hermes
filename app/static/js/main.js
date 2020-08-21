moment.locale('pl');
var api = new API();

const dates = document.querySelectorAll('.date');
dates.forEach((date) => {
	let iso = date.innerHTML;
	date.innerHTML = moment(iso).fromNow();
	date.setAttribute('title', iso);
});

const logoutForm = document.querySelector("#logout-form");
if (logoutForm) {
	logoutForm.addEventListener("click", () => {
		logoutForm.submit();
	});
}

const showSearch = document.querySelector(".page-header__search-button");
const header = document.querySelector(".page-header");
showSearch.addEventListener("click", () => {
	header.classList.toggle('show-search');
});