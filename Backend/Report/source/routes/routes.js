'use strict';

const router = require('express').Router();
const { authenticateToken } = require('../../source/middleware/authMiddleware');
const ReportRoutes = require('./reports/reports.routes');


class Routes {

    constructor() {
        this.myRoutes = router;
        this.core();
    }

    core() {
        this.myRoutes.use(authenticateToken);
        this.myRoutes.use('/report', new ReportRoutes().getRouters());
    }

    getRouters() {
        return this.myRoutes;
    }
}

module.exports = Routes;