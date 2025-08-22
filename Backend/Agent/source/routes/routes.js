'use strict';

const router = require('express').Router();
const AdminRoutes = require('./admin/admin.routes');
const { authenticateToken } = require('../middleware/authMiddleware');

class Routes {
    constructor() {
        this.myRoutes = router;
        this.core();
    }

    core() {
        this.myRoutes.use('/employee', new AdminRoutes().getRouters());
    }

    getRouters() {
        return this.myRoutes;
    }
}

module.exports = Routes;