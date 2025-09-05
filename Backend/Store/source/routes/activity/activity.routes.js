'use strict';

const router = require('express').Router();
const Controller = require('./activity.controller');


class ActivityRoutes {

    constructor() {
        this.myRoutes = router;
        this.core();
    }

    core() {
        this.myRoutes.post('/add-activity', Controller.addActivity);
    }

    getRouters() {
        return this.myRoutes;
    }
}

module.exports = ActivityRoutes;