'use strict';

const router = require('express').Router();
const ActivityRoutes = require('./activity/activity.routes');
const { authenticateToken } = require('../middleware/authMiddleware');

class Routes {

    constructor() {
        this.myRoutes = router;
        this.core();
    }

    core() {
        this.myRoutes.use(authenticateToken);
        this.myRoutes.use('/activity', new ActivityRoutes().getRouters());
    }

    getRouters() {
        return this.myRoutes;
    }
}

module.exports = Routes;