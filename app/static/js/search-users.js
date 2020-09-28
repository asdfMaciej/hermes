var t = new Vue({
    el: "#profile-search",
    data: {
        api: null,
        users: USERS
    },

    methods: {
    },

    mounted: function() {
        this.api = new API();
    }
});