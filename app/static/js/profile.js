function initCalendar() {
    var d = new Date();
    d.setMonth(d.getMonth() - 2);

    var cal = new CalHeatMap();
    var timeseries = {};
    for (let key in data) {
        timeseries[moment(key).unix()] = parseInt(data[key]);
    }
    cal.init({
        itemSelector: '#cal-heatmap',
        domain: 'month',
        subDomain: 'day',
        range: 3,
        cellSize: 15,
        highlight: "now",
        start: d,
        data: timeseries,
        legend: [1, 2, 3],
        legendVerticalPosition: "center",
        legendOrientation: "vertical",
        legendMargin: [0, 10, 0, 0],
        legendColors: {
            min: "#e8b8b8",
            max: "#d32f2f",
            empty: "#d1d7d8",
            base: "#d1d7d8"
            // Will use the CSS for the missing keys
        },

        onComplete: () => {
            let box = document.querySelector("#cal-heatmap > svg");
            box.setAttribute("viewBox", `0 0 ${cal.graphDim.width} ${cal.graphDim.height}`);
        }
    });
}
var t = new Vue({
    el: "#profile",
    data: {
        api: null,
        user: USER
    },

    methods: {
        follow: function() {
            this._follow('follow');
        },
        unfollow: function() {
            this._follow('unfollow');
        },

        _follow: function(action) {
            let data = {user_id: this.user.user_id};
            data.action = action;
            this.api.post('profiles', data, (response, data) => {
                if (response.code >= 300) {
                    return
                }
                if (action == 'follow') {
                   this.$set(this.user, 'following', 1); 
                   this.$set(this.user, 'followers_count', this.user.followers_count + 1); 
                } else {
                    this.$set(this.user, 'following', 0); 
                    this.$set(this.user, 'followers_count', this.user.followers_count - 1); 
                }
            });
        }
    },

    mounted: function() {
        this.api = new API();
        initCalendar();
    }
});