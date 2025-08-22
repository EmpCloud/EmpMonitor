const router = require('express').Router();
const adminController = require('./admin.controller');
const { authenticateToken, authorizeRole } = require('../../middleware/authMiddleware');

class AdminRoutes {
    constructor() {
        this.myRoutes = router;
        this.core();
    }

    core() {
        this.myRoutes.post('/login', adminController.adminLogin);
        //  this.myRoutes.get('/admin-only', authenticateToken, authorizeRole(['admin']), adminController.adminOnlyRoute); // Example with middleware
    }

    getRouters() {
        return this.myRoutes;
    }
}

module.exports = AdminRoutes;