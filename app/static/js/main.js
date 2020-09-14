function clone(obj) {
	return JSON.parse(JSON.stringify(obj));
}
function showAsDuration(seconds) {
	// moment.js doesn't support this
	let minutes = Math.floor(parseInt(seconds) / 60);
	let hours = Math.floor(minutes / 60);
	minutes = minutes % 60;

	let duration = '', postfix = '';
	if (hours) {
		postfix = hours == 1 ? 'godzina' : (hours < 5 ? 'godziny' : 'godzin');
		duration = `${hours} ${postfix} `;
	}

	if (minutes) {
		duration += minutes == 1 ? `${minutes} minuta` : (minutes < 5 ? `${minutes} minuty` : `${minutes} minut`);
	}

	if (!duration) {
		return '<1 min';
	}

	return duration;
}
moment.locale('pl');
var api = new API();

const dates = document.querySelectorAll('.date');
dates.forEach((date) => {
	let iso = date.innerHTML;
	date.innerHTML = moment(iso).fromNow();
	date.setAttribute('title', iso);
});

const duration = document.querySelectorAll('.duration');
duration.forEach((el) => {
	let seconds = el.innerHTML;
	el.innerHTML = showAsDuration(seconds);
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

Vue.config.ignoredElements = ['ion-icon'];
