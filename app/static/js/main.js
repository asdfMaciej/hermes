moment.locale('pl');
var api = new API();

let dates = document.querySelectorAll('.date');
dates.forEach((date) => {
	let iso = date.innerHTML;
	date.innerHTML = moment(iso).fromNow();
	date.setAttribute('title', iso);
});