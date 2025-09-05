'use strict';

const router = require('express').Router();
const employeeController = require('./employee.controller');
const authMiddleware = require('../../middleware/authMiddleware');

class EmployeeRoutes {

    constructor() {
        this.myRoutes = router;
        this.core();
    }

    core() {
        this.myRoutes.post('/login', employeeController.employeeLogin);
        this.myRoutes.use(authMiddleware.authenticateToken);
        this.myRoutes.use(authMiddleware.authorizeRole(['employee']));
        this.myRoutes.put('/', authMiddleware.authenticateToken, employeeController.updateEmployee);
        this.myRoutes.get('/', authMiddleware.authenticateToken, employeeController.getEmployee);
        this.myRoutes.get('/web-app-activity', employeeController.getWebAppActivity);
        this.myRoutes.get('/employees/:id', employeeController.getEmployeeById);
        this.myRoutes.post('/attendance', employeeController.getAttendance);
    }

    getRouters() {
        return this.myRoutes;
    }
}

module.exports = EmployeeRoutes;