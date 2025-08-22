'use strict';

const router = require('express').Router();
const Controller = require('./reports.controller');


class ReportsRoutes {

    constructor() {
        this.myRoutes = router;
        this.core();
    }

    core() {
        this.myRoutes.post('/add-data', Controller.addData);
    }

    getRouters() {
        return this.myRoutes;
    }
}

module.exports = ReportsRoutes;