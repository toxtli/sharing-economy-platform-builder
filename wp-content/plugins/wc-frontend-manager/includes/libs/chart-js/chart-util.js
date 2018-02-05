var timeFormat = 'MM/DD/YY';
function newDate(days) {
	return moment().add(days, 'd').toDate();
}

function newDateString(days) {
	return moment().add(days, 'd').format(timeFormat);
}

function newTimestamp(days) {
	return moment().add(days, 'd').unix();
}

window.chartColors = {
	red: 'rgb(255, 99, 132)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	purple: 'rgb(153, 102, 255)',
	grey: 'rgb(201, 203, 207)'
};

var color = Chart.helpers.color;