moment.locale('pl');
var api = new API();

let dates = document.querySelectorAll('.date');
dates.forEach((date) => {
	let iso = date.innerHTML;
	date.innerHTML = moment(iso).fromNow();
	date.setAttribute('title', iso);
});


//api.post('reactions', {action: 'unreact', workout_id: 69}, (response, data) => {console.log(response)})