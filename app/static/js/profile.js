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
        user: USER,
        view: 'main',
        cache: {
            profiles: []
        }
    },

    methods: {
        backButtonPressed: function(event) {
            if (this.view != 'main') {
                this.view = 'main';
                this.$nextTick(() => {initCalendar();});
            }
            //return "";
        },

        follow: function() {
            this._follow('follow');
        },
        unfollow: function() {
            this._follow('unfollow');
        },

        followers: function () {
            this._getFollow('followers');
        },

        following: function () {
            this._getFollow('following');
        },

        workouts: function () {
            if (this.view == 'main')
                return;
            this.view = 'main';
            this.$nextTick(() => {initCalendar();});
            this._addHistoryEntry('main');
        },

        _addHistoryEntry: function(entry) {
            history.pushState({page: entry}, `${entry} - Hermes`, "#"+entry);
        },

        _follow: function(action) {
            let data = {user_id: this.user.user_id};
            data.action = action;
            this.api.post('profiles', data, (response, data) => {
                if (response.code >= 300) {
                    return;
                }
                if (action == 'follow') {
                    this.$set(this.user, 'following', 1);
                    this.$set(this.user, 'followers_count', parseInt(this.user.followers_count) + 1);
                } else {
                    this.$set(this.user, 'following', 0);
                    this.$set(this.user, 'followers_count', parseInt(this.user.followers_count) - 1);
                }
            });
        },

        _getFollow: function(action) {
            this.api.get(`profiles/${this.user.user_id}/${action}`, (response, data) => {
                if (response.code >= 300) {
                    return;
                }
                this.view = action;
                this.$set(this.cache, 'profiles', data);
                this._addHistoryEntry(action);
            });
        },
    },

    mounted: function() {
        this.api = new API();
        initCalendar();
        window.onpopstate = this.backButtonPressed;
        window.onbeforeunload = this.backButtonPressed;
    }
});