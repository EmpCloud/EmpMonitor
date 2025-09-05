'use strict';

const router = require('express').Router();
const adminController = require('./admin.controller');
const authMiddleware = require('../../middleware/authMiddleware');

class AdminRoutes {

    constructor() {
        this.myRoutes = router;
        this.core();
    }

    core() {
        this.myRoutes.post('/login', adminController.adminLogin);
        this.myRoutes.use(authMiddleware.authenticateToken);
        this.myRoutes.use(authMiddleware.authorizeRole(['admin']));
        this.myRoutes.post('/register', adminController.adminRegister);
        this.myRoutes.get('/employees', adminController.getAllEmployees);
        this.myRoutes.get('/employees/:id', adminController.getEmployeeById);
        this.myRoutes.delete('/employees/:id', adminController.deleteEmployee);
        this.myRoutes.delete('/employee-delete-multiple', adminController.deleteEmployees);
        this.myRoutes.post('/attendance', adminController.getAttendance);
        this.myRoutes.post('/attendance/:id', adminController.getAttendanceById);
        this.myRoutes.get('/web-app-activity', adminController.getWebAppActivity);
        this.myRoutes.put('/update-employee', adminController.updateEmployee);

        this.myRoutes.get('/get-departments', adminController.getDepartments);
        this.myRoutes.post('/add-department', adminController.addDepartment);
        this.myRoutes.put('/update-department', adminController.updateDepartment);
        this.myRoutes.delete('/delete-department/:id', adminController.deleteDepartment);

        this.myRoutes.get('/locations', adminController.getLocations);
        this.myRoutes.post('/locations', adminController.addLocation);
        this.myRoutes.put('/locations/:id', adminController.updateLocation);
        this.myRoutes.delete('/locations/:id', adminController.deleteLocation);

        this.myRoutes.get("/report", adminController.getReports);

        this.myRoutes.post("/localization", adminController.updateLocalizationData);
        this.myRoutes.get("/localization", adminController.getLocalizationData);

        this.myRoutes.get("/productivity-rules", adminController.getProductivityRules);
        this.myRoutes.put("/productivity-rules", adminController.updateProductivityRules);
    }

    getRouters() {
        return this.myRoutes;
    }
}

module.exports = AdminRoutes;
